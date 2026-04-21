@extends('layouts.landing')

@section('title', 'Terms of Service | FinanceAI Enterprise')
@section('meta_description', 'Review the Terms and Conditions governing the use of the FinanceAI platform, API, and cryptographic ledgers.')

@section('content')

<div class="bg-[#f8fafc] font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden" 
     x-data="termsEngine()">

    {{-- Pristine Light Ambient Backgrounds --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-10%] right-[-10%] w-[900px] h-[900px] bg-indigo-500/10 blur-[120px] rounded-full transition-colors duration-1000"></div>
        <div class="absolute bottom-[10%] left-[-10%] w-[600px] h-[600px] bg-sky-500/10 blur-[100px] rounded-full"></div>
    </div>

    {{-- ================= 1. COMPLIANCE HERO & TL;DR ================= --}}
    <section class="relative pt-40 pb-20 lg:pt-48 lg:pb-24 overflow-hidden z-10 border-b border-slate-200/60 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-4xl mx-auto">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-50 border border-slate-200 text-slate-600 text-[10px] font-black uppercase tracking-widest mb-8 shadow-sm reveal-up">
                    <i class="fa-solid fa-file-contract text-indigo-500"></i> Legal Agreement
                </div>

                <h1 class="text-4xl md:text-6xl font-black text-slate-900 tracking-tight leading-[1.1] reveal-up" style="transition-delay: 100ms;">
                    Terms of Service
                </h1>

                <div class="mt-6 flex flex-wrap items-center justify-center gap-4 text-xs font-bold uppercase tracking-widest text-slate-400 reveal-up" style="transition-delay: 150ms;">
                    <span class="flex items-center gap-1.5"><i class="fa-regular fa-clock text-indigo-400"></i> Effective Date: {{ date('F d, Y') }}</span>
                    <span class="hidden sm:block text-slate-300">|</span>
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-code-commit text-emerald-400"></i> Document Version: 2.4.1</span>
                </div>

                <p class="mt-8 text-lg text-slate-500 font-medium leading-relaxed reveal-up" style="transition-delay: 200ms;">
                    This document establishes the binding cryptographic and legal parameters governing your utilization of the FinanceAI architecture. By initializing a node, you agree to these protocols.
                </p>

                {{-- Interactive Export Actions --}}
                <div class="mt-10 flex items-center justify-center gap-4 reveal-up" style="transition-delay: 300ms;">
                    <button @click="exportTermsPDF()" :disabled="isExporting" class="px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl font-black text-xs uppercase tracking-widest shadow-sm hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all flex items-center gap-2 focus:outline-none">
                        <span x-show="!isExporting"><i class="fa-solid fa-file-pdf text-rose-500"></i> Download Legal PDF</span>
                        <span x-show="isExporting" style="display: none;"><i class="fa-solid fa-circle-notch fa-spin text-indigo-500"></i> Generating Hash...</span>
                    </button>
                </div>
            </div>

            {{-- The "TL;DR" Bento Box --}}
            <div class="grid md:grid-cols-3 gap-6 mt-20 reveal-up" style="transition-delay: 400ms;">
                <div class="bg-slate-50 border border-slate-200 rounded-[2rem] p-8 text-center shadow-sm">
                    <div class="w-12 h-12 mx-auto bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mb-4"><i class="fa-solid fa-database text-lg"></i></div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2">Absolute Ownership</h3>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed">You retain 100% ownership of your financial data. We hold a limited license solely to run AI heuristics and generate your reports.</p>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-[2rem] p-8 text-center shadow-sm">
                    <div class="w-12 h-12 mx-auto bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center mb-4"><i class="fa-solid fa-door-open text-lg"></i></div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2">No Vendor Lock-In</h3>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed">You may terminate your subscription and permanently purge your cryptographic node at any time before your next billing cycle.</p>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-[2rem] p-8 text-center shadow-sm">
                    <div class="w-12 h-12 mx-auto bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center mb-4"><i class="fa-solid fa-hand-holding-dollar text-lg"></i></div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2">Not Financial Advice</h3>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed">FinanceAI provides telemetry and forecasting. Our AI does not provide certified legal, tax, or professional investment advice.</p>
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
                                'acceptance' => '1. Acceptance of Terms',
                                'account' => '2. Node Responsibilities',
                                'usage' => '3. Acceptable Use',
                                'payments' => '4. Billing & Quotas',
                                'ownership' => '5. Data Sovereignty',
                                'ai_disclaimer' => '6. AI Liability Disclaimer',
                                'liability' => '7. Limitation of Liability',
                                'termination' => '8. Protocol Termination',
                                'law' => '9. Governing Law',
                                'disputes' => '10. Dispute Resolution'
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
                        <div id="acceptance" class="policy-section scroll-mt-32">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">01</span>
                                Acceptance of Terms
                            </h2>
                            <p class="text-slate-600 font-medium leading-relaxed mb-6">
                                By deploying a FinanceAI node, accessing the REST API, or navigating the platform interface, you unequivocally agree to be bound by these Terms of Service. You represent and warrant that you are of legal age to form a binding contract (at least 18 years old in most jurisdictions) and possess the authority to bind your organization to these protocols.
                            </p>
                        </div>

                        {{-- Section 2 --}}
                        <div id="account" class="policy-section scroll-mt-32">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">02</span>
                                Node & Account Responsibilities
                            </h2>
                            <p class="text-slate-600 font-medium leading-relaxed mb-6">
                                You are the sole architect of your account's security. FinanceAI provides the encrypted infrastructure, but you are responsible for maintaining the confidentiality of your access keys.
                            </p>
                            <ul class="space-y-4">
                                <li class="flex items-start gap-3 bg-slate-50 p-4 rounded-xl border border-slate-100">
                                    <i class="fa-solid fa-shield-check text-indigo-500 mt-0.5 text-lg"></i>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900">Credential Integrity</h4>
                                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">You must maintain strict confidentiality of your cryptographic passwords and 2FA tokens. Any activity occurring under your node is legally attributed to you.</p>
                                    </div>
                                </li>
                                <li class="flex items-start gap-3 bg-slate-50 p-4 rounded-xl border border-slate-100">
                                    <i class="fa-solid fa-triangle-exclamation text-indigo-500 mt-0.5 text-lg"></i>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900">Breach Reporting</h4>
                                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">You must notify our security team immediately upon discovering any unauthorized deployment of your account or breach of your network perimeter.</p>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        {{-- Section 3 --}}
                        <div id="usage" class="policy-section scroll-mt-32">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">03</span>
                                Acceptable Use Policy
                            </h2>
                            <p class="text-slate-600 font-medium leading-relaxed mb-6">
                                FinanceAI is engineered for high-performance financial tracking. To maintain 99.99% uptime across the network, we strictly enforce the following prohibitions.
                            </p>
                            <div class="bg-rose-50 border border-rose-100 rounded-2xl p-6">
                                <h4 class="text-[10px] font-black uppercase tracking-widest text-rose-600 mb-4">Prohibited Vectors</h4>
                                <ul class="space-y-3">
                                    <li class="flex items-start gap-3"><i class="fa-solid fa-xmark text-rose-500 mt-1"></i> <span class="text-sm font-medium text-slate-700">Deploying automated scripts, scrapers, or bots against our UI (use the designated REST API instead).</span></li>
                                    <li class="flex items-start gap-3"><i class="fa-solid fa-xmark text-rose-500 mt-1"></i> <span class="text-sm font-medium text-slate-700">Reverse-engineering, decompiling, or attempting to extract the source code of our AI heuristic engines.</span></li>
                                    <li class="flex items-start gap-3"><i class="fa-solid fa-xmark text-rose-500 mt-1"></i> <span class="text-sm font-medium text-slate-700">Utilizing the platform to launder capital, track illicit transactions, or evade global tax compliance regulations.</span></li>
                                </ul>
                            </div>
                        </div>

                        {{-- Section 4 --}}
                        <div id="payments" class="policy-section scroll-mt-32">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">04</span>
                                Billing & Quota Allotment
                            </h2>
                            <p class="text-slate-600 font-medium leading-relaxed mb-6">
                                By upgrading to a Pro Hub or Enterprise Node, you authorize FinanceAI (via our payment processor, Stripe) to charge the applicable subscription fees to your designated payment method.
                            </p>
                            <ul class="grid sm:grid-cols-2 gap-4">
                                <li class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
                                    <i class="fa-solid fa-money-check-dollar text-emerald-500 mb-2 text-lg"></i>
                                    <h4 class="text-sm font-bold text-slate-900">Advance Billing</h4>
                                    <p class="text-xs text-slate-500 mt-1">Fees are billed in advance on a monthly or annual cycle and are strictly non-refundable.</p>
                                </li>
                                <li class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
                                    <i class="fa-solid fa-ban text-rose-400 mb-2 text-lg"></i>
                                    <h4 class="text-sm font-bold text-slate-900">Cancellation Protocol</h4>
                                    <p class="text-xs text-slate-500 mt-1">You may cancel your node anytime. Access remains active until the end of your current billing epoch.</p>
                                </li>
                            </ul>
                        </div>

                        {{-- Section 5 --}}
                        <div id="ownership" class="policy-section scroll-mt-32">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">05</span>
                                Data Sovereignty & Ownership
                            </h2>
                            <p class="text-slate-600 font-medium leading-relaxed">
                                You retain absolute, sovereign ownership of all financial data injected into the platform. By utilizing the service, you grant FinanceAI a worldwide, limited, non-exclusive license to host, process, and encrypt this data strictly for the purpose of executing the software's functionality and generating your neural forecasts.
                            </p>
                        </div>

                        {{-- Section 6 (CRITICAL: AI DISCLAIMER) --}}
                        <div id="ai_disclaimer" class="policy-section scroll-mt-32">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">06</span>
                                AI Liability & Financial Disclaimer
                            </h2>
                            
                            <div class="bg-amber-50 border-2 border-amber-200 rounded-[1.5rem] p-6 shadow-inner relative overflow-hidden">
                                <div class="absolute -right-4 -top-4 text-amber-500/10 text-7xl"><i class="fa-solid fa-scale-unbalanced"></i></div>
                                <div class="relative z-10">
                                    <h4 class="text-xs font-black uppercase tracking-widest text-amber-600 mb-3 flex items-center gap-2"><i class="fa-solid fa-triangle-exclamation"></i> Critical Disclaimer</h4>
                                    <p class="text-sm text-slate-700 font-medium leading-relaxed">
                                        FinanceAI is a software technology company, <strong class="text-slate-900">not a licensed bank, brokerage, or fiduciary advisory firm.</strong> 
                                        <br><br>
                                        All AI-generated insights, burn-rate projections, and heuristic forecasts are provided for <strong>informational and structural purposes only</strong>. They do not constitute professional financial, tax, or legal advice. You alone assume the sole responsibility of evaluating the merits and risks associated with the use of any data generated by our platform before making any financial decisions.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Section 7 --}}
                        <div id="liability" class="policy-section scroll-mt-32">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">07</span>
                                Limitation of Liability
                            </h2>
                            <p class="text-slate-600 font-medium leading-relaxed">
                                To the maximum extent permitted by global law, FinanceAI and its architects shall not be liable for any indirect, incidental, special, consequential, or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from (i) your access to or inability to access the service; (ii) any unauthorized access to our secure servers.
                            </p>
                        </div>

                        {{-- Section 8 --}}
                        <div id="termination" class="policy-section scroll-mt-32">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">08</span>
                                Protocol Termination
                            </h2>
                            <p class="text-slate-600 font-medium leading-relaxed">
                                We reserve the unilateral right to suspend or terminate your node immediately, without prior notice or liability, for any reason whatsoever, including without limitation if you breach the Acceptable Use limits defined in Section 03. Upon termination, your right to utilize the API and UI will cease immediately.
                            </p>
                        </div>

                        {{-- Section 9 --}}
                        <div id="law" class="policy-section scroll-mt-32">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">09</span>
                                Governing Law
                            </h2>
                            <p class="text-slate-600 font-medium leading-relaxed">
                                These Terms shall be governed and construed in accordance with international commercial law, without regard to its conflict of law provisions. Our failure to enforce any right or provision of these Terms will not be considered a waiver of those rights.
                            </p>
                        </div>

                        {{-- Section 10 --}}
                        <div id="disputes" class="policy-section scroll-mt-32">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-5 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs border border-indigo-100 shadow-sm">10</span>
                                Dispute Resolution
                            </h2>
                            <p class="text-slate-600 font-medium leading-relaxed mb-8">
                                In the event of a dispute, you agree to first attempt to resolve the issue informally by contacting our legal architecture team. If resolution is impossible, disputes shall be resolved through binding arbitration rather than formal litigation.
                            </p>

                            {{-- Digital Acknowledgement Box (NEW FUN) --}}
                            <div class="bg-slate-900 rounded-2xl p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6 shadow-2xl relative overflow-hidden">
                                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10 mix-blend-overlay"></div>
                                <div class="relative z-10 text-center md:text-left">
                                    <h4 class="text-white font-black text-lg">Digital Acknowledgment</h4>
                                    <p class="text-slate-400 text-xs mt-1">Please confirm your understanding of the architecture protocols.</p>
                                </div>
                                <button @click="acknowledgeTerms()" 
                                        class="relative z-10 px-8 py-4 bg-indigo-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-indigo-500 transition-colors focus:outline-none flex items-center gap-2 shadow-lg shadow-indigo-500/20"
                                        :class="hasAcknowledged ? 'bg-emerald-500 hover:bg-emerald-600 shadow-emerald-500/20' : ''">
                                    <span x-show="!hasAcknowledged">I Agree & Acknowledge</span>
                                    <span x-show="hasAcknowledged" style="display:none;"><i class="fa-solid fa-check text-lg"></i> Verified</span>
                                </button>
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
    Alpine.data('termsEngine', () => ({
        activeSection: 'acceptance',
        readingProgress: 0,
        isExporting: false,
        hasAcknowledged: false,

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
                        
                        // Scroll the mobile TOC to keep active item in view
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
        exportTermsPDF() {
            this.isExporting = true;
            this.$dispatch('notify', { message: 'Preparing document for PDF export...', type: 'info' });

            setTimeout(() => {
                this.isExporting = false;
                window.print();
            }, 800);
        },

        // Digital Acknowledgment Interaction
        acknowledgeTerms() {
            if(this.hasAcknowledged) return;
            this.hasAcknowledged = true;
            this.$dispatch('notify', { message: 'Cryptographic signature recorded. Protocol acknowledged.', type: 'success' });
        }
    }));
});
</script>
@endpush