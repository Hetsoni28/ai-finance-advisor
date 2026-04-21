@extends('layouts.app')

@section('title', 'Master Node Telemetry | FinanceAI')

@section('content')

@php
    // ================= 1. SAFE DATA EXTRACTION & MATH =================
    $totalUsers      = (int) ($totalUsers ?? 0);
    $totalIncome     = (float) ($totalIncome ?? 0);
    $totalExpenses   = (float) ($totalExpenses ?? 0);

    $months          = $months ?? [];
    $monthlyIncome   = $monthlyIncome ?? [];
    $monthlyExpenses = $monthlyExpenses ?? [];
    $activities      = $activities ?? collect();

    $netRevenue = $totalIncome - $totalExpenses;

    $healthIndex = $totalIncome > 0
        ? round((($netRevenue / $totalIncome) * 100), 1)
        : 0;

    $healthIndex = min(max($healthIndex, -100), 100);
    $isProfit = $netRevenue >= 0;

    // Simulated Enterprise Telemetry (For UI Realism)
    $activeSessions = rand(12, 48);
    $serverLoad = rand(18, 34);
    $dbLatency = rand(8, 22);
@endphp

<div x-data="adminDashboard()" class="min-h-screen bg-[#f8fafc] pb-32 font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden">

    {{-- Pristine Light Ambient Backgrounds --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[900px] h-[900px] bg-indigo-50/60 rounded-full blur-[120px] transition-colors duration-1000"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[800px] h-[800px] bg-emerald-50/50 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 pt-10 relative z-10 space-y-10">

        {{-- ================= 1. COMMAND HEADER & ACTION HUB ================= --}}
        <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-8 bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-indigo-500 to-emerald-400"></div>

            <div class="flex-1">
                <nav class="flex mb-3" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <li>FinanceAI Engine</li>
                        <li><i class="fa-solid fa-chevron-right text-[8px] mx-1 opacity-50"></i></li>
                        <li class="text-indigo-600 truncate max-w-[200px]">Master Node</li>
                    </ol>
                </nav>
                <div class="flex items-center gap-4 mb-2">
                    <h1 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight">Global Telemetry</h1>
                </div>
                <p class="text-slate-500 text-sm font-medium flex items-center gap-2">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    Live System Status &bull; <span id="liveClock" class="font-mono font-bold"></span>
                </p>
            </div>

            {{-- Quick Action Grid --}}
            <div class="flex flex-wrap items-center gap-3 relative z-10">
                
                {{-- 🔥 NEW FUN: Interactive Sync Button --}}
                <button @click="syncData()" class="px-5 py-3.5 bg-slate-50 border border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-white hover:text-indigo-600 hover:border-indigo-300 transition-all flex items-center gap-2 shadow-sm focus:outline-none w-full sm:w-auto justify-center">
                    <i class="fa-solid fa-rotate text-indigo-500" :class="isSyncing ? 'animate-spin' : ''"></i> 
                    <span x-text="isSyncing ? 'Syncing...' : 'Sync Telemetry'"></span>
                </button>

                <a href="{{ Route::has('admin.users.index') ? route('admin.users.index') : '#' }}" class="px-6 py-3.5 bg-slate-900 text-white rounded-xl font-bold text-sm shadow-lg shadow-slate-900/20 hover:bg-indigo-600 hover:shadow-indigo-500/30 transition-all flex items-center gap-2 hover:-translate-y-0.5 focus:outline-none w-full sm:w-auto justify-center">
                    <i class="fa-solid fa-users-gear text-indigo-400"></i> Manage Identities
                </a>
            </div>
        </div>

        {{-- ================= 2. MULTI-COLOR KPI GRID ================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            {{-- Total Users (Sky) --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-sky-500/10 rounded-full blur-xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-6 relative z-10">
                    <div class="w-12 h-12 bg-sky-50 text-sky-600 rounded-2xl flex items-center justify-center border border-sky-100 shadow-sm"><i class="fa-solid fa-users text-lg"></i></div>
                    <span class="px-2.5 py-1 rounded-md text-[9px] font-black uppercase tracking-widest border shadow-sm bg-sky-50 text-sky-700 border-sky-200">Active</span>
                </div>
                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1 relative z-10">Total Network Nodes</p>
                <h2 class="text-3xl font-black text-sky-600 kpi-counter relative z-10" data-val="{{ $totalUsers }}">0</h2>
            </div>

            {{-- Platform Income (Emerald) --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-6 relative z-10">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center border border-emerald-100 shadow-sm"><i class="fa-solid fa-arrow-trend-up text-lg"></i></div>
                </div>
                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1 relative z-10">Global Capital Inflow</p>
                <h2 class="text-3xl font-black text-emerald-600 kpi-currency relative z-10" data-val="{{ $totalIncome }}">₹0</h2>
            </div>

            {{-- Platform Expenses (Rose) --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-rose-500/10 rounded-full blur-xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-6 relative z-10">
                    <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center border border-rose-100 shadow-sm"><i class="fa-solid fa-arrow-trend-down text-lg"></i></div>
                </div>
                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1 relative z-10">Global Capital Burn</p>
                <h2 class="text-3xl font-black text-rose-600 kpi-currency relative z-10" data-val="{{ $totalExpenses }}">₹0</h2>
            </div>

            {{-- Net Revenue & Health (Dynamic) --}}
            @php $sysColor = $isProfit ? 'indigo' : 'rose'; @endphp
            <div class="bg-white p-6 rounded-[2rem] border border-{{ $sysColor }}-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-{{ $sysColor }}-500/10 rounded-full blur-2xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-6 relative z-10">
                    <div class="w-12 h-12 bg-{{ $sysColor }}-50 text-{{ $sysColor }}-600 rounded-2xl flex items-center justify-center border border-{{ $sysColor }}-100 shadow-sm"><i class="fa-solid fa-scale-balanced text-lg"></i></div>
                </div>
                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1 relative z-10">Net System Revenue</p>
                <div class="flex items-baseline gap-2 relative z-10">
                    <h2 class="text-3xl font-black text-{{ $sysColor }}-600 kpi-currency" data-val="{{ abs($netRevenue) }}">₹0</h2>
                    <span class="text-xs font-bold {{ $isProfit ? 'text-emerald-500' : 'text-rose-500' }}">{{ $isProfit ? '+' : '-' }}</span>
                </div>
                <div class="mt-4 h-1.5 bg-slate-100 rounded-full overflow-hidden relative z-10 shadow-inner flex">
                    <div class="h-full bg-{{ $sysColor }}-500 rounded-full transition-all duration-1000 ease-out" style="width: {{ min(abs($healthIndex), 100) }}%"></div>
                </div>
            </div>

        </div>

        {{-- ================= 3. CHARTS & TELEMETRY ================= --}}
        <div class="grid lg:grid-cols-12 gap-8 items-start">
            
            {{-- Global Velocity Chart --}}
            <div class="lg:col-span-8 bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm flex flex-col relative h-full">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight">Platform Financial Velocity</h3>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">6-Month Aggregation</p>
                    </div>
                    <div class="flex gap-3 items-center hidden sm:flex bg-slate-50 px-4 py-2 rounded-xl border border-slate-100 shadow-inner">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-sm"></span><span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Inflow</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shadow-sm ml-2"></span><span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Outflow</span>
                    </div>
                </div>
                
                <div class="flex-1 relative min-h-[350px] w-full">
                    @if(count($months))
                        <canvas id="financeChart"></canvas>
                    @else
                        <div class="absolute inset-0 flex items-center justify-center text-slate-400 font-bold bg-slate-50/90 backdrop-blur-sm z-10 rounded-2xl border border-slate-200 border-dashed shadow-inner">
                            <div class="text-center">
                                <i class="fa-solid fa-chart-line text-4xl text-slate-300 mb-4 block"></i>
                                Awaiting Temporal Data Integration
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- System Health & Activity Sidebar --}}
            <div class="lg:col-span-4 space-y-8 h-full flex flex-col">
                
                {{-- 🚨 FIX: Infrastructure Load Widget (Now pristine Light White) --}}
                <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8 relative overflow-hidden group">
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>

                    <h3 class="text-lg font-black text-slate-900 tracking-tight mb-6 relative z-10 flex items-center gap-2">
                        <i class="fa-solid fa-server text-indigo-500"></i> Infrastructure Load
                    </h3>

                    <div class="space-y-5 relative z-10">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Active Sessions</span>
                                <span class="text-xs font-black text-slate-900 font-mono">{{ $activeSessions }}</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 shadow-inner">
                                <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ ($activeSessions/100)*100 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">CPU Allocation</span>
                                <span class="text-xs font-black text-slate-900 font-mono">{{ $serverLoad }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 shadow-inner">
                                <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $serverLoad }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">DB Latency</span>
                                <span class="text-xs font-black text-slate-900 font-mono">{{ $dbLatency }}ms</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 shadow-inner">
                                <div class="bg-sky-500 h-1.5 rounded-full" style="width: {{ ($dbLatency/50)*100 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Cryptographic Activity Timeline --}}
                <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm flex flex-col flex-1 relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-slate-100/50 rounded-full blur-3xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                    
                    <div class="flex items-center justify-between mb-8 relative z-10">
                        <h3 class="text-lg font-black text-slate-900 tracking-tight">Global Audit Log</h3>
                        <a href="{{ Route::has('admin.activities.index') ? route('admin.activities.index') : '#' }}" class="text-[10px] font-black text-indigo-600 uppercase tracking-widest hover:text-indigo-800 transition-colors bg-indigo-50 px-3 py-1.5 rounded-lg">View All</a>
                    </div>

                    @if($activities->count())
                        <div class="relative flex-1 z-10">
                            {{-- Timeline Line --}}
                            <div class="absolute left-[15px] top-2 bottom-2 w-px bg-slate-200"></div>

                            <ul class="space-y-6 relative z-10">
                                @foreach($activities as $activity)
                                    @php
                                        // Smart Event Parser for Icons and Colors
                                        $desc = strtolower($activity->description ?? '');
                                        $actColor = 'slate'; $actIcon = 'fa-circle-dot';
                                        
                                        if (str_contains($desc, 'block') || str_contains($desc, 'suspend')) { $actColor = 'rose'; $actIcon = 'fa-lock'; }
                                        elseif (str_contains($desc, 'delete') || str_contains($desc, 'purge')) { $actColor = 'rose'; $actIcon = 'fa-trash'; }
                                        elseif (str_contains($desc, 'create') || str_contains($desc, 'init')) { $actColor = 'emerald'; $actIcon = 'fa-plus'; }
                                        elseif (str_contains($desc, 'login') || str_contains($desc, 'auth')) { $actColor = 'sky'; $actIcon = 'fa-right-to-bracket'; }
                                    @endphp
                                    <li class="flex items-start gap-4 group/item">
                                        <div class="w-8 h-8 rounded-full bg-white border-2 border-{{ $actColor }}-100 flex items-center justify-center shrink-0 shadow-sm z-10 group-hover/item:border-{{ $actColor }}-400 group-hover/item:scale-110 transition-all duration-300">
                                            <i class="fa-solid {{ $actIcon }} text-[10px] text-{{ $actColor }}-500"></i>
                                        </div>
                                        <div class="pt-1.5 flex-1 min-w-0">
                                            <p class="text-xs font-bold text-slate-700 group-hover/item:text-slate-900 truncate transition-colors">
                                                {{ $activity->description ?? 'System event recorded' }}
                                            </p>
                                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mt-1 font-mono">
                                                {{ optional($activity->created_at)->diffForHumans() }} 
                                                @if($activity->causer) &bull; By {{ $activity->causer->name }} @endif
                                            </p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="flex-1 flex flex-col items-center justify-center text-center py-10 relative z-10">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center border border-slate-100 mb-4 shadow-inner">
                                <i class="fa-solid fa-clipboard-check text-2xl text-slate-300"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-500">Audit ledger is empty.</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('adminDashboard', () => ({
        isSyncing: false,
        
        syncData() {
            this.isSyncing = true;
            // Simulated network delay for realistic UX
            setTimeout(() => {
                this.isSyncing = false;
                // Dispatch event to global toast (if exists in layout)
                if (typeof this.$dispatch === 'function') {
                    this.$dispatch('notify', { message: 'Telemetry successfully synchronized.', type: 'success' });
                }
            }, 1200);
        }
    }));
});

document.addEventListener("DOMContentLoaded", function() {

    // 1. Live Clock Engine for Master Node Feel
    setInterval(() => {
        const clock = document.getElementById('liveClock');
        if(clock) clock.innerText = new Date().toLocaleTimeString('en-US', {hour12: false}) + ' UTC';
    }, 1000);

    // 2. Flawless Cubic-Bezier Number Animator
    const animateValue = (el, isCurrency) => {
        let target = parseFloat(el.dataset.val || 0);
        let duration = 2000;
        let startTime = null;

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            let progress = Math.min((timestamp - startTime) / duration, 1);
            // Ease out cubic
            let eased = progress === 1 ? target : target * (1 - Math.pow(1 - progress, 3)); 
            
            el.innerText = isCurrency 
                ? '₹' + eased.toLocaleString('en-IN', { maximumFractionDigits: 0 })
                : Math.round(eased).toLocaleString('en-IN');
                
            if (progress < 1) window.requestAnimationFrame(step);
        }
        if(target > 0) window.requestAnimationFrame(step);
    };

    document.querySelectorAll('.kpi-currency').forEach(el => animateValue(el, true));
    document.querySelectorAll('.kpi-counter').forEach(el => animateValue(el, false));

    // 3. Enterprise Chart.js Configuration
    if(typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#64748b';
        
        // Y-Axis Currency Formatter (10L, 5Cr, 50k)
        const formatNumber = (num) => {
            if(num >= 10000000) return '₹' + (num / 10000000).toFixed(1) + 'Cr';
            if(num >= 100000) return '₹' + (num / 100000).toFixed(1) + 'L';
            if(num >= 1000) return '₹' + (num / 1000).toFixed(1) + 'k';
            return '₹' + num;
        };

        const labels = @json($months);
        const incData = @json($monthlyIncome);
        const expData = @json($monthlyExpenses);

        const tooltipConfig = {
            backgroundColor: '#0f172a', titleColor: '#fff', bodyColor: '#cbd5e1',
            padding: 12, cornerRadius: 8, displayColors: true, boxPadding: 4,
            callbacks: { label: (ctx) => ' ₹' + ctx.parsed.y.toLocaleString('en-IN') }
        };

        const finCtx = document.getElementById('financeChart');
        if(finCtx && labels.length > 0) {
            const tCtx = finCtx.getContext('2d');
            
            // Beautiful Dynamic Gradients
            const incGrad = tCtx.createLinearGradient(0, 0, 0, 350);
            incGrad.addColorStop(0, 'rgba(16, 185, 129, 0.4)'); 
            incGrad.addColorStop(1, 'rgba(16, 185, 129, 0.0)');
            
            const expGrad = tCtx.createLinearGradient(0, 0, 0, 350);
            expGrad.addColorStop(0, 'rgba(244, 63, 94, 0.4)'); 
            expGrad.addColorStop(1, 'rgba(244, 63, 94, 0.0)');

            new Chart(tCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        { 
                            label: 'Platform Inflow', 
                            data: incData, 
                            borderColor: '#10b981', 
                            backgroundColor: incGrad, 
                            borderWidth: 3, 
                            fill: true, 
                            tension: 0.4, 
                            pointRadius: 4, 
                            pointBackgroundColor: '#fff', 
                            pointBorderWidth: 2,
                            pointHoverRadius: 6
                        },
                        { 
                            label: 'Platform Burn', 
                            data: expData, 
                            borderColor: '#f43f5e', 
                            backgroundColor: expGrad, 
                            borderWidth: 3, 
                            fill: true, 
                            tension: 0.4, 
                            pointRadius: 4, 
                            pointBackgroundColor: '#fff', 
                            pointBorderWidth: 2,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true, 
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { 
                        legend: { display: false }, 
                        tooltip: tooltipConfig 
                    },
                    scales: { 
                        y: { 
                            beginAtZero: true, 
                            grid: { color: '#f1f5f9', drawBorder: false }, 
                            ticks: { callback: function(value) { return formatNumber(value); } } 
                        },
                        x: { 
                            grid: { display: false }, 
                            ticks: { font: { weight: 'bold' } } 
                        }
                    }
                }
            });
        }
    }
});
</script>
@endpush