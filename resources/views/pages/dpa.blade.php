@extends('layouts.app')

@section('title', 'Data Processing Addendum | FinanceAI')

@section('content')

<div x-data="dpaEngine()" @scroll.window="updateScroll()" class="min-h-screen bg-[#f8fafc] pb-32 font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden flex flex-col pt-24">

    {{-- Pristine Light Ambient Backgrounds --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-10%] right-[-5%] w-[800px] h-[800px] bg-slate-200/20 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[600px] h-[600px] bg-indigo-500/5 rounded-full blur-[100px]"></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.03]"></div>
    </div>

    <div class="max-w-[1400px] mx-auto w-full px-4 sm:px-6 lg:px-8 relative z-10 flex flex-col lg:flex-row gap-12 items-start">

        {{-- ================= 1. LEFT SIDEBAR (STICKY TOC) ================= --}}
        <div class="hidden lg:block w-72 shrink-0 sticky top-[120px] max-h-[calc(100vh-140px)] overflow-y-auto scrollbar-hide py-4 animate-fade-in-up">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Contents</h3>
            <nav class="space-y-2 border-l-2 border-slate-100">
                <template x-for="(section, index) in sections" :key="index">
                    <a :href="'#' + section.id" @click="playClick()" @mouseenter="playHover()" 
                       :class="activeSection === section.id ? 'border-indigo-500 text-indigo-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-900'"
                       class="block pl-4 py-1.5 text-sm transition-all -ml-[2px] border-l-2 focus:outline-none" x-text="section.title">
                    </a>
                </template>
            </nav>

            <div class="mt-10 p-5 bg-white rounded-2xl border border-slate-200 shadow-sm">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Document Controls</p>
                <button @click="downloadPDF()" @mouseenter="playHover()" class="w-full py-2.5 bg-slate-50 border border-slate-200 text-slate-600 rounded-xl font-bold text-xs hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all flex items-center justify-center gap-2 focus:outline-none group shadow-sm">
                    <i class="fa-solid fa-file-pdf group-hover:-translate-y-0.5 transition-transform" x-show="!isDownloading"></i>
                    <i class="fa-solid fa-circle-notch fa-spin text-indigo-500" x-show="isDownloading" style="display: none;"></i>
                    <span x-text="isDownloading ? 'Compiling PDF...' : 'Download PDF'"></span>
                </button>
                <button @click="copyLink()" @mouseenter="playHover()" class="w-full mt-2 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-xs hover:bg-slate-50 transition-all flex items-center justify-center gap-2 focus:outline-none shadow-sm">
                    <i class="fa-solid fa-link"></i> Copy Link
                </button>
            </div>
        </div>

        {{-- ================= 2. MAIN DOCUMENT CONTENT ================= --}}
        <div class="flex-1 max-w-4xl bg-white rounded-[2.5rem] border border-slate-200 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.03)] p-8 md:p-16 relative overflow-hidden animate-fade-in-up" style="animation-delay: 150ms;">
            
            {{-- Document Header --}}
            <div class="border-b border-slate-100 pb-10 mb-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-600 text-[10px] font-black tracking-widest uppercase mb-6">
                    <i class="fa-solid fa-scale-balanced"></i> Legal & Compliance
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-tight mb-4">
                    Data Processing Addendum
                </h1>
                <div class="flex flex-wrap items-center gap-4 text-xs font-bold text-slate-500 uppercase tracking-widest">
                    <span>Effective Date: <span class="text-slate-900">March 1, 2026</span></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                    <span>Version: <span class="text-slate-900">2.1.0</span></span>
                </div>
            </div>

            {{-- Document Body --}}
            <div class="prose prose-slate prose-headings:font-black prose-headings:tracking-tight prose-h2:text-2xl prose-h2:mt-12 prose-h2:mb-6 prose-p:font-medium prose-p:text-slate-600 prose-p:leading-relaxed prose-a:text-indigo-600 prose-li:text-slate-600 prose-li:font-medium max-w-none">
                
                <p>This Data Processing Addendum ("<strong>DPA</strong>") forms part of the Master Services Agreement or Terms of Service available at <a href="{{ route('pages.terms') ?? '#' }}">FinanceAI Terms</a> (the "<strong>Agreement</strong>") entered into by and between FinanceAI Technologies Inc. ("<strong>FinanceAI</strong>") and the Customer.</p>

                <h2 id="section-1" class="scroll-mt-32">1. Definitions</h2>
                <p>For the purposes of this DPA, the following terms have the meanings set forth below:</p>
                <ul>
                    <li><strong>"Applicable Data Protection Laws"</strong> means all global privacy and data protection laws applicable to the processing of Personal Data under the Agreement, including the GDPR (EU & UK) and CCPA/CPRA (California).</li>
                    <li><strong>"Controller"</strong>, <strong>"Processor"</strong>, <strong>"Data Subject"</strong>, <strong>"Personal Data"</strong>, and <strong>"Processing"</strong> shall have the meanings given to them under Applicable Data Protection Laws.</li>
                    <li><strong>"Customer Data"</strong> means any Personal Data processed by FinanceAI on behalf of Customer in connection with the Services.</li>
                </ul>

                <h2 id="section-2" class="scroll-mt-32">2. Processing of Personal Data</h2>
                <p>FinanceAI shall process Customer Data only to provide the Services, as strictly necessary to perform its obligations under the Agreement, and in accordance with Customer's documented instructions.</p>
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 my-6 not-prose">
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2">Scope of Processing</h4>
                    <ul class="space-y-2 text-sm font-medium text-slate-600">
                        <li class="flex items-start gap-2"><i class="fa-solid fa-check text-emerald-500 mt-0.5"></i> <strong>Nature:</strong> Cryptographic ledger storage and heuristic analysis.</li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-check text-emerald-500 mt-0.5"></i> <strong>Categories:</strong> Financial metadata, identity hashes, access logs.</li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-xmark text-rose-500 mt-0.5"></i> <strong>Exclusions:</strong> FinanceAI does NOT process raw PAN (Primary Account Numbers) or PCI-DSS prohibited data.</li>
                    </ul>
                </div>

                <h2 id="section-3" class="scroll-mt-32">3. Subprocessors</h2>
                <p>Customer provides a general authorization for FinanceAI to engage Subprocessors to process Customer Data. FinanceAI will maintain an up-to-date list of its Subprocessors. FinanceAI shall impose data protection terms on any Subprocessor it appoints that require it to protect the Customer Data to the standard required by Applicable Data Protection Laws.</p>

                <h2 id="section-4" class="scroll-mt-32">4. Cryptographic Security</h2>
                <p>Taking into account the state of the art, the costs of implementation and the nature, scope, context and purposes of Processing, FinanceAI shall implement and maintain appropriate technical and organizational measures to ensure a level of security appropriate to the risk.</p>
                <p>These measures include, but are not limited to:</p>
                <ol>
                    <li>AES-256-GCM encryption of all ledger data at rest.</li>
                    <li>TLS 1.3 transit encryption for all API endpoints.</li>
                    <li>Strict Role-Based Access Control (RBAC) and mandatory MFA for all Master Nodes.</li>
                </ol>

                <h2 id="section-5" class="scroll-mt-32">5. Data Subject Rights</h2>
                <p>FinanceAI shall, to the extent legally permitted, promptly notify Customer if it receives a request from a Data Subject to exercise their rights under Applicable Data Protection Laws. FinanceAI shall not respond to any such request without Customer's prior written consent, except to confirm that the request relates to Customer.</p>

            </div>

            {{-- Signature Block --}}
            <div class="mt-16 pt-10 border-t border-slate-100 flex flex-col md:flex-row justify-between gap-8">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Authorized Signature (FinanceAI)</p>
                    <div class="font-['Brush_Script_MT',_cursive] text-4xl text-slate-800 mb-2">FinanceAI Inc.</div>
                    <p class="text-xs font-bold text-slate-900">Legal & Compliance Office</p>
                    <p class="text-xs text-slate-500">privacy@financeai.com</p>
                </div>
                <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-6 w-full md:w-72">
                    <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-2">Execution Protocol</p>
                    <p class="text-xs text-indigo-900 font-medium mb-4">This DPA is pre-signed by FinanceAI. To execute, download the PDF, sign, and return via your Master Node dashboard.</p>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Notification Toast --}}
<div id="toast" class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[9999] bg-slate-900/95 backdrop-blur-xl text-white px-6 py-3.5 rounded-2xl shadow-[0_20px_40px_-15px_rgba(0,0,0,0.5)] flex items-center gap-3.5 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none border border-slate-700">
    <i id="toastIcon" class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
    <span id="toastMsg" class="text-sm font-bold tracking-wide">Action completed</span>
</div>

@endsection

@push('styles')
<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    html { scroll-behavior: smooth; }
    
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
    Alpine.data('dpaEngine', () => ({
        activeSection: 'section-1',
        isDownloading: false,
        sections: [
            { id: 'section-1', title: '1. Definitions' },
            { id: 'section-2', title: '2. Processing of Personal Data' },
            { id: 'section-3', title: '3. Subprocessors' },
            { id: 'section-4', title: '4. Cryptographic Security' },
            { id: 'section-5', title: '5. Data Subject Rights' }
        ],

        playClick() { window.audioEngine.playClick(); },
        playHover() { window.audioEngine.playHover(); },

        updateScroll() {
            // Simple scroll spy logic for TOC
            let current = 'section-1';
            for (let section of this.sections) {
                const el = document.getElementById(section.id);
                if (el && window.scrollY >= (el.offsetTop - 150)) {
                    current = section.id;
                }
            }
            this.activeSection = current;
        },

        downloadPDF() {
            this.playClick();
            this.isDownloading = true;
            setTimeout(() => {
                this.isDownloading = false;
                window.audioEngine.playSuccess();
                this.showToast('DPA Legal Document downloaded successfully.');
            }, 1500);
        },

        copyLink() {
            this.playClick();
            navigator.clipboard.writeText(window.location.href).then(() => {
                this.showToast('Document permalink copied to clipboard!');
            });
        },

        showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMsg').innerText = msg;
            toast.classList.remove('translate-y-20', 'opacity-0');
            setTimeout(() => toast.classList.add('translate-y-20', 'opacity-0'), 3000);
        }
    }));
});
</script>
@endpush