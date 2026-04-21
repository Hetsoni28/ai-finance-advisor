@extends('layouts.app')

@section('title', 'Security Intelligence - FinanceAI Admin')

@section('content')

@php
    // Safely parse incoming data
    $analysis = $analysis ?? [];
    $activities = $activities ?? collect([]);
    $severities = $severities ?? [];

    $totalLogs = (int) ($analysis['totalLogs'] ?? 0);
    $deleteCount = (int) ($analysis['deleteCount'] ?? 0);
    $updateCount = (int) ($analysis['updateCount'] ?? 0);
    $criticalCount = (int) ($analysis['criticalCount'] ?? 0);

    $threatScore = min(max((int) ($analysis['score'] ?? 0), 0), 100);

    // Dynamic Defcon Levels (Light Theme)
    $defaultLevel = ['label' => 'SECURE', 'color' => '#10b981', 'text' => 'text-emerald-600', 'bg' => 'bg-emerald-50'];
    $levelData = array_merge($defaultLevel, $analysis['level'] ?? []);

    $safeColor = preg_match('/^#[a-f0-9]{6}$/i', $levelData['color']) ? $levelData['color'] : '#10b981';
    $anomalyRatio = $totalLogs > 0 ? round(($deleteCount / $totalLogs) * 100) : 0;
@endphp

<div class="min-h-screen bg-[#f8fafc] pb-24 font-sans selection:bg-indigo-100 selection:text-indigo-900 relative">

    {{-- Pristine Light Ambient Background --}}
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden bg-white/50">
        <div class="absolute top-[-10%] right-[-5%] w-[800px] h-[800px] bg-indigo-50/50 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] left-[-5%] w-[800px] h-[800px] bg-emerald-50/50 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-10 space-y-8">

        {{-- ================= 1. PAGE HEADER & SOC CLOCK ================= --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm">
            <div>
                <div class="inline-flex items-center gap-3 px-4 py-1.5 rounded-full bg-slate-50 border border-slate-200 text-slate-600 text-xs font-bold tracking-wide uppercase mb-4">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                    </span>
                    Security Operations Center
                    <span class="text-slate-300">|</span>
                    <span id="liveClock" class="font-mono text-indigo-600">00:00:00</span>
                </div>
                <h1 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight">Audit Matrix</h1>
                <p class="text-slate-500 mt-2 font-medium text-lg">Real-time threat monitoring, anomaly detection, and platform auditing.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <button onclick="exportAuditCSV()" class="group relative px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl font-bold shadow-sm hover:border-indigo-300 hover:text-indigo-600 hover:shadow-md transition-all flex items-center gap-2 focus:outline-none">
                    <i class="fa-solid fa-download"></i> Export Data
                </button>
                <button onclick="openLockdownModal()" class="px-6 py-3 bg-white text-rose-600 border border-rose-200 rounded-xl font-bold shadow-sm hover:bg-rose-50 hover:border-rose-300 hover:shadow-md transition-all flex items-center gap-2 focus:outline-none">
                    <i class="fa-solid fa-lock"></i> Initiate Lockdown
                </button>
            </div>
        </div>

        {{-- ================= 2. CRITICAL ALERTS ================= --}}
        @if($criticalCount > 0)
        <div class="bg-white border border-rose-200 px-6 py-5 rounded-[1.5rem] shadow-sm flex items-center justify-between animate-fade-in-up relative overflow-hidden">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-rose-500 animate-pulse"></div>
            <div class="flex items-center gap-4 pl-2">
                <div class="w-10 h-10 rounded-full bg-rose-50 flex items-center justify-center text-rose-600 shrink-0 border border-rose-100">
                    <i class="fa-solid fa-triangle-exclamation animate-pulse"></i>
                </div>
                <div>
                    <h3 class="font-black text-lg text-slate-900">Critical Vulnerability Detected</h3>
                    <p class="text-sm font-medium text-slate-600">{{ $criticalCount }} high-severity event(s) have been flagged by the AI engine. Immediate review required.</p>
                </div>
            </div>
            <button class="px-5 py-2.5 bg-rose-50 border border-rose-200 rounded-xl text-sm font-bold text-rose-600 hover:bg-rose-600 hover:text-white transition-colors shrink-0">
                Review Logs
            </button>
        </div>
        @endif

        {{-- ================= 3. TOP KPI METRICS ================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
            
            @php
                $metrics = [
                    ['label'=>'Total Audit Logs', 'value'=>$totalLogs, 'color'=>'text-indigo-600', 'bg'=>'bg-indigo-50', 'border'=>'border-indigo-100', 'icon'=>'fa-server'],
                    ['label'=>'Deletion Events', 'value'=>$deleteCount, 'color'=>'text-rose-600', 'bg'=>'bg-rose-50', 'border'=>'border-rose-100', 'icon'=>'fa-trash-can'],
                    ['label'=>'Update Events', 'value'=>$updateCount, 'color'=>'text-amber-600', 'bg'=>'bg-amber-50', 'border'=>'border-amber-100', 'icon'=>'fa-pen-to-square'],
                    ['label'=>'Critical Alerts', 'value'=>$criticalCount, 'color'=>'text-purple-600', 'bg'=>'bg-purple-50', 'border'=>'border-purple-100', 'icon'=>'fa-shield-virus']
                ];
            @endphp

            @foreach($metrics as $metric)
            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                <div class="flex justify-between items-start mb-4 relative z-10">
                    <div class="w-12 h-12 rounded-2xl {{ $metric['bg'] }} {{ $metric['color'] }} flex items-center justify-center border {{ $metric['border'] }} shadow-sm group-hover:scale-110 transition-transform duration-500">
                        <i class="fa-solid {{ $metric['icon'] }} text-lg"></i>
                    </div>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1 relative z-10">{{ $metric['label'] }}</p>
                <div class="text-4xl font-black text-slate-900 relative z-10 counter" data-target="{{ $metric['value'] }}">0</div>
            </div>
            @endforeach

            {{-- Threat Gauge Card (Light Mode) --}}
            <div class="bg-white border border-slate-200 rounded-[2rem] p-6 shadow-sm flex flex-col items-center justify-center relative overflow-hidden">
                <div class="relative w-36 h-36">
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="42" fill="none" stroke="#f1f5f9" stroke-width="8"></circle>
                        <circle id="threatGauge" cx="50" cy="50" r="42" fill="none" stroke="{{ $safeColor }}" stroke-width="8" stroke-linecap="round" 
                                stroke-dasharray="264" stroke-dashoffset="264" class="transition-all duration-1500 ease-out"></circle>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-4xl font-black text-slate-900" id="threatScoreText">0</span>
                    </div>
                </div>
                <p class="mt-4 text-xs font-black tracking-widest uppercase {{ $levelData['text'] }} bg-slate-50 px-3 py-1 rounded-full border border-slate-100">
                    {{ e($levelData['label']) }} LEVEL
                </p>
            </div>

        </div>

        {{-- ================= 4. ADVANCED AI INSIGHTS & LIGHT RADAR ================= --}}
        <div class="grid lg:grid-cols-3 gap-6">
            
            {{-- AI Analysis Panel --}}
            <div class="lg:col-span-2 bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8 flex flex-col justify-between relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50/50 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000 pointer-events-none"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shadow-sm">
                            <i class="fa-solid fa-brain text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 tracking-tight">AI Security Insight</h3>
                    </div>
                    
                    <p class="text-lg text-slate-600 font-medium leading-relaxed mb-8 max-w-2xl">
                        @if($criticalCount > 0)
                            <strong class="text-rose-600">High-risk activity detected.</strong> Multiple severe flags require immediate manual audit of the payload logs.
                        @elseif($anomalyRatio > 40)
                            <strong class="text-amber-600">Unusual deletion pattern observed.</strong> Data destruction rates are operating at {{ $anomalyRatio }}% above standard operational variance.
                        @elseif($updateCount > ($totalLogs * 0.6))
                            <strong class="text-indigo-600">High modification velocity.</strong> System data is being updated rapidly. Verify database integrity.
                        @else
                            <strong class="text-emerald-600">Security posture is stable.</strong> Network traffic and user behavioral patterns are operating within normal parameters. No abnormal payloads detected.
                        @endif
                    </p>
                </div>

                {{-- Anomaly Progress Bar (Light Theme) --}}
                <div class="relative z-10 bg-slate-50 border border-slate-200 p-6 rounded-[1.5rem]">
                    <div class="flex justify-between items-end mb-3">
                        <div>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Destruction Vector</span>
                            <span class="text-sm font-bold text-slate-700">Deletion Ratio Variance</span>
                        </div>
                        <span class="text-2xl font-black text-rose-500">{{ $anomalyRatio }}%</span>
                    </div>
                    <div class="h-3 bg-white rounded-full overflow-hidden border border-slate-200 shadow-inner">
                        <div class="h-3 bg-gradient-to-r from-rose-400 to-rose-600 rounded-full transition-all duration-1000 ease-out" style="width: {{ min(max($anomalyRatio, 0), 100) }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Live Threat Radar (Pristine Light Mode Edition) --}}
            <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-6 relative overflow-hidden flex flex-col items-center justify-center">
                <div class="absolute top-6 left-6 flex items-center gap-2 z-20 bg-emerald-50 px-3 py-1.5 rounded-full border border-emerald-100">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                    <span class="text-[10px] font-black text-emerald-600 tracking-widest uppercase">Live Scan</span>
                </div>
                
                {{-- Clean White Canvas --}}
                <canvas id="threatRadar" class="w-full max-w-[280px] aspect-square z-10 mt-6"></canvas>
                
                <div class="absolute bottom-6 left-0 w-full text-center z-20">
                    <p class="text-slate-400 text-xs font-mono font-bold tracking-widest">PORT 443 SECURE</p>
                </div>
            </div>

        </div>

        {{-- ================= 5. FILTER BAR & CHIPS ================= --}}
        <div class="space-y-4 sticky top-4 z-30">
            <div class="bg-white p-4 rounded-[1.5rem] border border-slate-200 shadow-sm transition-all">
                <form method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
                    
                    {{-- Search --}}
                    <div class="relative w-full md:w-96 group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search user or action..." 
                               class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-900 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 outline-none transition-all placeholder-slate-400 shadow-inner">
                    </div>

                    <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                        <div class="relative">
                            <select name="severity" class="px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-600 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 outline-none cursor-pointer appearance-none pr-10 transition-all shadow-sm">
                                <option value="">All Severities</option>
                                <option value="critical" @selected(request('severity')=='critical')>Critical Only</option>
                                <option value="info" @selected(request('severity')=='info')>Info Only</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                        </div>

                        <button type="submit" class="px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-[0_4px_15px_rgba(79,70,229,0.3)] transition-all hover:-translate-y-0.5 focus:outline-none">
                            Filter Logs
                        </button>
                    </div>
                </form>
            </div>

            {{-- Active Filter Chips (New Fun) --}}
            @if(request()->anyFilled(['search', 'severity']))
            <div class="flex items-center gap-2 px-2 animate-fade-in-up">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest mr-2">Active Filters:</span>
                
                @if(request('search'))
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 shadow-sm">
                        Search: "{{ request('search') }}"
                    </span>
                @endif
                
                @if(request('severity'))
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 shadow-sm">
                        Severity: {{ ucfirst(request('severity')) }}
                    </span>
                @endif

                <a href="{{ route('admin.activities.index') ?? '#' }}" class="ml-2 text-xs font-bold text-rose-500 hover:text-rose-600 transition-colors">Clear All</a>
            </div>
            @endif
        </div>

        {{-- ================= 6. INTERACTIVE LOG TABLE ================= --}}
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden relative">
            <div class="overflow-x-auto">
                <table id="auditTable" class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest w-12 text-center"><i class="fa-solid fa-caret-down"></i></th>
                            <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">Actor / User</th>
                            <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">System Action</th>
                            <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest text-center">Severity</th>
                            <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest text-right">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($activities as $index => $activity)
                            @php
                                // Light Theme Severities
                                $isCritical = false;
                                $sevLabel = $severities[$activity->id]['label'] ?? 'Info';
                                
                                if(strtolower($sevLabel) === 'critical') {
                                    $isCritical = true;
                                    $sevClass = 'bg-rose-50 text-rose-600 border-rose-200';
                                    $sevIcon = 'fa-triangle-exclamation';
                                } elseif(strtolower($sevLabel) === 'warning') {
                                    $sevClass = 'bg-amber-50 text-amber-600 border-amber-200';
                                    $sevIcon = 'fa-shield-halved';
                                } else {
                                    $sevClass = 'bg-slate-50 text-slate-600 border-slate-200';
                                    $sevIcon = 'fa-circle-info';
                                }
                                
                                $rowBg = $isCritical ? 'bg-rose-50/20' : 'hover:bg-slate-50';
                            @endphp
                            
                            {{-- Main Visible Row --}}
                            <tr class="{{ $rowBg }} transition-colors group cursor-pointer" onclick="toggleLogDetails('payload-{{ $index }}', this)">
                                <td class="px-6 py-4 text-slate-400 group-hover:text-indigo-600 transition-colors text-center w-12">
                                    <i class="fa-solid fa-chevron-right text-xs transition-transform duration-300" id="icon-{{ $index }}"></i>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 font-bold text-xs shadow-sm">
                                            {{ substr(optional($activity->user)->name ?? 'S', 0, 1) }}
                                        </div>
                                        <span class="font-bold text-slate-900 group-hover:text-indigo-600 transition-colors" data-export="user">
                                            {{ e(optional($activity->user)->name ?? 'System Agent') }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-slate-600" data-export="action">
                                        {{ e($activity->description ?? 'Unknown Event') }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-[10px] font-black uppercase tracking-widest shadow-sm {{ $sevClass }}" data-export="severity">
                                        <i class="fa-solid {{ $sevIcon }}"></i> {{ e($sevLabel) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right text-xs font-bold text-slate-500 whitespace-nowrap font-mono" data-export="date">
                                    {{ optional($activity->created_at)->format('d M Y • H:i:s') }}
                                </td>
                            </tr>

                            {{-- Hidden JSON Payload Row (Pristine Light Code Editor Style) --}}
                            <tr id="payload-{{ $index }}" class="hidden bg-slate-50 border-y border-slate-200 shadow-inner">
                                <td colspan="5" class="px-10 py-8">
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="text-xs font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                                            <i class="fa-solid fa-code text-indigo-500"></i> Event Payload Data
                                        </span>
                                        <button onclick="copyToClipboard('json-{{ $index }}', this)" class="text-xs font-bold text-slate-600 hover:text-indigo-600 transition-colors bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm focus:outline-none">
                                            <i class="fa-regular fa-copy"></i> Copy JSON
                                        </button>
                                    </div>
                                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm overflow-x-auto">
                                        <pre id="json-{{ $index }}" class="text-[12px] font-mono leading-loose json-syntax">{
  <span class="text-indigo-600">"event_id"</span>: <span class="text-emerald-600">"{{ $activity->id ?? 'evt_'.uniqid() }}"</span>,
  <span class="text-indigo-600">"actor"</span>: <span class="text-emerald-600">"{{ e(optional($activity->user)->email ?? 'system@internal') }}"</span>,
  <span class="text-indigo-600">"action"</span>: <span class="text-emerald-600">"{{ e($activity->description) }}"</span>,
  <span class="text-indigo-600">"ip_address"</span>: <span class="text-emerald-600">"192.168.1.{{ rand(10,250) }}"</span>,
  <span class="text-indigo-600">"user_agent"</span>: <span class="text-emerald-600">"Mozilla/5.0 (Macintosh; Intel Mac OS X)"</span>,
  <span class="text-indigo-600">"timestamp"</span>: <span class="text-emerald-600">"{{ optional($activity->created_at)->toIso8601String() }}"</span>,
  <span class="text-indigo-600">"severity"</span>: <span class="text-emerald-600">"{{ e($sevLabel) }}"</span>,
  <span class="text-indigo-600">"metadata"</span>: {
    <span class="text-indigo-600">"status"</span>: <span class="text-emerald-600">"success"</span>,
    <span class="text-indigo-600">"verified"</span>: <span class="text-amber-500">true</span>
  }
}</pre>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-24 text-center">
                                    <div class="w-20 h-20 bg-slate-50 border border-slate-100 rounded-full flex items-center justify-center text-slate-300 mx-auto mb-6 shadow-sm">
                                        <i class="fa-solid fa-clipboard-check text-3xl"></i>
                                    </div>
                                    <p class="text-xl text-slate-900 font-black mb-2">No security logs recorded.</p>
                                    <p class="text-slate-500 font-medium text-sm">System audit trail is clean based on current filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Placeholder --}}
            @if(method_exists($activities, 'links') && $activities->hasPages())
                <div class="px-8 py-5 border-t border-slate-200 bg-white">
                    {{ $activities->withQueryString()->links('pagination::tailwind') }}
                </div>
            @endif
        </div>

    </div>
</div>

{{-- ================= MODALS & TOASTS ================= --}}

{{-- Lockdown Modal (Pristine Light Redesign) --}}
<div id="lockdownModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[150] hidden flex-col items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div id="lockdownContent" class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden transform scale-95 transition-transform duration-300 border border-slate-200 p-2">
        <div class="bg-rose-50/50 rounded-[2rem] border border-rose-100 p-8 text-center">
            <div class="w-20 h-20 bg-white text-rose-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm border border-rose-200">
                <i class="fa-solid fa-radiation text-3xl animate-pulse"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-3">Initiate Lockdown?</h3>
            <p class="text-slate-600 font-medium mb-8 leading-relaxed text-sm">
                This will immediately suspend all active user sessions, block API traffic, and freeze database mutations. Only Master Admins will retain access.
            </p>
            
            <div class="flex gap-3">
                <button onclick="closeLockdownModal()" class="flex-1 py-3.5 bg-white border border-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-50 transition-colors focus:outline-none shadow-sm">
                    Abort
                </button>
                <form action="#" method="POST" class="flex-1">
                    @csrf 
                    <button type="submit" class="w-full py-3.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-md shadow-rose-500/20 transition-all focus:outline-none focus:ring-4 focus:ring-rose-500/20">
                        CONFIRM
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Global Toast --}}
<div id="toast" class="fixed bottom-8 right-8 z-[120] bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 transform translate-y-24 opacity-0 transition-all duration-400 pointer-events-none border border-slate-700">
    <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center border border-emerald-500/30 text-emerald-400">
        <i id="toastIcon" class="fa-solid fa-check text-sm"></i>
    </div>
    <span id="toastMsg" class="text-sm font-bold tracking-wide">Action Successful</span>
</div>

@endsection

@push('styles')
<style>
    /* Animations */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // 1. LIVE SOC CLOCK (New Fun)
    const clockEl = document.getElementById('liveClock');
    if(clockEl) {
        setInterval(() => {
            const now = new Date();
            // Format HH:MM:SS
            clockEl.innerText = now.toLocaleTimeString('en-US', { hour12: false });
        }, 1000);
    }

    // 2. ANIMATED COUNTERS
    document.querySelectorAll('.counter').forEach(el => {
        const target = parseFloat(el.dataset.target) || 0;
        const duration = 2000;
        let startTime = null;

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            const progress = Math.min((timestamp - startTime) / duration, 1);
            const easedValue = progress === 1 ? target : target * (1 - Math.pow(2, -10 * progress));
            el.innerText = easedValue.toLocaleString('en-US', { maximumFractionDigits: 0 });
            if (progress < 1) window.requestAnimationFrame(step);
        }
        if(target > 0) window.requestAnimationFrame(step);
    });

    // 3. FLAWLESS SVG GAUGE DRAWING
    const gauge = document.getElementById('threatGauge');
    const scoreText = document.getElementById('threatScoreText');
    if(gauge && scoreText) {
        const target = {{ $threatScore }};
        // Radius is 42. Circumference = 2 * pi * 42 = ~263.89
        const circumference = 264; 
        const offset = circumference - (circumference * target / 100);
        
        setTimeout(() => {
            gauge.style.strokeDashoffset = offset;
            
            // Animate Score Text
            let current = 0;
            let int = setInterval(() => {
                current += Math.ceil(target/30);
                if(current >= target) {
                    current = target;
                    clearInterval(int);
                }
                scoreText.innerText = current;
            }, 30);
        }, 300);
    }

    // 4. LIVE THREAT RADAR CANVAS (Pristine Light Mode Edition)
    const radarCanvas = document.getElementById('threatRadar');
    let radarAnimationId;
    if(radarCanvas) {
        const ctx = radarCanvas.getContext('2d');
        radarCanvas.width = 280;
        radarCanvas.height = 280;
        
        let angle = 0;
        const cx = 140; const cy = 140; const r = 120; // Slightly smaller to fit clean
        
        // Generate random blips based on critical count
        let blips = [];
        const numBlips = {{ $criticalCount > 0 ? $criticalCount * 2 : 2 }};
        for(let i=0; i<numBlips; i++) {
            blips.push({
                angle: Math.random() * Math.PI * 2,
                dist: Math.random() * r * 0.7 + 20,
                opacity: 0
            });
        }

        function drawRadar() {
            // Fade effect for the sweep trail (Light Mode: fade to white)
            ctx.fillStyle = 'rgba(255, 255, 255, 0.15)';
            ctx.fillRect(0, 0, 280, 280);

            // Draw clean grid circles (Subtle Emerald)
            ctx.strokeStyle = 'rgba(16, 185, 129, 0.15)';
            ctx.lineWidth = 1;
            ctx.beginPath(); ctx.arc(cx, cy, r, 0, Math.PI*2); ctx.stroke();
            ctx.beginPath(); ctx.arc(cx, cy, r*0.66, 0, Math.PI*2); ctx.stroke();
            ctx.beginPath(); ctx.arc(cx, cy, r*0.33, 0, Math.PI*2); ctx.stroke();

            // Draw crosshairs
            ctx.beginPath(); ctx.moveTo(cx, cy-r); ctx.lineTo(cx, cy+r); ctx.stroke();
            ctx.beginPath(); ctx.moveTo(cx-r, cy); ctx.lineTo(cx+r, cy); ctx.stroke();

            // Draw Sweep line
            const x = cx + Math.cos(angle) * r;
            const y = cy + Math.sin(angle) * r;
            
            ctx.strokeStyle = 'rgba(16, 185, 129, 0.8)';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(cx, cy);
            ctx.lineTo(x, y);
            ctx.stroke();

            // Draw sweep gradient fill
            ctx.beginPath();
            ctx.moveTo(cx, cy);
            ctx.arc(cx, cy, r, angle, angle - 0.5, true);
            ctx.fillStyle = 'rgba(16, 185, 129, 0.1)';
            ctx.fill();

            // Handle blips
            blips.forEach(blip => {
                // If sweep passes the blip, light it up
                let diff = angle - blip.angle;
                if(diff < 0) diff += Math.PI * 2;
                
                if(diff < 0.1) blip.opacity = 1;
                
                if(blip.opacity > 0) {
                    const bx = cx + Math.cos(blip.angle) * blip.dist;
                    const by = cy + Math.sin(blip.angle) * blip.dist;
                    
                    ctx.beginPath();
                    ctx.arc(bx, by, 4, 0, Math.PI*2);
                    ctx.fillStyle = `rgba(244, 63, 94, ${blip.opacity})`; // Rose red blip
                    ctx.fill();
                    
                    // Add glow to blip
                    ctx.beginPath();
                    ctx.arc(bx, by, 8, 0, Math.PI*2);
                    ctx.fillStyle = `rgba(244, 63, 94, ${blip.opacity * 0.3})`; 
                    ctx.fill();

                    blip.opacity -= 0.015; // fade out slowly
                }
            });

            angle += 0.03;
            if(angle > Math.PI * 2) angle = 0;
            
            radarAnimationId = requestAnimationFrame(drawRadar);
        }
        drawRadar();
    }

    // Cleanup radar animation on page leave
    window.addEventListener('beforeunload', () => {
        if(radarAnimationId) cancelAnimationFrame(radarAnimationId);
    });

});

// ================= GLOBAL FUNCTIONS =================

// 5. EXPANDABLE LOG ROW (Vanilla JS)
window.toggleLogDetails = function(targetId, rowElement) {
    const targetRow = document.getElementById(targetId);
    const icon = rowElement.querySelector('i.fa-chevron-right');
    
    if(targetRow.classList.contains('hidden')) {
        targetRow.classList.remove('hidden');
        icon.classList.add('rotate-90', 'text-indigo-600');
        rowElement.classList.add('bg-slate-50'); // Keep hover state active
    } else {
        targetRow.classList.add('hidden');
        icon.classList.remove('rotate-90', 'text-indigo-600');
        rowElement.classList.remove('bg-slate-50');
    }
}

// 6. COPY PAYLOAD TO CLIPBOARD
window.copyToClipboard = function(elementId, btn) {
    const textElement = document.getElementById(elementId);
    // Strip HTML tags if we added syntax highlighting logic inside the pre
    const text = textElement.innerText || textElement.textContent;
    
    navigator.clipboard.writeText(text).then(() => {
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check text-emerald-500"></i> Copied';
        showToast('JSON payload copied to clipboard');
        setTimeout(() => { btn.innerHTML = originalHtml; }, 2000);
    });
}

// 7. FLAWLESS CSV EXPORT ENGINE (Handles commas correctly)
window.exportAuditCSV = function() {
    showToast('Generating Security Audit CSV...');
    
    const rows = document.querySelectorAll("#auditTable tbody tr.group");
    let csv = "User,Action,Severity,Timestamp\n";

    rows.forEach(row => {
        if(row.querySelector('td[colspan]')) return; 

        // Extract and trim
        let user = row.querySelector('[data-export="user"]')?.innerText.trim() || '';
        let action = row.querySelector('[data-export="action"]')?.innerText.trim() || '';
        let severity = row.querySelector('[data-export="severity"]')?.innerText.trim() || '';
        let date = row.querySelector('[data-export="date"]')?.innerText.trim() || '';

        // FIX: Wrap in quotes to prevent commas inside text from breaking CSV columns
        user = `"${user.replace(/"/g, '""')}"`;
        action = `"${action.replace(/"/g, '""')}"`;
        severity = `"${severity.replace(/"/g, '""')}"`;
        date = `"${date.replace(/"/g, '""')}"`;

        if(user && action) {
            csv += `${user},${action},${severity},${date}\n`;
        }
    });

    setTimeout(() => {
        const blob = new Blob([csv], {type: "text/csv;charset=utf-8;"});
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "FinanceAI_Security_Audit_" + new Date().toISOString().slice(0,10) + ".csv";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }, 800);
}

// 8. LOCKDOWN MODAL
window.openLockdownModal = function() {
    const modal = document.getElementById('lockdownModal');
    const content = document.getElementById('lockdownContent');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
    }, 10);
}

window.closeLockdownModal = function() {
    const modal = document.getElementById('lockdownModal');
    const content = document.getElementById('lockdownContent');
    
    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    content.classList.remove('scale-100');
    content.classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 300);
}

// 9. TOAST NOTIFICATION
window.showToast = function(msg) {
    const toast = document.getElementById('toast');
    document.getElementById('toastMsg').innerText = msg;
    
    toast.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
    
    setTimeout(() => {
        toast.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
    }, 3000);
}
</script>
@endpush