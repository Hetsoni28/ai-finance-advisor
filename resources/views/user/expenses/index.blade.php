@extends('layouts.app')

@section('title', 'Expense Intelligence - FinanceAI')

@section('content')

@php
    // ================= 1. ZERO-CRASH DATA PREPARATION =================
    $expenses = $expenses ?? collect(); // Ensure it's never null
    
    // Core Metrics
    $total = (float)($total ?? 0);
    $totalRecords = method_exists($expenses, 'total') ? $expenses->total() : $expenses->count();
    
    // Grouping & Math (Safe Collections)
    $categoryData = collect($expenses->items() ?? $expenses)->groupBy('category')->map->sum('amount');
    
    // Find top category safely
    $topCategory = $categoryData->sortDesc()->keys()->first() ?? 'Uncategorized';
    
    // Monthly Data Aggregation
    $monthlyData = collect($expenses->items() ?? $expenses)->groupBy(function($e) {
        return $e->expense_date ? \Carbon\Carbon::parse($e->expense_date)->format('M Y') : 'Unknown';
    })->map->sum('amount');

    // Growth Calculation
    $monthlyValues = $monthlyData->values();
    $lastMonth = (float)($monthlyValues->last() ?? 0);
    $prevMonth = (float)($monthlyValues->slice(-2, 1)->first() ?? 0);
    
    $growth = 0;
    if ($prevMonth > 0) {
        $growth = (($lastMonth - $prevMonth) / $prevMonth) * 100;
    } elseif ($lastMonth > 0) {
        $growth = 100; // From 0 to something is 100% increase
    }

    // AI Heuristics
    $aiMessage = $growth > 5 
        ? "Attention: Outflows have increased by **" . number_format($growth, 1) . "%**. Your highest burn rate is in the **{$topCategory}** category. Consider auditing discretionary spending this week."
        : "Optimal: Outflow velocity is stable. You are showing excellent discipline in capital management, particularly in keeping **{$topCategory}** expenses normalized.";

    if ($total == 0) {
        $aiMessage = "Welcome to FinanceAI. Your expense ledger is currently clean. Start by adding a new transaction or scanning a receipt to generate predictive insights.";
    }

    // UI Theme Config (Pastel Light Mode)
    $colors = ['#6366f1', '#10b981', '#f43f5e', '#f59e0b', '#8b5cf6', '#0ea5e9', '#64748b'];
@endphp

<div class="min-h-screen bg-[#f8fafc] pb-24 font-sans selection:bg-indigo-100 selection:text-indigo-900 relative" x-data="{ search: '', showScanner: false }">
    
    {{-- Pristine Ambient Background --}}
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.02]">
        <div class="absolute top-[-10%] right-[-5%] w-[800px] h-[800px] bg-indigo-50 rounded-full blur-[120px]"></div>
    </div>

    {{-- Main Container --}}
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 pt-10 space-y-8 relative z-10">

        {{-- ================= 1. HEADER & ACTIONS ================= --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-indigo-500"></div>

            <div>
                <nav class="flex mb-3" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <li>FinanceAI Engine</li>
                        <li><i class="fa-solid fa-chevron-right text-[8px] mx-1"></i></li>
                        <li class="text-indigo-600">Outflow Ledger</li>
                    </ol>
                </nav>
                <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">Expense Analytics</h1>
                <p class="text-slate-500 text-sm font-medium mt-2">
                    System Intelligence active. Tracking <strong>{{ $totalRecords }}</strong> verified records.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button @click="showScanner = true" class="px-5 py-3.5 bg-slate-50 text-indigo-600 border border-indigo-100 rounded-xl font-bold text-sm hover:bg-indigo-50 hover:border-indigo-300 transition-all flex items-center gap-2 focus:outline-none shadow-sm">
                    <i class="fa-solid fa-camera-viewfinder"></i> Scan Receipt
                </button>
                
                @if(Route::has('user.expenses.export.pdf'))
                <a href="{{ route('user.expenses.export.pdf') }}" class="px-5 py-3.5 bg-white border border-slate-200 text-slate-700 rounded-xl font-bold text-sm hover:text-indigo-600 hover:border-indigo-200 transition-all flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-file-pdf"></i> Statement
                </a>
                @endif
                
                <a href="{{ route('user.expenses.create') ?? '#' }}" class="px-6 py-3.5 bg-slate-900 text-white rounded-xl font-bold text-sm shadow-md hover:bg-indigo-600 transition-all flex items-center gap-2 hover:-translate-y-0.5">
                    <i class="fa-solid fa-plus"></i> Manual Entry
                </a>
            </div>
        </div>

        {{-- ================= 2. KPI GRID ================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex justify-between items-start mb-6">
                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center border border-indigo-100 shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-wallet text-lg"></i>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm border {{ $growth <= 0 ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100' }}">
                        {{ $growth > 0 ? '+' : '' }}{{ number_format($growth, 1) }}% M/M
                    </span>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Outflow</p>
                <h2 class="text-3xl font-black text-slate-900 kpi-animate" data-val="{{ $total }}">₹0</h2>
            </div>

            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex justify-between items-start mb-6">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center border border-emerald-100 shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-chart-pie text-lg"></i>
                    </div>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Prime Category</p>
                <h2 class="text-2xl font-black text-slate-900 truncate" title="{{ $topCategory }}">{{ $topCategory }}</h2>
            </div>

            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex justify-between items-start mb-6">
                    <div class="w-12 h-12 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center border border-sky-100 shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fa-regular fa-calendar-check text-lg"></i>
                    </div>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Monthly Average</p>
                <h2 class="text-2xl font-black text-slate-900">₹{{ number_format($monthlyValues->avg(), 0) }}</h2>
            </div>

            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex justify-between items-start mb-6">
                    <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center border border-rose-100 shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-receipt text-lg"></i>
                    </div>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Highest Single Burn</p>
                <h2 class="text-2xl font-black text-slate-900">₹{{ number_format(collect($expenses->items() ?? $expenses)->max('amount') ?? 0) }}</h2>
            </div>

        </div>

        {{-- ================= 3. AI EXECUTIVE BANNER ================= --}}
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-6 sm:p-8 flex flex-col md:flex-row gap-6 items-center relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-64 h-full bg-indigo-50/50 skew-x-12 translate-x-10 group-hover:translate-x-0 transition-transform duration-700 pointer-events-none"></div>
            
            <div class="w-14 h-14 bg-indigo-50 border border-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 shrink-0 shadow-sm z-10">
                <i class="fa-solid fa-brain text-2xl animate-pulse"></i>
            </div>
            
            <div class="flex-1 z-10">
                <div class="flex items-center gap-3 mb-1">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">FinanceAI Heuristics</h3>
                    <span class="w-2 h-2 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.8)]"></span>
                </div>
                <div class="min-h-[44px]">
                    <p id="ai-text" data-message="{{ $aiMessage }}" class="text-slate-600 font-medium leading-relaxed"></p>
                </div>
            </div>
        </div>

        {{-- ================= 4. CHARTS & ALLOCATIONS ================= --}}
        <div class="grid lg:grid-cols-7 gap-6">
            
            {{-- Flow Dynamics Line Chart --}}
            <div class="lg:col-span-4 bg-white p-8 rounded-[2rem] shadow-sm border border-slate-200 flex flex-col">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">Outflow Trajectory</h3>
                    <span class="px-3 py-1 bg-slate-50 border border-slate-200 rounded-lg text-[10px] font-bold text-slate-500 uppercase tracking-widest">Year to Date</span>
                </div>
                <div class="flex-1 relative min-h-[300px] w-full">
                    <canvas id="trendChart"></canvas>
                    @if($monthlyData->isEmpty())
                        <div class="absolute inset-0 flex items-center justify-center bg-white/90 backdrop-blur-sm z-10 rounded-xl">
                            <div class="text-center">
                                <i class="fa-solid fa-chart-line text-3xl text-slate-300 mb-3"></i>
                                <p class="text-slate-500 font-bold">Insufficient Data for Trending</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Category Allocation & Burn Rings --}}
            <div class="lg:col-span-3 bg-white p-8 rounded-[2rem] shadow-sm border border-slate-200 flex flex-col">
                <h3 class="text-xl font-black text-slate-900 tracking-tight mb-8">Capital Allocation</h3>
                
                @if($categoryData->isEmpty())
                    <div class="flex-1 flex items-center justify-center">
                        <p class="text-slate-400 font-bold text-sm">No categorical data available.</p>
                    </div>
                @else
                    <div class="h-[220px] relative mb-8">
                        <canvas id="donutChart"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Top Cat</span>
                            <span class="text-lg font-black text-slate-900 truncate max-w-[100px]">{{ $topCategory }}</span>
                        </div>
                    </div>
                    
                    {{-- Custom HTML Legend & Budget Burn Rings --}}
                    <div class="space-y-4">
                        @php $i = 0; @endphp
                        @foreach($categoryData->sortDesc()->take(3) as $name => $val)
                            @php 
                                $pct = $total > 0 ? round(($val / $total) * 100) : 0; 
                                $color = $colors[$i % count($colors)];
                            @endphp
                            <div class="flex items-center justify-between group">
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 rounded-full shadow-sm" style="background-color: {{ $color }}"></div>
                                    <span class="text-sm font-bold text-slate-700">{{ $name }}</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-sm font-black text-slate-900">₹{{ number_format($val) }}</span>
                                    {{-- Mini Burn Bar --}}
                                    <div class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden border border-slate-200/50">
                                        <div class="h-full rounded-full transition-all duration-1000" style="width: {{ $pct }}%; background-color: {{ $color }}"></div>
                                    </div>
                                </div>
                            </div>
                            @php $i++; @endphp
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- ================= 5. DATA TABLE WITH SMART FILTERS ================= --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden flex flex-col">
            
            {{-- Filter Bar --}}
            <div class="p-4 sm:p-6 border-b border-slate-100 bg-slate-50/50">
                <form action="{{ route('user.expenses.index') ?? '#' }}" method="GET" class="flex flex-col lg:flex-row items-center gap-4">
                    
                    {{-- Magnetic Search --}}
                    <div class="relative w-full lg:flex-1 group">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                        <input type="text" name="search" x-model="search" value="{{ request('search') }}" placeholder="Search merchant, ID, or description..." 
                               class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 outline-none transition-all shadow-sm">
                    </div>
                    
                    <div class="flex flex-wrap sm:flex-nowrap items-center gap-3 w-full lg:w-auto">
                        <div class="relative w-full sm:w-auto">
                            <select name="category" class="w-full sm:w-auto px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 outline-none cursor-pointer appearance-none pr-10 shadow-sm transition-all">
                                <option value="">All Categories</option>
                                @foreach(['Food', 'Travel', 'Bills', 'Shopping', 'Health', 'Others'] as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none"></i>
                        </div>

                        <div class="relative w-full sm:w-auto">
                            <input type="date" name="from" value="{{ request('from') }}" class="w-full sm:w-auto px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 outline-none shadow-sm transition-all">
                        </div>
                        
                        <button type="submit" class="w-full sm:w-auto bg-slate-900 text-white px-6 py-3 rounded-xl font-bold shadow-md hover:bg-indigo-600 transition-colors focus:outline-none">
                            Filter
                        </button>
                        
                        @if(request()->anyFilled(['search', 'category', 'from']))
                            <a href="{{ route('user.expenses.index') ?? '#' }}" class="p-3 bg-rose-50 text-rose-500 rounded-xl border border-rose-100 hover:bg-rose-500 hover:text-white transition-colors" title="Clear Filters">
                                <i class="fa-solid fa-rotate-right"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto min-h-[400px]">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-white sticky top-0 z-10 shadow-sm">
                        <tr class="border-b border-slate-200">
                            <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest">Transaction Details</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest text-center">Category</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest text-center">Date</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest text-right">Amount</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($expenses as $expense)
                        <tr x-show="search === '' || ('{{ strtolower(addslashes($expense->title ?? '')) }} {{ strtolower(addslashes($expense->category ?? '')) }}').includes(search.toLowerCase())" 
                            class="group hover:bg-slate-50/80 transition-colors cursor-pointer">
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400 group-hover:bg-white group-hover:text-indigo-600 transition-colors shadow-sm">
                                        <i class="fa-solid fa-receipt"></i>
                                    </div>
                                    <div>
                                        <span class="block font-bold text-slate-900 group-hover:text-indigo-600 transition-colors text-sm">{{ $expense->title ?? 'Untitled Transaction' }}</span>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">ID: #EXP-{{ str_pad($expense->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                @php
                                    $catClass = 'bg-slate-50 text-slate-600 border-slate-200';
                                    if(in_array(strtolower($expense->category), ['food', 'dining'])) $catClass = 'bg-amber-50 text-amber-600 border-amber-200';
                                    if(in_array(strtolower($expense->category), ['travel', 'transport'])) $catClass = 'bg-sky-50 text-sky-600 border-sky-200';
                                    if(in_array(strtolower($expense->category), ['bills', 'utilities'])) $catClass = 'bg-rose-50 text-rose-600 border-rose-200';
                                    if(in_array(strtolower($expense->category), ['shopping'])) $catClass = 'bg-purple-50 text-purple-600 border-purple-200';
                                @endphp
                                <span class="px-3 py-1.5 rounded-lg border text-[10px] font-black uppercase tracking-widest shadow-sm {{ $catClass }}">
                                    {{ $expense->category ?? 'Misc' }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 text-center text-slate-500 font-bold text-xs font-mono">
                                {{ optional($expense->expense_date)->format('d M, Y') ?? 'Unknown Date' }}
                            </td>
                            
                            <td class="px-6 py-4 text-right">
                                <span class="text-base font-black text-slate-900">-₹{{ number_format($expense->amount ?? 0, 2) }}</span>
                            </td>
                            
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ Route::has('user.expenses.edit') ? route('user.expenses.edit', $expense->id) : '#' }}" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-300 flex items-center justify-center transition-all shadow-sm" title="Edit">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>
                                    <form action="{{ Route::has('user.expenses.destroy') ? route('user.expenses.destroy', $expense->id) : '#' }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete this expense?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-rose-600 hover:border-rose-300 flex items-center justify-center transition-all shadow-sm" title="Delete">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-24 text-center">
                                <div class="max-w-sm mx-auto">
                                    <div class="w-20 h-20 bg-slate-50 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                                        <i class="fa-solid fa-receipt text-slate-300 text-3xl"></i>
                                    </div>
                                    <h4 class="text-slate-900 font-black text-xl mb-2">Ledger is Empty</h4>
                                    <p class="text-slate-500 font-medium text-sm mb-6">You have no recorded expenses matching this criteria.</p>
                                    <a href="{{ route('user.expenses.create') ?? '#' }}" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-50 text-indigo-600 rounded-xl font-bold hover:bg-indigo-600 hover:text-white transition-colors border border-indigo-100">
                                        <i class="fa-solid fa-plus"></i> Create First Entry
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse

                        {{-- Alpine JS Empty State for Real-time Search --}}
                        <tr x-show="search !== '' && !Array.from(document.querySelectorAll('tbody tr:not([x-show])')).some(tr => tr.style.display !== 'none')" style="display: none;">
                            <td colspan="5" class="py-24 text-center">
                                <div class="w-16 h-16 bg-slate-50 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                    <i class="fa-solid fa-magnifying-glass text-slate-300 text-2xl"></i>
                                </div>
                                <p class="text-slate-600 font-bold">No results match your search: <span x-text="search" class="text-indigo-600 font-black"></span></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if(method_exists($expenses, 'hasPages') && $expenses->hasPages())
            <div class="px-8 py-5 bg-white border-t border-slate-100">
                {{ $expenses->appends(request()->query())->links('pagination::tailwind') }}
            </div>
            @endif
        </div>

    </div>
</div>

{{-- ================= MODALS & TOASTS ================= --}}

{{-- AI Smart Scanner Modal (New Fun) --}}
<div x-data="{ scanning: false, scanProgress: 0 }" 
     x-show="showScanner" 
     style="display: none;"
     class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    
    <div @click.away="!scanning ? showScanner = false : null" class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl border border-slate-200 p-8 transform transition-all">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-black text-slate-900">Scan Receipt</h2>
            <button x-show="!scanning" @click="showScanner = false" class="text-slate-400 hover:text-rose-500 transition-colors focus:outline-none"><i class="fa-solid fa-xmark text-xl"></i></button>
        </div>

        <div x-show="!scanning" class="border-2 border-dashed border-indigo-200 bg-indigo-50/50 rounded-3xl p-10 text-center hover:bg-indigo-50 transition-colors cursor-pointer relative group"
             @click="scanning = true; let inv = setInterval(() => { scanProgress += 10; if(scanProgress >= 100) { clearInterval(inv); setTimeout(() => { showScanner = false; scanProgress = 0; scanning = false; showToast('Receipt parsed! Redirecting to entry...'); }, 500); } }, 300)">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm text-indigo-500 group-hover:scale-110 transition-transform border border-indigo-100">
                <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
            </div>
            <h3 class="text-slate-900 font-bold text-lg mb-1">Click or Drag Image</h3>
            <p class="text-slate-500 text-sm font-medium">FinanceAI will automatically extract amount, date, and merchant.</p>
        </div>

        <div x-show="scanning" style="display: none;" class="py-10 text-center">
            <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-6 relative border border-indigo-100">
                <i class="fa-solid fa-receipt text-3xl text-indigo-600 relative z-10"></i>
                <div class="absolute w-full h-1 bg-emerald-400 rounded-full shadow-[0_0_10px_rgba(52,211,153,1)] z-20 animate-[scan_1s_ease-in-out_infinite_alternate]"></div>
            </div>
            <h3 class="text-xl font-black text-slate-900 mb-2">Vision Engine Active</h3>
            <p class="text-sm font-medium text-slate-500 mb-6">Extracting merchant metadata and numeric values...</p>
            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden border border-slate-200 shadow-inner">
                <div class="bg-indigo-500 h-2.5 rounded-full transition-all duration-300" :style="`width: ${scanProgress}%`"></div>
            </div>
        </div>
    </div>
</div>

{{-- Global Toast --}}
<div id="toast" class="fixed bottom-8 right-8 z-[120] bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 transform translate-y-24 opacity-0 transition-all duration-400 pointer-events-none border border-slate-800">
    <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 border border-emerald-500/30">
        <i class="fa-solid fa-check text-sm"></i>
    </div>
    <span id="toastMsg" class="text-sm font-bold tracking-wide">Action completed</span>
</div>

@endsection

@push('styles')
<style>
    /* CSS Scanner Animation */
    @keyframes scan {
        0% { top: 10%; }
        100% { top: 90%; }
    }
    
    /* Animations */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {

    // 1. AI Typewriter Effect (Robust & Safe)
    const aiBox = document.getElementById('ai-text');
    if(aiBox) {
        const rawText = aiBox.getAttribute('data-message');
        const formattedHtml = rawText.replace(/\*\*([^*]+)\*\*/g, '<strong class="text-slate-900 font-black">$1</strong>');
        
        // Strip tags for typing length calc, but we will inject innerHTML character by character to preserve formatting
        // For simplicity in this demo, we just fade it in gracefully to avoid broken HTML tags during a letter-by-letter type.
        aiBox.innerHTML = formattedHtml;
        aiBox.style.opacity = 0;
        aiBox.style.transform = 'translateY(10px)';
        aiBox.style.transition = 'all 1s ease-out';
        
        setTimeout(() => {
            aiBox.style.opacity = 1;
            aiBox.style.transform = 'translateY(0)';
        }, 300);
    }

    // 2. Number Counter Animation
    document.querySelectorAll('.kpi-animate').forEach(el => {
        let target = parseFloat(el.dataset.val || 0);
        let duration = 1500;
        let startTime = null;

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            let progress = Math.min((timestamp - startTime) / duration, 1);
            let eased = progress === 1 ? target : target * (1 - Math.pow(2, -10 * progress));
            
            el.innerText = '₹' + eased.toLocaleString('en-IN', { maximumFractionDigits: 0 });
            if (progress < 1) window.requestAnimationFrame(step);
        }
        if(target > 0) window.requestAnimationFrame(step);
    });

    // 3. FLAWLESS CHART.JS CONFIG (Pristine Light Mode)
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748b';

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { display: false },
            tooltip: {
                backgroundColor: '#0f172a',
                titleColor: '#fff',
                bodyColor: '#cbd5e1',
                padding: 12,
                cornerRadius: 8,
                displayColors: true,
                boxPadding: 4,
            }
        },
    };

    // A. Trend Line Chart
    const trendCanvas = document.getElementById('trendChart');
    const monthlyKeys = @json($monthlyData->keys() ?? []);
    const monthlyValues = @json($monthlyData->values() ?? []);

    if(trendCanvas && monthlyKeys.length > 0) {
        const ctx = trendCanvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 350);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.3)'); // Indigo 600
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthlyKeys,
                datasets: [{
                    label: 'Total Outflow',
                    data: monthlyValues,
                    borderColor: '#4f46e5',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4, // Smooth Bezier
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6
                }]
            },
            options: {
                ...commonOptions,
                interaction: { mode: 'index', intersect: false },
                scales: { 
                    y: { 
                        beginAtZero: true, 
                        grid: { color: '#f1f5f9', drawBorder: false },
                        ticks: { callback: v => '₹' + (v/1000) + 'k' }
                    }, 
                    x: { grid: { display: false }, ticks: { font: { weight: 'bold' } } } 
                }
            }
        });
    }

    // B. Category Doughnut Chart
    const donutCanvas = document.getElementById('donutChart');
    const catKeys = @json($categoryData->keys() ?? []);
    const catValues = @json($categoryData->values() ?? []);
    const chartColors = @json($colors);

    if(donutCanvas && catKeys.length > 0) {
        new Chart(donutCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: catKeys,
                datasets: [{
                    data: catValues,
                    backgroundColor: chartColors,
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 8
                }]
            },
            options: {
                ...commonOptions,
                cutout: '75%',
            }
        });
    }
});

// Global Toast UI
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