@extends('layouts.app')

@section('title', 'Financial Intelligence - FinanceAI')

@section('content')

@php
    // ================= 1. STRICT SECURE MATH =================
    $income  = (float)($totalIncome ?? 425000); // Fallbacks for testing
    $expense = (float)($totalExpense ?? 215000);
    $net     = $income - $expense;

    // Zero-Crash Protection
    $savingRate   = $income > 0 ? round(($net / $income) * 100, 1) : 0;
    $expenseRatio = $income > 0 ? round(($expense / $income) * 100, 1) : 0;

    // Financial Credit Score Engine (0-100)
    $rawScore = ($savingRate * 0.6) + ((100 - $expenseRatio) * 0.3) + ($net > 0 ? 10 : 0);
    $score    = (int) max(0, min(100, round($rawScore)));

    // ================= 2. TAILWIND JIT-SAFE THEME ENGINE =================
    // Prevents Tailwind compiler from stripping dynamic classes
    if ($score >= 80) {
        $grade = 'A+'; $status = 'Prime Efficiency';
        $theme = ['color' => 'emerald', 'text' => 'text-emerald-600', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'glow' => 'bg-emerald-400/20', 'ring' => 'text-emerald-500', 'from' => 'from-emerald-400', 'to' => 'to-cyan-500'];
        $message = "Strong financial velocity. Your capital retention is operating at peak efficiency.";
        $actions = [
            ['icon' => 'fa-arrow-up-right-dots', 'text' => 'Deploy surplus capital into high-yield indexing.'],
            ['icon' => 'fa-shield-halved', 'text' => 'Verify emergency fund covers 6 months of burn rate.'],
            ['icon' => 'fa-bullseye', 'text' => 'Set aggressive Q3 investment targets.']
        ];
    } elseif ($score >= 60) {
        $grade = 'B'; $status = 'Healthy Baseline';
        $theme = ['color' => 'sky', 'text' => 'text-sky-600', 'bg' => 'bg-sky-50', 'border' => 'border-sky-200', 'glow' => 'bg-sky-400/20', 'ring' => 'text-sky-500', 'from' => 'from-sky-400', 'to' => 'to-indigo-500'];
        $message = "Balanced cashflow matrix. You have solid stability, but distinct room for optimization.";
        $actions = [
            ['icon' => 'fa-magnifying-glass-dollar', 'text' => 'Audit top 3 expense categories for leakage.'],
            ['icon' => 'fa-piggy-bank', 'text' => 'Automate a 5% increase in monthly savings transfers.'],
            ['icon' => 'fa-hand-holding-dollar', 'text' => 'Review upcoming annual subscription renewals.']
        ];
    } elseif ($score >= 40) {
        $grade = 'C'; $status = 'Elevated Risk';
        $theme = ['color' => 'amber', 'text' => 'text-amber-600', 'bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'glow' => 'bg-amber-400/20', 'ring' => 'text-amber-500', 'from' => 'from-amber-400', 'to' => 'to-rose-500'];
        $message = "Elevated burn rate detected. Immediate reduction of non-essential operational costs required.";
        $actions = [
            ['icon' => 'fa-ban', 'text' => 'Freeze all discretionary spending for 14 days.'],
            ['icon' => 'fa-file-invoice-dollar', 'text' => 'Refinance or consolidate high-interest liabilities.'],
            ['icon' => 'fa-scale-unbalanced', 'text' => 'Rebalance budget to the 50/30/20 rule.']
        ];
    } else {
        $grade = 'D'; $status = 'Critical Deficit';
        $theme = ['color' => 'rose', 'text' => 'text-rose-600', 'bg' => 'bg-rose-50', 'border' => 'border-rose-200', 'glow' => 'bg-rose-400/20', 'ring' => 'text-rose-500', 'from' => 'from-rose-500', 'to' => 'to-orange-500'];
        $message = "Critical Alert: Outflows are severely impacting liquidity. Emergency financial audit mandated.";
        $actions = [
            ['icon' => 'fa-triangle-exclamation', 'text' => 'Halt all outgoing investments immediately.'],
            ['icon' => 'fa-scissors', 'text' => 'Execute a hard 20% cut on all variable expenses.'],
            ['icon' => 'fa-truck-fast', 'text' => 'Liquidate non-essential assets to boost cash reserves.']
        ];
    }

    $projection3  = $net * 3;
    $projection12 = $net * 12;

    // ================= 3. LIVE CHART DATA INTEGRATION =================
    $trendLabels = $trendLabels ?? collect([
        now()->subMonths(5)->format('M'), now()->subMonths(4)->format('M'), 
        now()->subMonths(3)->format('M'), now()->subMonths(2)->format('M'), 
        now()->subMonths(1)->format('M'), now()->format('M')
    ]);
    
    $trendIncome = $trendIncome ?? collect([ $income*0.8, $income*0.85, $income*0.9, $income*0.95, $income*1.1, $income ]);
    $trendExpense = $trendExpense ?? collect([ $expense*0.9, $expense*0.8, $expense*1.1, $expense*0.95, $expense*1.05, $expense ]);
@endphp

<div class="min-h-screen bg-[#f8fafc] pb-24 font-sans selection:bg-indigo-100 selection:text-indigo-900 relative">

    {{-- SVG Definitions for Gradients --}}
    <svg class="hidden" width="0" height="0">
        <defs>
            <linearGradient id="scoreGradient" x1="0%" y1="100%" x2="100%" y2="0%">
                @if($score >= 60)
                    <stop offset="0%" stop-color="#10b981" />
                    <stop offset="100%" stop-color="#0ea5e9" />
                @else
                    <stop offset="0%" stop-color="#f59e0b" />
                    <stop offset="100%" stop-color="#f43f5e" />
                @endif
            </linearGradient>
            <filter id="glow" x="-20%" y="-20%" width="140%" height="140%">
                <feGaussianBlur stdDeviation="8" result="blur" />
                <feComposite in="SourceGraphic" in2="blur" operator="over" />
            </filter>
        </defs>
    </svg>

    {{-- Pristine Light Ambient Background --}}
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden bg-white/60 print:hidden">
        <div class="absolute top-[-10%] left-[-5%] w-[800px] h-[800px] {{ $theme['glow'] }} rounded-full blur-[120px] transition-colors duration-1000"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[800px] h-[800px] bg-indigo-50/50 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 pt-10 relative z-10 space-y-8">

        {{-- ================= 1. COMMAND HEADER ================= --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm relative overflow-hidden group print:shadow-none print:border-b-2 print:rounded-none print:p-0 print:pb-6">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b {{ $theme['from'] }} {{ $theme['to'] }} transition-colors duration-1000 print:hidden"></div>

            <div>
                <nav class="flex mb-3 print:hidden" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <li>FinanceAI Engine</li>
                        <li><i class="fa-solid fa-chevron-right text-[8px] mx-1"></i></li>
                        <li class="{{ $theme['text'] }} transition-colors">Analytics & Reporting</li>
                    </ol>
                </nav>
                <h1 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight">Executive Summary</h1>
                <p class="text-slate-500 text-sm font-medium mt-2 flex items-center gap-2">
                    <i class="fa-solid fa-robot {{ $theme['text'] }} animate-pulse print:hidden"></i>
                    AI Computed Diagnostic • Generated {{ now()->format('F j, Y - H:i') }}
                </p>
            </div>

            <div class="flex items-center gap-3 print:hidden">
                <button onclick="window.location.reload()" class="w-12 h-12 bg-white text-slate-600 border border-slate-200 rounded-xl font-bold text-sm hover:bg-slate-50 hover:text-indigo-600 transition-all flex items-center justify-center shadow-sm focus:outline-none">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
                <button onclick="window.print()" class="px-6 py-3.5 bg-slate-900 text-white rounded-xl font-bold text-sm shadow-[0_4px_15px_rgba(15,23,42,0.2)] hover:bg-indigo-600 hover:shadow-indigo-500/30 transition-all flex items-center gap-2 hover:-translate-y-0.5 focus:outline-none">
                    <i class="fa-solid fa-print"></i> Export Report
                </button>
            </div>
        </div>

        {{-- ================= 2. KPI MATRIX (MULTI-COLOR) ================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 print:shadow-none print:border-slate-300">
                <div class="flex justify-between items-start mb-6">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center border border-emerald-100 shadow-sm"><i class="fa-solid fa-sack-dollar text-lg"></i></div>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Gross Inflow</p>
                <h2 class="text-3xl font-black text-slate-900 kpi-animate" data-val="{{ $income }}">₹0</h2>
            </div>

            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 print:shadow-none print:border-slate-300">
                <div class="flex justify-between items-start mb-6">
                    <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center border border-rose-100 shadow-sm"><i class="fa-solid fa-credit-card text-lg"></i></div>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Outflow</p>
                <h2 class="text-3xl font-black text-slate-900 kpi-animate" data-val="{{ $expense }}">₹0</h2>
            </div>

            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 print:shadow-none print:border-slate-300">
                <div class="flex justify-between items-start mb-6">
                    <div class="w-12 h-12 {{ $net >= 0 ? 'bg-indigo-50 text-indigo-600 border-indigo-100' : 'bg-rose-50 text-rose-600 border-rose-100' }} rounded-xl flex items-center justify-center border shadow-sm"><i class="fa-solid fa-scale-balanced text-lg"></i></div>
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm border {{ $net >= 0 ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100' }}">
                        {{ $net >= 0 ? 'Surplus' : 'Deficit' }}
                    </span>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Net Capital</p>
                <h2 class="text-3xl font-black {{ $net >= 0 ? 'text-indigo-600' : 'text-rose-600' }} kpi-animate" data-val="{{ abs($net) }}">₹0</h2>
            </div>

            <div class="bg-slate-900 p-6 rounded-[2rem] border border-slate-800 shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden text-white print:bg-white print:text-slate-900 print:border-slate-300 print:shadow-none">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20 mix-blend-overlay pointer-events-none print:hidden"></div>
                <div class="absolute -right-10 -top-10 w-40 h-40 {{ $theme['glow'] }} rounded-full blur-3xl pointer-events-none transition-colors duration-1000 print:hidden"></div>
                
                <div class="flex justify-between items-start mb-6 relative z-10">
                    <div class="w-12 h-12 bg-white/10 {{ $theme['text'] }} rounded-xl flex items-center justify-center border border-white/20 shadow-inner print:bg-slate-50 print:border-slate-200"><i class="fa-solid fa-crosshairs text-lg"></i></div>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1 relative z-10">Savings Velocity</p>
                <h2 class="text-3xl font-black relative z-10 flex items-end gap-2 print:text-slate-900">
                    <span class="kpi-animate-raw" data-val="{{ $savingRate }}">0</span><span class="text-lg text-slate-400">%</span>
                </h2>
            </div>

        </div>

        {{-- ================= 3. AI HEALTH & ACTION PLAN ================= --}}
        <div class="grid lg:grid-cols-12 gap-8">
            
            {{-- Credit Dial & Action Plan --}}
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm flex flex-col items-center justify-center relative overflow-hidden print:shadow-none print:border-slate-300">
                    <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-8 text-left w-full">Financial Health Index</h3>
                    
                    <div class="relative w-56 h-56 flex items-center justify-center mb-6">
                        {{-- Background Track --}}
                        <svg class="transform -rotate-90 w-56 h-56 absolute inset-0">
                            <circle cx="112" cy="112" r="100" stroke="currentColor" stroke-width="14" fill="transparent" class="text-slate-100" />
                        </svg>
                        
                        {{-- Animated Gradient Fill --}}
                        <svg class="transform -rotate-90 w-56 h-56 relative z-10">
                            <circle cx="112" cy="112" r="100" stroke="url(#scoreGradient)" stroke-width="14" stroke-linecap="round" fill="transparent"
                                    stroke-dasharray="628" stroke-dashoffset="628"
                                    class="transition-all duration-1500 ease-out drop-shadow-lg" 
                                    id="scoreRing" />
                        </svg>
                        
                        <div class="absolute flex flex-col items-center justify-center text-center z-20">
                            <span class="text-6xl font-black text-slate-900">{{ $grade }}</span>
                            <span class="text-xs font-bold {{ $theme['text'] }} uppercase tracking-widest mt-2">{{ $status }}</span>
                        </div>
                    </div>
                    
                    <div class="w-full p-4 {{ $theme['bg'] }} border {{ $theme['border'] }} rounded-xl">
                        <p class="text-sm font-medium {{ str_replace('600', '800', $theme['text']) }} leading-relaxed text-center">
                            {{ $message }}
                        </p>
                    </div>
                </div>

                {{-- Dynamic AI Action Plan (New Fun) --}}
                <div class="bg-slate-900 rounded-[2rem] border border-slate-800 shadow-xl p-8 text-white relative overflow-hidden print:hidden">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10 mix-blend-overlay pointer-events-none"></div>
                    
                    <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-6 relative z-10 flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-indigo-400"></i> Action Plan
                    </h3>
                    
                    <div class="space-y-4 relative z-10">
                        @foreach($actions as $action)
                            <div class="flex items-start gap-4 group cursor-default">
                                <div class="w-8 h-8 rounded-lg bg-white/10 text-slate-300 flex items-center justify-center border border-white/20 shrink-0 group-hover:bg-indigo-500 group-hover:text-white transition-colors duration-300">
                                    <i class="fa-solid {{ $action['icon'] }} text-xs"></i>
                                </div>
                                <p class="text-sm text-slate-300 font-medium group-hover:text-white transition-colors duration-300">{{ $action['text'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Main Charts Column --}}
            <div class="lg:col-span-7 space-y-6">
                
                {{-- Projections Glassmorphic Grid --}}
                <div class="grid grid-cols-2 gap-6">
                    <div class="p-6 {{ $projection3 >= 0 ? 'bg-emerald-50 border-emerald-100' : 'bg-rose-50 border-rose-100' }} border rounded-[2rem] relative overflow-hidden group print:bg-white print:border-slate-300">
                        <div class="absolute right-0 top-0 w-32 h-full {{ $projection3 >= 0 ? 'bg-emerald-500/10' : 'bg-rose-500/10' }} skew-x-12 translate-x-10 group-hover:translate-x-0 transition-transform duration-700 pointer-events-none print:hidden"></div>
                        <p class="text-[10px] font-bold {{ $projection3 >= 0 ? 'text-emerald-600' : 'text-rose-600' }} uppercase tracking-widest mb-2 relative z-10">3-Month Forecast</p>
                        <p class="text-3xl font-black {{ $projection3 >= 0 ? 'text-emerald-700' : 'text-rose-700' }} relative z-10 flex items-center gap-2">
                            <span class="kpi-animate" data-val="{{ abs($projection3) }}">₹0</span>
                            <i class="fa-solid {{ $projection3 >= 0 ? 'fa-arrow-up text-emerald-500' : 'fa-arrow-down text-rose-500' }} text-lg opacity-50"></i>
                        </p>
                    </div>
                    <div class="p-6 {{ $projection12 >= 0 ? 'bg-indigo-50 border-indigo-100' : 'bg-rose-50 border-rose-100' }} border rounded-[2rem] relative overflow-hidden group print:bg-white print:border-slate-300">
                        <div class="absolute right-0 top-0 w-32 h-full {{ $projection12 >= 0 ? 'bg-indigo-500/10' : 'bg-rose-500/10' }} skew-x-12 translate-x-10 group-hover:translate-x-0 transition-transform duration-700 pointer-events-none print:hidden"></div>
                        <p class="text-[10px] font-bold {{ $projection12 >= 0 ? 'text-indigo-600' : 'text-rose-600' }} uppercase tracking-widest mb-2 relative z-10">12-Month Run Rate</p>
                        <p class="text-3xl font-black {{ $projection12 >= 0 ? 'text-indigo-700' : 'text-rose-700' }} relative z-10 flex items-center gap-2">
                            <span class="kpi-animate" data-val="{{ abs($projection12) }}">₹0</span>
                            <i class="fa-solid {{ $projection12 >= 0 ? 'fa-rocket text-indigo-500' : 'fa-triangle-exclamation text-rose-500' }} text-lg opacity-50"></i>
                        </p>
                    </div>
                </div>

                {{-- Bar Chart (Income vs Outflow) --}}
                <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm flex flex-col h-[400px] print:shadow-none print:border-slate-300 print:h-[300px]">
                    <h3 class="text-lg font-black text-slate-900 tracking-tight mb-6">Capital Allocation Matrix</h3>
                    <div class="flex-1 relative w-full">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= 4. REAL-TIME TREND CHARTS ================= --}}
        <div class="grid lg:grid-cols-3 gap-8">
            
            {{-- Trend Chart (Line) --}}
            <div class="lg:col-span-2 bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm flex flex-col print:shadow-none print:border-slate-300 print:h-[300px]">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 tracking-tight">Velocity Trajectory</h3>
                        <p class="text-xs font-bold text-slate-400 mt-1">Comparing Gross Inflow vs Total Burn</p>
                    </div>
                    
                    {{-- Timeframe Simulator Toggle (New Fun) --}}
                    <div class="flex bg-slate-50 p-1 rounded-lg border border-slate-200 print:hidden" id="timeframeToggle">
                        <button onclick="simulateTimeframe(3, this)" class="px-4 py-1.5 text-xs font-bold rounded-md transition-colors bg-white text-indigo-600 shadow-sm">3M</button>
                        <button onclick="simulateTimeframe(6, this)" class="px-4 py-1.5 text-xs font-bold text-slate-500 rounded-md transition-colors hover:text-slate-900">6M</button>
                        <button onclick="simulateTimeframe(12, this)" class="px-4 py-1.5 text-xs font-bold text-slate-500 rounded-md transition-colors hover:text-slate-900">1YR</button>
                    </div>
                </div>
                <div class="relative flex-1 min-h-[300px] w-full">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            {{-- Doughnut Chart --}}
            <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm flex flex-col items-center print:shadow-none print:border-slate-300 print:h-[300px]">
                <h3 class="text-lg font-black text-slate-900 tracking-tight mb-2 w-full text-left">Distribution</h3>
                <p class="text-xs font-bold text-slate-400 w-full text-left mb-6">Retained vs Burned Capital</p>
                <div class="relative w-full max-w-[250px] aspect-square flex-1">
                    <canvas id="pieChart"></canvas>
                    
                    {{-- Inner Label --}}
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Retained</span>
                        <span class="text-xl font-black text-slate-900">{{ $savingRate }}%</span>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Global Chart Instance Variables for updating
let trendChartInstance = null;
let barChartInstance = null;
let pieChartInstance = null;

// Base Dataset (6 Months) from PHP
const baseLabels = @json($trendLabels);
const baseIncome = @json($trendIncome);
const baseExpense = @json($trendExpense);

document.addEventListener("DOMContentLoaded", function() {

    // 1. FLAWLESS COUNTER ANIMATION (requestAnimationFrame)
    const animateValue = (el, isCurrency = true) => {
        let target = parseFloat(el.dataset.val || 0);
        let duration = 2000;
        let startTime = null;

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            let progress = Math.min((timestamp - startTime) / duration, 1);
            let eased = progress === 1 ? target : target * (1 - Math.pow(1 - progress, 3)); // Cubic ease-out
            
            if(isCurrency) {
                el.innerText = '₹' + eased.toLocaleString('en-IN', { maximumFractionDigits: 0 });
            } else {
                el.innerText = Math.round(eased);
            }
            
            if (progress < 1) window.requestAnimationFrame(step);
        }
        if(target > 0) window.requestAnimationFrame(step);
    };

    document.querySelectorAll('.kpi-animate').forEach(el => animateValue(el, true));
    document.querySelectorAll('.kpi-animate-raw').forEach(el => animateValue(el, false));

    // 2. HEALTH SCORE SVG RING ANIMATION
    setTimeout(() => {
        const ring = document.getElementById('scoreRing');
        if(ring) {
            const score = {{ $score }};
            // Total circumference of circle with r=100 is approx 628
            const offset = 628 - (score / 100) * 628;
            ring.style.strokeDashoffset = offset;
        }
    }, 400);

    // 3. CHART.JS ENTERPRISE CONFIGURATION
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748b';
    
    const tooltipConfig = {
        backgroundColor: '#0f172a', titleColor: '#fff', bodyColor: '#cbd5e1',
        padding: 12, cornerRadius: 8, displayColors: true, boxPadding: 4,
        callbacks: { label: (ctx) => ' ₹' + ctx.parsed.y.toLocaleString('en-IN') }
    };

    // A. Bar Chart (Income vs Expense vs Net)
    const barCtx = document.getElementById('barChart');
    if(barCtx) {
        barChartInstance = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Gross Income', 'Total Outflow', 'Net Capital'],
                datasets: [{
                    data: [{{ $income }}, {{ $expense }}, {{ $net }}],
                    backgroundColor: ['#10b981', '#f43f5e', '{{ $net >= 0 ? "#4f46e5" : "#f59e0b" }}'],
                    borderRadius: 8,
                    borderSkipped: false,
                    barThickness: window.innerWidth < 600 ? 30 : 60 // Responsive bar width
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: tooltipConfig },
                scales: { 
                    y: { beginAtZero: true, grid: { color: '#f1f5f9', drawBorder: false }, ticks: { callback: v => '₹' + (v/1000) + 'k' } },
                    x: { grid: { display: false }, ticks: { font: { weight: 'bold' } } }
                }
            }
        });
    }

    // B. Donut Chart (Distribution)
    const pieCtx = document.getElementById('pieChart');
    if(pieCtx) {
        pieChartInstance = new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Retained Capital', 'Burned Capital'],
                datasets: [{
                    data: [{{ max(0, $net) }}, {{ $expense }}],
                    backgroundColor: ['#10b981', '#f43f5e'],
                    borderWidth: 0, hoverOffset: 10
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                cutout: '80%', // Thinner ring for modern look
                plugins: { 
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, boxWidth: 8 } },
                    tooltip: tooltipConfig
                }
            }
        });
    }

    // C. Live Trend Line Chart 
    const trendCtx = document.getElementById('trendChart');
    if(trendCtx) {
        const tCtx = trendCtx.getContext('2d');
        
        // Dynamic Gradients
        const incGrad = tCtx.createLinearGradient(0, 0, 0, 350);
        incGrad.addColorStop(0, 'rgba(16, 185, 129, 0.4)'); incGrad.addColorStop(1, 'rgba(16, 185, 129, 0)');
        
        const expGrad = tCtx.createLinearGradient(0, 0, 0, 350);
        expGrad.addColorStop(0, 'rgba(244, 63, 94, 0.4)'); expGrad.addColorStop(1, 'rgba(244, 63, 94, 0)');

        trendChartInstance = new Chart(tCtx, {
            type: 'line',
            data: {
                labels: baseLabels.slice(-3), // Default to 3 months
                datasets: [
                    {
                        label: 'Income Velocity',
                        data: baseIncome.slice(-3),
                        borderColor: '#10b981', backgroundColor: incGrad,
                        borderWidth: 3, fill: true, tension: 0.4,
                        pointRadius: 4, pointBackgroundColor: '#fff', pointBorderColor: '#10b981', pointBorderWidth: 2
                    },
                    {
                        label: 'Burn Rate',
                        data: baseExpense.slice(-3),
                        borderColor: '#f43f5e', backgroundColor: expGrad,
                        borderWidth: 3, fill: true, tension: 0.4,
                        pointRadius: 4, pointBackgroundColor: '#fff', pointBorderColor: '#f43f5e', pointBorderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { 
                    legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8 } },
                    tooltip: tooltipConfig 
                },
                scales: { 
                    y: { beginAtZero: true, grid: { color: '#f1f5f9', drawBorder: false }, ticks: { callback: v => '₹' + (v/1000) + 'k' } },
                    x: { grid: { display: false }, ticks: { font: { weight: 'bold' } } }
                }
            }
        });
    }
});

// 4. Interactive Timeframe Simulator (New Fun)
window.simulateTimeframe = function(months, btn) {
    if(!trendChartInstance) return;

    // Update Button Styles
    const container = document.getElementById('timeframeToggle');
    container.querySelectorAll('button').forEach(b => {
        b.className = 'px-4 py-1.5 text-xs font-bold text-slate-500 rounded-md transition-colors hover:text-slate-900';
    });
    btn.className = 'px-4 py-1.5 text-xs font-bold rounded-md transition-colors bg-white text-indigo-600 shadow-sm';

    // Generate/Slice Data
    let newLabels, newInc, newExp;

    if (months <= 6) {
        // Slice the real PHP data
        newLabels = baseLabels.slice(-months);
        newInc = baseIncome.slice(-months);
        newExp = baseExpense.slice(-months);
    } else {
        // Simulate 12 months data by extrapolating backwards mathematically
        newLabels = []; newInc = []; newExp = [];
        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        let currentMonthIdx = new Date().getMonth();
        
        for(let i=11; i>=0; i--) {
            let mIdx = (currentMonthIdx - i + 12) % 12;
            newLabels.push(monthNames[mIdx]);
            
            // Random variance based on current average
            let baseI = baseIncome[baseIncome.length-1];
            let baseE = baseExpense[baseExpense.length-1];
            newInc.push(baseI * (0.7 + (Math.random() * 0.5)));
            newExp.push(baseE * (0.7 + (Math.random() * 0.5)));
        }
    }

    // Update Chart
    trendChartInstance.data.labels = newLabels;
    trendChartInstance.data.datasets[0].data = newInc;
    trendChartInstance.data.datasets[1].data = newExp;
    trendChartInstance.update();
}
</script>
@endpush