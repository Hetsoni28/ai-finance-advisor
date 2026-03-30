@extends('layouts.app')

@section('title', 'Careers & Hiring | FinanceAI')

@section('content')

@php
    $benefits = [
        ['icon' => 'fa-globe', 'color' => 'sky', 'title' => 'Remote First', 'desc' => 'Work from anywhere on Earth. We operate asynchronously.'],
        ['icon' => 'fa-heart-pulse', 'color' => 'rose', 'title' => 'Premium Health', 'desc' => 'Top-tier medical, dental, and vision coverage for your family.'],
        ['icon' => 'fa-chart-pie', 'color' => 'indigo', 'title' => 'Meaningful Equity', 'desc' => 'We want owners, not just employees. Generous stock options.'],
        ['icon' => 'fa-laptop-code', 'color' => 'emerald', 'title' => 'Home Office Setup', 'desc' => '₹250,000 stipend to build your perfect remote command center.']
    ];

    $jobs = [
        ['id' => 1, 'dept' => 'engineering', 'title' => 'Senior AI Researcher', 'loc' => 'Remote / Global', 'type' => 'Full-time'],
        ['id' => 2, 'dept' => 'engineering', 'title' => 'Backend Infrastructure Engineer', 'loc' => 'Remote / APAC', 'type' => 'Full-time'],
        ['id' => 3, 'dept' => 'security', 'title' => 'Cryptographic Security Analyst', 'loc' => 'Ahmedabad, IN', 'type' => 'Full-time'],
        ['id' => 4, 'dept' => 'product', 'title' => 'Principal Product Designer', 'loc' => 'Remote / EMEA', 'type' => 'Full-time'],
    ];
@endphp

<div x-data="careersEngine()" class="min-h-screen bg-[#f8fafc] pb-32 font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden flex flex-col pt-24">

    {{-- Ambient Backgrounds --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-10%] right-[-5%] w-[800px] h-[800px] bg-indigo-500/5 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[600px] h-[600px] bg-sky-500/5 rounded-full blur-[100px]"></div>
    </div>

    <div class="max-w-[1200px] mx-auto w-full px-4 sm:px-6 lg:px-8 relative z-10 space-y-24">

        {{-- Hero Section --}}
        <div class="text-center max-w-4xl mx-auto space-y-8 animate-fade-in-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white border border-slate-200 text-slate-500 text-[10px] font-black tracking-widest uppercase shadow-sm">
                <i class="fa-solid fa-rocket text-indigo-500"></i> Join the Core Team
            </span>
            <h1 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tight leading-[1.1]">
                Build the future of <br>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-sky-500">financial telemetry.</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-500 font-medium leading-relaxed max-w-2xl mx-auto">
                We are a collective of engineers, cryptographers, and designers building the world's most advanced AI-driven ledger system.
            </p>
            <button @click="document.getElementById('open-roles').scrollIntoView({behavior: 'smooth'})" @mouseenter="playHover()" class="px-8 py-4 bg-slate-900 text-white rounded-xl font-black uppercase tracking-widest text-xs shadow-lg hover:bg-indigo-600 transition-all hover:-translate-y-1 focus:outline-none">
                View Open Roles <i class="fa-solid fa-arrow-down ml-2"></i>
            </button>
        </div>

        {{-- Culture Bento Box --}}
        <div class="grid md:grid-cols-2 gap-6 animate-fade-in-up" style="animation-delay: 200ms;">
            @foreach($benefits as $b)
                <div class="bg-white/80 backdrop-blur-xl p-8 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-lg transition-all duration-300 group">
                    <div class="w-14 h-14 rounded-2xl bg-{{ $b['color'] }}-50 text-{{ $b['color'] }}-600 flex items-center justify-center border border-{{ $b['color'] }}-100 mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid {{ $b['icon'] }} text-xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 mb-2">{{ $b['title'] }}</h3>
                    <p class="text-slate-500 font-medium leading-relaxed">{{ $b['desc'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Open Roles Board --}}
        <div id="open-roles" class="pt-10 animate-fade-in-up" style="animation-delay: 400ms;">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 mb-8">
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">Open Nodes</h2>
                
                {{-- Alpine Filtering --}}
                <div class="flex bg-slate-50 border border-slate-200 p-1.5 rounded-xl shadow-inner overflow-x-auto">
                    <button @click="filter = 'all'; playClick()" @mouseenter="playHover()" :class="filter === 'all' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500 hover:text-slate-900'" class="px-5 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">All</button>
                    <button @click="filter = 'engineering'; playClick()" @mouseenter="playHover()" :class="filter === 'engineering' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500 hover:text-slate-900'" class="px-5 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">Engineering</button>
                    <button @click="filter = 'security'; playClick()" @mouseenter="playHover()" :class="filter === 'security' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500 hover:text-slate-900'" class="px-5 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">Security</button>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
                <div class="divide-y divide-slate-100">
                    @foreach($jobs as $job)
                        <a href="#" x-show="filter === 'all' || filter === '{{ $job['dept'] }}'" x-transition @mouseenter="playHover()" class="block p-8 hover:bg-slate-50 transition-colors group">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-xl font-black text-slate-900 group-hover:text-indigo-600 transition-colors tracking-tight mb-2">{{ $job['title'] }}</h3>
                                    <div class="flex items-center gap-4 text-xs font-bold text-slate-400 uppercase tracking-widest">
                                        <span class="flex items-center gap-1.5"><i class="fa-solid fa-location-dot text-slate-300"></i> {{ $job['loc'] }}</span>
                                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                        <span class="flex items-center gap-1.5"><i class="fa-solid fa-clock text-slate-300"></i> {{ $job['type'] }}</span>
                                    </div>
                                </div>
                                <div class="shrink-0 flex items-center justify-center w-12 h-12 rounded-full bg-white border border-slate-200 text-slate-400 group-hover:bg-indigo-600 group-hover:border-indigo-600 group-hover:text-white transition-all shadow-sm">
                                    <i class="fa-solid fa-arrow-right -rotate-45"></i>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
window.audioEngine = {
    ctx: null, lastHover: 0,
    init() { if(!this.ctx) { const AC = window.AudioContext || window.webkitAudioContext; if(AC) this.ctx = new AC(); } },
    playClick() {
        this.init(); if(!this.ctx) return; if(this.ctx.state === 'suspended') this.ctx.resume();
        const osc = this.ctx.createOscillator(); const gain = this.ctx.createGain();
        osc.connect(gain); gain.connect(this.ctx.destination); osc.type = 'sine'; 
        osc.frequency.setValueAtTime(800, this.ctx.currentTime); osc.frequency.exponentialRampToValueAtTime(300, this.ctx.currentTime + 0.05);
        gain.gain.setValueAtTime(0.1, this.ctx.currentTime); gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + 0.05);
        osc.start(); osc.stop(this.ctx.currentTime + 0.05);
    },
    playHover() {
        const now = Date.now(); if(now - this.lastHover < 50) return; this.lastHover = now;
        this.init(); if(!this.ctx) return; if(this.ctx.state === 'suspended') this.ctx.resume();
        const osc = this.ctx.createOscillator(); const gain = this.ctx.createGain();
        osc.connect(gain); gain.connect(this.ctx.destination); osc.type = 'sine'; 
        osc.frequency.setValueAtTime(400, this.ctx.currentTime); gain.gain.setValueAtTime(0.015, this.ctx.currentTime); 
        gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + 0.03); osc.start(); osc.stop(this.ctx.currentTime + 0.03);
    }
};

document.addEventListener('alpine:init', () => {
    Alpine.data('careersEngine', () => ({
        filter: 'all',
        playClick() { window.audioEngine.playClick(); },
        playHover() { window.audioEngine.playHover(); }
    }));
});
</script>
<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in-up { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
@endpush