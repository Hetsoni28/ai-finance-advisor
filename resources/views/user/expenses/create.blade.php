@extends('layouts.app')

@section('title', 'Log Transaction - FinanceAI')

@section('content')

@php
    // ================= SAFE DATA PREPARATION =================
    // Categories mapped to modern icons and pastel colors
    $categories = [
        ['id' => 'Food', 'icon' => 'fa-burger', 'color' => 'text-orange-500', 'bg' => 'bg-orange-50', 'border' => 'border-orange-200'],
        ['id' => 'Travel', 'icon' => 'fa-plane', 'color' => 'text-sky-500', 'bg' => 'bg-sky-50', 'border' => 'border-sky-200'],
        ['id' => 'Bills', 'icon' => 'fa-file-invoice-dollar', 'color' => 'text-rose-500', 'bg' => 'bg-rose-50', 'border' => 'border-rose-200'],
        ['id' => 'Shopping', 'icon' => 'fa-bag-shopping', 'color' => 'text-purple-500', 'bg' => 'bg-purple-50', 'border' => 'border-purple-200'],
        ['id' => 'Health', 'icon' => 'fa-heart-pulse', 'color' => 'text-emerald-500', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200'],
        ['id' => 'Others', 'icon' => 'fa-box-open', 'color' => 'text-slate-500', 'bg' => 'bg-slate-50', 'border' => 'border-slate-200'],
    ];

    $families = $families ?? collect([]); // Fallback if no families passed
    $today = now()->format('Y-m-d');
@endphp

<div class="min-h-screen bg-[#f8fafc] pb-24 font-sans selection:bg-indigo-100 selection:text-indigo-900 relative"
     x-data="expenseForm()">
    
    {{-- Pristine Ambient Background --}}
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden bg-white/40">
        <div class="absolute top-[-10%] left-[-5%] w-[800px] h-[800px] bg-indigo-50/70 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[800px] h-[800px] bg-rose-50/40 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 pt-10 relative z-10 space-y-8">

        {{-- ================= 1. COMMAND HEADER ================= --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-indigo-500 to-purple-500"></div>

            <div>
                <nav class="flex mb-3" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <li><a href="{{ route('user.expenses.index') ?? '#' }}" class="hover:text-indigo-600 transition-colors">Outflow Ledger</a></li>
                        <li><i class="fa-solid fa-chevron-right text-[8px] mx-1"></i></li>
                        <li class="text-indigo-600">Log Transaction</li>
                    </ol>
                </nav>
                <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">Record Expense</h1>
                <p class="text-slate-500 text-sm font-medium mt-2 flex items-center gap-2">
                    Intelligent Financial Processing Engine
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button @click="autoFillSimulate()" class="px-5 py-3.5 bg-slate-50 text-indigo-600 border border-indigo-100 rounded-xl font-bold text-sm hover:bg-indigo-50 hover:border-indigo-300 transition-all flex items-center gap-2 focus:outline-none shadow-sm">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Auto-Fill via AI
                </button>
                <a href="{{ route('user.expenses.index') ?? '#' }}" class="px-5 py-3.5 bg-white text-slate-600 border border-slate-200 rounded-xl font-bold text-sm hover:bg-slate-50 hover:text-indigo-600 transition-all shadow-sm focus:outline-none flex items-center gap-2">
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
                <form method="POST" action="{{ route('user.expenses.store') ?? '#' }}" id="expenseForm" @submit="isSubmitting = true" class="space-y-6">
                    @csrf

                    {{-- Section 1: Financial Value --}}
                    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8 sm:p-10 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-indigo-50/50 rounded-full blur-3xl group-focus-within:bg-indigo-100 transition-colors duration-500 pointer-events-none"></div>
                        
                        <label class="block text-xs font-black text-indigo-600 uppercase tracking-widest mb-4">Total Amount (INR)</label>
                        
                        <div class="relative flex items-center">
                            <span class="absolute left-0 text-4xl sm:text-5xl font-black text-slate-300 pointer-events-none">₹</span>
                            <input type="number" name="amount" x-model.number="amount" min="0.01" step="0.01" required
                                   class="w-full pl-12 sm:pl-16 py-2 bg-transparent border-none text-4xl sm:text-5xl font-black text-slate-900 focus:ring-0 outline-none p-0 m-0 placeholder-slate-200" placeholder="0.00">
                        </div>
                        
                        <div class="h-px w-full bg-slate-100 my-6 group-focus-within:bg-indigo-200 transition-colors duration-300"></div>

                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest mr-2">Quick Add:</span>
                            <button type="button" @click="addAmount(500)" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50 transition-all shadow-sm focus:outline-none">+500</button>
                            <button type="button" @click="addAmount(1000)" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50 transition-all shadow-sm focus:outline-none">+1,000</button>
                            <button type="button" @click="addAmount(5000)" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50 transition-all shadow-sm focus:outline-none">+5,000</button>
                            <button type="button" @click="amount = ''" class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-500 hover:text-slate-800 transition-all ml-auto focus:outline-none"><i class="fa-solid fa-rotate-left mr-1"></i> Clear</button>
                        </div>
                    </div>

                    {{-- Section 2: Transaction Details --}}
                    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8 sm:p-10 space-y-8">
                        
                        {{-- Title --}}
                        <div>
                            <div class="flex justify-between items-end mb-3">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Merchant / Description</label>
                                <span class="text-[10px] font-bold text-slate-300" x-text="`${title.length} / 150`">0 / 150</span>
                            </div>
                            <input type="text" name="title" x-model="title" maxlength="150" required
                                   class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-xl text-base font-bold text-slate-900 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 outline-none transition-all shadow-inner placeholder-slate-300" placeholder="e.g. Uber Ride, Starbucks, Internet Bill">
                        </div>

                        <div class="grid sm:grid-cols-2 gap-8">
                            {{-- Date --}}
                            <div>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Transaction Date</label>
                                <input type="date" name="expense_date" x-model="date" required max="{{ $today }}"
                                       class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 outline-none transition-all shadow-inner">
                            </div>

                            {{-- Account Type --}}
                            <div>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Account Type</label>
                                <div class="flex bg-slate-50 p-1.5 rounded-xl border border-slate-200 shadow-inner">
                                    <label class="flex-1 text-center cursor-pointer">
                                        <input type="radio" name="expense_type" value="personal" x-model="accountType" class="hidden peer">
                                        <div class="py-2.5 text-sm font-bold text-slate-500 rounded-lg peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-sm transition-all">
                                            <i class="fa-solid fa-user mr-1"></i> Personal
                                        </div>
                                    </label>
                                    <label class="flex-1 text-center cursor-pointer">
                                        <input type="radio" name="expense_type" value="family" x-model="accountType" class="hidden peer">
                                        <div class="py-2.5 text-sm font-bold text-slate-500 rounded-lg peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm transition-all">
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
                                <select name="family_id" :required="accountType === 'family'" class="w-full px-5 py-4 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-400 outline-none transition-all appearance-none cursor-pointer shadow-sm">
                                    <option value="">Choose designated family...</option>
                                    @foreach($families as $family)
                                        <option value="{{ $family->id }}">{{ $family->name }}</option>
                                    @endforeach
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                            </div>
                        </div>

                        {{-- Category Selection --}}
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Classification</label>
                            <input type="hidden" name="category" :value="category">
                            
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                @foreach($categories as $cat)
                                <button type="button" @click="category = '{{ $cat['id'] }}'; categoryIcon = '{{ $cat['icon'] }}'; categoryColor = '{{ $cat['color'] }}'; categoryBg = '{{ $cat['bg'] }}'" 
                                        :class="category === '{{ $cat['id'] }}' ? 'ring-2 ring-indigo-500 bg-indigo-50 border-indigo-200 shadow-md transform -translate-y-1' : 'bg-white border-slate-200 hover:border-indigo-300 hover:bg-slate-50'"
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
                        <button type="submit" :disabled="isSubmitting || amount <= 0 || !title || !category" 
                                class="w-full sm:w-auto px-10 py-4 bg-slate-900 text-white rounded-xl font-black shadow-[0_4px_15px_rgba(15,23,42,0.2)] hover:bg-indigo-600 hover:shadow-indigo-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all focus:outline-none flex items-center justify-center gap-3">
                            <span x-show="!isSubmitting">Record Transaction <i class="fa-solid fa-arrow-right"></i></span>
                            <span x-show="isSubmitting" style="display: none;"><i class="fa-solid fa-circle-notch animate-spin"></i> Committing...</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- RIGHT COLUMN: LIVE PREVIEW & AI --}}
            <div class="lg:col-span-4 space-y-6">
                
                {{-- Live Digital Receipt --}}
                <div class="bg-white rounded-[2rem] border border-slate-200 shadow-xl overflow-hidden relative">
                    {{-- Receipt Header Jagged Edge --}}
                    <div class="h-3 w-full bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMCIgaGVpZ2h0PSIxMCI+PHBvbHlnb24gcG9pbnRzPSIwLDAgNSwxMCAxMCwwIiBmaWxsPSIjZjhmYWZjIi8+PC9zdmc+')] bg-repeat-x rotate-180"></div>
                    
                    <div class="p-8">
                        <div class="text-center mb-8 border-b border-slate-100 pb-8 border-dashed">
                            <div class="w-14 h-14 mx-auto rounded-full flex items-center justify-center mb-3 shadow-sm border border-slate-200 transition-colors duration-300"
                                 :class="category ? categoryBg : 'bg-slate-50'">
                                <i class="fa-solid text-xl transition-colors duration-300" :class="category ? categoryIcon + ' ' + categoryColor : 'fa-receipt text-slate-300'"></i>
                            </div>
                            <h3 class="text-lg font-black text-slate-900 tracking-tight" x-text="title ? title : 'New Transaction'">New Transaction</h3>
                            <p class="text-xs font-bold text-slate-400 mt-1" x-text="formatDate(date)"></p>
                        </div>

                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-bold text-slate-400 uppercase tracking-widest text-[10px]">Classification</span>
                                <span class="font-bold text-slate-800" x-text="category ? category : 'Pending...'">Pending...</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-bold text-slate-400 uppercase tracking-widest text-[10px]">Ledger Account</span>
                                <span class="font-bold capitalize" :class="accountType === 'personal' ? 'text-indigo-600' : 'text-emerald-600'" x-text="accountType">Personal</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-bold text-slate-400 uppercase tracking-widest text-[10px]">Status</span>
                                <span class="font-bold text-emerald-600"><i class="fa-solid fa-check text-[10px]"></i> Cleared</span>
                            </div>
                        </div>

                        <div class="bg-slate-50 rounded-xl p-5 border border-slate-100 flex justify-between items-center">
                            <span class="font-black text-slate-900 uppercase tracking-widest text-xs">Total Amount</span>
                            <span class="text-2xl font-black text-slate-900" x-text="formatINR(amount)">₹0.00</span>
                        </div>
                    </div>

                    {{-- Receipt Footer Jagged Edge --}}
                    <div class="h-3 w-full bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMCIgaGVpZ2h0PSIxMCI+PHBvbHlnb24gcG9pbnRzPSIwLDAgNSwxMCAxMCwwIiBmaWxsPSIjZjhmYWZjIi8+PC9zdmc+')] bg-repeat-x"></div>
                </div>

                {{-- AI Intelligence Module --}}
                <div class="bg-indigo-600 rounded-[2rem] border border-indigo-500 shadow-[0_20px_50px_-12px_rgba(79,70,229,0.3)] p-8 text-white relative overflow-hidden group">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10 mix-blend-overlay pointer-events-none"></div>
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl transition-transform duration-1000 group-hover:scale-150 pointer-events-none"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center text-white shadow-inner">
                                <i class="fa-solid fa-brain animate-pulse"></i>
                            </div>
                            <h3 class="text-lg font-black tracking-tight">AI Assessment</h3>
                        </div>

                        <div class="bg-white/5 border border-white/10 rounded-xl p-4 min-h-[80px]">
                            <p x-show="amount === '' || amount === 0" class="text-sm text-indigo-200 font-medium">Awaiting financial input to generate real-time budget impact analysis.</p>
                            
                            <p x-show="amount > 0 && amount <= 2000" style="display: none;" class="text-sm text-white font-medium">
                                <i class="fa-solid fa-circle-check text-emerald-400 mr-1"></i> Low-impact transaction. This expense is well within standard daily operating variance.
                            </p>
                            
                            <p x-show="amount > 2000 && amount <= 10000" style="display: none;" class="text-sm text-white font-medium">
                                <i class="fa-solid fa-shield-halved text-amber-400 mr-1"></i> Moderate outflow. This will trigger a slight adjustment to your monthly predictive savings model.
                            </p>

                            <p x-show="amount > 10000" style="display: none;" class="text-sm text-white font-medium">
                                <i class="fa-solid fa-triangle-exclamation text-rose-400 mr-1"></i> High capital burn. Adding <strong x-text="formatINR(amount)"></strong> will significantly alter your baseline liquidity for this month.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- Full Screen AI Scanning Overlay (New Fun) --}}
    <div x-show="scanning" style="display: none;" class="fixed inset-0 z-[200] bg-slate-900/80 backdrop-blur-md flex flex-col items-center justify-center">
        <div class="w-24 h-24 bg-white rounded-3xl flex items-center justify-center shadow-[0_0_50px_rgba(79,70,229,0.5)] mb-6 border-4 border-indigo-500 relative overflow-hidden">
            <i class="fa-solid fa-file-invoice text-4xl text-slate-300"></i>
            {{-- Laser Line --}}
            <div class="absolute w-full h-1 bg-emerald-400 shadow-[0_0_15px_rgba(52,211,153,1)] z-20 animate-[scan_1.5s_ease-in-out_infinite_alternate]"></div>
        </div>
        <h2 class="text-3xl font-black text-white mb-2 tracking-tight">AI Vision Processing</h2>
        <p class="text-indigo-200 font-medium text-lg mb-8" x-text="scanText">Extracting metadata from document...</p>
        <div class="w-64 h-2 bg-slate-800 rounded-full overflow-hidden border border-slate-700">
            <div class="h-full bg-indigo-500 transition-all duration-300 rounded-full" :style="`width: ${scanProgress}%`"></div>
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
    
    /* Remove number input arrows for clean UI */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    input[type=number] { -moz-appearance: textfield; }
</style>
@endpush

@push('scripts')
<script>
// Alpine.js Component Logic
document.addEventListener('alpine:init', () => {
    Alpine.data('expenseForm', () => ({
        amount: '',
        title: '',
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
                
                if(this.scanProgress > 30) this.scanText = 'Detecting bounding boxes...';
                if(this.scanProgress > 60) this.scanText = 'Extracting numeric total and merchant name...';
                if(this.scanProgress > 85) this.scanText = 'Classifying category via LLM...';

                if(this.scanProgress >= 100) {
                    this.scanProgress = 100;
                    clearInterval(interval);
                    
                    setTimeout(() => {
                        this.scanning = false;
                        
                        // Inject Dummy Data
                        this.amount = 4250.00;
                        this.title = "Starbucks Corp - Client Meeting";
                        this.category = "Food";
                        this.categoryIcon = "fa-burger";
                        this.categoryColor = "text-orange-500";
                        this.categoryBg = "bg-orange-50";
                        
                        showToast('Receipt parsed successfully!');
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