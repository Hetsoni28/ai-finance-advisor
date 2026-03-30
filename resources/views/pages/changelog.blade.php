@extends('layouts.app')

@section('title', 'System Changelog | FinanceAI')

@section('content')

@php
    // Simulated Enterprise Changelog Payload
    $releases = [
        [
            'version' => 'v2.4.0',
            'date' => 'March 26, 2026',
            'latest' => true,
            'title' => 'Master Node Telemetry & Heuristic Dashboards',
            'description' => 'A complete overhaul of the global master node, introducing real-time infrastructure load monitoring and deterministic event routing.',
            'tags' => ['feature', 'infrastructure'],
            'updates' => [
                ['type' => 'feature', 'text' => 'Implemented Live EKG Database Latency monitoring on the Admin Dashboard.'],
                ['type' => 'feature', 'text' => 'Added mathematical capital retention rate algorithms to the Identity Profile.'],
                ['type' => 'security', 'text' => 'Enforced strict cryptographic session termination when passwords are rotated.'],
                ['type' => 'optimization', 'text' => 'Replaced 12 N+1 database queries with 2 highly optimized aggregate raw SQL statements for 6-month telemetry.']
            ]
        ],
        [
            'version' => 'v2.3.5',
            'date' => 'March 14, 2026',
            'latest' => false,
            'title' => 'AES-256 Workspace Handshakes',
            'description' => 'Upgraded the collaborative family hub with magic-link session hijacking protection and global audit ledgers.',
            'tags' => ['security', 'feature'],
            'updates' => [
                ['type' => 'security', 'text' => 'Added strict Session ID regeneration during magic-link acceptance to prevent XSS session fixation.'],
                ['type' => 'feature', 'text' => 'Integrated Spatie Activitylog for permanent, immutable records of all workspace invitations and revocations.'],
                ['type' => 'bugfix', 'text' => 'Resolved a transaction desync where expired tokens were not properly purged from the master database.']
            ]
        ],
        [
            'version' => 'v2.3.0',
            'date' => 'February 28, 2026',
            'latest' => false,
            'title' => 'AI Predictive Analytics Engine Phase 1',
            'description' => 'Deployed the foundational NLP models required for automated ledger categorization and runway calculation.',
            'tags' => ['feature', 'ai'],
            'updates' => [
                ['type' => 'feature', 'text' => 'Introduced the /analyze and /runway slash commands for rapid financial telemetry.'],
                ['type' => 'optimization', 'text' => 'Implemented asynchronous DOM streaming for simulated LLM response rendering.'],
                ['type' => 'bugfix', 'text' => 'Fixed an overflow error where February leap years corrupted the trailing 30-day savings algorithms.']
            ]
        ],
        [
            'version' => 'v2.2.1',
            'date' => 'February 10, 2026',
            'latest' => false,
            'title' => 'Core Database Migrations',
            'description' => 'Structural upgrades to the MySQL 8 database to support upcoming AI and Team features.',
            'tags' => ['infrastructure', 'optimization'],
            'updates' => [
                ['type' => 'infrastructure', 'text' => 'Migrated standard integer IDs to ULIDs for distributed node processing.'],
                ['type' => 'optimization', 'text' => 'Added composite database indexes to `created_at` and `user_id` on the Expenses table, reducing query times by 40%.'],
            ]
        ]
    ];

    // Tag Color Mapping
    $tagColors = [
        'feature' => 'bg-indigo-50 text-indigo-600 border-indigo-200',
        'security' => 'bg-rose-50 text-rose-600 border-rose-200',
        'optimization' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
        'bugfix' => 'bg-amber-50 text-amber-600 border-amber-200',
        'infrastructure' => 'bg-sky-50 text-sky-600 border-sky-200',
        'ai' => 'bg-purple-50 text-purple-600 border-purple-200',
    ];
@endphp

<div x-data="changelogEngine()" class="min-h-screen bg-[#f8fafc] pb-32 font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden flex flex-col pt-24">

    {{-- Holographic Light Ambient Backgrounds --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-10%] right-[-5%] w-[800px] h-[800px] bg-indigo-500/5 rounded-full blur-[120px] transition-colors duration-1000"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[600px] h-[600px] bg-sky-500/5 rounded-full blur-[100px]"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCI+PHBhdGggZD0iTTAgMGg0MHY0MEgwVjB6bTIwIDIwaDIwdjIwSDIwaC0yMHptMCAwaC0yMHYtMjBoMjB2MjB6IiBmaWxsPSIjZThlYWVkIiBmaWxsLW9wYWNpdHk9IjAuMDUiIGZpbGwtcnVsZT0iZXZlbm9kZCIvPjwvc3ZnPg==')] opacity-100"></div>
    </div>

    <div class="max-w-[1000px] mx-auto w-full px-4 sm:px-6 lg:px-8 relative z-10 space-y-12">

        {{-- ================= 1. PAGE HEADER ================= --}}
        <div class="text-center space-y-6 animate-fade-in-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white border border-slate-200 text-slate-500 text-xs font-black tracking-widest uppercase shadow-sm">
                <i class="fa-solid fa-code-commit text-indigo-500"></i> Release Notes
            </span>
            <h1 class="text-4xl md:text-6xl font-black text-slate-900 tracking-tight leading-tight">
                System Changelog
            </h1>
            <p class="text-lg text-slate-500 font-medium max-w-2xl mx-auto leading-relaxed">
                Track the evolution of the FinanceAI architecture. We ship updates, optimizations, and cryptographic security patches weekly.
            </p>
        </div>

        {{-- ================= 2. FILTER TOOLBAR ================= --}}
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-2 border border-slate-200 shadow-sm flex flex-wrap items-center justify-center gap-2 animate-fade-in-up" style="animation-delay: 100ms;">
            <button @click="activeFilter = 'all'; playClick()" @mouseenter="playHover()" :class="activeFilter === 'all' ? 'bg-slate-900 text-white shadow-md' : 'bg-transparent text-slate-500 hover:bg-slate-100'" class="px-5 py-2 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all focus:outline-none">
                All Updates
            </button>
            <button @click="activeFilter = 'feature'; playClick()" @mouseenter="playHover()" :class="activeFilter === 'feature' ? 'bg-indigo-500 text-white shadow-md' : 'bg-transparent text-slate-500 hover:bg-slate-100'" class="px-5 py-2 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all focus:outline-none">
                Features
            </button>
            <button @click="activeFilter = 'security'; playClick()" @mouseenter="playHover()" :class="activeFilter === 'security' ? 'bg-rose-500 text-white shadow-md' : 'bg-transparent text-slate-500 hover:bg-slate-100'" class="px-5 py-2 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all focus:outline-none">
                Security
            </button>
            <button @click="activeFilter = 'optimization'; playClick()" @mouseenter="playHover()" :class="activeFilter === 'optimization' ? 'bg-emerald-500 text-white shadow-md' : 'bg-transparent text-slate-500 hover:bg-slate-100'" class="px-5 py-2 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all focus:outline-none">
                Optimizations
            </button>
        </div>

        {{-- ================= 3. TIMELINE ENGINE ================= --}}
        <div class="relative pt-8 pb-16 animate-fade-in-up" style="animation-delay: 200ms;">
            
            {{-- Center Timeline Line --}}
            <div class="absolute left-8 md:left-32 top-12 bottom-0 w-px bg-gradient-to-b from-indigo-200 via-slate-200 to-transparent"></div>

            <div class="space-y-16">
                @foreach($releases as $index => $release)
                    <div x-show="matchesFilter({{ json_encode($release['tags']) }})" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="relative pl-20 md:pl-48">
                        
                        {{-- Timeline Node & Date --}}
                        <div class="absolute left-0 md:left-0 top-0 w-16 md:w-32 text-right pr-6 hidden md:block">
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest block pt-1.5">{{ $release['date'] }}</span>
                            @if($release['latest'])
                                <span class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest mt-1 block">Latest</span>
                            @endif
                        </div>

                        {{-- Node Dot --}}
                        <div class="absolute left-[28px] md:left-[124px] top-2 w-3 h-3 rounded-full bg-white border-2 {{ $release['latest'] ? 'border-indigo-500 ring-4 ring-indigo-500/20' : 'border-slate-300' }} shadow-sm z-10 transition-all duration-300"></div>

                        {{-- Release Card --}}
                        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-[0_10px_30px_rgba(0,0,0,0.03)] p-8 relative overflow-hidden group hover:shadow-[0_15px_40px_rgba(79,70,229,0.08)] transition-all duration-500">
                            
                            {{-- Ambient Card Glow --}}
                            @if($release['latest'])
                                <div class="absolute top-0 right-0 w-48 h-48 bg-indigo-500/5 rounded-full blur-3xl pointer-events-none"></div>
                            @endif

                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6 relative z-10">
                                <div>
                                    <div class="flex items-center gap-3 mb-2">
                                        <button @click="copyLink('{{ $release['version'] }}')" @mouseenter="playHover()" class="text-xl md:text-2xl font-black text-slate-900 tracking-tight hover:text-indigo-600 transition-colors focus:outline-none flex items-center gap-2 group/version">
                                            {{ $release['version'] }}
                                            <i class="fa-solid fa-link text-xs text-slate-300 group-hover/version:text-indigo-400 transition-colors opacity-0 group-hover/version:opacity-100 transform -translate-x-2 group-hover/version:translate-x-0"></i>
                                        </button>
                                    </div>
                                    <h2 class="text-lg font-bold text-slate-700">{{ $release['title'] }}</h2>
                                </div>
                                <div class="flex flex-wrap gap-2 shrink-0">
                                    @foreach($release['tags'] as $tag)
                                        <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border shadow-sm {{ $tagColors[$tag] ?? $tagColors['feature'] }}">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <p class="text-slate-500 font-medium text-sm leading-relaxed mb-8 relative z-10">
                                {{ $release['description'] }}
                            </p>

                            <ul class="space-y-4 relative z-10 border-t border-slate-100 pt-6">
                                @foreach($release['updates'] as $update)
                                    @php
                                        $uColor = $tagColors[$update['type']] ?? $tagColors['feature'];
                                    @endphp
                                    <li class="flex items-start gap-4">
                                        <div class="mt-1 w-2 h-2 rounded-full border-2 {{ str_replace('bg-', 'border-', explode(' ', $uColor)[0]) }} shrink-0"></div>
                                        <div>
                                            <span class="text-[9px] font-black uppercase tracking-widest {{ str_replace('bg-', 'text-', explode(' ', $uColor)[1]) }} block mb-0.5">{{ $update['type'] }}</span>
                                            <p class="text-sm font-medium text-slate-600 leading-relaxed">{{ $update['text'] }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>

                        </div>
                    </div>
                @endforeach
            </div>
            
            {{-- End of Timeline --}}
            <div class="absolute bottom-0 left-8 md:left-32 transform -translate-x-1/2 translate-y-4">
                <div class="w-8 h-8 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center shadow-sm">
                    <i class="fa-solid fa-clock-rotate-left text-[10px] text-slate-400"></i>
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
    Alpine.data('changelogEngine', () => ({
        activeFilter: 'all',

        playClick() { window.audioEngine.playClick(); },
        playHover() { window.audioEngine.playHover(); },

        matchesFilter(tags) {
            if (this.activeFilter === 'all') return true;
            return tags.includes(this.activeFilter);
        },

        copyLink(version) {
            this.playClick();
            // Construct a fake permalink for the UI
            const url = window.location.origin + window.location.pathname + '#' + version.replace(/\./g, '-');
            navigator.clipboard.writeText(url).then(() => {
                this.showToast(`Link to ${version} copied to clipboard!`);
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