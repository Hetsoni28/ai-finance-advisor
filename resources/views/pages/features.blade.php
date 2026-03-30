@extends('layouts.landing')

@section('title', 'Platform Features | FinanceAI Enterprise')
@section('meta_description', 'Explore the cryptographic ledgers, AI forecasting engines, and global telemetry tools powering FinanceAI.')

@section('content')

<div class="bg-[#f8fafc] font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden" x-data="featuresEngine()">

    {{-- ================= 1. HERO SHOWCASE ================= --}}
    <section class="relative pt-40 pb-20 lg:pt-48 lg:pb-32 overflow-hidden bg-white border-b border-slate-200/60">
        
        {{-- Hero Ambient Glows --}}
        <div class="absolute top-[-20%] right-[-10%] w-[800px] h-[800px] bg-indigo-500/10 blur-[120px] rounded-full pointer-events-none"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[600px] h-[600px] bg-emerald-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-50 border border-slate-200 text-slate-600 text-[10px] font-black uppercase tracking-widest mb-8 reveal-up shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Platform Capabilities V3.0
            </div>

            <h1 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tight leading-[1.1] max-w-5xl mx-auto reveal-up" style="transition-delay: 100ms;">
                Engineered for absolute <br class="hidden md:block">
                <span class="relative inline-block mt-2">
                    <span class="relative z-10 bg-gradient-to-r from-indigo-600 to-sky-500 bg-clip-text text-transparent">financial clarity.</span>
                    <span class="absolute bottom-2 left-0 w-full h-4 bg-indigo-100 -z-10 transform -rotate-1"></span>
                </span>
            </h1>

            <p class="mt-8 text-lg md:text-xl text-slate-500 font-medium leading-relaxed max-w-3xl mx-auto reveal-up" style="transition-delay: 200ms;">
                We stripped away the friction of traditional accounting. FinanceAI introduces autonomous ledgers, military-grade encryption, and real-time AI heuristics to scale your wealth globally.
            </p>

        </div>

        {{-- Floating Hero Artifacts (Creates depth) --}}
        <div class="max-w-6xl mx-auto mt-20 relative h-[400px] hidden md:block reveal-up" style="transition-delay: 300ms;">
            {{-- Center Main Artifact --}}
            <div class="absolute left-1/2 top-10 transform -translate-x-1/2 w-[800px] h-[500px] bg-white rounded-t-[3rem] border-t border-l border-r border-slate-200 shadow-[0_-20px_60px_-15px_rgba(0,0,0,0.05)] p-8 overflow-hidden z-20">
                <div class="flex items-center gap-2 mb-8 border-b border-slate-100 pb-4">
                    <div class="w-3 h-3 rounded-full bg-rose-400"></div><div class="w-3 h-3 rounded-full bg-amber-400"></div><div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                </div>
                <div class="grid grid-cols-3 gap-6 h-full">
                    <div class="col-span-2 space-y-4">
                        <div class="h-48 bg-slate-50 rounded-2xl border border-slate-100 flex items-end px-6 gap-3 pb-6 relative overflow-hidden group">
                            {{-- Scanning Line Animation --}}
                            <div class="absolute inset-0 w-full h-1 bg-indigo-400/20 shadow-[0_0_15px_rgba(99,102,241,0.5)] animate-[float_4s_ease-in-out_infinite]"></div>
                            <div class="w-full bg-indigo-500 rounded-t h-[40%] hover:h-[45%] transition-all duration-500 cursor-pointer"></div>
                            <div class="w-full bg-indigo-400 rounded-t h-[70%] hover:h-[75%] transition-all duration-500 cursor-pointer"></div>
                            <div class="w-full bg-indigo-600 rounded-t h-[90%] hover:h-[95%] transition-all duration-500 cursor-pointer"></div>
                            <div class="w-full bg-sky-400 rounded-t h-[60%] hover:h-[65%] transition-all duration-500 cursor-pointer"></div>
                        </div>
                        <div class="h-20 bg-slate-50 rounded-2xl border border-slate-100 flex items-center px-6 gap-4">
                            <div class="h-10 w-10 bg-white rounded-xl shadow-sm border border-slate-200"></div>
                            <div class="space-y-2 flex-1"><div class="h-2 w-1/3 bg-slate-200 rounded"></div><div class="h-2 w-1/4 bg-slate-200 rounded"></div></div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="h-24 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center justify-center">
                            <span class="text-emerald-500 font-black text-2xl tracking-tighter">↑ 18.4%</span>
                        </div>
                        <div class="h-48 bg-slate-50 border border-slate-100 rounded-2xl p-4 flex flex-col gap-3">
                            <div class="flex-1 bg-white rounded-xl border border-slate-100"></div>
                            <div class="flex-1 bg-white rounded-xl border border-slate-100"></div>
                            <div class="flex-1 bg-white rounded-xl border border-slate-100"></div>
                        </div>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-40 bg-gradient-to-t from-white to-transparent z-30"></div>
            </div>

            {{-- Floating Side Cards --}}
            <div class="absolute left-[5%] top-32 w-64 bg-white/80 backdrop-blur-xl border border-white rounded-[2rem] shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] p-6 z-30 animate-[float_6s_ease-in-out_infinite]">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center"><i class="fa-solid fa-arrow-trend-up text-lg"></i></div>
                    <div><div class="h-2 w-16 bg-slate-200 rounded mb-2"></div><div class="h-3 w-24 bg-emerald-500 rounded"></div></div>
                </div>
                <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="w-[70%] h-full bg-emerald-500 relative"><div class="absolute top-0 right-0 bottom-0 w-4 bg-white/30 animate-pulse"></div></div></div>
            </div>

            <div class="absolute right-[5%] top-20 w-72 bg-slate-900/90 backdrop-blur-xl border border-slate-700 rounded-[2rem] shadow-[0_20px_40px_-15px_rgba(0,0,0,0.2)] p-6 z-10 animate-[float_8s_ease-in-out_infinite_reverse]">
                <div class="flex items-center justify-between mb-4">
                    <i class="fa-solid fa-shield-check text-emerald-400 text-2xl"></i>
                    <span class="px-2 py-1 bg-emerald-500/20 text-emerald-400 text-[8px] font-black uppercase tracking-widest rounded border border-emerald-500/30">Encrypted</span>
                </div>
                <div class="space-y-2">
                    <div class="h-2 w-full bg-slate-700 rounded"></div>
                    <div class="h-2 w-4/5 bg-slate-700 rounded"></div>
                </div>
            </div>
        </div>

    </section>

    {{-- ================= 2. THE PARADIGM SHIFT (NEW FUN: COMPARISON) ================= --}}
    <section class="py-32 bg-white relative z-10 border-b border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-20 reveal-up">
                <h2 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight mb-6">A Complete Paradigm Shift</h2>
                <p class="text-slate-500 text-lg font-medium">The legacy financial stack is broken. We rebuilt it from the primitive data structures upward.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 lg:gap-16 items-stretch">
                
                {{-- The Old Way --}}
                <div class="bg-slate-50 rounded-[2.5rem] border border-slate-200 p-10 relative overflow-hidden grayscale hover:grayscale-0 transition-all duration-700 reveal-up">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-rose-500/5 rounded-full blur-2xl"></div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-8 border-b border-slate-200 pb-4">Traditional Software</h3>
                    
                    <ul class="space-y-6">
                        <li class="flex items-start gap-4">
                            <div class="mt-0.5 text-rose-400"><i class="fa-solid fa-xmark"></i></div>
                            <div>
                                <h4 class="font-bold text-slate-700 mb-1">Manual Categorization</h4>
                                <p class="text-sm text-slate-500">Hours spent dragging rows in spreadsheets to calculate basic tax categories.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="mt-0.5 text-rose-400"><i class="fa-solid fa-xmark"></i></div>
                            <div>
                                <h4 class="font-bold text-slate-700 mb-1">Reactive Analytics</h4>
                                <p class="text-sm text-slate-500">Finding out you exceeded your monthly burn rate 15 days after the month ends.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="mt-0.5 text-rose-400"><i class="fa-solid fa-xmark"></i></div>
                            <div>
                                <h4 class="font-bold text-slate-700 mb-1">Fragmented Security</h4>
                                <p class="text-sm text-slate-500">Sharing unencrypted PDF ledgers via email, risking severe data breaches.</p>
                            </div>
                        </li>
                    </ul>
                </div>

                {{-- The FinanceAI Way --}}
                <div class="bg-white rounded-[2.5rem] border-2 border-indigo-50 shadow-[0_20px_60px_-15px_rgba(79,70,229,0.15)] p-10 relative overflow-hidden reveal-up" style="transition-delay: 100ms;">
                    <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -left-20 -top-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
                    
                    <h3 class="text-xs font-black uppercase tracking-widest text-indigo-600 mb-8 border-b border-indigo-100 pb-4 flex items-center gap-2">
                        <i class="fa-solid fa-bolt"></i> FinanceAI Architecture
                    </h3>
                    
                    <ul class="space-y-6 relative z-10">
                        <li class="flex items-start gap-4">
                            <div class="mt-0.5 w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-check text-[10px]"></i></div>
                            <div>
                                <h4 class="font-black text-slate-900 mb-1">Autonomous Tagging</h4>
                                <p class="text-sm text-slate-500 font-medium">Neural networks instantly categorize capital flows the millisecond they hit the ledger.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="mt-0.5 w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-check text-[10px]"></i></div>
                            <div>
                                <h4 class="font-black text-slate-900 mb-1">Predictive Telemetry</h4>
                                <p class="text-sm text-slate-500 font-medium">Live AI projections warn you of future cash-flow deficits before they materialize.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="mt-0.5 w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-check text-[10px]"></i></div>
                            <div>
                                <h4 class="font-black text-slate-900 mb-1">Cryptographic Vaults</h4>
                                <p class="text-sm text-slate-500 font-medium">End-to-end 256-bit encryption with strictly authenticated IAM Role access.</p>
                            </div>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </section>

    {{-- ================= 3. THE CORE BENTO GRID ================= --}}
    <section class="py-32 bg-[#f8fafc] relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-20 reveal-up">
                <h2 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight mb-6">The Architecture of Wealth</h2>
                <p class="text-slate-500 text-lg font-medium">Everything you need to orchestrate capital, built into a single, cohesive, high-performance engine.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 auto-rows-[320px]">
                
                {{-- Bento 1: Cryptographic Ledger (Span 2) --}}
                <div class="md:col-span-2 bg-white rounded-[2.5rem] border border-slate-200 shadow-[0_4px_20px_rgba(0,0,0,0.03)] p-10 relative overflow-hidden group reveal-up">
                    <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative z-10 w-2/3">
                        <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center border border-indigo-100 shadow-sm mb-6"><i class="fa-solid fa-link text-2xl"></i></div>
                        <h3 class="text-3xl font-black text-slate-900 mb-4">Immutable Ledgers</h3>
                        <p class="text-slate-500 font-medium leading-relaxed">Every transaction is hashed and locked. Say goodbye to spreadsheet errors, accidental deletions, and unsynced data. Absolute mathematical truth.</p>
                    </div>
                    {{-- Artifact --}}
                    <div class="absolute right-[-5%] top-[20%] w-[45%] h-full bg-slate-50 rounded-tl-3xl border-t border-l border-slate-200 shadow-xl p-6 group-hover:-translate-x-4 transition-transform duration-500">
                        <div class="space-y-3">
                            <div class="h-12 w-full bg-white rounded-xl border border-slate-200 flex items-center px-4 gap-3 relative overflow-hidden"><div class="absolute inset-0 bg-gradient-to-r from-emerald-400/0 via-emerald-400/10 to-emerald-400/0 transform -translate-x-full group-hover:animate-[shimmer_2s_infinite]"></div><div class="w-6 h-6 rounded-full bg-emerald-100"></div><div class="h-2 w-1/2 bg-slate-200 rounded"></div></div>
                            <div class="h-12 w-full bg-white rounded-xl border border-slate-200 flex items-center px-4 gap-3"><div class="w-6 h-6 rounded-full bg-rose-100"></div><div class="h-2 w-2/3 bg-slate-200 rounded"></div></div>
                            <div class="h-12 w-full bg-white rounded-xl border border-slate-200 flex items-center px-4 gap-3"><div class="w-6 h-6 rounded-full bg-indigo-100"></div><div class="h-2 w-1/3 bg-slate-200 rounded"></div></div>
                        </div>
                    </div>
                </div>

                {{-- Bento 2: Deep Analytics --}}
                <div class="bg-slate-900 rounded-[2.5rem] border border-slate-800 p-10 shadow-2xl relative overflow-hidden group reveal-up" style="transition-delay: 100ms;">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10 mix-blend-overlay pointer-events-none"></div>
                    <div class="relative z-10 h-full flex flex-col justify-between">
                        <div>
                            <div class="w-14 h-14 bg-emerald-500/20 text-emerald-400 rounded-2xl flex items-center justify-center border border-emerald-500/30 mb-6"><i class="fa-solid fa-chart-radar text-2xl group-hover:rotate-12 transition-transform"></i></div>
                            <h3 class="text-2xl font-black text-white mb-3">Live Telemetry</h3>
                        </div>
                        <p class="text-slate-400 font-medium text-sm">Monitor capital velocity in real-time with sub-millisecond database indexing.</p>
                    </div>
                </div>

                {{-- Bento 3: Workspaces --}}
                <div class="bg-gradient-to-br from-indigo-600 to-sky-500 rounded-[2.5rem] p-10 shadow-xl shadow-indigo-500/20 relative overflow-hidden group reveal-up">
                    <div class="absolute top-0 right-0 w-full h-full bg-white/5 pointer-events-none group-hover:scale-110 transition-transform duration-700 rounded-full blur-2xl"></div>
                    <div class="relative z-10 h-full flex flex-col justify-between">
                        <div>
                            <div class="w-14 h-14 bg-white/20 text-white rounded-2xl flex items-center justify-center border border-white/30 backdrop-blur-sm mb-6"><i class="fa-solid fa-network-wired text-2xl group-hover:animate-pulse"></i></div>
                            <h3 class="text-2xl font-black text-white mb-3">Shared Hubs</h3>
                        </div>
                        <p class="text-indigo-100 font-medium text-sm">Deploy isolated environments for family members, accountants, or corporate divisions.</p>
                    </div>
                </div>

                {{-- Bento 4: AI Heuristics (Span 2) --}}
                <div class="md:col-span-2 bg-white rounded-[2.5rem] border border-slate-200 shadow-[0_4px_20px_rgba(0,0,0,0.03)] p-10 relative overflow-hidden group reveal-up" style="transition-delay: 100ms;">
                    <div class="absolute -left-10 -bottom-10 w-64 h-64 bg-rose-500/5 rounded-full blur-3xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="flex flex-col md:flex-row gap-8 h-full relative z-10">
                        <div class="flex-1">
                            <div class="w-14 h-14 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center border border-rose-100 shadow-sm mb-6"><i class="fa-solid fa-brain text-2xl"></i></div>
                            <h3 class="text-3xl font-black text-slate-900 mb-4">Neural Forecasting</h3>
                            <p class="text-slate-500 font-medium leading-relaxed">Our machine learning models ingest your burn rate and instantly project cash-flow constraints up to 36 months into the future.</p>
                        </div>
                        <div class="w-full md:w-1/2 flex items-center justify-center">
                            <div class="w-full h-full bg-slate-50 rounded-2xl border border-slate-100 p-6 flex flex-col justify-center gap-4 relative">
                                <div class="w-full h-1 bg-rose-200 rounded-full relative"><div class="absolute left-1/4 top-1/2 -translate-y-1/2 w-4 h-4 bg-rose-500 rounded-full shadow-[0_0_10px_rgba(244,63,94,0.6)] animate-pulse"></div></div>
                                <div class="text-center"><span class="text-xs font-black text-rose-500 uppercase tracking-widest">Anomaly Detected</span><br><span class="text-[10px] font-bold text-slate-400">Projected runway dip in Q4</span></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ================= 4. TIME SAVED SIMULATOR (NEW FUN!) ================= --}}
    <section class="py-32 bg-indigo-900 relative overflow-hidden text-white">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10 mix-blend-overlay"></div>
        <div class="absolute -right-[20%] -bottom-[20%] w-[800px] h-[800px] bg-indigo-500/40 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute -left-[20%] -top-[20%] w-[600px] h-[600px] bg-sky-500/20 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center reveal-up">
            <h2 class="text-3xl md:text-5xl font-black tracking-tight mb-4">Calculate Your ROI</h2>
            <p class="text-indigo-200 text-lg mb-16 max-w-2xl mx-auto">See exactly how much human capital you recapture by deploying the FinanceAI engine instead of manual ledgers.</p>

            <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-700/50 rounded-[2.5rem] p-8 md:p-12 shadow-2xl relative">
                
                <div class="mb-12">
                    <div class="flex justify-between items-end mb-4">
                        <label class="text-sm font-bold text-slate-300 uppercase tracking-widest">Monthly Transaction Volume</label>
                        <span class="text-3xl font-black text-white font-mono" x-text="volume + ' TX'"></span>
                    </div>
                    {{-- Alpine Range Slider --}}
                    <input type="range" x-model="volume" min="50" max="5000" step="50" class="w-full h-2 bg-slate-700 rounded-lg appearance-none cursor-pointer accent-indigo-500">
                    <style>
                        input[type=range]::-webkit-slider-thumb {
                            -webkit-appearance: none; appearance: none;
                            width: 24px; height: 24px; border-radius: 50%;
                            background: #4f46e5; cursor: pointer;
                            box-shadow: 0 0 15px rgba(79, 70, 229, 0.8);
                            border: 3px solid #fff;
                        }
                    </input>
                    </style>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <div class="bg-white/5 border border-white/10 rounded-[1.5rem] p-8 text-center">
                        <p class="text-[10px] font-black uppercase tracking-widest text-indigo-300 mb-2">Hours Saved Monthly</p>
                        <p class="text-5xl font-black text-emerald-400 font-mono" x-text="Math.round(volume * 0.15) + 'h'"></p>
                        <p class="text-xs text-slate-400 mt-3 font-medium">Reclaimed from manual data entry.</p>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-[1.5rem] p-8 text-center">
                        <p class="text-[10px] font-black uppercase tracking-widest text-indigo-300 mb-2">Capital Retained (Avg)</p>
                        <p class="text-5xl font-black text-sky-400 font-mono" x-text="'$' + (volume * 4.5).toLocaleString()"></p>
                        <p class="text-xs text-slate-400 mt-3 font-medium">Calculated at $30/hr administrative rate.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ================= 5. INTERACTIVE "STRIPE-STYLE" TABS ================= --}}
    <section class="py-32 bg-white border-y border-slate-200/60 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-20 reveal-up">
                <h2 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight">Granular Control. <br><span class="text-indigo-600">Infinite Possibility.</span></h2>
            </div>

            <div class="grid lg:grid-cols-12 gap-12 items-center">
                
                {{-- Left: Tab Navigation --}}
                <div class="lg:col-span-5 space-y-4 reveal-up" style="transition-delay: 100ms;">
                    
                    {{-- Tab 1 --}}
                    <button @click="activeTab = 'automation'" class="w-full text-left p-6 rounded-[2rem] border transition-all duration-300 focus:outline-none relative group overflow-hidden"
                            :class="activeTab === 'automation' ? 'bg-indigo-50/50 border-indigo-200 shadow-sm' : 'bg-transparent border-transparent hover:bg-slate-50'">
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-12 bg-indigo-500 rounded-r-full transition-transform duration-300" :class="activeTab === 'automation' ? 'scale-y-100' : 'scale-y-0'"></div>
                        <h4 class="text-xl font-black mb-2 transition-colors" :class="activeTab === 'automation' ? 'text-indigo-600' : 'text-slate-900 group-hover:text-indigo-600'">Smart Automation</h4>
                        <p class="text-sm font-medium text-slate-500 leading-relaxed">Set up recurring cryptographic rules to automatically route capital, categorize expenses, and trigger alerts based on custom thresholds.</p>
                    </button>

                    {{-- Tab 2 --}}
                    <button @click="activeTab = 'security'" class="w-full text-left p-6 rounded-[2rem] border transition-all duration-300 focus:outline-none relative group overflow-hidden"
                            :class="activeTab === 'security' ? 'bg-indigo-50/50 border-indigo-200 shadow-sm' : 'bg-transparent border-transparent hover:bg-slate-50'">
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-12 bg-indigo-500 rounded-r-full transition-transform duration-300" :class="activeTab === 'security' ? 'scale-y-100' : 'scale-y-0'"></div>
                        <h4 class="text-xl font-black mb-2 transition-colors" :class="activeTab === 'security' ? 'text-indigo-600' : 'text-slate-900 group-hover:text-indigo-600'">Identity & Access (IAM)</h4>
                        <p class="text-sm font-medium text-slate-500 leading-relaxed">Assign precise Roles and Permissions to hub members. Ensure accountants only see telemetry, while admins retain full destructive rights.</p>
                    </button>

                    {{-- Tab 3 --}}
                    <button @click="activeTab = 'export'" class="w-full text-left p-6 rounded-[2rem] border transition-all duration-300 focus:outline-none relative group overflow-hidden"
                            :class="activeTab === 'export' ? 'bg-indigo-50/50 border-indigo-200 shadow-sm' : 'bg-transparent border-transparent hover:bg-slate-50'">
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-12 bg-indigo-500 rounded-r-full transition-transform duration-300" :class="activeTab === 'export' ? 'scale-y-100' : 'scale-y-0'"></div>
                        <h4 class="text-xl font-black mb-2 transition-colors" :class="activeTab === 'export' ? 'text-indigo-600' : 'text-slate-900 group-hover:text-indigo-600'">Universal Export</h4>
                        <p class="text-sm font-medium text-slate-500 leading-relaxed">Generate cryptographically verified PDF audits and CSV data dumps with a single click for tax compliance and legal review.</p>
                    </button>

                </div>

                {{-- Right: Interactive Window Sandbox --}}
                <div class="lg:col-span-7 reveal-up" style="transition-delay: 200ms;">
                    <div class="w-full h-[500px] bg-slate-900 rounded-[2.5rem] border border-slate-800 shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)] relative overflow-hidden flex flex-col">
                        
                        {{-- Window Header --}}
                        <div class="h-14 bg-slate-800/50 border-b border-slate-700/50 flex items-center px-6 gap-2 shrink-0">
                            <div class="w-3 h-3 rounded-full bg-rose-500/80"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-500/80"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-500/80"></div>
                            <div class="ml-4 px-4 py-1 bg-slate-900 rounded-md border border-slate-700 flex-1 flex items-center gap-2">
                                <i class="fa-solid fa-lock text-[10px] text-slate-500"></i>
                                <span class="text-[10px] font-mono text-slate-400">app.financeai.com/console</span>
                            </div>
                        </div>

                        {{-- Window Body (Alpine x-show toggles) --}}
                        <div class="flex-1 relative p-8">
                            
                            {{-- View 1: Automation --}}
                            <div x-show="activeTab === 'automation'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0" class="absolute inset-8">
                                <div class="space-y-4">
                                    <div class="bg-slate-800 rounded-xl p-4 border border-slate-700 flex items-center justify-between shadow-inner">
                                        <div class="flex items-center gap-3"><i class="fa-solid fa-bolt text-amber-400"></i> <span class="text-white font-bold text-sm">If Inflow > ₹50,000</span></div>
                                        <i class="fa-solid fa-arrow-right text-slate-500"></i>
                                        <div class="flex items-center gap-3"><span class="text-white font-bold text-sm">Tag as 'Corporate Salary'</span> <i class="fa-solid fa-tag text-indigo-400"></i></div>
                                    </div>
                                    <div class="bg-slate-800 rounded-xl p-4 border border-slate-700 flex items-center justify-between opacity-50 shadow-inner">
                                        <div class="flex items-center gap-3"><i class="fa-solid fa-bolt text-amber-400"></i> <span class="text-white font-bold text-sm">If Outflow hits Netflix</span></div>
                                        <i class="fa-solid fa-arrow-right text-slate-500"></i>
                                        <div class="flex items-center gap-3"><span class="text-white font-bold text-sm">Route to 'Entertainment'</span> <i class="fa-solid fa-film text-rose-400"></i></div>
                                    </div>
                                    <div class="w-full py-4 border-2 border-dashed border-slate-700 rounded-xl flex items-center justify-center text-slate-500 font-bold text-xs uppercase tracking-widest cursor-pointer hover:bg-slate-800 hover:text-slate-400 transition-colors">
                                        + Add Logic Trigger
                                    </div>
                                </div>
                            </div>

                            {{-- View 2: Security --}}
                            <div x-show="activeTab === 'security'" style="display:none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0" class="absolute inset-8">
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center border-b border-slate-700 pb-4">
                                        <div><h5 class="text-white font-bold">Node Administrator</h5><p class="text-xs text-slate-400 font-mono">Full Read/Write Access</p></div>
                                        <div class="w-10 h-6 bg-emerald-500 rounded-full relative"><div class="absolute right-1 top-1 w-4 h-4 bg-white rounded-full shadow-sm"></div></div>
                                    </div>
                                    <div class="flex justify-between items-center border-b border-slate-700 pb-4">
                                        <div><h5 class="text-white font-bold">Financial Auditor</h5><p class="text-xs text-slate-400 font-mono">Read-Only Telemetry</p></div>
                                        <div class="w-10 h-6 bg-slate-700 rounded-full relative"><div class="absolute left-1 top-1 w-4 h-4 bg-slate-400 rounded-full shadow-sm"></div></div>
                                    </div>
                                    <div class="flex justify-between items-center border-b border-slate-700 pb-4">
                                        <div><h5 class="text-white font-bold">Data Entry Clerk</h5><p class="text-xs text-slate-400 font-mono">Write-Only Ledgers</p></div>
                                        <div class="w-10 h-6 bg-slate-700 rounded-full relative"><div class="absolute left-1 top-1 w-4 h-4 bg-slate-400 rounded-full shadow-sm"></div></div>
                                    </div>
                                </div>
                            </div>

                            {{-- View 3: Export --}}
                            <div x-show="activeTab === 'export'" style="display:none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0" class="absolute inset-8 flex flex-col items-center justify-center">
                                <div class="w-24 h-24 bg-indigo-500/20 text-indigo-400 rounded-[2rem] flex items-center justify-center border border-indigo-500/30 mb-6 animate-pulse">
                                    <i class="fa-solid fa-file-pdf text-4xl"></i>
                                </div>
                                <h4 class="text-white font-black text-xl">Tax Audit Generation</h4>
                                <p class="text-slate-400 text-sm mt-2 text-center max-w-xs">Compiling FY2025-2026 cryptographic ledger into a legally compliant PDF structure.</p>
                                <div class="w-48 h-1.5 bg-slate-800 rounded-full mt-6 overflow-hidden"><div class="h-full bg-indigo-500 w-2/3 shadow-[0_0_10px_rgba(99,102,241,0.8)]"></div></div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ================= 6. DEVELOPER API TERMINAL (NATIVE FIX) ================= --}}
    <section class="py-32 bg-slate-900 relative overflow-hidden" id="terminal-container">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10 mix-blend-overlay"></div>
        <div class="absolute -right-[20%] -bottom-[20%] w-[800px] h-[800px] bg-indigo-600/20 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 grid lg:grid-cols-2 gap-16 items-center">
            
            <div class="reveal-up">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-[10px] font-black uppercase tracking-widest mb-6">
                    <i class="fa-solid fa-code"></i> Developer API
                </div>
                <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight leading-tight mb-6">
                    Built for Scale. <br> Designed for Engineers.
                </h2>
                <p class="text-slate-400 text-lg leading-relaxed mb-8">
                    Every feature in the FinanceAI UI is powered by our enterprise REST API. Build custom internal tools, sync with legacy banking mainframes, or write your own automation scripts with secure Bearer tokens.
                </p>
                <a href="#" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-slate-900 font-black text-xs uppercase tracking-widest rounded-xl hover:bg-indigo-50 transition-colors focus:outline-none">
                    Read the Docs <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                </a>
            </div>

            {{-- The Typing Terminal Simulator (Bug Fixed) --}}
            <div class="w-full bg-black/80 backdrop-blur-xl border border-slate-700/50 rounded-2xl shadow-2xl p-6 font-mono text-[13px] leading-relaxed relative reveal-up" style="transition-delay: 200ms;">
                <div class="flex gap-2 mb-4">
                    <div class="w-3 h-3 rounded-full bg-rose-500"></div><div class="w-3 h-3 rounded-full bg-amber-500"></div><div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                </div>
                <div class="text-emerald-400 mb-2">admin@finance-ai:~$ <span x-show="!terminalTriggered" class="animate-pulse">_</span></div>
                
                {{-- Typing Target --}}
                <div id="terminalCommand" class="text-white inline"></div><span x-show="terminalTriggered && !showTerminalResponse" class="text-white animate-pulse">_</span>

                <div x-show="showTerminalResponse" x-transition.opacity style="display:none;" class="mt-4 pt-4 border-t border-slate-800">
                    <span class="text-sky-400">HTTP/2 201 Created</span><br>
                    <span class="text-slate-300">{</span><br>
                    <span class="text-slate-300">&nbsp;&nbsp;"status": <span class="text-emerald-400">"success"</span>,</span><br>
                    <span class="text-slate-300">&nbsp;&nbsp;"ledger_id": <span class="text-amber-400">"ld_9x8f7a6b5c"</span>,</span><br>
                    <span class="text-slate-300">&nbsp;&nbsp;"amount": <span class="text-amber-400">50000.00</span>,</span><br>
                    <span class="text-slate-300">&nbsp;&nbsp;"cryptographic_hash": <span class="text-emerald-400">"0x..."</span></span><br>
                    <span class="text-slate-300">}</span><br>
                    <span class="text-emerald-400 mt-2 block">admin@finance-ai:~$ <span class="animate-pulse text-white">_</span></span>
                </div>
            </div>

        </div>
    </section>

</div>

@endsection

@push('scripts')
<style>
    /* Floating Animations for Artifacts */
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }
    @keyframes shimmer {
        100% { transform: translateX(100%); }
    }
</style>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('featuresEngine', () => ({
        activeTab: 'automation',
        volume: 500, // For ROI Calculator
        
        terminalTriggered: false,
        showTerminalResponse: false,

        init() {
            // 🚨 FIX: Native IntersectionObserver replaces broken x-intersect plugin
            const observer = new IntersectionObserver((entries) => {
                if(entries[0].isIntersecting && !this.terminalTriggered) {
                    this.terminalTriggered = true;
                    this.typeTerminal();
                }
            }, { threshold: 0.5 });
            
            const terminalContainer = document.getElementById('terminal-container');
            if(terminalContainer) observer.observe(terminalContainer);
        },

        typeTerminal() {
            const text = "curl -X POST https://api.financeai.com/v1/inflow \\\n  -H \"Authorization: Bearer sk_live_...\" \\\n  -d '{\"amount\": 50000, \"category\": \"revenue\"}'";
            let i = 0;
            const el = document.getElementById("terminalCommand");
            
            const typeWriter = () => {
                if (i < text.length) {
                    if(text.charAt(i) === '\n') {
                        el.innerHTML += '<br>';
                    } else if (text.charAt(i) === ' ') {
                        el.innerHTML += '&nbsp;';
                    } else {
                        el.innerHTML += text.charAt(i);
                    }
                    i++;
                    setTimeout(typeWriter, Math.random() * 25 + 10); 
                } else {
                    setTimeout(() => {
                        this.showTerminalResponse = true;
                    }, 600);
                }
            };
            
            setTimeout(typeWriter, 400); 
        }
    }));
});
</script>
@endpush