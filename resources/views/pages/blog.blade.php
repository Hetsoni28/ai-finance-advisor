@extends('layouts.app')

@section('title', 'Engineering Blog | FinanceAI')

@section('content')

@php
    $featuredPost = [
        'title' => 'Scaling MySQL 8 for Real-Time Heuristic Ledger Analytics',
        'excerpt' => 'How we migrated from standard auto-incrementing IDs to cryptographic ULIDs to prevent database collision and increase read throughput by 40% across our node clusters.',
        'date' => 'Mar 24, 2026',
        'author' => 'Jonathan Doe',
        'role' => 'Principal Architect',
        'tag' => 'Database Infrastructure'
    ];

    $posts = [
        ['title' => 'Implementing AES-256-GCM in Laravel 8', 'date' => 'Mar 18, 2026', 'tag' => 'Security', 'color' => 'rose'],
        ['title' => 'The Mathematics of Capital Retention Rates', 'date' => 'Mar 10, 2026', 'tag' => 'Algorithms', 'color' => 'indigo'],
        ['title' => 'Building a Zero-Latency AI Chat Interface', 'date' => 'Feb 28, 2026', 'tag' => 'Frontend UX', 'color' => 'sky'],
    ];
@endphp

<div x-data="blogEngine()" class="min-h-screen bg-[#f8fafc] pb-32 font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden flex flex-col pt-24">

    {{-- Ambient Backgrounds --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-10%] right-[-5%] w-[800px] h-[800px] bg-indigo-500/5 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-[1400px] mx-auto w-full px-4 sm:px-6 lg:px-8 relative z-10 space-y-16">

        {{-- Header --}}
        <div class="text-center space-y-6 animate-fade-in-up">
            <h1 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tight leading-tight">
                FinanceAI <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-sky-500">Engineering</span>
            </h1>
            <p class="text-lg text-slate-500 font-medium max-w-2xl mx-auto leading-relaxed">
                Technical deep-dives, architectural post-mortems, and security whitepapers from the engineers building the core platform.
            </p>
        </div>

        {{-- Featured Article (Editorial Hero) --}}
        <a href="#" @mouseenter="playHover()" class="block bg-slate-900 rounded-[2.5rem] p-8 md:p-16 border border-slate-800 shadow-2xl hover:shadow-[0_20px_50px_rgba(79,70,229,0.3)] transition-all duration-500 relative overflow-hidden group animate-fade-in-up" style="animation-delay: 100ms;">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-30 mix-blend-overlay"></div>
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-indigo-500/30 rounded-full blur-[100px] group-hover:scale-150 transition-transform duration-1000"></div>
            
            <div class="relative z-10 flex flex-col h-full min-h-[300px] justify-between">
                <div>
                    <span class="inline-flex px-3 py-1 bg-indigo-500/20 border border-indigo-500/30 text-indigo-400 text-[10px] font-black uppercase tracking-widest rounded-lg mb-6">
                        <i class="fa-solid fa-star mr-1.5"></i> Featured Article
                    </span>
                    <h2 class="text-3xl md:text-5xl font-black text-white tracking-tight leading-tight max-w-3xl group-hover:text-indigo-100 transition-colors">
                        {{ $featuredPost['title'] }}
                    </h2>
                    <p class="text-slate-400 font-medium mt-6 text-lg max-w-2xl leading-relaxed">
                        {{ $featuredPost['excerpt'] }}
                    </p>
                </div>
                
                <div class="flex items-center gap-4 mt-12 pt-8 border-t border-slate-800">
                    <div class="w-12 h-12 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-300 font-black font-mono shadow-inner">
                        {{ substr($featuredPost['author'], 0, 1) }}
                    </div>
                    <div>
                        <p class="text-white font-bold tracking-tight">{{ $featuredPost['author'] }}</p>
                        <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest mt-0.5">{{ $featuredPost['role'] }} &bull; {{ $featuredPost['date'] }}</p>
                    </div>
                </div>
            </div>
        </a>

        {{-- Recent Posts Grid --}}
        <div class="grid md:grid-cols-3 gap-8 animate-fade-in-up" style="animation-delay: 250ms;">
            @foreach($posts as $post)
                <a href="#" @mouseenter="playHover()" class="bg-white rounded-[2rem] p-8 border border-slate-200 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group flex flex-col h-full">
                    <div class="mb-6">
                        <span class="px-3 py-1 bg-{{ $post['color'] }}-50 text-{{ $post['color'] }}-600 border border-{{ $post['color'] }}-200 rounded-lg text-[9px] font-black uppercase tracking-widest">
                            {{ $post['tag'] }}
                        </span>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 tracking-tight leading-snug group-hover:text-indigo-600 transition-colors mb-8">
                        {{ $post['title'] }}
                    </h3>
                    <div class="mt-auto flex items-center justify-between text-[10px] font-black text-slate-400 uppercase tracking-widest pt-6 border-t border-slate-100">
                        <span><i class="fa-regular fa-calendar mr-1"></i> {{ $post['date'] }}</span>
                        <span class="text-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1">Read <i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Newsletter Capture --}}
        <div class="bg-indigo-600 rounded-[2.5rem] p-12 text-center relative overflow-hidden shadow-2xl mt-16 animate-fade-in-up" style="animation-delay: 350ms;">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
            <h3 class="text-3xl font-black text-white tracking-tight relative z-10 mb-4">Subscribe to the Engineering Newsletter</h3>
            <p class="text-indigo-200 font-medium mb-8 relative z-10">Get architectural breakdowns and system telemetry delivered to your inbox monthly.</p>
            
            <form class="max-w-md mx-auto relative z-10 flex gap-2" @submit.prevent="playClick(); showToast()">
                <input type="email" placeholder="developer@node.com" required class="flex-1 px-5 py-4 rounded-xl bg-indigo-700/50 border border-indigo-500 text-white placeholder-indigo-300 focus:outline-none focus:ring-2 focus:ring-white">
                <button type="submit" @mouseenter="playHover()" class="px-6 py-4 bg-white text-indigo-600 font-black uppercase tracking-widest text-[10px] rounded-xl hover:bg-slate-50 transition-colors focus:outline-none shadow-lg">
                    Subscribe
                </button>
            </form>
        </div>

    </div>
</div>

{{-- Toast --}}
<div id="toast" class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[9999] bg-slate-900/95 backdrop-blur-xl text-white px-6 py-3.5 rounded-2xl shadow-[0_20px_40px_-15px_rgba(0,0,0,0.5)] flex items-center gap-3.5 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none border border-slate-700">
    <i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
    <span class="text-sm font-bold tracking-wide">Successfully subscribed to telemetry!</span>
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
    }
};

document.addEventListener('alpine:init', () => {
    Alpine.data('blogEngine', () => ({
        playClick() { window.audioEngine.playClick(); },
        playHover() { window.audioEngine.playHover(); },
        showToast() {
            const toast = document.getElementById('toast');
            toast.classList.remove('translate-y-20', 'opacity-0');
            setTimeout(() => toast.classList.add('translate-y-20', 'opacity-0'), 3000);
        }
    }));
});
</script>
@endpush