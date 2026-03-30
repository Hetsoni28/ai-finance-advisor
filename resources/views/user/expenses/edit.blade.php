@extends('layouts.app')

@section('title', 'Edit Expense - FinanceAI')

@section('content')

@php
    // ================= SAFE DATA PREPARATION =================
    $originalAmount = (float)($expense->amount ?? 0);
    $isPersonal = $expense->is_personal ?? true;
    
    // Fallback categories matching the database ENUMs or standard list
    $categories = [
        ['id' => 'Food', 'icon' => 'fa-burger', 'color' => 'text-orange-500', 'bg' => 'bg-orange-50', 'border' => 'border-orange-200'],
        ['id' => 'Travel', 'icon' => 'fa-plane', 'color' => 'text-sky-500', 'bg' => 'bg-sky-50', 'border' => 'border-sky-200'],
        ['id' => 'Bills', 'icon' => 'fa-file-invoice-dollar', 'color' => 'text-rose-500', 'bg' => 'bg-rose-50', 'border' => 'border-rose-200'],
        ['id' => 'Shopping', 'icon' => 'fa-bag-shopping', 'color' => 'text-purple-500', 'bg' => 'bg-purple-50', 'border' => 'border-purple-200'],
        ['id' => 'Health', 'icon' => 'fa-heart-pulse', 'color' => 'text-emerald-500', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200'],
        ['id' => 'Others', 'icon' => 'fa-box-open', 'color' => 'text-slate-500', 'bg' => 'bg-slate-50', 'border' => 'border-slate-200'],
    ];

    $currentCategory = old('category', $expense->category ?? 'Others');
@endphp

<div class="min-h-screen bg-[#f8fafc] pb-24 font-sans selection:bg-indigo-100 selection:text-indigo-900 relative"
     x-data="{
        amount: {{ old('amount', $originalAmount) }},
        originalAmount: {{ $originalAmount }},
        category: '{{ $currentCategory }}',
        title: '{{ addslashes(old('title', $expense->title ?? '')) }}',
        isSubmitting: false,
        
        get variance() { return this.amount - this.originalAmount; },
        get isIncrease() { return this.variance > 0; },
        get isDecrease() { return this.variance < 0; },
        get isUnchanged() { return this.variance === 0; },
        
        adjustAmount(val) {
            let newAmount = this.amount + val;
            this.amount = newAmount < 0 ? 0 : parseFloat(newAmount.toFixed(2));
            triggerVarianceAnimation();
        }
     }">

    {{-- Pristine Ambient Background --}}
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden bg-white/40">
        <div class="absolute top-[-10%] left-[-5%] w-[800px] h-[800px] bg-indigo-50/70 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[800px] h-[800px] bg-rose-50/40 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 pt-10 relative z-10 space-y-8">

        {{-- ================= 1. HEADER ================= --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-indigo-500 to-purple-500"></div>
            
            <div>
                <nav class="flex mb-3" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <li><a href="{{ route('user.expenses.index') ?? '#' }}" class="hover:text-indigo-600 transition-colors">Outflow Ledger</a></li>
                        <li><i class="fa-solid fa-chevron-right text-[8px] mx-1"></i></li>
                        <li class="text-indigo-600">Modify Record</li>
                    </ol>
                </nav>
                <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">Edit Transaction</h1>
                <p class="text-slate-500 text-sm font-medium mt-2 flex items-center gap-2">
                    <i class="fa-solid fa-fingerprint text-slate-300"></i>
                    Record ID: #EXP-{{ str_pad($expense->id ?? 0, 5, '0', STR_PAD_LEFT) }} • Logged {{ optional($expense->created_at)->diffForHumans() ?? 'Unknown' }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <span class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-500 shadow-inner flex items-center gap-2">
                    @if($isPersonal)
                        <i class="fa-solid fa-user text-indigo-500"></i> Personal Account
                    @else
                        <i class="fa-solid fa-users text-emerald-500"></i> Family Account
                    @endif
                </span>
                <a href="{{ route('user.expenses.index') ?? '#' }}" class="px-5 py-3 bg-white text-slate-600 border border-slate-200 rounded-xl font-bold text-sm hover:bg-slate-50 hover:text-indigo-600 transition-all shadow-sm focus:outline-none flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i> Cancel
                </a>
            </div>
        </div>

        {{-- ================= 2. MAIN GRID FORM ================= --}}
        <form method="POST" action="{{ route('user.expenses.update', $expense->id ?? 0) }}" id="expenseForm" @submit="isSubmitting = true" class="grid lg:grid-cols-12 gap-8">
            @csrf
            @method('PUT')

            {{-- LEFT COLUMN: DATA ENTRY --}}
            <div class="lg:col-span-8 space-y-6">
                
                {{-- Financial Value Core --}}
                <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8 sm:p-10 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-indigo-50/50 rounded-full blur-3xl group-focus-within:bg-indigo-100 transition-colors duration-500"></div>
                    
                    <label class="block text-xs font-black text-indigo-600 uppercase tracking-widest mb-4">Total Amount (INR)</label>
                    
                    <div class="relative flex items-center">
                        <span class="absolute left-0 text-4xl sm:text-5xl font-black text-slate-300 pointer-events-none">₹</span>
                        <input id="amountInput" type="number" name="amount" x-model.number="amount" @input="triggerVarianceAnimation()" min="0.01" step="0.01" required
                               class="w-full pl-12 sm:pl-16 py-2 bg-transparent border-none text-4xl sm:text-5xl font-black text-slate-900 focus:ring-0 outline-none p-0 m-0 placeholder-slate-200" placeholder="0.00">
                    </div>
                    
                    <div class="h-px w-full bg-slate-100 my-6 group-focus-within:bg-indigo-200 transition-colors duration-300"></div>

                    {{-- Quick Adjustments (New Fun) --}}
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest mr-2">Quick Adjust:</span>
                        <button type="button" @click="adjustAmount(100)" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50 transition-all shadow-sm focus:outline-none">+100</button>
                        <button type="button" @click="adjustAmount(500)" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50 transition-all shadow-sm focus:outline-none">+500</button>
                        <button type="button" @click="adjustAmount(-500)" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 transition-all shadow-sm focus:outline-none">-500</button>
                        <button type="button" @click="amount = originalAmount; triggerVarianceAnimation();" class="px-4 py-2 bg-slate-100 border border-slate-200 rounded-lg text-xs font-bold text-slate-500 hover:text-slate-800 transition-all ml-auto focus:outline-none"><i class="fa-solid fa-rotate-left mr-1"></i> Reset</button>
                    </div>
                    @error('amount') <p class="text-rose-500 text-xs font-bold mt-3"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
                </div>

                {{-- Metadata Core --}}
                <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8 sm:p-10 space-y-8">
                    
                    {{-- Title Input --}}
                    <div>
                        <div class="flex justify-between items-end mb-3">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Transaction Title</label>
                            <span class="text-[10px] font-bold text-slate-300" x-text="`${title.length} / 150`">0 / 150</span>
                        </div>
                        <input id="titleInput" type="text" name="title" x-model="title" maxlength="150" required
                               class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-xl text-base font-bold text-slate-900 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 outline-none transition-all shadow-inner placeholder-slate-300" placeholder="e.g. Monthly Internet Bill">
                        @error('title') <p class="text-rose-500 text-xs font-bold mt-2"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
                    </div>

                    <div class="grid sm:grid-cols-2 gap-8">
                        {{-- Date Selection --}}
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Billing Date</label>
                            <input type="date" name="expense_date" required value="{{ old('expense_date', optional($expense->expense_date)->format('Y-m-d')) }}"
                                   class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 outline-none transition-all shadow-inner">
                            @error('expense_date') <p class="text-rose-500 text-xs font-bold mt-2"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
                        </div>

                        {{-- Hidden Category Input tied to Alpine --}}
                        <input type="hidden" name="category" :value="category">
                    </div>

                    {{-- Visual Category Grid (New Fun) --}}
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Classification</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            @foreach($categories as $cat)
                            <button type="button" @click="category = '{{ $cat['id'] }}'" 
                                    :class="category === '{{ $cat['id'] }}' ? 'ring-2 ring-indigo-500 bg-indigo-50 border-indigo-200 shadow-md transform -translate-y-1' : 'bg-white border-slate-200 hover:border-indigo-300 hover:bg-slate-50'"
                                    class="border rounded-2xl p-4 flex flex-col items-center justify-center gap-3 transition-all duration-200 focus:outline-none">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center shadow-sm {{ $cat['bg'] }} {{ $cat['color'] }} border {{ $cat['border'] }}">
                                    <i class="fa-solid {{ $cat['icon'] }}"></i>
                                </div>
                                <span class="text-xs font-bold text-slate-700">{{ $cat['id'] }}</span>
                            </button>
                            @endforeach
                        </div>
                        @error('category') <p class="text-rose-500 text-xs font-bold mt-2"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
                    </div>

                </div>
            </div>

            {{-- RIGHT COLUMN: AI INTELLIGENCE & SUBMIT --}}
            <div class="lg:col-span-4 space-y-6">
                
                {{-- AI Financial Impact Engine --}}
                <div class="bg-slate-900 rounded-[2rem] border border-slate-800 shadow-xl p-8 text-white relative overflow-hidden group">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10 mix-blend-overlay pointer-events-none"></div>
                    
                    {{-- Dynamic Background Glow based on Variance --}}
                    <div class="absolute -top-20 -right-20 w-48 h-48 rounded-full blur-3xl transition-colors duration-700 opacity-30"
                         :class="{'bg-emerald-500': isDecrease, 'bg-rose-500': isIncrease, 'bg-slate-500': isUnchanged}"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center text-white shadow-inner">
                                <i class="fa-solid fa-brain animate-pulse"></i>
                            </div>
                            <h3 class="text-lg font-black tracking-tight">AI Impact Engine</h3>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Original Value</p>
                                <p class="text-xl font-bold text-slate-300">₹{{ number_format($originalAmount, 2) }}</p>
                            </div>

                            <div class="h-px w-full bg-slate-700/50"></div>

                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Projected Variance</p>
                                
                                {{-- Live Animated Counter --}}
                                <h2 id="varianceDisplay" class="text-4xl font-black mb-3 transition-colors duration-300"
                                    :class="{'text-emerald-400': isDecrease, 'text-rose-400': isIncrease, 'text-slate-300': isUnchanged}">
                                    ₹0.00
                                </h2>
                                
                                {{-- AI Context Text --}}
                                <div class="bg-white/5 border border-white/10 rounded-xl p-4 min-h-[80px]">
                                    <p x-show="isUnchanged" class="text-sm text-slate-400 font-medium">No financial deviation detected. Value remains static.</p>
                                    <p x-show="isIncrease" style="display: none;" class="text-sm text-rose-300 font-medium">
                                        <i class="fa-solid fa-arrow-trend-up text-rose-400 mr-1"></i> Increasing this expense will place higher strain on your monthly budget liquidity.
                                    </p>
                                    <p x-show="isDecrease" style="display: none;" class="text-sm text-emerald-300 font-medium">
                                        <i class="fa-solid fa-arrow-trend-down text-emerald-400 mr-1"></i> Decreasing this expense successfully recovers capital back into your savings.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm">
                    <button type="submit" :disabled="isSubmitting" 
                            class="w-full py-4 bg-indigo-600 text-white rounded-xl font-black shadow-[0_4px_15px_rgba(79,70,229,0.3)] hover:bg-indigo-700 hover:shadow-[0_6px_25px_rgba(79,70,229,0.4)] disabled:opacity-50 disabled:cursor-not-allowed transition-all focus:outline-none flex items-center justify-center gap-3">
                        <span x-show="!isSubmitting"><i class="fa-solid fa-check"></i> Commit Update</span>
                        <span x-show="isSubmitting" style="display: none;"><i class="fa-solid fa-circle-notch animate-spin"></i> Processing...</span>
                    </button>
                    <p class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-4">FinanceAI Secure Ledger</p>
                </div>

            </div>
        </form>

    </div>

    {{-- Loading Overlay (Simulated Server Processing) --}}
    <div x-show="isSubmitting" style="display: none;" class="fixed inset-0 z-[150] bg-slate-900/40 backdrop-blur-sm flex flex-col items-center justify-center">
        <div class="w-16 h-16 rounded-2xl bg-white flex items-center justify-center shadow-2xl mb-4">
            <i class="fa-solid fa-robot text-2xl text-indigo-600 animate-pulse"></i>
        </div>
        <p class="text-white font-black text-xl tracking-tight">Syncing Database...</p>
    </div>

</div>

@endsection

@push('scripts')
<script>
    // Bulletproof Variable Counter logic (replaces buggy setInterval)
    let animationFrameId;
    let currentDisplayVal = 0;
    const formatINR = (n) => {
        let sign = n > 0 ? '+' : (n < 0 ? '-' : '');
        let abs = Math.abs(n);
        // Using standard local string for safety
        return sign + '₹' + abs.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    function triggerVarianceAnimation() {
        // We read from Alpine's data state indirectly via DOM or calculate manually
        const amountInput = document.getElementById('amountInput');
        const displayEl = document.getElementById('varianceDisplay');
        const orig = {{ $originalAmount }};
        
        if(!amountInput || !displayEl) return;

        let targetVal = (parseFloat(amountInput.value) || 0) - orig;
        
        // Cancel previous animation
        if (animationFrameId) cancelAnimationFrame(animationFrameId);

        const duration = 400; // ms
        const startTime = performance.now();
        const startVal = currentDisplayVal;

        function animate(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Ease out cubic
            const easeProgress = 1 - Math.pow(1 - progress, 3);
            
            currentDisplayVal = startVal + (targetVal - startVal) * easeProgress;
            displayEl.innerText = formatINR(currentDisplayVal);

            if (progress < 1) {
                animationFrameId = requestAnimationFrame(animate);
            } else {
                currentDisplayVal = targetVal; // Snap to exact
                displayEl.innerText = formatINR(targetVal);
            }
        }

        animationFrameId = requestAnimationFrame(animate);
    }

    // Trigger initial state
    document.addEventListener('DOMContentLoaded', () => {
        triggerVarianceAnimation();
    });
</script>
@endpush