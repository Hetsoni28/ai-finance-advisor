@extends('layouts.app')

@section('title', 'Modify Income Record - FinanceAI')

@section('content')

@php
    // ================= SAFE DATA PREPARATION =================
    $originalAmount = (float) ($income->amount ?? 0);
    $currentCategory = old('category', $income->category ?? 'Salary');
    
    // Fallback categories mapped to modern multi-color SaaS icons
    $categories = [
        ['id' => 'Salary', 'icon' => 'fa-building-columns', 'color' => 'text-emerald-500', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200'],
        ['id' => 'Freelance', 'icon' => 'fa-laptop-code', 'color' => 'text-sky-500', 'bg' => 'bg-sky-50', 'border' => 'border-sky-200'],
        ['id' => 'Investments', 'icon' => 'fa-arrow-trend-up', 'color' => 'text-purple-500', 'bg' => 'bg-purple-50', 'border' => 'border-purple-200'],
        ['id' => 'Business', 'icon' => 'fa-store', 'color' => 'text-amber-500', 'bg' => 'bg-amber-50', 'border' => 'border-amber-200'],
        ['id' => 'Gifts', 'icon' => 'fa-gift', 'color' => 'text-rose-500', 'bg' => 'bg-rose-50', 'border' => 'border-rose-200'],
        ['id' => 'Others', 'icon' => 'fa-box-open', 'color' => 'text-slate-500', 'bg' => 'bg-slate-50', 'border' => 'border-slate-200'],
    ];

    $today = now()->format('Y-m-d');
@endphp

<div class="min-h-screen bg-[#f8fafc] pb-24 font-sans selection:bg-emerald-100 selection:text-emerald-900 relative"
     x-data="{
        amount: {{ old('amount', $originalAmount) }},
        originalAmount: {{ $originalAmount }},
        category: '{{ $currentCategory }}',
        source: '{{ addslashes(old('source', $income->source ?? '')) }}',
        date: '{{ old('income_date', optional($income->income_date ?? $income->created_at)->format('Y-m-d')) }}',
        isSubmitting: false,
        
        get variance() { return this.amount - this.originalAmount; },
        get isIncrease() { return this.variance > 0; },
        get isDecrease() { return this.variance < 0; },
        get isUnchanged() { return this.variance === 0; },
        
        adjustAmount(val) {
            let newAmount = this.amount + val;
            this.amount = newAmount < 0 ? 0 : parseFloat(newAmount.toFixed(2));
            triggerVarianceAnimation();
        },
        
        formatINR(val) {
            let num = parseFloat(val) || 0;
            return '₹' + num.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        
        formatDateDisplay() {
            if(!this.date) return 'Pending Date';
            return new Date(this.date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        }
     }"
     x-init="setTimeout(() => triggerVarianceAnimation(), 100)">

    {{-- Pristine Ambient Background --}}
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden bg-white/40">
        <div class="absolute top-[-10%] left-[-5%] w-[800px] h-[800px] bg-emerald-50/70 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[800px] h-[800px] bg-cyan-50/40 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 pt-10 relative z-10 space-y-8">

        {{-- ================= 1. COMMAND HEADER ================= --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-emerald-500 to-cyan-500"></div>
            
            <div>
                <nav class="flex mb-3" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <li><a href="{{ route('user.incomes.index') ?? '#' }}" class="hover:text-emerald-600 transition-colors">Inbound Capital</a></li>
                        <li><i class="fa-solid fa-chevron-right text-[8px] mx-1"></i></li>
                        <li class="text-emerald-600">Modify Record</li>
                    </ol>
                </nav>
                <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">Edit <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-500 to-cyan-500">Income</span></h1>
                <p class="text-slate-500 text-sm font-medium mt-2 flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-emerald-500"></i>
                    Record ID: #INC-{{ str_pad($income->id ?? 0, 5, '0', STR_PAD_LEFT) }} • Logged {{ optional($income->created_at)->diffForHumans() ?? 'Unknown' }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('user.incomes.index') ?? '#' }}" class="px-5 py-3.5 bg-white text-slate-600 border border-slate-200 rounded-xl font-bold text-sm hover:bg-slate-50 hover:text-emerald-600 transition-all shadow-sm focus:outline-none flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i> Cancel Edit
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

        {{-- ================= 2. MAIN GRID FORM ================= --}}
        <form method="POST" action="{{ route('user.incomes.update', $income->id ?? 0) }}" id="incomeForm" @submit="isSubmitting = true" class="grid lg:grid-cols-12 gap-8">
            @csrf
            @method('PUT')

            {{-- LEFT COLUMN: DATA ENTRY --}}
            <div class="lg:col-span-8 space-y-6">
                
                {{-- Financial Value Core --}}
                <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8 sm:p-10 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-emerald-50/50 rounded-full blur-3xl group-focus-within:bg-emerald-100 transition-colors duration-500 pointer-events-none"></div>
                    
                    <label class="block text-xs font-black text-emerald-600 uppercase tracking-widest mb-4">Total Amount (INR)</label>
                    
                    <div class="relative flex items-center">
                        <span class="absolute left-0 text-4xl sm:text-5xl font-black text-slate-300 pointer-events-none">₹</span>
                        <input id="amountInput" type="number" name="amount" x-model.number="amount" @input="triggerVarianceAnimation()" min="0.01" step="0.01" required
                               class="w-full pl-12 sm:pl-16 py-2 bg-transparent border-none text-4xl sm:text-5xl font-black text-slate-900 focus:ring-0 outline-none p-0 m-0 placeholder-slate-200" placeholder="0.00">
                    </div>
                    
                    <div class="h-px w-full bg-slate-100 my-6 group-focus-within:bg-emerald-200 transition-colors duration-300"></div>

                    {{-- Quick Adjustments (New Fun) --}}
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest mr-2">Quick Adjust:</span>
                        <button type="button" @click="adjustAmount(1000)" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:text-emerald-600 hover:border-emerald-300 hover:bg-emerald-50 transition-all shadow-sm focus:outline-none">+1,000</button>
                        <button type="button" @click="adjustAmount(5000)" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:text-emerald-600 hover:border-emerald-300 hover:bg-emerald-50 transition-all shadow-sm focus:outline-none">+5,000</button>
                        <button type="button" @click="adjustAmount(-1000)" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 transition-all shadow-sm focus:outline-none">-1,000</button>
                        <button type="button" @click="amount = originalAmount; triggerVarianceAnimation();" class="px-4 py-2 bg-slate-100 border border-slate-200 rounded-lg text-xs font-bold text-slate-500 hover:text-slate-800 transition-all ml-auto focus:outline-none"><i class="fa-solid fa-rotate-left mr-1"></i> Reset</button>
                    </div>
                    @error('amount') <p class="text-rose-500 text-xs font-bold mt-3"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
                </div>

                {{-- Metadata Core --}}
                <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8 sm:p-10 space-y-8">
                    
                    <div class="grid sm:grid-cols-2 gap-8">
                        {{-- Source Input --}}
                        <div>
                            <div class="flex justify-between items-end mb-3">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Income Source</label>
                                <span class="text-[10px] font-bold text-slate-300" x-text="`${source.length} / 60`">0 / 60</span>
                            </div>
                            <input id="sourceInput" type="text" name="source" x-model="source" maxlength="60" required
                                   class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-xl text-base font-bold text-slate-900 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-400 outline-none transition-all shadow-inner placeholder-slate-300" placeholder="e.g. Acme Corp, Upwork, Dividend">
                            @error('source') <p class="text-rose-500 text-xs font-bold mt-2"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
                        </div>

                        {{-- Date Selection --}}
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Deposit Date</label>
                            <input type="date" name="income_date" x-model="date" required max="{{ $today }}"
                                   class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-400 outline-none transition-all shadow-inner">
                            @error('income_date') <p class="text-rose-500 text-xs font-bold mt-2"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Hidden Category Input tied to Alpine --}}
                    <input type="hidden" name="category" :value="category">

                    {{-- Visual Multi-Color Category Grid (New Fun) --}}
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Capital Classification</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            @foreach($categories as $cat)
                            <button type="button" @click="category = '{{ $cat['id'] }}'" 
                                    :class="category === '{{ $cat['id'] }}' ? 'ring-2 ring-emerald-500 bg-emerald-50 border-emerald-200 shadow-md transform -translate-y-1' : 'bg-white border-slate-200 hover:border-emerald-300 hover:bg-slate-50'"
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

            {{-- RIGHT COLUMN: AI INTELLIGENCE & RECEIPT --}}
            <div class="lg:col-span-4 space-y-6">
                
                {{-- AI Financial Impact Engine (Corrected Logic for Income) --}}
                <div class="bg-slate-900 rounded-[2rem] border border-slate-800 shadow-xl p-8 text-white relative overflow-hidden group">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10 mix-blend-overlay pointer-events-none"></div>
                    
                    {{-- Dynamic Background Glow based on Variance --}}
                    {{-- FIX: For income, increase is good (Emerald), decrease is bad (Rose) --}}
                    <div class="absolute -top-20 -right-20 w-48 h-48 rounded-full blur-3xl transition-colors duration-700 opacity-30"
                         :class="{'bg-emerald-500': isIncrease, 'bg-rose-500': isDecrease, 'bg-slate-500': isUnchanged}"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center text-white shadow-inner">
                                <i class="fa-solid fa-brain animate-pulse"></i>
                            </div>
                            <h3 class="text-lg font-black tracking-tight">AI Impact Engine</h3>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Original Capital</p>
                                <p class="text-xl font-bold text-slate-300">₹{{ number_format($originalAmount, 2) }}</p>
                            </div>

                            <div class="h-px w-full bg-slate-700/50"></div>

                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Projected Variance</p>
                                
                                {{-- Live Animated Counter --}}
                                <h2 id="varianceDisplay" class="text-4xl font-black mb-3 transition-colors duration-300"
                                    :class="{'text-emerald-400': isIncrease, 'text-rose-400': isDecrease, 'text-slate-300': isUnchanged}">
                                    ₹0.00
                                </h2>
                                
                                {{-- AI Context Text --}}
                                <div class="bg-white/5 border border-white/10 rounded-xl p-4 min-h-[80px]">
                                    <p x-show="isUnchanged" class="text-sm text-slate-400 font-medium">No financial deviation detected. Liquidity remains static.</p>
                                    
                                    <p x-show="isIncrease" style="display: none;" class="text-sm text-emerald-300 font-medium">
                                        <i class="fa-solid fa-arrow-trend-up text-emerald-400 mr-1"></i> Positive growth detected. Upward revision strengthens overall capital reserves.
                                    </p>
                                    
                                    <p x-show="isDecrease" style="display: none;" class="text-sm text-rose-300 font-medium">
                                        <i class="fa-solid fa-arrow-trend-down text-rose-400 mr-1"></i> Revenue contraction detected. This downward revision reduces available capital.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Live Digital Deposit Slip --}}
                <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden relative hidden sm:block">
                    <div class="h-2 w-full bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMCIgaGVpZ2h0PSIxMCI+PHBvbHlnb24gcG9pbnRzPSIwLDAgNSwxMCAxMCwwIiBmaWxsPSIjZjhmYWZjIi8+PC9zdmc+')] bg-repeat-x rotate-180"></div>
                    
                    <div class="p-6">
                        <div class="text-center mb-6 border-b border-slate-100 pb-6 border-dashed">
                            <div class="w-12 h-12 bg-emerald-50 border border-emerald-100 text-emerald-500 mx-auto rounded-full flex items-center justify-center mb-3 shadow-sm">
                                <i class="fa-solid fa-arrow-right-to-bracket text-xl"></i>
                            </div>
                            <h3 class="text-base font-black text-slate-900 tracking-tight">Deposit Modification</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">ID: #INC-{{ str_pad($income->id ?? 0, 5, '0', STR_PAD_LEFT) }}</p>
                        </div>

                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-bold text-slate-400 uppercase tracking-widest text-[10px]">Source</span>
                                <span class="font-bold text-slate-800 truncate max-w-[150px]" x-text="source ? source : 'Pending...'"></span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-bold text-slate-400 uppercase tracking-widest text-[10px]">Classification</span>
                                <span class="font-bold text-indigo-600" x-text="category ? category : 'Pending...'"></span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-bold text-slate-400 uppercase tracking-widest text-[10px]">Date</span>
                                <span class="font-bold text-slate-800 font-mono" x-text="formatDateDisplay()"></span>
                            </div>
                        </div>

                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 flex justify-between items-center">
                            <span class="font-black text-slate-900 uppercase tracking-widest text-[10px]">Revised Total</span>
                            <span class="text-xl font-black text-emerald-600" x-text="formatINR(amount)">₹0.00</span>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm">
                    <button type="submit" :disabled="isSubmitting || amount <= 0 || !source || !category" 
                            class="w-full py-4 bg-emerald-500 text-white rounded-xl font-black shadow-[0_4px_15px_rgba(16,185,129,0.3)] hover:bg-emerald-600 hover:shadow-[0_6px_25px_rgba(16,185,129,0.4)] disabled:opacity-50 disabled:cursor-not-allowed transition-all focus:outline-none flex items-center justify-center gap-3">
                        <span x-show="!isSubmitting"><i class="fa-solid fa-check"></i> Commit Update</span>
                        <span x-show="isSubmitting" style="display: none;"><i class="fa-solid fa-circle-notch animate-spin"></i> Validating...</span>
                    </button>
                </div>

            </div>
        </form>

    </div>

    {{-- Loading Overlay (Simulated Server Processing) --}}
    <div x-show="isSubmitting" style="display: none;" class="fixed inset-0 z-[150] bg-slate-900/60 backdrop-blur-sm flex flex-col items-center justify-center">
        <div class="w-20 h-20 rounded-3xl bg-white flex items-center justify-center shadow-[0_0_40px_rgba(16,185,129,0.4)] mb-6 border-4 border-emerald-500">
            <i class="fa-solid fa-server text-3xl text-emerald-500 animate-pulse"></i>
        </div>
        <h2 class="text-3xl font-black text-white tracking-tight mb-2">Syncing Ledger</h2>
        <p class="text-emerald-200 font-medium">Encrypting and committing changes to database...</p>
    </div>

</div>

@endsection

@push('styles')
<style>
    /* Remove number input arrows for clean SaaS UI */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    input[type=number] { -moz-appearance: textfield; }
    
    /* Entrance Animation */
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
</style>
@endpush

@push('scripts')
<script>
    // Bulletproof Variable Counter logic (replaces buggy setInterval)
    let animationFrameId;
    let currentDisplayVal = 0;
    
    // Global format function matching Alpine
    const formatINR = (n) => {
        let sign = n > 0 ? '+' : (n < 0 ? '-' : '');
        let abs = Math.abs(n);
        return sign + '₹' + abs.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    function triggerVarianceAnimation() {
        const amountInput = document.getElementById('amountInput');
        const displayEl = document.getElementById('varianceDisplay');
        const orig = {{ $originalAmount }};
        
        if(!amountInput || !displayEl) return;

        let targetVal = (parseFloat(amountInput.value) || 0) - orig;
        
        // Cancel previous animation to prevent overlapping spasms
        if (animationFrameId) cancelAnimationFrame(animationFrameId);

        const duration = 400; // ms
        const startTime = performance.now();
        const startVal = currentDisplayVal;

        function animate(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Ease out cubic function for smooth landing
            const easeProgress = 1 - Math.pow(1 - progress, 3);
            
            currentDisplayVal = startVal + (targetVal - startVal) * easeProgress;
            displayEl.innerText = formatINR(currentDisplayVal);

            if (progress < 1) {
                animationFrameId = requestAnimationFrame(animate);
            } else {
                currentDisplayVal = targetVal; // Snap to exact value at end
                displayEl.innerText = formatINR(targetVal);
            }
        }

        animationFrameId = requestAnimationFrame(animate);
    }
</script>
@endpush