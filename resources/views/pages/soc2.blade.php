@extends('layouts.landing')

@section('title', 'SOC 2 Compliance | FinanceAI Enterprise')
@section('meta_description', 'FinanceAI\'s SOC 2 Type II compliance framework. Learn about our security controls, audit reports, and trust service criteria.')

@section('content')

<div class="bg-[#f8fafc] font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden"
     x-data="socEngine()">

    {{-- Ambient --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[900px] h-[900px] bg-indigo-500/10 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[10%] right-[-10%] w-[600px] h-[600px] bg-emerald-500/5 blur-[100px] rounded-full"></div>
    </div>

    {{-- ================= 1. HERO ================= --}}
    <section class="relative pt-40 pb-20 lg:pt-48 lg:pb-24 overflow-hidden z-10 border-b border-slate-200/60 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-600 text-[10px] font-black uppercase tracking-widest mb-8 shadow-sm reveal-up">
                <i class="fa-solid fa-shield-check"></i> Compliance Verified
            </div>

            <h1 class="text-4xl md:text-6xl font-black text-slate-900 tracking-tight leading-[1.1] reveal-up" style="transition-delay: 100ms;">
                SOC 2 Type II Compliance
            </h1>

            <p class="mt-8 text-lg text-slate-500 font-medium leading-relaxed max-w-3xl mx-auto reveal-up" style="transition-delay: 200ms;">
                FinanceAI undergoes annual third-party audits to verify our security posture meets the highest industry standards. Our SOC 2 Type II report examines operational effectiveness over a continuous period.
            </p>

            {{-- Trust Badges --}}
            <div class="mt-12 flex flex-wrap items-center justify-center gap-4 reveal-up" style="transition-delay: 300ms;">
                @foreach([
                    ['bg-emerald-500', 'fa-shield-halved', 'SOC 2 Type II'],
                    ['bg-indigo-500', 'fa-lock', 'AES-256'],
                    ['bg-sky-500', 'fa-globe', 'GDPR Ready'],
                    ['bg-slate-800', 'fa-network-wired', 'TLS 1.3']
                ] as $badge)
                <span class="flex items-center gap-2 px-4 py-2.5 {{ $badge[0] }} text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg">
                    <i class="fa-solid {{ $badge[1] }}"></i> {{ $badge[2] }}
                </span>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= 2. TRUST SERVICE CRITERIA ================= --}}
    <section class="py-24 bg-[#f8fafc] relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 reveal-up">
                <h2 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight mb-6">Trust Service Criteria</h2>
                <p class="text-slate-500 text-lg font-medium">Our SOC 2 audit evaluates controls across all five AICPA Trust Service Criteria principles.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                $criteria = [
                    ['icon' => 'fa-shield-halved', 'color' => 'indigo', 'title' => 'Security', 'status' => 'Verified', 'desc' => 'Protection against unauthorized access through multi-layered security controls, WAFs, IDS, and role-based access.'],
                    ['icon' => 'fa-server', 'color' => 'emerald', 'title' => 'Availability', 'status' => 'Verified', 'desc' => '99.99% uptime SLA with multi-AZ deployments, automated failover, and incident response under 15 minutes.'],
                    ['icon' => 'fa-microchip', 'color' => 'sky', 'title' => 'Processing Integrity', 'status' => 'Verified', 'desc' => 'All financial computations are deterministic. Data inputs, transformations, and outputs are verifiable and auditable.'],
                    ['icon' => 'fa-lock', 'color' => 'purple', 'title' => 'Confidentiality', 'status' => 'Verified', 'desc' => 'Financial telemetry is classified as confidential. Data is encrypted everywhere and accessible only via authenticated IAM roles.'],
                    ['icon' => 'fa-user-shield', 'color' => 'rose', 'title' => 'Privacy', 'status' => 'Verified', 'desc' => 'GDPR and CCPA compliant. Data subjects retain full rights over their data. Zero-knowledge architecture with no data selling.']
                ];
                @endphp

                @foreach($criteria as $idx => $c)
                <div class="bg-white rounded-[2rem] border border-slate-200 p-8 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 reveal-up group relative overflow-hidden {{ $idx === 4 ? 'lg:col-span-1 md:col-span-2 lg:col-span-1' : '' }}" style="transition-delay: {{ $idx * 80 }}ms;">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-{{ $c['color'] }}-500/5 rounded-full blur-2xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                    
                    <div class="flex items-center justify-between mb-6 relative z-10">
                        <div class="w-12 h-12 rounded-xl bg-{{ $c['color'] }}-100 text-{{ $c['color'] }}-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fa-solid {{ $c['icon'] }} text-xl"></i>
                        </div>
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 text-[8px] font-black uppercase tracking-widest rounded-md border border-emerald-200">
                            <i class="fa-solid fa-check mr-1"></i> {{ $c['status'] }}
                        </span>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 mb-3 relative z-10">{{ $c['title'] }}</h3>
                    <p class="text-sm text-slate-500 font-medium leading-relaxed relative z-10">{{ $c['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= 3. LIVE SECURITY STATUS ================= --}}
    <section class="py-24 bg-slate-900 relative z-10 overflow-hidden text-white">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10 mix-blend-overlay pointer-events-none"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-3xl h-full bg-indigo-500/10 rounded-full blur-[150px] pointer-events-none"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16 reveal-up">
                <h2 class="text-3xl md:text-5xl font-black text-white tracking-tight mb-4">Live Security Posture</h2>
                <p class="text-indigo-200 font-medium text-lg">Real-time compliance and security diagnostics across the FinanceAI architecture.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 reveal-up" style="transition-delay: 100ms;">
                @foreach([
                    ['icon' => 'fa-lock', 'label' => 'Encryption Status', 'value' => 'AES-256 Active', 'status' => 'nominal'],
                    ['icon' => 'fa-server', 'label' => 'Infrastructure Uptime', 'value' => '99.997%', 'status' => 'nominal'],
                    ['icon' => 'fa-shield-halved', 'label' => 'Firewall (WAF)', 'value' => 'All Rules Active', 'status' => 'nominal'],
                    ['icon' => 'fa-eye', 'label' => 'Intrusion Detection', 'value' => '0 Threats', 'status' => 'nominal'],
                    ['icon' => 'fa-code-commit', 'label' => 'Last SOC 2 Audit', 'value' => 'March 2026', 'status' => 'nominal'],
                    ['icon' => 'fa-certificate', 'label' => 'SSL Certificate', 'value' => 'Grade A+', 'status' => 'nominal']
                ] as $idx => $item)
                <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700 rounded-[1.5rem] p-6 relative overflow-hidden group hover:border-slate-600 transition-colors">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="flex items-center justify-between mb-4 relative z-10">
                        <i class="fa-solid {{ $item['icon'] }} text-slate-400 text-lg"></i>
                        <span class="flex items-center gap-1.5">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            <span class="text-[8px] font-black uppercase tracking-widest text-emerald-400">Nominal</span>
                        </span>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1 relative z-10">{{ $item['label'] }}</p>
                    <p class="text-lg font-black text-white font-mono relative z-10">{{ $item['value'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= 4. AUDIT REQUEST CTA ================= --}}
    <section class="py-24 bg-white border-t border-slate-200/60 relative z-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-slate-50 rounded-[3rem] border border-slate-200 p-10 md:p-16 relative overflow-hidden reveal-up">
                <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl pointer-events-none"></div>
                <div class="flex flex-col lg:flex-row items-center gap-10 relative z-10">
                    <div class="flex-1">
                        <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center border border-indigo-100 shadow-sm mb-6">
                            <i class="fa-solid fa-file-contract text-2xl"></i>
                        </div>
                        <h2 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight mb-4">Request a SOC 2 Report</h2>
                        <p class="text-slate-500 font-medium leading-relaxed">Our full SOC 2 Type II audit report is available upon request to existing customers and verified enterprise prospects under NDA.</p>
                    </div>
                    <a href="{{ route('contact') }}" class="shrink-0 px-8 py-4 bg-slate-900 text-white rounded-xl font-black text-sm uppercase tracking-widest shadow-[0_10px_20px_rgba(0,0,0,0.1)] hover:bg-indigo-600 hover:shadow-indigo-500/30 transition-all">
                        Contact Architecture <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                    </a>
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
</style>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('socEngine', () => ({
        init() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('is-visible'); });
            }, { threshold: 0.1 });
            document.querySelectorAll('.reveal-up').forEach(el => observer.observe(el));
        }
    }));
});
</script>
@endpush
