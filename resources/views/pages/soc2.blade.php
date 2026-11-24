@extends('layouts.landing')
@include('partials.site-type')

@section('title', 'SOC 2 Compliance | FinanceAI Enterprise')
@section('meta_description', 'FinanceAI\'s SOC 2 Type II compliance framework. Learn about our security controls, audit reports, and trust service criteria.')

@section('content')
@php
    $criteria = [
        ['icon' => 'fa-shield-halved', 'title' => 'Security', 'status' => 'Verified', 'desc' => 'Protection against unauthorized access through multi-layered security controls, WAFs, IDS, and role-based access.'],
        ['icon' => 'fa-server', 'title' => 'Availability', 'status' => 'Verified', 'desc' => '99.99% uptime SLA with multi-AZ deployments, automated failover, and incident response under 15 minutes.'],
        ['icon' => 'fa-microchip', 'title' => 'Processing Integrity', 'status' => 'Verified', 'desc' => 'All financial computations are deterministic. Data inputs, transformations, and outputs are verifiable and auditable.'],
        ['icon' => 'fa-lock', 'title' => 'Confidentiality', 'status' => 'Verified', 'desc' => 'Financial telemetry is classified as confidential. Data is encrypted everywhere and accessible only via authenticated IAM roles.'],
        ['icon' => 'fa-user-shield', 'title' => 'Privacy', 'status' => 'Verified', 'desc' => 'GDPR and CCPA compliant. Data subjects retain full rights over their data. Zero-knowledge architecture with no data selling.'],
    ];
    $posture = [
        ['icon' => 'fa-lock', 'label' => 'Encryption Status', 'value' => 'AES-256 Active'],
        ['icon' => 'fa-server', 'label' => 'Infrastructure Uptime', 'value' => '99.997%'],
        ['icon' => 'fa-shield-halved', 'label' => 'Firewall (WAF)', 'value' => 'All Rules Active'],
        ['icon' => 'fa-eye', 'label' => 'Intrusion Detection', 'value' => '0 Threats'],
        ['icon' => 'fa-code-commit', 'label' => 'Last SOC 2 Audit', 'value' => 'March 2026'],
        ['icon' => 'fa-certificate', 'label' => 'SSL Certificate', 'value' => 'Grade A+'],
    ];
@endphp
<div class="paper">

    <x-site.hero eyebrow="Compliance Verified" lead="SOC 2" accent="Type II" tail="Compliance"
        lede="FinanceAI undergoes annual third-party audits to verify our security posture meets the highest industry standards. Our SOC 2 Type II report examines operational effectiveness over a continuous period.">
        <x-slot name="meta">
            <ul class="flex flex-wrap gap-x-8 gap-y-3">
                @foreach([['fa-shield-halved', 'SOC 2 Type II'], ['fa-lock', 'AES-256'], ['fa-globe', 'GDPR Ready'], ['fa-network-wired', 'TLS 1.3']] as [$icon, $label])
                    <li class="flex items-center gap-2 font-mono text-xs uppercase tracking-wider text-slate-300">
                        <i class="fa-solid {{ $icon }} text-indigo-300" aria-hidden="true"></i> {{ $label }}
                    </li>
                @endforeach
            </ul>
        </x-slot>
    </x-site.hero>

    {{-- Trust service criteria --}}
    <section class="py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-site.section-head label="01 — Criteria" title="Trust Service" accent="Criteria"
                lede="Our SOC 2 audit evaluates controls across all five AICPA Trust Service Criteria principles." />

            <ol class="mt-14 border-t border-slate-200">
                @foreach($criteria as $i => $c)
                    <li class="grid grid-cols-12 gap-4 sm:gap-8 py-7 border-b border-slate-200" data-reveal style="--d: {{ $i * 60 }}ms">
                        <span class="col-span-2 sm:col-span-1 font-mono text-sm text-slate-400 pt-1">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="col-span-10 sm:col-span-4 text-xl sm:text-2xl font-black tracking-tight text-slate-900 flex items-center gap-3">
                            <i class="fa-solid {{ $c['icon'] }} text-base text-slate-400" aria-hidden="true"></i>{{ $c['title'] }}
                        </h3>
                        <p class="col-span-12 sm:col-span-5 sm:col-start-6 text-slate-600 leading-relaxed">{{ $c['desc'] }}</p>
                        <span class="col-span-12 sm:col-span-2 sm:text-right font-mono text-[11px] uppercase tracking-wider text-emerald-700 pt-1.5">
                            <i class="fa-solid fa-check mr-1" aria-hidden="true"></i>{{ $c['status'] }}
                        </span>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Security posture --}}
    <section class="py-20 sm:py-28 bg-[#0b0b1a] text-white grain">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-site.section-head dark label="02 — Posture" title="Live Security" accent="Posture"
                lede="Real-time compliance and security diagnostics across the FinanceAI architecture." />

            <dl class="mt-14 grid sm:grid-cols-2 lg:grid-cols-3 border-t border-l border-white/10">
                @foreach($posture as $i => $item)
                    <div class="border-b border-r border-white/10 p-7" data-reveal style="--d: {{ $i * 60 }}ms">
                        <dt class="flex items-center justify-between">
                            <span class="font-mono text-[11px] uppercase tracking-wider text-slate-400">{{ $item['label'] }}</span>
                            <span class="flex items-center gap-1.5 font-mono text-[10px] uppercase tracking-wider text-emerald-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400" aria-hidden="true"></span> Nominal
                            </span>
                        </dt>
                        <dd class="mt-6 flex items-end justify-between gap-3">
                            <span class="text-2xl font-black tracking-tight">{{ $item['value'] }}</span>
                            <i class="fa-solid {{ $item['icon'] }} text-slate-600" aria-hidden="true"></i>
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- Report request --}}
    <section class="py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-12 gap-10 items-end border-t border-slate-200 pt-12" data-reveal>
                <div class="lg:col-span-8">
                    <p class="eyebrow text-indigo-600">03 — Audit report</p>
                    <h2 class="mt-4 text-3xl sm:text-5xl font-black tracking-[-0.03em] leading-[1.05] text-slate-900">Request a <span class="font-serif-i text-indigo-600">SOC 2</span> Report</h2>
                    <p class="mt-5 text-lg text-slate-600 leading-relaxed max-w-2xl">Our full SOC 2 Type II audit report is available upon request to existing customers and verified enterprise prospects under NDA.</p>
                </div>
                <div class="lg:col-span-4 lg:text-right">
                    <a href="{{ route('contact') }}" class="group inline-flex items-center gap-2 h-12 px-6 rounded-xl bg-slate-900 text-white font-bold hover:bg-indigo-600 transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-200">
                        Contact Architecture <i class="fa-solid fa-arrow-right text-sm transition-transform group-hover:translate-x-0.5" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
