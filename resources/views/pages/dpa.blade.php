@extends('layouts.landing')

@section('title', 'Data Processing Addendum | FinanceAI Enterprise')
@section('meta_description', 'Review the Data Processing Addendum (DPA) governing how FinanceAI handles data on behalf of controllers and processors.')

@section('content')

<div class="bg-[#f8fafc] font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden"
     x-data="dpaEngine()">

    {{-- Ambient --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-10%] right-[-10%] w-[900px] h-[900px] bg-indigo-500/10 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[10%] left-[-10%] w-[600px] h-[600px] bg-sky-500/10 blur-[100px] rounded-full"></div>
    </div>

    {{-- ================= 1. HERO ================= --}}
    <section class="relative pt-40 pb-20 lg:pt-48 lg:pb-24 overflow-hidden z-10 border-b border-slate-200/60 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-4xl mx-auto">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-50 border border-slate-200 text-slate-600 text-[10px] font-black uppercase tracking-widest mb-8 shadow-sm reveal-up">
                    <i class="fa-solid fa-file-signature text-indigo-500"></i> Compliance Framework
                </div>

                <h1 class="text-4xl md:text-6xl font-black text-slate-900 tracking-tight leading-[1.1] reveal-up" style="transition-delay: 100ms;">
                    Data Processing Addendum
                </h1>

                <div class="mt-6 flex flex-wrap items-center justify-center gap-4 text-xs font-bold uppercase tracking-widest text-slate-400 reveal-up" style="transition-delay: 150ms;">
                    <span class="flex items-center gap-1.5"><i class="fa-regular fa-clock text-indigo-400"></i> Effective: {{ date('F d, Y') }}</span>
                    <span class="hidden sm:block text-slate-300">|</span>
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-code-commit text-emerald-400"></i> DPA Version: 1.2.0</span>
                </div>

                <p class="mt-8 text-lg text-slate-500 font-medium leading-relaxed reveal-up" style="transition-delay: 200ms;">
                    This Data Processing Addendum supplements our Terms of Service and Privacy Policy. It establishes the technical and organizational measures governing data processing on behalf of FinanceAI users.
                </p>

                <div class="mt-10 flex items-center justify-center gap-4 reveal-up" style="transition-delay: 300ms;">
                    <button @click="printDPA()" class="px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl font-black text-xs uppercase tracking-widest shadow-sm hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all flex items-center gap-2 focus:outline-none">
                        <i class="fa-solid fa-file-pdf text-rose-500"></i> Download DPA
                    </button>
                </div>
            </div>

            {{-- TL;DR Cards --}}
            <div class="grid md:grid-cols-3 gap-6 mt-20 reveal-up" style="transition-delay: 400ms;">
                <div class="bg-slate-50 border border-slate-200 rounded-[2rem] p-8 text-center shadow-sm">
                    <div class="w-12 h-12 mx-auto bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mb-4"><i class="fa-solid fa-handshake text-lg"></i></div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2">Data Controller</h3>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed">You (the user) are the Data Controller. You determine the purpose and means of processing personal data within your ledgers.</p>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-[2rem] p-8 text-center shadow-sm">
                    <div class="w-12 h-12 mx-auto bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center mb-4"><i class="fa-solid fa-server text-lg"></i></div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2">Data Processor</h3>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed">FinanceAI acts as the Data Processor. We process your financial telemetry strictly on your documented instructions.</p>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-[2rem] p-8 text-center shadow-sm">
                    <div class="w-12 h-12 mx-auto bg-sky-100 text-sky-600 rounded-xl flex items-center justify-center mb-4"><i class="fa-solid fa-gavel text-lg"></i></div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2">Legal Basis</h3>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed">Processing is governed by GDPR Article 28 (Controller-Processor agreements) and applicable SCCs for international transfers.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= 2. DPA DOCUMENT ================= --}}
    <section class="py-20 relative z-10" id="dpa-printable">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-[3rem] border border-slate-200 shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8 sm:p-12 lg:p-16 relative">

                {{-- Reading Progress --}}
                <div class="absolute top-0 left-0 right-0 h-1 bg-slate-100 rounded-t-[3rem] overflow-hidden">
                    <div class="h-full bg-indigo-500 transition-all duration-150" :style="`width: ${readingProgress}%`"></div>
                </div>

                <div class="space-y-14" id="dpa-content">

                    {{-- Section 1 --}}
                    <div class="scroll-mt-32">
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">01</span>
                            Scope of Processing
                        </h2>
                        <p class="text-slate-600 font-medium leading-relaxed mb-6">
                            FinanceAI processes personal data and financial telemetry solely on behalf of the Data Controller for the following authorized purposes:
                        </p>
                        <ul class="space-y-3">
                            @foreach([
                                'Operating the cryptographic ledgers (income, expense, and capital flow tracking)',
                                'Executing AI heuristic forecasting and predictive burn-rate analysis',
                                'Generating automated financial reports and PDF audit documents',
                                'Providing customer support and resolving technical inquiries',
                                'Ensuring platform security through anomaly detection and access logging'
                            ] as $item)
                            <li class="flex items-start gap-3">
                                <i class="fa-solid fa-circle-check text-emerald-500 mt-1"></i>
                                <span class="text-slate-600 font-medium">{{ $item }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Section 2 --}}
                    <div class="scroll-mt-32">
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">02</span>
                            Sub-Processor Disclosure
                        </h2>
                        <p class="text-slate-600 font-medium leading-relaxed mb-6">
                            FinanceAI engages vetted sub-processors to deliver specific infrastructure services. All sub-processors are contractually bound to data protection standards equivalent to this DPA.
                        </p>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr>
                                        <th class="py-3 text-xs font-black uppercase tracking-widest text-slate-400 border-b border-slate-200">Sub-Processor</th>
                                        <th class="py-3 text-xs font-black uppercase tracking-widest text-slate-400 border-b border-slate-200">Purpose</th>
                                        <th class="py-3 text-xs font-black uppercase tracking-widest text-slate-400 border-b border-slate-200">Location</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm font-medium text-slate-700">
                                    @foreach([
                                        ['Amazon Web Services (AWS)', 'Cloud Infrastructure & Compute', 'US / EU / AP'],
                                        ['Google Cloud (Gemini API)', 'AI Inference Engine', 'US'],
                                        ['Stripe Inc.', 'Payment Processing', 'US / EU'],
                                        ['Mailgun (Sinch)', 'Transactional Email Routing', 'EU']
                                    ] as $sp)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="py-4 border-b border-slate-100 font-bold text-slate-900">{{ $sp[0] }}</td>
                                        <td class="py-4 border-b border-slate-100">{{ $sp[1] }}</td>
                                        <td class="py-4 border-b border-slate-100">{{ $sp[2] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Section 3 --}}
                    <div class="scroll-mt-32">
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">03</span>
                            Technical & Organizational Measures (TOMs)
                        </h2>
                        <p class="text-slate-600 font-medium leading-relaxed mb-6">
                            FinanceAI implements the following security controls to protect data integrity and confidentiality:
                        </p>
                        <div class="grid sm:grid-cols-2 gap-4">
                            @foreach([
                                ['fa-lock', 'indigo', 'Encryption', 'AES-256 at rest, TLS 1.3 in transit. Zero-knowledge key management.'],
                                ['fa-users', 'emerald', 'Access Control', 'Role-based IAM with principle of least privilege. MFA enforced for operators.'],
                                ['fa-server', 'sky', 'Availability', '99.99% uptime SLA. Multi-AZ redundancy. Automated failover.'],
                                ['fa-clipboard-list', 'amber', 'Audit Logging', 'Immutable audit trails via Spatie Activity Log. 12-month retention.']
                            ] as $tom)
                            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200">
                                <i class="fa-solid {{ $tom[0] }} text-{{ $tom[1] }}-500 mb-3 text-lg block"></i>
                                <h4 class="text-sm font-black text-slate-900 mb-1">{{ $tom[2] }}</h4>
                                <p class="text-xs text-slate-500 font-medium leading-relaxed">{{ $tom[3] }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Section 4 --}}
                    <div class="scroll-mt-32">
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">04</span>
                            Data Breach Notification Protocol
                        </h2>
                        <p class="text-slate-600 font-medium leading-relaxed mb-6">
                            In the event of a confirmed data breach affecting personal data, FinanceAI will:
                        </p>
                        <div class="bg-rose-50 border border-rose-100 rounded-2xl p-6">
                            <ul class="space-y-3">
                                <li class="flex items-start gap-3"><i class="fa-solid fa-clock text-rose-500 mt-1"></i><span class="text-sm font-medium text-slate-700"><strong class="text-slate-900">Within 48 hours</strong> — Notify the Data Controller via any registered email addresses.</span></li>
                                <li class="flex items-start gap-3"><i class="fa-solid fa-file-alt text-rose-500 mt-1"></i><span class="text-sm font-medium text-slate-700"><strong class="text-slate-900">Within 72 hours</strong> — Deliver a preliminary incident report detailing nature, scope, and containment actions.</span></li>
                                <li class="flex items-start gap-3"><i class="fa-solid fa-shield-halved text-rose-500 mt-1"></i><span class="text-sm font-medium text-slate-700"><strong class="text-slate-900">Within 30 days</strong> — Provide a full post-mortem with root cause, remediation, and preventive measures.</span></li>
                            </ul>
                        </div>
                    </div>

                    {{-- Section 5 --}}
                    <div class="scroll-mt-32">
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">05</span>
                            Data Returns & Deletion
                        </h2>
                        <p class="text-slate-600 font-medium leading-relaxed">
                            Upon termination of services, FinanceAI will, at the Data Controller's election, either (a) return all personal data via a structured export (JSON/CSV), or (b) cryptographically shred all data within 72 hours, with written certification of destruction. No recoverable backups containing personal data will exist beyond 30 calendar days post-deletion-request.
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<style>
    .reveal-up { opacity: 0; transform: translateY(30px); transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
    .reveal-up.is-visible { opacity: 1; transform: translateY(0); }
    @media print {
        nav, footer, .fixed, button, .reveal-up { opacity: 1 !important; transform: none !important; }
        .fixed { display: none !important; }
    }
</style>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dpaEngine', () => ({
        readingProgress: 0,
        init() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('is-visible'); });
            }, { threshold: 0.1 });
            document.querySelectorAll('.reveal-up').forEach(el => observer.observe(el));
            window.addEventListener('scroll', () => this.calculateReadingProgress());
        },
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
