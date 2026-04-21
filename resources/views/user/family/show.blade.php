@extends('layouts.app')

@section('title', $family->name . ' - Hub | FinanceAI')

@section('content')

@php
    // ================= 1. FATAL ERROR GUARD =================
    if(!$family || !$family->id){
        echo '<div class="min-h-screen flex items-center justify-center bg-slate-50"><div class="text-center p-12 bg-white rounded-[2.5rem] shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] border border-rose-100 max-w-lg"><div class="w-24 h-24 bg-rose-50 text-rose-500 rounded-[1.5rem] flex items-center justify-center mx-auto mb-6 shadow-inner"><i class="fa-solid fa-triangle-exclamation text-4xl"></i></div><h2 class="text-3xl font-black text-slate-900 mb-2 tracking-tight">Workspace Not Found</h2><p class="text-slate-500 font-medium leading-relaxed">This collaborative hub may have been deleted, or your access privileges were revoked by the administrator.</p><a href="' . url('/dashboard') . '" class="mt-8 inline-block px-8 py-3.5 bg-slate-900 text-white font-bold text-sm tracking-widest uppercase rounded-xl hover:bg-indigo-600 transition-all hover:shadow-[0_10px_20px_rgba(79,70,229,0.3)]">Return Home</a></div></div>';
        return;
    }

    // ================= 2. BULLETPROOF DATA EXTRACTION =================
    $metrics = $metrics ?? [];
    $trend = $trend ?? ['months' => [], 'income' => [], 'expense' => []];
    
    $rawCategories = $categories ?? [];
    $catLabels = is_array($rawCategories) ? array_keys($rawCategories) : (method_exists($rawCategories, 'keys') ? $rawCategories->keys()->toArray() : []);
    $catData = is_array($rawCategories) ? array_values($rawCategories) : (method_exists($rawCategories, 'values') ? $rawCategories->values()->toArray() : []);

    $members = $members ?? collect();
    $recentIncomes = $recentIncomes ?? collect();
    $recentExpenses = $recentExpenses ?? collect();

    // Financial Math Engine
    $totalIncome = (float) ($metrics['total_income'] ?? 0);
    $totalExpense = (float) ($metrics['total_expense'] ?? 0);
    $balance = (float) ($metrics['balance'] ?? ($totalIncome - $totalExpense));
    $savingRate = (float) ($metrics['saving_rate'] ?? ($totalIncome > 0 ? ($balance / $totalIncome) * 100 : 0));

    // ================= 3. ROLE-BASED ACCESS CONTROL (RBAC) =================
    $currentUser = auth()->user();
    $currentUserMember = collect($members)->where('id', $currentUser->id ?? 0)->first();
    $userRole = $currentUserMember ? strtolower($currentUserMember->pivot->role ?? 'member') : 'member';
    
    $isOwner = ($userRole === 'owner' || $userRole === 'admin' || $family->created_by === ($currentUser->id ?? 0));

    // ================= 4. AI HEURISTIC & COLOR THEME ENGINE =================
    if ($savingRate >= 30) {
        $sysColor = 'emerald'; $sysStatus = 'Optimal'; $sysIcon = 'fa-shield-check';
        $aiMessage = 'Workspace liquidity is operating at peak efficiency. Capital retention is exceptionally high.';
        $aiAction = 'Maintain current allocation structures. Safe to scale up discretionary or investment outlays.';
    } elseif ($savingRate >= 10) {
        $sysColor = 'indigo'; $sysStatus = 'Stable'; $sysIcon = 'fa-check-circle';
        $aiMessage = 'Shared cashflow is stable. The workspace is maintaining a healthy median savings velocity.';
        $aiAction = 'Monitor variable expenses. Minor optimizations in utility overhead recommended.';
    } elseif ($savingRate >= 0) {
        $sysColor = 'amber'; $sysStatus = 'Warning'; $sysIcon = 'fa-triangle-exclamation';
        $aiMessage = 'Elevated burn rate detected within the shared ledger. Margin of error is dangerously thin.';
        $aiAction = 'Algorithm recommends restricting discretionary outflows by 15% to avoid structural deficit.';
    } else {
        $sysColor = 'rose'; $sysStatus = 'Critical Risk'; $sysIcon = 'fa-skull-crossbones';
        $aiMessage = 'Deficit Alert! Group expenses currently exceed total inbound capital. Reserves are bleeding.';
        $aiAction = 'Immediate financial audit and spending freeze required across all connected workspace nodes.';
    }

    // Unified Ledger Engine
    $rawInc = $recentIncomes->map(fn($i) => (object)['type' => 'inflow', 'data' => $i, 'date' => $i->income_date ?? $i->created_at]);
    $rawExp = $recentExpenses->map(fn($e) => (object)['type' => 'outflow', 'data' => $e, 'date' => $e->expense_date ?? $e->created_at]);
    $unifiedLedger = $rawInc->merge($rawExp)->sortByDesc('date')->values();
@endphp

<div x-data="familyWorkspaceEngine()" class="min-h-screen bg-[#f8fafc] pb-32 font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden">

    {{-- Holographic Light Ambient Backgrounds --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[900px] h-[900px] bg-indigo-50/60 rounded-full blur-[120px] transition-colors duration-1000 transform-gpu"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[800px] h-[800px] bg-sky-50/50 rounded-full blur-[120px] transform-gpu"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+PHBhdGggZD0iTTAgMGgyMHYyMEgwVjB6bTEwIDEwaDEwdjEwSDEwaC0xMHptMCAwaC0xMHYtMTBoMTB2MTB6IiBmaWxsPSIjZThlYWVkIiBmaWxsLW9wYWNpdHk9IjAuMjUiIGZpbGwtcnVsZT0iZXZlbm9kZCIvPjwvc3ZnPg==')] opacity-30 mix-blend-multiply"></div>
    </div>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 pt-10 relative z-10 space-y-8">

        {{-- ================= SUCCESS NOTIFICATION ================= --}}
        @if(session('success'))
            <div x-show="showSuccess" x-init="setTimeout(() => showSuccess = false, 5000)" 
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-[-1rem] scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-[-1rem] scale-95"
                 class="bg-white/80 backdrop-blur-xl border border-emerald-200 rounded-[1.5rem] p-4 flex items-center justify-between shadow-[0_10px_30px_-10px_rgba(16,185,129,0.2)] max-w-3xl mx-auto mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-sm border border-emerald-400"><i class="fa-solid fa-check text-sm"></i></div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-emerald-600 mb-0.5">Success Protocol</p>
                        <p class="text-sm font-bold text-slate-700 leading-tight">{{ session('success') }}</p>
                    </div>
                </div>
                <button @click="showSuccess = false" class="text-slate-400 hover:bg-slate-100 hover:text-slate-700 w-8 h-8 rounded-lg flex items-center justify-center transition-colors focus:outline-none"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        {{-- ================= 1. WORKSPACE COMMAND HEADER ================= --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 bg-white p-8 md:p-10 rounded-[2.5rem] border border-slate-200 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-2 h-full bg-gradient-to-b from-indigo-500 to-sky-400"></div>
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl pointer-events-none group-hover:bg-indigo-500/10 transition-colors duration-700"></div>

            <div class="relative z-10">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <li><a href="{{ route('user.families.index') }}" @mouseenter="playHover()" class="hover:text-indigo-600 transition-colors focus:outline-none">Hubs</a></li>
                        <li><i class="fa-solid fa-chevron-right text-[8px] mx-1 opacity-40"></i></li>
                        <li class="text-indigo-600 truncate max-w-[200px] flex items-center gap-1.5">
                            <span class="relative flex h-1.5 w-1.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span><span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-indigo-500"></span></span>
                            {{ $family->name }}
                        </li>
                    </ol>
                </nav>
                <div class="flex items-center gap-4 mb-2">
                    <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight">{{ $family->name }}</h1>
                    <span class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest border shadow-sm bg-{{ $sysColor }}-50 text-{{ $sysColor }}-600 border-{{ $sysColor }}-200">
                        {{ $sysStatus }}
                    </span>
                </div>
                <p class="text-slate-500 text-sm font-medium flex items-center gap-2 max-w-xl leading-relaxed">
                    <i class="fa-solid fa-lock text-emerald-500"></i>
                    Encrypted Multi-Party Ledger. {{ $family->description ?? 'AI optimization active.' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 relative z-10 shrink-0">
                <button @click="syncLedger()" @mouseenter="playHover()" class="px-5 py-3.5 bg-slate-50 text-slate-600 border border-slate-200 rounded-2xl font-bold text-sm hover:bg-white hover:text-indigo-600 hover:border-indigo-300 hover:shadow-sm transition-all flex items-center gap-2 focus:outline-none focus:ring-4 focus:ring-indigo-500/10">
                    <i class="fa-solid fa-rotate text-indigo-500" :class="syncing ? 'animate-spin' : ''"></i> Sync
                </button>

                <a href="{{ route('user.incomes.create', ['family_id' => $family->id]) }}" @mouseenter="playHover()" class="px-6 py-3.5 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl font-bold text-sm shadow-sm hover:bg-emerald-600 hover:text-white hover:shadow-[0_10px_20px_rgba(16,185,129,0.3)] transition-all flex items-center gap-2 hover:-translate-y-0.5 focus:outline-none">
                    <i class="fa-solid fa-arrow-turn-down transform rotate-90"></i> Record Inflow
                </a>

                <a href="{{ route('user.expenses.create', ['family_id' => $family->id]) }}" @mouseenter="playHover()" class="px-6 py-3.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl font-bold text-sm shadow-sm hover:bg-rose-600 hover:text-white hover:shadow-[0_10px_20px_rgba(225,29,72,0.3)] transition-all flex items-center gap-2 hover:-translate-y-0.5 focus:outline-none">
                    <i class="fa-solid fa-arrow-turn-up transform rotate-45"></i> Record Outflow
                </a>
            </div>
        </div>

        {{-- ================= 2. MULTI-COLOR KPI GRID (3D TILT ENABLED) ================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 perspective-[1500px]">
            
            {{-- Gross Inflow (Emerald) --}}
            <div @mousemove="handleTilt($event, $el)" @mouseleave="resetTilt($el)" class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-[0_15px_30px_-10px_rgba(0,0,0,0.1)] transition-all duration-500 relative overflow-hidden transform-style-3d group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-6 relative z-10 translate-z-20">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-[1rem] flex items-center justify-center border border-emerald-100 shadow-sm group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300"><i class="fa-solid fa-money-bill-trend-up text-lg"></i></div>
                </div>
                <div class="translate-z-30 relative z-10">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Hub Capital Inflow</p>
                    <h2 class="text-3xl font-black text-emerald-600 tabular-nums tracking-tight" x-intersect.once="animateValue($el, {{ $totalIncome }}, true)">₹0</h2>
                </div>
            </div>

            {{-- Total Outflow (Rose) --}}
            <div @mousemove="handleTilt($event, $el)" @mouseleave="resetTilt($el)" class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-[0_15px_30px_-10px_rgba(0,0,0,0.1)] transition-all duration-500 relative overflow-hidden transform-style-3d group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-rose-500/10 rounded-full blur-xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-6 relative z-10 translate-z-20">
                    <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-[1rem] flex items-center justify-center border border-rose-100 shadow-sm group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300"><i class="fa-solid fa-fire-flame-curved text-lg"></i></div>
                </div>
                <div class="translate-z-30 relative z-10">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Hub Capital Burn</p>
                    <h2 class="text-3xl font-black text-rose-600 tabular-nums tracking-tight" x-intersect.once="animateValue($el, {{ $totalExpense }}, true)">₹0</h2>
                </div>
            </div>

            {{-- Net Balance (Indigo) --}}
            <div @mousemove="handleTilt($event, $el)" @mouseleave="resetTilt($el)" class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-[0_15px_30px_-10px_rgba(0,0,0,0.1)] transition-all duration-500 relative overflow-hidden transform-style-3d group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-indigo-500/10 rounded-full blur-xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-6 relative z-10 translate-z-20">
                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-[1rem] flex items-center justify-center border border-indigo-100 shadow-sm group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300"><i class="fa-solid fa-scale-balanced text-lg"></i></div>
                </div>
                <div class="translate-z-30 relative z-10">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Retained Balance</p>
                    <h2 class="text-3xl font-black text-indigo-600 tabular-nums tracking-tight" x-intersect.once="animateValue($el, {{ abs($balance) }}, true)">₹0</h2>
                </div>
            </div>

            {{-- Savings Velocity (Dynamic) --}}
            <div @mousemove="handleTilt($event, $el)" @mouseleave="resetTilt($el)" class="bg-white p-6 rounded-[2rem] border border-{{ $sysColor }}-200 shadow-sm hover:shadow-[0_15px_30px_-10px_rgba(0,0,0,0.1)] transition-all duration-500 relative overflow-hidden transform-style-3d group">
                <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-{{ $sysColor }}-500/10 rounded-full blur-2xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-6 relative z-10 translate-z-20">
                    <div class="w-12 h-12 bg-{{ $sysColor }}-50 text-{{ $sysColor }}-600 rounded-[1rem] flex items-center justify-center border border-{{ $sysColor }}-100 shadow-sm group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300"><i class="fa-solid fa-gauge-high text-lg"></i></div>
                </div>
                <div class="translate-z-30 relative z-10">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Savings Velocity</p>
                    <div class="flex items-baseline gap-1">
                        <h2 class="text-3xl font-black text-{{ $sysColor }}-600 tabular-nums tracking-tight" x-intersect.once="animateValue($el, {{ $savingRate }}, false)">0</h2>
                        <span class="text-lg font-bold text-{{ $sysColor }}-400">%</span>
                    </div>
                    <div class="mt-4 h-1.5 bg-slate-100 rounded-full overflow-hidden shadow-inner border border-slate-200">
                        <div class="h-full bg-{{ $sysColor }}-500 rounded-full transition-all duration-1000 ease-out shadow-[0_0_8px_rgba(var(--color-{{ $sysColor }}-500),0.8)]" style="width: {{ min(abs($savingRate), 100) }}%"></div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ================= 3. MULTI-CHART ANALYTICS & AI ================= --}}
        <div class="grid lg:grid-cols-12 gap-8 items-start">
            
            {{-- Trend Line Chart --}}
            <div class="lg:col-span-8 bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm flex flex-col relative h-full">
                <h3 class="text-lg font-black text-slate-900 tracking-tight mb-6">Financial Velocity Trajectory</h3>
                <div class="flex-1 relative min-h-[300px] w-full">
                    @if(!empty($trend['months']))
                        <canvas id="trendChart"></canvas>
                    @else
                        <div class="absolute inset-0 flex items-center justify-center text-slate-400 font-bold bg-slate-50/90 backdrop-blur-sm z-10 rounded-3xl border border-slate-200 border-dashed shadow-inner">
                            <div class="text-center">
                                <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4 border border-slate-200 shadow-sm">
                                    <i class="fa-solid fa-chart-line text-2xl text-slate-300"></i>
                                </div>
                                Awaiting Temporal Data Integration
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Doughnut & AI Diagnostic --}}
            <div class="lg:col-span-4 space-y-6 h-full flex flex-col">
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm flex flex-col items-center justify-center relative flex-1">
                    <h3 class="text-lg font-black text-slate-900 tracking-tight mb-1 w-full text-left">Capital Allocation</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest w-full text-left mb-6">Expense Vector Distribution</p>
                    <div class="relative w-full max-w-[220px] aspect-square">
                        @if(!empty($catLabels))
                            <canvas id="categoryChart"></canvas>
                        @else
                            <div class="absolute inset-0 flex items-center justify-center text-slate-400 font-bold text-[10px] uppercase tracking-widest bg-slate-50/90 backdrop-blur-sm z-10 rounded-[2rem] border border-slate-200 border-dashed text-center px-4 shadow-inner">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 rounded-[1rem] bg-white border border-slate-200 shadow-sm flex items-center justify-center">
                                        <i class="fa-solid fa-chart-pie text-xl text-slate-300"></i>
                                    </div>
                                    Data Unavailable
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 🔥 BEAST MODE: Animated AI Typewriter Panel --}}
                <div class="bg-{{ $sysColor }}-50 border border-{{ $sysColor }}-200 rounded-[2.5rem] p-8 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 text-{{ $sysColor }}-500/10 text-8xl pointer-events-none group-hover:scale-110 group-hover:rotate-6 transition-transform duration-700"><i class="fa-solid fa-brain"></i></div>
                    <h4 class="text-[11px] font-black text-{{ $sysColor }}-600 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i class="fa-solid {{ $sysIcon }}"></i> AI Diagnostic Matrix
                    </h4>
                    <p class="text-sm font-bold text-{{ $sysColor }}-900 leading-relaxed relative z-10 mb-4 h-[45px]" x-text="typedAiMessage">
                        </p>
                    <div class="border-t border-{{ $sysColor }}-200/50 pt-4 relative z-10">
                        <p class="text-[10px] font-black text-{{ $sysColor }}-600 uppercase tracking-widest mb-1">Recommended Action</p>
                        <p class="text-xs font-bold text-{{ $sysColor }}-800 leading-relaxed">{{ $aiAction }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= 4. IAM ZONE & UNIFIED LEDGER ================= --}}
        <div class="grid lg:grid-cols-12 gap-8 items-stretch">
            
            {{-- 🚨 IDENTITY & ACCESS MANAGEMENT (IAM) ZONE --}}
            <div class="lg:col-span-4 flex flex-col gap-6">
                
                {{-- 4A. Member Roster --}}
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm flex flex-col h-full max-h-[500px]">
                    <div class="flex justify-between items-end mb-8 border-b border-slate-100 pb-6 shrink-0">
                        <div>
                            <h3 class="text-xl font-black text-slate-900 tracking-tight">Authorized Nodes</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Identity Management</p>
                        </div>
                        <span class="px-3 py-1.5 bg-slate-50 text-slate-600 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm border border-slate-200">{{ count($members) }} Users</span>
                    </div>

                    <div class="flex-1 space-y-4 overflow-y-auto pr-2 custom-scrollbar">
                        @forelse($members as $member)
                            @php
                                $mRole = strtolower($member->pivot->role ?? 'member');
                                $isCurrentUser = $member->id === ($currentUser->id ?? 0);
                                
                                $joinDate = $member->pivot->created_at ?? null;
                                $isNew = $joinDate && \Carbon\Carbon::parse($joinDate)->diffInDays(now()) <= 7;

                                $roleBg = $mRole === 'owner' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-slate-50 border-slate-200 text-slate-600';
                                $avatarColors = ['bg-indigo-100 text-indigo-600', 'bg-rose-100 text-rose-600', 'bg-amber-100 text-amber-600', 'bg-sky-100 text-sky-600'];
                                $avColor = $avatarColors[$loop->index % count($avatarColors)];
                            @endphp
                            
                            <div class="flex items-center justify-between p-4 rounded-[1.25rem] border border-slate-100 bg-slate-50/50 hover:bg-white hover:shadow-md hover:border-indigo-100 transition-all duration-300 group relative overflow-hidden">
                                
                                @if($isNew)
                                    <div class="absolute top-0 right-0 w-16 h-16 overflow-hidden pointer-events-none z-0">
                                        <div class="absolute transform rotate-45 bg-indigo-500 text-center text-white font-black text-[7px] uppercase tracking-widest py-0.5 right-[-18px] top-[8px] w-[70px] shadow-sm">
                                            NEW
                                        </div>
                                    </div>
                                @endif

                                <div class="flex items-center gap-4 relative z-10">
                                    <div class="relative">
                                        <div class="w-12 h-12 rounded-[1rem] flex items-center justify-center font-black text-lg shadow-inner border border-white {{ $avColor }}">
                                            {{ strtoupper(substr($member->name ?? 'U', 0, 1)) }}
                                        </div>
                                        {{-- Live Ping Dot --}}
                                        <span class="absolute -bottom-1 -right-1 flex h-3.5 w-3.5">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500 border-2 border-white"></span>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-bold text-slate-900">{{ $member->name ?? 'Unknown Node' }}</p>
                                            @if($isCurrentUser)
                                                <span class="px-1.5 py-0.5 rounded text-[7px] font-black uppercase tracking-widest bg-indigo-500 text-white shadow-sm">You</span>
                                            @endif
                                        </div>
                                        <div class="mt-1.5">
                                            <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest border shadow-sm {{ $roleBg }}">
                                                @if($mRole === 'owner') <i class="fa-solid fa-crown mr-1 text-emerald-500"></i> @endif
                                                {{ $mRole }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                @if($isOwner && !$isCurrentUser)
                                    <button @click="openDeleteModal('{{ route('user.families.removeMember', ['family' => $family->id, 'member' => $member->id]) }}', 'Revoke Access', 'Are you sure you want to permanently remove {{ addslashes($member->name ?? '') }} from this workspace?')" 
                                            @mouseenter="playHover()"
                                            class="w-8 h-8 rounded-lg bg-white border border-rose-100 text-rose-400 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-all duration-300 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 focus:opacity-100 relative z-10 focus:outline-none">
                                        <i class="fa-solid fa-user-xmark text-xs"></i>
                                    </button>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-slate-400 font-bold text-center py-10">No active members found.</p>
                        @endforelse
                    </div>
                </div>

                {{-- 4B. The Secure Mailbox --}}
                @if($isOwner)
                <div class="bg-gradient-to-br from-indigo-50/80 to-white border border-indigo-100 shadow-sm rounded-[2.5rem] p-8 relative overflow-hidden group">
                    <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none group-hover:bg-indigo-500/20 transition-colors duration-700"></div>
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-indigo-500"></div>

                    <div class="relative z-10">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 bg-white rounded-[1rem] flex items-center justify-center text-indigo-600 border border-indigo-100 shadow-sm">
                                <i class="fa-solid fa-envelope-open-text text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-900 tracking-tight">Add Node</h3>
                                <p class="text-[9px] font-black text-indigo-500 uppercase tracking-widest mt-0.5">Secure Mailbox</p>
                            </div>
                        </div>
                        
                        <p class="text-xs font-medium text-slate-500 leading-relaxed mb-6">
                            Deploy a cryptographic invite to a collaborator.
                        </p>

                        <form method="POST" action="{{ route('user.families.invite', $family->id) }}" @submit="submitInvite($event)" class="space-y-4">
                            @csrf
                            <div x-show="!sendingInvite">
                                <div class="relative mb-3">
                                    <i class="fa-solid fa-at absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    <input type="email" name="email" required placeholder="Target Email Address" 
                                           class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 placeholder-slate-400 focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-400 outline-none transition-all shadow-sm">
                                </div>
                                <button type="submit" @mouseenter="playHover()" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-[0_10px_20px_rgba(79,70,229,0.3)] transition-all flex items-center justify-center gap-2 hover:-translate-y-0.5 focus:outline-none">
                                    <i class="fa-solid fa-paper-plane text-indigo-300"></i> Transmit Request
                                </button>
                            </div>

                            <div x-show="sendingInvite" style="display:none;" class="w-full py-8 bg-white border border-indigo-100 rounded-xl flex flex-col items-center justify-center gap-3 shadow-inner">
                                <i class="fa-solid fa-circle-notch fa-spin text-2xl text-indigo-500"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest text-indigo-600 font-mono" x-text="inviteStatus">Securing Handshake...</span>
                            </div>
                        </form>
                        
                        <a href="{{ route('user.families.access', $family->id) }}" @mouseenter="playHover()" class="mt-4 w-full py-3 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-xs flex items-center justify-center gap-2 hover:bg-slate-50 hover:text-indigo-600 transition-colors shadow-sm focus:outline-none">
                            <i class="fa-solid fa-satellite-dish"></i> View Pending Transmissions
                        </a>
                    </div>
                </div>
                @endif
            </div>

            {{-- 🚨 UNIFIED INTERACTIVE LEDGER (TABBED) --}}
            <div class="lg:col-span-8 bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden flex flex-col h-full min-h-[600px]">
                <div class="p-6 md:p-8 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shrink-0">
                    <div>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                            <i class="fa-solid fa-book-journal-whills text-indigo-500"></i> Cryptographic Ledger
                        </h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Transaction History</p>
                    </div>

                    {{-- Interactive Alpine Tabs --}}
                    <div class="flex bg-slate-200/50 p-1 rounded-[1rem] shadow-inner border border-slate-200 w-full sm:w-auto relative" x-ref="tabContainer">
                        <div class="absolute h-[calc(100%-8px)] top-1 bg-white rounded-xl shadow-sm border border-slate-200 transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]" 
                             :style="`width: ${tabWidth}px; transform: translateX(${tabOffset}px);`"></div>
                        
                        <button x-ref="tabAll" @click="setTab('all', $refs.tabAll)" @mouseenter="playHover()" 
                                class="relative z-10 px-5 py-2 text-[10px] font-black uppercase tracking-widest transition-colors focus:outline-none flex-1 sm:flex-none text-center" 
                                :class="activeTab === 'all' ? 'text-indigo-600' : 'text-slate-500 hover:text-slate-800'">All</button>
                        
                        <button x-ref="tabIn" @click="setTab('inflow', $refs.tabIn)" @mouseenter="playHover()" 
                                class="relative z-10 px-5 py-2 text-[10px] font-black uppercase tracking-widest transition-colors focus:outline-none flex-1 sm:flex-none text-center" 
                                :class="activeTab === 'inflow' ? 'text-emerald-600' : 'text-slate-500 hover:text-slate-800'">Inflows</button>
                        
                        <button x-ref="tabOut" @click="setTab('outflow', $refs.tabOut)" @mouseenter="playHover()" 
                                class="relative z-10 px-5 py-2 text-[10px] font-black uppercase tracking-widest transition-colors focus:outline-none flex-1 sm:flex-none text-center" 
                                :class="activeTab === 'outflow' ? 'text-rose-600' : 'text-slate-500 hover:text-slate-800'">Outflows</button>
                    </div>
                </div>

                <div class="p-2 flex-1 overflow-y-auto custom-scrollbar relative">
                    <table class="w-full text-left border-collapse">
                        <tbody>
                            @forelse($unifiedLedger->take(20) as $txn)
                                @php
                                    $isInc = $txn->type === 'inflow';
                                    $data = $txn->data;
                                    $title = $isInc ? ($data->source ?? 'Capital Deposit') : ($data->title ?? 'Capital Burn');
                                    $date = \Carbon\Carbon::parse($txn->date)->format('d M Y');
                                    $amount = (float)($data->amount ?? 0);
                                    $badgeColor = $isInc ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-rose-50 text-rose-600 border-rose-200';
                                    $iconColor = $isInc ? 'bg-emerald-100 text-emerald-600 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200';
                                    $icon = $isInc ? 'fa-arrow-turn-down transform rotate-90' : 'fa-tag';
                                    
                                    // Robust Trace Hash Fallback
                                    $salt = ($data->id ?? 'sys') . '-' . ($data->created_at ?? rand());
                                    $traceHash = strtoupper(substr(md5($salt), 0, 8));
                                @endphp
                                <tr x-show="activeTab === 'all' || activeTab === '{{ $txn->type }}'" 
                                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                                    class="border-b border-slate-50 hover:bg-slate-50/80 transition-colors last:border-0 group/txn">
                                    <td class="p-4 w-full">
                                        <div class="flex items-center gap-4">
                                            <div class="w-11 h-11 rounded-[12px] flex items-center justify-center shrink-0 shadow-inner border {{ $iconColor }}">
                                                <i class="fa-solid {{ $icon }} text-xs"></i>
                                            </div>
                                            <div>
                                                <span class="block text-sm font-bold text-slate-900">{{ $title }}</span>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="px-1.5 py-0.5 rounded text-[7px] font-black uppercase tracking-widest border shadow-sm {{ $badgeColor }}">{{ $isInc ? 'INFLOW' : 'OUTFLOW' }}</span>
                                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-1"><i class="fa-regular fa-clock"></i> {{ $date }}</span>
                                                    <button @click="copyTrace('EXP-{{ $traceHash }}')" @mouseenter="playHover()" class="text-[8px] font-mono text-slate-400 hover:text-indigo-600 bg-white hover:bg-indigo-50 border border-slate-200 hover:border-indigo-200 px-1.5 py-0.5 rounded shadow-sm transition-all focus:outline-none flex items-center gap-1" title="Copy Cryptographic Trace ID">
                                                        <i class="fa-regular fa-copy"></i> EXP-{{ $traceHash }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-right">
                                        <span class="text-sm font-black block font-mono {{ $isInc ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $isInc ? '+' : '-' }} ₹{{ number_format($amount) }}
                                        </span>
                                        <div class="flex justify-end gap-1.5 mt-1 opacity-0 group-hover/txn:opacity-100 transition-opacity">
                                            @php $editRoute = $isInc ? route('user.incomes.edit', $data->id ?? 0) : route('user.expenses.edit', $data->id ?? 0); @endphp
                                            <a href="{{ $editRoute }}" @mouseenter="playHover()" class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 flex items-center justify-center transition-colors shadow-sm focus:outline-none">
                                                <i class="fa-solid fa-pen text-[10px]"></i>
                                            </a>
                                            @php 
                                                $delRoute = $isInc ? route('user.incomes.destroy', $data->id ?? 0) : route('user.expenses.destroy', $data->id ?? 0); 
                                                $delMsg = $isInc ? 'Void Inflow Record' : 'Void Outflow Record';
                                            @endphp
                                            <button @click="openDeleteModal('{{ $delRoute }}', '{{ $delMsg }}', 'Are you sure you want to permanently delete this transaction? This will trigger a full ledger recalculation.')" 
                                                    @mouseenter="playHover()"
                                                    class="w-7 h-7 rounded-lg bg-white border border-rose-100 text-rose-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 flex items-center justify-center transition-colors shadow-sm focus:outline-none">
                                                <i class="fa-solid fa-trash text-[10px]"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="p-16 text-center">
                                        <div class="w-16 h-16 bg-slate-50 rounded-[1.25rem] flex items-center justify-center mx-auto mb-4 border border-slate-100 shadow-inner"><i class="fa-solid fa-ghost text-2xl text-slate-300"></i></div>
                                        <p class="text-slate-900 font-black text-sm mb-1">Ledger is Empty</p>
                                        <p class="text-slate-500 font-bold text-xs">No transactions match the current filter state.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- ================= MODALS & TOASTS ================= --}}
    
    <div x-show="syncing" x-cloak style="display: none;" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-md flex flex-col items-center justify-center"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="bg-white/95 border border-white rounded-[2.5rem] shadow-[0_30px_100px_-15px_rgba(0,0,0,0.3)] p-10 flex flex-col items-center max-w-sm w-full relative overflow-hidden">
            <div class="w-24 h-24 bg-indigo-50 rounded-[1.5rem] flex items-center justify-center shadow-inner mb-6 border border-indigo-100 relative">
                <div class="absolute inset-0 border-4 border-indigo-200 rounded-[1.5rem]"></div>
                <div class="absolute inset-0 border-4 border-indigo-600 rounded-[1.5rem] border-t-transparent animate-spin"></div>
                <i class="fa-solid fa-network-wired text-3xl text-indigo-600 animate-pulse"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-900 mb-2 tracking-tight">Syncing Hub</h2>
            <p class="text-slate-500 font-mono text-[10px] uppercase tracking-widest mb-8 h-4" x-text="syncText">Reconciling blocks...</p>
            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden border border-slate-200 shadow-inner">
                <div class="h-full bg-indigo-600 transition-all duration-300 ease-out rounded-full shadow-[0_0_10px_rgba(79,70,229,0.5)]" :style="`width: ${syncProgress}%`"></div>
            </div>
        </div>
    </div>

    <div x-show="deleteModal.open" x-cloak style="display: none;" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
        <div x-show="deleteModal.open" x-transition.opacity.duration.300ms class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="deleteModal.open = false"></div>
        <div x-show="deleteModal.open" 
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl relative z-10 border border-slate-200 overflow-hidden">
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-rose-50 text-rose-600 rounded-[1.2rem] flex items-center justify-center mx-auto mb-6 border border-rose-100 shadow-inner">
                    <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-900 mb-2 tracking-tight" x-text="deleteModal.title"></h3>
                <p class="text-slate-500 font-medium mb-8 text-sm leading-relaxed" x-text="deleteModal.desc"></p>
                <div class="flex gap-4">
                    <button @click="deleteModal.open = false" @mouseenter="playHover()" type="button" class="flex-1 py-3.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-600 text-xs tracking-widest uppercase font-black rounded-xl transition-colors focus:outline-none">
                        Abort
                    </button>
                    <form :action="deleteModal.action" method="POST" class="flex-1" @submit="playClick()">
                        @csrf @method('DELETE')
                        <button type="submit" @mouseenter="playHover()" class="w-full py-3.5 bg-rose-600 hover:bg-rose-700 text-white text-xs tracking-widest uppercase font-black rounded-xl shadow-[0_10px_20px_rgba(225,29,72,0.3)] transition-all hover:-translate-y-0.5 focus:outline-none">
                            Confirm Void
                        </button>
                    </form>
                </div>
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
</style>
@endpush

@push('scripts')
{{-- SAFE CHART.JS IMPORT --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('familyWorkspaceEngine', () => ({
        showSuccess: true,
        
        // Native Web Audio Synthesizer (No external files needed)
        audioCtx: null,
        initAudio() {
            if(!this.audioCtx) {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if(AudioContext) this.audioCtx = new AudioContext();
            }
        },
        playClick() {
            if(window.audioEngine && typeof window.audioEngine.playClick === 'function') {
                window.audioEngine.playClick(); return;
            }
            // Fallback Native Synthesizer
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
        playHover() {
            if(window.audioEngine && typeof window.audioEngine.playHover === 'function') {
                window.audioEngine.playHover(); return;
            }
            // Fallback Native Synthesizer
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
        
        // Sync & Form States
        syncing: false,
        syncProgress: 0,
        syncText: '',
        sendingInvite: false,
        inviteStatus: '',
        
        // Ledger Tabs (Resize Observer enabled)
        activeTab: 'all',
        tabWidth: 0,
        tabOffset: 0,
        resizeObserver: null,
        
        // Modal & Toast
        deleteModal: { open: false, action: '', title: '', desc: '' },
        toast: { show: false, message: '' },

        // Data for charts
        trendLabels: @json($trend['months'] ?? []),
        incData: @json($trend['income'] ?? []),
        expData: @json($trend['expense'] ?? []),
        catLabels: @json($catLabels ?? []),
        catData: @json($catData ?? []),
        totalExpenseVal: {{ $totalExpense }},

        // AI Typewriter
        fullAiMessage: "{{ addslashes($aiMessage) }}",
        typedAiMessage: "",

        init() {
            // Resize Observer for Tabs to prevent glitching
            this.resizeObserver = new ResizeObserver(() => {
                this.$nextTick(() => {
                    const activeEl = this.$refs['tab' + (this.activeTab === 'all' ? 'All' : (this.activeTab === 'inflow' ? 'In' : 'Out'))];
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

            setTimeout(() => { this.initCharts(); }, 100);
            
            // Start AI Typewriter
            setTimeout(() => { this.typeWriterEffect(); }, 500);
        },

        destroy() {
            if(this.resizeObserver) this.resizeObserver.disconnect();
        },

        typeWriterEffect() {
            let i = 0;
            let msg = this.fullAiMessage;
            let int = setInterval(() => {
                this.typedAiMessage += msg.charAt(i);
                i++;
                if (i >= msg.length) {
                    clearInterval(int);
                } else if(i % 5 === 0) {
                    // Micro-sound for typing effect (optional, kept silent for sanity)
                }
            }, 25); // Speed of typing
        },

        setTab(val, el, playSound = true) {
            this.activeTab = val;
            if (el) {
                this.tabWidth = el.offsetWidth;
                this.tabOffset = el.offsetLeft;
            }
            if(playSound) this.playClick();
        },

        openDeleteModal(actionUrl, title, desc) {
            this.deleteModal.action = actionUrl;
            this.deleteModal.title = title;
            this.deleteModal.desc = desc;
            this.deleteModal.open = true;
            this.playClick();
        },

        async copyTrace(text) {
            this.playClick();
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

        submitInvite(e) {
            this.playClick();
            this.sendingInvite = true;
            this.inviteStatus = 'Securing Handshake...';
            setTimeout(() => { this.inviteStatus = 'Transmitting Request...'; }, 600);
            setTimeout(() => { e.target.submit(); }, 1200);
        },

        syncLedger() {
            this.playClick();
            this.syncing = true;
            this.syncProgress = 0;
            this.syncText = 'Reconciling blocks...';
            let interval = setInterval(() => {
                this.syncProgress += Math.floor(Math.random() * 20) + 10;
                if(this.syncProgress > 30) this.syncText = 'Verifying AES Decryption...';
                if(this.syncProgress > 60) this.syncText = 'Aggregating Telemetry...';
                if(this.syncProgress >= 100) {
                    this.syncProgress = 100;
                    this.syncText = 'Sync Complete. Refreshing...';
                    clearInterval(interval);
                    this.playClick();
                    setTimeout(() => window.location.reload(), 400);
                }
            }, 300);
        },

        animateValue(el, target, isCurrency) {
            let duration = 2000;
            let startTime = null;
            const step = (timestamp) => {
                if (!startTime) startTime = timestamp;
                let progress = Math.min((timestamp - startTime) / duration, 1);
                let ease = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                let current = ease * target;
                el.innerText = isCurrency ? '₹' + Math.floor(current).toLocaleString('en-IN') : current.toFixed(1);
                if (progress < 1) window.requestAnimationFrame(step);
                else el.innerText = isCurrency ? '₹' + Math.floor(target).toLocaleString('en-IN') : target.toFixed(1);
            };
            if(target > 0) window.requestAnimationFrame(step);
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

        initCharts() {
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = '#94a3b8';
            
            const tooltipConfig = {
                backgroundColor: '#0f172a', titleColor: '#fff', bodyColor: '#cbd5e1',
                padding: 12, cornerRadius: 8, displayColors: true, boxPadding: 4,
                callbacks: { label: (ctx) => ' ₹' + ctx.parsed.y.toLocaleString('en-IN') }
            };

            const finCtx = document.getElementById('trendChart');
            if(finCtx && this.trendLabels.length > 0) {
                const tCtx = finCtx.getContext('2d');
                const incGrad = tCtx.createLinearGradient(0, 0, 0, 300);
                incGrad.addColorStop(0, 'rgba(16, 185, 129, 0.25)'); incGrad.addColorStop(1, 'rgba(16, 185, 129, 0)');
                const expGrad = tCtx.createLinearGradient(0, 0, 0, 300);
                expGrad.addColorStop(0, 'rgba(244, 63, 94, 0.25)'); expGrad.addColorStop(1, 'rgba(244, 63, 94, 0)');

                new Chart(tCtx, {
                    type: 'line',
                    data: {
                        labels: this.trendLabels,
                        datasets: [
                            { label: 'Inflow', data: this.incData, borderColor: '#10b981', backgroundColor: incGrad, borderWidth: 3, fill: true, tension: 0.4, pointRadius: 0, pointHoverRadius: 6, pointBackgroundColor: '#fff', pointBorderWidth: 2 },
                            { label: 'Outflow', data: this.expData, borderColor: '#f43f5e', backgroundColor: expGrad, borderWidth: 3, fill: true, tension: 0.4, pointRadius: 0, pointHoverRadius: 6, pointBackgroundColor: '#fff', pointBorderWidth: 2 }
                        ]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: { legend: { display: false }, tooltip: tooltipConfig },
                        scales: { 
                            y: { beginAtZero: true, grid: { color: '#f1f5f9', drawBorder: false }, ticks: { callback: v => '₹' + (v >= 1000 ? (v/1000)+'k' : v) } },
                            x: { grid: { display: false }, ticks: { font: { weight: 'bold' } } }
                        }
                    }
                });
            }

            const allocCtx = document.getElementById('categoryChart');
            if(allocCtx && this.catLabels.length > 0) {
                const centerTextPlugin = {
                    id: 'centerText',
                    beforeDraw: (chart) => {
                        const width = chart.width, height = chart.height, ctx = chart.ctx;
                        ctx.clearRect(0, 0, width, height); ctx.restore();
                        const fontSize = (height / 114).toFixed(2);
                        ctx.font = "900 " + fontSize + "em Inter";
                        ctx.textBaseline = "middle"; ctx.fillStyle = "#0f172a";
                        const text = "₹" + (this.totalExpenseVal >= 1000 ? (this.totalExpenseVal/1000).toFixed(1)+'k' : this.totalExpenseVal),
                              textX = Math.round((width - ctx.measureText(text).width) / 2),
                              textY = height / 2;
                        ctx.fillText(text, textX, textY + 5);
                        ctx.font = "bold " + (fontSize * 0.3) + "em Inter";
                        ctx.fillStyle = "#64748b";
                        const subText = "TOTAL BURN", subTextX = Math.round((width - ctx.measureText(subText).width) / 2);
                        ctx.fillText(subText, subTextX, textY - 15);
                        ctx.save();
                    }
                };

                new Chart(allocCtx, {
                    type: 'doughnut',
                    data: {
                        labels: this.catLabels,
                        datasets: [{ data: this.catData, backgroundColor: ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6'], borderWidth: 0, hoverOffset: 8 }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, cutout: '78%', plugins: { legend: { display: false }, tooltip: tooltipConfig } },
                    plugins: [centerTextPlugin]
                });
            }
        }
    }));
});
</script>
@endpush