@extends('layouts.landing')

@section('title', 'Global Privacy Policy | FinanceAI Enterprise')
@section('meta_description', 'FinanceAI is committed to cryptographic data security and absolute financial privacy. Read our global compliance policy.')

@section('content')

<div class="bg-[#f8fafc] font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden" 
     x-data="complianceEngine()">

    {{-- Pristine Light Ambient Backgrounds --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[900px] h-[900px] bg-indigo-500/10 blur-[120px] rounded-full transition-colors duration-1000"></div>
        <div class="absolute bottom-[10%] right-[-10%] w-[600px] h-[600px] bg-sky-500/10 blur-[100px] rounded-full"></div>
    </div>

    {{-- ================= 1. COMPLIANCE HERO & TL;DR ================= --}}
    <section class="relative pt-40 pb-20 lg:pt-48 lg:pb-24 overflow-hidden z-10 border-b border-slate-200/60 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-4xl mx-auto">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-50 border border-slate-200 text-slate-600 text-[10px] font-black uppercase tracking-widest mb-8 shadow-sm reveal-up">
                    <i class="fa-solid fa-scale-balanced text-indigo-500"></i> Legal & Compliance
                </div>

                <h1 class="text-4xl md:text-6xl font-black text-slate-900 tracking-tight leading-[1.1] reveal-up" style="transition-delay: 100ms;">
                    Global Privacy Policy
                </h1>

                <div class="mt-6 flex flex-wrap items-center justify-center gap-4 text-xs font-bold uppercase tracking-widest text-slate-400 reveal-up" style="transition-delay: 150ms;">
                    <span class="flex items-center gap-1.5"><i class="fa-regular fa-clock text-indigo-400"></i> Last Updated: {{ date('F d, Y') }}</span>
                    <span class="hidden sm:block text-slate-300">|</span>
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-code-commit text-emerald-400"></i> Document Version: 3.1.4</span>
                </div>

                <p class="mt-8 text-lg text-slate-500 font-medium leading-relaxed reveal-up" style="transition-delay: 200ms;">
                    FinanceAI is architected on the principle of absolute cryptographic privacy. We are committed to protecting your personal and financial telemetry in strict compliance with global regulations.
                </p>

                {{-- Interactive Export Actions --}}
                <div class="mt-10 flex items-center justify-center gap-4 reveal-up" style="transition-delay: 300ms;">
                    <button @click="exportPolicyPDF()" :disabled="isExporting" class="px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl font-black text-xs uppercase tracking-widest shadow-sm hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all flex items-center gap-2 focus:outline-none">
                        <span x-show="!isExporting"><i class="fa-solid fa-file-pdf text-rose-500"></i> Download Legal PDF</span>
                        <span x-show="isExporting" style="display: none;"><i class="fa-solid fa-circle-notch fa-spin text-indigo-500"></i> Generating Hash...</span>
                    </button>
                </div>
            </div>

            {{-- The "TL;DR" Bento Box --}}
            <div class="grid md:grid-cols-3 gap-6 mt-20 reveal-up" style="transition-delay: 400ms;">
                <div class="bg-slate-50 border border-slate-200 rounded-[2rem] p-8 text-center shadow-sm">
                    <div class="w-12 h-12 mx-auto bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center mb-4"><i class="fa-solid fa-ban text-lg"></i></div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2">Zero Data Selling</h3>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed">Your financial telemetry is your own. We do not, and will never, sell your data to third-party advertisers or brokers.</p>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-[2rem] p-8 text-center shadow-sm">
                    <div class="w-12 h-12 mx-auto bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mb-4"><i class="fa-solid fa-lock text-lg"></i></div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2">Military Encryption</h3>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed">All ledgers are secured using AES-256 encryption at rest and TLS 1.3 in transit to prevent unauthorized access.</p>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-[2rem] p-8 text-center shadow-sm">
                    <div class="w-12 h-12 mx-auto bg-sky-100 text-sky-600 rounded-xl flex items-center justify-center mb-4"><i class="fa-solid fa-globe text-lg"></i></div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2">Global Compliance</h3>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed">Strict adherence to GDPR (EU), CCPA (California), and international financial data protection frameworks.</p>
                </div>
            </div>

        </div>
    </section>

    {{-- ================= 2. LEGAL DOCUMENT ENGINE ================= --}}
    <section class="py-20 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col lg:flex-row gap-12 lg:gap-20 relative">
                
                {{-- LEFT: Sticky Table of Contents --}}
                <div class="w-full lg:w-1/4 shrink-0">
                    <div class="sticky top-28 bg-white lg:bg-transparent lg:border-none border border-slate-200 rounded-[2rem] p-6 lg:p-0 shadow-sm lg:shadow-none z-30 overflow-x-auto lg:overflow-visible">
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6 hidden lg:block">Document Index</h4>
                        
                        <ul class="flex lg:flex-col gap-2 lg:gap-1 text-sm font-bold w-max lg:w-full">
                            @foreach([
                                'collection' => '1. Data Collection',
                                'usage' => '2. Usage Protocol',
                                'gdpr' => '3. GDPR (EU) Compliance',
                                'ccpa' => '4. CCPA (California)',
                                'retention' => '5. Data Retention',
                                'cookies' => '6. Tracking & Cookies',
                                'transfers' => '7. International Transfers',
                                'security' => '8. Security Architecture',
                                'rights' => '9. Exercising Rights',
                                'dpo' => '10. Contact DPO'
                            ] as $id => $label)
                                <li>
                                    <a href="#{{ $id }}" 
                                       @click.prevent="scrollToSection('{{ $id }}')"
                                       class="block px-4 lg:px-3 py-2 lg:py-2.5 rounded-xl transition-all duration-300 relative group overflow-hidden"
                                       :class="activeSection === '{{ $id }}' ? 'bg-indigo-50/80 text-indigo-700' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'">
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500 transition-transform duration-300" :class="activeSection === '{{ $id }}' ? 'scale-y-100' : 'scale-y-0'"></div>
                                        {{ $label }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- RIGHT: The Legal Text --}}
                <div class="w-full lg:w-3/4 max-w-4xl bg-white rounded-[3rem] border border-slate-200 shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8 sm:p-12 lg:p-16 relative">
                    
                    {{-- Interactive Reading Progress --}}
                    <div class="absolute top-0 left-0 right-0 h-1 bg-slate-100 rounded-t-[3rem] overflow-hidden">
                        <div class="h-full bg-indigo-500 transition-all duration-150" :style="`width: ${readingProgress}%`"></div>
                    </div>

                    <div class="space-y-16" id="legal-content">
                        
                        {{-- Section 1 --}}
                        <div id="collection" class="policy-section scroll-mt-32">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">01</span>
                                Information We Collect
                            </h2>
                            <p class="text-slate-600 font-medium leading-relaxed mb-6">
                                To provide the FinanceAI suite of tools, we must collect and process specific telemetric and personal data points. We operate on a principle of data minimization—collecting only what is absolutely necessary.
                            </p>
                            <ul class="space-y-3">
                                <li class="flex items-start gap-3 group">
                                    <i class="fa-solid fa-circle-check text-emerald-500 mt-1 transition-transform group-hover:scale-125"></i>
                                    <span class="text-slate-600 font-medium"><strong class="text-slate-900">Identity Nodes:</strong> Name, authenticated email addresses, and encrypted login credentials.</span>
                                </li>
                                <li class="flex items-start gap-3 group">
                                    <i class="fa-solid fa-circle-check text-emerald-500 mt-1 transition-transform group-hover:scale-125"></i>
                                    <span class="text-slate-600 font-medium"><strong class="text-slate-900">Cryptographic Ledgers:</strong> Transaction amounts, categorizations, income streams, and linked budget structures.</span>
                                </li>
                                <li class="flex items-start gap-3 group">
                                    <i class="fa-solid fa-circle-check text-emerald-500 mt-1 transition-transform group-hover:scale-125"></i>
                                    <span class="text-slate-600 font-medium"><strong class="text-slate-900">System Telemetry:</strong> Anonymized interaction analytics, device signatures, and IP logs strictly for security monitoring.</span>
                                </li>
                            </ul>
                        </div>

                        {{-- Section 2 --}}
                        <div id="usage" class="policy-section scroll-mt-32">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">02</span>
                                Information Usage Protocol
                            </h2>
                            <p class="text-slate-600 font-medium leading-relaxed mb-6">
                                The data we ingest is utilized exclusively to operate, secure, and improve the FinanceAI architecture. Your financial variables are fed into our heuristic engines to generate insights, but are never exposed to human operators.
                            </p>
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                                    <i class="fa-solid fa-microchip text-indigo-500 mb-2 block"></i>
                                    <h4 class="text-sm font-black text-slate-900">Algorithm Training</h4>
                                    <p class="text-xs text-slate-500 mt-1">Anonymized data trains predictive burn-rate models.</p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                                    <i class="fa-solid fa-shield-halved text-emerald-500 mb-2 block"></i>
                                    <h4 class="text-sm font-black text-slate-900">Fraud Prevention</h4>
                                    <p class="text-xs text-slate-500 mt-1">Identifying anomalies and defending against unauthorized access.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Section 3 --}}
                        <div id="gdpr" class="policy-section scroll-mt-32">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">03</span>
                                GDPR (EU) Compliance
                            </h2>
                            <p class="text-slate-600 font-medium leading-relaxed mb-6">
                                For users residing within the European Economic Area (EEA), FinanceAI operates strictly as a Data Controller under the General Data Protection Regulation (GDPR). You retain absolute sovereignty over your nodes.
                            </p>
                            <div class="bg-white border-2 border-indigo-50 rounded-2xl p-6 shadow-inner">
                                <h4 class="text-xs font-black uppercase tracking-widest text-indigo-600 mb-4">Your Retained Rights</h4>
                                <ul class="grid sm:grid-cols-2 gap-3">
                                    <li class="flex items-center gap-2 text-sm font-bold text-slate-700"><i class="fa-solid fa-arrow-right text-indigo-300 text-[10px]"></i> Right to Access</li>
                                    <li class="flex items-center gap-2 text-sm font-bold text-slate-700"><i class="fa-solid fa-arrow-right text-indigo-300 text-[10px]"></i> Right to Rectification</li>
                                    <li class="flex items-center gap-2 text-sm font-bold text-slate-700"><i class="fa-solid fa-arrow-right text-indigo-300 text-[10px]"></i> Right to Erasure ("Forgotten")</li>
                                    <li class="flex items-center gap-2 text-sm font-bold text-slate-700"><i class="fa-solid fa-arrow-right text-indigo-300 text-[10px]"></i> Right to Portability</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Section 4 --}}
                        <div id="ccpa" class="policy-section scroll-mt-32">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">04</span>
                                CCPA (California Residents)
                            </h2>
                            <p class="text-slate-600 font-medium leading-relaxed">
                                Pursuant to the California Consumer Privacy Act (CCPA), California residents have explicit rights regarding their personal data. FinanceAI unequivocally states that <strong class="text-rose-500">we do not sell personal or financial data</strong>. You may request disclosure of any telemetry we have collected or demand immediate ledger deletion without facing any service discrimination.
                            </p>
                        </div>

                        {{-- Section 5 --}}
                        <div id="retention" class="policy-section scroll-mt-32">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">05</span>
                                Cryptographic Data Retention
                            </h2>
                            <p class="text-slate-600 font-medium leading-relaxed">
                                Active ledgers are retained indefinitely to ensure your historical AI heuristics remain accurate. However, if a user initiates a "Purge Node" protocol, all associated financial records, personal identifiers, and access tokens are cryptographically shredded from our primary databases within 72 hours, leaving no recoverable trace.
                            </p>
                        </div>

                        {{-- Section 6 --}}
                        <div id="cookies" class="policy-section scroll-mt-32">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">06</span>
                                Tracking & Session Cookies
                            </h2>
                            <p class="text-slate-600 font-medium leading-relaxed mb-6">
                                We deploy highly secure JWTs (JSON Web Tokens) and essential session cookies to maintain your authenticated state across the application. We do not deploy third-party advertising trackers.
                            </p>
                            <button @click="$dispatch('notify', {message: 'Cookie preferences are managed in your account settings.', type: 'info'})" class="px-5 py-2.5 bg-slate-100 text-slate-700 font-bold text-xs uppercase tracking-widest rounded-xl hover:bg-slate-200 transition-colors focus:outline-none">
                                Manage Cookie Preferences
                            </button>
                        </div>

                        {{-- Section 7 --}}
                        <div id="transfers" class="policy-section scroll-mt-32">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">07</span>
                                International Server Transfers
                            </h2>
                            <p class="text-slate-600 font-medium leading-relaxed">
                                FinanceAI utilizes a globally distributed infrastructure. By utilizing the platform, your encrypted payloads may be routed through servers located in the United States, European Union, or Singapore. All cross-border transfers are governed by Standard Contractual Clauses (SCCs) to ensure legal compliance.
                            </p>
                        </div>

                        {{-- Section 8 --}}
                        <div id="security" class="policy-section scroll-mt-32">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">08</span>
                                Security Architecture
                            </h2>
                            <p class="text-slate-600 font-medium leading-relaxed mb-6">
                                Security is not an afterthought; it is our foundation. We employ multiple redundant systems to guarantee the integrity of your capital ledgers.
                            </p>
                            <div class="flex flex-wrap gap-3">
                                <span class="px-3 py-1.5 bg-slate-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest"><i class="fa-solid fa-lock text-slate-400 mr-1"></i> AES-256</span>
                                <span class="px-3 py-1.5 bg-slate-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest"><i class="fa-solid fa-network-wired text-slate-400 mr-1"></i> TLS 1.3</span>
                                <span class="px-3 py-1.5 bg-slate-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest"><i class="fa-solid fa-users text-slate-400 mr-1"></i> Strict IAM</span>
                            </div>
                        </div>

                        {{-- Section 9 --}}
                        <div id="rights" class="policy-section scroll-mt-32">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">09</span>
                                Exercising Your Rights
                            </h2>
                            <p class="text-slate-600 font-medium leading-relaxed">
                                To invoke any of your rights (Data Export, Node Deletion, Rectification), navigate to your <strong>Identity Profile &rarr; Security Settings</strong>. For highly sensitive requests requiring manual verification, please interface with our compliance team via the <a href="{{ route('contact') ?? '#' }}" class="text-indigo-600 font-bold hover:underline">Contact Hub</a>. Requests are fulfilled within 30 standard cryptographic cycles (days).
                            </p>
                        </div>

                        {{-- Section 10 --}}
                        <div id="dpo" class="policy-section scroll-mt-32">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">10</span>
                                Data Protection Officer (DPO)
                            </h2>
                            <p class="text-slate-600 font-medium leading-relaxed mb-6">
                                If you require direct escalation regarding data compliance, our designated Data Protection Officer can be reached through the secure channels below.
                            </p>
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 shadow-sm"><i class="fa-solid fa-envelope"></i></div>
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Encrypted Email</p>
                                        <p class="text-sm font-bold text-slate-900">privacy@financeai.com</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 shadow-sm"><i class="fa-solid fa-location-dot"></i></div>
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Headquarters</p>
                                        <p class="text-sm font-bold text-slate-900">Global Operations Node</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

</div>

@endsection

@push('scripts')
<style>
    /* Scroll Reveal Initial State */
    .reveal-up {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .reveal-up.is-visible {
        opacity: 1;
        transform: translateY(0);
    }
    /* Hide scrollbar for mobile TOC */
    .overflow-x-auto::-webkit-scrollbar {
        display: none;
    }
    .overflow-x-auto {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    @media print {
        nav, footer, .fixed, button, .sticky { display: none !important; }
        .reveal-up { opacity: 1 !important; transform: none !important; }
        .rounded-\[3rem\] { border-radius: 0 !important; border: none !important; box-shadow: none !important; }
    }
</style>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('complianceEngine', () => ({
        activeSection: 'collection',
        readingProgress: 0,
        isExporting: false,

        init() {
            this.initScrollReveal();
            this.initScrollspy();
            
            // Listen for reading progress specifically on the legal content box
            window.addEventListener('scroll', () => {
                this.calculateReadingProgress();
            });
        },

        initScrollReveal() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.reveal-up').forEach(el => observer.observe(el));
        },

        // Dynamic Table of Contents Logic
        initScrollspy() {
            const sections = document.querySelectorAll('.policy-section');
            
            // Intersection Observer specifically for the TOC highlighting
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.activeSection = entry.target.id;
                        
                        // Optional: Scroll the mobile TOC to keep active item in view
                        const mobileToc = entry.target.id ? document.querySelector(`a[href="#${entry.target.id}"]`) : null;
                        if(mobileToc && window.innerWidth < 1024) {
                            mobileToc.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                        }
                    }
                });
            }, { 
                rootMargin: '-20% 0px -70% 0px' // Triggers when section hits the upper third of screen
            });

            sections.forEach(section => observer.observe(section));
        },

        calculateReadingProgress() {
            const container = document.getElementById('legal-content');
            if(!container) return;

            const rect = container.getBoundingClientRect();
            // If we haven't reached the content yet
            if (rect.top > window.innerHeight) {
                this.readingProgress = 0;
                return;
            }
            
            // Calculate percentage
            const totalHeight = rect.height - window.innerHeight;
            const scrolled = Math.abs(Math.min(0, rect.top)); // How much of the container has passed the top
            
            let progress = (scrolled / totalHeight) * 100;
            this.readingProgress = Math.min(Math.max(progress, 0), 100);
        },

        scrollToSection(id) {
            this.activeSection = id;
            const element = document.getElementById(id);
            if(element) {
                element.scrollIntoView({ behavior: 'smooth' });
            }
        },

        // Real PDF Generation via Browser Print
        exportPolicyPDF() {
            this.isExporting = true;
            this.$dispatch('notify', { message: 'Preparing document for PDF export...', type: 'info' });

            setTimeout(() => {
                this.isExporting = false;
                window.print();
            }, 800);
        }
    }));
});
</script>
@endpush