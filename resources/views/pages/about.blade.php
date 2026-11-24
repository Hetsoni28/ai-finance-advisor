@extends('layouts.landing')
@include('partials.site-type')

@section('title', 'The Company | FinanceAI Enterprise')
@section('meta_description', 'Discover the mission, global infrastructure, and the architectural team behind FinanceAI.')

@section('content')
@php
    $principles = [
        ['fa-bolt', 'Sub-Millisecond Latency', 'Financial telemetry is useless if it\'s delayed. Our databases are indexed for instant retrieval.'],
        ['fa-lock', 'Mathematical Truth', 'Ledgers are immutable. Once a transaction is hashed, it cannot be silently altered.'],
        ['fa-wand-magic-sparkles', 'Frictionless UX', 'Enterprise power should not require an enterprise manual. Design must be intuitive.'],
        ['fa-network-wired', 'Distributed Redundancy', 'No single point of failure. Your data is sharded and backed up across multiple global nodes.'],
    ];
    $team = [
        ['F', 'Founder & CEO', 'Vision & Strategy', 'Directing the global architecture and defining the AI integration pathways.', 'Level 5 Clearance', 'fa-linkedin-in'],
        ['T', 'Head of Technology', 'Infrastructure', 'Securing the ledger cryptography and maintaining 99.9% network uptime.', 'Level 4 Clearance', null],
        ['G', 'Head of Growth', 'Global Scaling', 'Deploying FinanceAI systems to organizational nodes and institutional partners.', 'Level 3 Clearance', 'fa-twitter'],
    ];
    $regions = [
        'us-east' => ['US-East (N. Virginia)', 'US'],
        'eu-central' => ['EU-Central (Frankfurt)', 'EU'],
        'ap-south' => ['AP-South (Singapore)', 'SG'],
    ];
@endphp
<div class="paper" x-data="aboutEngine()">

    <x-site.hero eyebrow="The FinanceAI Architecture" lead="We are building the future of" accent="financial intelligence."
        lede="FinanceAI empowers organizational nodes and high-net-worth families with real-time telemetry, predictive AI heuristics, and cryptographic ledgers. We replace chaos with absolute mathematical truth.">
        <x-slot name="meta">
            <dl class="grid grid-cols-2 sm:grid-cols-4 gap-6">
                <div>
                    <dt class="font-mono text-[11px] uppercase tracking-wider text-slate-400">Live Nodes</dt>
                    <dd class="mt-1 text-xl sm:text-2xl font-black text-white tabular-nums" x-text="liveNodes.toLocaleString()"></dd>
                </div>
                <div class="col-span-2">
                    <dt class="font-mono text-[11px] uppercase tracking-wider text-slate-400">Capital Secured (USD)</dt>
                    <dd class="mt-1 text-xl sm:text-2xl font-black text-white tabular-nums" x-text="'$' + capitalSecured.toLocaleString()"></dd>
                </div>
                <div>
                    <dt class="font-mono text-[11px] uppercase tracking-wider text-slate-400">AI Ops/Sec</dt>
                    <dd class="mt-1 text-xl sm:text-2xl font-black text-white tabular-nums" x-text="aiOps"></dd>
                </div>
            </dl>
        </x-slot>
    </x-site.hero>

    {{-- Backers --}}
    <section class="border-b border-slate-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex flex-col md:flex-row md:items-center gap-6 md:gap-12">
            <p class="eyebrow text-slate-400 shrink-0">Backed by Tier-1 Capital &amp; Infrastructure</p>
            <div class="flex flex-wrap items-center gap-x-12 gap-y-4 text-slate-400">
                <span class="text-xl font-black tracking-tighter">SEQUOIA/</span>
                <span class="text-xl font-black tracking-tighter">a16z</span>
                <span class="text-xl font-black tracking-tighter"><i class="fa-brands fa-y-combinator mr-1" aria-hidden="true"></i>Combinator</span>
                <span class="text-base font-black tracking-widest uppercase"><i class="fa-brands fa-aws mr-1" aria-hidden="true"></i>Partner</span>
            </div>
        </div>
    </section>

    {{-- Mission & vision --}}
    <section class="py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 border-t border-slate-900">
                <article class="py-10 lg:pr-12 lg:border-r border-slate-200" data-reveal>
                    <p class="eyebrow text-indigo-600">01 — Mission</p>
                    <h2 class="mt-4 text-3xl sm:text-4xl font-black tracking-[-0.03em] text-slate-900">The Mission <span class="font-serif-i text-indigo-600">Directive</span></h2>
                    <p class="mt-5 text-lg text-slate-600 leading-relaxed">To simplify complex financial systems using deterministic automation and artificial intelligence, allowing users to focus entirely on capital growth rather than manual tracking and spreadsheet maintenance.</p>
                </article>
                <article class="py-10 lg:pl-12 border-t lg:border-t-0 border-slate-200" data-reveal style="--d: 100ms">
                    <p class="eyebrow text-indigo-600">02 — Vision</p>
                    <h2 class="mt-4 text-3xl sm:text-4xl font-black tracking-[-0.03em] text-slate-900">The Global <span class="font-serif-i text-indigo-600">Vision</span></h2>
                    <p class="mt-5 text-lg text-slate-600 leading-relaxed">A world where financial decisions are inherently intelligent, highly predictive, and effortlessly collaborative—powered by an infrastructure that understands economic behavior before it happens.</p>
                </article>
            </div>

            <div class="mt-6 bg-[#0b0b1a] text-white grain rounded-2xl p-8 sm:p-12 grid lg:grid-cols-12 gap-6 items-center" data-reveal>
                <div class="lg:col-span-4">
                    <p class="eyebrow text-indigo-300 flex items-center gap-2"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Privacy</p>
                    <h2 class="mt-3 text-2xl sm:text-3xl font-black tracking-tight">Zero-Knowledge Architecture</h2>
                </div>
                <p class="lg:col-span-8 text-lg text-slate-300 leading-relaxed">We believe financial privacy is a fundamental human right. FinanceAI employs 256-bit AES encryption. Our engineers cannot see your capital inflows, and our AI models process heuristics locally within isolated secure nodes.</p>
            </div>
        </div>
    </section>

    {{-- Principles --}}
    <section class="py-20 sm:py-28 bg-white border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-site.section-head label="How we build" title="Engineering First" accent="Principles"
                lede="We don't build software. We engineer robust financial systems designed to outlast legacy infrastructure." />

            <div class="mt-14 grid sm:grid-cols-2 lg:grid-cols-4 border-t border-l border-slate-200">
                @foreach($principles as $i => [$icon, $title, $desc])
                    <article class="p-7 border-b border-r border-slate-200" data-reveal style="--d: {{ $i * 70 }}ms">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-sm text-slate-400">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <i class="fa-solid {{ $icon }} text-slate-300" aria-hidden="true"></i>
                        </div>
                        <h3 class="mt-10 text-lg font-black tracking-tight text-slate-900">{{ $title }}</h3>
                        <p class="mt-2 text-[15px] text-slate-600 leading-relaxed">{{ $desc }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Regions --}}
    <section class="py-20 sm:py-28 bg-[#0b0b1a] text-white grain" x-data="{ activeRegion: 'us-east' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-site.section-head dark label="Infrastructure" title="Global Node" accent="Topology"
                lede="FinanceAI operates on a multi-region deployment to guarantee localized latency and strict sovereign data compliance." />

            <div class="mt-14 grid lg:grid-cols-12 gap-10" data-reveal>
                <div class="lg:col-span-5 border-t border-white/15" role="tablist" aria-label="Regions">
                    @foreach($regions as $key => [$label, $code])
                        <button type="button" role="tab" @click="activeRegion = '{{ $key }}'" :aria-selected="activeRegion === '{{ $key }}'"
                                class="w-full flex items-center justify-between gap-4 py-5 border-b border-white/15 text-left transition-colors focus:outline-none focus-visible:bg-white/5"
                                :class="activeRegion === '{{ $key }}' ? 'text-white' : 'text-slate-500 hover:text-slate-300'">
                            <span class="flex items-center gap-4">
                                <span class="font-mono text-xs w-6">{{ $code }}</span>
                                <span class="text-lg font-black tracking-tight">{{ $label }}</span>
                            </span>
                            <span class="h-1.5 w-1.5 rounded-full" :class="activeRegion === '{{ $key }}' ? 'bg-emerald-400' : 'bg-slate-700'" aria-hidden="true"></span>
                        </button>
                    @endforeach
                </div>

                <div class="lg:col-span-7 grid sm:grid-cols-2 gap-8">
                    <div>
                        <p class="font-mono text-[11px] uppercase tracking-wider text-slate-400">Current Region</p>
                        <p class="mt-2 text-3xl font-black tracking-tight" x-text="activeRegion === 'us-east' ? 'US-East-1' : (activeRegion === 'eu-central' ? 'EU-Central-1' : 'AP-South-1')"></p>
                        <dl class="mt-6 border-t border-white/10 text-sm">
                            <div class="flex justify-between py-3 border-b border-white/10"><dt class="text-slate-400">Database Status</dt><dd class="font-mono text-xs uppercase tracking-wider text-emerald-400">Nominal</dd></div>
                            <div class="flex justify-between py-3 border-b border-white/10"><dt class="text-slate-400">AI Inference Engine</dt><dd class="font-mono text-xs uppercase tracking-wider text-emerald-400">Nominal</dd></div>
                            <div class="flex justify-between py-3 border-b border-white/10"><dt class="text-slate-400">Data Sovereignty</dt><dd class="font-mono text-xs uppercase tracking-wider text-indigo-300" x-text="activeRegion === 'eu-central' ? 'GDPR Compliant' : 'Local Sandbox'"></dd></div>
                        </dl>
                    </div>
                    <div class="border border-white/10 rounded-2xl p-8 flex flex-col justify-between">
                        <p class="font-mono text-[11px] uppercase tracking-wider text-slate-400">Region Latency</p>
                        <p class="mt-6 font-black tracking-tight leading-none"><span class="text-7xl tabular-nums" x-text="activeRegion === 'us-east' ? '12' : (activeRegion === 'eu-central' ? '18' : '24')"></span><span class="ml-2 font-serif-i text-3xl text-indigo-300">ms</span></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Team --}}
    <section class="py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-site.section-head label="Team" title="The Architect" accent="Protocol"
                lede="Engineered by specialists in cryptography, corporate finance, and machine learning." />

            <div class="mt-14 grid md:grid-cols-3 gap-6">
                @foreach($team as $i => [$initial, $role, $area, $desc, $clearance, $social])
                    <article class="card-site is-hover p-7 flex flex-col" data-reveal style="--d: {{ $i * 80 }}ms">
                        <div class="flex items-start justify-between">
                            <span class="h-16 w-16 rounded-2xl bg-[#0b0b1a] text-white flex items-center justify-center font-serif-i text-4xl">{{ $initial }}</span>
                            <span class="font-mono text-[10px] uppercase tracking-wider text-slate-400">{{ $clearance }}</span>
                        </div>
                        <h3 class="mt-8 text-2xl font-black tracking-tight text-slate-900">{{ $role }}</h3>
                        <p class="mt-1 eyebrow text-indigo-600">{{ $area }}</p>
                        <p class="mt-4 text-[15px] text-slate-600 leading-relaxed flex-1">{{ $desc }}</p>
                        @if($social)
                            <a href="#" class="mt-6 inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:text-slate-900 hover:border-slate-900 transition-colors" aria-label="Social profile"><i class="fa-brands {{ $social }}" aria-hidden="true"></i></a>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Roadmap --}}
    <section class="py-20 sm:py-28 bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-site.section-head label="Roadmap" title="Deployment" accent="Vector"
                lede="The sequence of deployment for the FinanceAI Master Engine." />

            <ol class="mt-14 grid md:grid-cols-3 border-t border-slate-200">
                @foreach([
                    ['Phase 01', 'Alpha Genesis', 'Deployment of core cryptographic ledgers and user identity access management (IAM).', 'Completed', 'done'],
                    ['Phase 02', 'Neural Integration', 'Activation of AI heuristic engines for predictive burn-rate analysis and autonomous categorization.', 'Current Vector', 'now'],
                    ['Phase 03', 'Institutional Sync', 'API connections to global banking infrastructure for automated ledger synchronization.', 'Pending Authorization', 'next'],
                ] as $i => [$phase, $title, $desc, $status, $state])
                    <li class="relative pt-8 pb-4 md:pr-8 {{ $state === 'next' ? 'opacity-60' : '' }}" data-reveal style="--d: {{ $i * 90 }}ms">
                        <span class="absolute top-0 left-0 h-0.5 {{ $state === 'done' ? 'w-full bg-slate-900' : ($state === 'now' ? 'w-1/2 bg-indigo-600' : 'w-0') }}" aria-hidden="true"></span>
                        <p class="font-mono text-xs uppercase tracking-wider text-slate-400">{{ $phase }}</p>
                        <h3 class="mt-3 text-2xl font-black tracking-tight text-slate-900">{{ $title }}</h3>
                        <p class="mt-3 text-[15px] text-slate-600 leading-relaxed">{{ $desc }}</p>
                        <p class="mt-5 font-mono text-[11px] uppercase tracking-wider {{ $state === 'done' ? 'text-slate-900' : ($state === 'now' ? 'text-indigo-600' : 'text-slate-400') }}">{{ $status }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-20 sm:py-28 bg-[#0b0b1a] text-white grain">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-10 items-end">
            <div class="lg:col-span-8" data-reveal>
                <h2 class="text-4xl sm:text-6xl font-black tracking-[-0.035em] leading-[1.02]">Ready to initialize your <span class="font-serif-i text-indigo-300">financial node?</span></h2>
                <p class="mt-6 text-lg text-slate-300 max-w-2xl">Join thousands of users who have abandoned spreadsheets for absolute cryptographic clarity.</p>
            </div>
            <div class="lg:col-span-4 lg:text-right" data-reveal style="--d: 120ms">
                <a href="{{ route('register') }}" class="group inline-flex items-center gap-2 h-12 px-6 rounded-xl text-white font-bold bg-gradient-to-br from-indigo-500 to-violet-600 shadow-[0_10px_30px_-8px_rgba(99,102,241,.7)] hover:-translate-y-0.5 transition-transform focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-400/40">
                    Deploy Infrastructure <i class="fa-solid fa-arrow-right text-sm transition-transform group-hover:translate-x-0.5" aria-hidden="true"></i>
                </a>
                <p class="mt-4 font-mono text-[11px] uppercase tracking-wider text-slate-500"><i class="fa-solid fa-shield-halved mr-1" aria-hidden="true"></i>256-Bit Encrypted Connection</p>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('aboutEngine', () => ({
        capitalSecured: 1425890500,
        liveNodes: 12458,
        aiOps: 450,
        init() {
            setInterval(() => { this.capitalSecured += Math.floor(Math.random() * 5000) + 1000; }, 3000);
            setInterval(() => { if (Math.random() > 0.5) this.liveNodes += 1; }, 8000);
            setInterval(() => { this.aiOps = Math.floor(400 + Math.random() * 150); }, 1500);
        }
    }));
});
</script>
@endpush
