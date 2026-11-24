@extends('layouts.landing')
@include('partials.site-type')

@section('title', 'Engineering Blog | FinanceAI Enterprise')
@section('meta_description', 'Deep technical insights from the FinanceAI engineering team on AI, cryptography, Laravel architecture, and fintech infrastructure.')

@section('content')
@php
    $featuredPost = [
        'id' => 'post-feat-1',
        'title' => 'Building a RAG-Powered Financial Assistant with Gemini & Laravel',
        'excerpt' => 'How we architected a Retrieval-Augmented Generation pipeline that processes real-time market data, user financial context, and delivers personalized AI insights with 99.9% accuracy.',
        'date' => 'April 18, 2026',
        'readTime' => '12 min',
        'author' => 'Het Soni',
        'role' => 'Chief Architect',
        'avatar' => '11',
        'tags' => ['AI/ML', 'Laravel', 'Architecture'],
    ];

    $posts = [
        ['id' => 'post-1', 'title' => 'How We Reduced Dashboard Query Time by 40%', 'excerpt' => 'A deep dive into Eloquent optimization, indexed composite keys, and memory-safe caching strategies in our strict-mode MySQL 8 environment.', 'date' => 'April 10, 2026', 'readTime' => '8 min', 'author' => 'Het Soni', 'avatar' => '11', 'tags' => ['Laravel', 'Performance', 'Database']],
        ['id' => 'post-2', 'title' => 'Subscription Access Control with Custom Middleware', 'excerpt' => 'Building highly secure, feature-gated middleware that integrates with our 3-tier SaaS plan system for granular, impenetrable access control.', 'date' => 'March 28, 2026', 'readTime' => '6 min', 'author' => 'David R.', 'avatar' => '33', 'tags' => ['Laravel', 'Architecture', 'Security']],
        ['id' => 'post-3', 'title' => 'Geolocation Tracking in Contact Forms with Leaflet.js', 'excerpt' => 'Implementing browser geolocation capture, storing coordinates securely via AJAX, and visualizing them on Admin dashboard maps.', 'date' => 'March 15, 2026', 'readTime' => '5 min', 'author' => 'Elena P.', 'avatar' => '44', 'tags' => ['Frontend', 'Architecture']],
        ['id' => 'post-4', 'title' => 'Securing Financial Data: Our Encryption Strategy', 'excerpt' => 'From AES-256 at rest to TLS 1.3 in transit — how we engineer zero-knowledge financial data protection utilizing native Laravel Facades.', 'date' => 'February 22, 2026', 'readTime' => '10 min', 'author' => 'Marcus C.', 'avatar' => '68', 'tags' => ['Security', 'Cryptography']],
        ['id' => 'post-5', 'title' => 'Real-Time Notifications with Laravel Events', 'excerpt' => 'How our deployment pipeline leverages database channels, Alpine.js polling, and global event dispatchers for instant, socket-free alerts.', 'date' => 'January 30, 2026', 'readTime' => '7 min', 'author' => 'Het Soni', 'avatar' => '11', 'tags' => ['Laravel', 'Frontend']],
        ['id' => 'post-6', 'title' => 'The Spatie Activity Log: Auditing Every Admin Action', 'excerpt' => 'Implementing comprehensive, mathematically immutable audit trails for our Master Node admin panel using Spatie\'s powerful activity logging package.', 'date' => 'December 12, 2025', 'readTime' => '5 min', 'author' => 'Sarah J.', 'avatar' => '12', 'tags' => ['Security', 'Database']],
    ];

    $allTags = [];
    foreach (array_merge([$featuredPost], $posts) as $p) { $allTags = array_merge($allTags, $p['tags']); }
    $uniqueTags = array_unique($allTags);
    sort($uniqueTags);
@endphp
<div class="paper" x-data="blogEngine()">

    <x-site.hero eyebrow="Architectural Insights" lead="The FinanceAI" accent="Engineering Blog."
        lede="Deep dives into AI heuristics, cryptographic architecture, and the engineering decisions behind scaling a real-time financial intelligence platform.">
        <x-slot name="actions">
            <button type="button" @click="toggleReadingList()" @mouseenter="playHover()" class="relative inline-flex items-center justify-center gap-2 h-12 px-6 rounded-xl font-bold text-white border border-white/25 bg-white/5 hover:bg-white/10 transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-white/20">
                <i class="fa-solid fa-bookmark text-sm" aria-hidden="true"></i> Reading List
                <span x-show="savedPosts.length > 0" x-cloak class="ml-1 font-mono text-xs text-indigo-300" x-text="'(' + savedPosts.length + ')'"></span>
            </button>
        </x-slot>
    </x-site.hero>

    {{-- Toolbar --}}
    <div class="sticky top-16 lg:top-[72px] z-30 bg-[#fbfbfa]/90 backdrop-blur border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-col md:flex-row md:items-center gap-3">
            <label class="relative md:w-80 shrink-0">
                <span class="sr-only">Search articles</span>
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400" aria-hidden="true"></i>
                <input type="text" x-model="searchQuery" @input="playKeySound()" placeholder="Search articles, tags, or authors..."
                       class="w-full h-10 pl-9 pr-3 rounded-lg border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
            </label>
            <div class="flex gap-1 overflow-x-auto scrollbar-hide" role="tablist" aria-label="Filter by tag">
                <button type="button" role="tab" @click="setFilter('All')" @mouseenter="playHover()" :aria-selected="activeTag === 'All'"
                        class="h-10 px-3.5 rounded-lg font-mono text-[11px] uppercase tracking-wider whitespace-nowrap transition-colors focus:outline-none"
                        :class="activeTag === 'All' ? 'bg-slate-900 text-white' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100'">All</button>
                @foreach($uniqueTags as $tag)
                    <button type="button" role="tab" @click="setFilter('{{ $tag }}')" @mouseenter="playHover()" :aria-selected="activeTag === '{{ $tag }}'"
                            class="h-10 px-3.5 rounded-lg font-mono text-[11px] uppercase tracking-wider whitespace-nowrap transition-colors focus:outline-none"
                            :class="activeTag === '{{ $tag }}' ? 'bg-slate-900 text-white' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100'">{{ $tag }}</button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Featured --}}
    <section class="pt-14 sm:pt-20" x-show="isFeaturedVisible()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <article id="{{ $featuredPost['id'] }}" class="grid lg:grid-cols-12 gap-10 items-center border-b border-slate-200 pb-14 sm:pb-20" data-reveal>
                <div class="lg:col-span-7">
                    <p class="eyebrow text-indigo-600 flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span> Featured Post</p>
                    <a href="#" @mouseenter="playHover()" class="group block mt-5 focus:outline-none">
                        <h2 class="text-3xl sm:text-5xl font-black tracking-[-0.03em] leading-[1.06] text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $featuredPost['title'] }}</h2>
                    </a>
                    <p class="mt-6 text-lg text-slate-600 leading-relaxed max-w-2xl">{{ $featuredPost['excerpt'] }}</p>
                    <ul class="mt-6 flex flex-wrap gap-x-4 gap-y-1 font-mono text-[11px] uppercase tracking-wider text-slate-500">
                        @foreach($featuredPost['tags'] as $tag)<li>#{{ $tag }}</li>@endforeach
                    </ul>
                    <div class="mt-8 pt-6 border-t border-slate-200 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <img src="https://i.pravatar.cc/150?img={{ $featuredPost['avatar'] }}" class="h-10 w-10 rounded-full grayscale" alt="{{ $featuredPost['author'] }}" loading="lazy">
                            <div>
                                <p class="text-sm font-bold text-slate-900">{{ $featuredPost['author'] }}</p>
                                <p class="font-mono text-[11px] uppercase tracking-wider text-slate-500">{{ $featuredPost['role'] }} · {{ $featuredPost['date'] }} · {{ $featuredPost['readTime'] }}</p>
                            </div>
                        </div>
                        <button type="button" @click="toggleBookmark('{{ $featuredPost['id'] }}')" @mouseenter="playHover()"
                                class="h-10 w-10 rounded-lg border transition-colors focus:outline-none"
                                :class="isBookmarked('{{ $featuredPost['id'] }}') ? 'bg-slate-900 border-slate-900 text-white' : 'border-slate-200 text-slate-400 hover:text-slate-900 hover:border-slate-900'"
                                title="Save to Reading List" aria-label="Save to Reading List"><i class="fa-solid fa-bookmark text-xs" aria-hidden="true"></i></button>
                    </div>
                </div>
                <div class="lg:col-span-5">
                    <div class="bg-[#0b0b1a] grain rounded-2xl p-6 sm:p-8 font-mono text-[13px] leading-loose" aria-hidden="true">
                        <p class="text-slate-500 text-[11px] uppercase tracking-wider mb-4">LedgerController.php</p>
                        <span class="text-pink-400">public function</span> <span class="text-emerald-400">analyzeLedger</span><span class="text-white">(Request $req) {</span><br>
                        &nbsp;&nbsp;<span class="text-slate-500">// Trigger RAG Pipeline</span><br>
                        &nbsp;&nbsp;<span class="text-indigo-300">$context</span> <span class="text-white">= VectorDB::search(</span><span class="text-amber-300">'transactions'</span><span class="text-white">);</span><br>
                        &nbsp;&nbsp;<span class="text-pink-400">return</span> <span class="text-white">Gemini::stream(</span><span class="text-indigo-300">$context</span><span class="text-white">);</span><br>
                        <span class="text-white">}</span>
                    </div>
                </div>
            </article>
        </div>
    </section>

    {{-- All posts --}}
    <section class="py-14 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-x-10 gap-y-14">
                @foreach($posts as $idx => $post)
                    <article id="{{ $post['id'] }}" class="post-card group flex flex-col border-t border-slate-900 pt-6"
                             data-title="{{ strtolower($post['title']) }}"
                             data-excerpt="{{ strtolower($post['excerpt']) }}"
                             data-tags="{{ strtolower(implode(',', $post['tags'])) }}"
                             x-show="matchesFilter($el)" x-transition.opacity.duration.250ms>
                        <div class="flex items-center justify-between">
                            <ul class="flex flex-wrap gap-x-3 font-mono text-[11px] uppercase tracking-wider text-slate-500">
                                @foreach($post['tags'] as $tag)<li>#{{ $tag }}</li>@endforeach
                            </ul>
                            <div class="flex gap-1 opacity-60 group-hover:opacity-100 transition-opacity">
                                <button type="button" @click.prevent="copyLink('{{ $post['id'] }}')" @mouseenter="playHover()" class="h-8 w-8 rounded-md text-slate-400 hover:text-slate-900 hover:bg-slate-100 focus:outline-none" title="Copy Permalink" aria-label="Copy link"><i class="fa-solid fa-link text-xs" aria-hidden="true"></i></button>
                                <button type="button" @click.prevent="toggleBookmark('{{ $post['id'] }}')" @mouseenter="playHover()" class="h-8 w-8 rounded-md focus:outline-none" :class="isBookmarked('{{ $post['id'] }}') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-900 hover:bg-slate-100'" title="Save Post" aria-label="Save post"><i class="fa-solid fa-bookmark text-xs" aria-hidden="true"></i></button>
                            </div>
                        </div>
                        <a href="#" @mouseenter="playHover()" class="mt-5 block flex-1 focus:outline-none">
                            <h3 class="text-2xl font-black tracking-tight leading-tight text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $post['title'] }}</h3>
                            <p class="mt-3 text-[15px] text-slate-600 leading-relaxed">{{ $post['excerpt'] }}</p>
                        </a>
                        <div class="mt-6 flex items-center gap-3">
                            <img src="https://i.pravatar.cc/150?img={{ $post['avatar'] }}" class="h-7 w-7 rounded-full grayscale" alt="{{ $post['author'] }}" loading="lazy">
                            <p class="text-sm font-semibold text-slate-900">{{ $post['author'] }}</p>
                            <p class="ml-auto font-mono text-[11px] uppercase tracking-wider text-slate-400">{{ $post['date'] }} · {{ $post['readTime'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>

            <div x-show="filteredCount === 0" x-cloak class="py-20 text-center">
                <p class="text-2xl font-black tracking-tight text-slate-900">No engineering notes found.</p>
                <p class="mt-2 text-slate-500 max-w-sm mx-auto">Try adjusting your search terms or clearing the category filters to find what you're looking for.</p>
                <button type="button" @click="searchQuery = ''; activeTag = 'All'; playClick()" class="mt-6 h-10 px-4 rounded-lg bg-slate-900 text-white text-sm font-semibold hover:bg-indigo-600 transition-colors focus:outline-none">Clear All Filters</button>
            </div>
        </div>
    </section>

    {{-- Reading list drawer --}}
    <div x-show="showReadingList" x-cloak class="fixed inset-0 z-[9999] flex justify-end" @keydown.escape.window="showReadingList = false">
        <div x-show="showReadingList" x-transition.opacity @click="toggleReadingList()" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
        <aside x-show="showReadingList"
               x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
               class="relative w-full max-w-md bg-[#fbfbfa] h-full shadow-2xl flex flex-col" role="dialog" aria-modal="true" aria-label="Reading List">
            <div class="px-6 h-16 border-b border-slate-200 flex items-center justify-between">
                <p class="text-lg font-black tracking-tight text-slate-900">Reading List</p>
                <button type="button" @click="toggleReadingList()" class="h-9 w-9 rounded-lg text-slate-500 hover:bg-slate-100 focus:outline-none" aria-label="Close"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
            </div>
            <div class="flex-1 overflow-y-auto px-6">
                <template x-if="savedPosts.length === 0">
                    <p class="py-12 text-center text-slate-500">Your reading list is empty.</p>
                </template>
                <template x-for="id in savedPosts" :key="id">
                    <div class="py-4 border-b border-slate-200 flex items-start justify-between gap-4">
                        <div>
                            <p class="font-mono text-[10px] uppercase tracking-wider text-slate-400">Saved Article</p>
                            <a :href="'#' + id" @click="showReadingList = false" class="mt-1 block font-bold text-slate-900 leading-snug hover:text-indigo-600" x-text="getPostTitle(id)"></a>
                        </div>
                        <button type="button" @click="toggleBookmark(id)" class="h-8 w-8 shrink-0 rounded-md text-slate-400 hover:text-rose-600 hover:bg-rose-50 focus:outline-none" title="Remove" aria-label="Remove"><i class="fa-solid fa-trash text-xs" aria-hidden="true"></i></button>
                    </div>
                </template>
            </div>
        </aside>
    </div>
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
    Alpine.data('blogEngine', () => ({
        activeTag: 'All',
        searchQuery: '',
        filteredCount: -1,
        savedPosts: [],
        showReadingList: false,
        allPostData: @json(array_merge([$featuredPost], $posts)),
        email: '',
        subscribeState: 'idle',
        terminalLogs: [],
        logCounter: 0,

        init() {
            try { const stored = localStorage.getItem('financeai_reading_list'); if (stored) this.savedPosts = JSON.parse(stored); } catch (e) {}
            this.$watch('searchQuery', () => this.updateFilteredCount());
            this.$watch('activeTag', () => this.updateFilteredCount());
            this.$nextTick(() => this.updateFilteredCount());
        },

        playClick() { if (window.audioEngine) window.audioEngine.playClick(); },
        playHover() { if (window.audioEngine) window.audioEngine.playHover(); },
        playSuccess() { if (window.audioEngine) window.audioEngine.playSuccess(); },
        playKeySound() {
            if (window.audioEngine && (!this.lastTick || Date.now() - this.lastTick > 50)) { window.audioEngine.playClick(); this.lastTick = Date.now(); }
        },

        setFilter(tag) { this.playClick(); this.activeTag = tag; },

        matchesFilter(el) {
            const title = el.getAttribute('data-title') || '';
            const excerpt = el.getAttribute('data-excerpt') || '';
            const tags = el.getAttribute('data-tags') || '';
            const matchTag = this.activeTag === 'All' || tags.includes(this.activeTag.toLowerCase());
            const q = this.searchQuery.trim().toLowerCase();
            const matchSearch = !q || title.includes(q) || excerpt.includes(q) || tags.includes(q);
            return matchTag && matchSearch;
        },

        isFeaturedVisible() { return this.searchQuery.trim() === '' && this.activeTag === 'All'; },

        updateFilteredCount() {
            setTimeout(() => {
                let count = 0;
                document.querySelectorAll('.post-card').forEach(item => { if (item.style.display !== 'none') count++; });
                this.filteredCount = count;
            }, 300);
        },

        copyLink(id) {
            this.playClick();
            const url = window.location.origin + window.location.pathname + '#' + id;
            navigator.clipboard.writeText(url).then(() => this.$dispatch('notify', { message: 'Article permalink copied to clipboard.', type: 'success' }));
        },

        toggleReadingList() { this.playClick(); this.showReadingList = !this.showReadingList; },

        toggleBookmark(id) {
            this.playClick();
            const index = this.savedPosts.indexOf(id);
            if (index > -1) {
                this.savedPosts.splice(index, 1);
                this.$dispatch('notify', { message: 'Removed from reading list.', type: 'info' });
            } else {
                this.savedPosts.push(id);
                this.$dispatch('notify', { message: 'Saved to reading list.', type: 'success' });
            }
            try { localStorage.setItem('financeai_reading_list', JSON.stringify(this.savedPosts)); } catch (e) {}
        },

        isBookmarked(id) { return this.savedPosts.includes(id); },

        getPostTitle(id) {
            const post = this.allPostData.find(p => p.id === id);
            return post ? post.title : 'Unknown Article';
        },

        focusTerminal() { if (this.$refs.termInput) this.$refs.termInput.focus(); },

        escape(s) { return String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c]); },

        addTermLog(html) {
            this.terminalLogs.push({ id: this.logCounter++, html });
            this.$nextTick(() => { const c = document.getElementById('term-output'); if (c) c.scrollTop = c.scrollHeight; });
        },

        submitTerminal() {
            if (this.email.trim() === '' || !this.email.includes('@')) {
                this.addTermLog(`<span class="text-rose-400">Error:</span> Invalid email payload format.`);
                this.playClick();
                return;
            }
            this.playClick();
            this.subscribeState = 'loading';
            const inputEmail = this.escape(this.email.trim());
            this.email = '';
            this.addTermLog(`<span class="text-sky-300">subscribe</span> <span class="text-slate-300">${inputEmail}</span>`);
            this.addTermLog(`<span class="text-slate-500">Initiating handshake protocol...</span>`);
            setTimeout(() => {
                this.addTermLog(`<span class="text-emerald-400">SUCCESS:</span> Payload secured. Subscription active for ${inputEmail}.`);
                this.subscribeState = 'idle';
                this.playSuccess();
                this.$dispatch('notify', { message: 'Terminal subscription successful.', type: 'success' });
            }, 1500);
        }
    }));
});
</script>
@endpush
