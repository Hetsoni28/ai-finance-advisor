@extends('layouts.landing')

@section('title', 'Pricing & Quotas | FinanceAI Enterprise')
@section('meta_description', 'Transparent, scalable pricing for financial intelligence. Choose the right node for your organization.')

@section('content')

<div class="bg-[#f8fafc] font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden" 
     x-data="{ annual: true, currency: '₹' }">

    {{-- Holographic Ambient Backgrounds --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-20%] left-[10%] w-[800px] h-[800px] bg-indigo-500/10 blur-[120px] rounded-full transition-colors duration-1000"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[600px] h-[600px] bg-emerald-500/5 blur-[100px] rounded-full"></div>
    </div>

    {{-- ================= 1. HERO & TOGGLE ================= --}}
    <section class="relative pt-40 pb-20 lg:pt-48 lg:pb-24 overflow-hidden z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-600 text-[10px] font-black uppercase tracking-widest mb-8 shadow-sm reveal-up">
                <i class="fa-solid fa-tags"></i> Transparent Licensing
            </div>

            <h1 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tight leading-[1.1] max-w-4xl mx-auto reveal-up" style="transition-delay: 100ms;">
                Invest in absolute <br class="hidden md:block">
                <span class="bg-gradient-to-r from-indigo-600 to-sky-500 bg-clip-text text-transparent">financial clarity.</span>
            </h1>

            <p class="mt-8 text-lg text-slate-500 font-medium leading-relaxed max-w-2xl mx-auto reveal-up" style="transition-delay: 200ms;">
                Deploy the FinanceAI engine for your household or enterprise. Scalable infrastructure designed to generate ROI within the first 30 days.
            </p>

            {{-- Billing Toggle (Alpine.js Powered) --}}
            <div class="mt-14 flex flex-col items-center justify-center gap-4 reveal-up" style="transition-delay: 300ms;">
                <div class="flex items-center gap-4 bg-white p-2 rounded-full border border-slate-200 shadow-sm relative">
                    <button @click="annual = false" class="relative z-10 px-6 py-2.5 rounded-full text-sm font-black uppercase tracking-widest transition-colors focus:outline-none" :class="!annual ? 'text-slate-900' : 'text-slate-400 hover:text-slate-600'">
                        Monthly
                    </button>
                    <button @click="annual = true" class="relative z-10 px-6 py-2.5 rounded-full text-sm font-black uppercase tracking-widest transition-colors focus:outline-none" :class="annual ? 'text-slate-900' : 'text-slate-400 hover:text-slate-600'">
                        Annually
                    </button>
                    {{-- Sliding Pill --}}
                    <div class="absolute top-2 bottom-2 w-[calc(50%-8px)] bg-slate-100 rounded-full transition-transform duration-300 ease-out border border-slate-200 shadow-inner" :class="annual ? 'translate-x-[calc(100%+8px)]' : 'translate-x-0'"></div>
                </div>
                <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-emerald-600 bg-emerald-50 border border-emerald-100 px-3 py-1 rounded-full animate-pulse">
                    <i class="fa-solid fa-gift"></i> Save 20% on Annual Deployment
                </div>
            </div>

        </div>
    </section>

    {{-- ================= 2. PRICING TIERS (THE BENTO CARDS) ================= --}}
    <section class="py-12 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8 items-center max-w-6xl mx-auto">

                {{-- Tier 1: Standard Node --}}
                <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm hover:shadow-[0_10px_40px_rgba(0,0,0,0.04)] hover:-translate-y-1 transition-all duration-300 p-10 flex flex-col h-full reveal-up relative overflow-hidden group">
                    <div class="absolute -left-10 -top-10 w-40 h-40 bg-slate-100 rounded-full blur-3xl pointer-events-none transition-transform group-hover:scale-150 duration-700"></div>
                    
                    <div class="relative z-10">
                        <div class="w-12 h-12 bg-slate-50 text-slate-500 rounded-2xl flex items-center justify-center border border-slate-200 mb-6"><i class="fa-solid fa-user text-xl"></i></div>
                        <h3 class="text-2xl font-black text-slate-900 mb-2">Standard Node</h3>
                        <p class="text-sm text-slate-500 font-medium h-10">For individuals requiring basic cryptographic tracking.</p>
                        
                        <div class="my-8 flex items-end gap-1">
                            <span class="text-5xl font-black text-slate-900 tracking-tight" x-text="annual ? '₹1,999' : '₹2,499'"></span>
                            <span class="text-sm font-bold text-slate-400 mb-1">/mo</span>
                        </div>

                        <a href="{{ route('register') ?? '#' }}" class="w-full block text-center px-6 py-4 bg-slate-50 border border-slate-200 text-slate-700 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-100 transition-colors focus:outline-none">
                            Deploy Node
                        </a>
                    </div>

                    <div class="mt-8 space-y-4 flex-1 relative z-10">
                        <div class="flex items-start gap-3"><i class="fa-solid fa-check text-emerald-500 mt-1 text-sm"></i> <span class="text-sm font-bold text-slate-600">1 Secure Workspace</span></div>
                        <div class="flex items-start gap-3"><i class="fa-solid fa-check text-emerald-500 mt-1 text-sm"></i> <span class="text-sm font-bold text-slate-600">Basic Manual Ledgers</span></div>
                        <div class="flex items-start gap-3"><i class="fa-solid fa-check text-emerald-500 mt-1 text-sm"></i> <span class="text-sm font-bold text-slate-600">30-Day Telemetry History</span></div>
                        <div class="flex items-start gap-3 opacity-40"><i class="fa-solid fa-xmark text-slate-400 mt-1 text-sm"></i> <span class="text-sm font-bold text-slate-500">No AI Neural Forecasting</span></div>
                        <div class="flex items-start gap-3 opacity-40"><i class="fa-solid fa-xmark text-slate-400 mt-1 text-sm"></i> <span class="text-sm font-bold text-slate-500">No Multi-User IAM</span></div>
                    </div>
                </div>

                {{-- Tier 2: Professional Hub (HIGHLIGHTED DECOY) --}}
                <div class="bg-white rounded-[2.5rem] border-2 border-indigo-500 shadow-[0_20px_60px_-15px_rgba(79,70,229,0.2)] hover:shadow-[0_25px_60px_-15px_rgba(79,70,229,0.3)] hover:-translate-y-2 transition-all duration-300 p-10 flex flex-col h-full transform md:scale-105 relative overflow-hidden group reveal-up z-20" style="transition-delay: 100ms;">
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none transition-transform group-hover:scale-150 duration-700"></div>
                    
                    <div class="absolute top-0 inset-x-0 flex justify-center transform -translate-y-1/2">
                        <span class="bg-indigo-600 text-white px-4 py-1 rounded-full text-[9px] font-black uppercase tracking-widest shadow-md">Most Deployed</span>
                    </div>

                    <div class="relative z-10 mt-2">
                        <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center border border-indigo-100 shadow-sm mb-6"><i class="fa-solid fa-network-wired text-xl"></i></div>
                        <h3 class="text-2xl font-black text-slate-900 mb-2">Professional Hub</h3>
                        <p class="text-sm text-slate-500 font-medium h-10">Advanced automation and AI tools for families and small teams.</p>
                        
                        <div class="my-8 flex items-end gap-1">
                            <span class="text-5xl font-black text-indigo-600 tracking-tight" x-text="annual ? '₹4,999' : '₹5,999'"></span>
                            <span class="text-sm font-bold text-slate-400 mb-1">/mo</span>
                        </div>

                        <a href="{{ route('register') ?? '#' }}" class="w-full block text-center px-6 py-4 bg-slate-900 text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-[0_10px_20px_rgba(0,0,0,0.1)] hover:bg-indigo-600 transition-colors focus:outline-none group-hover:shadow-indigo-500/30">
                            Initialize Hub
                        </a>
                    </div>

                    <div class="mt-8 space-y-4 flex-1 relative z-10">
                        <div class="flex items-start gap-3"><i class="fa-solid fa-check text-indigo-500 mt-1 text-sm"></i> <span class="text-sm font-bold text-slate-900">3 Secure Workspaces</span></div>
                        <div class="flex items-start gap-3"><i class="fa-solid fa-check text-indigo-500 mt-1 text-sm"></i> <span class="text-sm font-bold text-slate-900">AI Neural Categorization</span></div>
                        <div class="flex items-start gap-3"><i class="fa-solid fa-check text-indigo-500 mt-1 text-sm"></i> <span class="text-sm font-bold text-slate-900">Predictive Burn-Rate Forecasting</span></div>
                        <div class="flex items-start gap-3"><i class="fa-solid fa-check text-indigo-500 mt-1 text-sm"></i> <span class="text-sm font-bold text-slate-900">Role-Based Access (IAM)</span></div>
                        <div class="flex items-start gap-3"><i class="fa-solid fa-check text-indigo-500 mt-1 text-sm"></i> <span class="text-sm font-bold text-slate-900">Universal PDF Export</span></div>
                    </div>
                </div>

                {{-- Tier 3: Enterprise Scale --}}
                <div class="bg-slate-900 rounded-[2.5rem] border border-slate-800 shadow-xl hover:shadow-[0_20px_50px_rgba(0,0,0,0.2)] hover:-translate-y-1 transition-all duration-300 p-10 flex flex-col h-full reveal-up relative overflow-hidden group" style="transition-delay: 200ms;">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-[0.03] mix-blend-overlay pointer-events-none"></div>
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-sky-500/20 rounded-full blur-3xl pointer-events-none transition-transform group-hover:scale-150 duration-700"></div>
                    
                    <div class="relative z-10">
                        <div class="w-12 h-12 bg-white/10 text-sky-400 rounded-2xl flex items-center justify-center border border-white/20 backdrop-blur-md mb-6"><i class="fa-solid fa-server text-xl"></i></div>
                        <h3 class="text-2xl font-black text-white mb-2">Enterprise Scale</h3>
                        <p class="text-sm text-slate-400 font-medium h-10">Dedicated infrastructure and API access for massive throughput.</p>
                        
                        <div class="my-8 flex items-end gap-1">
                            <span class="text-5xl font-black text-white tracking-tight">Custom</span>
                        </div>

                        <a href="{{ route('contact') ?? '#' }}" class="w-full block text-center px-6 py-4 bg-white/10 border border-white/20 text-white backdrop-blur-md rounded-xl font-black text-xs uppercase tracking-widest hover:bg-white hover:text-slate-900 transition-colors focus:outline-none">
                            Contact Sales
                        </a>
                    </div>

                    <div class="mt-8 space-y-4 flex-1 relative z-10">
                        <div class="flex items-start gap-3"><i class="fa-solid fa-check text-sky-400 mt-1 text-sm"></i> <span class="text-sm font-bold text-slate-300">Unlimited Workspaces & Nodes</span></div>
                        <div class="flex items-start gap-3"><i class="fa-solid fa-check text-sky-400 mt-1 text-sm"></i> <span class="text-sm font-bold text-slate-300">Full REST API Access</span></div>
                        <div class="flex items-start gap-3"><i class="fa-solid fa-check text-sky-400 mt-1 text-sm"></i> <span class="text-sm font-bold text-slate-300">Custom Bank integrations</span></div>
                        <div class="flex items-start gap-3"><i class="fa-solid fa-check text-sky-400 mt-1 text-sm"></i> <span class="text-sm font-bold text-slate-300">Dedicated Success Architect</span></div>
                        <div class="flex items-start gap-3"><i class="fa-solid fa-check text-sky-400 mt-1 text-sm"></i> <span class="text-sm font-bold text-slate-300">SLA 99.99% Uptime</span></div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ================= 3. FEATURE COMPARISON MATRIX ================= --}}
    <section class="py-24 bg-white relative z-10 border-y border-slate-200/60 hidden md:block">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center mb-16 reveal-up">
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">Comprehensive Matrix</h2>
                <p class="text-slate-500 font-medium mt-2">A technical breakdown of node capabilities.</p>
            </div>

            <div class="overflow-x-auto reveal-up" style="transition-delay: 100ms;">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="w-1/3 pb-6 text-xs font-black uppercase tracking-widest text-slate-400 border-b border-slate-200">System Capability</th>
                            <th class="w-2/9 pb-6 text-center text-xs font-black uppercase tracking-widest text-slate-900 border-b border-slate-200">Standard Node</th>
                            <th class="w-2/9 pb-6 text-center text-xs font-black uppercase tracking-widest text-indigo-600 border-b border-indigo-200 bg-indigo-50/50 rounded-t-xl">Pro Hub</th>
                            <th class="w-2/9 pb-6 text-center text-xs font-black uppercase tracking-widest text-slate-900 border-b border-slate-200">Enterprise</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        
                        {{-- Group 1 --}}
                        <tr><td colspan="4" class="pt-8 pb-4 text-[10px] font-black uppercase tracking-widest text-slate-400 bg-white">Core Cryptography</td></tr>
                        
                        <tr class="group hover:bg-slate-50 transition-colors">
                            <td class="py-4 border-b border-slate-100 font-bold text-slate-700">AES-256 Encryption</td>
                            <td class="py-4 border-b border-slate-100 text-center"><i class="fa-solid fa-check text-emerald-500"></i></td>
                            <td class="py-4 border-b border-slate-100 text-center bg-indigo-50/30 group-hover:bg-indigo-50/80 transition-colors"><i class="fa-solid fa-check text-indigo-600"></i></td>
                            <td class="py-4 border-b border-slate-100 text-center"><i class="fa-solid fa-check text-slate-900"></i></td>
                        </tr>
                        <tr class="group hover:bg-slate-50 transition-colors">
                            <td class="py-4 border-b border-slate-100 font-bold text-slate-700">Live Telemetry Sync</td>
                            <td class="py-4 border-b border-slate-100 text-center"><i class="fa-solid fa-check text-emerald-500"></i></td>
                            <td class="py-4 border-b border-slate-100 text-center bg-indigo-50/30 group-hover:bg-indigo-50/80 transition-colors"><i class="fa-solid fa-check text-indigo-600"></i></td>
                            <td class="py-4 border-b border-slate-100 text-center"><i class="fa-solid fa-check text-slate-900"></i></td>
                        </tr>
                        <tr class="group hover:bg-slate-50 transition-colors">
                            <td class="py-4 border-b border-slate-100 font-bold text-slate-700">Data Retention</td>
                            <td class="py-4 border-b border-slate-100 text-center font-bold text-slate-500">30 Days</td>
                            <td class="py-4 border-b border-slate-100 text-center font-bold text-indigo-600 bg-indigo-50/30 group-hover:bg-indigo-50/80 transition-colors">5 Years</td>
                            <td class="py-4 border-b border-slate-100 text-center font-bold text-slate-900">Infinite</td>
                        </tr>

                        {{-- Group 2 --}}
                        <tr><td colspan="4" class="pt-8 pb-4 text-[10px] font-black uppercase tracking-widest text-slate-400 bg-white">Artificial Intelligence</td></tr>
                        
                        <tr class="group hover:bg-slate-50 transition-colors">
                            <td class="py-4 border-b border-slate-100 font-bold text-slate-700">Auto-Categorization</td>
                            <td class="py-4 border-b border-slate-100 text-center"><i class="fa-solid fa-minus text-slate-300"></i></td>
                            <td class="py-4 border-b border-slate-100 text-center bg-indigo-50/30 group-hover:bg-indigo-50/80 transition-colors"><i class="fa-solid fa-check text-indigo-600"></i></td>
                            <td class="py-4 border-b border-slate-100 text-center"><i class="fa-solid fa-check text-slate-900"></i></td>
                        </tr>
                        <tr class="group hover:bg-slate-50 transition-colors">
                            <td class="py-4 border-b border-slate-100 font-bold text-slate-700">Burn-Rate Heuristics</td>
                            <td class="py-4 border-b border-slate-100 text-center"><i class="fa-solid fa-minus text-slate-300"></i></td>
                            <td class="py-4 border-b border-slate-100 text-center bg-indigo-50/30 group-hover:bg-indigo-50/80 transition-colors"><i class="fa-solid fa-check text-indigo-600"></i></td>
                            <td class="py-4 border-b border-slate-100 text-center"><i class="fa-solid fa-check text-slate-900"></i></td>
                        </tr>
                        <tr class="group hover:bg-slate-50 transition-colors">
                            <td class="py-4 border-b border-slate-100 font-bold text-slate-700">Predictive Alerts</td>
                            <td class="py-4 border-b border-slate-100 text-center"><i class="fa-solid fa-minus text-slate-300"></i></td>
                            <td class="py-4 border-b border-slate-100 text-center bg-indigo-50/30 group-hover:bg-indigo-50/80 transition-colors"><i class="fa-solid fa-minus text-indigo-300"></i></td>
                            <td class="py-4 border-b border-slate-100 text-center"><i class="fa-solid fa-check text-slate-900"></i></td>
                        </tr>

                        {{-- Group 3 --}}
                        <tr><td colspan="4" class="pt-8 pb-4 text-[10px] font-black uppercase tracking-widest text-slate-400 bg-white">Infrastructure</td></tr>
                        
                        <tr class="group hover:bg-slate-50 transition-colors">
                            <td class="py-4 border-b border-slate-100 font-bold text-slate-700">REST API Access</td>
                            <td class="py-4 border-b border-slate-100 text-center"><i class="fa-solid fa-minus text-slate-300"></i></td>
                            <td class="py-4 border-b border-slate-100 text-center bg-indigo-50/30 group-hover:bg-indigo-50/80 transition-colors"><i class="fa-solid fa-minus text-indigo-300"></i></td>
                            <td class="py-4 border-b border-slate-100 text-center"><i class="fa-solid fa-check text-slate-900"></i></td>
                        </tr>
                        <tr class="group hover:bg-slate-50 transition-colors">
                            <td class="py-4 border-b border-slate-100 font-bold text-slate-700">Dedicated Node Priority</td>
                            <td class="py-4 border-b border-slate-100 text-center"><i class="fa-solid fa-minus text-slate-300"></i></td>
                            <td class="py-4 border-b border-slate-100 text-center bg-indigo-50/30 group-hover:bg-indigo-50/80 transition-colors rounded-b-xl"><i class="fa-solid fa-minus text-indigo-300"></i></td>
                            <td class="py-4 border-b border-slate-100 text-center"><i class="fa-solid fa-check text-slate-900"></i></td>
                        </tr>

                    </tbody>
                </table>
            </div>

        </div>
    </section>

    {{-- ================= 4. ACCORDION FAQ ================= --}}
    <section class="py-32 bg-[#f8fafc] relative z-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center mb-16 reveal-up">
                <h2 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight">System Inquiries</h2>
            </div>

            <div class="space-y-4" x-data="{ selected: null }">
                
                {{-- FAQ 1 --}}
                <div class="bg-white border border-slate-200 rounded-[1.5rem] overflow-hidden shadow-sm reveal-up">
                    <button @click="selected !== 1 ? selected = 1 : selected = null" class="w-full flex items-center justify-between p-6 text-left focus:outline-none hover:bg-slate-50 transition-colors">
                        <span class="font-black text-slate-900">How secure is the cryptographic ledger?</span>
                        <i class="fa-solid fa-chevron-down text-slate-400 transition-transform duration-300" :class="selected === 1 ? 'rotate-180 text-indigo-600' : ''"></i>
                    </button>
                    <div class="relative overflow-hidden transition-all max-h-0 duration-300" style="" x-ref="container1" x-bind:style="selected == 1 ? 'max-height: ' + $refs.container1.scrollHeight + 'px' : ''">
                        <div class="px-6 pb-6 text-sm text-slate-500 font-medium leading-relaxed border-t border-slate-100 pt-4">
                            We utilize AES-256 military-grade encryption at rest and TLS 1.3 in transit. Our Zero-Knowledge architecture means our engineers cannot read your financial telemetry. Your data remains strictly your own.
                        </div>
                    </div>
                </div>

                {{-- FAQ 2 --}}
                <div class="bg-white border border-slate-200 rounded-[1.5rem] overflow-hidden shadow-sm reveal-up" style="transition-delay: 100ms;">
                    <button @click="selected !== 2 ? selected = 2 : selected = null" class="w-full flex items-center justify-between p-6 text-left focus:outline-none hover:bg-slate-50 transition-colors">
                        <span class="font-black text-slate-900">Can I migrate from a Standard Node to a Pro Hub later?</span>
                        <i class="fa-solid fa-chevron-down text-slate-400 transition-transform duration-300" :class="selected === 2 ? 'rotate-180 text-indigo-600' : ''"></i>
                    </button>
                    <div class="relative overflow-hidden transition-all max-h-0 duration-300" style="" x-ref="container2" x-bind:style="selected == 2 ? 'max-height: ' + $refs.container2.scrollHeight + 'px' : ''">
                        <div class="px-6 pb-6 text-sm text-slate-500 font-medium leading-relaxed border-t border-slate-100 pt-4">
                            Absolutely. You can upgrade your deployment tier at any time from your Identity Profile. Prorated charges will automatically apply to your billing cycle without any data migration downtime.
                        </div>
                    </div>
                </div>

                {{-- FAQ 3 --}}
                <div class="bg-white border border-slate-200 rounded-[1.5rem] overflow-hidden shadow-sm reveal-up" style="transition-delay: 200ms;">
                    <button @click="selected !== 3 ? selected = 3 : selected = null" class="w-full flex items-center justify-between p-6 text-left focus:outline-none hover:bg-slate-50 transition-colors">
                        <span class="font-black text-slate-900">Do you offer a Developer License for APIs?</span>
                        <i class="fa-solid fa-chevron-down text-slate-400 transition-transform duration-300" :class="selected === 3 ? 'rotate-180 text-indigo-600' : ''"></i>
                    </button>
                    <div class="relative overflow-hidden transition-all max-h-0 duration-300" style="" x-ref="container3" x-bind:style="selected == 3 ? 'max-height: ' + $refs.container3.scrollHeight + 'px' : ''">
                        <div class="px-6 pb-6 text-sm text-slate-500 font-medium leading-relaxed border-t border-slate-100 pt-4">
                            REST API access is currently restricted to Enterprise deployments to guarantee node stability. However, verified students and developers building non-commercial tools can apply for a Sandbox API key by contacting our architecture team.
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
    .reveal-up {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .reveal-up.is-visible {
        opacity: 1;
        transform: translateY(0);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Re-initialize intersection observer for reveal animations specific to this page
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.reveal-up').forEach(el => observer.observe(el));
});
</script>
@endpush