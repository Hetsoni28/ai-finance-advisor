@extends('layouts.app')

@section('title', 'Manage Subscription - FinanceAI')

@section('content')

@php
    $user = auth()->user();
    
    // Safe fallback for user properties
    $currentPlan = strtolower($user->plan ?? 'starter');
    $currentBilling = strtolower($user->billing_cycle ?? 'monthly');
    
    // Simulated Usage Data (Passed from Controller in production)
    $usage = [
        'ai_queries' => ['used' => 840, 'total' => 1000, 'percent' => 84],
        'api_calls'  => ['used' => 12500, 'total' => 100000, 'percent' => 12.5],
    ];

    // Simulated Payment Method
    $paymentMethod = [
        'brand' => 'visa',
        'last4' => '4242',
        'exp'   => '12/28',
        'status'=> 'valid'
    ];

    // Simulated Invoice History
    $invoices = [
        ['id' => 'INV-2026-004', 'date' => 'Mar 01, 2026', 'amount' => '₹199.00', 'status' => 'Paid', 'plan' => 'Pro Advisor'],
        ['id' => 'INV-2026-003', 'date' => 'Feb 01, 2026', 'amount' => '₹199.00', 'status' => 'Paid', 'plan' => 'Pro Advisor'],
        ['id' => 'INV-2026-002', 'date' => 'Jan 01, 2026', 'amount' => '₹0.00', 'status' => 'Paid', 'plan' => 'Starter'],
    ];
@endphp

<div class="min-h-screen bg-[#f8fafc] pb-32 font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden">
    
    {{-- Ambient Light Backgrounds --}}
    <div class="absolute top-[-20%] left-[-10%] w-[800px] h-[800px] bg-indigo-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="absolute top-[40%] right-[-10%] w-[600px] h-[600px] bg-sky-500/5 rounded-full blur-[100px] pointer-events-none z-0"></div>
    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCI+PHBhdGggZD0iTTAgMGg0MHY0MEgwVjB6bTIwIDIwaDIwdjIwSDIwaC0yMHptMCAwaC0yMHYtMjBoMjB2MjB6IiBmaWxsPSIjZThlYWVkIiBmaWxsLW9wYWNpdHk9IjAuMDUiIGZpbGwtcnVsZT0iZXZlbm9kZCIvPjwvc3ZnPg==')] opacity-100 pointer-events-none z-0"></div>

    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-16 relative z-10 space-y-12">

        {{-- ================= 1. PAGE HEADER ================= --}}
        <div class="text-center max-w-3xl mx-auto space-y-6 animate-fade-in-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white border border-slate-200 text-slate-500 text-xs font-black tracking-widest uppercase shadow-sm">
                <i class="fa-solid fa-credit-card text-indigo-500"></i> Subscription Management
            </span>
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                Scale your financial intelligence.
            </h1>
            <p class="text-lg text-slate-500 font-medium">
                Cancel anytime. Secure billing powered by Stripe. No hidden fees.
            </p>
        </div>

        {{-- ================= SUCCESS/ERROR NOTIFICATIONS ================= --}}
        @if(session('success'))
            <div id="sysAlert" class="bg-emerald-50 border border-emerald-200 rounded-[1.5rem] p-4 flex items-center justify-between shadow-sm max-w-3xl mx-auto animate-fade-in-up">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-[12px] bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-emerald-500/30 border border-emerald-400"><i class="fa-solid fa-check text-sm"></i></div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-emerald-600 mb-0.5">System Notice</p>
                        <p class="text-sm font-bold text-slate-700 leading-tight">{{ session('success') }}</p>
                    </div>
                </div>
                <button onclick="document.getElementById('sysAlert').remove()" class="text-emerald-600 hover:bg-emerald-100 w-8 h-8 rounded-lg flex items-center justify-center transition-colors focus:outline-none"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        {{-- ================= 2. ACTIVE HUB DASHBOARD (USAGE & PAYMENT) ================= --}}
        <div class="bg-white/90 backdrop-blur-xl rounded-[2.5rem] p-8 md:p-10 border border-white shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] animate-fade-in-up" style="animation-delay: 100ms;">
            <div class="grid lg:grid-cols-12 gap-10 items-stretch">
                
                {{-- Active Plan Status --}}
                <div class="lg:col-span-4 flex flex-col justify-between border-b lg:border-b-0 lg:border-r border-slate-100 pb-8 lg:pb-0 lg:pr-8">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2"><i class="fa-solid fa-bolt text-amber-400"></i> Active Node</p>
                        <div class="flex items-end gap-3 mb-4">
                            <h2 class="text-4xl font-black text-slate-900 capitalize tracking-tight">{{ $currentPlan }}</h2>
                            @if($currentPlan === 'starter')
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-slate-200 mb-1.5 shadow-sm">Free</span>
                            @else
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-emerald-200 mb-1.5 shadow-sm">Active</span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-500 font-medium leading-relaxed">
                            @if($currentPlan === 'starter')
                                You are currently operating on the free tier. Upgrade to Pro to unlock algorithmic forecasting.
                            @else
                                Your node is fully synchronized. Next billing cycle resolves on <strong class="text-slate-700">{{ now()->addDays(14)->format('M d, Y') }}</strong>.
                            @endif
                        </p>
                    </div>
                    
                    @if($currentPlan !== 'starter')
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-[1.25rem] border border-slate-200 group hover:border-indigo-200 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-8 bg-white rounded border border-slate-200 shadow-sm flex items-center justify-center text-indigo-600 font-black italic text-lg">
                                    VISA
                                </div>
                                <div>
                                    <p class="text-xs font-black text-slate-900 tracking-tight">•••• {{ $paymentMethod['last4'] }}</p>
                                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-0.5">Exp {{ $paymentMethod['exp'] }}</p>
                                </div>
                            </div>
                            <button onclick="openPaymentModal()" onmouseenter="audioEngine.playHover()" class="w-8 h-8 rounded-full bg-white border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-200 shadow-sm flex items-center justify-center transition-all focus:outline-none hover:-translate-y-0.5" title="Update Payment Method">
                                <i class="fa-solid fa-pen text-xs"></i>
                            </button>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Live Usage Quotas --}}
                <div class="lg:col-span-8 flex flex-col justify-center gap-8">
                    
                    {{-- AI Queries --}}
                    <div class="group">
                        <div class="flex justify-between items-end mb-3">
                            <div>
                                <span class="text-xs font-black text-slate-900 flex items-center gap-2"><i class="fa-solid fa-brain text-indigo-500"></i> AI Heuristic Queries</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1 block">Monthly Token Limit</span>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-black text-indigo-600 tabular-nums">{{ number_format($usage['ai_queries']['used']) }}</span>
                                <span class="text-xs font-bold text-slate-400">/ {{ number_format($usage['ai_queries']['total']) }}</span>
                            </div>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden border border-slate-200/50 shadow-inner">
                            @php $aiColor = $usage['ai_queries']['percent'] > 85 ? 'bg-rose-500' : ($usage['ai_queries']['percent'] > 60 ? 'bg-amber-500' : 'bg-indigo-500'); @endphp
                            <div class="{{ $aiColor }} h-full rounded-full transition-all duration-1000 ease-out relative overflow-hidden" style="width: {{ $usage['ai_queries']['percent'] }}%">
                                <div class="absolute inset-0 bg-white/20 w-full animate-[shimmer_2s_infinite] translate-x-[-100%]" style="background-image: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);"></div>
                            </div>
                        </div>
                        @if($usage['ai_queries']['percent'] >= 80)
                            <p class="text-[10px] text-rose-500 font-black uppercase tracking-widest mt-2.5 flex items-center gap-1.5"><i class="fa-solid fa-triangle-exclamation"></i> Approaching compute threshold.</p>
                        @endif
                    </div>

                    {{-- API Calls --}}
                    <div class="group">
                        <div class="flex justify-between items-end mb-3">
                            <div>
                                <span class="text-xs font-black text-slate-900 flex items-center gap-2"><i class="fa-solid fa-network-wired text-sky-500"></i> Master Database Syncs</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1 block">Read/Write Operations</span>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-black text-sky-600 tabular-nums">{{ number_format($usage['api_calls']['used']) }}</span>
                                <span class="text-xs font-bold text-slate-400">/ {{ number_format($usage['api_calls']['total']) }}</span>
                            </div>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden border border-slate-200/50 shadow-inner">
                            @php $apiColor = $usage['api_calls']['percent'] > 85 ? 'bg-rose-500' : ($usage['api_calls']['percent'] > 60 ? 'bg-amber-500' : 'bg-sky-500'); @endphp
                            <div class="{{ $apiColor }} h-full rounded-full transition-all duration-1000 ease-out relative overflow-hidden" style="width: {{ $usage['api_calls']['percent'] }}%">
                                <div class="absolute inset-0 bg-white/20 w-full animate-[shimmer_2s_infinite] translate-x-[-100%]" style="background-image: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= 3. PRICING CARDS ================= --}}
        <div class="pt-8 animate-fade-in-up" style="animation-delay: 200ms;">
            
            {{-- FLAWLESS VANILLA JS BILLING TOGGLE --}}
            <div class="flex justify-center mb-12">
                <div class="relative inline-flex bg-white border border-slate-200 rounded-2xl p-1.5 shadow-sm">
                    {{-- Physical Sliding Indicator --}}
                    <div id="indicator" class="absolute top-1.5 bottom-1.5 left-1.5 w-[calc(50%-0.375rem)] bg-slate-900 rounded-[10px] shadow-[0_5px_15px_rgba(15,23,42,0.3)] transition-transform duration-400 ease-out z-0"></div>
                    
                    <button type="button" id="btnMonthly" onmouseenter="audioEngine.playHover()" class="relative z-10 w-44 py-3 text-xs font-black uppercase tracking-widest text-white transition-colors duration-300 focus:outline-none">
                        Pay Monthly
                    </button>
                    <button type="button" id="btnYearly" onmouseenter="audioEngine.playHover()" class="relative z-10 w-44 py-3 text-xs font-black uppercase tracking-widest text-slate-500 transition-colors duration-300 focus:outline-none flex items-center justify-center gap-2">
                        Pay Yearly <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700 text-[8px] uppercase tracking-widest border border-emerald-200 shadow-sm">-20%</span>
                    </button>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-8 items-center max-w-6xl mx-auto">

                {{-- STARTER TIER --}}
                <div class="bg-white/80 backdrop-blur-md border border-slate-200 rounded-[2.5rem] p-10 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 relative overflow-hidden group">
                    <div class="relative z-10">
                        <h3 class="text-2xl font-black text-slate-900 tracking-tight">Starter</h3>
                        <p class="text-sm font-medium text-slate-500 mt-3 h-10 leading-relaxed">Secure, baseline financial telemetry for personal use.</p>

                        <div class="my-8 border-b border-slate-100 pb-8">
                            <span class="text-5xl font-black text-slate-900 tracking-tighter">₹0</span>
                            <span class="text-slate-400 font-bold text-sm">/mo</span>
                        </div>

                        @if($currentPlan === 'starter')
                            <button disabled class="w-full py-4 rounded-xl bg-slate-100 border border-slate-200 text-slate-400 font-black uppercase tracking-widest text-[11px] shadow-inner cursor-not-allowed">
                                Current Active Plan
                            </button>
                        @else
                            <form method="POST" action="{{ Route::has('profile.subscription.upgrade') ? route('profile.subscription.upgrade') : '#' }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="plan" value="starter">
                                <button type="submit" onmouseenter="audioEngine.playHover()" class="w-full py-4 rounded-xl bg-white border-2 border-slate-900 text-slate-900 font-black uppercase tracking-widest text-[11px] hover:bg-slate-900 hover:text-white transition-all focus:outline-none hover:-translate-y-0.5">
                                    Downgrade to Starter
                                </button>
                            </form>
                        @endif

                        <ul class="mt-8 space-y-4 text-sm font-medium text-slate-600">
                            <li class="flex items-start gap-3">
                                <div class="w-5 h-5 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center mt-0.5 shrink-0 border border-emerald-100"><i class="fa-solid fa-check text-[10px]"></i></div>
                                <span>100 DB Syncs / minute</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="w-5 h-5 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center mt-0.5 shrink-0 border border-emerald-100"><i class="fa-solid fa-check text-[10px]"></i></div>
                                <span>Manual Ledger Audits</span>
                            </li>
                            <li class="flex items-start gap-3 opacity-40">
                                <div class="w-5 h-5 rounded-md bg-slate-100 text-slate-400 flex items-center justify-center mt-0.5 shrink-0"><i class="fa-solid fa-xmark text-[10px]"></i></div>
                                <span>AI Predictive Forecasting</span>
                            </li>
                            <li class="flex items-start gap-3 opacity-40">
                                <div class="w-5 h-5 rounded-md bg-slate-100 text-slate-400 flex items-center justify-center mt-0.5 shrink-0"><i class="fa-solid fa-xmark text-[10px]"></i></div>
                                <span>Multi-Node Collaboration</span>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- PRO ADVISOR TIER (THE BEAST) --}}
                <div class="relative rounded-[2.5rem] p-[2px] shadow-[0_20px_50px_-12px_rgba(79,70,229,0.4)] transform lg:scale-105 z-10 overflow-hidden group">
                    
                    {{-- FLAWLESS Animated CSS Border --}}
                    <div class="absolute inset-0 bg-slate-900 z-0"></div>
                    <div class="absolute inset-[-50%] bg-[conic-gradient(from_0deg,transparent_0_340deg,#4f46e5_360deg)] animate-[spin_3s_linear_infinite] z-0 opacity-50"></div>
                    
                    {{-- Inner Card Container --}}
                    <div class="relative bg-white h-full w-full rounded-[2.4rem] p-10 z-10 flex flex-col">
                        <div class="absolute top-0 inset-x-0 flex justify-center">
                            <span class="bg-indigo-600 text-white text-[9px] font-black uppercase tracking-widest px-4 py-1.5 rounded-b-xl shadow-md border border-indigo-500 border-t-0">Highly Recommended</span>
                        </div>

                        <div class="relative z-10 mt-4">
                            <h3 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">Pro Advisor <i class="fa-solid fa-sparkles text-indigo-500 text-sm"></i></h3>
                            <p class="text-sm font-medium text-slate-500 mt-3 h-10 leading-relaxed">Full automated AI financial intelligence and forecasting.</p>

                            <div class="my-8 border-b border-slate-100 pb-8 flex items-end h-16">
                                <span id="proPrice" data-monthly="199" data-yearly="1999" class="text-5xl font-black text-indigo-600 tracking-tighter transition-all">
                                    ₹{{ $currentBilling === 'yearly' ? '1999' : '199' }}
                                </span>
                                <span id="billingText" class="text-slate-400 font-bold text-sm ml-1 mb-1.5 transition-all">
                                    /{{ $currentBilling === 'yearly' ? 'yr' : 'mo' }}
                                </span>
                            </div>

                            @if($currentPlan === 'pro')
                                <button disabled class="w-full py-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 font-black uppercase tracking-widest text-[11px] cursor-not-allowed flex items-center justify-center gap-2 shadow-inner">
                                    <i class="fa-solid fa-shield-check"></i> Active Pro Node
                                </button>
                            @else
                                <form method="POST" action="{{ Route::has('profile.subscription.upgrade') ? route('profile.subscription.upgrade') : '#' }}" id="proUpgradeForm">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="plan" value="pro">
                                    <input type="hidden" name="billing" id="billingCycle" value="{{ $currentBilling }}">
                                    <button type="submit" onclick="simulateStripeCheckout(event, this)" onmouseenter="audioEngine.playHover()" class="w-full py-4 rounded-xl bg-slate-900 text-white font-black uppercase tracking-widest text-[11px] hover:bg-indigo-600 shadow-md hover:shadow-xl hover:shadow-indigo-500/30 transition-all flex items-center justify-center gap-2 hover:-translate-y-0.5 focus:outline-none relative overflow-hidden">
                                        <div class="absolute inset-0 bg-white/20 w-full animate-[shimmer_2s_infinite] translate-x-[-100%]" style="background-image: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);"></div>
                                        <span class="relative z-10">Initialize Upgrade</span>
                                    </button>
                                </form>
                            @endif

                            <ul class="mt-8 space-y-4 text-sm font-medium text-slate-700">
                                <li class="flex items-start gap-3">
                                    <div class="w-5 h-5 rounded-md bg-indigo-50 text-indigo-600 flex items-center justify-center mt-0.5 shrink-0 border border-indigo-100"><i class="fa-solid fa-check text-[10px]"></i></div>
                                    <span>Everything in Starter</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="w-5 h-5 rounded-md bg-indigo-50 text-indigo-600 flex items-center justify-center mt-0.5 shrink-0 border border-indigo-100"><i class="fa-solid fa-brain text-[10px]"></i></div>
                                    <span class="font-bold text-slate-900">AI Predictive Forecasting</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="w-5 h-5 rounded-md bg-indigo-50 text-indigo-600 flex items-center justify-center mt-0.5 shrink-0 border border-indigo-100"><i class="fa-solid fa-users text-[10px]"></i></div>
                                    <span class="font-bold text-slate-900">Family Collaboration (5 Nodes)</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="w-5 h-5 rounded-md bg-indigo-50 text-indigo-600 flex items-center justify-center mt-0.5 shrink-0 border border-indigo-100"><i class="fa-solid fa-file-pdf text-[10px]"></i></div>
                                    <span>Automated PDF Intel Reports</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- ENTERPRISE TIER --}}
                <div class="bg-white/80 backdrop-blur-md border border-slate-200 rounded-[2.5rem] p-10 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-slate-100 rounded-full blur-3xl group-hover:bg-slate-200 transition-colors z-0"></div>
                    <div class="relative z-10 opacity-70">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-2xl font-black text-slate-900 tracking-tight">Enterprise</h3>
                            <span class="px-2.5 py-1 bg-slate-900 text-white rounded border border-slate-700 text-[8px] font-black uppercase tracking-widest shadow-sm flex items-center gap-1.5"><span class="w-1 h-1 rounded-full bg-rose-500 animate-pulse"></span> Beta</span>
                        </div>
                        <p class="text-sm font-medium text-slate-500 h-10 leading-relaxed">Dedicated API architecture and raw data access.</p>

                        <div class="my-8 border-b border-slate-100 pb-8 flex items-end h-16">
                            <span class="text-5xl font-black text-slate-900 tracking-tighter">₹499</span>
                            <span class="text-slate-400 font-bold text-sm ml-1 mb-1.5">/mo</span>
                        </div>

                        <button disabled onmouseenter="audioEngine.playHover()" class="w-full py-4 rounded-xl bg-white border-2 border-slate-200 text-slate-400 font-black uppercase tracking-widest text-[11px] transition-colors focus:outline-none cursor-not-allowed">
                            <i class="fa-solid fa-envelope mr-1"></i> Join Waitlist
                        </button>

                        <ul class="mt-8 space-y-4 text-sm font-medium text-slate-600">
                            <li class="flex items-start gap-3">
                                <div class="w-5 h-5 rounded-md bg-slate-100 text-slate-500 flex items-center justify-center mt-0.5 shrink-0"><i class="fa-solid fa-server text-[10px]"></i></div>
                                Dedicated Database Instance
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="w-5 h-5 rounded-md bg-slate-100 text-slate-500 flex items-center justify-center mt-0.5 shrink-0"><i class="fa-solid fa-code text-[10px]"></i></div>
                                RESTful API Access Key
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        {{-- ================= 4. ENTERPRISE FEATURE COMPARISON ================= --}}
        <div class="pt-24 hidden lg:block animate-fade-in-up" style="animation-delay: 300ms;">
            <h3 class="text-3xl font-black text-slate-900 mb-8 text-center tracking-tight">Compare Architecture</h3>
            <div class="bg-white rounded-[2rem] border border-slate-200 shadow-[0_10px_30px_rgba(0,0,0,0.03)] overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 bg-white/90 backdrop-blur-md z-20">
                        <tr class="border-b border-slate-200 text-slate-900 font-black">
                            <th class="p-6 text-sm uppercase tracking-widest text-slate-400 w-1/3">Technical Features</th>
                            <th class="p-6 text-lg text-center w-[22%] border-x border-slate-100">Starter</th>
                            <th class="p-6 text-xl text-center w-[22%] text-indigo-600 bg-indigo-50/50 shadow-[inset_0_-2px_0_theme(colors.indigo.500)]">Pro Advisor</th>
                            <th class="p-6 text-lg text-center w-[22%] border-l border-slate-100 text-slate-500">Enterprise</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-700 font-bold text-sm">
                        <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                            <td class="p-6 font-medium text-slate-500">MySQL 8 Strict Sync</td>
                            <td class="p-6 text-center border-x border-slate-100"><i class="fa-solid fa-check text-slate-300"></i></td>
                            <td class="p-6 text-center bg-indigo-50/20"><div class="w-6 h-6 mx-auto rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center"><i class="fa-solid fa-check text-[10px]"></i></div></td>
                            <td class="p-6 text-center border-l border-slate-100"><i class="fa-solid fa-check text-slate-400"></i></td>
                        </tr>
                        <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                            <td class="p-6 font-medium text-slate-500">AI Pattern Recognition</td>
                            <td class="p-6 text-center text-slate-300 border-x border-slate-100">—</td>
                            <td class="p-6 text-center bg-indigo-50/20"><div class="w-6 h-6 mx-auto rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center"><i class="fa-solid fa-check text-[10px]"></i></div></td>
                            <td class="p-6 text-center border-l border-slate-100"><i class="fa-solid fa-check text-slate-400"></i></td>
                        </tr>
                        <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                            <td class="p-6 font-medium text-slate-500">Node API Rate Limit</td>
                            <td class="p-6 text-center text-slate-400 font-mono text-[11px] border-x border-slate-100">100 / min</td>
                            <td class="p-6 text-center bg-indigo-50/20 text-indigo-600 font-mono font-black text-xs">10,000 / min</td>
                            <td class="p-6 text-center text-slate-700 font-mono text-[11px] border-l border-slate-100">Unlimited</td>
                        </tr>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-6 font-medium text-slate-500">Cryptographic Export</td>
                            <td class="p-6 text-center text-slate-300 border-x border-slate-100">—</td>
                            <td class="p-6 text-center bg-indigo-50/20"><div class="w-6 h-6 mx-auto rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center"><i class="fa-solid fa-check text-[10px]"></i></div></td>
                            <td class="p-6 text-center border-l border-slate-100"><i class="fa-solid fa-check text-slate-400"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= 5. BILLING HISTORY TABLE ================= --}}
        <div class="pt-16 animate-fade-in-up" style="animation-delay: 400ms;">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-black text-slate-900 tracking-tight">Ledger Archives</h3>
            </div>
            <div class="bg-white rounded-[2rem] border border-slate-200 shadow-[0_8px_30px_rgb(0,0,0,0.03)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-100 text-[10px] uppercase tracking-widest text-slate-400 font-black">
                                <th class="p-6 whitespace-nowrap">Receipt ID</th>
                                <th class="p-6">Date</th>
                                <th class="p-6">Plan Resolved</th>
                                <th class="p-6">Amount</th>
                                <th class="p-6">Status</th>
                                <th class="p-6 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm font-bold text-slate-700">
                            @forelse($invoices as $inv)
                            <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors last:border-0 group/row">
                                <td class="p-6 font-mono text-slate-500 text-xs">{{ $inv['id'] }}</td>
                                <td class="p-6 whitespace-nowrap">{{ $inv['date'] }}</td>
                                <td class="p-6">{{ $inv['plan'] }}</td>
                                <td class="p-6 font-black text-slate-900">{{ $inv['amount'] }}</td>
                                <td class="p-6">
                                    @if($inv['status'] === 'Paid')
                                        <span class="px-2.5 py-1.5 rounded-md bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-widest border border-emerald-100 flex items-center gap-1.5 w-max shadow-sm">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Validated
                                        </span>
                                    @endif
                                </td>
                                <td class="p-6 text-right">
                                    <button onclick="downloadInvoice('{{ $inv['id'] }}', this)" onmouseenter="audioEngine.playHover()" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all flex items-center justify-center ml-auto shadow-sm hover:shadow-md focus:outline-none group-hover/row:-translate-y-0.5">
                                        <i class="fa-solid fa-download text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-16 text-center">
                                    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100 shadow-inner"><i class="fa-solid fa-receipt text-2xl text-slate-300"></i></div>
                                    <p class="text-slate-900 font-black text-sm mb-1">No Archives Found</p>
                                    <p class="text-slate-500 font-medium text-xs">Past billing receipts will populate here.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= 6. INTERACTIVE FAQ ================= --}}
        <div class="pt-24 max-w-4xl mx-auto animate-fade-in-up" style="animation-delay: 500ms;">
            <h3 class="text-3xl font-black text-slate-900 mb-8 text-center tracking-tight">System FAQ</h3>
            <div class="space-y-4">
                @php
                    $faqs = [
                        ['q'=>'Is my ledger data secure?', 'a'=>'Absolutely. FinanceAI utilizes industry-standard AES-256-GCM encryption natively through Laravel. Your database connections are strictly isolated and not used for public LLM training.'],
                        ['q'=>'Can I revoke my node anytime?', 'a'=>'Yes. There are no contracts. If you cancel, your node will remain on the Pro tier until the exact end of your current cryptographic billing cycle.'],
                        ['q'=>'Will my pricing algorithm change?', 'a'=>'Negative. Once you authenticate a subscription, your price vector is permanently locked, immune to future global pricing adjustments.'],
                    ];
                @endphp

                @foreach($faqs as $index => $faq)
                <div class="bg-white border border-slate-200 rounded-[1.5rem] overflow-hidden transition-all duration-300 shadow-sm hover:shadow-md hover:border-slate-300">
                    <button class="faq-btn w-full px-8 py-6 text-left flex justify-between items-center focus:outline-none group" onmouseenter="audioEngine.playHover()">
                        <span class="font-black text-lg text-slate-900 group-hover:text-indigo-600 transition-colors tracking-tight">{{ $faq['q'] }}</span>
                        <span class="faq-icon w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center transform transition-all duration-300 text-slate-400 border border-slate-200 group-hover:bg-indigo-50 group-hover:border-indigo-200 group-hover:text-indigo-600 shadow-sm shrink-0">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </span>
                    </button>
                    <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
                        <div class="px-8 pb-6 text-slate-500 font-medium text-sm leading-relaxed border-t border-slate-100 pt-6 mt-2">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ================= 7. DANGER ZONE ================= --}}
        @if($currentPlan !== 'starter')
        <div class="pt-24 animate-fade-in-up" style="animation-delay: 600ms;">
            <div class="bg-rose-50/50 rounded-[2.5rem] border border-rose-100 p-8 md:p-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-8 relative overflow-hidden group">
                <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-rose-500/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700 pointer-events-none"></div>
                <div class="relative z-10">
                    <h3 class="text-2xl font-black text-rose-900 mb-2 flex items-center gap-3 tracking-tight"><i class="fa-solid fa-triangle-exclamation text-rose-500"></i> Revoke Subscription</h3>
                    <p class="text-rose-700/80 font-bold text-sm max-w-2xl leading-relaxed">
                        Revoking your subscription will downgrade your node to Starter at the end of the billing cycle. You will instantly lose access to AI heuristic analytics and multi-node family collaboration.
                    </p>
                </div>
                <button onclick="openCancelModal()" onmouseenter="audioEngine.playHover()" class="px-8 py-4 bg-white border border-rose-200 text-rose-600 font-black uppercase tracking-widest text-[11px] rounded-xl hover:bg-rose-600 hover:border-rose-600 hover:text-white hover:shadow-[0_10px_20px_rgba(225,29,72,0.3)] transition-all shadow-sm shrink-0 focus:outline-none hover:-translate-y-0.5 relative z-10">
                    Revoke Access
                </button>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- ================= MODALS & OVERLAYS ================= --}}

{{-- Cancel Confirmation Modal --}}
<div id="cancelModal" class="fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-md hidden flex-col items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div id="cancelModalContent" class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden transform scale-95 transition-transform duration-300 border border-slate-200 relative">
        <div class="absolute top-0 left-0 w-full h-1.5 bg-rose-500"></div>
        <div class="p-10 text-center">
            <div class="w-20 h-20 bg-rose-50 text-rose-600 border border-rose-100 rounded-[1.5rem] flex items-center justify-center mx-auto mb-6 shadow-inner relative overflow-hidden">
                <div class="absolute inset-0 bg-rose-500/10 animate-pulse"></div>
                <i class="fa-solid fa-triangle-exclamation text-3xl relative z-10"></i>
            </div>
            <h3 class="text-3xl font-black text-slate-900 mb-3 tracking-tight">Confirm Revocation</h3>
            <p class="text-slate-500 font-medium mb-10 leading-relaxed text-sm">
                You are about to disconnect your Pro Node. You will lose all predictive intelligence capabilities at the end of this cycle.
            </p>
            
            <div class="flex gap-4">
                <button onclick="closeCancelModal()" onmouseenter="audioEngine.playHover()" class="flex-1 py-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-black uppercase tracking-widest text-[10px] rounded-xl transition-colors focus:outline-none shadow-sm">
                    Abort
                </button>
                <form action="#" method="POST" class="flex-1" onsubmit="audioEngine.playClick()">
                    @csrf @method('DELETE')
                    <button type="submit" onmouseenter="audioEngine.playHover()" class="w-full py-4 bg-rose-600 hover:bg-rose-700 text-white font-black uppercase tracking-widest text-[10px] rounded-xl shadow-[0_10px_20px_rgba(225,29,72,0.3)] transition-all hover:-translate-y-0.5 focus:outline-none">
                        Confirm Revoke
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Update Payment Modal --}}
<div id="paymentModal" class="fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-md hidden flex-col items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div id="paymentModalContent" class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden transform scale-95 transition-transform duration-300 border border-slate-200 relative">
        <div class="p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-black text-slate-900 tracking-tight">Update Billing</h3>
                <button onclick="closePaymentModal()" onmouseenter="audioEngine.playHover()" class="w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center text-slate-500 hover:text-rose-500 transition-colors focus:outline-none"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Card Number</label>
                    <div class="relative">
                        <i class="fa-regular fa-credit-card absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" placeholder="0000 0000 0000 0000" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 outline-none transition-all shadow-inner">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Expiry</label>
                        <input type="text" placeholder="MM/YY" class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 outline-none transition-all shadow-inner text-center">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">CVC</label>
                        <input type="text" placeholder="123" class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 outline-none transition-all shadow-inner text-center">
                    </div>
                </div>
                <button onclick="simulateStripeCheckout(event, this, 'Updating Vault...')" onmouseenter="audioEngine.playHover()" class="w-full mt-4 py-4 rounded-xl bg-indigo-600 text-white font-black uppercase tracking-widest text-[11px] shadow-[0_10px_20px_rgba(79,70,229,0.3)] hover:bg-indigo-700 transition-all hover:-translate-y-0.5 focus:outline-none relative overflow-hidden">
                    <span class="relative z-10">Secure Vault Update</span>
                </button>
            </div>
            <p class="text-center text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-6 flex items-center justify-center gap-1.5"><i class="fa-solid fa-lock"></i> Secured by Stripe 256-bit encryption</p>
        </div>
    </div>
</div>

{{-- Stripe Processing Overlay --}}
<div id="stripeOverlay" class="fixed inset-0 z-[9999] bg-white/90 backdrop-blur-md flex flex-col items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="w-24 h-24 bg-white rounded-2xl shadow-xl border border-slate-200 flex items-center justify-center mb-6 relative">
        <div class="absolute inset-0 border-4 border-indigo-100 rounded-2xl"></div>
        <div class="absolute inset-0 border-4 border-indigo-600 rounded-2xl border-t-transparent animate-spin"></div>
        <i class="fa-brands fa-stripe text-3xl text-indigo-600"></i>
    </div>
    <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-2">Processing Transaction</h2>
    <p id="stripeOverlayText" class="text-sm font-bold text-slate-500 font-mono tracking-widest uppercase">Contacting payment gateway...</p>
</div>

{{-- Notification Toast --}}
<div id="toast" class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[9999] bg-slate-900/95 backdrop-blur-xl text-white px-6 py-3.5 rounded-2xl shadow-[0_20px_40px_-15px_rgba(0,0,0,0.5)] flex items-center gap-3.5 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none border border-slate-700">
    <i id="toastIcon" class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
    <span id="toastMsg" class="text-sm font-bold tracking-wide">Action completed</span>
</div>

@endsection

@push('styles')
<style>
    /* Animations */
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in-up { animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    
    @keyframes shimmer { 100% { transform: translateX(100%); } }

    /* 3D Hardware Acceleration */
    .transform-style-3d { transform-style: preserve-3d; perspective: 1000px; }
    .translate-z-20 { transform: translateZ(20px); }
    .translate-z-30 { transform: translateZ(30px); }
</style>
@endpush

@push('scripts')
<script>
// ================= AUDIO ENGINE =================
window.audioEngine = {
    ctx: null, lastHover: 0,
    init() { if(!this.ctx) { const AC = window.AudioContext || window.webkitAudioContext; if(AC) this.ctx = new AC(); } },
    playClick() {
        this.init(); if(!this.ctx) return; if(this.ctx.state === 'suspended') this.ctx.resume();
        const osc = this.ctx.createOscillator(); const gain = this.ctx.createGain();
        osc.connect(gain); gain.connect(this.ctx.destination); osc.type = 'sine'; 
        osc.frequency.setValueAtTime(800, this.ctx.currentTime); osc.frequency.exponentialRampToValueAtTime(300, this.ctx.currentTime + 0.05);
        gain.gain.setValueAtTime(0.1, this.ctx.currentTime); gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + 0.05);
        osc.start(); osc.stop(this.ctx.currentTime + 0.05);
    },
    playHover() {
        const now = Date.now(); if(now - this.lastHover < 50) return; this.lastHover = now;
        this.init(); if(!this.ctx) return; if(this.ctx.state === 'suspended') this.ctx.resume();
        const osc = this.ctx.createOscillator(); const gain = this.ctx.createGain();
        osc.connect(gain); gain.connect(this.ctx.destination); osc.type = 'sine'; 
        osc.frequency.setValueAtTime(400, this.ctx.currentTime); gain.gain.setValueAtTime(0.015, this.ctx.currentTime); 
        gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + 0.03); osc.start(); osc.stop(this.ctx.currentTime + 0.03);
    },
    playSuccess() {
        this.init(); if(!this.ctx) return; if(this.ctx.state === 'suspended') this.ctx.resume();
        const osc = this.ctx.createOscillator(); const gain = this.ctx.createGain();
        osc.connect(gain); gain.connect(this.ctx.destination); osc.type = 'sine'; 
        osc.frequency.setValueAtTime(600, this.ctx.currentTime); osc.frequency.setValueAtTime(900, this.ctx.currentTime + 0.1);
        gain.gain.setValueAtTime(0.05, this.ctx.currentTime); gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + 0.3);
        osc.start(); osc.stop(this.ctx.currentTime + 0.3);
    }
};

document.addEventListener('DOMContentLoaded', function() {

    // 1. FLAWLESS CSS-BASED TOGGLE LOGIC
    const btnMonthly = document.getElementById('btnMonthly');
    const btnYearly = document.getElementById('btnYearly');
    
    if (btnMonthly && btnYearly) {
        let isAnnual = {{ $currentBilling === 'yearly' ? 'true' : 'false' }};
        const indicator = document.getElementById('indicator');
        const priceElement = document.getElementById('proPrice');
        const billingText = document.getElementById('billingText');
        const billingInput = document.getElementById('billingCycle');

        // Initial Setup based on backend state
        if(isAnnual) {
            indicator.classList.replace('translate-x-0', 'translate-x-[100%]');
            btnYearly.classList.replace('text-slate-500', 'text-white');
            btnMonthly.classList.replace('text-white', 'text-slate-500');
        }

        function togglePlan(setToAnnual) {
            if(isAnnual === setToAnnual) return; // Prevent double click
            audioEngine.playClick();
            isAnnual = setToAnnual;

            if (isAnnual) {
                indicator.classList.replace('translate-x-0', 'translate-x-[100%]');
                btnYearly.classList.replace('text-slate-500', 'text-white');
                btnMonthly.classList.replace('text-white', 'text-slate-500');
                priceElement.style.opacity = 0;
                setTimeout(() => {
                    priceElement.innerText = '₹' + priceElement.getAttribute('data-yearly');
                    billingText.innerText = '/yr';
                    if(billingInput) billingInput.value = 'yearly';
                    priceElement.style.opacity = 1;
                }, 200);
            } else {
                indicator.classList.replace('translate-x-[100%]', 'translate-x-0');
                btnMonthly.classList.replace('text-slate-500', 'text-white');
                btnYearly.classList.replace('text-white', 'text-slate-500');
                priceElement.style.opacity = 0;
                setTimeout(() => {
                    priceElement.innerText = '₹' + priceElement.getAttribute('data-monthly');
                    billingText.innerText = '/mo';
                    if(billingInput) billingInput.value = 'monthly';
                    priceElement.style.opacity = 1;
                }, 200);
            }
        }

        btnMonthly.addEventListener('click', () => togglePlan(false));
        btnYearly.addEventListener('click', () => togglePlan(true));
    }

    // 2. VANILLA JS FAQ ACCORDION (SMOOTH)
    const faqBtns = document.querySelectorAll('.faq-btn');
    faqBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            audioEngine.playClick();
            const content = this.nextElementSibling;
            const icon = this.querySelector('.faq-icon');
            
            faqBtns.forEach(otherBtn => {
                if(otherBtn !== btn) {
                    otherBtn.nextElementSibling.style.maxHeight = null;
                    otherBtn.querySelector('.faq-icon').classList.remove('rotate-180', 'bg-indigo-50', 'text-indigo-600', 'border-indigo-200', 'shadow-inner');
                }
            });

            if (content.style.maxHeight) {
                content.style.maxHeight = null;
                icon.classList.remove('rotate-180', 'bg-indigo-50', 'text-indigo-600', 'border-indigo-200', 'shadow-inner');
            } else {
                content.style.maxHeight = content.scrollHeight + "px";
                icon.classList.add('rotate-180', 'bg-indigo-50', 'text-indigo-600', 'border-indigo-200', 'shadow-inner');
            }
        });
    });

});

// 3. TOAST NOTIFICATION
function showToast(msg, isError = false) {
    const toast = document.getElementById('toast');
    const icon = document.getElementById('toastIcon');
    document.getElementById('toastMsg').innerText = msg;
    if(isError) icon.className = "fa-solid fa-triangle-exclamation text-rose-400 text-lg";
    else icon.className = "fa-solid fa-circle-check text-emerald-400 text-lg";
    toast.classList.remove('translate-y-20', 'opacity-0');
    setTimeout(() => toast.classList.add('translate-y-20', 'opacity-0'), 3000);
}

// 4. INVOICE DOWNLOAD SIMULATION
window.downloadInvoice = function(id, btn) {
    audioEngine.playClick();
    const originalIcon = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-xs"></i>';
    btn.classList.add('pointer-events-none', 'text-indigo-600', 'bg-indigo-50');
    
    setTimeout(() => {
        audioEngine.playSuccess();
        showToast('Receipt PDF downloaded successfully.');
        btn.innerHTML = '<i class="fa-solid fa-check text-emerald-500 text-xs"></i>';
        
        setTimeout(() => {
            btn.innerHTML = originalIcon;
            btn.classList.remove('pointer-events-none', 'text-indigo-600', 'bg-indigo-50');
        }, 2000);
    }, 1500);
}

// 5. MODAL LOGIC 
window.openCancelModal = function() {
    audioEngine.playClick();
    const modal = document.getElementById('cancelModal');
    const content = document.getElementById('cancelModalContent');
    modal.classList.remove('hidden'); modal.classList.add('flex');
    setTimeout(() => {
        modal.classList.remove('opacity-0'); modal.classList.add('opacity-100');
        content.classList.remove('scale-95'); content.classList.add('scale-100');
    }, 10);
}

window.closeCancelModal = function() {
    audioEngine.playClick();
    const modal = document.getElementById('cancelModal');
    const content = document.getElementById('cancelModalContent');
    modal.classList.remove('opacity-100'); modal.classList.add('opacity-0');
    content.classList.remove('scale-100'); content.classList.add('scale-95');
    setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); }, 300);
}

window.openPaymentModal = function() {
    audioEngine.playClick();
    const modal = document.getElementById('paymentModal');
    const content = document.getElementById('paymentModalContent');
    modal.classList.remove('hidden'); modal.classList.add('flex');
    setTimeout(() => {
        modal.classList.remove('opacity-0'); modal.classList.add('opacity-100');
        content.classList.remove('scale-95'); content.classList.add('scale-100');
    }, 10);
}

window.closePaymentModal = function() {
    audioEngine.playClick();
    const modal = document.getElementById('paymentModal');
    const content = document.getElementById('paymentModalContent');
    modal.classList.remove('opacity-100'); modal.classList.add('opacity-0');
    content.classList.remove('scale-100'); content.classList.add('scale-95');
    setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); }, 300);
}

// 6. STRIPE CHECKOUT SIMULATION
window.simulateStripeCheckout = function(e, btn, startText = 'Contacting payment gateway...') {
    e.preventDefault();
    audioEngine.playClick();
    
    const form = btn.closest('form');
    const overlay = document.getElementById('stripeOverlay');
    const overlayText = document.getElementById('stripeOverlayText');
    
    // Lock screen
    overlay.classList.remove('opacity-0', 'pointer-events-none');
    overlayText.innerText = startText;
    
    setTimeout(() => {
        overlayText.innerText = 'Validating cryptographic signature...';
        audioEngine.playHover();
        
        setTimeout(() => {
            overlayText.innerText = 'Transaction Approved.';
            overlay.querySelector('.fa-stripe').className = "fa-solid fa-check text-5xl text-emerald-500";
            overlay.querySelector('.animate-spin').classList.add('hidden');
            overlay.querySelector('.border-indigo-100').classList.replace('border-indigo-100', 'border-emerald-100');
            audioEngine.playSuccess();
            
            setTimeout(() => {
                if(form.action && form.action !== window.location.href) {
                    form.submit();
                } else {
                    closePaymentModal();
                    overlay.classList.add('opacity-0', 'pointer-events-none');
                    showToast("Vault updated successfully.");
                    // Reset overlay for next time
                    setTimeout(() => {
                        overlay.querySelector('.fa-check').className = "fa-brands fa-stripe text-3xl text-indigo-600";
                        overlay.querySelector('.hidden').classList.remove('hidden');
                        overlay.querySelector('.border-emerald-100').classList.replace('border-emerald-100', 'border-indigo-100');
                    }, 500);
                }
            }, 1000);
        }, 1500);
    }, 1000);
}
</script>
@endpush