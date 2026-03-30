@extends('layouts.app')

@section('title', 'Financial Command Center | FinanceAI')

@section('content')

@php
    // ================= 1. BULLETPROOF DATA EXTRACTION & MATH =================
    $analysis = $analysis ?? [];

    $income      = (float)($analysis['totalIncome'] ?? 0);
    $expense     = (float)($analysis['totalExpense'] ?? 0);
    $savings     = (float)($analysis['savings'] ?? ($income - $expense));
    $rate        = (float)($analysis['savingRate'] ?? ($income > 0 ? ($savings / $income) * 100 : 0));
    $score       = (int)($analysis['score'] ?? 85); // Safe default for UI rendering
    $risk        = $analysis['riskLevel'] ?? 'Unknown';
    $runway      = (int)($analysis['runway'] ?? 6);

    // Chart Data Arrays
    $labels      = $analysis['labels'] ?? [];
    $incomeData  = $analysis['incomeSeries'] ?? [];
    $expenseData = $analysis['expenseSeries'] ?? [];
    $netWorthData= $analysis['netWorthSeries'] ?? [];
    
    // Safely parse categories for Chart.js
    $rawCatLabels = $analysis['categoryLabels'] ?? [];
    $rawCatSeries = $analysis['categorySeries'] ?? [];
    $catLabels = is_array($rawCatLabels) ? $rawCatLabels : (method_exists($rawCatLabels, 'toArray') ? $rawCatLabels->toArray() : []);
    $catData = is_array($rawCatSeries) ? $rawCatSeries : (method_exists($rawCatSeries, 'toArray') ? $rawCatSeries->toArray() : []);

    // Telemetry Data
    $recentIncomes = $recentIncomes ?? collect();
    $recentExpenses = $recentExpenses ?? collect();

    // ================= 2. AI HEURISTIC & COLOR THEME ENGINE =================
    if ($score >= 80) {
        $sysColor = 'emerald'; $sysStatus = 'Optimal'; $sysIcon = 'fa-shield-check';
        $aiMessage = 'Personal liquidity is operating at peak efficiency. Capital retention is exceptionally high.';
        $aiTips = ['Invest surplus capital in index funds', 'Maximize automated retirement allocations'];
    } elseif ($score >= 50) {
        $sysColor = 'indigo'; $sysStatus = 'Stable'; $sysIcon = 'fa-check-circle';
        $aiMessage = 'Cashflow is stable. Maintaining a healthy median savings velocity across trailing 90 days.';
        $aiTips = ['Audit recurring SaaS subscriptions', 'Optimize end-of-year tax allocations'];
    } elseif ($score >= 20) {
        $sysColor = 'amber'; $sysStatus = 'Warning'; $sysIcon = 'fa-triangle-exclamation';
        $aiMessage = 'Elevated burn rate detected. Neural engine recommends restricting discretionary outflows.';
        $aiTips = ['Freeze non-essential discretionary spending', 'Review highest outflow categories'];
    } else {
        $sysColor = 'rose'; $sysStatus = 'Critical Risk'; $sysIcon = 'fa-skull-crossbones';
        $aiMessage = 'Deficit Alert! Personal expenses currently exceed inbound capital. Immediate audit required.';
        $aiTips = ['Initiate emergency liquidity protocol', 'Liquidate non-essential depreciating assets'];
    }

    // 🚨 TAILWIND SAFE-LIST ENGINE: Guarantees compilation for dynamic classes
    $colorMap = [
        'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'border-emerald-200', 'shadow' => 'shadow-emerald-500/20', 'accent' => 'bg-emerald-500', 'glow' => 'rgba(16,185,129,0.15)'],
        'rose'    => ['bg' => 'bg-rose-50',    'text' => 'text-rose-600',    'border' => 'border-rose-200',    'shadow' => 'shadow-rose-500/20',    'accent' => 'bg-rose-500',    'glow' => 'rgba(244,63,94,0.15)'],
        'amber'   => ['bg' => 'bg-amber-50',   'text' => 'text-amber-600',   'border' => 'border-amber-200',   'shadow' => 'shadow-amber-500/20',   'accent' => 'bg-amber-500',   'glow' => 'rgba(245,158,11,0.15)'],
        'indigo'  => ['bg' => 'bg-indigo-50',  'text' => 'text-indigo-600',  'border' => 'border-indigo-200',  'shadow' => 'shadow-indigo-500/20',  'accent' => 'bg-indigo-500',  'glow' => 'rgba(79,70,229,0.15)'],
        'sky'     => ['bg' => 'bg-sky-50',     'text' => 'text-sky-600',     'border' => 'border-sky-200',     'shadow' => 'shadow-sky-500/20',     'accent' => 'bg-sky-500',     'glow' => 'rgba(14,165,233,0.15)'],
    ];
    $activeTheme = $colorMap[$sysColor];

    // ================= 3. MASTER LEDGER NORMALIZATION ENGINE =================
    $unifiedLedger = collect();
    
    foreach($recentIncomes as $inc) {
        $unifiedLedger->push((object)[
            'id' => 'INC-'.$inc->id ?? rand(),
            'type' => 'income',
            'title' => $inc->source ?? 'Capital Deposit',
            'amount' => (float)$inc->amount,
            'date' => $inc->income_date ?? $inc->created_at ?? now(),
            'edit_route' => Route::has('user.incomes.edit') ? route('user.incomes.edit', $inc->id ?? 0) : '#',
            'delete_route' => Route::has('user.incomes.destroy') ? route('user.incomes.destroy', $inc->id ?? 0) : '#',
        ]);
    }
    
    foreach($recentExpenses as $exp) {
        $unifiedLedger->push((object)[
            'id' => 'EXP-'.$exp->id ?? rand(),
            'type' => 'expense',
            'title' => $exp->category ?? 'Capital Burn',
            'amount' => (float)$exp->amount,
            'date' => $exp->expense_date ?? $exp->created_at ?? now(),
            'edit_route' => Route::has('user.expenses.edit') ? route('user.expenses.edit', $exp->id ?? 0) : '#',
            'delete_route' => Route::has('user.expenses.destroy') ? route('user.expenses.destroy', $exp->id ?? 0) : '#',
        ]);
    }
    
    $unifiedLedger = $unifiedLedger->sortByDesc('date')->values();
@endphp

<div x-data="userDashboardEngine()" class="min-h-screen bg-[#f8fafc] pb-32 font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden">

    {{-- 🔥 BEAST MODE: SPA Progress Bar --}}
    <div x-show="isNavigating" x-cloak class="fixed top-0 left-0 h-1.5 bg-indigo-600 z-[99999] transition-all duration-300 ease-out shadow-[0_0_10px_rgba(79,70,229,0.8)]" :style="`width: ${navProgress}%`"></div>

    {{-- ================= 0. PRISTINE AMBIENT BACKGROUND ================= --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        {{-- High-End Engineering Grid --}}
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCI+PHBhdGggZD0iTTAgMGg0MHY0MEgwVjB6bTIwIDIwaDIwdjIwSDIwaC0yMHptMCAwaC0yMHYtMjBoMjB2MjB6IiBmaWxsPSIjZThlYWVkIiBmaWxsLW9wYWNpdHk9IjAuNiIgZmlsbC1ydWxlPSJldmVub2RkIi8+PC9zdmc+')] opacity-60"></div>
        <div class="absolute top-[-10%] left-[-10%] w-[900px] h-[900px] bg-indigo-500/5 rounded-full blur-[150px] transition-colors duration-1000"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[800px] h-[800px] bg-sky-500/5 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 pt-8 relative z-10 space-y-10">

        {{-- ================= SUCCESS NOTIFICATION ================= --}}
        @if(session('success'))
            <div x-show="showSuccess" x-init="setTimeout(() => showSuccess = false, 5000)" 
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-[-1rem] scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-[-1rem] scale-95"
                 class="bg-white/80 backdrop-blur-xl border border-emerald-200 rounded-[1.5rem] p-4 flex items-center justify-between shadow-[0_10px_30px_rgba(16,185,129,0.1)] max-w-3xl mx-auto mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-[12px] bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-emerald-500/30 border border-emerald-400"><i class="fa-solid fa-check text-sm"></i></div>
                    <div>
                        <p class="text-[10px] font-black text-emerald-900 tracking-widest uppercase mb-0.5">System Notice</p>
                        <p class="text-sm font-bold text-emerald-700 leading-tight">{{ session('success') }}</p>
                    </div>
                </div>
                <button @click="showSuccess = false" class="text-emerald-600 hover:bg-emerald-100 w-8 h-8 rounded-lg flex items-center justify-center transition-colors focus:outline-none"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        {{-- ================= 1. COMMAND HEADER & ACTION HUB ================= --}}
        <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-8 bg-white/90 backdrop-blur-2xl p-8 md:p-10 rounded-[2.5rem] border border-white shadow-[0_8px_30px_rgba(0,0,0,0.04)] relative overflow-hidden group hover:shadow-[0_20px_60px_-15px_rgba(0,0,0,0.08)] transition-all duration-500">
            {{-- Accent Line --}}
            <div class="absolute top-0 left-0 w-2 h-full bg-gradient-to-b from-indigo-500 to-sky-400"></div>
            {{-- Decorative Orb --}}
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-indigo-50/80 rounded-full blur-3xl pointer-events-none group-hover:scale-150 transition-transform duration-1000"></div>

            <div class="flex-1 relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-2.5 py-1 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded-lg text-[9px] font-black uppercase tracking-widest flex items-center gap-1.5 shadow-sm">
                        <span class="relative flex h-1.5 w-1.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span><span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-indigo-500"></span></span>
                        Network Active
                    </span>
                    <span class="text-[10px] font-bold text-slate-400 font-mono tracking-widest uppercase">IP: <span x-text="clientIp">Scanning...</span></span>
                </div>
                
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-none mb-3">
                    <span x-text="greeting">Welcome</span>, {{ explode(' ', auth()->user()->name ?? 'Operator')[0] }}.
                </h1>
                <p class="text-slate-500 text-base md:text-lg font-medium max-w-xl">
                    Your cryptographic dashboard is fully synced. Review your telemetry and algorithmic forecasts below.
                </p>
            </div>

            {{-- Quick Action Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 w-full xl:w-auto relative z-10">
                <button @click="syncData()" @mouseenter="playHoverSound()" class="px-5 py-4 bg-slate-50 border border-slate-200 text-slate-600 rounded-[1.25rem] font-bold text-[10px] uppercase tracking-widest shadow-sm hover:bg-white hover:text-indigo-600 hover:border-indigo-300 hover:-translate-y-1 hover:shadow-lg transition-all flex flex-col items-center justify-center gap-3 focus:outline-none group/btn">
                    <i class="fa-solid fa-rotate text-indigo-500 text-2xl group-hover/btn:animate-spin"></i> 
                    <span x-text="isSyncing ? 'Syncing...' : 'Sync Node'"></span>
                </button>
                
                <a href="{{ Route::has('user.incomes.create') ? route('user.incomes.create') : '#' }}" @click.prevent="simulateNavigation('{{ Route::has('user.incomes.create') ? route('user.incomes.create') : '#' }}')" @mouseenter="playHoverSound()" class="px-5 py-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-[1.25rem] font-bold text-[10px] uppercase tracking-widest shadow-sm hover:bg-emerald-500 hover:text-white hover:-translate-y-1 hover:shadow-lg hover:shadow-emerald-500/30 transition-all flex flex-col items-center justify-center gap-3 focus:outline-none group/in">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center group-hover/in:bg-white group-hover/in:text-emerald-500 transition-colors shadow-inner"><i class="fa-solid fa-arrow-trend-up text-sm"></i></div>
                    Log Inflow
                </a>
                
                <a href="{{ Route::has('user.expenses.create') ? route('user.expenses.create') : '#' }}" @click.prevent="simulateNavigation('{{ Route::has('user.expenses.create') ? route('user.expenses.create') : '#' }}')" @mouseenter="playHoverSound()" class="px-5 py-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-[1.25rem] font-bold text-[10px] uppercase tracking-widest shadow-sm hover:bg-rose-500 hover:text-white hover:-translate-y-1 hover:shadow-lg hover:shadow-rose-500/30 transition-all flex flex-col items-center justify-center gap-3 focus:outline-none group/out col-span-2 md:col-span-1">
                    <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center group-hover/out:bg-white group-hover/out:text-rose-500 transition-colors shadow-inner"><i class="fa-solid fa-arrow-trend-down text-sm"></i></div>
                    Log Outflow
                </a>
            </div>
        </div>

        {{-- ================= 2. MULTI-COLOR KPI GRID (LIGHT THEME) ================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 perspective-[1500px]">
            
            {{-- Gross Inflow (Emerald) --}}
            <div @mousemove="handleTilt($event, $el)" @mouseleave="resetTilt($el)" class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm hover:shadow-[0_20px_50px_-10px_rgba(16,185,129,0.15)] hover:-translate-y-1 transition-all duration-500 relative overflow-hidden transform-style-3d group">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-6 relative z-10 translate-z-20">
                    <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-[1rem] flex items-center justify-center border border-emerald-100 shadow-sm group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300"><i class="fa-solid fa-money-bill-trend-up text-xl"></i></div>
                    <a href="{{ Route::has('user.incomes.create') ? route('user.incomes.create') : '#' }}" @click.prevent="simulateNavigation('{{ Route::has('user.incomes.create') ? route('user.incomes.create') : '#' }}')" class="w-8 h-8 rounded-full bg-slate-50 border border-slate-200 text-slate-400 flex items-center justify-center hover:bg-emerald-500 hover:text-white hover:border-emerald-500 transition-colors opacity-0 group-hover:opacity-100 focus:outline-none"><i class="fa-solid fa-plus text-[10px]"></i></a>
                </div>
                <div class="translate-z-30 relative z-10">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Total Capital Inflow</p>
                    <div class="flex items-start gap-1">
                        <span class="text-2xl font-bold text-slate-300 mt-1">₹</span>
                        <h2 class="text-4xl md:text-5xl font-black text-emerald-600 kpi-number tracking-tighter" data-val="{{ $income }}">0</h2>
                    </div>
                </div>
            </div>

            {{-- Total Outflow (Rose) --}}
            <div @mousemove="handleTilt($event, $el)" @mouseleave="resetTilt($el)" class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm hover:shadow-[0_20px_50px_-10px_rgba(244,63,94,0.15)] hover:-translate-y-1 transition-all duration-500 relative overflow-hidden transform-style-3d group">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-rose-500/10 rounded-full blur-2xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-6 relative z-10 translate-z-20">
                    <div class="w-14 h-14 bg-rose-50 text-rose-600 rounded-[1rem] flex items-center justify-center border border-rose-100 shadow-sm group-hover:scale-110 group-hover:-rotate-6 transition-transform duration-300"><i class="fa-solid fa-fire-flame-curved text-xl"></i></div>
                    <a href="{{ Route::has('user.expenses.create') ? route('user.expenses.create') : '#' }}" @click.prevent="simulateNavigation('{{ Route::has('user.expenses.create') ? route('user.expenses.create') : '#' }}')" class="w-8 h-8 rounded-full bg-slate-50 border border-slate-200 text-slate-400 flex items-center justify-center hover:bg-rose-500 hover:text-white hover:border-rose-500 transition-colors opacity-0 group-hover:opacity-100 focus:outline-none"><i class="fa-solid fa-plus text-[10px]"></i></a>
                </div>
                <div class="translate-z-30 relative z-10">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Total Capital Burn</p>
                    <div class="flex items-start gap-1">
                        <span class="text-2xl font-bold text-slate-300 mt-1">₹</span>
                        <h2 class="text-4xl md:text-5xl font-black text-rose-600 kpi-number tracking-tighter" data-val="{{ $expense }}">0</h2>
                    </div>
                </div>
            </div>

            {{-- Net Savings (Indigo) --}}
            <div @mousemove="handleTilt($event, $el)" @mouseleave="resetTilt($el)" class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm hover:shadow-[0_20px_50px_-10px_rgba(79,70,229,0.15)] hover:-translate-y-1 transition-all duration-500 relative overflow-hidden transform-style-3d group">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-6 relative z-10 translate-z-20">
                    <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-[1rem] flex items-center justify-center border border-indigo-100 shadow-sm group-hover:scale-110 transition-transform duration-300"><i class="fa-solid fa-scale-balanced text-xl"></i></div>
                    <div class="px-2.5 py-1 rounded-lg bg-indigo-50 border border-indigo-100 text-[9px] font-black uppercase tracking-widest text-indigo-600">{{ number_format($rate, 1) }}% Rate</div>
                </div>
                <div class="translate-z-30 relative z-10">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Net Retained Capital</p>
                    <div class="flex items-start gap-1">
                        <span class="text-2xl font-bold text-slate-300 mt-1">₹</span>
                        <h2 class="text-4xl md:text-5xl font-black text-indigo-600 kpi-number tracking-tighter" data-val="{{ abs($savings) }}">0</h2>
                    </div>
                </div>
            </div>

            {{-- AI Stability Score (Dynamic Light Theme) --}}
            <div @mousemove="handleTilt($event, $el)" @mouseleave="resetTilt($el)" class="bg-white p-8 rounded-[2.5rem] border {{ $activeTheme['border'] }} shadow-sm hover:shadow-[0_20px_50px_-10px_{{ $activeTheme['glow'] }}] hover:-translate-y-1 transition-all duration-500 relative overflow-hidden transform-style-3d group">
                <div class="absolute -right-10 -bottom-10 w-40 h-40 {{ $activeTheme['accent'] }} opacity-10 rounded-full blur-3xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-6 relative z-10 translate-z-20">
                    <div class="w-14 h-14 {{ $activeTheme['bg'] }} {{ $activeTheme['text'] }} rounded-[1rem] flex items-center justify-center border {{ $activeTheme['border'] }} shadow-sm group-hover:scale-110 transition-transform duration-300"><i class="fa-solid fa-brain text-xl"></i></div>
                    <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border shadow-sm bg-white {{ $activeTheme['text'] }} {{ $activeTheme['border'] }} group-hover:{{ $activeTheme['bg'] }} transition-colors">
                        {{ $sysStatus }}
                    </span>
                </div>
                <div class="translate-z-30 relative z-10">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">AI Stability Score</p>
                    <div class="flex items-baseline gap-1.5">
                        <h2 class="text-4xl md:text-5xl font-black {{ $activeTheme['text'] }} kpi-number tracking-tighter" data-val="{{ $score }}">0</h2>
                        <span class="text-lg font-bold opacity-50 {{ $activeTheme['text'] }}">/100</span>
                    </div>
                    <div class="mt-5 w-full h-1.5 bg-slate-100 rounded-full overflow-hidden border border-slate-200/50 shadow-inner">
                        <div class="h-full {{ $activeTheme['accent'] }} rounded-full transition-all duration-1000 ease-out shadow-[0_0_5px_{{ $activeTheme['glow'] }}]" style="width: {{ min(abs($score), 100) }}%"></div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ================= 3. MULTI-CHART ANALYTICS & AI ================= --}}
        <div class="grid lg:grid-cols-12 gap-8 items-start">
            
            {{-- Velocity Line Chart --}}
            <div class="lg:col-span-8 bg-white/90 backdrop-blur-xl p-8 rounded-[2.5rem] border border-white shadow-[0_8px_30px_rgba(0,0,0,0.04)] flex flex-col relative h-full group hover:shadow-[0_20px_50px_-10px_rgba(0,0,0,0.08)] transition-shadow duration-500">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                    <div>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight">Financial Velocity Trajectory</h3>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Trailing Telemetry</p>
                    </div>
                    <div class="flex items-center bg-slate-50 p-1 rounded-xl border border-slate-200 shadow-inner">
                        <button class="px-4 py-1.5 rounded-lg bg-white text-slate-900 text-[10px] font-black uppercase tracking-widest shadow-sm">Monthly</button>
                        <button class="px-4 py-1.5 rounded-lg text-slate-500 hover:text-slate-900 text-[10px] font-black uppercase tracking-widest transition-colors focus:outline-none">Quarterly</button>
                    </div>
                </div>
                
                <div class="flex-1 relative min-h-[350px] w-full">
                    <canvas id="financeChart"></canvas>
                    @if(empty($labels))
                        <div class="absolute inset-0 flex items-center justify-center z-10 bg-white/80 backdrop-blur-sm rounded-3xl border-2 border-dashed border-slate-200">
                            <div class="text-center p-8">
                                <div class="w-16 h-16 bg-slate-50 rounded-[1.5rem] flex items-center justify-center mx-auto mb-4 border border-slate-100 shadow-inner">
                                    <i class="fa-solid fa-chart-line text-2xl text-slate-300"></i>
                                </div>
                                <h4 class="text-slate-900 font-black text-lg mb-1 tracking-tight">Awaiting Telemetry</h4>
                                <p class="text-slate-500 font-medium text-sm">Add income or expense records to generate neural forecasts.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-4 space-y-8 h-full flex flex-col">
                {{-- AI Diagnostic Panel (Animated Typewriter) --}}
                <div class="{{ $activeTheme['bg'] }} bg-opacity-80 backdrop-blur-md border {{ $activeTheme['border'] }} rounded-[2.5rem] p-8 shadow-sm relative overflow-hidden group hover:shadow-lg transition-all duration-500">
                    <div class="absolute -right-10 -top-10 {{ $activeTheme['text'] }} opacity-10 text-9xl pointer-events-none group-hover:scale-110 group-hover:-rotate-6 transition-transform duration-700"><i class="fa-solid fa-robot"></i></div>
                    
                    <h4 class="text-[11px] font-black {{ $activeTheme['text'] }} uppercase tracking-widest mb-4 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-white flex items-center justify-center border {{ $activeTheme['border'] }} shadow-sm"><i class="fa-solid {{ $sysIcon }} text-[11px]"></i></span> 
                        Neural Heuristic Analysis
                    </h4>
                    
                    {{-- 🔥 BEAST MODE: Typewriter Element --}}
                    <p class="text-sm font-black text-slate-900 leading-relaxed relative z-10 mb-6 tracking-tight min-h-[45px]">
                        <span x-text="typedAiMessage"></span><span class="animate-pulse border-r-2 border-slate-900 ml-0.5"></span>
                    </p>
                    
                    {{-- Actionable Pills --}}
                    <div class="space-y-2.5 relative z-10 mb-6">
                        @foreach($aiTips as $tip)
                            <div @mouseenter="playHoverSound()" class="p-3 bg-white/60 backdrop-blur-sm border {{ $activeTheme['border'] }} opacity-50 rounded-xl text-xs font-bold text-slate-700 flex items-center gap-3 shadow-sm hover:bg-white hover:opacity-100 hover:shadow-md transition-all cursor-pointer">
                                <div class="w-5 h-5 rounded-md {{ $activeTheme['bg'] }} flex items-center justify-center shrink-0"><i class="fa-solid fa-arrow-right text-[8px] {{ $activeTheme['text'] }}"></i></div> 
                                <span>{{ $tip }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Interactive Runway Slider Scenario --}}
                    <div class="border-t {{ $activeTheme['border'] }} opacity-50 pt-5 relative z-10">
                        <p class="text-[10px] font-black uppercase tracking-widest {{ $activeTheme['text'] }} mb-3 flex items-center justify-between">
                            <span>Projected Runway</span>
                            <span class="font-mono text-sm bg-white px-2 py-0.5 rounded-lg shadow-sm border {{ $activeTheme['border'] }}"><span x-text="simulatedRunway">{{ $runway }}</span> Mo</span>
                        </p>
                        <input type="range" x-model="burnRateModifier" min="0.5" max="2" step="0.1" @input="playClickSound()" class="w-full h-1.5 bg-white border {{ $activeTheme['border'] }} rounded-full appearance-none cursor-pointer outline-none shadow-inner" style="accent-color: {{ $sysColor === 'emerald' ? '#10b981' : ($sysColor === 'rose' ? '#f43f5e' : ($sysColor === 'amber' ? '#f59e0b' : '#4f46e5')) }};">
                        <div class="flex justify-between mt-2 text-[8px] font-bold text-slate-400 uppercase tracking-widest">
                            <span>-50% Burn</span>
                            <span>Current</span>
                            <span>+100% Burn</span>
                        </div>
                    </div>
                </div>

                {{-- Doughnut Chart (Compact) --}}
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm flex flex-col items-center justify-center relative flex-1 hover:shadow-md transition-shadow duration-500">
                    <h3 class="text-lg font-black text-slate-900 tracking-tight mb-1 w-full text-left">Capital Allocation</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest w-full text-left mb-6">Expense Vector Distribution</p>
                    
                    <div class="relative w-full max-w-[200px] aspect-square">
                        <canvas id="categoryChart"></canvas>
                        @if(empty($catLabels))
                            <div class="absolute inset-0 flex items-center justify-center text-slate-400 font-bold text-xs bg-slate-50/90 backdrop-blur-sm z-10 rounded-[1.5rem] border-2 border-slate-200 border-dashed text-center px-4">
                                Categorization Data Unavailable
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= 4. THE MASTER LEDGER (ALPINE FILTER ENGINE) ================= --}}
        <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-[0_8px_30px_rgba(0,0,0,0.03)] overflow-hidden flex flex-col relative group hover:shadow-[0_20px_50px_-10px_rgba(0,0,0,0.08)] transition-all duration-500">
            
            {{-- Ledger Header & Toolbar --}}
            <div class="p-6 md:p-8 border-b border-slate-100 bg-slate-50/80 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6 z-20">
                <div>
                    <h3 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-3 mb-1">
                        <div class="w-10 h-10 rounded-[12px] bg-indigo-100 text-indigo-600 flex items-center justify-center border border-indigo-200 shadow-sm"><i class="fa-solid fa-book-journal-whills text-sm"></i></div>
                        Cryptographic Ledger
                    </h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Unified Chronological Telemetry</p>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-4 w-full xl:w-auto">
                    {{-- Live Alpine Search --}}
                    <div class="relative w-full sm:w-64 group/search">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within/search:text-indigo-500 transition-colors"></i>
                        <input type="text" x-model="searchQuery" placeholder="Search transactions..." class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 outline-none transition-all shadow-sm placeholder-slate-400">
                    </div>

                    {{-- Interactive Tabs --}}
                    <div class="flex bg-white p-1 rounded-xl shadow-sm border border-slate-200 w-full sm:w-auto overflow-x-auto shrink-0 relative" x-ref="tabContainer">
                        <div class="absolute h-[calc(100%-8px)] top-1 bg-slate-100 rounded-lg border border-slate-200 transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]" 
                             :style="`width: ${tabWidth}px; transform: translateX(${tabOffset}px);`"></div>

                        <button x-ref="tabAll" @click="setTab('all', $refs.tabAll)" @mouseenter="playHoverSound()" :class="filterType === 'all' ? 'text-slate-900 shadow-sm bg-white' : 'text-slate-500 hover:text-slate-900'" class="relative z-10 flex-1 sm:flex-none px-5 py-2.5 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all focus:outline-none whitespace-nowrap border border-transparent">All</button>
                        <button x-ref="tabIn" @click="setTab('income', $refs.tabIn)" @mouseenter="playHoverSound()" :class="filterType === 'income' ? 'text-emerald-700 border-emerald-200 shadow-sm bg-emerald-50' : 'text-slate-500 hover:text-slate-900 border-transparent'" class="relative z-10 flex-1 sm:flex-none px-5 py-2.5 text-[10px] font-black uppercase tracking-widest rounded-lg border transition-all focus:outline-none whitespace-nowrap">Inflow</button>
                        <button x-ref="tabOut" @click="setTab('expense', $refs.tabOut)" @mouseenter="playHoverSound()" :class="filterType === 'expense' ? 'text-rose-700 border-rose-200 shadow-sm bg-rose-50' : 'text-slate-500 hover:text-slate-900 border-transparent'" class="relative z-10 flex-1 sm:flex-none px-5 py-2.5 text-[10px] font-black uppercase tracking-widest rounded-lg border transition-all focus:outline-none whitespace-nowrap">Outflow</button>
                    </div>
                </div>
            </div>

            {{-- The Data Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead class="bg-white border-b border-slate-100">
                        <tr>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Transaction Hub</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Classification</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Timestamp</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Magnitude</th>
                            <th class="px-8 py-5"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($unifiedLedger as $tx)
                            @php
                                $isIncome = $tx->type === 'income';
                                $txColor = $isIncome ? 'emerald' : 'rose';
                                $txIcon  = $isIncome ? 'fa-arrow-trend-up' : 'fa-tag';
                                $catText = strtolower($tx->title);
                                
                                if (!$isIncome) {
                                    if(str_contains($catText, 'food') || str_contains($catText, 'dining')) { $txColor = 'amber'; $txIcon = 'fa-utensils'; }
                                    elseif(str_contains($catText, 'tech') || str_contains($catText, 'software')) { $txColor = 'indigo'; $txIcon = 'fa-laptop'; }
                                    elseif(str_contains($catText, 'health') || str_contains($catText, 'medical')) { $txColor = 'rose'; $txIcon = 'fa-heart-pulse'; }
                                    elseif(str_contains($catText, 'bill') || str_contains($catText, 'utility')) { $txColor = 'sky'; $txIcon = 'fa-bolt'; }
                                }
                                $colorVars = $colorMap[$txColor];
                                
                                // Trace Hash
                                $salt = ($tx->id ?? 'sys') . '-' . ($tx->date ?? rand());
                                $traceHash = strtoupper(substr(md5($salt), 0, 8));
                            @endphp

                            <tr x-show="matchesFilters('{{ addslashes($tx->title) }}', '{{ $tx->type }}')"
                                x-transition.opacity.duration.300ms
                                class="border-b border-slate-50 hover:bg-slate-50/80 transition-colors last:border-0 group/row">
                                
                                <td class="px-8 py-5 w-[40%]">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-[12px] flex items-center justify-center shrink-0 shadow-sm border {{ $colorVars['bg'] }} {{ $colorVars['text'] }} {{ $colorVars['border'] }} group-hover/row:scale-110 transition-transform duration-300">
                                            <i class="fa-solid {{ $txIcon }} text-sm"></i>
                                        </div>
                                        <div>
                                            <span class="block text-sm font-black text-slate-900">{{ $tx->title }}</span>
                                            <button @click="copyTrace('EXP-{{ $traceHash }}')" @mouseenter="playHoverSound()" class="text-[9px] font-bold text-slate-400 uppercase tracking-widest font-mono mt-1 flex items-center gap-1.5 hover:text-indigo-600 transition-colors focus:outline-none" title="Copy Cryptographic Trace ID">
                                                <i class="fa-regular fa-copy"></i> EXP-{{ $traceHash }}
                                            </button>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-8 py-5">
                                    <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border shadow-sm {{ $isIncome ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-rose-50 text-rose-600 border-rose-200' }}">
                                        {{ $isIncome ? 'INFLOW' : 'OUTFLOW' }}
                                    </span>
                                </td>

                                <td class="px-8 py-5 text-[11px] font-bold text-slate-500 uppercase tracking-widest font-mono">
                                    {{ \Carbon\Carbon::parse($tx->date)->format('d M Y, H:i') }}
                                </td>

                                <td class="px-8 py-5 text-right">
                                    <span class="text-lg font-black block {{ $isIncome ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $isIncome ? '+' : '-' }} ₹{{ number_format($tx->amount) }}
                                    </span>
                                </td>
                                
                                <td class="px-8 py-5 text-right w-32">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover/row:opacity-100 transition-opacity">
                                        <a href="{{ $tx->edit_route }}" @click.prevent="simulateNavigation('{{ $tx->edit_route }}')" @mouseenter="playHoverSound()" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 flex items-center justify-center transition-all shadow-sm focus:outline-none hover:-translate-y-0.5">
                                            <i class="fa-solid fa-pen text-[10px]"></i>
                                        </a>
                                        <form action="{{ $tx->delete_route }}" method="POST" class="m-0" onsubmit="return confirm('Void this cryptographic record? This cannot be undone.');">
                                            @csrf @method('DELETE')
                                            <button type="submit" @mouseenter="playHoverSound()" class="w-8 h-8 rounded-lg bg-white border border-rose-100 text-rose-400 hover:text-white hover:bg-rose-600 hover:border-rose-600 flex items-center justify-center transition-all shadow-sm focus:outline-none hover:-translate-y-0.5">
                                                <i class="fa-solid fa-trash text-[10px]"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- Empty State Handled by Alpine below --}}
                        @endforelse
                    </tbody>
                </table>
                
                {{-- Alpine JS Empty State --}}
                <div x-show="visibleRows === 0" style="display: none;" class="py-24 text-center flex flex-col items-center justify-center">
                    <div class="w-20 h-20 bg-slate-50 border border-slate-100 rounded-[1.5rem] flex items-center justify-center mb-6 shadow-inner rotate-12 hover:rotate-0 transition-transform duration-500">
                        <i class="fa-solid fa-ghost text-4xl text-slate-300"></i>
                    </div>
                    <h4 class="text-slate-900 font-black text-2xl tracking-tight mb-2">No Records Found</h4>
                    <p class="text-slate-500 font-medium text-sm max-w-sm mx-auto">Your cryptographic search parameters yielded zero results.</p>
                </div>
            </div>
            
            <div class="p-4 bg-slate-50/80 border-t border-slate-100 flex justify-between items-center z-10">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Entries: {{ count($unifiedLedger) }}</span>
                <button @mouseenter="playHoverSound()" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 hover:text-indigo-600 hover:border-indigo-300 hover:shadow-sm rounded-xl text-[10px] font-black uppercase tracking-widest transition-all focus:outline-none">Load More Telemetry &rarr;</button>
            </div>
        </div>

    </div>

    {{-- Universal Toast --}}
    <div class="fixed bottom-10 left-1/2 transform -translate-x-1/2 z-[10000]" 
         x-show="toast.show" x-cloak
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-10 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-10 scale-95"
         style="display: none;">
        <div class="bg-slate-900/95 backdrop-blur-xl text-white px-5 py-3 rounded-xl shadow-[0_20px_40px_-15px_rgba(0,0,0,0.5)] flex items-center gap-3 border border-slate-700 max-w-sm w-max">
            <i class="fa-solid fa-circle-check text-emerald-400"></i>
            <span class="text-xs font-bold tracking-wide text-slate-100" x-text="toast.message"></span>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .perspective-\[1500px\] { perspective: 1500px; }
    .transform-style-3d { transform-style: preserve-3d; }
    .translate-z-20 { transform: translateZ(20px); }
    .translate-z-30 { transform: translateZ(30px); }
    
    /* Center Text Plugin for Chart.js */
    .chart-center-text {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        text-align: center; pointer-events: none;
    }
</style>
@endpush

@push('scripts')
{{-- Ensure Chart.js is loaded --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('userDashboardEngine', () => ({
        // Toast State
        showSuccess: true,
        toast: { show: false, message: '' },
        
        // Header States
        clientIp: 'Scanning...',
        greeting: 'Welcome',
        isSyncing: false,

        // SPA Navigation State
        isNavigating: false,
        navProgress: 0,

        // Ledger Filtering State
        filterType: 'all', 
        searchQuery: '',
        visibleRows: {{ count($unifiedLedger) }},
        
        // Scenario Modeler State
        baseRunway: {{ $runway }},
        burnRateModifier: 1.0,

        // AI Typewriter
        fullAiMessage: "{{ addslashes($aiMessage) }}",
        typedAiMessage: "",

        // Tabs
        tabWidth: 0,
        tabOffset: 0,
        resizeObserver: null,

        // Native Web Audio Synthesizer
        audioCtx: null,

        init() {
            this.setGreeting();
            this.fetchSimulatedIp();
            
            // Tab resizing observer
            this.resizeObserver = new ResizeObserver(() => {
                this.$nextTick(() => {
                    const activeEl = this.$refs['tab' + (this.filterType === 'all' ? 'All' : (this.filterType === 'income' ? 'In' : 'Out'))];
                    if(activeEl) {
                        this.tabWidth = activeEl.offsetWidth;
                        this.tabOffset = activeEl.offsetLeft;
                    }
                });
            });
            if(this.$refs.tabContainer) this.resizeObserver.observe(this.$refs.tabContainer);

            this.$nextTick(() => {
                if(this.$refs.tabAll) this.setTab('all', this.$refs.tabAll, false);
            });
            
            // Wait for DOM to paint
            setTimeout(() => {
                this.animateNumbers();
                this.initCharts();
            }, 100);

            // Start AI Typewriter
            setTimeout(() => { this.typeWriterEffect(); }, 400);
        },

        destroy() {
            if(this.resizeObserver) this.resizeObserver.disconnect();
        },

        // Web Audio API Engine
        initAudio() {
            if(!this.audioCtx) {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if(AudioContext) this.audioCtx = new AudioContext();
            }
        },
        playClickSound() {
            this.initAudio();
            if(!this.audioCtx) return;
            if(this.audioCtx.state === 'suspended') this.audioCtx.resume();
            const osc = this.audioCtx.createOscillator();
            const gain = this.audioCtx.createGain();
            osc.connect(gain); gain.connect(this.audioCtx.destination);
            osc.type = 'sine';
            osc.frequency.setValueAtTime(800, this.audioCtx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(300, this.audioCtx.currentTime + 0.05);
            gain.gain.setValueAtTime(0.1, this.audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, this.audioCtx.currentTime + 0.05);
            osc.start(); osc.stop(this.audioCtx.currentTime + 0.05);
        },
        playHoverSound() {
            this.initAudio();
            if(!this.audioCtx) return;
            if(this.audioCtx.state === 'suspended') this.audioCtx.resume();
            const osc = this.audioCtx.createOscillator();
            const gain = this.audioCtx.createGain();
            osc.connect(gain); gain.connect(this.audioCtx.destination);
            osc.type = 'sine';
            osc.frequency.setValueAtTime(400, this.audioCtx.currentTime);
            gain.gain.setValueAtTime(0.02, this.audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, this.audioCtx.currentTime + 0.03);
            osc.start(); osc.stop(this.audioCtx.currentTime + 0.03);
        },

        // AI Typewriter Effect
        typeWriterEffect() {
            let i = 0;
            let msg = this.fullAiMessage;
            let int = setInterval(() => {
                this.typedAiMessage += msg.charAt(i);
                i++;
                if (i >= msg.length) clearInterval(int);
            }, 25);
        },

        // SPA Navigation Simulation
        simulateNavigation(url) {
            if(url === '#') return;
            this.playClickSound();
            this.isNavigating = true;
            this.navProgress = 10;
            
            let interval = setInterval(() => {
                this.navProgress += Math.random() * 20;
                if(this.navProgress >= 90) {
                    clearInterval(interval);
                    window.location.href = url;
                }
            }, 100);
        },

        async copyTrace(text) {
            this.playClickSound();
            try {
                await navigator.clipboard.writeText(text);
                this.showToast('Trace ID copied to clipboard.');
            } catch (err) {
                this.showToast('Failed to copy ID.');
            }
        },

        showToast(msg) {
            this.toast.message = msg;
            this.toast.show = true;
            setTimeout(() => { this.toast.show = false; }, 3000);
        },

        // Computes dynamic runway based on user slider input
        get simulatedRunway() {
            if (this.burnRateModifier == 1.0) return this.baseRunway;
            let val = Math.round(this.baseRunway / this.burnRateModifier);
            return val > 0 ? val : 0;
        },

        setTab(val, el, playSound = true) {
            this.filterType = val;
            if (el) {
                this.tabWidth = el.offsetWidth;
                this.tabOffset = el.offsetLeft;
            }
            if(playSound) this.playClickSound();
        },

        // Client-side Ledger Search Engine
        matchesFilters(title, type) {
            let typeMatch = (this.filterType === 'all' || this.filterType === type);
            let searchMatch = this.searchQuery === '' || title.toLowerCase().includes(this.searchQuery.toLowerCase());
            let isVisible = typeMatch && searchMatch;
            
            // Debounce the count update slightly to let Alpine render
            setTimeout(() => {
                const rows = document.querySelectorAll('tbody tr[style*="display: none"]');
                this.visibleRows = {{ count($unifiedLedger) }} - rows.length;
            }, 10);

            return isVisible;
        },

        setGreeting() {
            const hour = new Date().getHours();
            if (hour >= 5 && hour < 12) this.greeting = "Good Morning";
            else if (hour >= 12 && hour < 17) this.greeting = "Good Afternoon";
            else this.greeting = "Good Evening";
        },

        fetchSimulatedIp() {
            setTimeout(() => { this.clientIp = '192.168.' + Math.floor(Math.random() * 255) + '.42'; }, 1500);
        },

        syncData() {
            if(this.isSyncing) return;
            this.playClickSound();
            this.isSyncing = true;
            
            setTimeout(() => {
                this.isSyncing = false;
                this.showToast('Local telemetry synced successfully.');
            }, 2000);
        },

        // High-Performance Number Animation (requestAnimationFrame)
        animateNumbers() {
            document.querySelectorAll('.kpi-number').forEach(el => {
                let target = parseFloat(el.dataset.val || 0);
                if (target === 0) return;
                
                let duration = 2500;
                let startTime = null;

                const step = (timestamp) => {
                    if (!startTime) startTime = timestamp;
                    let progress = Math.min((timestamp - startTime) / duration, 1);
                    // Ease Out Expo
                    let eased = progress === 1 ? target : target * (1 - Math.pow(2, -10 * progress)); 
                    
                    el.innerText = Math.round(eased).toLocaleString('en-IN');
                    
                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    } else {
                        el.innerText = target.toLocaleString('en-IN'); // ensure exact end
                    }
                }
                window.requestAnimationFrame(step);
            });
        },

        handleTilt(e, el) {
            const rect = el.getBoundingClientRect();
            const x = e.clientX - rect.left; const y = e.clientY - rect.top;
            const centerX = rect.width / 2; const centerY = rect.height / 2;
            const rotateX = ((y - centerY) / centerY) * -4;
            const rotateY = ((x - centerX) / centerX) * 4;
            el.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
        },
        resetTilt(el) { el.style.transform = `rotateX(0deg) rotateY(0deg)`; },

        // Chart.js Factory
        initCharts() {
            if(typeof Chart === 'undefined') return;

            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = '#94a3b8';
            
            const formatCurrency = (num) => {
                if(num >= 10000000) return '₹' + (num / 10000000).toFixed(1) + 'Cr';
                if(num >= 100000) return '₹' + (num / 100000).toFixed(1) + 'L';
                if(num >= 1000) return '₹' + (num / 1000).toFixed(1) + 'k';
                return '₹' + num;
            };

            const labels = @json($labels);
            const incData = @json($incomeData);
            const expData = @json($expenseData);
            const catLabels = @json($catLabels);
            const catData = @json($catData);

            const tooltipConfig = {
                backgroundColor: '#0f172a', titleColor: '#fff', bodyColor: '#cbd5e1',
                padding: 16, cornerRadius: 12, displayColors: true, boxPadding: 6,
                titleFont: { size: 14 }, bodyFont: { weight: 'bold', size: 14 },
                callbacks: { label: (ctx) => ' ₹' + ctx.parsed.y.toLocaleString('en-IN') }
            };

            // A. Main Trajectory Line Chart
            const finCanvas = document.getElementById('financeChart');
            if(finCanvas && labels.length > 0) {
                const tCtx = finCanvas.getContext('2d');
                
                // Deep Gradients
                const incGrad = tCtx.createLinearGradient(0, 0, 0, 400);
                incGrad.addColorStop(0, 'rgba(16, 185, 129, 0.4)'); incGrad.addColorStop(1, 'rgba(16, 185, 129, 0)');
                const expGrad = tCtx.createLinearGradient(0, 0, 0, 400);
                expGrad.addColorStop(0, 'rgba(244, 63, 94, 0.4)'); expGrad.addColorStop(1, 'rgba(244, 63, 94, 0)');

                new Chart(tCtx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            { label: 'Capital Inflow', data: incData, borderColor: '#10b981', backgroundColor: incGrad, borderWidth: 3, fill: true, tension: 0.4, pointRadius: 0, pointHoverRadius: 8, pointBackgroundColor: '#fff', pointBorderColor: '#10b981', pointBorderWidth: 3 },
                            { label: 'Capital Burn', data: expData, borderColor: '#f43f5e', backgroundColor: expGrad, borderWidth: 3, fill: true, tension: 0.4, pointRadius: 0, pointHoverRadius: 8, pointBackgroundColor: '#fff', pointBorderColor: '#f43f5e', pointBorderWidth: 3 }
                        ]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: { legend: { display: false }, tooltip: tooltipConfig },
                        scales: { 
                            y: { beginAtZero: true, grid: { color: '#f1f5f9', drawBorder: false }, ticks: { callback: function(value) { return formatCurrency(value); } } },
                            x: { grid: { display: false, drawBorder: false }, ticks: { font: { weight: 'bold' } } }
                        }
                    }
                });
            }

            // B. Category Doughnut Chart with Center Text Plugin
            const allocCanvas = document.getElementById('categoryChart');
            if(allocCanvas && catLabels.length > 0) {
                
                const centerTextPlugin = {
                    id: 'centerText',
                    beforeDraw: (chart) => {
                        const width = chart.width, height = chart.height, ctx = chart.ctx;
                        ctx.clearRect(0, 0, width, height); 
                        ctx.restore();
                        
                        const fontSize = (height / 114).toFixed(2);
                        ctx.font = "900 " + fontSize + "em Inter";
                        ctx.textBaseline = "middle";
                        ctx.fillStyle = "#0f172a"; 
                        const text = "₹" + (this.totalExpenseVal >= 1000 ? (this.totalExpenseVal/1000).toFixed(1)+'k' : this.totalExpenseVal),
                              textX = Math.round((width - ctx.measureText(text).width) / 2),
                              textY = height / 2;
                        ctx.fillText(text, textX, textY + 5);
                        
                        ctx.font = "bold " + (fontSize * 0.3) + "em Inter";
                        ctx.fillStyle = "#64748b"; 
                        const subText = "TOTAL BURN",
                              subTextX = Math.round((width - ctx.measureText(subText).width) / 2);
                        ctx.fillText(subText, subTextX, textY - 15);
                        ctx.save();
                    }
                };

                new Chart(allocCanvas.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: catLabels,
                        datasets: [{
                            data: catData,
                            backgroundColor: ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6'],
                            borderWidth: 2, borderColor: '#ffffff', hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, cutout: '76%',
                        plugins: { legend: { display: false }, tooltip: tooltipConfig }
                    },
                    plugins: [centerTextPlugin]
                });
            }
        }
    }));
});
</script>
@endpush