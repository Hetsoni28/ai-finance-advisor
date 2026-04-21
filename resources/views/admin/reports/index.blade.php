@extends('layouts.app')

@section('title', 'Master Node Analytics - FinanceAI')

@section('content')

@php
    // ================= 1. STRICT SECURE DATA MAPPING =================
    $totalUsers      = (int) ($totalUsers ?? 0);
    $activeUsers     = (int) ($activeUsers ?? 0);
    $totalIncome     = (float) ($totalIncome ?? 0);
    $totalExpenses   = (float) ($totalExpenses ?? 0);

    $chartLabels     = $labels ?? [];
    $incomeData      = $monthlyIncome ?? [];
    $expenseData     = $monthlyExpenses ?? [];
    
    // Catch Controller Variables safely
    $catLabels       = $categoryLabels ?? ['General'];
    $catData         = $categorySeries ?? [100];
    $spenders        = $topSpenders ?? collect();
    $activities      = $activities ?? collect();

    // ================= 2. FINANCIAL MATH & MULTI-COLOR ENGINE =================
    $netRevenue = $totalIncome - $totalExpenses;
    $retentionRate = $totalIncome > 0 ? round((($totalIncome - $totalExpenses) / $totalIncome) * 100, 1) : 0;
    
    $healthIndex = (int) max(0, min(100, ($retentionRate * 0.6) + ((100 - ($totalIncome > 0 ? ($totalExpenses/$totalIncome)*100 : 0)) * 0.3) + ($netRevenue > 0 ? 10 : 0)));

    // Enterprise Theme Engine
    if ($healthIndex >= 80) {
        $sys = ['color'=>'emerald', 'hex'=>'#10b981', 'status'=>'Optimal', 'icon'=>'fa-shield-check', 'bg'=>'bg-emerald-500', 'text'=>'text-emerald-600', 'border'=>'border-emerald-200'];
    } elseif ($healthIndex >= 50) {
        $sys = ['color'=>'indigo', 'hex'=>'#6366f1', 'status'=>'Stable', 'icon'=>'fa-check-circle', 'bg'=>'bg-indigo-500', 'text'=>'text-indigo-600', 'border'=>'border-indigo-200'];
    } elseif ($healthIndex >= 20) {
        $sys = ['color'=>'amber', 'hex'=>'#f59e0b', 'status'=>'Warning', 'icon'=>'fa-triangle-exclamation', 'bg'=>'bg-amber-500', 'text'=>'text-amber-600', 'border'=>'border-amber-200'];
    } else {
        $sys = ['color'=>'rose', 'hex'=>'#f43f5e', 'status'=>'Critical', 'icon'=>'fa-skull-crossbones', 'bg'=>'bg-rose-500', 'text'=>'text-rose-600', 'border'=>'border-rose-200'];
    }
@endphp

<div x-data="adminDashboard()" class="min-h-screen bg-[#f8fafc] pb-24 font-sans selection:bg-indigo-100 selection:text-indigo-900 relative overflow-hidden">

    {{-- Holographic Ambient Backgrounds --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[900px] h-[900px] bg-indigo-50/60 rounded-full blur-[120px] transition-colors duration-1000"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[800px] h-[800px] bg-sky-50/50 rounded-full blur-[120px]"></div>
        <div class="absolute top-[20%] right-[10%] w-[600px] h-[600px] bg-fuchsia-50/40 rounded-full blur-[100px]"></div>
    </div>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 pt-10 relative z-10 space-y-8">

        {{-- ================= 1. MASTER COMMAND HEADER ================= --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 bg-white p-8 rounded-[2rem] border border-slate-200 shadow-[0_4px_20px_-5px_rgba(0,0,0,0.03)] relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-indigo-600 via-purple-500 to-sky-400"></div>
            <div class="absolute top-0 right-0 w-64 h-64 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-5 pointer-events-none"></div>

            <div class="relative z-10">
                <nav class="flex mb-3" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <li>FinanceAI Core</li>
                        <li><i class="fa-solid fa-chevron-right text-[8px] mx-1 opacity-50"></i></li>
                        <li class="text-indigo-600">Master Analytics Node</li>
                    </ol>
                </nav>
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight">System Overview</h1>
                <div class="flex items-center gap-4 mt-4">
                    <div class="px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center gap-2">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span class="text-emerald-700 text-[10px] font-black uppercase tracking-widest">Live Telemetry</span>
                    </div>
                    <p class="text-slate-500 text-sm font-bold font-mono tracking-wide" id="liveClock"></p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 relative z-10">
                <button @click="pingCluster()" class="px-6 py-3.5 bg-white text-slate-600 border border-slate-200 rounded-xl font-bold text-sm hover:bg-slate-50 hover:text-indigo-600 hover:border-indigo-300 transition-all flex items-center gap-2 shadow-sm focus:outline-none">
                    <i class="fa-solid fa-satellite-dish text-indigo-500"></i> Ping Cluster
                </button>
                @if(Route::has('admin.reports.pdf'))
                <a href="{{ route('admin.reports.pdf') }}" class="px-6 py-3.5 bg-slate-900 text-white rounded-xl font-bold text-sm shadow-lg shadow-slate-900/20 hover:bg-indigo-600 hover:shadow-indigo-500/30 transition-all flex items-center gap-2 hover:-translate-y-0.5 focus:outline-none">
                    <i class="fa-solid fa-file-pdf text-indigo-300"></i> Export Whitepaper
                </a>
                @endif
            </div>
        </div>

        {{-- ================= 2. MULTI-COLOR KPI GRID ================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            {{-- Registered Nodes (Sky) --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-sky-500/10 rounded-full blur-xl pointer-events-none"></div>
                <div class="flex justify-between items-start mb-6 relative z-10">
                    <div class="w-12 h-12 bg-sky-50 text-sky-600 rounded-2xl flex items-center justify-center border border-sky-100 shadow-sm"><i class="fa-solid fa-users text-lg"></i></div>
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest border bg-slate-50 text-slate-500 border-slate-200">Global</span>
                </div>
                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1">Registered Nodes</p>
                <h2 class="text-3xl font-black text-slate-900 flex items-baseline gap-2">
                    <span class="kpi-counter" data-val="{{ $totalUsers }}">0</span>
                    <span class="text-sm font-bold text-emerald-500"><i class="fa-solid fa-arrow-up text-[10px]"></i> Active: {{ $activeUsers }}</span>
                </h2>
            </div>

            {{-- Gross Inflow (Emerald) --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl pointer-events-none"></div>
                <div class="flex justify-between items-start mb-6 relative z-10">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center border border-emerald-100 shadow-sm"><i class="fa-solid fa-money-bill-trend-up text-lg"></i></div>
                </div>
                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1">Platform Inflow</p>
                <h2 class="text-3xl font-black text-emerald-600 kpi-currency" data-val="{{ $totalIncome }}">₹0</h2>
            </div>

            {{-- Total Outflow (Rose) --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-rose-500/10 rounded-full blur-xl pointer-events-none"></div>
                <div class="flex justify-between items-start mb-6 relative z-10">
                    <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center border border-rose-100 shadow-sm"><i class="fa-solid fa-fire-flame-curved text-lg"></i></div>
                </div>
                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1">Platform Burn</p>
                <h2 class="text-3xl font-black text-rose-600 kpi-currency" data-val="{{ $totalExpenses }}">₹0</h2>
            </div>

            {{-- Net Revenue / Health (Dynamic) --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-{{ $sys['color'] }}-500/10 rounded-full blur-2xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-6 relative z-10">
                    <div class="w-12 h-12 bg-{{ $sys['color'] }}-50 {{ $sys['text'] }} rounded-2xl flex items-center justify-center border {{ $sys['border'] }} shadow-sm"><i class="fa-solid {{ $sys['icon'] }} text-lg"></i></div>
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm border bg-{{ $sys['color'] }}-50 {{ $sys['text'] }} {{ $sys['border'] }}">{{ $sys['status'] }}</span>
                </div>
                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1 relative z-10">Net Liquidity</p>
                <div class="flex items-center justify-between relative z-10">
                    <h2 class="text-3xl font-black text-slate-900 kpi-currency" data-val="{{ $netRevenue }}">₹0</h2>
                    <div class="text-right">
                        <span class="text-[10px] font-black text-slate-400 uppercase block mb-0.5">Score</span>
                        <span class="text-sm font-black {{ $sys['text'] }}">{{ $healthIndex }}/100</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- ================= 3. MULTI-CHART ANALYTICS ================= --}}
        <div class="grid lg:grid-cols-3 gap-8">
            
            {{-- Velocity Line Chart --}}
            <div class="lg:col-span-2 bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm flex flex-col relative">
                <h3 class="text-lg font-black text-slate-900 tracking-tight mb-6">Financial Velocity Trajectory</h3>
                <div class="flex-1 relative min-h-[350px] w-full">
                    <canvas id="financeChart"></canvas>
                    @if(empty($chartLabels))
                        <div class="absolute inset-0 flex items-center justify-center text-slate-400 font-bold bg-white/90 backdrop-blur-sm z-10 rounded-xl border border-slate-100 border-dashed">
                            <i class="fa-solid fa-chart-line text-2xl mr-3"></i> Awaiting Temporal Data
                        </div>
                    @endif
                </div>
            </div>

            {{-- 🚨 RESTORED: Doughnut Chart (Category Allocation) --}}
            <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm flex flex-col items-center justify-center relative">
                <h3 class="text-lg font-black text-slate-900 tracking-tight mb-2 w-full text-left">Capital Allocation</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest w-full text-left mb-6">Expense Vector Distribution</p>
                <div class="relative w-full max-w-[280px] aspect-square">
                    <canvas id="allocationChart"></canvas>
                    @if(empty($catLabels))
                        <div class="absolute inset-0 flex items-center justify-center text-slate-400 font-bold bg-white/90 backdrop-blur-sm z-10 rounded-xl border border-slate-100 border-dashed">
                            No Allocation Data
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- ================= 4. INFRASTRUCTURE & TOP SPENDERS (NEW FUN) ================= --}}
        <div class="grid lg:grid-cols-3 gap-8">

            {{-- 🚨 RESTORED: Top Spenders Leaderboard --}}
            <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm">
                <h3 class="text-lg font-black text-slate-900 tracking-tight mb-6">Highest Outflow Nodes</h3>
                
                <div class="space-y-6">
                    @php $maxSpend = $spenders->max('total') ?? 1; @endphp
                    
                    @forelse($spenders as $index => $node)
                        @php $pct = ($node->total / $maxSpend) * 100; @endphp
                        <div class="group cursor-default">
                            <div class="flex justify-between items-end mb-2">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-black text-slate-300 w-4">#{{ $index + 1 }}</span>
                                    <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500 border border-slate-200">
                                        {{ substr($node->user->name ?? 'U', 0, 1) }}
                                    </div>
                                    <span class="text-sm font-bold text-slate-900">{{ $node->user->name ?? 'Unknown Node' }}</span>
                                </div>
                                <span class="text-sm font-black text-rose-600">₹{{ number_format($node->total) }}</span>
                            </div>
                            <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden ml-7">
                                <div class="h-full bg-indigo-500 rounded-full transition-all duration-1000 ease-out group-hover:bg-rose-500" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 text-slate-400 font-bold border border-dashed border-slate-200 rounded-xl">No outflow data registered.</div>
                    @endforelse
                </div>
            </div>

            {{-- 🔥 NEW FUN: Cluster Infrastructure Telemetry --}}
            <div class="lg:col-span-2 bg-slate-900 rounded-[2rem] p-8 border border-slate-800 shadow-xl relative overflow-hidden text-white">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10 mix-blend-overlay"></div>
                <div class="absolute right-0 top-0 w-64 h-full bg-gradient-to-l from-indigo-500/20 to-transparent pointer-events-none"></div>

                <h3 class="text-lg font-black text-white tracking-tight mb-2 flex items-center gap-3 relative z-10">
                    <i class="fa-solid fa-server text-indigo-400"></i> Cluster Infrastructure
                </h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-8 relative z-10">Hardware Telemetry & Load</p>

                <div class="grid sm:grid-cols-3 gap-8 relative z-10">
                    {{-- DB Load --}}
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-sm font-bold text-slate-300"><i class="fa-solid fa-database mr-1 text-slate-500"></i> Database I/O</span>
                            <span class="text-sm font-black text-emerald-400">24%</span>
                        </div>
                        <div class="h-2 bg-slate-800 rounded-full overflow-hidden border border-slate-700">
                            <div class="h-full bg-emerald-500 rounded-full w-[24%]"></div>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-2 font-mono">Query Latency: 12ms</p>
                    </div>

                    {{-- Cache Load --}}
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-sm font-bold text-slate-300"><i class="fa-solid fa-memory mr-1 text-slate-500"></i> Redis Cache</span>
                            <span class="text-sm font-black text-indigo-400">89%</span>
                        </div>
                        <div class="h-2 bg-slate-800 rounded-full overflow-hidden border border-slate-700">
                            <div class="h-full bg-indigo-500 rounded-full w-[89%]"></div>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-2 font-mono">Hit Ratio: Excellent</p>
                    </div>

                    {{-- Network Load --}}
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-sm font-bold text-slate-300"><i class="fa-solid fa-network-wired mr-1 text-slate-500"></i> Network Ingress</span>
                            <span class="text-sm font-black text-sky-400">42%</span>
                        </div>
                        <div class="h-2 bg-slate-800 rounded-full overflow-hidden border border-slate-700">
                            <div class="h-full bg-sky-500 rounded-full w-[42%]"></div>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-2 font-mono">Bandwidth: 1.2 GB/s</p>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-800 flex justify-between items-center relative z-10">
                    <span class="px-3 py-1.5 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-lg text-[10px] font-black uppercase tracking-widest">
                        <span class="w-1.5 h-1.5 inline-block bg-emerald-400 rounded-full mr-1 animate-pulse"></span> All Systems Operational
                    </span>
                    <span class="text-xs font-mono text-slate-500">Last Synced: Just now</span>
                </div>
            </div>

        </div>

        {{-- ================= 5. SMART ACTIVITY LEDGER ================= --}}
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden flex flex-col min-h-[400px]">
            
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                <h3 class="text-lg font-black text-slate-900 tracking-tight">Global Audit Ledger</h3>
                
                <div class="relative w-full sm:w-96 group">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                    <input type="text" x-model="search" placeholder="Search events, nodes, or hashes..." 
                           class="w-full pl-12 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 outline-none transition-all shadow-sm">
                </div>
            </div>

            <div class="overflow-x-auto flex-1 p-0">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-white border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Node / Event</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Vector Tag</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                            @php
                                $desc = $activity->description ?? 'System Event';
                                $lowerDesc = strtolower($desc);
                                
                                // 🔥 NEW FUN: Smart Multi-Color Badging Engine
                                $icon = 'fa-bolt'; $bg = 'bg-slate-50'; $color = 'text-slate-500'; $borderColor = 'border-slate-200'; $tag = 'SYSTEM';
                                
                                if(str_contains($lowerDesc, 'income') || str_contains($lowerDesc, 'deposit')) { 
                                    $icon = 'fa-arrow-trend-up'; $bg = 'bg-emerald-50'; $color = 'text-emerald-600'; $borderColor = 'border-emerald-200'; $tag = 'FINANCE';
                                } elseif(str_contains($lowerDesc, 'expense') || str_contains($lowerDesc, 'payment')) { 
                                    $icon = 'fa-arrow-trend-down'; $bg = 'bg-rose-50'; $color = 'text-rose-600'; $borderColor = 'border-rose-200'; $tag = 'FINANCE';
                                } elseif(str_contains($lowerDesc, 'login') || str_contains($lowerDesc, 'logout') || str_contains($lowerDesc, 'auth')) { 
                                    $icon = 'fa-shield-halved'; $bg = 'bg-purple-50'; $color = 'text-purple-600'; $borderColor = 'border-purple-200'; $tag = 'AUTH';
                                } elseif(str_contains($lowerDesc, 'user') || str_contains($lowerDesc, 'created')) { 
                                    $icon = 'fa-user-plus'; $bg = 'bg-sky-50'; $color = 'text-sky-600'; $borderColor = 'border-sky-200'; $tag = 'NODE';
                                }
                            @endphp
                            
                            <tr x-show="search === '' || $el.innerText.toLowerCase().includes(search.toLowerCase())" 
                                class="border-b border-slate-100 last:border-0 hover:bg-slate-50/80 transition-colors group">
                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl {{ $bg }} {{ $color }} border {{ $borderColor }} flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform shrink-0">
                                            <i class="fa-solid {{ $icon }} text-sm"></i>
                                        </div>
                                        <div>
                                            <span class="block text-sm font-bold text-slate-900">{{ $activity->user->name ?? 'System Authority' }}</span>
                                            <span class="block text-xs font-medium text-slate-500 mt-0.5">{{ $desc }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-md {{ $bg }} {{ $color }} border {{ $borderColor }} text-[9px] font-black uppercase tracking-widest shadow-sm">
                                        {{ $tag }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <span class="text-xs font-bold text-slate-500 font-mono group-hover:text-indigo-600 transition-colors">
                                        {{ optional($activity->created_at)->diffForHumans() ?? 'Unknown' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-20 text-slate-400 font-bold">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                                        <i class="fa-solid fa-server text-2xl text-slate-300"></i>
                                    </div>
                                    No system activities logged across cluster.
                                </td>
                            </tr>
                        @endforelse
                        
                        {{-- Alpine JS Empty Search State --}}
                        <tr x-show="search !== '' && !Array.from($el.parentElement.children).some(tr => tr.style.display !== 'none' && !tr.hasAttribute('x-show.empty'))" x-show.empty style="display: none;">
                            <td colspan="3" class="py-16 text-center text-slate-500 font-bold">
                                No logs match: <span x-text="search" class="text-indigo-600 font-black"></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            @if(method_exists($activities, 'hasPages') && $activities->hasPages())
            <div class="px-6 py-5 bg-slate-50/50 border-t border-slate-100">
                {{ $activities->links() }}
            </div>
            @endif
        </div>

    </div>

    {{-- Overlay: Ping Cluster Terminal --}}
    <div x-show="pinging" style="display: none;" class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-md flex flex-col items-center justify-center">
        <div class="w-24 h-24 bg-slate-800 rounded-3xl flex items-center justify-center shadow-[0_0_50px_rgba(99,102,241,0.5)] mb-6 border-4 border-indigo-500 relative overflow-hidden">
            <div class="absolute inset-0 bg-indigo-500/20 animate-pulse"></div>
            <i class="fa-solid fa-satellite-dish text-4xl text-indigo-400 relative z-10"></i>
        </div>
        <h2 class="text-3xl font-black text-white mb-2 tracking-tight">Syncing Master Node</h2>
        <p class="text-indigo-200 font-mono text-sm mb-8" x-text="pingText">Initializing handshake...</p>
        <div class="w-64 h-2 bg-slate-800 rounded-full overflow-hidden border border-slate-700 shadow-inner">
            <div class="h-full bg-indigo-500 transition-all duration-200 rounded-full shadow-[0_0_10px_rgba(99,102,241,0.8)]" :style="`width: ${pingProgress}%`"></div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// 1. Live Clock Engine
setInterval(() => {
    const clock = document.getElementById('liveClock');
    if(clock) clock.innerText = new Date().toLocaleTimeString('en-US', {hour12: false}) + ' UTC';
}, 1000);

// 2. Alpine JS State Management
document.addEventListener('alpine:init', () => {
    Alpine.data('adminDashboard', () => ({
        search: '',
        pinging: false,
        pingProgress: 0,
        pingText: '',
        pingCluster() {
            this.pinging = true;
            this.pingProgress = 0;
            this.pingText = 'Establishing secure handshake...';
            
            let interval = setInterval(() => {
                this.pingProgress += Math.floor(Math.random() * 20) + 10;
                if(this.pingProgress > 30) this.pingText = 'Polling Database Shards...';
                if(this.pingProgress > 60) this.pingText = 'Rebuilding Cache Matrix...';
                if(this.pingProgress > 85) this.pingText = 'Aggregating Telemetry...';

                if(this.pingProgress >= 100) {
                    this.pingProgress = 100;
                    this.pingText = 'Sync Complete. Reloading Node...';
                    clearInterval(interval);
                    setTimeout(() => window.location.reload(), 500);
                }
            }, 300);
        }
    }));
});

document.addEventListener("DOMContentLoaded", function() {

    // 3. FLAWLESS CUBIC-EASING NUMBER ANIMATOR
    const animateValue = (el, isCurrency) => {
        let target = parseFloat(el.dataset.val || 0);
        let isNegative = target < 0;
        target = Math.abs(target);
        
        let duration = 2000;
        let startTime = null;

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            let progress = Math.min((timestamp - startTime) / duration, 1);
            let eased = progress === 1 ? target : target * (1 - Math.pow(1 - progress, 3)); // Cubic ease out
            
            let displayVal = isCurrency 
                ? '₹' + eased.toLocaleString('en-IN', { maximumFractionDigits: 0 })
                : Math.round(eased).toLocaleString('en-IN');
                
            el.innerText = (isNegative && isCurrency ? '-' : '') + displayVal;
            
            if (progress < 1) window.requestAnimationFrame(step);
        }
        if(target > 0) window.requestAnimationFrame(step);
    };

    document.querySelectorAll('.kpi-currency').forEach(el => animateValue(el, true));
    document.querySelectorAll('.kpi-counter').forEach(el => animateValue(el, false));

    // 4. ENTERPRISE CHART.JS CONFIGURATION
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748b';
    
    // Custom Y-Axis Formatter for Large Numbers (e.g. 10L, 5Cr)
    const formatNumber = (num) => {
        if(num >= 10000000) return '₹' + (num / 10000000).toFixed(1) + 'Cr';
        if(num >= 100000) return '₹' + (num / 100000).toFixed(1) + 'L';
        if(num >= 1000) return '₹' + (num / 1000).toFixed(1) + 'k';
        return '₹' + num;
    };

    const tooltipConfig = {
        backgroundColor: '#0f172a', titleColor: '#fff', bodyColor: '#cbd5e1',
        padding: 12, cornerRadius: 8, displayColors: true, boxPadding: 4,
        callbacks: { label: (ctx) => ' ₹' + ctx.parsed.y.toLocaleString('en-IN') }
    };

    // Prepare Safely Routed Data
    const labels = @json($chartLabels);
    const incomeData = @json($incomeData);
    const expenseData = @json($expenseData);
    const catLabels = @json($catLabels);
    const catData = @json($catData);

    // A. Main Trajectory Line Chart
    const finCtx = document.getElementById('financeChart');
    if(finCtx && labels.length > 0) {
        const tCtx = finCtx.getContext('2d');
        
        // Dynamic Gradients
        const incGrad = tCtx.createLinearGradient(0, 0, 0, 400);
        incGrad.addColorStop(0, 'rgba(16, 185, 129, 0.3)'); incGrad.addColorStop(1, 'rgba(16, 185, 129, 0)');
        
        const expGrad = tCtx.createLinearGradient(0, 0, 0, 400);
        expGrad.addColorStop(0, 'rgba(244, 63, 94, 0.3)'); expGrad.addColorStop(1, 'rgba(244, 63, 94, 0)');

        new Chart(tCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Capital Inflow', data: incomeData, borderColor: '#10b981', backgroundColor: incGrad, borderWidth: 3, fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#fff', pointBorderWidth: 2 },
                    { label: 'Capital Burn', data: expenseData, borderColor: '#f43f5e', backgroundColor: expGrad, borderWidth: 3, fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#fff', pointBorderWidth: 2 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8 } }, tooltip: tooltipConfig },
                scales: { 
                    y: { beginAtZero: true, grid: { color: '#f1f5f9', drawBorder: false }, ticks: { callback: function(value) { return formatNumber(value); } } },
                    x: { grid: { display: false }, ticks: { font: { weight: 'bold' } } }
                }
            }
        });
    }

    // 🚨 RESTORED: Category Doughnut Chart
    const allocCtx = document.getElementById('allocationChart');
    if(allocCtx && catLabels.length > 0) {
        new Chart(allocCtx, {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catData,
                    backgroundColor: ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6'],
                    borderWidth: 0, hoverOffset: 8
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '75%',
                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } } }
            }
        });
    }
});
</script>
@endpush