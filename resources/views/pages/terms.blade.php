@extends('layouts.landing')
@include('partials.site-type')

@section('title', 'Terms of Service | FinanceAI Enterprise')
@section('meta_description', 'Review the Terms and Conditions governing the use of the FinanceAI platform, API, and cryptographic ledgers.')

@section('content')
@php
    $toc = [
        'acceptance' => '1. Acceptance of Terms',
        'account' => '2. Node Responsibilities',
        'usage' => '3. Acceptable Use',
        'payments' => '4. Billing & Quotas',
        'ownership' => '5. Data Sovereignty',
        'ai_disclaimer' => '6. AI Liability Disclaimer',
        'liability' => '7. Limitation of Liability',
        'termination' => '8. Protocol Termination',
        'law' => '9. Governing Law',
        'disputes' => '10. Dispute Resolution',
    ];
    $h2 = 'mt-2 text-2xl sm:text-3xl font-black tracking-tight text-slate-900';
@endphp
<div class="paper" x-data="termsEngine()">

    <x-site.hero eyebrow="Legal Agreement" lead="Terms of" accent="Service"
        lede="This document establishes the binding cryptographic and legal parameters governing your utilization of the FinanceAI architecture. By initializing a node, you agree to these protocols.">
        <x-slot name="actions">
            <button type="button" @click="exportTermsPDF()" :disabled="isExporting" class="inline-flex items-center justify-center gap-2 h-12 px-6 rounded-xl font-bold text-white border border-white/25 bg-white/5 hover:bg-white/10 transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-white/20">
                <span x-show="!isExporting"><i class="fa-solid fa-file-pdf text-sm mr-1" aria-hidden="true"></i> Download Legal PDF</span>
                <span x-show="isExporting" x-cloak><i class="fa-solid fa-circle-notch fa-spin mr-1" aria-hidden="true"></i> Generating Hash...</span>
            </button>
        </x-slot>
        <x-slot name="meta">
            <dl class="flex flex-wrap gap-x-10 gap-y-3 font-mono text-xs uppercase tracking-wider">
                <div class="flex gap-2"><dt class="text-slate-400">Effective Date:</dt><dd class="text-slate-200">{{ date('F d, Y') }}</dd></div>
                <div class="flex gap-2"><dt class="text-slate-400">Document Version:</dt><dd class="text-slate-200">2.4.1</dd></div>
            </dl>
        </x-slot>
    </x-site.hero>

    {{-- The short version --}}
    <section class="border-b border-slate-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-200">
            @foreach([
                ['fa-database', 'Absolute Ownership', 'You retain 100% ownership of your financial data. We hold a limited license solely to run AI heuristics and generate your reports.'],
                ['fa-door-open', 'No Vendor Lock-In', 'You may terminate your subscription and permanently purge your cryptographic node at any time before your next billing cycle.'],
                ['fa-hand-holding-dollar', 'Not Financial Advice', 'FinanceAI provides telemetry and forecasting. Our AI does not provide certified legal, tax, or professional investment advice.'],
            ] as $i => [$icon, $title, $desc])
                <div class="py-10 md:px-8 first:md:pl-0 last:md:pr-0" data-reveal style="--d: {{ $i * 80 }}ms">
                    <p class="eyebrow text-slate-400 flex items-center gap-2"><i class="fa-solid {{ $icon }} text-indigo-500" aria-hidden="true"></i> {{ $title }}</p>
                    <p class="mt-4 text-slate-700 leading-relaxed">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Document --}}
    <section class="py-16 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-12">
            <aside class="lg:col-span-3">
                <nav class="lg:sticky lg:top-28 -mx-4 px-4 lg:mx-0 lg:px-0 overflow-x-auto no-scrollbar" aria-label="Document Index">
                    <p class="eyebrow text-slate-400 hidden lg:block">Document Index</p>
                    <ol class="flex lg:flex-col gap-2 lg:gap-0 lg:mt-5 lg:border-l border-slate-200 w-max lg:w-auto">
                        @foreach($toc as $id => $label)
                            <li>
                                <a href="#{{ $id }}" @click.prevent="scrollToSection('{{ $id }}')"
                                   class="block whitespace-nowrap lg:whitespace-normal lg:-ml-px px-3 py-1.5 lg:py-2 lg:pl-4 rounded-full lg:rounded-none border lg:border-0 lg:border-l text-sm transition-colors"
                                   :class="activeSection === '{{ $id }}' ? 'border-slate-900 text-slate-900 font-semibold' : 'border-slate-200 lg:border-transparent text-slate-500 hover:text-slate-900'">{{ $label }}</a>
                            </li>
                        @endforeach
                    </ol>
                    <div class="hidden lg:block mt-8 h-px bg-slate-200 overflow-hidden" aria-hidden="true"><div class="h-full bg-indigo-600 transition-[width] duration-150" :style="`width: ${readingProgress}%`"></div></div>
                </nav>
            </aside>

            <article class="lg:col-span-9 max-w-3xl prose-site space-y-16" id="legal-content">
                <section id="acceptance" class="policy-section scroll-mt-28">
                    <p class="font-mono text-sm text-indigo-600">01</p>
                    <h2 class="{{ $h2 }}">Acceptance of Terms</h2>
                    <p class="mt-5">By deploying a FinanceAI node, accessing the REST API, or navigating the platform interface, you unequivocally agree to be bound by these Terms of Service. You represent and warrant that you are of legal age to form a binding contract (at least 18 years old in most jurisdictions) and possess the authority to bind your organization to these protocols.</p>
                </section>

                <section id="account" class="policy-section scroll-mt-28">
                    <p class="font-mono text-sm text-indigo-600">02</p>
                    <h2 class="{{ $h2 }}">Node &amp; Account Responsibilities</h2>
                    <p class="mt-5">You are the sole architect of your account's security. FinanceAI provides the encrypted infrastructure, but you are responsible for maintaining the confidentiality of your access keys.</p>
                    <dl class="mt-6 border-t border-slate-200">
                        <div class="grid sm:grid-cols-3 gap-2 sm:gap-6 py-5 border-b border-slate-200">
                            <dt class="font-black text-slate-900 text-base">Credential Integrity</dt>
                            <dd class="sm:col-span-2 text-[15px] leading-relaxed text-slate-600">You must maintain strict confidentiality of your cryptographic passwords and 2FA tokens. Any activity occurring under your node is legally attributed to you.</dd>
                        </div>
                        <div class="grid sm:grid-cols-3 gap-2 sm:gap-6 py-5 border-b border-slate-200">
                            <dt class="font-black text-slate-900 text-base">Breach Reporting</dt>
                            <dd class="sm:col-span-2 text-[15px] leading-relaxed text-slate-600">You must notify our security team immediately upon discovering any unauthorized deployment of your account or breach of your network perimeter.</dd>
                        </div>
                    </dl>
                </section>

                <section id="usage" class="policy-section scroll-mt-28">
                    <p class="font-mono text-sm text-indigo-600">03</p>
                    <h2 class="{{ $h2 }}">Acceptable Use Policy</h2>
                    <p class="mt-5">FinanceAI is engineered for high-performance financial tracking. To maintain 99.99% uptime across the network, we strictly enforce the following prohibitions.</p>
                    <div class="mt-6 border-l-2 border-rose-500 pl-6">
                        <p class="eyebrow text-rose-600">Prohibited Vectors</p>
                        <ul class="mt-4 space-y-3 text-base">
                            <li class="flex gap-3"><i class="fa-solid fa-xmark text-rose-500 mt-1.5 text-sm" aria-hidden="true"></i><span>Deploying automated scripts, scrapers, or bots against our UI (use the designated REST API instead).</span></li>
                            <li class="flex gap-3"><i class="fa-solid fa-xmark text-rose-500 mt-1.5 text-sm" aria-hidden="true"></i><span>Reverse-engineering, decompiling, or attempting to extract the source code of our AI heuristic engines.</span></li>
                            <li class="flex gap-3"><i class="fa-solid fa-xmark text-rose-500 mt-1.5 text-sm" aria-hidden="true"></i><span>Utilizing the platform to launder capital, track illicit transactions, or evade global tax compliance regulations.</span></li>
                        </ul>
                    </div>
                </section>

                <section id="payments" class="policy-section scroll-mt-28">
                    <p class="font-mono text-sm text-indigo-600">04</p>
                    <h2 class="{{ $h2 }}">Billing &amp; Quota Allotment</h2>
                    <p class="mt-5">By upgrading to a Pro Hub or Enterprise Node, you authorize FinanceAI (via our payment processor, Stripe) to charge the applicable subscription fees to your designated payment method.</p>
                    <dl class="mt-6 grid sm:grid-cols-2 border-t border-l border-slate-200 bg-white">
                        <div class="p-6 border-b border-r border-slate-200">
                            <dt class="flex items-center gap-2 font-black text-slate-900 text-base"><i class="fa-solid fa-money-check-dollar text-sm text-slate-400" aria-hidden="true"></i>Advance Billing</dt>
                            <dd class="mt-2 text-[15px] leading-relaxed text-slate-600">Fees are billed in advance on a monthly or annual cycle and are strictly non-refundable.</dd>
                        </div>
                        <div class="p-6 border-b border-r border-slate-200">
                            <dt class="flex items-center gap-2 font-black text-slate-900 text-base"><i class="fa-solid fa-ban text-sm text-slate-400" aria-hidden="true"></i>Cancellation Protocol</dt>
                            <dd class="mt-2 text-[15px] leading-relaxed text-slate-600">You may cancel your node anytime. Access remains active until the end of your current billing epoch.</dd>
                        </div>
                    </dl>
                </section>

                <section id="ownership" class="policy-section scroll-mt-28">
                    <p class="font-mono text-sm text-indigo-600">05</p>
                    <h2 class="{{ $h2 }}">Data Sovereignty &amp; Ownership</h2>
                    <p class="mt-5">You retain absolute, sovereign ownership of all financial data injected into the platform. By utilizing the service, you grant FinanceAI a worldwide, limited, non-exclusive license to host, process, and encrypt this data strictly for the purpose of executing the software's functionality and generating your neural forecasts.</p>
                </section>

                <section id="ai_disclaimer" class="policy-section scroll-mt-28">
                    <p class="font-mono text-sm text-indigo-600">06</p>
                    <h2 class="{{ $h2 }}">AI Liability &amp; Financial Disclaimer</h2>
                    <div class="mt-6 bg-amber-50 border-l-2 border-amber-500 px-6 py-6">
                        <p class="eyebrow text-amber-700 flex items-center gap-2"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> Critical Disclaimer</p>
                        <p class="mt-4">FinanceAI is a software technology company, <strong>not a licensed bank, brokerage, or fiduciary advisory firm.</strong></p>
                        <p>All AI-generated insights, burn-rate projections, and heuristic forecasts are provided for <strong>informational and structural purposes only</strong>. They do not constitute professional financial, tax, or legal advice. You alone assume the sole responsibility of evaluating the merits and risks associated with the use of any data generated by our platform before making any financial decisions.</p>
                    </div>
                </section>

                <section id="liability" class="policy-section scroll-mt-28">
                    <p class="font-mono text-sm text-indigo-600">07</p>
                    <h2 class="{{ $h2 }}">Limitation of Liability</h2>
                    <p class="mt-5">To the maximum extent permitted by global law, FinanceAI and its architects shall not be liable for any indirect, incidental, special, consequential, or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from (i) your access to or inability to access the service; (ii) any unauthorized access to our secure servers.</p>
                </section>

                <section id="termination" class="policy-section scroll-mt-28">
                    <p class="font-mono text-sm text-indigo-600">08</p>
                    <h2 class="{{ $h2 }}">Protocol Termination</h2>
                    <p class="mt-5">We reserve the unilateral right to suspend or terminate your node immediately, without prior notice or liability, for any reason whatsoever, including without limitation if you breach the Acceptable Use limits defined in Section 03. Upon termination, your right to utilize the API and UI will cease immediately.</p>
                </section>

                <section id="law" class="policy-section scroll-mt-28">
                    <p class="font-mono text-sm text-indigo-600">09</p>
                    <h2 class="{{ $h2 }}">Governing Law</h2>
                    <p class="mt-5">These Terms shall be governed and construed in accordance with international commercial law, without regard to its conflict of law provisions. Our failure to enforce any right or provision of these Terms will not be considered a waiver of those rights.</p>
                </section>

                <section id="disputes" class="policy-section scroll-mt-28">
                    <p class="font-mono text-sm text-indigo-600">10</p>
                    <h2 class="{{ $h2 }}">Dispute Resolution</h2>
                    <p class="mt-5">In the event of a dispute, you agree to first attempt to resolve the issue informally by contacting our legal architecture team. If resolution is impossible, disputes shall be resolved through binding arbitration rather than formal litigation.</p>

                    <div class="mt-10 bg-[#0b0b1a] text-white grain rounded-2xl p-7 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div>
                            <p class="text-lg font-black tracking-tight">Digital Acknowledgment</p>
                            <p class="mt-1 text-sm text-slate-400">Please confirm your understanding of the architecture protocols.</p>
                        </div>
                        <button type="button" @click="acknowledgeTerms()"
                                class="shrink-0 inline-flex items-center justify-center gap-2 h-12 px-6 rounded-xl font-bold text-white transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-white/20"
                                :class="hasAcknowledged ? 'bg-emerald-600' : 'bg-gradient-to-br from-indigo-500 to-violet-600 hover:from-indigo-400'">
                            <span x-show="!hasAcknowledged">I Agree &amp; Acknowledge</span>
                            <span x-show="hasAcknowledged" x-cloak><i class="fa-solid fa-check mr-1" aria-hidden="true"></i> Verified</span>
                        </button>
                    </div>
                </section>
            </article>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    @media print {
        header, footer, aside, button, .fixed { display: none !important; }
        [data-reveal] { opacity: 1 !important; transform: none !important; }
    }
</style>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('termsEngine', () => ({
        activeSection: 'acceptance',
        readingProgress: 0,
        isExporting: false,
        hasAcknowledged: false,

        init() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) return;
                    this.activeSection = entry.target.id;
                    const link = document.querySelector(`a[href="#${entry.target.id}"]`);
                    if (link && window.innerWidth < 1024) link.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                });
            }, { rootMargin: '-20% 0px -70% 0px' });
            document.querySelectorAll('.policy-section').forEach(s => observer.observe(s));
            window.addEventListener('scroll', () => this.calculateReadingProgress(), { passive: true });
        },

        calculateReadingProgress() {
            const c = document.getElementById('legal-content');
            if (!c) return;
            const r = c.getBoundingClientRect();
            if (r.top > window.innerHeight) { this.readingProgress = 0; return; }
            const total = r.height - window.innerHeight;
            const scrolled = Math.abs(Math.min(0, r.top));
            this.readingProgress = Math.min(Math.max((scrolled / total) * 100, 0), 100);
        },

        scrollToSection(id) {
            this.activeSection = id;
            const el = document.getElementById(id);
            if (el) el.scrollIntoView({ behavior: 'smooth' });
        },

        exportTermsPDF() {
            this.isExporting = true;
            this.$dispatch('notify', { message: 'Preparing document for PDF export...', type: 'info' });
            setTimeout(() => { this.isExporting = false; window.print(); }, 800);
        },

        acknowledgeTerms() {
            if (this.hasAcknowledged) return;
            this.hasAcknowledged = true;
            this.$dispatch('notify', { message: 'Cryptographic signature recorded. Protocol acknowledged.', type: 'success' });
        }
    }));
});
</script>
@endpush
