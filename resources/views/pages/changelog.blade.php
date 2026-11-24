@extends('layouts.landing')
@include('partials.site-type')

@section('title', 'System Changelog & Release Notes | FinanceAI Enterprise')
@section('meta_description', 'Track every deployment, feature release, and security patch in the FinanceAI architecture.')

@section('content')
@php
    $metrics = [
        ['label' => 'Total Deployments', 'value' => '142', 'icon' => 'fa-rocket'],
        ['label' => 'Security Patches', 'value' => '38', 'icon' => 'fa-shield-halved'],
        ['label' => 'Platform Uptime', 'value' => '99.99%', 'icon' => 'fa-server'],
    ];

    $releases = [
        [
            'version' => '3.1.0', 'date' => 'April 14, 2026', 'type' => 'major',
            'title' => 'Contact Module & Geolocation Tracking',
            'description' => 'A complete overhaul of the public ingress system. We introduced a highly secure, rate-limited contact architecture with automated geographical routing.',
            'authors' => ['12', '33'],
            'changes' => [
                ['tag' => 'feature', 'text' => 'Full contact form with AJAX submission and secure geolocation capture.'],
                ['tag' => 'feature', 'text' => 'Admin contact inbox with KPI cards, read/unread toggle, and interactive Leaflet maps.'],
                ['tag' => 'improvement', 'text' => 'Enhanced admin sidebar with Contact Messages navigation tree.'],
                ['tag' => 'security', 'text' => 'Strict XSS protection via server-side strip_tags() on all message inputs.'],
            ],
        ],
        [
            'version' => '3.0.0', 'date' => 'March 28, 2026', 'type' => 'major',
            'title' => 'Neural Engine 3.0 & AI Financial Assistant',
            'description' => 'The largest update in FinanceAI history. We replaced standard algorithmic categorization with a custom-trained LLM architecture utilizing Retrieval-Augmented Generation (RAG).',
            'authors' => ['45', '11', '68'],
            'changes' => [
                ['tag' => 'feature', 'text' => 'Gemini-powered AI financial advisor integrated directly into the user dashboard.'],
                ['tag' => 'feature', 'text' => 'Real-time market data ingestion via Alpha Vantage and CoinGecko APIs.'],
                ['tag' => 'feature', 'text' => 'Streaming AI chat with source citations and mathematical confidence scoring.'],
                ['tag' => 'improvement', 'text' => 'Subscription-gated AI features specifically for Pro and Enterprise tiers.'],
                ['tag' => 'security', 'text' => 'Strict prompt-injection sanitization to prevent LLM jailbreaks.'],
            ],
        ],
        [
            'version' => '2.5.0', 'date' => 'February 12, 2026', 'type' => 'minor',
            'title' => 'SaaS Subscription System',
            'description' => 'Transitioned from a single-tenant model to a fully scalable SaaS architecture with integrated billing limits and compute quotas.',
            'authors' => ['32', '59'],
            'changes' => [
                ['tag' => 'feature', 'text' => 'Three-tier subscription model (Core Node, Pro Hub, Enterprise) deployed.'],
                ['tag' => 'feature', 'text' => 'Custom Laravel middleware engineered for feature-based access control.'],
                ['tag' => 'improvement', 'text' => 'Simulated payment flow with a comprehensive subscription management UI.'],
                ['tag' => 'fix', 'text' => 'Resolved a race condition during concurrent user registration.'],
            ],
        ],
        [
            'version' => '2.4.0', 'date' => 'January 05, 2026', 'type' => 'minor',
            'title' => 'Family Hub & Shared Ledgers',
            'description' => 'Enabled multi-node collaboration. Families and financial teams can now operate securely within isolated, shared database clusters.',
            'authors' => ['12'],
            'changes' => [
                ['tag' => 'feature', 'text' => 'Multi-user workspace system with strict Role-Based Access Control (RBAC).'],
                ['tag' => 'feature', 'text' => 'Cryptographic family invitation system with secure email token dispatch.'],
                ['tag' => 'improvement', 'text' => 'Workspace context switcher added to the master sidebar navigation.'],
                ['tag' => 'fix', 'text' => 'Fixed a pagination offset bug on large family member listings.'],
            ],
        ],
        [
            'version' => '2.3.1', 'date' => 'December 18, 2025', 'type' => 'patch',
            'title' => 'Security Hardening & Performance',
            'description' => 'Routine infrastructure maintenance and database optimization to support scaling load.',
            'authors' => ['68', '33'],
            'changes' => [
                ['tag' => 'security', 'text' => 'Upgraded to TLS 1.3 requirement for all API endpoint communications.'],
                ['tag' => 'security', 'text' => 'Rate limiting applied to all authentication and password reset endpoints.'],
                ['tag' => 'improvement', 'text' => '40% improvement in dashboard query performance via composite SQL indexing.'],
                ['tag' => 'fix', 'text' => 'Fixed a memory leak in the global notification polling service.'],
            ],
        ],
    ];

    $tagStyles = [
        'feature' => 'text-emerald-700',
        'improvement' => 'text-sky-700',
        'security' => 'text-rose-700',
        'fix' => 'text-amber-700',
    ];
@endphp
<div class="paper" x-data="changelogEngine()">

    <x-site.hero eyebrow="Deployment History" lead="System" accent="Changelog"
        lede="Every cryptographic patch, neural engine upgrade, and infrastructure enhancement — documented with absolute mathematical precision.">
        <x-slot name="meta">
            <dl class="grid grid-cols-3 max-w-xl">
                @foreach($metrics as $m)
                    <div>
                        <dt class="font-mono text-[11px] uppercase tracking-wider text-slate-400">{{ $m['label'] }}</dt>
                        <dd class="mt-1 text-2xl sm:text-3xl font-black text-white">{{ $m['value'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-slot>
    </x-site.hero>

    {{-- Toolbar --}}
    <div class="sticky top-16 lg:top-[72px] z-30 bg-[#fbfbfa]/90 backdrop-blur border-b border-slate-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-col md:flex-row md:items-center gap-3">
            <label class="relative md:w-72">
                <span class="sr-only">Search updates</span>
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400" aria-hidden="true"></i>
                <input type="text" x-model="searchQuery" @input="playKeySound()" placeholder="Search updates..."
                       class="w-full h-10 pl-9 pr-3 rounded-lg border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
            </label>
            <div class="flex gap-1 overflow-x-auto scrollbar-hide" role="tablist" aria-label="Filter by type">
                <template x-for="cat in ['all', 'feature', 'improvement', 'security', 'fix']" :key="cat">
                    <button type="button" @click="setFilter(cat)" @mouseenter="playHover()" role="tab" :aria-selected="activeFilter === cat"
                            class="h-10 px-3.5 rounded-lg font-mono text-[11px] uppercase tracking-wider whitespace-nowrap transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-100"
                            :class="activeFilter === cat ? 'bg-slate-900 text-white' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100'">
                        <span x-text="cat === 'all' ? 'All' : cat"></span>
                    </button>
                </template>
            </div>
        </div>
    </div>

    {{-- Releases --}}
    <section class="py-16 sm:py-24">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="border-t border-slate-900">
                @foreach($releases as $idx => $release)
                    @php $anchor = 'v' . str_replace('.', '-', $release['version']); @endphp
                    <article id="{{ $anchor }}" class="release-item grid md:grid-cols-12 gap-6 md:gap-10 py-12 border-b border-slate-200 scroll-mt-36"
                             data-version="v{{ $release['version'] }}"
                             x-show="matchesFilter({{ json_encode($release) }})"
                             x-transition.opacity.duration.250ms>
                        <header class="md:col-span-3">
                            <p class="font-mono text-sm font-semibold {{ $release['type'] === 'major' ? 'text-indigo-600' : 'text-slate-900' }}">v{{ $release['version'] }}</p>
                            <p class="mt-1 font-mono text-xs uppercase tracking-wider text-slate-400">{{ $release['date'] }}</p>
                            @if($idx === 0)
                                <p class="mt-3 inline-flex items-center gap-1.5 font-mono text-[10px] uppercase tracking-wider text-emerald-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span> Latest
                                </p>
                            @endif
                        </header>

                        <div class="md:col-span-9">
                            <div class="flex items-start justify-between gap-4">
                                <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">{{ $release['title'] }}</h2>
                                <button type="button" @click="copyLink('{{ $anchor }}')" @mouseenter="playHover()" class="shrink-0 h-9 w-9 rounded-lg border border-slate-200 text-slate-400 hover:text-slate-900 hover:border-slate-900 transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-100" title="Copy Permalink" aria-label="Copy link to v{{ $release['version'] }}">
                                    <i class="fa-solid fa-link text-xs" aria-hidden="true"></i>
                                </button>
                            </div>
                            <p class="mt-4 text-lg text-slate-600 leading-relaxed max-w-2xl">{{ $release['description'] }}</p>

                            <ul class="mt-7 border-t border-slate-200">
                                @foreach($release['changes'] as $item)
                                    <li class="change-item grid grid-cols-[7.5rem_1fr] gap-4 py-3 border-b border-slate-100" data-tag="{{ $item['tag'] }}">
                                        <span class="font-mono text-[11px] uppercase tracking-wider pt-0.5 {{ $tagStyles[$item['tag']] ?? 'text-slate-500' }}">{{ $item['tag'] }}</span>
                                        <span class="text-[15px] text-slate-700 leading-relaxed">{{ $item['text'] }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <footer class="mt-6 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono text-[10px] uppercase tracking-wider text-slate-400">Shipped By</span>
                                    <div class="flex -space-x-2">
                                        @foreach($release['authors'] as $author)
                                            <img src="https://i.pravatar.cc/100?img={{ $author }}" class="w-7 h-7 rounded-full border-2 border-[#fbfbfa] grayscale" alt="Engineer" loading="lazy">
                                        @endforeach
                                    </div>
                                </div>
                                <a href="#" class="font-mono text-[11px] uppercase tracking-wider text-slate-500 hover:text-slate-900">View PR Details &rarr;</a>
                            </footer>
                        </div>
                    </article>
                @endforeach

                <div x-show="filteredCount === 0" x-cloak class="py-20 text-center border-b border-slate-200">
                    <p class="text-2xl font-black tracking-tight text-slate-900">No matching updates found</p>
                    <p class="mt-2 text-slate-500">Try adjusting your search terms or clearing the category filters.</p>
                    <button type="button" @click="searchQuery = ''; activeFilter = 'all'; playClick()" class="mt-6 h-10 px-4 rounded-lg bg-slate-900 text-white text-sm font-semibold hover:bg-indigo-600 transition-colors focus:outline-none">
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- Subscribe --}}
    <section class="py-20 sm:py-28 bg-[#0b0b1a] text-white grain">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-10 items-end">
            <div class="lg:col-span-7" data-reveal>
                <p class="eyebrow text-indigo-300">Release notes by email</p>
                <h2 class="mt-4 text-3xl sm:text-5xl font-black tracking-[-0.03em] leading-[1.05]">Subscribe to Engine <span class="font-serif-i text-indigo-300">Telemetry</span></h2>
                <p class="mt-5 text-lg text-slate-300 leading-relaxed max-w-xl">Join 12,000+ engineers receiving our cryptographic updates, AI financial forecasts, and feature drops directly.</p>
            </div>
            <form @submit.prevent="playClick(); $dispatch('notify', {message: 'Payload secured. Welcome to the network.', type: 'success'})" class="lg:col-span-5 flex flex-col sm:flex-row gap-3" data-reveal style="--d: 120ms">
                <label class="sr-only" for="cl-email">Email</label>
                <input id="cl-email" type="email" required placeholder="developer@node.com" class="flex-1 h-12 px-4 rounded-xl bg-white/5 border border-white/15 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-400/20">
                <button type="submit" @mouseenter="playHover()" class="h-12 px-6 rounded-xl bg-white text-slate-900 font-bold hover:bg-indigo-100 transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-white/30">
                    Subscribe <i class="fa-solid fa-arrow-right ml-1 text-sm" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('changelogEngine', () => ({
        activeFilter: 'all',
        searchQuery: '',
        filteredCount: -1,

        init() {
            this.$watch('searchQuery', () => this.updateFilteredCount());
            this.$watch('activeFilter', () => this.updateFilteredCount());
            this.$nextTick(() => this.updateFilteredCount());
        },

        playClick() { if (window.audioEngine) window.audioEngine.playClick(); },
        playHover() { if (window.audioEngine) window.audioEngine.playHover(); },
        playKeySound() {
            if (!window.audioEngine) return;
            if (!this.lastTick || Date.now() - this.lastTick > 50) { window.audioEngine.playClick(); this.lastTick = Date.now(); }
        },

        setFilter(cat) { this.playClick(); this.activeFilter = cat; this.searchQuery = ''; },

        matchesFilter(release) {
            let matchCategory = true, matchSearch = true;
            if (this.activeFilter !== 'all') matchCategory = release.changes.some(c => c.tag === this.activeFilter);
            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase();
                matchSearch = release.title.toLowerCase().includes(q) || release.description.toLowerCase().includes(q) || release.changes.some(c => c.text.toLowerCase().includes(q));
            }
            return matchCategory && matchSearch;
        },

        updateFilteredCount() {
            setTimeout(() => {
                let count = 0;
                document.querySelectorAll('.release-item').forEach(item => { if (item.style.display !== 'none') count++; });
                this.filteredCount = count;
            }, 300);
        },

        copyLink(elementId) {
            this.playClick();
            const url = window.location.origin + window.location.pathname + '#' + elementId;
            navigator.clipboard.writeText(url)
                .then(() => this.$dispatch('notify', { message: 'Release permalink copied to clipboard.', type: 'success' }))
                .catch(() => this.$dispatch('notify', { message: 'Failed to copy link.', type: 'error' }));
        }
    }));
});
</script>
@endpush
