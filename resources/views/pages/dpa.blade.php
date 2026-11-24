@extends('layouts.landing')
@include('partials.site-type')

@section('title', 'Data Processing Addendum | FinanceAI Enterprise')
@section('meta_description', 'Review the Data Processing Addendum (DPA) governing how FinanceAI handles data on behalf of controllers and processors.')

@section('content')
@php
    $sections = ['Scope of Processing', 'Sub-Processor Disclosure', 'Technical & Organizational Measures (TOMs)', 'Data Breach Notification Protocol', 'Data Returns & Deletion'];
@endphp
<div class="paper" x-data="dpaEngine()">

    <x-site.hero eyebrow="Compliance Framework" lead="Data Processing" accent="Addendum"
        lede="This Data Processing Addendum supplements our Terms of Service and Privacy Policy. It establishes the technical and organizational measures governing data processing on behalf of FinanceAI users.">
        <x-slot name="actions">
            <button type="button" @click="printDPA()" class="inline-flex items-center justify-center gap-2 h-12 px-6 rounded-xl font-bold text-white border border-white/25 bg-white/5 hover:bg-white/10 transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-white/20">
                <i class="fa-solid fa-file-pdf text-sm" aria-hidden="true"></i> Download DPA
            </button>
        </x-slot>
        <x-slot name="meta">
            <dl class="flex flex-wrap gap-x-10 gap-y-3 font-mono text-xs uppercase tracking-wider">
                <div class="flex gap-2"><dt class="text-slate-400">Effective:</dt><dd class="text-slate-200">{{ date('F d, Y') }}</dd></div>
                <div class="flex gap-2"><dt class="text-slate-400">DPA Version:</dt><dd class="text-slate-200">1.2.0</dd></div>
            </dl>
        </x-slot>
    </x-site.hero>

    {{-- Roles at a glance --}}
    <section class="border-b border-slate-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-200">
            @foreach([
                ['fa-handshake', 'Data Controller', 'You (the user) are the Data Controller. You determine the purpose and means of processing personal data within your ledgers.'],
                ['fa-server', 'Data Processor', 'FinanceAI acts as the Data Processor. We process your financial telemetry strictly on your documented instructions.'],
                ['fa-gavel', 'Legal Basis', 'Processing is governed by GDPR Article 28 (Controller-Processor agreements) and applicable SCCs for international transfers.'],
            ] as $i => [$icon, $title, $desc])
                <div class="py-10 md:px-8 first:md:pl-0 last:md:pr-0" data-reveal style="--d: {{ $i * 80 }}ms">
                    <p class="eyebrow text-slate-400 flex items-center gap-2"><i class="fa-solid {{ $icon }} text-indigo-500" aria-hidden="true"></i> {{ $title }}</p>
                    <p class="mt-4 text-slate-700 leading-relaxed">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Document --}}
    <section class="py-16 sm:py-24" id="dpa-printable">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-12">
            <aside class="hidden lg:block lg:col-span-3">
                <nav class="sticky top-28" aria-label="Contents">
                    <p class="eyebrow text-slate-400">Contents</p>
                    <ol class="mt-5 space-y-3 border-l border-slate-200">
                        @foreach($sections as $i => $s)
                            <li><a href="#dpa-{{ $i + 1 }}" class="block -ml-px pl-4 border-l border-transparent text-sm text-slate-600 hover:text-slate-900 hover:border-slate-900 transition-colors">
                                <span class="font-mono text-xs text-slate-400 mr-2">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>{{ $s }}</a></li>
                        @endforeach
                    </ol>
                    <div class="mt-8 h-px bg-slate-200 overflow-hidden" aria-hidden="true"><div class="h-full bg-indigo-600 transition-[width] duration-150" :style="`width: ${readingProgress}%`"></div></div>
                </nav>
            </aside>

            <article class="lg:col-span-9 max-w-3xl prose-site space-y-16" id="dpa-content">
                <section id="dpa-1" class="scroll-mt-28" data-reveal>
                    <p class="font-mono text-sm text-indigo-600">01</p>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-slate-900">{{ $sections[0] }}</h2>
                    <p class="mt-5">FinanceAI processes personal data and financial telemetry solely on behalf of the Data Controller for the following authorized purposes:</p>
                    <ul class="mt-6 border-t border-slate-200">
                        @foreach([
                            'Operating the cryptographic ledgers (income, expense, and capital flow tracking)',
                            'Executing AI heuristic forecasting and predictive burn-rate analysis',
                            'Generating automated financial reports and PDF audit documents',
                            'Providing customer support and resolving technical inquiries',
                            'Ensuring platform security through anomaly detection and access logging',
                        ] as $item)
                            <li class="flex gap-4 py-3.5 border-b border-slate-200 text-base"><i class="fa-solid fa-check text-emerald-600 mt-1.5 text-sm" aria-hidden="true"></i><span>{{ $item }}</span></li>
                        @endforeach
                    </ul>
                </section>

                <section id="dpa-2" class="scroll-mt-28" data-reveal>
                    <p class="font-mono text-sm text-indigo-600">02</p>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-slate-900">{{ $sections[1] }}</h2>
                    <p class="mt-5">FinanceAI engages vetted sub-processors to deliver specific infrastructure services. All sub-processors are contractually bound to data protection standards equivalent to this DPA.</p>
                    <div class="mt-6 overflow-x-auto">
                        <table class="w-full text-left text-base">
                            <thead>
                                <tr class="border-b border-slate-900">
                                    <th class="py-3 pr-4 eyebrow text-slate-500 font-normal">Sub-Processor</th>
                                    <th class="py-3 pr-4 eyebrow text-slate-500 font-normal">Purpose</th>
                                    <th class="py-3 eyebrow text-slate-500 font-normal">Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach([
                                    ['Amazon Web Services (AWS)', 'Cloud Infrastructure & Compute', 'US / EU / AP'],
                                    ['Google Cloud (Gemini API)', 'AI Inference Engine', 'US'],
                                    ['Stripe Inc.', 'Payment Processing', 'US / EU'],
                                    ['Mailgun (Sinch)', 'Transactional Email Routing', 'EU'],
                                ] as $sp)
                                    <tr class="border-b border-slate-200">
                                        <td class="py-4 pr-4 font-bold text-slate-900">{{ $sp[0] }}</td>
                                        <td class="py-4 pr-4">{{ $sp[1] }}</td>
                                        <td class="py-4 font-mono text-sm">{{ $sp[2] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                <section id="dpa-3" class="scroll-mt-28" data-reveal>
                    <p class="font-mono text-sm text-indigo-600">03</p>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-slate-900">{{ $sections[2] }}</h2>
                    <p class="mt-5">FinanceAI implements the following security controls to protect data integrity and confidentiality:</p>
                    <dl class="mt-6 grid sm:grid-cols-2 border-t border-l border-slate-200 bg-white">
                        @foreach([
                            ['fa-lock', 'Encryption', 'AES-256 at rest, TLS 1.3 in transit. Zero-knowledge key management.'],
                            ['fa-users', 'Access Control', 'Role-based IAM with principle of least privilege. MFA enforced for operators.'],
                            ['fa-server', 'Availability', '99.99% uptime SLA. Multi-AZ redundancy. Automated failover.'],
                            ['fa-clipboard-list', 'Audit Logging', 'Immutable audit trails via Spatie Activity Log. 12-month retention.'],
                        ] as [$icon, $t, $d])
                            <div class="p-6 border-b border-r border-slate-200">
                                <dt class="flex items-center gap-2 font-black text-slate-900 text-base"><i class="fa-solid {{ $icon }} text-sm text-slate-400" aria-hidden="true"></i>{{ $t }}</dt>
                                <dd class="mt-2 text-[15px] leading-relaxed text-slate-600">{{ $d }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </section>

                <section id="dpa-4" class="scroll-mt-28" data-reveal>
                    <p class="font-mono text-sm text-indigo-600">04</p>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-slate-900">{{ $sections[3] }}</h2>
                    <p class="mt-5">In the event of a confirmed data breach affecting personal data, FinanceAI will:</p>
                    <ol class="mt-6 border-l-2 border-rose-500 pl-6 space-y-5 text-base">
                        <li><strong>Within 48 hours</strong> — Notify the Data Controller via any registered email addresses.</li>
                        <li><strong>Within 72 hours</strong> — Deliver a preliminary incident report detailing nature, scope, and containment actions.</li>
                        <li><strong>Within 30 days</strong> — Provide a full post-mortem with root cause, remediation, and preventive measures.</li>
                    </ol>
                </section>

                <section id="dpa-5" class="scroll-mt-28" data-reveal>
                    <p class="font-mono text-sm text-indigo-600">05</p>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-slate-900">{{ $sections[4] }}</h2>
                    <p class="mt-5">Upon termination of services, FinanceAI will, at the Data Controller's election, either (a) return all personal data via a structured export (JSON/CSV), or (b) cryptographically shred all data within 72 hours, with written certification of destruction. No recoverable backups containing personal data will exist beyond 30 calendar days post-deletion-request.</p>
                </section>
            </article>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<style>
    @media print {
        header, footer, aside, button, .fixed { display: none !important; }
        [data-reveal] { opacity: 1 !important; transform: none !important; }
    }
</style>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dpaEngine', () => ({
        readingProgress: 0,
        init() { window.addEventListener('scroll', () => this.calculateReadingProgress(), { passive: true }); },
        calculateReadingProgress() {
            const c = document.getElementById('dpa-content');
            if (!c) return;
            const r = c.getBoundingClientRect();
            if (r.top > window.innerHeight) { this.readingProgress = 0; return; }
            const total = r.height - window.innerHeight;
            const scrolled = Math.abs(Math.min(0, r.top));
            this.readingProgress = Math.min(Math.max((scrolled / total) * 100, 0), 100);
        },
        printDPA() {
            this.$dispatch('notify', { message: 'Preparing DPA document for print...', type: 'info' });
            setTimeout(() => { window.print(); }, 500);
        }
    }));
});
</script>
@endpush
