@extends('layouts.app')

@section('title', 'Log Inbound Capital - FinanceAI')

@section('content')

@php
    // ================= SAFE DATA PREPARATION =================
    // Categories mapped to modern icons and multi-color pastel themes
    $categories = [
        ['id' => 'Salary', 'icon' => 'fa-building-columns', 'color' => 'text-emerald-500', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200'],
        ['id' => 'Freelance', 'icon' => 'fa-laptop-code', 'color' => 'text-sky-500', 'bg' => 'bg-sky-50', 'border' => 'border-sky-200'],
        ['id' => 'Investments', 'icon' => 'fa-arrow-trend-up', 'color' => 'text-purple-500', 'bg' => 'bg-purple-50', 'border' => 'border-purple-200'],
        ['id' => 'Business', 'icon' => 'fa-store', 'color' => 'text-amber-500', 'bg' => 'bg-amber-50', 'border' => 'border-amber-200'],
        ['id' => 'Gifts', 'icon' => 'fa-gift', 'color' => 'text-rose-500', 'bg' => 'bg-rose-50', 'border' => 'border-rose-200'],
        ['id' => 'Others', 'icon' => 'fa-box-open', 'color' => 'text-slate-500', 'bg' => 'bg-slate-50', 'border' => 'border-slate-200'],
    ];

    $families = $families ?? collect([]); // Safe fallback
    $recentIncome = $recentIncome ?? collect([]); // Safe fallback
    $today = now()->format('Y-m-d');
@endphp

<div class="min-h-screen bg-[#f8fafc] pb-24 font-sans selection:bg-emerald-100 selection:text-emerald-900 relative"
     x-data="incomeForm()">
    
    {{-- Pristine Ambient Background --}}
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden bg-white/40">
        <div class="absolute top-[-10%] left-[-5%] w-[800px] h-[800px] bg-emerald-50/70 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[800px] h-[800px] bg-cyan-50/40 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 pt-10 relative z-10 space-y-8">

        {{-- ================= 1. COMMAND HEADER ================= --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-emerald-400 to-cyan-500"></div>

            <div>
                <nav class="flex mb-3" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <li><a href="{{ route('user.incomes.index') ?? '#' }}" class="hover:text-emerald-600 transition-colors">Inbound Capital</a></li>
                        <li><i class="fa-solid fa-chevron-right text-[8px] mx-1"></i></li>
                        <li class="text-emerald-600">Log Deposit</li>
                    </ol>
                </nav>
                <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">Record Income</h1>
                <p class="text-slate-500 text-sm font-medium mt-2 flex items-center gap-2">
                    Secure • Intelligent • Real-time Synced
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button @click="autoFillSimulate()" type="button" class="px-5 py-3.5 bg-slate-50 text-emerald-600 border border-emerald-100 rounded-xl font-bold text-sm hover:bg-emerald-50 hover:border-emerald-300 transition-all flex items-center gap-2 focus:outline-none shadow-sm">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Auto-Fill via AI
                </button>
                <a href="{{ route('user.incomes.index') ?? '#' }}" class="px-5 py-3.5 bg-white text-slate-600 border border-slate-200 rounded-xl font-bold text-sm hover:bg-slate-50 hover:text-emerald-600 transition-all shadow-sm focus:outline-none flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i> Cancel
                </a>
            </div>
        </div>

        {{-- ALERTS --}}
        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-5 rounded-[1.5rem] shadow-sm flex items-start gap-4 animate-fade-in-up">
                <i class="fa-solid fa-circle-exclamation text-xl mt-0.5"></i>
                <div>
                    <h3 class="font-black text-sm uppercase tracking-widest mb-2">Validation Failed</h3>
                    <ul class="space-y-1 text-sm font-medium">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- ================= 2. SPLIT LAYOUT (FORM & PREVIEW) ================= --}}
        <div class="grid lg:grid-cols-12 gap-8">
            
            {{-- LEFT COLUMN: DATA ENTRY FORM --}}
            <div class="lg:col-span-8">
                <form method="POST" action="{{ route('user.incomes.store') ?? '#' }}" id="incomeForm" @submit="isSubmitting = true" class="space-y-6">
                    @csrf

                    {{-- Section 1: Financial Value --}}
                    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8 sm:p-10 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-emerald-50/50 rounded-full blur-3xl group-focus-within:bg-emerald-100 transition-colors duration-500 pointer-events-none"></div>
                        
                        <label class="block text-xs font-black text-emerald-600 uppercase tracking-widest mb-4">Total Amount (INR)</label>
                        
                        <div class="relative flex items-center">
                            <span class="absolute left-0 text-4xl sm:text-5xl font-black text-slate-300 pointer-events-none">₹</span>
                            <input type="number" name="amount" x-model.number="amount" min="0.01" step="0.01" required
                                   class="w-full pl-12 sm:pl-16 py-2 bg-transparent border-none text-4xl sm:text-5xl font-black text-slate-900 focus:ring-0 outline-none p-0 m-0 placeholder-slate-200" placeholder="0.00">
                        </div>
                        
                        <div class="h-px w-full bg-slate-100 my-6 group-focus-within:bg-emerald-200 transition-colors duration-300"></div>

                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest mr-2">Quick Add:</span>
                            <button type="button" @click="addAmount(5000)" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:text-emerald-600 hover:border-emerald-300 hover:bg-emerald-50 transition-all shadow-sm focus:outline-none">+5,000</button>
                            <button type="button" @click="addAmount(10000)" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:text-emerald-600 hover:border-emerald-300 hover:bg-emerald-50 transition-all shadow-sm focus:outline-none">+10,000</button>
                            <button type="button" @click="addAmount(50000)" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:text-emerald-600 hover:border-emerald-300 hover:bg-emerald-50 transition-all shadow-sm focus:outline-none">+50,000</button>
                            <button type="button" @click="amount = ''" class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-500 hover:text-slate-800 transition-all ml-auto focus:outline-none"><i class="fa-solid fa-rotate-left mr-1"></i> Clear</button>
                        </div>
                    </div>

                    {{-- Section 2: Transaction Details --}}
                    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8 sm:p-10 space-y-8">
                        
                        {{-- Source Input --}}
                        <div>
                            <div class="flex justify-between items-end mb-3">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Income Source / Payer</label>
                                <span class="text-[10px] font-bold text-slate-300" x-text="`${source.length} / 60`">0 / 60</span>
                            </div>
                            <input type="text" name="source" x-model="source" maxlength="60" required
                                   class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-xl text-base font-bold text-slate-900 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-400 outline-none transition-all shadow-inner placeholder-slate-300" placeholder="e.g. Acme Corp Salary, Upwork, Dividend">
                        </div>

                        <div class="grid sm:grid-cols-2 gap-8">
                            {{-- Date --}}
                            <div>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Deposit Date</label>
                                {{-- 🚨 FIX: Added missing income_date field --}}
                                <input type="date" name="income_date" x-model="date" required max="{{ $today }}"
                                       class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-400 outline-none transition-all shadow-inner">
                            </div>

                            {{-- Account Type --}}
                            <div>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Account Ledger</label>
                                <div class="flex bg-slate-50 p-1.5 rounded-xl border border-slate-200 shadow-inner">
                                    <label class="flex-1 text-center cursor-pointer">
                                        <input type="radio" name="income_type" value="personal" x-model="accountType" class="hidden peer">
                                        <div class="py-2.5 text-sm font-bold text-slate-500 rounded-lg peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm transition-all">
                                            <i class="fa-solid fa-user mr-1"></i> Personal
                                        </div>
                                    </label>
                                    <label class="flex-1 text-center cursor-pointer">
                                        <input type="radio" name="income_type" value="family" x-model="accountType" class="hidden peer">
                                        <div class="py-2.5 text-sm font-bold text-slate-500 rounded-lg peer-checked:bg-white peer-checked:text-sky-600 peer-checked:shadow-sm transition-all">
                                            <i class="fa-solid fa-users mr-1"></i> Family
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Family Selector (Conditional) --}}
                        <div x-show="accountType === 'family'" x-collapse style="display: none;">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Select Family Ledger</label>
                            <div class="relative">
                                <select name="family_id" :required="accountType === 'family'" class="w-full px-5 py-4 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-sky-500/10 focus:border-sky-400 outline-none transition-all appearance-none cursor-pointer shadow-sm">
                                    <option value="">Choose designated family...</option>
                                    @foreach($families as $family)
                                        <option value="{{ $family->id }}">{{ $family->name }}</option>
                                    @endforeach
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                            </div>
                        </div>

                        {{-- Multi-Color Category Selection (🚨 FIX: Added missing category field) --}}
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Capital Classification</label>
                            <input type="hidden" name="category" :value="category" required>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                @foreach($categories as $cat)
                                <button type="button" @click="category = '{{ $cat['id'] }}'; categoryIcon = '{{ $cat['icon'] }}'; categoryColor = '{{ $cat['color'] }}'; categoryBg = '{{ $cat['bg'] }}'" 
                                        :class="category === '{{ $cat['id'] }}' ? 'ring-2 ring-emerald-500 bg-emerald-50 border-emerald-200 shadow-md transform -translate-y-1' : 'bg-white border-slate-200 hover:border-emerald-300 hover:bg-slate-50'"
                                        class="border rounded-2xl p-4 flex flex-col items-center justify-center gap-3 transition-all duration-200 focus:outline-none">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center shadow-sm {{ $cat['bg'] }} {{ $cat['color'] }} border {{ $cat['border'] }}">
                                        <i class="fa-solid {{ $cat['icon'] }}"></i>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700">{{ $cat['id'] }}</span>
                                </button>
                                @endforeach
                            </div>
                        </div>

                    </div>

                    {{-- Submit Area --}}
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm flex items-center justify-between">
                        <p class="text-xs font-bold text-slate-400 hidden sm:block"><i class="fa-solid fa-lock text-emerald-500 mr-1"></i> Secure 256-bit encrypted ledger.</p>
                        <button type="submit" :disabled="isSubmitting || amount <= 0 || !source || !category" 
                                class="w-full sm:w-auto px-10 py-4 bg-slate-900 text-white rounded-xl font-black shadow-[0_4px_15px_rgba(15,23,42,0.2)] hover:bg-emerald-600 hover:shadow-[0_6px_25px_rgba(16,185,129,0.3)] disabled:opacity-50 disabled:cursor-not-allowed transition-all focus:outline-none flex items-center justify-center gap-3">
                            <span x-show="!isSubmitting">Log Deposit <i class="fa-solid fa-arrow-right"></i></span>
                            <span x-show="isSubmitting" style="display: none;"><i class="fa-solid fa-circle-notch animate-spin"></i> Committing...</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- RIGHT COLUMN: PREVIEW, AI & RECENT --}}
            <div class="lg:col-span-4 space-y-6">
                
                {{-- Live Digital Deposit Slip --}}
                <div class="bg-white rounded-[2rem] border border-slate-200 shadow-xl overflow-hidden relative hidden sm:block">
                    <div class="h-3 w-full bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMCIgaGVpZ2h0PSIxMCI+PHBvbHlnb24gcG9pbnRzPSIwLDAgNSwxMCAxMCwwIiBmaWxsPSIjZjhmYWZjIi8+PC9zdmc+')] bg-repeat-x rotate-180"></div>
                    
                    <div class="p-8">
                        <div class="text-center mb-8 border-b border-slate-100 pb-8 border-dashed">
                            <div class="w-14 h-14 mx-auto rounded-full flex items-center justify-center mb-3 shadow-sm border border-slate-200 transition-colors duration-300"
                                 :class="category ? categoryBg : 'bg-slate-50'">
                                <i class="fa-solid text-xl transition-colors duration-300" :class="category ? categoryIcon + ' ' + categoryColor : 'fa-building-columns text-slate-300'"></i>
                            </div>
                            <h3 class="text-lg font-black text-slate-900 tracking-tight" x-text="source ? source : 'New Income Source'">New Income Source</h3>
                            <p class="text-xs font-bold text-slate-400 mt-1" x-text="formatDate(date)"></p>
                        </div>

                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-bold text-slate-400 uppercase tracking-widest text-[10px]">Classification</span>
                                <span class="font-bold text-slate-800" x-text="category ? category : 'Pending...'">Pending...</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-bold text-slate-400 uppercase tracking-widest text-[10px]">Ledger Account</span>
                                <span class="font-bold capitalize" :class="accountType === 'personal' ? 'text-emerald-600' : 'text-sky-600'" x-text="accountType">Personal</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-bold text-slate-400 uppercase tracking-widest text-[10px]">Status</span>
                                <span class="font-bold text-emerald-600"><i class="fa-solid fa-clock text-[10px]"></i> Processing</span>
                            </div>
                        </div>

                        <div class="bg-emerald-50 rounded-xl p-5 border border-emerald-100 flex justify-between items-center">
                            <span class="font-black text-emerald-700 uppercase tracking-widest text-[10px]">Gross Deposit</span>
                            <span class="text-2xl font-black text-emerald-600" x-text="formatINR(amount)">₹0.00</span>
                        </div>
                    </div>

                    <div class="h-3 w-full bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMCIgaGVpZ2h0PSIxMCI+PHBvbHlnb24gcG9pbnRzPSIwLDAgNSwxMCAxMCwwIiBmaWxsPSIjZjhmYWZjIi8+PC9zdmc+')] bg-repeat-x"></div>
                </div>

                {{-- AI Intelligence Module --}}
                <div class="bg-slate-900 rounded-[2rem] border border-slate-800 shadow-xl p-8 text-white relative overflow-hidden group">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10 mix-blend-overlay pointer-events-none"></div>
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/20 rounded-full blur-2xl transition-transform duration-1000 group-hover:scale-150 pointer-events-none"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center text-emerald-400 shadow-inner">
                                <i class="fa-solid fa-brain animate-pulse"></i>
                            </div>
                            <h3 class="text-lg font-black tracking-tight">AI Assessment</h3>
                        </div>

                        <div class="bg-white/5 border border-white/10 rounded-xl p-4 min-h-[80px]">
                            <p x-show="amount === '' || amount === 0" class="text-sm text-slate-400 font-medium">Awaiting financial input to generate real-time capital impact analysis.</p>
                            
                            <p x-show="amount > 0 && amount <= 10000" style="display: none;" class="text-sm text-white font-medium">
                                <i class="fa-solid fa-circle-check text-emerald-400 mr-1"></i> Stable deposit. This income stream adds healthy diversification to your portfolio.
                            </p>
                            
                            <p x-show="amount > 10000 && amount <= 50000" style="display: none;" class="text-sm text-white font-medium">
                                <i class="fa-solid fa-arrow-trend-up text-sky-400 mr-1"></i> Strong capital inflow. Consider allocating 20% of this deposit towards your investment targets.
                            </p>

                            <p x-show="amount > 50000" style="display: none;" class="text-sm text-white font-medium">
                                <i class="fa-solid fa-rocket text-purple-400 mr-1"></i> Exceptional liquidity event. This significantly raises your projected annual run rate.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Recent Income Ledger --}}
                <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Recent Deposits</h4>
                    <div class="space-y-4">
                        @forelse($recentIncome as $inc)
                            <div class="flex justify-between items-center group">
                                <div>
                                    <p class="text-sm font-bold text-slate-700 group-hover:text-emerald-600 transition-colors">{{ $inc->source ?? 'Deposit' }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ optional($inc->income_date ?? $inc->created_at)->format('d M') }}</p>
                                </div>
                                <span class="text-sm font-black text-emerald-600">+₹{{ number_format($inc->amount, 0) }}</span>
                            </div>
                            @if(!$loop->last) <div class="h-px w-full bg-slate-100"></div> @endif
                        @empty
                            <p class="text-xs font-bold text-slate-400 text-center py-4">No recent deposits found.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- Full Screen AI Scanning Overlay (New Fun) --}}
    <div x-show="scanning" style="display: none;" class="fixed inset-0 z-[200] bg-slate-900/80 backdrop-blur-md flex flex-col items-center justify-center">
        <div class="w-24 h-24 bg-white rounded-3xl flex items-center justify-center shadow-[0_0_50px_rgba(16,185,129,0.4)] mb-6 border-4 border-emerald-500 relative overflow-hidden">
            <i class="fa-solid fa-file-invoice-dollar text-4xl text-slate-300"></i>
            {{-- Laser Line --}}
            <div class="absolute w-full h-1 bg-emerald-400 shadow-[0_0_15px_rgba(52,211,153,1)] z-20 animate-[scan_1.5s_ease-in-out_infinite_alternate]"></div>
        </div>
        <h2 class="text-3xl font-black text-white mb-2 tracking-tight">AI Vision Processing</h2>
        <p class="text-emerald-200 font-medium text-lg mb-8" x-text="scanText">Extracting metadata from document...</p>
        <div class="w-64 h-2 bg-slate-800 rounded-full overflow-hidden border border-slate-700">
            <div class="h-full bg-emerald-500 transition-all duration-300 rounded-full" :style="`width: ${scanProgress}%`"></div>
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
    /* Laser Scan Animation */
    @keyframes scan { 0% { top: 5%; } 100% { top: 95%; } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
    
    /* Remove number input arrows for clean SaaS UI */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    input[type=number] { -moz-appearance: textfield; }
</style>
@endpush

@push('scripts')
<script>
// Alpine.js Component Logic
document.addEventListener('alpine:init', () => {
    Alpine.data('incomeForm', () => ({
        amount: '',
        source: '',
        date: '{{ $today }}',
        category: '',
        categoryIcon: '',
        categoryColor: '',
        categoryBg: '',
        accountType: 'personal',
        isSubmitting: false,
        
        // AI Simulator State
        scanning: false,
        scanProgress: 0,
        scanText: 'Connecting to Vision API...',

        addAmount(val) {
            let current = parseFloat(this.amount) || 0;
            this.amount = current + val;
        },

        formatINR(val) {
            let num = parseFloat(val) || 0;
            return '₹' + num.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        formatDate(dateStr) {
            if(!dateStr) return 'Pending Date';
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(dateStr).toLocaleDateString('en-US', options);
        },

        // The "Wow Factor" AI Auto-Fill Simulation
        autoFillSimulate() {
            this.scanning = true;
            this.scanProgress = 0;
            this.scanText = 'Connecting to Vision API...';
            
            let interval = setInterval(() => {
                this.scanProgress += Math.floor(Math.random() * 15) + 5;
                
                if(this.scanProgress > 30) this.scanText = 'Detecting deposit slip boundaries...';
                if(this.scanProgress > 60) this.scanText = 'Extracting numeric total and payer name...';
                if(this.scanProgress > 85) this.scanText = 'Classifying category via LLM...';

                if(this.scanProgress >= 100) {
                    this.scanProgress = 100;
                    clearInterval(interval);
                    
                    setTimeout(() => {
                        this.scanning = false;
                        
                        // Inject Dummy Data
                        this.amount = 85000.00;
                        this.source = "Acme Corp Software Retainer";
                        this.category = "Freelance";
                        this.categoryIcon = "fa-laptop-code";
                        this.categoryColor = "text-sky-500";
                        this.categoryBg = "bg-sky-50";
                        
                        showToast('Document parsed successfully!');
                    }, 500);
                }
            }, 300);
        }
    }));
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