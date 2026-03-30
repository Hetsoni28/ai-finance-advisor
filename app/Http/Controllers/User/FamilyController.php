<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFamilyRequest;
use App\Http\Requests\SendFamilyInviteRequest;
use App\Models\Family;
use App\Models\FamilyInvite;
use App\Models\FamilyMember;
use App\Models\User;
use App\Services\FamilyDashboardService;
use App\Services\FamilyInviteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Spatie\Activitylog\Models\Activity;
use Throwable;

class FamilyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['acceptInvite']);
    }

    /*
    |--------------------------------------------------------------------------
    | LIST USER FAMILIES (WORKSPACES)
    |--------------------------------------------------------------------------
    */
    public function index(): View
    {
        $user = auth()->user();
        abort_unless($user, 403, 'Unauthorized Node Access');

        $families = $user->families()
            ->withPivot('role')
            ->withCount('users as member_count') 
            ->latest('families.created_at')
            ->paginate(9);

        $familyIds = $user->families()->pluck('families.id');
        
        $activities = Activity::where('subject_type', Family::class)
            ->whereIn('subject_id', $familyIds)
            ->with('causer')
            ->latest()
            ->limit(12)
            ->get();

        return view('user.family.index', compact('families', 'activities'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE FAMILY
    |--------------------------------------------------------------------------
    */
    public function create(): View
    {
        return view('user.family.create');
    }

    public function store(StoreFamilyRequest $request): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user, 403);

        try {
            DB::transaction(function () use ($request, $user) {
                
                $validated = $request->validated();

                $family = Family::create([
                    'name'        => $validated['name'] ?? 'New Workspace',
                    'description' => $validated['description'] ?? null,
                    'created_by'  => $user->id,
                ]);

                FamilyMember::create([
                    'family_id' => $family->id,
                    'user_id'   => $user->id,
                    'role'      => 'owner',
                ]);

                activity()
                    ->causedBy($user)
                    ->performedOn($family)
                    ->log('Initialized a new collaborative workspace.');
            });

            return redirect()
                ->route('user.families.index')
                ->with('success', 'Workspace initialized successfully.');

        } catch (Throwable $e) {
            Log::error('Workspace Creation Failed: ' . $e->getMessage());
            return back()->with('error', 'Critical failure during initialization. Please try again.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | FAMILY DASHBOARD (HUB)
    |--------------------------------------------------------------------------
    */
    public function show(Family $family, FamilyDashboardService $dashboard): View
    {
        $this->authorize('view', $family);

        $activities = Activity::where('subject_type', Family::class)
            ->where('subject_id', $family->id)
            ->with('causer')
            ->latest()
            ->limit(15)
            ->get();

        $members = $family->users()->withPivot('role', 'created_at')->get();
        $invites = $family->invites()->latest()->get();

        $dashboardPayload = $dashboard->build($family);

        return view('user.family.show', array_merge($dashboardPayload, [
            'activities' => $activities,
            'members'    => $members,
            'invites'    => $invites,
        ]));
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESS MANAGEMENT HUB (INVITES UI)
    |--------------------------------------------------------------------------
    */
    public function accessManagement(Family $family): View
    {
        $this->authorize('update', $family);

        $invites = $family->invites()->latest()->get();

        $totalWait = 0;
        $resolvedCount = 0;
        
        foreach($invites as $inv) {
            if($inv->accepted_at) {
                $totalWait += $inv->accepted_at->diffInHours($inv->created_at);
                $resolvedCount++;
            }
        }
        
        $avgWait = $resolvedCount > 0 ? round($totalWait / $resolvedCount, 1) : 0;

        return view('user.family.invites', [
            'family'  => $family,
            'invites' => $invites,
            'avgWait' => $avgWait
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SEND INVITE (SECURE MAILBOX)
    |--------------------------------------------------------------------------
    */
    public function invite(SendFamilyInviteRequest $request, Family $family, FamilyInviteService $inviteService): RedirectResponse
    {
        $this->authorize('invite', $family);

        try {
            $email = $request->validated()['email'];
            $inviteService->sendEmailInvite($family, $email);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($family)
                ->withProperties(['email' => $email])
                ->log('Transmitted access token to target email.');

            return back()->with('success', 'Cryptographic invitation transmitted successfully.');

        } catch (Throwable $e) {
            Log::error('Invite Transmission Failed: ' . $e->getMessage());
            return back()->with('error', 'Transmission failed. Ensure mail configurations are valid.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 ACCEPT INVITE (SESSION HANDSHAKE FOR REGISTRATION)
    |--------------------------------------------------------------------------
    */
    public function acceptInvite(Family $family, string $token): RedirectResponse
    {
        $invite = FamilyInvite::where('token', $token)
            ->where('family_id', $family->id)
            ->firstOrFail();

        if ($invite->accepted_at) {
            return redirect()->route('login')->withErrors('This cryptographic token has already been consumed.');
        }

        if (isset($invite->expires_at) && \Carbon\Carbon::parse($invite->expires_at)->isPast()) {
            return redirect()->route('login')->withErrors('This access token has exceeded its Time-To-Live (TTL) and is void.');
        }

        // 🔥 BEAST MODE: If not logged in, save to session and force Registration!
        if (!auth()->check()) {
            session([
                'pending_invite_token' => $token,
                'pending_family_id'    => $family->id,
                'invited_email'        => $invite->email
            ]);
            
            return redirect()->route('register')->with('info', 'Please create your account to securely join the workspace.');
        }

        $user = auth()->user();

        if ($family->users()->where('user_id', $user->id)->exists()) {
            // 🚨 FIXED: user.families.show
            return redirect()->route('user.families.show', $family->id)
                ->with('info', 'Node is already connected to this workspace.');
        }

        try {
            DB::transaction(function () use ($invite, $user, $family) {
                FamilyMember::create([
                    'family_id' => $family->id,
                    'user_id'   => $user->id,
                    'role'      => 'member', 
                ]);

                $invite->update([
                    'accepted_at' => now(),
                    'accepted_by' => $user->id
                ]);

                activity()
                    ->causedBy($user)
                    ->performedOn($family)
                    ->log('Node successfully accepted handshake.');
            });

            // 🚨 FIXED: user.families.show
            return redirect()->route('user.families.show', $family->id)
                ->with('success', 'Secure connection established. Welcome to the Hub.');

        } catch (Throwable $e) {
            Log::error('Invite Acceptance Failed: ' . $e->getMessage());
            
            $errorMsg = config('app.debug') 
                ? 'Ledger integration failed: ' . $e->getMessage() 
                : 'Ledger integration failed. Contact administrator.';
                
            // 🚨 FIXED: user.dashboard
            return redirect()->route('user.dashboard')->with('error', $errorMsg);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE MEMBER (IAM REVOCATION)
    |--------------------------------------------------------------------------
    */
    public function removeMember(Family $family, User $member): RedirectResponse
    {
        $this->authorize('update', $family); 

        try {
            $familyMember = FamilyMember::where('family_id', $family->id)->where('user_id', $member->id)->first();
            
            if ($familyMember && $familyMember->role === 'owner') {
                return back()->with('error', 'Protocol Violation: Cannot revoke access for the workspace owner.');
            }

            $family->users()->detach($member->id);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($family)
                ->log("Revoked workspace access for node: {$member->name}.");

            return back()->with('success', 'Node access successfully revoked.');

        } catch (Throwable $e) {
            Log::error('Member Revocation Failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to revoke node access. Please try again.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PURGE SINGLE INVITE
    |--------------------------------------------------------------------------
    */
    public function destroyInvite(Family $family, FamilyInvite $invite): RedirectResponse
    {
        $this->authorize('update', $family);

        try {
            if ($invite->family_id !== $family->id) {
                abort(403, 'Security Protocol: Invite does not belong to this workspace.');
            }

            $email = $invite->email;
            $invite->delete();

            activity()
                ->causedBy(auth()->user())
                ->performedOn($family)
                ->log("Purged pending cryptographic invite for {$email}.");

            return back()->with('success', 'Transmission voided. Token permanently destroyed.');

        } catch (Throwable $e) {
            Log::error('Invite Destruction Failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to void transmission.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | BULK PURGE EXPIRED INVITES
    |--------------------------------------------------------------------------
    */
    public function bulkDestroyInvites(Request $request, Family $family): RedirectResponse
    {
        $this->authorize('update', $family);

        try {
            $inviteIds = $request->input('invite_ids', []);
            
            if (empty($inviteIds)) {
                return back()->with('error', 'No transmissions selected for purge.');
            }

            DB::transaction(function () use ($family, $inviteIds) {
                
                $count = FamilyInvite::where('family_id', $family->id)
                    ->whereIn('id', $inviteIds)
                    ->delete();

                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($family)
                    ->log("Executed bulk purge. Destroyed {$count} expired transmission tokens.");
            });

            return back()->with('success', 'Selected transmissions have been permanently voided.');

        } catch (Throwable $e) {
            Log::error('Bulk Invite Destruction Failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to execute bulk purge protocol.');
        }
    }
}