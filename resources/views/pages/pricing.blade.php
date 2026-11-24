@extends('layouts.landing')
@include('partials.site-type')

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

<div class="paper" x-data="pricingEngine()">

    <x-site.hero eyebrow="Transparent Licensing" lead="Invest in absolute" accent="financial clarity."
        lede="Deploy the FinanceAI engine for your household or enterprise. Scalable infrastructure designed to generate absolute ROI within the first 30 days.">
        <x-slot name="actions">
            <div class="flex flex-wrap items-center gap-3">
                <div class="inline-flex items-center rounded-xl border border-white/15 bg-white/5 p-1" role="radiogroup" aria-label="Billing period">
                    <button type="button" role="radio" :aria-checked="(!annual).toString()" @click="if (annual) { toggleBilling(); playClick() }" @mouseenter="playHover()"
                            class="h-10 px-5 rounded-lg text-sm font-bold transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-white/40"
                            :class="!annual ? 'bg-white text-slate-900' : 'text-slate-300 hover:text-white'">Monthly</button>
                    <button type="button" role="radio" :aria-checked="annual.toString()" @click="if (!annual) { toggleBilling(); playClick() }" @mouseenter="playHover()"
                            class="h-10 px-5 rounded-lg text-sm font-bold transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-white/40"
                            :class="annual ? 'bg-white text-slate-900' : 'text-slate-300 hover:text-white'">Annually</button>
                </div>

                <div class="relative" @click.away="currencyOpen = false">
                    <button type="button" @click="currencyOpen = !currencyOpen; playClick()" :aria-expanded="currencyOpen.toString()"
                            class="h-12 px-4 rounded-xl border border-white/15 bg-white/5 text-sm font-bold text-white hover:bg-white/10 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-white/40">
                        <i class="fa-solid fa-globe text-slate-400 mr-1.5" aria-hidden="true"></i><span x-text="currency"></span><i class="fa-solid fa-chevron-down text-[9px] ml-2" aria-hidden="true"></i>
                    </button>
                    <div x-show="currencyOpen" x-cloak x-transition.opacity class="absolute top-full left-0 mt-2 w-36 bg-white rounded-xl shadow-xl overflow-hidden z-50 py-1">
                        @foreach(['INR' => '₹', 'USD' => '$', 'EUR' => '€', 'GBP' => '£'] as $code => $sym)
                            <button type="button" @click="setCurrency('{{ $code }}'); playClick()" class="w-full text-left px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" :class="currency === '{{ $code }}' ? 'text-indigo-600' : ''">{{ $code }} ({{ $sym }})</button>
                        @endforeach
                    </div>
                </div>
            </div>
        </x-slot>
        <x-slot name="meta">
            <p class="font-mono text-xs uppercase tracking-wider text-emerald-400"><i class="fa-solid fa-gift mr-1.5" aria-hidden="true"></i>Save 16% on Annual Deployment</p>
        </x-slot>
    </x-site.hero>

    {{-- Logos --}}
    <section class="border-b border-slate-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col md:flex-row md:items-center gap-5 md:gap-12">
            <p class="eyebrow text-slate-400 shrink-0">Trusted by modern financial architects at</p>
            <div class="flex flex-wrap items-center gap-x-10 gap-y-3 text-lg font-black uppercase tracking-tighter text-slate-300">
                <span>TechFlow</span>
                <span><i class="fa-solid fa-bolt text-base mr-1" aria-hidden="true"></i>Velocity</span>
                <span>DataCore Inc</span>
                <span><i class="fa-solid fa-wave-square text-base mr-1" aria-hidden="true"></i>Nexus Finance</span>
                <span>Stark Industries</span>
            </div>
        </div>
    </section>

    {{-- Plans --}}
    <section class="py-20 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-5 items-stretch">
                @foreach($plans as $idx => $plan)
                    @php $hl = $plan['highlight']; @endphp
                    <article class="relative flex flex-col rounded-2xl p-8 sm:p-9 {{ $hl ? 'bg-[#0b0b1a] text-white grain lg:-my-4 lg:py-12' : 'card-site' }}" data-reveal style="--d: {{ $idx * 90 }}ms">
                        <div class="flex items-center justify-between">
                            <p class="eyebrow {{ $hl ? 'text-indigo-300' : 'text-slate-400' }}">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }} — {{ $plan['id'] === 'core' ? 'Free' : ($plan['id'] === 'pro' ? 'Pro' : 'Scale') }}</p>
                            @if($hl)
                                <span class="font-mono text-[10px] uppercase tracking-wider text-amber-300"><i class="fa-solid fa-star mr-1" aria-hidden="true"></i>Optimal Choice</span>
                            @endif
                        </div>

                        <h2 class="mt-6 text-3xl font-black tracking-tight {{ $hl ? 'text-white' : 'text-slate-900' }}">{{ $plan['name'] }}</h2>
                        <p class="mt-3 text-[15px] leading-relaxed min-h-[3rem] {{ $hl ? 'text-slate-300' : 'text-slate-600' }}">{{ $plan['desc'] }}</p>

                        <div class="mt-8 pb-8 border-b {{ $hl ? 'border-white/10' : 'border-slate-200' }} min-h-[118px] flex flex-col justify-end">
                            @if($plan['base_monthly'] === -1)
                                <p class="font-serif-i text-6xl {{ $hl ? 'text-white' : 'text-slate-900' }}">Custom</p>
                                <p class="mt-2 text-sm {{ $hl ? 'text-slate-400' : 'text-slate-500' }}">Tailored to your architectural scale</p>
                            @else
                                <p class="flex items-end gap-1">
                                    <span class="text-2xl font-bold mb-1.5 {{ $hl ? 'text-slate-400' : 'text-slate-400' }}" x-text="currencySymbol">₹</span>
                                    <span class="text-6xl font-black tracking-tighter tabular-nums {{ $hl ? 'text-white' : 'text-slate-900' }}" x-text="formatPrice('{{ $plan['id'] }}')">{{ number_format($plan['base_monthly']) }}</span>
                                    <span class="text-sm font-semibold mb-2 {{ $hl ? 'text-slate-400' : 'text-slate-400' }}" x-text="annual ? '/yr' : '/mo'">/mo</span>
                                </p>
                                <p class="mt-3 h-5 text-sm {{ $hl ? 'text-slate-400' : 'text-slate-500' }}">
                                    @if($plan['base_yearly'] > 0)
                                        <span x-show="annual" x-cloak>
                                            <span class="line-through" x-text="currencySymbol + formatExact('{{ $plan['id'] }}', false) + '/mo'"></span>
                                            <span class="ml-1 font-mono text-[11px] uppercase tracking-wider {{ $hl ? 'text-indigo-300' : 'text-indigo-600' }}">Equivalent to <span x-text="currencySymbol + formatEquivalent('{{ $plan['id'] }}')"></span>/mo</span>
                                        </span>
                                        <span x-show="!annual">Billed exactly <span x-text="currencySymbol + formatExact('{{ $plan['id'] }}', false)"></span> every month</span>
                                    @else
                                        Free forever. No credit card required.
                                    @endif
                                </p>
                            @endif
                        </div>

                        <ul class="mt-8 flex-1 space-y-3.5">
                            @foreach($plan['features'] as $feature)
                                <li class="flex gap-3 text-[15px] {{ $hl ? 'text-slate-200' : 'text-slate-700' }}"><i class="fa-solid fa-check mt-1 text-xs {{ $hl ? 'text-indigo-300' : 'text-emerald-600' }}" aria-hidden="true"></i>{{ $feature }}</li>
                            @endforeach
                            @foreach($plan['missing'] as $feature)
                                <li class="flex gap-3 text-[15px] {{ $hl ? 'text-slate-500' : 'text-slate-400' }} line-through decoration-1"><i class="fa-solid fa-xmark mt-1 text-xs" aria-hidden="true"></i>{{ $feature }}</li>
                            @endforeach
                        </ul>

                        <a href="{{ $plan['id'] === 'enterprise' ? route('contact') : route('register') }}" @mouseenter="playHover()"
                           class="mt-10 inline-flex items-center justify-center gap-2 h-12 rounded-xl font-bold transition-colors focus:outline-none focus-visible:ring-4
                                  {{ $hl ? 'text-white bg-gradient-to-br from-indigo-500 to-violet-600 hover:from-indigo-400 focus-visible:ring-indigo-400/40' : 'bg-slate-900 text-white hover:bg-indigo-600 focus-visible:ring-indigo-200' }}">
                            {{ $plan['btn'] }} <i class="fa-solid fa-arrow-right text-sm" aria-hidden="true"></i>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Add-ons --}}
    <section class="py-20 sm:py-24 bg-white border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-site.section-head label="Add-ons" title="Scale your" accent="Infrastructure"
                lede="Require specific architectural upgrades? Attach dedicated modules to your base node." />

            <div class="mt-12 grid md:grid-cols-3 border-t border-l border-slate-200" data-reveal>
                @foreach($addons as $addon)
                    <button type="button" @click="toggleAddon('{{ $addon['id'] }}'); playClick()" @mouseenter="playHover()" :aria-pressed="addons.includes('{{ $addon['id'] }}').toString()"
                            class="text-left p-7 border-b border-r border-slate-200 transition-colors focus:outline-none focus-visible:bg-slate-50"
                            :class="addons.includes('{{ $addon['id'] }}') ? 'bg-indigo-50/60' : 'hover:bg-slate-50'">
                        <span class="flex items-start justify-between gap-4">
                            <span class="text-lg font-black tracking-tight text-slate-900">{{ $addon['name'] }}</span>
                            <span class="h-5 w-5 shrink-0 rounded border flex items-center justify-center transition-colors" :class="addons.includes('{{ $addon['id'] }}') ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-slate-300 text-transparent'" aria-hidden="true"><i class="fa-solid fa-check text-[10px]"></i></span>
                        </span>
                        <span class="mt-2 block text-[15px] text-slate-600 leading-relaxed">{{ $addon['desc'] }}</span>
                        <span class="mt-6 block font-mono text-sm text-slate-900">+<span x-text="currencySymbol">₹</span><span x-text="convertValue({{ $addon['price'] }})">{{ $addon['price'] }}</span><span class="text-slate-400">/mo</span></span>
                    </button>
                @endforeach
            </div>
            <div class="flex items-center justify-between gap-4 border-b border-slate-900 py-5">
                <span class="eyebrow text-slate-500">Dynamic Module Total</span>
                <span class="text-2xl font-black tabular-nums text-slate-900"><span x-text="currencySymbol">₹</span><span x-text="addonTotal">0</span><span class="text-sm font-semibold text-slate-400 ml-1">/mo</span></span>
            </div>
        </div>
    </section>

    {{-- ROI matcher --}}
    <section class="py-20 sm:py-24 bg-[#0b0b1a] text-white grain">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-6" data-reveal>
                <p class="eyebrow text-indigo-300">True-Cost ROI Simulator</p>
                <div class="mt-6 flex items-end justify-between">
                    <label for="tx-volume" class="font-mono text-[11px] uppercase tracking-wider text-slate-400">Monthly Ledger Volume</label>
                    <span class="text-3xl font-black tabular-nums" x-text="txVolume"></span>
                </div>
                <input id="tx-volume" type="range" x-model="txVolume" min="10" max="10000" step="50" @input="playKeySound()" class="roi-range mt-5 w-full" :style="`--p: ${(txVolume - 10) / 99.9}%`">
                <div class="mt-3 flex justify-between font-mono text-[10px] uppercase tracking-wider text-slate-500"><span>10 tx</span><span>5,000 tx</span><span>10,000+ tx</span></div>
            </div>
            <div class="lg:col-span-6 border-t lg:border-t-0 lg:border-l border-white/15 pt-8 lg:pt-0 lg:pl-12" data-reveal style="--d: 120ms">
                <p class="font-mono text-[11px] uppercase tracking-wider text-emerald-400">Estimated Reconcilliation Time Saved</p>
                <p class="mt-3 flex items-end gap-3"><span class="text-7xl font-black tracking-tighter tabular-nums" x-text="hoursSaved"></span><span class="font-serif-i text-2xl text-indigo-300 mb-2">hours per month</span></p>
                <p class="mt-8 font-mono text-[11px] uppercase tracking-wider text-slate-500">Optimal Architecture Required</p>
                <p class="mt-2 text-xl font-black" x-text="recommendedPlan === 'core' ? 'Developer Node (Free)' : (recommendedPlan === 'pro' ? 'Professional Hub (₹499/mo)' : 'Enterprise Scale (Custom)')"></p>
            </div>
        </div>
    </section>

    {{-- Matrix --}}
    <section class="py-20 sm:py-28" id="matrix">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-site.section-head label="Compare" title="Architectural" accent="Matrix"
                lede="A deep technical breakdown of node capabilities." />

            <div class="mt-12 overflow-x-auto scrollbar-custom" data-reveal>
                <table class="w-full text-left min-w-[760px]">
                    <thead>
                        <tr class="border-b border-slate-900">
                            <th class="w-1/4 py-4 pr-4 eyebrow font-normal text-slate-500 sticky left-0 bg-[#fbfbfa]">System Capability</th>
                            <th class="py-4 px-4 text-center text-sm font-black text-slate-900">Core Node</th>
                            <th class="py-4 px-4 text-center text-sm font-black text-indigo-600 bg-indigo-50/60">Pro Hub</th>
                            <th class="py-4 px-4 text-center text-sm font-black text-slate-900">Enterprise</th>
                        </tr>
                    </thead>
                    <tbody class="text-[15px]">
                        @foreach($matrix as $category => $features)
                            <tr>
                                <td colspan="4" class="pt-10 pb-3 eyebrow text-indigo-600 sticky left-0">{{ $category }}</td>
                            </tr>
                            @foreach($features as $feature)
                                <tr class="border-b border-slate-200 hover:bg-white transition-colors">
                                    <td class="py-4 pr-4 font-semibold text-slate-800 sticky left-0 bg-[#fbfbfa]">
                                        <span class="inline-flex items-center gap-1.5">
                                            <span class="{{ isset($feature['tooltip']) ? 'border-b border-dashed border-slate-400 cursor-help' : '' }}" @if(isset($feature['tooltip'])) title="{{ $feature['tooltip'] }}" @endif>{{ $feature['name'] }}</span>
                                        </span>
                                    </td>
                                    @foreach(['core' => 'text-slate-600', 'pro' => 'text-indigo-700', 'ent' => 'text-slate-900'] as $col => $tone)
                                        <td class="py-4 px-4 text-center {{ $col === 'pro' ? 'bg-indigo-50/60' : '' }}">
                                            @if($feature[$col] === true) <i class="fa-solid fa-check {{ $col === 'pro' ? 'text-indigo-600' : 'text-emerald-600' }}" aria-label="Included"></i>
                                            @elseif($feature[$col] === false) <span class="text-slate-300" aria-label="Not included">—</span>
                                            @else <span class="font-mono text-xs {{ $tone }}">{{ $feature[$col] }}</span> @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="mt-4 font-mono text-[11px] uppercase tracking-wider text-slate-400 md:hidden"><i class="fa-solid fa-arrows-left-right mr-1" aria-hidden="true"></i>Swipe matrix horizontally</p>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="py-20 sm:py-28 bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-12">
            <div class="lg:col-span-4">
                <x-site.section-head label="FAQ" title="System" accent="Inquiries" />
            </div>

            <div x-data="faqEngine()" class="lg:col-span-8" data-reveal style="--d: 100ms">
                <label class="relative block">
                    <span class="sr-only">Search questions</span>
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400" aria-hidden="true"></i>
                    <input type="text" x-model="searchQuery" @input="playKeySound()" placeholder="Search deployment questions..."
                           class="w-full h-11 pl-9 pr-3 rounded-lg border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                </label>

                <div class="mt-6 border-t border-slate-900">
                    <template x-for="(faq, index) in filteredFaqs" :key="index">
                        <div class="border-b border-slate-200">
                            <button type="button" @click="selected !== index ? selected = index : selected = null; playClick()" @mouseenter="playHover()" :aria-expanded="(selected === index).toString()"
                                    class="w-full flex items-center justify-between gap-4 py-5 text-left focus:outline-none focus-visible:bg-slate-50">
                                <span class="text-lg font-bold text-slate-900" x-text="faq.q"></span>
                                <i class="fa-solid fa-plus text-sm text-slate-400 transition-transform duration-300" :class="selected === index ? 'rotate-45 text-slate-900' : ''" aria-hidden="true"></i>
                            </button>
                            <div class="overflow-hidden transition-all duration-500" :style="selected === index ? 'max-height: 500px;' : 'max-height: 0px;'">
                                <p class="pb-6 pr-10 text-[15px] text-slate-600 leading-relaxed" x-text="faq.a"></p>
                            </div>
                        </div>
                    </template>
                    <p x-show="filteredFaqs.length === 0" x-cloak class="py-10 text-center text-slate-500">No matching inquiries found.</p>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection

@push('styles')
<style>
    .scrollbar-custom::-webkit-scrollbar { height: 6px; }
    .scrollbar-custom::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .roi-range { -webkit-appearance: none; appearance: none; height: 4px; border-radius: 9999px; outline: none;
        background: linear-gradient(to right, #a5b4fc var(--p, 2%), rgba(255,255,255,.15) var(--p, 2%)); }
    .roi-range::-webkit-slider-thumb { -webkit-appearance: none; width: 22px; height: 22px; border-radius: 9999px; background: #fff; border: 2px solid #a5b4fc; cursor: pointer; }
    .roi-range::-moz-range-thumb { width: 20px; height: 20px; border-radius: 9999px; background: #fff; border: 2px solid #a5b4fc; cursor: pointer; }
    .roi-range:focus-visible::-webkit-slider-thumb { box-shadow: 0 0 0 6px rgba(165,180,252,.3); }
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