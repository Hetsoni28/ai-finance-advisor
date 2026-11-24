@extends('layouts.landing')
@include('partials.site-type')

@section('title', 'Global Privacy Policy | FinanceAI Enterprise')
@section('meta_description', 'FinanceAI is committed to cryptographic data security and absolute financial privacy. Read our global compliance policy.')

@section('content')
@php
    $toc = [
        'collection' => '1. Data Collection',
        'usage' => '2. Usage Protocol',
        'gdpr' => '3. GDPR (EU) Compliance',
        'ccpa' => '4. CCPA (California)',
        'retention' => '5. Data Retention',
        'cookies' => '6. Tracking & Cookies',
        'transfers' => '7. International Transfers',
        'security' => '8. Security Architecture',
        'rights' => '9. Exercising Rights',
        'dpo' => '10. Contact DPO',
    ];
    $h2 = 'mt-2 text-2xl sm:text-3xl font-black tracking-tight text-slate-900';
@endphp
<div class="paper" x-data="complianceEngine()">

    <x-site.hero eyebrow="Legal & Compliance" lead="Global" accent="Privacy" tail="Policy"
        lede="FinanceAI is architected on the principle of absolute cryptographic privacy. We are committed to protecting your personal and financial telemetry in strict compliance with global regulations.">
        <x-slot name="actions">
            <button type="button" @click="exportPolicyPDF()" :disabled="isExporting" class="inline-flex items-center justify-center gap-2 h-12 px-6 rounded-xl font-bold text-white border border-white/25 bg-white/5 hover:bg-white/10 transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-white/20">
                <span x-show="!isExporting"><i class="fa-solid fa-file-pdf text-sm mr-1" aria-hidden="true"></i> Download Legal PDF</span>
                <span x-show="isExporting" x-cloak><i class="fa-solid fa-circle-notch fa-spin mr-1" aria-hidden="true"></i> Generating Hash...</span>
            </button>
        </x-slot>
        <x-slot name="meta">
            <dl class="flex flex-wrap gap-x-10 gap-y-3 font-mono text-xs uppercase tracking-wider">
                <div class="flex gap-2"><dt class="text-slate-400">Last Updated:</dt><dd class="text-slate-200">{{ date('F d, Y') }}</dd></div>
                <div class="flex gap-2"><dt class="text-slate-400">Document Version:</dt><dd class="text-slate-200">3.1.4</dd></div>
            </dl>
        </x-slot>
    </x-site.hero>

    {{-- The short version --}}
    <section class="border-b border-slate-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-200">
            @foreach([
                ['fa-ban', 'Zero Data Selling', 'Your financial telemetry is your own. We do not, and will never, sell your data to third-party advertisers or brokers.'],
                ['fa-lock', 'Military Encryption', 'All ledgers are secured using AES-256 encryption at rest and TLS 1.3 in transit to prevent unauthorized access.'],
                ['fa-globe', 'Global Compliance', 'Strict adherence to GDPR (EU), CCPA (California), and international financial data protection frameworks.'],
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
                <section id="collection" class="policy-section scroll-mt-28">
                    <p class="font-mono text-sm text-indigo-600">01</p>
                    <h2 class="{{ $h2 }}">Information We Collect</h2>
                    <p class="mt-5">To provide the FinanceAI suite of tools, we must collect and process specific telemetric and personal data points. We operate on a principle of data minimization—collecting only what is absolutely necessary.</p>
                    <ul class="mt-6 border-t border-slate-200 text-base">
                        <li class="py-3.5 border-b border-slate-200"><strong>Identity Nodes:</strong> Name, authenticated email addresses, and encrypted login credentials.</li>
                        <li class="py-3.5 border-b border-slate-200"><strong>Cryptographic Ledgers:</strong> Transaction amounts, categorizations, income streams, and linked budget structures.</li>
                        <li class="py-3.5 border-b border-slate-200"><strong>System Telemetry:</strong> Anonymized interaction analytics, device signatures, and IP logs strictly for security monitoring.</li>
                    </ul>
                </section>

                <section id="usage" class="policy-section scroll-mt-28">
                    <p class="font-mono text-sm text-indigo-600">02</p>
                    <h2 class="{{ $h2 }}">Information Usage Protocol</h2>
                    <p class="mt-5">The data we ingest is utilized exclusively to operate, secure, and improve the FinanceAI architecture. Your financial variables are fed into our heuristic engines to generate insights, but are never exposed to human operators.</p>
                    <dl class="mt-6 grid sm:grid-cols-2 border-t border-l border-slate-200 bg-white">
                        <div class="p-6 border-b border-r border-slate-200">
                            <dt class="flex items-center gap-2 font-black text-slate-900 text-base"><i class="fa-solid fa-microchip text-sm text-slate-400" aria-hidden="true"></i>Algorithm Training</dt>
                            <dd class="mt-2 text-[15px] leading-relaxed text-slate-600">Anonymized data trains predictive burn-rate models.</dd>
                        </div>
                        <div class="p-6 border-b border-r border-slate-200">
                            <dt class="flex items-center gap-2 font-black text-slate-900 text-base"><i class="fa-solid fa-shield-halved text-sm text-slate-400" aria-hidden="true"></i>Fraud Prevention</dt>
                            <dd class="mt-2 text-[15px] leading-relaxed text-slate-600">Identifying anomalies and defending against unauthorized access.</dd>
                        </div>
                    </dl>
                </section>

                <section id="gdpr" class="policy-section scroll-mt-28">
                    <p class="font-mono text-sm text-indigo-600">03</p>
                    <h2 class="{{ $h2 }}">GDPR (EU) Compliance</h2>
                    <p class="mt-5">For users residing within the European Economic Area (EEA), FinanceAI operates strictly as a Data Controller under the General Data Protection Regulation (GDPR). You retain absolute sovereignty over your nodes.</p>
                    <div class="mt-6 border-l-2 border-indigo-600 pl-6">
                        <p class="eyebrow text-indigo-600">Your Retained Rights</p>
                        <ul class="mt-4 grid sm:grid-cols-2 gap-x-6 gap-y-2 text-base font-semibold text-slate-800">
                            <li>Right to Access</li>
                            <li>Right to Rectification</li>
                            <li>Right to Erasure ("Forgotten")</li>
                            <li>Right to Portability</li>
                        </ul>
                    </div>
                </section>

                <section id="ccpa" class="policy-section scroll-mt-28">
                    <p class="font-mono text-sm text-indigo-600">04</p>
                    <h2 class="{{ $h2 }}">CCPA (California Residents)</h2>
                    <p class="mt-5">Pursuant to the California Consumer Privacy Act (CCPA), California residents have explicit rights regarding their personal data. FinanceAI unequivocally states that <strong>we do not sell personal or financial data</strong>. You may request disclosure of any telemetry we have collected or demand immediate ledger deletion without facing any service discrimination.</p>
                </section>

                <section id="retention" class="policy-section scroll-mt-28">
                    <p class="font-mono text-sm text-indigo-600">05</p>
                    <h2 class="{{ $h2 }}">Cryptographic Data Retention</h2>
                    <p class="mt-5">Active ledgers are retained indefinitely to ensure your historical AI heuristics remain accurate. However, if a user initiates a "Purge Node" protocol, all associated financial records, personal identifiers, and access tokens are cryptographically shredded from our primary databases within 72 hours, leaving no recoverable trace.</p>
                </section>

                <section id="cookies" class="policy-section scroll-mt-28">
                    <p class="font-mono text-sm text-indigo-600">06</p>
                    <h2 class="{{ $h2 }}">Tracking &amp; Session Cookies</h2>
                    <p class="mt-5">We deploy highly secure JWTs (JSON Web Tokens) and essential session cookies to maintain your authenticated state across the application. We do not deploy third-party advertising trackers.</p>
                    <button type="button" @click="$dispatch('notify', {message: 'Cookie preferences are managed in your account settings.', type: 'info'})" class="mt-6 inline-flex items-center h-10 px-4 rounded-lg border border-slate-300 bg-white text-sm font-semibold text-slate-800 hover:border-slate-900 transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-100">
                        Manage Cookie Preferences
                    </button>
                </section>

                <section id="transfers" class="policy-section scroll-mt-28">
                    <p class="font-mono text-sm text-indigo-600">07</p>
                    <h2 class="{{ $h2 }}">International Server Transfers</h2>
                    <p class="mt-5">FinanceAI utilizes a globally distributed infrastructure. By utilizing the platform, your encrypted payloads may be routed through servers located in the United States, European Union, or Singapore. All cross-border transfers are governed by Standard Contractual Clauses (SCCs) to ensure legal compliance.</p>
                </section>

                <section id="security" class="policy-section scroll-mt-28">
                    <p class="font-mono text-sm text-indigo-600">08</p>
                    <h2 class="{{ $h2 }}">Security Architecture</h2>
                    <p class="mt-5">Security is not an afterthought; it is our foundation. We employ multiple redundant systems to guarantee the integrity of your capital ledgers.</p>
                    <ul class="mt-6 flex flex-wrap gap-x-8 gap-y-2 font-mono text-xs uppercase tracking-wider text-slate-700">
                        <li><i class="fa-solid fa-lock text-slate-400 mr-1.5" aria-hidden="true"></i>AES-256</li>
                        <li><i class="fa-solid fa-network-wired text-slate-400 mr-1.5" aria-hidden="true"></i>TLS 1.3</li>
                        <li><i class="fa-solid fa-users text-slate-400 mr-1.5" aria-hidden="true"></i>Strict IAM</li>
                    </ul>
                </section>

                <section id="rights" class="policy-section scroll-mt-28">
                    <p class="font-mono text-sm text-indigo-600">09</p>
                    <h2 class="{{ $h2 }}">Exercising Your Rights</h2>
                    <p class="mt-5">To invoke any of your rights (Data Export, Node Deletion, Rectification), navigate to your <strong>Identity Profile &rarr; Security Settings</strong>. For highly sensitive requests requiring manual verification, please interface with our compliance team via the <a href="{{ route('contact') }}">Contact Hub</a>. Requests are fulfilled within 30 standard cryptographic cycles (days).</p>
                </section>

                <section id="dpo" class="policy-section scroll-mt-28">
                    <p class="font-mono text-sm text-indigo-600">10</p>
                    <h2 class="{{ $h2 }}">Data Protection Officer (DPO)</h2>
                    <p class="mt-5">If you require direct escalation regarding data compliance, our designated Data Protection Officer can be reached through the secure channels below.</p>
                    <dl class="mt-6 grid sm:grid-cols-2 border-t border-slate-900">
                        <div class="py-5 sm:pr-6 border-b border-slate-200">
                            <dt class="eyebrow text-slate-400">Encrypted Email</dt>
                            <dd class="mt-2 text-base font-bold text-slate-900"><a href="mailto:privacy@financeai.com" class="!no-underline !text-slate-900 hover:!text-indigo-600">privacy@financeai.com</a></dd>
                        </div>
                        <div class="py-5 border-b border-slate-200">
                            <dt class="eyebrow text-slate-400">Headquarters</dt>
                            <dd class="mt-2 text-base font-bold text-slate-900">Global Operations Node</dd>
                        </div>
                    </dl>
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
    Alpine.data('complianceEngine', () => ({
        activeSection: 'collection',
        readingProgress: 0,
        isExporting: false,

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

        exportPolicyPDF() {
            this.isExporting = true;
            this.$dispatch('notify', { message: 'Preparing document for PDF export...', type: 'info' });
            setTimeout(() => { this.isExporting = false; window.print(); }, 800);
        }
    }));
});
</script>
@endpush
