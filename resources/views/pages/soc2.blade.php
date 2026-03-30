@extends('layouts.app')

@section('title', 'SOC 2 Compliance & Trust Center | FinanceAI')

@section('content')

<div x-data="trustCenterEngine()" class="min-h-screen bg-[#0f172a] text-slate-300 pb-32 font-sans selection:bg-emerald-500 selection:text-white relative overflow-hidden flex flex-col pt-24">

    {{-- Dark/Security Backgrounds --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-20%] left-[-10%] w-[1000px] h-[1000px] bg-emerald-500/10 rounded-full blur-[150px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[800px] h-[800px] bg-indigo-500/10 rounded-full blur-[120px]"></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20"></div>
    </div>

    <div class="max-w-[1200px] mx-auto w-full px-4 sm:px-6 lg:px-8 relative z-10 space-y-20">

        {{-- ================= 1. HERO SECURITY SHIELD ================= --}}
        <div class="text-center max-w-4xl mx-auto animate-fade-in-up">
            <div class="w-24 h-24 mx-auto mb-8 relative flex items-center justify-center">
                <div class="absolute inset-0 bg-emerald-500/20 rounded-full blur-xl animate-pulse"></div>
                <div class="w-full h-full bg-[#1e293b] border border-slate-700 rounded-2xl flex items-center justify-center shadow-[0_0_40px_rgba(16,185,129,0.3)] relative z-10 transform rotate-12 hover:rotate-0 transition-transform duration-500">
                    <i class="fa-solid fa-shield-check text-4xl text-emerald-400"></i>
                </div>
            </div>
            
            <h1 class="text-5xl md:text-7xl font-black text-white tracking-tight mb-6">
                Trust & Compliance Center
            </h1>
            <p class="text-lg md:text-xl text-slate-400 font-medium leading-relaxed max-w-2xl mx-auto">
                FinanceAI is audited annually by independent third parties to ensure our cryptographic and organizational controls meet strict SOC 2 Type II standards.
            </p>
        </div>

        {{-- ================= 2. LIVE SECURITY TELEMETRY ================= --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in-up" style="animation-delay: 150ms;">
            
            <div class="bg-[#1e293b]/80 backdrop-blur-md rounded-[2rem] p-8 border border-slate-700 shadow-2xl relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-6">
                    <div class="w-12 h-12 bg-slate-800 rounded-xl flex items-center justify-center border border-slate-700 text-emerald-400"><i class="fa-solid fa-lock"></i></div>
                    <span class="px-2 py-1 bg-emerald-500/20 text-emerald-400 text-[9px] font-black uppercase tracking-widest rounded border border-emerald-500/30">Active</span>
                </div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Ledger Encryption</p>
                <h3 class="text-2xl font-black text-white tracking-tight">AES-256-GCM</h3>
            </div>

            <div class="bg-[#1e293b]/80 backdrop-blur-md rounded-[2rem] p-8 border border-slate-700 shadow-2xl relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-32 h-32 bg-sky-500/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-6">
                    <div class="w-12 h-12 bg-slate-800 rounded-xl flex items-center justify-center border border-slate-700 text-sky-400"><i class="fa-solid fa-server"></i></div>
                    <span class="px-2 py-1 bg-sky-500/20 text-sky-400 text-[9px] font-black uppercase tracking-widest rounded border border-sky-500/30">AWS US-East</span>
                </div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Hosting Architecture</p>
                <h3 class="text-2xl font-black text-white tracking-tight">Isolated Tenancy</h3>
            </div>

            <div class="bg-[#1e293b]/80 backdrop-blur-md rounded-[2rem] p-8 border border-slate-700 shadow-2xl relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-6">
                    <div class="w-12 h-12 bg-slate-800 rounded-xl flex items-center justify-center border border-slate-700 text-indigo-400"><i class="fa-solid fa-eye"></i></div>
                    <span class="flex items-center gap-1.5 px-2 py-1 bg-indigo-500/20 text-indigo-400 text-[9px] font-black uppercase tracking-widest rounded border border-indigo-500/30"><span class="w-1 h-1 bg-indigo-500 rounded-full animate-pulse"></span> Scanning</span>
                </div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Threat Mitigation</p>
                <h3 class="text-2xl font-black text-white tracking-tight" x-text="threatsBlocked">24,591</h3>
            </div>

        </div>

        {{-- ================= 3. SECURITY PILLARS ================= --}}
        <div class="grid lg:grid-cols-12 gap-8 animate-fade-in-up" style="animation-delay: 250ms;">
            
            <div class="lg:col-span-7 space-y-6">
                <h2 class="text-3xl font-black text-white tracking-tight border-b border-slate-800 pb-4">Our Security Principles</h2>
                
                <div class="bg-[#1e293b]/50 rounded-[2rem] p-8 border border-slate-800">
                    <h3 class="text-xl font-bold text-white mb-2 flex items-center gap-3"><i class="fa-solid fa-user-shield text-indigo-400"></i> Identity & Access Management</h3>
                    <p class="text-slate-400 text-sm leading-relaxed mb-4">Access to the FinanceAI production environment is strictly regulated via Role-Based Access Control (RBAC). All master node engineers are required to use hardware-backed Multi-Factor Authentication (MFA) and connect via secure VPN tunnels.</p>
                </div>

                <div class="bg-[#1e293b]/50 rounded-[2rem] p-8 border border-slate-800">
                    <h3 class="text-xl font-bold text-white mb-2 flex items-center gap-3"><i class="fa-solid fa-file-code text-emerald-400"></i> Secure Development Lifecycle</h3>
                    <p class="text-slate-400 text-sm leading-relaxed mb-4">Our continuous integration pipeline automatically scans code for OWASP Top 10 vulnerabilities. All cryptographic algorithms and heuristic models are subject to peer review and automated SAST/DAST testing before deployment.</p>
                </div>
            </div>

            <div class="lg:col-span-5">
                {{-- Request Report Action Box --}}
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-[2.5rem] p-10 border border-slate-700 shadow-2xl relative overflow-hidden h-full flex flex-col justify-center text-center">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                    
                    <div class="w-20 h-20 bg-slate-900 border border-slate-700 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner relative z-10">
                        <i class="fa-solid fa-file-contract text-3xl text-slate-300"></i>
                    </div>

                    <h3 class="text-2xl font-black text-white mb-4 relative z-10">SOC 2 Type II Report</h3>
                    <p class="text-sm text-slate-400 mb-8 relative z-10 font-medium">Available under NDA for Enterprise and Pro Advisor clients evaluating our platform.</p>

                    <button @click="requestReport()" @mouseenter="playHover()" class="w-full py-4 bg-white text-slate-900 hover:bg-emerald-400 hover:text-white rounded-xl font-black uppercase tracking-widest text-[11px] transition-all focus:outline-none shadow-[0_0_20px_rgba(255,255,255,0.1)] hover:shadow-[0_0_20px_rgba(52,211,153,0.4)] relative z-10 group">
                        <span x-show="!isRequesting">Request Full Audit Report</span>
                        <span x-show="isRequesting" style="display: none;"><i class="fa-solid fa-circle-notch fa-spin"></i> Authenticating Request...</span>
                    </button>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-6 relative z-10">Period covering Jan - Dec 2025</p>
                </div>
            </div>

        </div>

    </div>
</div>

{{-- Notification Toast --}}
<div id="toast" class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[9999] bg-white/95 backdrop-blur-xl text-slate-900 px-6 py-3.5 rounded-2xl shadow-[0_20px_40px_-15px_rgba(0,0,0,0.5)] flex items-center gap-3.5 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none border border-slate-200">
    <i id="toastIcon" class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
    <span id="toastMsg" class="text-sm font-bold tracking-wide">Action completed</span>
</div>

@endsection

@push('styles')
<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in-up { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
@endpush

@push('scripts')
<script>
// ================= AUDIO ENGINE =================
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
    },
    playSuccess() {
        this.init(); if(!this.ctx) return; if(this.ctx.state === 'suspended') this.ctx.resume();
        const osc = this.ctx.createOscillator(); const gain = this.ctx.createGain();
        osc.connect(gain); gain.connect(this.ctx.destination); osc.type = 'sine'; 
        osc.frequency.setValueAtTime(600, this.ctx.currentTime); osc.frequency.setValueAtTime(900, this.ctx.currentTime + 0.1);
        gain.gain.setValueAtTime(0.05, this.ctx.currentTime); gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + 0.3);
        osc.start(); osc.stop(this.ctx.currentTime + 0.3);
    }
};

document.addEventListener('alpine:init', () => {
    Alpine.data('trustCenterEngine', () => ({
        threatsBlocked: '24,591',
        isRequesting: false,

        init() {
            // Live Security Telemetry Simulation
            setInterval(() => {
                let current = parseInt(this.threatsBlocked.replace(/,/g, ''));
                current += Math.floor(Math.random() * 5);
                this.threatsBlocked = current.toLocaleString('en-US');
            }, 3500);
        },

        playClick() { window.audioEngine.playClick(); },
        playHover() { window.audioEngine.playHover(); },

        requestReport() {
            this.playClick();
            this.isRequesting = true;
            setTimeout(() => {
                this.isRequesting = false;
                window.audioEngine.playSuccess();
                this.showToast('Request submitted. Our compliance team will email you shortly.');
            }, 1800);
        },

        showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMsg').innerText = msg;
            toast.classList.remove('translate-y-20', 'opacity-0');
            setTimeout(() => toast.classList.add('translate-y-20', 'opacity-0'), 4000);
        }
    }));
});
</script>
@endpush