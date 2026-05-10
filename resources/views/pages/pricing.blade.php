@extends('layouts.landing')

@section('title', 'Pricing & Quotas | FinanceAI Enterprise')
@section('meta_description', 'Transparent, scalable pricing for financial intelligence. Choose the right node for your organization.')

@section('content')

@php
    // ================= ENTERPRISE SSR PAYLOAD =================
    
    $exchangeRates = ['INR' => 1, 'USD' => 0.012, 'EUR' => 0.011, 'GBP' => 0.0095];
    
    $plans = [
        [
            'id' => 'core',
            'name' => 'Developer Node',
            'desc' => 'For individual engineers requiring basic cryptographic tracking and API testing.',
            'base_monthly' => 0,
            'base_yearly' => 0,
            'btn' => 'Initialize Free',
            'highlight' => false,
            'features' => [
                '1 Secure Workspace',
                'Basic Manual Ledgers',
                '30-Day Telemetry History',
                'Sandbox API Access'
            ],
            'missing' => [
                'AI Neural Forecasting',
                'Multi-User IAM (RBAC)',
                'Priority Engineering Support'
            ]
        ],
        [
            'id' => 'pro',
            'name' => 'Professional Hub',
            'desc' => 'Advanced automation and AI heuristic tools for families and small teams.',
            'base_monthly' => 499, // Strict 499 monthly
            'base_yearly' => 4999, // Strict 4999 yearly
            'btn' => 'Deploy Pro Hub',
            'highlight' => true,
            'features' => [
                '3 Secure Workspaces',
                'AI Neural Categorization',
                'Predictive Burn-Rate Forecasting',
                'Role-Based Access (IAM)',
                'Universal PDF Export',
                'Standard Email Support'
            ],
            'missing' => [
                'Dedicated Database Instance'
            ]
        ],
        [
            'id' => 'enterprise',
            'name' => 'Enterprise Scale',
            'desc' => 'Dedicated infrastructure and unlimited API access for massive data throughput.',
            'base_monthly' => -1, // Triggers "Custom" UI
            'base_yearly' => -1,
            'btn' => 'Contact Architecture',
            'highlight' => false,
            'features' => [
                'Unlimited Workspaces & Nodes',
                'Full REST & Webhook Access',
                'Custom Bank Integrations',
                'Dedicated Success Architect',
                'SLA 99.999% Uptime',
                'White-labeled Dashboards'
            ],
            'missing' => []
        ]
    ];

    $addons = [
        ['id' => 'addon_db', 'name' => 'Dedicated RDS Instance', 'desc' => 'Isolated database architecture for maximum IOPS.', 'price' => 1500],
        ['id' => 'addon_sla', 'name' => '1-Hour SLA Support', 'desc' => 'Direct Slack channel with our core engineering team.', 'price' => 2000],
        ['id' => 'addon_api', 'name' => 'Extended API Rate Limits', 'desc' => 'Increase cap from 1k to 100k requests per minute.', 'price' => 800],
    ];

    $matrix = [
        'Core Cryptography' => [
            ['name' => 'AES-256 Encryption', 'tooltip' => 'Military-grade encryption applied at the database row level.', 'core' => true, 'pro' => true, 'ent' => true],
            ['name' => 'Live Telemetry Sync', 'core' => true, 'pro' => true, 'ent' => true],
            ['name' => 'Data Retention', 'core' => '30 Days', 'pro' => '5 Years', 'ent' => 'Infinite'],
            ['name' => 'Geographic Backup', 'core' => false, 'pro' => 'Daily', 'ent' => 'Real-time (Multi-AZ)'],
        ],
        'Artificial Intelligence' => [
            ['name' => 'Auto-Categorization', 'tooltip' => 'Proprietary ML engine maps raw transaction strings to strict accounting codes.', 'core' => false, 'pro' => true, 'ent' => true],
            ['name' => 'Burn-Rate Heuristics', 'tooltip' => 'Predicts end-of-month runway based on trailing 90-day spend velocity.', 'core' => false, 'pro' => true, 'ent' => true],
            ['name' => 'Predictive Alerts', 'core' => false, 'pro' => false, 'ent' => true],
            ['name' => 'Custom ML Model Training', 'tooltip' => 'Train the heuristic engine strictly on your proprietary ledger data.', 'core' => false, 'pro' => false, 'ent' => true],
        ],
        'Infrastructure & Support' => [
            ['name' => 'REST API Access', 'core' => 'Sandbox Only', 'pro' => '1k Req/mo', 'ent' => 'Unlimited'],
            ['name' => 'Role-Based Access Control', 'core' => false, 'pro' => 'Basic (Admin/User)', 'ent' => 'Granular IAM'],
            ['name' => 'Dedicated Node Priority', 'core' => false, 'pro' => false, 'ent' => true],
            ['name' => 'Support SLA', 'core' => 'Community', 'pro' => '24 Hour', 'ent' => '1 Hour (Dedicated)'],
        ]
    ];
@endphp

<div class="bg-[#fcf9f2] font-sans selection:bg-[#bacdf3] selection:text-[#0f172a] relative overflow-hidden flex flex-col min-h-screen pt-24" 
     x-data="pricingEngine()">

    {{-- Holographic Ambient Backgrounds (Fintech Palette) --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-20%] left-[10%] w-[1000px] h-[1000px] bg-[#bacdf3]/30 blur-[150px] rounded-full animate-float"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[800px] h-[800px] bg-[#9fb2df]/20 blur-[120px] rounded-full animate-float" style="animation-delay: -3s;"></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.03]"></div>
    </div>

    {{-- ================= 1. HERO & REAL-WORLD TOGGLE ================= --}}
    <section class="relative pt-20 pb-12 lg:pt-32 lg:pb-24 overflow-hidden z-10 border-b border-[#bacdf3]/40 bg-white/60 backdrop-blur-3xl">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            
            <div class="flex justify-center items-center gap-4 mb-8 reveal-up">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#fcf9f2] border border-[#bacdf3] text-[#7284b5] text-[10px] font-black uppercase tracking-widest shadow-sm">
                    <i class="fa-solid fa-scale-balanced"></i> Transparent Licensing
                </div>
                
                {{-- Live Currency Switcher --}}
                <div class="relative" @click.away="currencyOpen = false">
                    <button @click="currencyOpen = !currencyOpen; playClick()" class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white border border-slate-200 text-slate-700 text-[10px] font-black uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-colors focus:outline-none">
                        <i class="fa-solid fa-globe text-slate-400"></i> <span x-text="currency"></span> <i class="fa-solid fa-chevron-down text-[8px] ml-1"></i>
                    </button>
                    <div x-show="currencyOpen" x-cloak x-transition class="absolute top-full left-0 mt-2 w-full bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden z-50">
                        <button @click="setCurrency('INR'); playClick()" class="w-full text-left px-4 py-2 text-[10px] font-black hover:bg-slate-50 transition-colors">INR (₹)</button>
                        <button @click="setCurrency('USD'); playClick()" class="w-full text-left px-4 py-2 text-[10px] font-black hover:bg-slate-50 transition-colors">USD ($)</button>
                        <button @click="setCurrency('EUR'); playClick()" class="w-full text-left px-4 py-2 text-[10px] font-black hover:bg-slate-50 transition-colors">EUR (€)</button>
                        <button @click="setCurrency('GBP'); playClick()" class="w-full text-left px-4 py-2 text-[10px] font-black hover:bg-slate-50 transition-colors">GBP (£)</button>
                    </div>
                </div>
            </div>

            <h1 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tight leading-[1.05] max-w-4xl mx-auto reveal-up text-balance" style="transition-delay: 100ms;">
                Invest in absolute <br class="hidden md:block">
                <span class="bg-gradient-to-r from-[#7284b5] via-[#879ac9] to-[#bacdf3] bg-clip-text text-transparent">financial clarity.</span>
            </h1>

            <p class="mt-6 text-lg text-slate-500 font-medium leading-relaxed max-w-2xl mx-auto reveal-up text-balance" style="transition-delay: 200ms;">
                Deploy the FinanceAI engine for your household or enterprise. Scalable infrastructure designed to generate absolute ROI within the first 30 days.
            </p>

            {{-- Billing Toggle (Real-World Accurate) --}}
            <div class="mt-14 flex flex-col items-center justify-center gap-5 reveal-up" style="transition-delay: 300ms;">
                
                <div class="flex items-center gap-2 bg-[#fcf9f2] p-1.5 rounded-full border border-[#bacdf3] shadow-inner relative w-[320px] h-[56px] cursor-pointer group" 
                     @click="toggleBilling(); playClick()" @mouseenter="playHover()"
                     role="switch" :aria-checked="annual.toString()">
                    
                    {{-- Sliding Pill --}}
                    <div class="absolute top-1.5 bottom-1.5 left-1.5 w-[calc(50%-0.375rem)] bg-[#7284b5] rounded-full shadow-md transition-transform duration-500 cubic-bezier(0.34, 1.56, 0.64, 1) z-0" 
                         :class="annual ? 'translate-x-[100%]' : 'translate-x-0'"></div>
                    
                    <button class="relative z-10 w-1/2 h-full flex items-center justify-center text-xs font-black uppercase tracking-widest transition-colors duration-300 focus:outline-none cursor-none" 
                            :class="!annual ? 'text-white' : 'text-[#7284b5] group-hover:text-slate-900'">
                        Monthly
                    </button>
                    <button class="relative z-10 w-1/2 h-full flex items-center justify-center text-xs font-black uppercase tracking-widest transition-colors duration-300 focus:outline-none cursor-none" 
                            :class="annual ? 'text-white' : 'text-[#7284b5] group-hover:text-slate-900'">
                        Annually
                    </button>
                </div>

                <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-emerald-600 bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-full animate-pulse shadow-sm">
                    <i class="fa-solid fa-gift"></i> Save 16% on Annual Deployment
                </div>
            </div>
        </div>
    </section>

    {{-- ================= 1.5 SOCIAL PROOF MARQUEE ================= --}}
    <div class="border-b border-[#bacdf3]/40 bg-white relative z-10 overflow-hidden py-8 shadow-sm">
        <div class="absolute inset-y-0 left-0 w-32 bg-gradient-to-r from-white to-transparent z-10 pointer-events-none"></div>
        <div class="absolute inset-y-0 right-0 w-32 bg-gradient-to-l from-white to-transparent z-10 pointer-events-none"></div>
        
        <p class="text-center text-[10px] font-black uppercase tracking-widest text-slate-400 mb-4">Trusted by modern financial architects at</p>
        
        <div class="flex whitespace-nowrap animate-marquee items-center gap-16 px-4 font-black text-xl text-slate-300 uppercase tracking-tighter">
            @for ($i = 0; $i < 4; $i++)
                <span class="hover:text-[#7284b5] transition-colors cursor-default">TechFlow</span>
                <span class="hover:text-[#7284b5] transition-colors cursor-default"><i class="fa-solid fa-bolt text-lg mr-1"></i> Velocity</span>
                <span class="hover:text-[#7284b5] transition-colors cursor-default">DataCore Inc</span>
                <span class="hover:text-[#7284b5] transition-colors cursor-default"><i class="fa-solid fa-wave-square text-lg mr-1"></i> Nexus Finance</span>
                <span class="hover:text-[#7284b5] transition-colors cursor-default">Stark Industries</span>
            @endfor
        </div>
    </div>

    {{-- ================= 2. PRICING TIERS (PERFECT FLEX ALIGNMENT) ================= --}}
    <section class="py-24 relative z-10">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Flex container ensures cards stretch to equal height --}}
            <div class="flex flex-col lg:flex-row gap-8 items-stretch justify-center">

                @foreach($plans as $idx => $plan)
                    <div class="flex-1 bg-white rounded-[3rem] border border-[#bacdf3]/50 p-10 flex flex-col h-full reveal-up relative overflow-hidden group transition-all duration-500 
                                {{ $plan['highlight'] ? 'lg:scale-105 border-[3px] border-[#7284b5] shadow-[0_30px_80px_-15px_rgba(114,132,181,0.25)] z-20' : 'shadow-sm hover:shadow-[0_20px_50px_-10px_rgba(114,132,181,0.15)] hover:-translate-y-2 z-10' }}"
                         style="transition-delay: {{ $idx * 100 }}ms">
                        
                        {{-- Generative CSS Patterns & Badges --}}
                        @if($plan['highlight'])
                            <div class="absolute inset-0 pattern-grid-lg opacity-[0.15] z-0 pointer-events-none"></div>
                            <div class="absolute -right-20 -top-20 w-64 h-64 bg-[#bacdf3]/30 rounded-full blur-3xl pointer-events-none transition-transform group-hover:scale-150 duration-700 z-0"></div>
                            
                            {{-- FLAWLESS OPTIMAL CHOICE BADGE (As requested from image) --}}
                            <div class="absolute top-0 inset-x-0 flex justify-center transform -translate-y-[2px] z-20">
                                <div class="bg-[#7284b5] text-white px-6 py-2 rounded-b-2xl text-[10px] font-black uppercase tracking-widest shadow-md flex items-center gap-2 border-x border-b border-[#bacdf3]">
                                    <i class="fa-solid fa-star text-amber-300"></i> Optimal Choice
                                </div>
                            </div>
                        @endif

                        {{-- Card Header --}}
                        <div class="relative z-10 pt-4">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center border mb-6 shadow-sm group-hover:scale-110 transition-transform duration-500
                                        {{ $plan['highlight'] ? 'bg-[#fcf9f2] text-[#7284b5] border-[#bacdf3]' : 'bg-slate-50 text-slate-500 border-slate-200' }}">
                                <i class="fa-solid {{ $plan['id'] === 'core' ? 'fa-user' : ($plan['id'] === 'pro' ? 'fa-network-wired' : 'fa-server') }} text-xl"></i>
                            </div>
                            
                            <h3 class="text-3xl font-black text-slate-900 mb-3 tracking-tight">{{ $plan['name'] }}</h3>
                            <p class="text-sm text-slate-500 font-medium h-12 leading-relaxed text-balance">{{ $plan['desc'] }}</p>
                            
                            {{-- REAL WORLD BILLING MATH (Perfectly Aligned) --}}
                            <div class="mt-8 mb-10 pb-8 border-b border-slate-100 flex flex-col h-[100px] justify-end relative">
                                @if($plan['base_monthly'] === -1)
                                    <span class="text-5xl font-black text-slate-900 tracking-tight">Custom</span>
                                    <span class="text-xs font-bold text-slate-400 mt-2">Tailored to your architectural scale</span>
                                @else
                                    <div class="flex items-end gap-1">
                                        <span class="text-2xl text-slate-400 font-bold mb-1.5" x-text="currencySymbol">₹</span>
                                        <span class="text-6xl font-black text-slate-900 tracking-tighter tabular-nums" 
                                              x-text="formatPrice('{{ $plan['id'] }}')">
                                            {{ number_format($plan['base_monthly']) }}
                                        </span>
                                        <span class="text-sm font-bold text-slate-400 mb-2 transition-all duration-300" x-text="annual ? '/yr' : '/mo'">/mo</span>
                                    </div>
                                    
                                    {{-- Authentic Subtext Billing Explanation --}}
                                    <div class="mt-3 h-5 flex items-center gap-2">
                                        @if($plan['base_yearly'] > 0)
                                            {{-- Annual View: Show equivalent monthly cost + discount --}}
                                            <div x-show="annual" x-cloak x-transition.opacity class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-slate-400 line-through" x-text="currencySymbol + formatExact('{{ $plan['id'] }}', false) + '/mo'"></span>
                                                <span class="text-[10px] font-black uppercase tracking-widest text-[#7284b5] bg-[#bacdf3]/20 px-2 py-0.5 rounded border border-[#bacdf3]/50">
                                                    Equivalent to <span x-text="currencySymbol + formatEquivalent('{{ $plan['id'] }}')"></span>/mo
                                                </span>
                                            </div>
                                            {{-- Monthly View: Show standard billing warning --}}
                                            <span x-show="!annual" class="text-xs font-medium text-slate-400">Billed exactly <span x-text="currencySymbol + formatExact('{{ $plan['id'] }}', false)"></span> every month</span>
                                        @else
                                            <span class="text-xs font-medium text-slate-400">Free forever. No credit card required.</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Features List (flex-1 pushes the button to the bottom) --}}
                        <div class="flex-1 space-y-4 relative z-10 mb-10">
                            @foreach($plan['features'] as $feature)
                                <div class="flex items-start gap-4">
                                    <div class="mt-1 w-5 h-5 rounded-full flex items-center justify-center shrink-0 {{ $plan['highlight'] ? 'bg-[#7284b5] text-white' : 'bg-emerald-50 text-emerald-500 border border-emerald-100' }}">
                                        <i class="fa-solid fa-check text-[9px]"></i>
                                    </div>
                                    <span class="text-sm font-bold text-slate-700 leading-snug">{{ $feature }}</span>
                                </div>
                            @endforeach

                            @foreach($plan['missing'] as $feature)
                                <div class="flex items-start gap-4 opacity-40">
                                    <div class="mt-1 w-5 h-5 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center shrink-0 border border-slate-200">
                                        <i class="fa-solid fa-xmark text-[9px]"></i>
                                    </div>
                                    <span class="text-sm font-bold text-slate-500 leading-snug">{{ $feature }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- mt-auto guarantees perfect bottom alignment across all cards --}}
                        <div class="mt-auto relative z-10">
                            <a href="{{ route('register') ?? '#' }}" @mouseenter="playHover()" class="magnetic-target w-full block text-center px-8 py-5 rounded-2xl font-black text-xs uppercase tracking-widest transition-all duration-300 focus:outline-none cursor-none
                                      {{ $plan['highlight'] ? 'bg-[#7284b5] text-white hover:bg-[#616dab] shadow-[0_10px_20px_rgba(114,132,181,0.3)] hover:shadow-[0_15px_30px_rgba(114,132,181,0.5)]' : 'bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100 hover:border-slate-300' }}">
                                {{ $plan['btn'] }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= 3. ADD-ON CONFIGURATOR (NEW FUN) ================= --}}
    <section class="py-20 relative z-10 bg-[#fcf9f2] border-y border-[#bacdf3]/30">
        <div class="max-w-[1000px] mx-auto px-4 sm:px-6 lg:px-8 reveal-up">
            <div class="text-center mb-12">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-4"><i class="fa-solid fa-puzzle-piece text-[#7284b5] mr-2"></i> Scale your Infrastructure</h3>
                <p class="text-slate-500 font-medium">Require specific architectural upgrades? Attach dedicated modules to your base node.</p>
            </div>

            <div class="bg-white rounded-[2rem] border border-[#bacdf3] shadow-[0_20px_50px_-15px_rgba(114,132,181,0.15)] overflow-hidden">
                <div class="grid md:grid-cols-2 lg:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-100">
                    @foreach($addons as $addon)
                        <div class="p-8 hover:bg-slate-50 transition-colors cursor-pointer group" @click="toggleAddon('{{ $addon['id'] }}'); playClick()" @mouseenter="playHover()">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-black text-slate-900">{{ $addon['name'] }}</h4>
                                <div class="w-6 h-6 rounded border transition-colors flex items-center justify-center shadow-sm"
                                     :class="addons.includes('{{ $addon['id'] }}') ? 'bg-[#7284b5] border-[#7284b5] text-white' : 'bg-white border-slate-200 text-transparent'">
                                    <i class="fa-solid fa-check text-[10px]"></i>
                                </div>
                            </div>
                            <p class="text-xs text-slate-500 font-medium mb-6 leading-relaxed">{{ $addon['desc'] }}</p>
                            <p class="text-lg font-black text-[#7284b5] font-mono tracking-tight">+<span x-text="currencySymbol">₹</span><span x-text="convertValue({{ $addon['price'] }})">{{ $addon['price'] }}</span><span class="text-[10px] text-slate-400 uppercase tracking-widest ml-1">/mo</span></p>
                        </div>
                    @endforeach
                </div>
                
                {{-- Live Addon Total --}}
                <div class="bg-slate-900 p-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-white">
                    <span class="text-xs font-black uppercase tracking-widest text-[#bacdf3]">Dynamic Module Total</span>
                    <span class="text-2xl font-black font-mono tracking-tighter"><span x-text="currencySymbol">₹</span><span x-text="addonTotal">0</span><span class="text-xs text-slate-400 ml-1">/mo</span></span>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= 4. THE ROI PLAN MATCHER (TRUE COST SIMULATOR) ================= --}}
    <section class="py-24 relative z-10 border-b border-[#bacdf3]/30 bg-white">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 text-center reveal-up">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-8"><i class="fa-solid fa-chart-line text-[#7284b5] mr-2"></i> True-Cost ROI Simulator</h3>
            
            <div class="bg-[#fcf9f2] p-10 rounded-[3rem] border border-[#bacdf3] shadow-sm relative overflow-hidden">
                <div class="absolute right-0 top-0 w-64 h-64 bg-[#bacdf3]/10 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="grid md:grid-cols-2 gap-12 items-center relative z-10">
                    <div class="text-left bg-white p-8 rounded-[2rem] border border-[#bacdf3]/50 shadow-[0_10px_30px_-15px_rgba(114,132,181,0.2)]">
                        <div class="flex justify-between items-end mb-6">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Monthly Ledger Volume</span>
                            <span class="text-3xl font-black text-[#7284b5] font-mono tracking-tight"><span x-text="txVolume"></span></span>
                        </div>
                        
                        <input type="range" x-model="txVolume" min="10" max="10000" step="50" @input="playKeySound()" 
                               class="w-full h-3 bg-slate-100 rounded-full appearance-none cursor-pointer border border-[#bacdf3] accent-[#7284b5] outline-none hover:bg-[#bacdf3]/30 transition-colors">
                        
                        <div class="flex justify-between mt-4 text-[9px] font-bold text-slate-400 font-mono">
                            <span>10 tx</span>
                            <span>5,000 tx</span>
                            <span>10,000+ tx</span>
                        </div>
                    </div>

                    <div class="text-left flex flex-col h-full justify-center">
                        <p class="text-[10px] font-black uppercase tracking-widest text-emerald-600 mb-2"><i class="fa-solid fa-clock-rotate-left"></i> Estimated Reconcilliation Time Saved</p>
                        <div class="flex items-end gap-2 mb-6 border-b border-slate-200 pb-6">
                            <span class="text-6xl font-black text-slate-900 tracking-tighter" x-text="hoursSaved"></span>
                            <span class="text-sm font-bold text-slate-500 mb-2">hours per month</span>
                        </div>
                        
                        <div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 block mb-3">Optimal Architecture Required</span>
                            <span class="inline-flex px-5 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-300 shadow-sm w-full justify-center"
                                  :class="{
                                      'bg-white text-slate-600 border border-slate-200': recommendedPlan === 'core',
                                      'bg-[#7284b5] text-white border border-[#7284b5] shadow-md': recommendedPlan === 'pro',
                                      'bg-slate-900 text-white border border-slate-800 shadow-md': recommendedPlan === 'enterprise'
                                  }">
                                <i class="fa-solid mt-0.5" :class="recommendedPlan === 'core' ? 'fa-user' : (recommendedPlan === 'pro' ? 'fa-network-wired' : 'fa-server')"></i>
                                <span x-text="recommendedPlan === 'core' ? 'Developer Node (Free)' : (recommendedPlan === 'pro' ? 'Professional Hub (₹499/mo)' : 'Enterprise Scale (Custom)')" class="ml-2"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= 5. FEATURE COMPARISON MATRIX (FLAWLESS MOBILE STICKY) ================= --}}
    <section class="py-32 bg-white relative z-10 border-y border-[#bacdf3]/40" id="matrix">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center mb-20 reveal-up">
                <h2 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight mb-4">Architectural Matrix</h2>
                <p class="text-slate-500 font-medium text-lg">A deep technical breakdown of node capabilities.</p>
            </div>

            {{-- Flawless Mobile-friendly overflow wrapper --}}
            <div class="overflow-x-auto scrollbar-custom reveal-up bg-white rounded-[2rem] border border-slate-200 shadow-sm relative" style="transition-delay: 100ms; max-height: 800px;">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr>
                            {{-- Dual Sticky: Top AND Left --}}
                            <th class="w-1/4 p-6 text-xs font-black uppercase tracking-widest text-slate-400 border-b border-slate-200 sticky top-0 left-0 bg-white z-40 shadow-[4px_4px_12px_rgba(0,0,0,0.05)]">System Capability</th>
                            
                            {{-- Sticky Top Only --}}
                            <th class="min-w-[200px] p-6 text-center text-xs font-black uppercase tracking-widest text-slate-900 border-b border-slate-200 sticky top-0 bg-slate-50/95 backdrop-blur z-30">Core Node</th>
                            <th class="min-w-[200px] p-6 text-center text-xs font-black uppercase tracking-widest text-[#7284b5] border-b border-[#bacdf3] sticky top-0 bg-[#fcf9f2]/95 backdrop-blur z-30 shadow-sm">
                                <div class="absolute top-0 inset-x-0 h-1 bg-[#7284b5]"></div>
                                Pro Hub
                            </th>
                            <th class="min-w-[200px] p-6 text-center text-xs font-black uppercase tracking-widest text-slate-900 border-b border-slate-200 sticky top-0 bg-slate-50/95 backdrop-blur z-30">Enterprise</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach($matrix as $category => $features)
                            <tr>
                                {{-- Sticky Left Category Header --}}
                                <td colspan="4" class="pt-10 pb-4 px-6 text-[10px] font-black uppercase tracking-widest text-[#7284b5] bg-white sticky left-0 z-20 shadow-[4px_0_12px_rgba(0,0,0,0.03)]"><i class="fa-solid fa-microchip mr-2 opacity-50"></i> {{ $category }}</td>
                            </tr>
                            @foreach($features as $feature)
                                <tr class="group hover:bg-slate-50 transition-colors">
                                    
                                    {{-- Feature Name (Sticky Left with explicit bg to hide scrolling content) --}}
                                    <td class="p-6 border-b border-slate-100 font-bold text-slate-700 sticky left-0 bg-white group-hover:bg-slate-50 transition-colors shadow-[4px_0_12px_rgba(0,0,0,0.05)] z-20 flex items-center gap-2">
                                        <span class="{{ isset($feature['tooltip']) ? 'border-b border-dashed border-slate-400 cursor-help' : '' }}">{{ $feature['name'] }}</span>
                                        
                                        {{-- Intelligent Tooltip --}}
                                        @if(isset($feature['tooltip']))
                                            <div class="relative group/tooltip inline-block" @mouseenter="playHover()">
                                                <i class="fa-solid fa-circle-info text-slate-300 text-[10px] hover:text-[#7284b5] transition-colors ml-1"></i>
                                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-56 p-4 bg-slate-900 text-white text-xs font-medium rounded-xl shadow-xl opacity-0 invisible group-hover/tooltip:opacity-100 group-hover/tooltip:visible transition-all duration-200 z-50 pointer-events-none text-center leading-relaxed">
                                                    {{ $feature['tooltip'] }}
                                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-900"></div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Data Cells --}}
                                    <td class="p-6 border-b border-slate-100 text-center bg-slate-50/20 group-hover:bg-transparent transition-colors z-10 relative">
                                        @if($feature['core'] === true) <i class="fa-solid fa-check text-emerald-500"></i>
                                        @elseif($feature['core'] === false) <i class="fa-solid fa-minus text-slate-300"></i>
                                        @else <span class="font-bold text-slate-500 text-xs">{{ $feature['core'] }}</span> @endif
                                    </td>

                                    <td class="p-6 border-b border-slate-100 text-center bg-[#fcf9f2]/60 group-hover:bg-[#fcf9f2] transition-colors border-x border-[#bacdf3]/30 z-10 relative">
                                        @if($feature['pro'] === true) <i class="fa-solid fa-check text-[#7284b5] text-lg"></i>
                                        @elseif($feature['pro'] === false) <i class="fa-solid fa-minus text-[#bacdf3]"></i>
                                        @else <span class="font-bold text-[#7284b5] text-xs">{{ $feature['pro'] }}</span> @endif
                                    </td>

                                    <td class="p-6 border-b border-slate-100 text-center bg-slate-50/20 group-hover:bg-transparent transition-colors z-10 relative">
                                        @if($feature['ent'] === true) <i class="fa-solid fa-check text-slate-900"></i>
                                        @elseif($feature['ent'] === false) <i class="fa-solid fa-minus text-slate-300"></i>
                                        @else <span class="font-bold text-slate-900 text-xs">{{ $feature['ent'] }}</span> @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- Mobile scroll indicator --}}
            <div class="mt-6 text-center md:hidden">
                <span class="text-[10px] font-black uppercase tracking-widest text-[#7284b5] bg-[#fcf9f2] px-4 py-2 rounded-full border border-[#bacdf3] shadow-sm animate-pulse inline-flex items-center gap-2">
                    <i class="fa-solid fa-arrows-left-right"></i> Swipe matrix horizontally
                </span>
            </div>

        </div>
    </section>

    {{-- ================= 6. SEARCHABLE ACCORDION FAQ ================= --}}
    <section class="py-32 bg-[#fcf9f2] relative z-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center mb-10 reveal-up">
                <h2 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight mb-6">System Inquiries</h2>
            </div>

            <div x-data="faqEngine()" class="reveal-up" style="transition-delay: 100ms;">
                {{-- FAQ Search --}}
                <div class="relative mb-10">
                    <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-[#9fb2df]"></i>
                    <input type="text" x-model="searchQuery" @input="playKeySound()" placeholder="Search deployment questions..." 
                           class="w-full pl-12 pr-4 py-4 bg-white border border-[#bacdf3] rounded-2xl text-sm font-bold text-slate-900 placeholder-slate-400 outline-none transition-all focus:ring-4 focus:ring-[#bacdf3]/20 shadow-sm">
                </div>

                <div class="space-y-4">
                    <template x-for="(faq, index) in filteredFaqs" :key="index">
                        <div class="bg-white border border-[#bacdf3]/50 rounded-[1.5rem] overflow-hidden shadow-sm hover:shadow-md hover:border-[#bacdf3] transition-all duration-300">
                            <button @click="selected !== index ? selected = index : selected = null; playClick()" @mouseenter="playHover()" class="w-full flex items-center justify-between p-6 text-left focus:outline-none hover:bg-slate-50 transition-colors magnetic-target cursor-none">
                                <span class="font-black text-lg text-slate-900 group-hover:text-[#7284b5] transition-colors pr-4" x-text="faq.q"></span>
                                <span class="w-10 h-10 rounded-full bg-[#fcf9f2] flex items-center justify-center transform transition-all duration-500 text-[#7284b5] border border-[#bacdf3] shrink-0"
                                      :class="selected === index ? 'rotate-180 bg-[#7284b5] text-white border-[#7284b5]' : ''">
                                    <i class="fa-solid fa-chevron-down text-sm"></i>
                                </span>
                            </button>
                            <div class="relative overflow-hidden transition-all duration-500 cubic-bezier(0.4, 0, 0.2, 1)" 
                                 :style="selected === index ? 'max-height: 500px;' : 'max-height: 0px;'">
                                <div class="px-6 pb-8 text-sm text-slate-600 font-medium leading-relaxed border-t border-slate-100 pt-4 mt-2" x-text="faq.a"></div>
                            </div>
                        </div>
                    </template>
                    <div x-show="filteredFaqs.length === 0" class="text-center py-10" style="display: none;">
                        <p class="text-sm font-bold text-slate-500">No matching inquiries found.</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

</div>

@endsection

@push('styles')
<style>
    /* PURE CSS PATTERNS */
    .pattern-grid-lg { background-image: linear-gradient(#bacdf3 1px, transparent 1px), linear-gradient(90deg, #bacdf3 1px, transparent 1px); background-size: 40px 40px; }
    
    /* Elegant Scrollbar for Matrix */
    .scrollbar-custom::-webkit-scrollbar { height: 6px; width: 6px; }
    .scrollbar-custom::-webkit-scrollbar-track { background: #f8fafc; border-radius: 10px; }
    .scrollbar-custom::-webkit-scrollbar-thumb { background: #bacdf3; border-radius: 10px; }
    .scrollbar-custom::-webkit-scrollbar-thumb:hover { background: #7284b5; }

    /* Reveal Animations */
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .reveal-up { opacity: 0; transform: translateY(30px); transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
    .reveal-up.is-visible { opacity: 1; transform: translateY(0); }
    
    @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-20px); } }
    .animate-float { animation: float 8s ease-in-out infinite; }

    @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
    .animate-marquee { animation: marquee 30s linear infinite; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    
    // Core Pricing Engine
    Alpine.data('pricingEngine', () => ({
        annual: true,
        txVolume: 150,
        currencyOpen: false,
        currency: 'INR',
        
        // Exchange rates injected from PHP
        rates: @json($exchangeRates),
        symbols: { 'INR': '₹', 'USD': '$', 'EUR': '€', 'GBP': '£' },
        
        // Base data in INR (Matches backend payload)
        planData: {
            'core': { m: 0, y: 0 },
            'pro': { m: 499, y: 4999 }, 
        },
        
        // Active visual state (Gets tweened)
        prices: {
            'core': 0,
            'pro': 4999 
        },

        addons: [],
        addonTotal: 0,

        init() {
            // Scroll Animation Observer
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('is-visible'); });
            }, { threshold: 0.1 });
            document.querySelectorAll('.reveal-up').forEach(el => observer.observe(el));
            
            // Initial render
            this.toggleBilling(true);
        },

        playClick() { if(window.audioEngine) window.audioEngine.playClick(); },
        playHover() { if(window.audioEngine) window.audioEngine.playHover(); },
        playKeySound() {
            if(!window.audioEngine) return;
            if(!this.lastTick || Date.now() - this.lastTick > 50) {
                window.audioEngine.playClick(); 
                this.lastTick = Date.now();
            }
        },

        get currencySymbol() {
            return this.symbols[this.currency];
        },

        setCurrency(curr) {
            this.currency = curr;
            this.currencyOpen = false;
            // Retrigger the billing toggle to recalculate limits with new currency
            this.toggleBilling(true); 
            this.calculateAddons();
        },

        // Complex Numeric Tweening Engine for Price Toggles
        toggleBilling(force = false) {
            if(!force) this.annual = !this.annual;
            
            const rate = this.rates[this.currency];

            Object.keys(this.prices).forEach(key => {
                // Base calculation
                const baseTarget = this.annual ? this.planData[key].y : this.planData[key].m;
                // Convert currency
                const targetVal = baseTarget * rate;
                
                this.animateValue(key, this.prices[key], targetVal, 500);
            });
        },

        animateValue(key, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                // Cubic ease-out
                const easeProgress = 1 - Math.pow(1 - progress, 3);
                // Math.round handles decimals nicely for USD/EUR, but we format later
                this.prices[key] = start + easeProgress * (end - start);
                
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                } else {
                    this.prices[key] = end; // Snap to exact end value
                }
            };
            window.requestAnimationFrame(step);
        },

        // Helper for Alpine formatting based on currency
        formatPrice(key) {
            const val = this.prices[key];
            // If it's INR, no decimals. If USD/EUR, 2 decimals
            return this.currency === 'INR' ? Math.round(val).toLocaleString('en-IN') : val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        // Helper to format exact strings without tweening (for the strikethrough/subtitle)
        formatExact(key, isYearly) {
            const base = isYearly ? this.planData[key].y : this.planData[key].m;
            const converted = base * this.rates[this.currency];
            return this.currency === 'INR' ? Math.round(converted).toLocaleString('en-IN') : converted.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        // Format the "Monthly Equivalent" of the annual plan
        formatEquivalent(key) {
            const baseYearly = this.planData[key].y;
            const monthlyEquiv = (baseYearly / 12) * this.rates[this.currency];
            return this.currency === 'INR' ? Math.round(monthlyEquiv).toLocaleString('en-IN') : monthlyEquiv.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        // ADD-ON LOGIC
        toggleAddon(id) {
            if(this.addons.includes(id)) {
                this.addons = this.addons.filter(a => a !== id);
            } else {
                this.addons.push(id);
            }
            this.calculateAddons();
        },
        convertValue(val) {
            const converted = val * this.rates[this.currency];
            return this.currency === 'INR' ? Math.round(converted).toLocaleString('en-IN') : converted.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        calculateAddons() {
            // Find total base value of selected addons
            let total = 0;
            const addonPrices = { 'addon_db': 1500, 'addon_sla': 2000, 'addon_api': 800 };
            this.addons.forEach(id => total += addonPrices[id]);
            this.addonTotal = this.convertValue(total);
        },

        // Plan Matcher Logic
        get recommendedPlan() {
            if (this.txVolume < 500) return 'core';
            if (this.txVolume >= 500 && this.txVolume <= 5000) return 'pro';
            return 'enterprise';
        },

        get hoursSaved() {
            // Assume manual entry takes 3 mins per transaction
            return Math.round((this.txVolume * 3) / 60);
        },

        calculateRecommendation() {} // Keeps sound hook valid
    }));

    // FAQ Search Engine
    Alpine.data('faqEngine', () => ({
        searchQuery: '',
        selected: null,
        faqs: [
            {q:'Is my financial data secure?', a:'Absolutely. We use industry-standard AES-256 encryption via Laravel at rest and TLS 1.3 in transit. Your database connections are completely isolated and we never store plaintext credentials.'},
            {q:'Can I migrate from a Core Node to a Pro Hub later?', a:'Yes. You can upgrade your deployment tier at any time from your Identity Profile. Prorated charges will automatically apply to your billing cycle without any data migration downtime.'},
            {q:'Do you offer a Developer License for APIs?', a:'REST API access is currently restricted to Pro and Enterprise deployments to guarantee node stability. However, verified students can apply for a Sandbox API key by contacting our architecture team.'},
            {q:'How does the AI prediction work?', a:'Our ML engine takes your last 90 days of transactions, identifies recurring burn rates, and mathematically forecasts your likely balance at the end of the current month with 99.8% accuracy.'},
            {q:'Are there volume limits on the Pro Hub?', a:'The Pro Hub allows up to 5,000 transactions per month. If you exceed this volume, the system continues to operate normally, but we will contact you to upgrade to an Enterprise Node.'}
        ],

        get filteredFaqs() {
            if (this.searchQuery.trim() === '') return this.faqs;
            const q = this.searchQuery.toLowerCase();
            return this.faqs.filter(f => f.q.toLowerCase().includes(q) || f.a.toLowerCase().includes(q));
        },

        playClick() { if(window.audioEngine) window.audioEngine.playClick(); },
        playHover() { if(window.audioEngine) window.audioEngine.playHover(); },
        playKeySound() {
            if(!window.audioEngine) return;
            if(!this.lastTick || Date.now() - this.lastTick > 50) {
                window.audioEngine.playClick(); 
                this.lastTick = Date.now();
            }
        },
    }));
});
</script>
@endpush