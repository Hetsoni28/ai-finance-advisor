@extends('layouts.app')

@section('title', 'Inbound Capital - FinanceAI')

@section('content')

@php
    // ================= 1. SAFE DATA PREPARATION =================
    $incomes = $incomes ?? collect();
    $stats = $stats ?? [];

    $total = (float)($stats['total'] ?? $incomes->sum('amount'));
    $currentMonth = (float)($stats['currentMonth'] ?? $incomes->where('income_date', '>=', now()->startOfMonth())->sum('amount'));
    $average = (float)($stats['average'] ?? ($incomes->count() > 0 ? $total / $incomes->count() : 0));
    
    // Predictive Math: Annual Run Rate (ARR) & Taxes (New Fun)
    $projectedARR = $currentMonth > 0 ? $currentMonth * 12 : ($total > 0 ? ($total / max(1, $incomes->count())) * 12 : 0);
    $estimatedTax = $total * 0.15; // 15% Simulated Tax Bracket

    // Chart Grouping Logic
    $monthly = collect($incomes->items() ?? $incomes)
        ->groupBy(fn($i) => $i->income_date ? \Carbon\Carbon::parse($i->income_date)->format('M Y') : \Carbon\Carbon::parse($i->created_at)->format('M Y'))
        ->map(fn($group) => $group->sum('amount'))
        ->take(6);

    $chartLabels = $monthly->keys()->values();
    $chartValues = $monthly->values();

    // Income Diversification Logic
    $sourceData = collect($incomes->items() ?? $incomes)->groupBy('source')->map->sum('amount')->sortDesc()->take(5);
    
    // Multi-Color Palette for Income Streams
    $colors = [
        ['hex' => '#10b981', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'border-emerald-200', 'icon' => 'fa-building-columns'],
        ['hex' => '#0ea5e9', 'bg' => 'bg-sky-50', 'text' => 'text-sky-600', 'border' => 'border-sky-200', 'icon' => 'fa-laptop-code'],
        ['hex' => '#8b5cf6', 'bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'border' => 'border-purple-200', 'icon' => 'fa-arrow-trend-up'],
        ['hex' => '#f59e0b', 'bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'border' => 'border-amber-200', 'icon' => 'fa-store'],
        ['hex' => '#f43f5e', 'bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'border' => 'border-rose-200', 'icon' => 'fa-gift'],
    ];
@endphp

<div class="min-h-screen bg-[#f8fafc] pb-24 font-sans selection:bg-emerald-100 selection:text-emerald-900 relative"
     x-data="{ search: '', activeMenu: null }">

    {{-- Pristine Light Ambient Background --}}
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden bg-white/40">
        <div class="absolute top-[-10%] left-[-5%] w-[800px] h-[800px] bg-emerald-50/70 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[800px] h-[800px] bg-cyan-50/50 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 pt-10 relative z-10 space-y-8">

        {{-- ================= 1. COMMAND HEADER ================= --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 bg-white p-8 rounded-[2rem] border border-slate-200 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.02)] relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-emerald-400 to-cyan-500"></div>

            <div>
                <nav class="flex mb-3" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <li>FinanceAI Engine</li>
                        <li><i class="fa-solid fa-chevron-right text-[8px] mx-1"></i></li>
                        <li class="text-emerald-600">Inbound Capital</li>
                    </ol>
                </nav>
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight">
                    Income <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-500 to-cyan-500">Intelligence</span>
                </h1>
                <p class="text-slate-500 text-sm font-medium mt-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.8)]"></span>
                    Monitor, optimize, and forecast your personal revenue streams.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button onclick="simulateWireTransfer()" class="px-5 py-3.5 bg-slate-50 text-slate-600 border border-slate-200 rounded-xl font-bold text-sm hover:bg-white hover:text-emerald-600 hover:border-emerald-300 hover:shadow-sm transition-all flex items-center gap-2 focus:outline-none">
                    <i class="fa-solid fa-building-columns"></i> Sync Bank
                </button>
                
                {{-- 🚨 FIX: Updated route to user.incomes.create --}}
                <a href="{{ route('user.incomes.create') ?? '#' }}" class="px-6 py-3.5 bg-slate-900 text-white rounded-xl font-bold text-sm shadow-[0_4px_15px_rgba(15,23,42,0.2)] hover:bg-emerald-600 hover:shadow-emerald-500/30 transition-all flex items-center gap-2 hover:-translate-y-0.5">
                    <i class="fa-solid fa-plus"></i> Add Income
                </a>
            </div>
        </div>

        {{-- ================= 2. KPI GRID (Multi-Color Accents) ================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
            
            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex justify-between items-start mb-6">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center border border-emerald-100 shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-sack-dollar text-lg"></i>
                    </div>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Gross Cumulative</p>
                <h2 class="text-3xl font-black text-slate-900 kpi-animate" id="kpiTotal" data-val="{{ $total }}">₹0</h2>
            </div>

            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex justify-between items-start mb-6">
                    <div class="w-12 h-12 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center border border-sky-100 shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fa-regular fa-calendar-days text-lg"></i>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm border bg-emerald-50 text-emerald-600 border-emerald-100">Live</span>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Current Month</p>
                <h2 class="text-2xl font-black text-slate-900 kpi-animate" id="kpiMonth" data-val="{{ $currentMonth }}">₹0</h2>
            </div>

            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex justify-between items-start mb-6">
                    <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center border border-purple-100 shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-money-bill-trend-up text-lg"></i>
                    </div>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Average Deposit</p>
                <h2 class="text-2xl font-black text-slate-900 kpi-animate" data-val="{{ $average }}">₹0</h2>
            </div>

            {{-- New ARR Engine Card --}}
            <div class="bg-slate-900 p-6 rounded-[2rem] border border-slate-800 shadow-xl hover:-translate-y-1 transition-all duration-300 group relative overflow-hidden text-white flex flex-col justify-center">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20 mix-blend-overlay pointer-events-none"></div>
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-500/20 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700 pointer-events-none"></div>
                
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fa-solid fa-chart-line text-emerald-400"></i>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Est. Annual Run Rate</p>
                        </div>
                        <h2 class="text-2xl font-black text-white kpi-animate" data-val="{{ $projectedARR }}">₹0</h2>
                    </div>
                </div>
            </div>

            {{-- Est. Tax Liability (New Fun) --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex justify-between items-start mb-6">
                    <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center border border-rose-100 shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-scale-balanced text-lg"></i>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm border bg-slate-50 text-slate-500 border-slate-200">15% Bracket</span>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Est. Tax Liability</p>
                <h2 class="text-2xl font-black text-rose-600 kpi-animate" id="kpiTax" data-val="{{ $estimatedTax }}">₹0</h2>
            </div>

        </div>

        {{-- ================= 3. CHARTS & DIVERSIFICATION ================= --}}
        <div class="grid lg:grid-cols-7 gap-6">
            
            {{-- Revenue Trajectory Line Chart --}}
            <div class="lg:col-span-4 bg-white p-8 rounded-[2rem] shadow-sm border border-slate-200 flex flex-col relative overflow-hidden">
                <div class="flex items-center justify-between mb-8 relative z-10">
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">Revenue Trajectory</h3>
                    <span class="px-3 py-1 bg-slate-50 border border-slate-200 rounded-lg text-[10px] font-bold text-slate-500 uppercase tracking-widest">Last 6 Months</span>
                </div>
                <div class="flex-1 relative min-h-[300px] w-full z-10">
                    <canvas id="incomeChart"></canvas>
                    @if($monthly->isEmpty())
                        <div class="absolute inset-0 flex items-center justify-center bg-white/90 backdrop-blur-sm z-20 rounded-xl">
                            <div class="text-center">
                                <div class="w-16 h-16 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center mx-auto mb-3">
                                    <i class="fa-solid fa-chart-line text-2xl text-slate-300"></i>
                                </div>
                                <p class="text-slate-500 font-bold">Insufficient Data for Trending</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Capital Streams (Multi-Color Diversification) --}}
            <div class="lg:col-span-3 bg-white p-8 rounded-[2rem] shadow-sm border border-slate-200 flex flex-col">
                <h3 class="text-xl font-black text-slate-900 tracking-tight mb-2">Capital Streams</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-8">Income Diversification Matrix</p>
                
                @if($sourceData->isEmpty())
                    <div class="flex-1 flex items-center justify-center">
                        <p class="text-slate-400 font-bold text-sm">No categorical data available.</p>
                    </div>
                @else
                    <div class="space-y-6 flex-1 flex flex-col justify-center">
                        @php $i = 0; @endphp
                        @foreach($sourceData as $name => $val)
                            @php 
                                $pct = $total > 0 ? round(($val / $total) * 100) : 0; 
                                $theme = $colors[$i % count($colors)];
                            @endphp
                            <div class="group">
                                <div class="flex justify-between items-end mb-3">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center border {{ $theme['bg'] }} {{ $theme['text'] }} {{ $theme['border'] }} shadow-sm group-hover:scale-110 transition-transform">
                                            <i class="fa-solid {{ $theme['icon'] }}"></i>
                                        </div>
                                        <div>
                                            <span class="block text-sm font-bold text-slate-900">{{ $name ?: 'Unknown Source' }}</span>
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $pct }}% of Portfolio</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="block text-base font-black text-slate-900">₹{{ number_format($val) }}</span>
                                    </div>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden border border-slate-200/50 shadow-inner">
                                    <div class="h-full rounded-full transition-all duration-1000 ease-out" style="width: {{ $pct }}%; background-color: {{ $theme['hex'] }}"></div>
                                </div>
                            </div>
                            @php $i++; @endphp
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- ================= 4. DATA TABLE WITH SMART FILTERS ================= --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden flex flex-col min-h-[500px]">
            
            {{-- Filter Bar --}}
            <div class="p-4 sm:p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col lg:flex-row items-center justify-between gap-4">
                
                {{-- Alpine.js Real-Time Search --}}
                <div class="relative w-full lg:w-[400px] group">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-500 transition-colors"></i>
                    <input type="text" x-model="search" placeholder="Search source, description, or amount..." 
                           class="w-full pl-12 pr-4 py-3.5 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-400 outline-none transition-all shadow-sm">
                </div>
                
                <div class="flex items-center gap-3 w-full lg:w-auto">
                    {{-- 🚨 FIX: Ensured export routes point to user.incomes --}}
                    @if(Route::has('user.incomes.export.csv'))
                    <a href="{{ route('user.incomes.export.csv') }}" class="w-full sm:w-auto bg-white border border-slate-200 text-slate-600 px-5 py-3.5 rounded-xl font-bold text-sm shadow-sm hover:bg-slate-50 hover:text-emerald-600 transition-colors focus:outline-none flex items-center justify-center gap-2">
                        <i class="fa-solid fa-file-csv"></i> CSV Export
                    </a>
                    @endif
                </div>
            </div>

            {{-- Table Wrapper: pb-32 prevents dropdowns from being clipped by overflow-x-auto --}}
            <div class="overflow-x-auto flex-1 pb-32">
                <table class="w-full text-left border-collapse" id="incomeTable">
                    <thead class="bg-white sticky top-0 z-10 shadow-sm">
                        <tr class="border-b border-slate-200">
                            <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest cursor-pointer hover:text-emerald-600 transition-colors" onclick="sortTable(0)">
                                Income Source <i class="fa-solid fa-sort ml-1"></i>
                            </th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest text-center cursor-pointer hover:text-emerald-600 transition-colors" onclick="sortTable(1)">
                                Date <i class="fa-solid fa-sort ml-1"></i>
                            </th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest text-center">Status</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest text-right cursor-pointer hover:text-emerald-600 transition-colors" onclick="sortTable(3)">
                                Amount <i class="fa-solid fa-sort ml-1"></i>
                            </th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($incomes as $income)
                        <tr x-show="search === '' || ('{{ strtolower(addslashes($income->source ?? '')) }} {{ $income->amount }}').includes(search.toLowerCase())" 
                            class="group hover:bg-slate-50/80 transition-colors"
                            :class="{ 'bg-emerald-50/50': activeMenu === {{ $income->id }} }">
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-500 group-hover:bg-emerald-500 group-hover:text-white transition-colors shadow-sm">
                                        <i class="fa-solid fa-money-bill-wave"></i>
                                    </div>
                                    <div>
                                        <span class="block font-bold text-slate-900 text-sm">{{ $income->source ?? 'Direct Deposit' }}</span>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">ID: #INC-{{ str_pad($income->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 text-center text-slate-500 font-bold text-xs font-mono">
                                {{ optional($income->income_date ?? $income->created_at)->format('d M Y') ?? 'Unknown Date' }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest border border-emerald-100 shadow-sm">
                                    <i class="fa-solid fa-check"></i> Cleared
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 text-right">
                                <span class="text-base font-black text-emerald-600">+₹{{ number_format($income->amount ?? 0, 2) }}</span>
                            </td>
                            
                            {{-- 🚨 FLAWLESS CONTEXT MENU (Uses global activeMenu state to prevent clipping & sorting detachments) --}}
                            <td class="px-6 py-4 text-right relative">
                                <button @click.stop="activeMenu = (activeMenu === {{ $income->id }} ? null : {{ $income->id }})" 
                                        class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-emerald-600 hover:border-emerald-300 flex items-center justify-center transition-all shadow-sm focus:outline-none ml-auto"
                                        :class="{ 'ring-2 ring-emerald-500 border-emerald-500 text-emerald-600': activeMenu === {{ $income->id }} }">
                                    <i class="fa-solid fa-ellipsis"></i>
                                </button>
                                
                                <div x-show="activeMenu === {{ $income->id }}" style="display: none;" 
                                     @click.outside="if (activeMenu === {{ $income->id }}) activeMenu = null"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-10 top-10 w-48 bg-white border border-slate-200 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.1)] z-[100] py-2 text-left">
                                    
                                    {{-- 🚨 FIX: Updated route to user.incomes.edit --}}
                                    <a href="{{ Route::has('user.incomes.edit') ? route('user.incomes.edit', $income->id) : '#' }}" class="block px-4 py-2 text-sm font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-600 transition-colors">
                                        <i class="fa-solid fa-pen w-4 text-center mr-2 text-slate-400"></i> Edit Entry
                                    </a>
                                    
                                    <div class="h-px bg-slate-100 my-1"></div>
                                    
                                    {{-- 🚨 FIX: Updated route to user.incomes.destroy --}}
                                    <form action="{{ Route::has('user.incomes.destroy') ? route('user.incomes.destroy', $income->id) : '#' }}" method="POST" class="block" onsubmit="return confirm('Permanently delete this income record?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm font-bold text-rose-600 hover:bg-rose-50 transition-colors">
                                            <i class="fa-solid fa-trash-can w-4 text-center mr-2 text-rose-400"></i> Delete Record
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
                                        <i class="fa-solid fa-money-bill-wave text-slate-300 text-3xl"></i>
                                    </div>
                                    <h4 class="text-slate-900 font-black text-xl mb-2">Ledger is Empty</h4>
                                    <p class="text-slate-500 font-medium text-sm mb-6">You have no recorded inbound capital. Start by adding an income source.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse

                        {{-- Alpine JS Empty State for Real-time Search --}}
                        <tr x-show="search !== '' && !Array.from(document.querySelectorAll('#incomeTable tbody tr:not([style*=\'display: none\'])')).length" style="display: none;">
                            <td colspan="5" class="py-24 text-center">
                                <div class="w-16 h-16 bg-slate-50 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                    <i class="fa-solid fa-magnifying-glass text-slate-300 text-2xl"></i>
                                </div>
                                <p class="text-slate-600 font-bold">No results match your search: <span x-text="search" class="text-emerald-600 font-black"></span></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if(method_exists($incomes, 'hasPages') && $incomes->hasPages())
            <div class="px-8 py-5 bg-white border-t border-slate-100">
                {{ $incomes->appends(request()->query())->links('pagination::tailwind') }}
            </div>
            @endif
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {

    // 1. Number Counter Animation
    document.querySelectorAll('.kpi-animate').forEach(el => {
        let target = parseFloat(el.dataset.val || 0);
        let duration = 2000;
        let startTime = null;

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            let progress = Math.min((timestamp - startTime) / duration, 1);
            let eased = progress === 1 ? target : target * (1 - Math.pow(1 - progress, 3));
            
            el.innerText = '₹' + eased.toLocaleString('en-IN', { maximumFractionDigits: 0 });
            if (progress < 1) window.requestAnimationFrame(step);
        }
        if(target > 0) window.requestAnimationFrame(step);
    });

    // 2. FLAWLESS CHART.JS CONFIG (Pristine Emerald Theme)
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748b';

    const trendCanvas = document.getElementById('incomeChart');
    const monthlyKeys = @json($chartLabels);
    const monthlyValues = @json($chartValues);

    if(trendCanvas && monthlyKeys.length > 0) {
        const ctx = trendCanvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)'); // Emerald 500
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthlyKeys,
                datasets: [{
                    label: 'Gross Income',
                    data: monthlyValues,
                    borderColor: '#10b981',
                    backgroundColor: gradient,
                    borderWidth: 4,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#10b981',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
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
                        callbacks: { label: (ctx) => ' ₹' + ctx.parsed.y.toLocaleString('en-IN') }
                    }
                },
                scales: { 
                    y: { beginAtZero: true, grid: { color: '#f1f5f9', drawBorder: false }, ticks: { callback: v => '₹' + (v/1000) + 'k' } }, 
                    x: { grid: { display: false }, ticks: { font: { weight: 'bold' } } } 
                }
            }
        });
    }
});

// 3. Simulated Wire Transfer Animation
window.simulateWireTransfer = function() {
    showToast("Authenticating banking API...");
    
    setTimeout(() => {
        showToast("Wire transfer of ₹12,500 cleared!");
        
        const totalEl = document.getElementById('kpiTotal');
        const monthEl = document.getElementById('kpiMonth');
        const taxEl = document.getElementById('kpiTax');
        
        [totalEl, monthEl].forEach(el => {
            if(!el) return;
            const currentVal = parseFloat(el.dataset.val) || 0;
            const newVal = currentVal + 12500;
            el.dataset.val = newVal;
            
            let start = currentVal;
            let diff = 12500;
            let startT = null;
            
            function animate(t) {
                if(!startT) startT = t;
                let p = Math.min((t - startT) / 1000, 1);
                let v = start + (diff * p);
                el.innerText = '₹' + v.toLocaleString('en-IN', { maximumFractionDigits: 0 });
                if(p < 1) requestAnimationFrame(animate);
            }
            requestAnimationFrame(animate);
        });

        // Update Tax Predictor
        if(taxEl) {
            const taxCurrent = parseFloat(taxEl.dataset.val) || 0;
            const taxNew = taxCurrent + (12500 * 0.15);
            taxEl.innerText = '₹' + taxNew.toLocaleString('en-IN', { maximumFractionDigits: 0 });
            taxEl.dataset.val = taxNew;
        }

    }, 1500);
}

// Global Toast UI
window.showToast = function(msg) {
    const toast = document.getElementById('toast');
    document.getElementById('toastMsg').innerText = msg;
    
    toast.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
    setTimeout(() => {
        toast.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
    }, 3000);
}

// 4. Vanilla JS Table Sorting
let currentSortCol = null;
let currentSortAsc = true;

window.sortTable = function(colIndex) {
    const tbody = document.querySelector('#incomeTable tbody');
    const rows = Array.from(tbody.querySelectorAll('tr:not([style*=\'display: none\']):not([x-show])')).concat(
        Array.from(tbody.querySelectorAll('tr[class*="group"]'))
    );
    
    currentSortAsc = currentSortCol === colIndex ? !currentSortAsc : true;
    currentSortCol = colIndex;

    rows.sort((a, b) => {
        let valA = a.children[colIndex].innerText.trim();
        let valB = b.children[colIndex].innerText.trim();
        
        if (colIndex === 3) {
            valA = parseFloat(valA.replace(/[^0-9.-]+/g,"")) || 0;
            valB = parseFloat(valB.replace(/[^0-9.-]+/g,"")) || 0;
        } else if (colIndex === 1) {
            valA = new Date(valA).getTime() || 0;
            valB = new Date(valB).getTime() || 0;
        }

        if (valA < valB) return currentSortAsc ? -1 : 1;
        if (valA > valB) return currentSortAsc ? 1 : -1;
        return 0;
    });

    rows.forEach(row => tbody.appendChild(row));
}
</script>
@endpush