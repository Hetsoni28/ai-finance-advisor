<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SecurityController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Enterprise Security Intelligence Console
     */
    public function index(Request $request): View
    {
        $days = (int) $request->get('days', 7);

        // 🚨 FIX: Do NOT use with('user') on the base query. It breaks MySQL Strict Mode aggregations.
        $query = Activity::where('created_at', '>=', now()->subDays($days));

        // Relational Search Filter
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function (Builder $q) use ($search) {
                $q->where('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function (Builder $uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        // NLP Severity Filter
        if ($request->filled('severity')) {
            $sev = strtolower($request->severity);
            if ($sev === 'critical') {
                $query->where(function($q) {
                    $q->where('description', 'LIKE', '%delete%')
                      ->orWhere('description', 'LIKE', '%fail%')
                      ->orWhere('description', 'LIKE', '%block%')
                      ->orWhere('description', 'LIKE', '%suspend%')
                      ->orWhere('description', 'LIKE', '%lockdown%');
                });
            } elseif ($sev === 'info') {
                $query->where('description', 'NOT LIKE', '%delete%')
                      ->where('description', 'NOT LIKE', '%fail%')
                      ->where('description', 'NOT LIKE', '%block%');
            }
        }

        // 🚨 FIX: Apply eager loading `with('user')` ONLY to the paginated collection, not the base query.
        $activities = $query->clone()->with('user')->latest()->paginate(20)->withQueryString();

        // Analytics Engine (Cache-backed DB Aggregations)
        $cacheKey = "security_analysis_d{$days}_s" . md5($request->search . $request->severity);
        
        $analysis = Cache::remember($cacheKey, 60, function () use ($query) {
            return $this->executeThreatMatrix($query->clone());
        });

        $severities = $this->mapSeverities($activities);

        return view('admin.activities.index', [
            'activities' => $activities,
            'analysis'   => $analysis,
            'severities' => $severities,
            'days'       => $days,
        ]);
    }

    /**
     * Threat Matrix Calculation Engine (MySQL Strict Mode Compliant)
     */
    private function executeThreatMatrix(Builder $query): array
    {
        // 🚨 FIX: Use toBase() to drop Eloquent overhead, and COALESCE to prevent null errors in strict mode.
        $stats = $query->toBase()->selectRaw('
            COUNT(*) as total_logs,
            COALESCE(SUM(CASE WHEN description LIKE "%delete%" OR description LIKE "%destroy%" THEN 1 ELSE 0 END), 0) as delete_count,
            COALESCE(SUM(CASE WHEN description LIKE "%update%" OR description LIKE "%edit%" OR description LIKE "%modify%" THEN 1 ELSE 0 END), 0) as update_count,
            COALESCE(SUM(CASE WHEN description LIKE "%fail%" OR description LIKE "%block%" OR description LIKE "%lockdown%" THEN 1 ELSE 0 END), 0) as critical_count,
            COALESCE(SUM(CASE WHEN description LIKE "%login%" OR description LIKE "%auth%" THEN 1 ELSE 0 END), 0) as login_count
        ')->first();

        $totalLogs     = (int) ($stats->total_logs ?? 0);
        $deleteCount   = (int) ($stats->delete_count ?? 0);
        $updateCount   = (int) ($stats->update_count ?? 0);
        $criticalCount = (int) ($stats->critical_count ?? 0);
        $loginCount    = (int) ($stats->login_count ?? 0);

        // Bayesian Threat Algorithm
        $score = 0;

        if ($totalLogs > 0) {
            $criticalRatio = ($criticalCount / $totalLogs) * 100;
            $score += ($criticalRatio * 1.5);

            $deletionRatio = ($deleteCount / $totalLogs) * 100;
            if ($deletionRatio > 15) {
                $score += (($deletionRatio - 15) * 2); 
            }

            if ($totalLogs > 2000) {
                $score += 25;
            } elseif ($totalLogs > 500) {
                $score += 10;
            }
        }

        $finalScore = (int) min(max($score, 0), 100);

        return [
            'score'         => $finalScore,
            'level'         => $this->determineDefconLevel($finalScore),
            'deleteCount'   => $deleteCount,
            'updateCount'   => $updateCount,
            'loginCount'    => $loginCount,
            'criticalCount' => $criticalCount,
            'totalLogs'     => $totalLogs,
        ];
    }

    private function mapSeverities($activities): array
    {
        $map = [];
        foreach ($activities as $activity) {
            $desc = strtolower($activity->description ?? '');

            if (Str::contains($desc, ['delete', 'destroy', 'fail', 'block', 'suspend', 'lockdown'])) {
                $map[$activity->id] = ['label' => 'Critical', 'class' => 'bg-rose-50 text-rose-600 border-rose-200', 'icon' => 'fa-triangle-exclamation'];
            } elseif (Str::contains($desc, ['update', 'edit', 'change', 'modify', 'upgrade', 'switch'])) {
                $map[$activity->id] = ['label' => 'Warning', 'class' => 'bg-amber-50 text-amber-600 border-amber-200', 'icon' => 'fa-shield-halved'];
            } elseif (Str::contains($desc, ['login', 'auth', 'register', 'create', 'session'])) {
                $map[$activity->id] = ['label' => 'Secure', 'class' => 'bg-emerald-50 text-emerald-600 border-emerald-200', 'icon' => 'fa-lock'];
            } else {
                $map[$activity->id] = ['label' => 'Info', 'class' => 'bg-slate-50 text-slate-600 border-slate-200', 'icon' => 'fa-circle-info'];
            }
        }
        return $map;
    }

    private function determineDefconLevel(int $score): array
    {
        if ($score >= 75) return ['label' => 'CRITICAL', 'color' => '#e11d48', 'text' => 'text-rose-600', 'bg' => 'rose-50'];
        if ($score >= 40) return ['label' => 'ELEVATED', 'color' => '#d97706', 'text' => 'text-amber-600', 'bg' => 'amber-50'];
        if ($score >= 15) return ['label' => 'GUARDED', 'color' => '#4f46e5', 'text' => 'text-indigo-600', 'bg' => 'indigo-50'];
        return ['label' => 'SECURE', 'color' => '#10b981', 'text' => 'text-emerald-600', 'bg' => 'emerald-50'];
    }

    /**
     * Backend CSV Streaming Engine
     */
    public function exportAuditLogs(Request $request): StreamedResponse
    {
        $days = (int) $request->get('days', 7);
        $query = Activity::with('user')->where('created_at', '>=', now()->subDays($days));

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function (Builder $q) use ($search) {
                $q->where('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function (Builder $uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%")->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($request->filled('severity')) {
            $sev = strtolower($request->severity);
            if ($sev === 'critical') {
                $query->where(function($q) {
                    $q->where('description', 'LIKE', '%delete%')->orWhere('description', 'LIKE', '%fail%')->orWhere('description', 'LIKE', '%block%')->orWhere('description', 'LIKE', '%suspend%');
                });
            } elseif ($sev === 'info') {
                $query->where('description', 'NOT LIKE', '%delete%')->where('description', 'NOT LIKE', '%fail%')->where('description', 'NOT LIKE', '%block%');
            }
        }

        $fileName = 'FinanceAI_Security_Audit_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Event ID', 'User ID', 'Actor Email', 'Action / Description', 'Timestamp (UTC)'];

        $callback = function () use ($query, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            $query->chunk(1000, function ($logs) use ($file) {
                foreach ($logs as $log) {
                    fputcsv($file, [
                        $log->id,
                        $log->user_id ?? 'System',
                        $log->user->email ?? 'System Agent',
                        $log->description,
                        $log->created_at->toIso8601String()
                    ]);
                }
            });
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function executeLockdown(Request $request): RedirectResponse
    {
        try {
            DB::transaction(function () {
                $affectedRows = User::where('id', '!=', Auth::id())
                                    ->where('role', '!=', User::ROLE_ADMIN ?? 'admin')
                                    ->update(['is_blocked' => true]);

                Activity::create([
                    'user_id'     => Auth::id(),
                    'description' => "SYSTEM LOCKDOWN INITIATED. Suspended {$affectedRows} active accounts.",
                ]);
                Cache::flush();
            });
            return back()->with('success', 'Lockdown Protocol Executed. All non-admin traffic has been halted.');
        } catch (\Exception $e) {
            Log::critical("Failed to execute System Lockdown: " . $e->getMessage());
            return back()->with('error', 'Critical failure executing Lockdown Protocol. Contact engineering.');
        }
    }
}