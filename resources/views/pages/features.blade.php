@extends('layouts.landing')
@include('partials.site-type')

@section('title', 'Platform Features | FinanceAI Enterprise')
@section('meta_description', 'Explore the cryptographic ledgers, AI forecasting engines, and global telemetry tools powering FinanceAI.')

@section('content')
@php
    $tabs = [
        'automation' => ['Smart Automation', 'Set up recurring cryptographic rules to automatically route capital, categorize expenses, and trigger alerts based on custom thresholds.'],
        'security' => ['Identity & Access (IAM)', 'Assign precise Roles and Permissions to hub members. Ensure accountants only see telemetry, while admins retain full destructive rights.'],
        'export' => ['Universal Export', 'Generate cryptographically verified PDF audits and CSV data dumps with a single click for tax compliance and legal review.'],
    ];
@endphp
<div class="paper" x-data="featuresEngine()">

    <x-site.hero eyebrow="Platform Capabilities V3.0" lead="Engineered for absolute" accent="financial clarity."
        lede="We stripped away the friction of traditional accounting. FinanceAI introduces autonomous ledgers, military-grade encryption, and real-time AI heuristics to scale your wealth globally.">
        <x-slot name="aside">
            <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-6" aria-hidden="true">
                <div class="flex items-center justify-between">
                    <span class="font-mono text-[11px] uppercase tracking-wider text-slate-400">Ledger overview</span>
                    <span class="font-mono text-[10px] uppercase tracking-wider text-emerald-400">Encrypted</span>
                </div>
                <div class="mt-6 flex items-end gap-3 h-40 border-b border-white/10">
                    @foreach([40, 70, 90, 60, 75, 52] as $i => $h)
                        <div class="flex-1 rounded-t {{ $i === 2 ? 'bg-indigo-400' : 'bg-white/15' }}" style="height: {{ $h }}%"></div>
                    @endforeach
                </div>
                <div class="mt-5 flex items-end justify-between">
                    <div>
                        <p class="font-mono text-[11px] uppercase tracking-wider text-slate-400">Growth</p>
                        <p class="mt-1 text-3xl font-black text-white">↑ 18.4%</p>
                    </div>
                    <i class="fa-solid fa-shield-halved text-2xl text-slate-600"></i>
                </div>
            </div>
        </x-slot>
    </x-site.hero>

    {{-- Paradigm shift --}}
    <section class="py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-site.section-head label="01 — Before & after" title="A Complete Paradigm" accent="Shift"
                lede="The legacy financial stack is broken. We rebuilt it from the primitive data structures upward." />

            <div class="mt-14 grid md:grid-cols-2 border-t border-slate-900">
                <div class="py-10 md:pr-12 md:border-r border-slate-200" data-reveal>
                    <p class="eyebrow text-slate-400">Traditional Software</p>
                    <ul class="mt-6 space-y-7">
                        @foreach([
                            ['Manual Categorization', 'Hours spent dragging rows in spreadsheets to calculate basic tax categories.'],
                            ['Reactive Analytics', 'Finding out you exceeded your monthly burn rate 15 days after the month ends.'],
                            ['Fragmented Security', 'Sharing unencrypted PDF ledgers via email, risking severe data breaches.'],
                        ] as [$t, $d])
                            <li class="flex gap-4">
                                <i class="fa-solid fa-xmark mt-1.5 text-sm text-slate-300" aria-hidden="true"></i>
                                <div>
                                    <h3 class="font-bold text-slate-500 line-through decoration-slate-300">{{ $t }}</h3>
                                    <p class="mt-1 text-[15px] text-slate-500 leading-relaxed">{{ $d }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="py-10 md:pl-12 border-t md:border-t-0 border-slate-200" data-reveal style="--d: 100ms">
                    <p class="eyebrow text-indigo-600">FinanceAI Architecture</p>
                    <ul class="mt-6 space-y-7">
                        @foreach([
                            ['Autonomous Tagging', 'Neural networks instantly categorize capital flows the millisecond they hit the ledger.'],
                            ['Predictive Telemetry', 'Live AI projections warn you of future cash-flow deficits before they materialize.'],
                            ['Cryptographic Vaults', 'End-to-end 256-bit encryption with strictly authenticated IAM Role access.'],
                        ] as [$t, $d])
                            <li class="flex gap-4">
                                <i class="fa-solid fa-check mt-1.5 text-sm text-emerald-600" aria-hidden="true"></i>
                                <div>
                                    <h3 class="font-black text-slate-900">{{ $t }}</h3>
                                    <p class="mt-1 text-[15px] text-slate-600 leading-relaxed">{{ $d }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Core capabilities --}}
    <section class="py-20 sm:py-28 bg-white border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-site.section-head label="02 — Capabilities" title="The Architecture of" accent="Wealth"
                lede="Everything you need to orchestrate capital, built into a single, cohesive, high-performance engine." />

            <div class="mt-14 grid md:grid-cols-3 gap-5">
                <article class="md:col-span-2 card-site is-hover p-8 sm:p-10 grid sm:grid-cols-2 gap-8 items-center" data-reveal>
                    <div>
                        <i class="fa-solid fa-link text-slate-400" aria-hidden="true"></i>
                        <h3 class="mt-6 text-2xl sm:text-3xl font-black tracking-tight text-slate-900">Immutable Ledgers</h3>
                        <p class="mt-3 text-slate-600 leading-relaxed">Every transaction is hashed and locked. Say goodbye to spreadsheet errors, accidental deletions, and unsynced data. Absolute mathematical truth.</p>
                    </div>
                    <ul class="border-t border-slate-200 font-mono text-xs text-slate-500" aria-hidden="true">
                        @foreach([['0x7f3a…e91c', 'emerald-500'], ['0x2b8d…14f0', 'rose-500'], ['0xc41e…9a7b', 'indigo-500']] as [$hash, $dot])
                            <li class="flex items-center justify-between py-3 border-b border-slate-200"><span class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-{{ $dot }}"></span>{{ $hash }}</span><i class="fa-solid fa-lock text-slate-300"></i></li>
                        @endforeach
                    </ul>
                </article>

                <article class="bg-[#0b0b1a] text-white grain rounded-2xl p-8 sm:p-10 flex flex-col justify-between min-h-[260px]" data-reveal style="--d: 80ms">
                    <i class="fa-solid fa-satellite-dish text-slate-500" aria-hidden="true"></i>
                    <div>
                        <h3 class="text-2xl font-black tracking-tight">Live Telemetry</h3>
                        <p class="mt-3 text-slate-400 leading-relaxed">Monitor capital velocity in real-time with sub-millisecond database indexing.</p>
                    </div>
                </article>

                <article class="card-site is-hover p-8 sm:p-10 flex flex-col justify-between min-h-[260px]" data-reveal>
                    <i class="fa-solid fa-network-wired text-slate-400" aria-hidden="true"></i>
                    <div>
                        <h3 class="text-2xl font-black tracking-tight text-slate-900">Shared Hubs</h3>
                        <p class="mt-3 text-slate-600 leading-relaxed">Deploy isolated environments for family members, accountants, or corporate divisions.</p>
                    </div>
                </article>

                <article class="md:col-span-2 card-site is-hover p-8 sm:p-10 grid sm:grid-cols-2 gap-8 items-center" data-reveal style="--d: 80ms">
                    <div>
                        <i class="fa-solid fa-brain text-slate-400" aria-hidden="true"></i>
                        <h3 class="mt-6 text-2xl sm:text-3xl font-black tracking-tight text-slate-900">Neural Forecasting</h3>
                        <p class="mt-3 text-slate-600 leading-relaxed">Our machine learning models ingest your burn rate and instantly project cash-flow constraints up to 36 months into the future.</p>
                    </div>
                    <div class="border-l-2 border-rose-500 pl-5" aria-hidden="true">
                        <p class="font-mono text-[11px] uppercase tracking-wider text-rose-600">Anomaly Detected</p>
                        <p class="mt-2 text-slate-600">Projected runway dip in Q4</p>
                        <svg viewBox="0 0 200 50" class="mt-4 w-full h-12"><polyline fill="none" stroke="#0f172a" stroke-width="1.5" points="0,18 30,15 60,20 90,14 120,22 140,38 160,30 200,26" /><circle cx="140" cy="38" r="3" fill="#f43f5e" /></svg>
                    </div>
                </article>
            </div>
        </div>
    </section>

    {{-- ROI --}}
    <section class="py-20 sm:py-28 bg-[#0b0b1a] text-white grain">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-5">
                <x-site.section-head dark label="03 — Calculator" title="Calculate Your" accent="ROI"
                    lede="See exactly how much human capital you recapture by deploying the FinanceAI engine instead of manual ledgers." />
            </div>
            <div class="lg:col-span-7" data-reveal style="--d: 100ms">
                <div class="flex items-end justify-between">
                    <label for="roi-volume" class="font-mono text-[11px] uppercase tracking-wider text-slate-400">Monthly Transaction Volume</label>
                    <span class="text-3xl font-black tabular-nums" x-text="volume + ' TX'"></span>
                </div>
                <input id="roi-volume" type="range" x-model="volume" min="50" max="5000" step="50" class="roi-range mt-5 w-full" :style="`--p: ${(volume - 50) / 49.5}%`">
                <dl class="mt-10 grid sm:grid-cols-2 border-t border-white/15">
                    <div class="py-6 sm:pr-6 border-b sm:border-b-0 sm:border-r border-white/15">
                        <dt class="font-mono text-[11px] uppercase tracking-wider text-slate-400">Hours Saved Monthly</dt>
                        <dd class="mt-2 text-5xl font-black tabular-nums text-emerald-400" x-text="Math.round(volume * 0.15) + 'h'"></dd>
                        <dd class="mt-2 text-sm text-slate-400">Reclaimed from manual data entry.</dd>
                    </div>
                    <div class="py-6 sm:pl-6">
                        <dt class="font-mono text-[11px] uppercase tracking-wider text-slate-400">Capital Retained (Avg)</dt>
                        <dd class="mt-2 text-5xl font-black tabular-nums text-indigo-300" x-text="'$' + (volume * 4.5).toLocaleString()"></dd>
                        <dd class="mt-2 text-sm text-slate-400">Calculated at $30/hr administrative rate.</dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    {{-- Control tabs --}}
    <section class="py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl" data-reveal>
                <p class="eyebrow text-indigo-600">04 — Control</p>
                <h2 class="mt-4 text-3xl sm:text-5xl font-black tracking-[-0.03em] leading-[1.05] text-slate-900">Granular Control. <span class="font-serif-i text-indigo-600">Infinite Possibility.</span></h2>
            </div>

            <div class="mt-14 grid lg:grid-cols-12 gap-10 items-start">
                <div class="lg:col-span-5 border-t border-slate-200" role="tablist" aria-label="Capabilities" data-reveal>
                    @foreach($tabs as $key => [$title, $desc])
                        <button type="button" role="tab" @click="activeTab = '{{ $key }}'" :aria-selected="activeTab === '{{ $key }}'"
                                class="relative w-full text-left py-6 pl-6 border-b border-slate-200 focus:outline-none focus-visible:bg-slate-50">
                            <span class="absolute left-0 top-6 bottom-6 w-0.5 transition-colors" :class="activeTab === '{{ $key }}' ? 'bg-indigo-600' : 'bg-transparent'" aria-hidden="true"></span>
                            <span class="block text-xl font-black tracking-tight transition-colors" :class="activeTab === '{{ $key }}' ? 'text-slate-900' : 'text-slate-400'">{{ $title }}</span>
                            <span class="mt-2 block text-[15px] leading-relaxed transition-colors" :class="activeTab === '{{ $key }}' ? 'text-slate-600' : 'text-slate-400'">{{ $desc }}</span>
                        </button>
                    @endforeach
                </div>

                <div class="lg:col-span-7" data-reveal style="--d: 120ms">
                    <div class="h-[440px] bg-[#0b0b1a] rounded-2xl border border-slate-800 overflow-hidden flex flex-col">
                        <div class="h-12 border-b border-white/10 flex items-center px-5 gap-3 shrink-0">
                            <span class="flex gap-1.5" aria-hidden="true"><span class="h-2.5 w-2.5 rounded-full bg-white/15"></span><span class="h-2.5 w-2.5 rounded-full bg-white/15"></span><span class="h-2.5 w-2.5 rounded-full bg-white/15"></span></span>
                            <span class="font-mono text-[11px] text-slate-500"><i class="fa-solid fa-lock mr-1 text-[9px]" aria-hidden="true"></i>app.financeai.com/console</span>
                        </div>
                        <div class="flex-1 relative p-7">
                            <div x-show="activeTab === 'automation'" x-transition.opacity.duration.300ms class="absolute inset-7 space-y-3">
                                <div class="rounded-xl border border-white/10 p-4 flex items-center justify-between gap-3 text-sm text-white">
                                    <span><i class="fa-solid fa-bolt text-amber-400 mr-2" aria-hidden="true"></i>If Inflow &gt; ₹50,000</span>
                                    <i class="fa-solid fa-arrow-right text-slate-600" aria-hidden="true"></i>
                                    <span>Tag as 'Corporate Salary'</span>
                                </div>
                                <div class="rounded-xl border border-white/10 p-4 flex items-center justify-between gap-3 text-sm text-white/50">
                                    <span><i class="fa-solid fa-bolt text-amber-400/60 mr-2" aria-hidden="true"></i>If Outflow hits Netflix</span>
                                    <i class="fa-solid fa-arrow-right text-slate-700" aria-hidden="true"></i>
                                    <span>Route to 'Entertainment'</span>
                                </div>
                                <div class="rounded-xl border border-dashed border-white/15 py-4 text-center font-mono text-[11px] uppercase tracking-wider text-slate-500">+ Add Logic Trigger</div>
                            </div>
                            <div x-show="activeTab === 'security'" x-cloak x-transition.opacity.duration.300ms class="absolute inset-7">
                                @foreach([['Node Administrator', 'Full Read/Write Access', true], ['Financial Auditor', 'Read-Only Telemetry', false], ['Data Entry Clerk', 'Write-Only Ledgers', false]] as [$r, $a, $on])
                                    <div class="flex items-center justify-between py-4 border-b border-white/10">
                                        <div><p class="font-bold text-white">{{ $r }}</p><p class="font-mono text-xs text-slate-500">{{ $a }}</p></div>
                                        <span class="h-6 w-10 rounded-full relative {{ $on ? 'bg-emerald-500' : 'bg-white/10' }}" aria-hidden="true"><span class="absolute top-1 h-4 w-4 rounded-full {{ $on ? 'right-1 bg-white' : 'left-1 bg-slate-500' }}"></span></span>
                                    </div>
                                @endforeach
                            </div>
                            <div x-show="activeTab === 'export'" x-cloak x-transition.opacity.duration.300ms class="absolute inset-7 flex flex-col justify-center">
                                <i class="fa-solid fa-file-pdf text-3xl text-slate-500" aria-hidden="true"></i>
                                <p class="mt-6 text-xl font-black text-white">Tax Audit Generation</p>
                                <p class="mt-2 text-sm text-slate-400 max-w-sm">Compiling FY2025-2026 cryptographic ledger into a legally compliant PDF structure.</p>
                                <div class="mt-6 h-1 w-56 bg-white/10 rounded-full overflow-hidden"><div class="h-full w-2/3 bg-indigo-400"></div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- API --}}
    <section class="py-20 sm:py-28 bg-[#0b0b1a] text-white grain" id="terminal-container">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-14 items-center">
            <div data-reveal>
                <p class="eyebrow text-indigo-300">Developer API</p>
                <h2 class="mt-4 text-3xl sm:text-5xl font-black tracking-[-0.03em] leading-[1.05]">Built for Scale. <span class="font-serif-i text-indigo-300">Designed for Engineers.</span></h2>
                <p class="mt-6 text-lg text-slate-300 leading-relaxed">Every feature in the FinanceAI UI is powered by our enterprise REST API. Build custom internal tools, sync with legacy banking mainframes, or write your own automation scripts with secure Bearer tokens.</p>
                <a href="#" class="mt-8 inline-flex items-center gap-2 h-11 px-5 rounded-xl bg-white text-slate-900 font-bold hover:bg-indigo-100 transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-white/30">
                    Read the Docs <i class="fa-solid fa-arrow-up-right-from-square text-xs" aria-hidden="true"></i>
                </a>
            </div>

            <div class="rounded-2xl border border-white/10 bg-black/40 p-6 font-mono text-[13px] leading-relaxed" data-reveal style="--d: 150ms">
                <div class="text-emerald-400 mb-2">admin@finance-ai:~$ <span x-show="!terminalTriggered" class="animate-pulse">_</span></div>
                <div id="terminalCommand" class="text-white inline"></div><span x-show="terminalTriggered && !showTerminalResponse" class="text-white animate-pulse">_</span>
                <div x-show="showTerminalResponse" x-transition.opacity x-cloak class="mt-4 pt-4 border-t border-white/10">
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

@push('styles')
<style>
    .roi-range { -webkit-appearance: none; appearance: none; height: 4px; border-radius: 9999px; outline: none;
        background: linear-gradient(to right, #a5b4fc var(--p, 10%), rgba(255,255,255,.15) var(--p, 10%)); }
    .roi-range::-webkit-slider-thumb { -webkit-appearance: none; width: 22px; height: 22px; border-radius: 9999px; background: #fff; border: 2px solid #a5b4fc; cursor: pointer; }
    .roi-range::-moz-range-thumb { width: 20px; height: 20px; border-radius: 9999px; background: #fff; border: 2px solid #a5b4fc; cursor: pointer; }
    .roi-range:focus-visible::-webkit-slider-thumb { box-shadow: 0 0 0 6px rgba(165,180,252,.3); }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('featuresEngine', () => ({
        activeTab: 'automation',
        volume: 500,
        terminalTriggered: false,
        showTerminalResponse: false,

        init() {
            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting && !this.terminalTriggered) {
                    this.terminalTriggered = true;
                    this.typeTerminal();
                }
            }, { threshold: 0.5 });
            const el = document.getElementById('terminal-container');
            if (el) observer.observe(el);
        },

        typeTerminal() {
            const text = "curl -X POST https://api.financeai.com/v1/inflow \\\n  -H \"Authorization: Bearer sk_live_...\" \\\n  -d '{\"amount\": 50000, \"category\": \"revenue\"}'";
            let i = 0;
            const el = document.getElementById('terminalCommand');
            const typeWriter = () => {
                if (i < text.length) {
                    const ch = text.charAt(i);
                    el.innerHTML += ch === '\n' ? '<br>' : (ch === ' ' ? '&nbsp;' : ch.replace('<', '&lt;'));
                    i++;
                    setTimeout(typeWriter, Math.random() * 25 + 10);
                } else {
                    setTimeout(() => { this.showTerminalResponse = true; }, 600);
                }
            };
            setTimeout(typeWriter, 400);
        }
    }));
});
</script>
@endpush
