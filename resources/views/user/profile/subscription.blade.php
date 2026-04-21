@extends('layouts.app')

@section('title', 'Manage Subscription - FinanceAI')

@section('content')

@php
    $user = auth()->user();
    // Safe fallback for user properties
    $currentPlan = $user->plan ?? 'starter';
    $currentBilling = $user->billing_cycle ?? 'monthly';
    
    // Simulated Usage Data (Passed from Controller in production)
    $usage = [
        'ai_queries' => ['used' => 12, 'total' => 20, 'percent' => 60],
        'api_calls'  => ['used' => 8500, 'total' => 10000, 'percent' => 85],
        'storage'    => ['used' => 2.1, 'total' => 5.0, 'percent' => 42], // New Fun Metric
    ];

    // Simulated Invoice History
    $invoices = [
        ['id' => 'INV-2026-004', 'date' => 'Mar 01, 2026', 'amount' => '₹199.00', 'status' => 'Paid', 'plan' => 'Pro Advisor'],
        ['id' => 'INV-2026-003', 'date' => 'Feb 01, 2026', 'amount' => '₹0.00', 'status' => 'Paid', 'plan' => 'Starter'],
    ];

    // Auto-Color Logic for Quotas
    $getColor = function($pct) {
        if($pct >= 90) return ['bg' => 'bg-rose-500', 'text' => 'text-rose-600', 'border' => 'border-rose-200'];
        if($pct >= 75) return ['bg' => 'bg-amber-500', 'text' => 'text-amber-600', 'border' => 'border-amber-200'];
        return ['bg' => 'bg-indigo-500', 'text' => 'text-indigo-600', 'border' => 'border-indigo-200'];
    };
@endphp

<div x-data="subscriptionManager('{{ $currentBilling }}')" 
     class="min-h-screen bg-[#f8fafc] pb-32 font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden">
    
    {{-- Ambient Light Backgrounds --}}
    <div class="fixed top-[-20%] left-[-10%] w-[800px] h-[800px] bg-indigo-400/5 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="fixed top-[40%] right-[-10%] w-[600px] h-[600px] bg-sky-400/5 rounded-full blur-[100px] pointer-events-none z-0"></div>

    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-12 relative z-10 space-y-12">

        {{-- ================= 1. PAGE COMMAND HEADER ================= --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-16">
            <div>
                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white border border-slate-200 text-indigo-600 text-[10px] font-black tracking-widest uppercase shadow-sm mb-4">
                    <i class="fa-solid fa-bolt"></i> Billing & Quotas
                </span>
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight">
                    Manage Workspace.
                </h1>
                <p class="text-slate-500 font-medium mt-3 text-lg">
                    Scale your financial intelligence. No hidden fees. Secure processing.
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <span class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm">
                    <i class="fa-brands fa-stripe text-indigo-500 text-lg"></i> Secured
                </span>
            </div>
        </div>

        {{-- ================= 2. DASHBOARD: USAGE & PAYMENT METHOD (NEW FUN) ================= --}}
        <div class="grid lg:grid-cols-12 gap-8">
            
            {{-- Current Usage Quotas --}}
            <div class="lg:col-span-7 bg-white rounded-[2rem] p-8 md:p-10 border border-slate-200 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-indigo-50 to-transparent rounded-bl-full pointer-events-none"></div>
                
                <h2 class="text-xl font-black text-slate-900 mb-8 flex items-center gap-3">
                    <i class="fa-solid fa-chart-pie text-indigo-500"></i> Active Resource Quotas
                </h2>

                <div class="space-y-8">
                    {{-- AI Queries --}}
                    @php $aiColor = $getColor($usage['ai_queries']['percent']); @endphp
                    <div>
                        <div class="flex justify-between items-end mb-3">
                            <div>
                                <span class="text-sm font-bold text-slate-900 block">AI Analytics Engine</span>
                                <span class="text-xs font-medium text-slate-500">Deep-learning financial scans</span>
                            </div>
                            <span class="text-sm font-black {{ $aiColor['text'] }}">{{ $usage['ai_queries']['used'] }} <span class="text-slate-400 font-bold">/ {{ $usage['ai_queries']['total'] }}</span></span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden border border-slate-200/60 shadow-inner">
                            <div class="{{ $aiColor['bg'] }} h-full rounded-full transition-all duration-1000 ease-out" style="width: {{ $usage['ai_queries']['percent'] }}%"></div>
                        </div>
                    </div>

                    {{-- API Calls --}}
                    @php $apiColor = $getColor($usage['api_calls']['percent']); @endphp
                    <div>
                        <div class="flex justify-between items-end mb-3">
                            <div>
                                <span class="text-sm font-bold text-slate-900 block">API Sync Rate Limit</span>
                                <span class="text-xs font-medium text-slate-500">Bank connections & webhooks</span>
                            </div>
                            <span class="text-sm font-black {{ $apiColor['text'] }}">{{ number_format($usage['api_calls']['used']) }} <span class="text-slate-400 font-bold">/ {{ number_format($usage['api_calls']['total']) }}</span></span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden border border-slate-200/60 shadow-inner">
                            <div class="{{ $apiColor['bg'] }} h-full rounded-full transition-all duration-1000 ease-out relative" style="width: {{ $usage['api_calls']['percent'] }}%">
                                @if($usage['api_calls']['percent'] >= 80)
                                    <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                                @endif
                            </div>
                        </div>
                        @if($usage['api_calls']['percent'] >= 80)
                            <p class="text-[10px] uppercase tracking-widest text-rose-500 font-bold mt-2"><i class="fa-solid fa-triangle-exclamation"></i> Warning: Approaching soft limit</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Active Plan & Payment Method --}}
            <div class="lg:col-span-5 flex flex-col gap-8">
                
                {{-- Plan Status --}}
                <div class="bg-slate-900 rounded-[2rem] p-8 border border-slate-800 shadow-xl relative overflow-hidden flex-1">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10 mix-blend-overlay pointer-events-none"></div>
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-500/30 rounded-full blur-3xl pointer-events-none transition-colors duration-1000"></div>
                    
                    <div class="relative z-10 flex flex-col h-full justify-between">
                        <div>
                            <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">Current Active Tier</p>
                            <h2 class="text-4xl font-black text-white capitalize mb-4">{{ $currentPlan }} Plan</h2>
                            <p class="text-sm text-slate-400 font-medium leading-relaxed">
                                @if($currentPlan === 'starter')
                                    You are operating on the restricted free tier. Upgrade to unlock full analytical power.
                                @else
                                    Your premium workspace is fully active. Billing cycle renews automatically.
                                @endif
                            </p>
                        </div>
                        
                        <div class="mt-8 pt-6 border-t border-white/10 flex justify-between items-center">
                            <div>
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Next Invoice</p>
                                <p class="text-white font-bold">{{ $currentPlan === 'starter' ? 'N/A' : now()->addDays(14)->format('d M, Y') }}</p>
                            </div>
                            @if($currentPlan !== 'starter')
                                <span class="px-3 py-1.5 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm">
                                    <span class="w-1.5 h-1.5 inline-block bg-emerald-400 rounded-full mr-1 animate-pulse"></span> Active
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Payment Method UI (New Fun) --}}
                <div class="bg-white rounded-[2rem] p-6 border border-slate-200 shadow-sm flex items-center justify-between group cursor-pointer hover:border-indigo-300 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-center text-xl text-[#1434CB] shadow-inner group-hover:scale-105 transition-transform">
                            <i class="fa-brands fa-cc-visa"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Payment Method</p>
                            <p class="text-sm font-bold text-slate-900 font-mono tracking-wider">•••• 4242 <span class="text-xs text-slate-400 ml-1">12/28</span></p>
                        </div>
                    </div>
                    <button class="w-8 h-8 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center border border-slate-200 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </button>
                </div>

            </div>
        </div>

        {{-- ================= 3. PRICING MATRIX ================= --}}
        <div class="pt-16">
            <div class="text-center mb-12">
                <h3 class="text-3xl font-black text-slate-900 tracking-tight mb-4">Upgrade your Architecture</h3>
                
                {{-- FLAWLESS ALPINE JS BILLING TOGGLE --}}
                <div class="inline-flex bg-slate-100 border border-slate-200 rounded-full p-1.5 shadow-inner relative">
                    {{-- Sliding Background --}}
                    <div class="absolute top-1.5 bottom-1.5 left-1.5 w-[calc(50%-0.375rem)] bg-white rounded-full shadow-sm transition-transform duration-300 ease-out border border-slate-200"
                         :class="billing === 'yearly' ? 'translate-x-full' : 'translate-x-0'"></div>
                    
                    <button @click="setBilling('monthly')" type="button" 
                            class="relative z-10 w-40 py-2.5 text-xs uppercase tracking-widest font-black transition-colors duration-300 focus:outline-none"
                            :class="billing === 'monthly' ? 'text-indigo-600' : 'text-slate-500'">
                        Monthly
                    </button>
                    <button @click="setBilling('yearly')" type="button" 
                            class="relative z-10 w-40 py-2.5 text-xs uppercase tracking-widest font-black transition-colors duration-300 focus:outline-none flex items-center justify-center gap-2"
                            :class="billing === 'yearly' ? 'text-indigo-600' : 'text-slate-500'">
                        Annually <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700 text-[9px] border border-emerald-200">Save 20%</span>
                    </button>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-8 items-start">

                {{-- STARTER CARD --}}
                <div class="bg-white border border-slate-200 rounded-[2.5rem] p-10 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                    <div class="relative z-10">
                        <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center border border-slate-200 text-slate-600 mb-6 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-seedling text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900">Starter</h3>
                        <p class="text-sm font-medium text-slate-500 mt-2 h-10">Essential tools for personal tracking.</p>

                        <div class="my-8 border-b border-slate-100 pb-8">
                            <span class="text-5xl font-black text-slate-900">₹0</span>
                            <span class="text-slate-400 font-bold uppercase text-[10px] tracking-widest">/ forever</span>
                        </div>

                        @if($currentPlan === 'starter')
                            <button disabled class="w-full py-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-400 font-bold tracking-widest uppercase text-xs cursor-not-allowed">
                                Current Active Plan
                            </button>
                        @else
                            <button @click="openDowngradeModal()" class="w-full py-4 rounded-xl bg-white border-2 border-slate-900 text-slate-900 font-bold tracking-widest uppercase text-xs hover:bg-slate-900 hover:text-white transition-colors">
                                Downgrade to Starter
                            </button>
                        @endif

                        <ul class="mt-8 space-y-4 text-sm font-medium text-slate-600">
                            <li class="flex items-center gap-3"><i class="fa-solid fa-check text-emerald-500"></i> Basic transaction ledger</li>
                            <li class="flex items-center gap-3"><i class="fa-solid fa-check text-emerald-500"></i> Standard web exports</li>
                            <li class="flex items-center gap-3 opacity-40"><i class="fa-solid fa-xmark text-slate-400"></i> No AI predictive forecasting</li>
                            <li class="flex items-center gap-3 opacity-40"><i class="fa-solid fa-xmark text-slate-400"></i> No collaborative family nodes</li>
                        </ul>
                    </div>
                </div>

                {{-- PRO ADVISOR (HIGHLIGHT) --}}
                <div class="relative rounded-[2.5rem] p-[2px] shadow-[0_20px_50px_-12px_rgba(79,70,229,0.3)] transform lg:scale-105 z-10 group overflow-hidden">
                    {{-- Animated Gradient Border --}}
                    <div class="absolute inset-[-50%] bg-[conic-gradient(from_0deg,theme(colors.indigo.500),theme(colors.sky.400),theme(colors.purple.500),theme(colors.indigo.500))] animate-[spin_4s_linear_infinite]"></div>
                    
                    <div class="relative bg-white h-full w-full rounded-[2.4rem] p-10 flex flex-col">
                        <div class="absolute top-0 inset-x-0 flex justify-center">
                            <span class="bg-indigo-600 text-white text-[9px] font-black uppercase tracking-widest px-4 py-1.5 rounded-b-xl shadow-md border-x border-b border-indigo-500">Master Node</span>
                        </div>

                        <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center border border-indigo-100 text-indigo-600 mb-6 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-bolt text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900">Pro Advisor</h3>
                        <p class="text-sm font-medium text-slate-500 mt-2 h-10">Full automated AI financial intelligence.</p>

                        <div class="my-8 border-b border-slate-100 pb-8 flex items-end">
                            {{-- Alpine Reactive Pricing --}}
                            <span class="text-5xl font-black text-indigo-600">₹<span x-text="billing === 'yearly' ? '1999' : '199'"></span></span>
                            <span class="text-slate-400 font-bold uppercase text-[10px] tracking-widest ml-1 mb-1 block" x-text="billing === 'yearly' ? '/ year' : '/ month'"></span>
                        </div>

                        @if($currentPlan === 'pro')
                            <button disabled class="w-full py-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 font-bold tracking-widest uppercase text-xs cursor-not-allowed flex items-center justify-center gap-2">
                                <i class="fa-solid fa-shield-check text-lg"></i> Active Pro Plan
                            </button>
                        @else
                            <form method="POST" action="{{ Route::has('profile.subscription.upgrade') ? route('profile.subscription.upgrade') : '#' }}" @submit="showUpgradeLoader($event)">
                                @csrf
                                <input type="hidden" name="plan" value="pro">
                                <input type="hidden" name="billing" :value="billing">
                                <button type="submit" class="upgrade-btn w-full py-4 rounded-xl bg-slate-900 text-white font-bold tracking-widest uppercase text-xs hover:bg-indigo-600 shadow-md hover:shadow-xl hover:shadow-indigo-500/30 transition-all flex items-center justify-center gap-2">
                                    <span>Deploy Architecture</span> <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            </form>
                        @endif

                        <ul class="mt-8 space-y-4 text-sm font-medium text-slate-600 flex-1">
                            <li class="flex items-center gap-3"><i class="fa-solid fa-check text-indigo-500"></i> Everything in Starter</li>
                            <li class="flex items-center gap-3"><i class="fa-solid fa-brain text-indigo-500"></i> <strong class="text-slate-900">AI Predictive Forecasting</strong></li>
                            <li class="flex items-center gap-3"><i class="fa-solid fa-network-wired text-indigo-500"></i> Family Node Sync (Up to 5)</li>
                            <li class="flex items-center gap-3"><i class="fa-solid fa-file-pdf text-indigo-500"></i> Boardroom-ready PDF Exports</li>
                        </ul>
                    </div>
                </div>

                {{-- ENTERPRISE CARD --}}
                <div class="bg-white border border-slate-200 rounded-[2.5rem] p-10 shadow-sm relative overflow-hidden group">
                    <div class="absolute inset-0 bg-white/60 backdrop-blur-[2px] z-20 flex items-center justify-center border border-white/50">
                        <span class="px-6 py-3 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-2xl transform group-hover:scale-110 transition-transform">
                            Contact Sales
                        </span>
                    </div>

                    <div class="relative z-10 opacity-50">
                        <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center border border-slate-200 text-slate-900 mb-6">
                            <i class="fa-solid fa-building-columns text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900">Enterprise</h3>
                        <p class="text-sm font-medium text-slate-500 mt-2 h-10">Dedicated API and isolated databases.</p>

                        <div class="my-8 border-b border-slate-100 pb-8">
                            <span class="text-5xl font-black text-slate-900">Custom</span>
                        </div>

                        <button disabled class="w-full py-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-400 font-bold tracking-widest uppercase text-xs">
                            Join Waitlist
                        </button>

                        <ul class="mt-8 space-y-4 text-sm font-medium text-slate-600">
                            <li class="flex items-center gap-3"><i class="fa-solid fa-server text-slate-400"></i> Isolated Database Shards</li>
                            <li class="flex items-center gap-3"><i class="fa-solid fa-code text-slate-400"></i> Unlimited REST API Access</li>
                            <li class="flex items-center gap-3"><i class="fa-solid fa-headset text-slate-400"></i> 24/7 Priority Support</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        {{-- ================= 4. BILLING LEDGER ================= --}}
        <div class="pt-16">
            <h3 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-3">
                <i class="fa-solid fa-file-invoice-dollar text-indigo-500"></i> Billing Ledger
            </h3>
            <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] uppercase tracking-widest text-slate-400 font-black">
                                <th class="p-6 whitespace-nowrap">Invoice ID</th>
                                <th class="p-6">Date</th>
                                <th class="p-6">Plan Vector</th>
                                <th class="p-6">Amount</th>
                                <th class="p-6">Status</th>
                                <th class="p-6 text-right">Document</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm font-medium text-slate-700">
                            @forelse($invoices as $inv)
                            <tr class="border-b border-slate-50 hover:bg-slate-50/80 transition-colors last:border-0 group">
                                <td class="p-6 font-mono text-xs text-indigo-600">{{ $inv['id'] }}</td>
                                <td class="p-6 whitespace-nowrap text-slate-500">{{ $inv['date'] }}</td>
                                <td class="p-6 font-bold text-slate-900">{{ $inv['plan'] }}</td>
                                <td class="p-6 font-mono text-slate-900">{{ $inv['amount'] }}</td>
                                <td class="p-6">
                                    @if($inv['status'] === 'Paid')
                                        <span class="px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest border border-emerald-100 flex items-center gap-1.5 w-max shadow-sm">
                                            <i class="fa-solid fa-check text-emerald-500"></i> Paid
                                        </span>
                                    @endif
                                </td>
                                <td class="p-6 text-right">
                                    <button class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-colors flex items-center justify-center ml-auto shadow-sm group-hover:shadow">
                                        <i class="fa-solid fa-download"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-12 text-center text-slate-400 font-bold">No historical invoices found in ledger.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= 5. KNOWLEDGE BASE (ALPINE FAQ) ================= --}}
        <div class="pt-16 max-w-4xl mx-auto">
            <h3 class="text-3xl font-black text-slate-900 mb-8 text-center">Protocol Inquiries</h3>
            
            <div class="space-y-4">
                @php
                    $faqs = [
                        ['id'=>1, 'q'=>'Is my telemetry data secure?', 'a'=>'Affirmative. We employ industry-standard AES-256 encryption via the Laravel framework. Your database connections and financial ledgers are completely isolated and secured.'],
                        ['id'=>2, 'q'=>'Can I downgrade the architecture anytime?', 'a'=>'Absolutely. There are no restrictive contracts. If you initiate a downgrade, you will retain access to the Pro Node features until the expiration of your current billing cycle.'],
                        ['id'=>3, 'q'=>'Will my pricing vector increase?', 'a'=>'Negative. Once you establish a subscription protocol, your pricing tier is permanently locked, shielding you from future adjustments.'],
                    ];
                @endphp

                @foreach($faqs as $faq)
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    <button @click="activeFaq = activeFaq === {{ $faq['id'] }} ? null : {{ $faq['id'] }}" 
                            class="w-full px-8 py-6 text-left flex justify-between items-center focus:outline-none group">
                        <span class="font-bold text-base text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $faq['q'] }}</span>
                        <span class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center transform transition-transform duration-300 text-slate-400 border border-slate-200 group-hover:bg-indigo-50 group-hover:text-indigo-600"
                              :class="activeFaq === {{ $faq['id'] }} ? 'rotate-180 bg-indigo-50 border-indigo-200 text-indigo-600' : ''">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </span>
                    </button>
                    
                    {{-- Smooth Alpine CSS Grid Transition for Accordion --}}
                    <div class="grid transition-all duration-300 ease-in-out"
                         :style="activeFaq === {{ $faq['id'] }} ? 'grid-template-rows: 1fr; opacity: 1;' : 'grid-template-rows: 0fr; opacity: 0;'">
                        <div class="overflow-hidden">
                            <div class="px-8 pb-6 text-slate-500 font-medium leading-relaxed border-t border-slate-100 pt-6 mt-2">
                                {{ $faq['a'] }}
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ================= 6. DANGER ZONE ================= --}}
        @if($currentPlan !== 'starter')
        <div class="pt-24 pb-10">
            <div class="bg-white rounded-[2.5rem] border border-rose-200 p-8 md:p-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-8 relative overflow-hidden shadow-sm">
                <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-rose-50 to-transparent rounded-bl-full pointer-events-none"></div>
                
                <div class="relative z-10">
                    <h3 class="text-2xl font-black text-rose-600 mb-2 flex items-center gap-3">
                        <i class="fa-solid fa-triangle-exclamation"></i> Deactivate Master Node
                    </h3>
                    <p class="text-slate-600 font-medium text-sm max-w-2xl leading-relaxed">
                        Initiating deactivation will downgrade your workspace to the restricted Starter tier at the end of your billing cycle. Automated forecasting and family collaboration will be suspended.
                    </p>
                </div>
                <button @click="showCancelModal = true" class="relative z-10 px-8 py-4 bg-white border-2 border-rose-200 text-rose-600 text-[10px] uppercase tracking-widest font-black rounded-xl hover:bg-rose-50 hover:border-rose-300 transition-all shadow-sm shrink-0 focus:outline-none">
                    Initialize Downgrade
                </button>
            </div>
        </div>
        @endif

    </div>

    {{-- ================= MODALS & OVERLAYS (ALPINE CONTROLLED) ================= --}}

    {{-- Downgrade Confirmation Modal --}}
    <div x-show="showCancelModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        {{-- Backdrop --}}
        <div x-show="showCancelModal" x-transition.opacity.duration.300ms class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showCancelModal = false"></div>
        
        {{-- Dialog --}}
        <div x-show="showCancelModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="bg-white w-full max-w-md rounded-[2rem] shadow-2xl relative z-10 border border-slate-200 overflow-hidden">
            
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-6 border border-rose-100 shadow-inner">
                    <i class="fa-solid fa-power-off text-2xl"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-900 mb-2">Confirm Protocol</h3>
                <p class="text-slate-500 font-medium mb-8 text-sm leading-relaxed">
                    You are about to sever the connection to the Pro Node. You will lose access to AI features at the end of the cycle. Proceed?
                </p>
                
                <div class="flex gap-4">
                    <button @click="showCancelModal = false" class="flex-1 py-3.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-600 text-xs tracking-widest uppercase font-black rounded-xl transition-colors">
                        Abort
                    </button>
                    <form action="#" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-3.5 bg-rose-600 hover:bg-rose-700 text-white text-xs tracking-widest uppercase font-black rounded-xl shadow-lg shadow-rose-500/30 transition-colors">
                            Confirm
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
// 1. Alpine.js State Machine Registration
document.addEventListener('alpine:init', () => {
    Alpine.data('subscriptionManager', (initialBilling) => ({
        billing: initialBilling || 'monthly',
        activeFaq: null,
        showCancelModal: false,
        
        setBilling(type) {
            this.billing = type;
        },
        
        openDowngradeModal() {
            // Can be tied to form submission or logic later
            this.showCancelModal = true;
        },

        showUpgradeLoader(event) {
            const btn = event.target.querySelector('.upgrade-btn');
            if(btn) {
                btn.innerHTML = `<svg class="w-5 h-5 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> <span class="ml-2">Establishing Handshake...</span>`;
                btn.classList.add('opacity-90', 'cursor-not-allowed');
            }
        }
    }));
});
</script>
@endpush