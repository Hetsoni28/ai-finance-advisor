@extends('layouts.landing')

@section('title', 'Engineering Blog | FinanceAI Enterprise')
@section('meta_description', 'Deep technical insights from the FinanceAI engineering team on AI, cryptography, Laravel architecture, and fintech infrastructure.')

@section('content')

@php
    // ================= 1. ENTERPRISE SSR PAYLOAD (ZERO EXTERNAL IMAGES) =================
    
    $featuredPost = [
        'id' => 'post-feat-1',
        'title' => 'Building a RAG-Powered Financial Assistant with Gemini & Laravel',
        'excerpt' => 'How we architected a Retrieval-Augmented Generation pipeline that processes real-time market data, user financial context, and delivers personalized AI insights with 99.9% accuracy.',
        'date' => 'April 18, 2026',
        'readTime' => '12 min',
        'author' => 'Het Soni',
        'role' => 'Chief Architect',
        'avatar' => '11',
        'pattern' => 'pattern-grid-lg', // Pure CSS Pattern
        'tags' => ['AI/ML', 'Laravel', 'Architecture']
    ];

    $posts = [
        [
            'id' => 'post-1',
            'title' => 'How We Reduced Dashboard Query Time by 40%',
            'excerpt' => 'A deep dive into Eloquent optimization, indexed composite keys, and memory-safe caching strategies in our strict-mode MySQL 8 environment.',
            'date' => 'April 10, 2026',
            'readTime' => '8 min',
            'author' => 'Het Soni',
            'avatar' => '11',
            'pattern' => 'pattern-dots',
            'tags' => ['Laravel', 'Performance', 'Database']
        ],
        [
            'id' => 'post-2',
            'title' => 'Subscription Access Control with Custom Middleware',
            'excerpt' => 'Building highly secure, feature-gated middleware that integrates with our 3-tier SaaS plan system for granular, impenetrable access control.',
            'date' => 'March 28, 2026',
            'readTime' => '6 min',
            'author' => 'David R.',
            'avatar' => '33',
            'pattern' => 'pattern-circuit',
            'tags' => ['Laravel', 'Architecture', 'Security']
        ],
        [
            'id' => 'post-3',
            'title' => 'Geolocation Tracking in Contact Forms with Leaflet.js',
            'excerpt' => 'Implementing browser geolocation capture, storing coordinates securely via AJAX, and visualizing them on Admin dashboard maps.',
            'date' => 'March 15, 2026',
            'readTime' => '5 min',
            'author' => 'Elena P.',
            'avatar' => '44',
            'pattern' => 'pattern-waves',
            'tags' => ['Frontend', 'Architecture']
        ],
        [
            'id' => 'post-4',
            'title' => 'Securing Financial Data: Our Encryption Strategy',
            'excerpt' => 'From AES-256 at rest to TLS 1.3 in transit — how we engineer zero-knowledge financial data protection utilizing native Laravel Facades.',
            'date' => 'February 22, 2026',
            'readTime' => '10 min',
            'author' => 'Marcus C.',
            'avatar' => '68',
            'pattern' => 'pattern-isometric',
            'tags' => ['Security', 'Cryptography']
        ],
        [
            'id' => 'post-5',
            'title' => 'Real-Time Notifications with Laravel Events',
            'excerpt' => 'How our deployment pipeline leverages database channels, Alpine.js polling, and global event dispatchers for instant, socket-free alerts.',
            'date' => 'January 30, 2026',
            'readTime' => '7 min',
            'author' => 'Het Soni',
            'avatar' => '11',
            'pattern' => 'pattern-boxes',
            'tags' => ['Laravel', 'Frontend']
        ],
        [
            'id' => 'post-6',
            'title' => 'The Spatie Activity Log: Auditing Every Admin Action',
            'excerpt' => 'Implementing comprehensive, mathematically immutable audit trails for our Master Node admin panel using Spatie\'s powerful activity logging package.',
            'date' => 'December 12, 2025',
            'readTime' => '5 min',
            'author' => 'Sarah J.',
            'avatar' => '12',
            'pattern' => 'pattern-hex',
            'tags' => ['Security', 'Database']
        ]
    ];

    $allTags = [];
    foreach (array_merge([$featuredPost], $posts) as $p) {
        $allTags = array_merge($allTags, $p['tags']);
    }
    $uniqueTags = array_unique($allTags);
    sort($uniqueTags);
@endphp

<div class="bg-[#fcf9f2] font-sans selection:bg-[#bacdf3] selection:text-[#0f172a] relative overflow-hidden min-h-screen flex flex-col pt-24"
     x-data="blogEngine()">

    {{-- Ambient Backgrounds (Fintech Light Palette) --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[1000px] h-[1000px] bg-[#bacdf3]/30 blur-[150px] rounded-full animate-float"></div>
        <div class="absolute bottom-[20%] right-[-10%] w-[800px] h-[800px] bg-[#9fb2df]/20 blur-[120px] rounded-full animate-float" style="animation-delay: -3s;"></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.03]"></div>
    </div>

    {{-- ================= 1. HERO & SEARCH CONSOLE ================= --}}
    <section class="relative pt-16 pb-16 lg:pt-24 lg:pb-24 overflow-hidden z-10 border-b border-[#bacdf3]/40 bg-white/60 backdrop-blur-3xl">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#fcf9f2] border border-[#bacdf3] text-[#7284b5] text-[10px] font-black uppercase tracking-widest mb-8 shadow-sm reveal-up">
                <i class="fa-solid fa-microchip text-[#9fb2df]"></i> Architectural Insights
            </div>

            <h1 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tight leading-[1.05] reveal-up" style="transition-delay: 100ms;">
                The FinanceAI <br>
                <span class="bg-gradient-to-r from-[#7284b5] via-[#879ac9] to-[#bacdf3] bg-clip-text text-transparent">Engineering Blog.</span>
            </h1>
            
            <p class="mt-6 text-lg text-slate-500 font-medium leading-relaxed max-w-2xl mx-auto reveal-up" style="transition-delay: 200ms;">
                Deep dives into AI heuristics, cryptographic architecture, and the engineering decisions behind scaling a real-time financial intelligence platform.
            </p>

            {{-- Floating Reading List Counter --}}
            <div class="absolute top-0 right-4 md:right-8 reveal-up" style="transition-delay: 300ms;">
                <button @click="toggleReadingList()" @mouseenter="playHover()" class="magnetic-target relative flex items-center gap-3 px-5 py-3 bg-white border border-[#bacdf3] rounded-2xl shadow-[0_10px_30px_rgba(114,132,181,0.15)] hover:bg-[#fcf9f2] transition-all cursor-none focus:outline-none">
                    <i class="fa-solid fa-bookmark text-[#7284b5]"></i>
                    <span class="text-xs font-black uppercase tracking-widest text-[#7284b5]">Reading List</span>
                    <span class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-rose-500 text-white text-[10px] font-black flex items-center justify-center shadow-md transform transition-transform" 
                          :class="savedPosts.length > 0 ? 'scale-100' : 'scale-0'" x-text="savedPosts.length"></span>
                </button>
            </div>

            {{-- Sticky Search & Filter Toolbar --}}
            <div class="mt-16 max-w-4xl mx-auto bg-white p-2 rounded-[1.5rem] border border-[#bacdf3]/60 shadow-[0_20px_50px_-20px_rgba(114,132,181,0.2)] flex flex-col md:flex-row items-center gap-4 reveal-up sticky-toolbar transition-all duration-300 z-50" style="transition-delay: 300ms;" id="searchBar">
                
                {{-- Search Input --}}
                <div class="relative w-full md:w-1/2 flex-shrink-0 group/search">
                    <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-[#9fb2df] group-focus-within/search:text-[#7284b5] transition-colors"></i>
                    <input type="text" x-model="searchQuery" @input="playKeySound()" placeholder="Search articles, tags, or authors..." 
                           class="w-full pl-12 pr-4 py-3.5 bg-[#fcf9f2]/50 border border-transparent focus:border-[#bacdf3] focus:bg-white rounded-xl text-sm font-bold text-slate-900 placeholder-slate-400 outline-none transition-all focus:ring-4 focus:ring-[#bacdf3]/20">
                </div>

                {{-- Horizontal Scrollable Tag Filter --}}
                <div class="flex overflow-x-auto w-full scrollbar-hide gap-2 p-1">
                    <button @click="setFilter('All')" @mouseenter="playHover()"
                            class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest border transition-all focus:outline-none whitespace-nowrap flex-shrink-0"
                            :class="activeTag === 'All' ? 'bg-[#7284b5] text-white border-[#7284b5] shadow-md' : 'bg-transparent text-slate-500 border-transparent hover:bg-[#fcf9f2] hover:text-[#7284b5]'">
                        All
                    </button>
                    @foreach($uniqueTags as $tag)
                        <button @click="setFilter('{{ $tag }}')" @mouseenter="playHover()"
                                class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest border transition-all focus:outline-none whitespace-nowrap flex-shrink-0"
                                :class="activeTag === '{{ $tag }}' ? 'bg-[#7284b5] text-white border-[#7284b5] shadow-md' : 'bg-transparent text-slate-500 border-transparent hover:bg-[#fcf9f2] hover:text-[#7284b5]'">
                            {{ $tag }}
                        </button>
                    @endforeach
                </div>
            </div>

        </div>
    </section>

    {{-- ================= 2. FEATURED POST (PURE CSS BENTO) ================= --}}
    <section class="py-16 relative z-10" x-show="isFeaturedVisible()">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
            <article class="bg-white rounded-[3rem] border border-[#bacdf3]/50 shadow-[0_20px_60px_-15px_rgba(114,132,181,0.15)] overflow-hidden group hover:shadow-[0_30px_80px_-20px_rgba(114,132,181,0.25)] transition-all duration-500 reveal-up">
                <div class="grid lg:grid-cols-2 h-full">
                    
                    {{-- CSS Generative Pattern Half --}}
                    <div class="relative h-[300px] lg:h-auto overflow-hidden {{ $featuredPost['pattern'] }} bg-[#fcf9f2]">
                        <div class="absolute inset-0 bg-gradient-to-br from-[#7284b5]/10 to-[#bacdf3]/20 mix-blend-multiply z-10 group-hover:scale-105 transition-transform duration-1000"></div>
                        
                        {{-- Simulated Code Block --}}
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-3/4 bg-[#0f172a] rounded-2xl p-6 shadow-2xl border border-slate-700 z-20 group-hover:-translate-y-[55%] transition-transform duration-700">
                            <div class="flex gap-2 mb-4"><div class="w-3 h-3 rounded-full bg-rose-500"></div><div class="w-3 h-3 rounded-full bg-amber-500"></div><div class="w-3 h-3 rounded-full bg-emerald-500"></div></div>
                            <div class="font-mono text-xs leading-loose">
                                <span class="text-pink-400">public function</span> <span class="text-emerald-400">analyzeLedger</span><span class="text-white">(Request $req) {</span><br>
                                &nbsp;&nbsp;<span class="text-slate-400">// Trigger RAG Pipeline</span><br>
                                &nbsp;&nbsp;<span class="text-indigo-400">$context</span> <span class="text-white">= VectorDB::search(</span><span class="text-amber-300">'transactions'</span><span class="text-white">);</span><br>
                                &nbsp;&nbsp;<span class="text-pink-400">return</span> <span class="text-white">Gemini::stream(</span><span class="text-indigo-400">$context</span><span class="text-white">);</span><br>
                                <span class="text-white">}</span>
                            </div>
                        </div>

                        <div class="absolute top-6 left-6 z-20 flex items-center gap-3">
                            <span class="px-3 py-1.5 bg-white/90 backdrop-blur-md text-[#7284b5] text-[9px] font-black uppercase tracking-widest rounded-lg border border-[#bacdf3] shadow-lg flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Featured Post
                            </span>
                        </div>
                    </div>
                    
                    {{-- Featured Content Half --}}
                    <div class="p-10 lg:p-16 flex flex-col justify-center relative bg-white">
                        <div class="absolute right-0 top-0 w-64 h-64 bg-[#bacdf3]/20 rounded-full blur-[80px] pointer-events-none group-hover:bg-[#bacdf3]/40 transition-colors duration-700"></div>
                        
                        <div class="relative z-10">
                            <div class="flex flex-wrap gap-2 mb-6">
                                @foreach($featuredPost['tags'] as $tag)
                                    <span class="px-2.5 py-1 bg-[#fcf9f2] border border-[#bacdf3] text-[#7284b5] text-[9px] font-black uppercase tracking-widest rounded-md shadow-sm">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                            </div>

                            <a href="#" @mouseenter="playHover()" class="block focus:outline-none group/title">
                                <h2 class="text-3xl lg:text-5xl font-black text-slate-900 tracking-tight mb-6 leading-[1.1] group-hover/title:text-[#7284b5] transition-colors">
                                    {{ $featuredPost['title'] }}
                                </h2>
                            </a>
                            
                            <p class="text-slate-600 font-medium text-lg leading-relaxed mb-10 max-w-xl">
                                {{ $featuredPost['excerpt'] }}
                            </p>

                            <div class="flex items-center justify-between pt-8 border-t border-[#bacdf3]/50">
                                <div class="flex items-center gap-4">
                                    <img src="https://i.pravatar.cc/150?img={{ $featuredPost['avatar'] }}" class="w-12 h-12 rounded-full border-2 border-[#bacdf3] shadow-sm" alt="{{ $featuredPost['author'] }}">
                                    <div>
                                        <p class="text-sm font-black text-slate-900">{{ $featuredPost['author'] }}</p>
                                        <p class="text-[10px] font-bold text-[#7284b5] uppercase tracking-widest mt-0.5">{{ $featuredPost['role'] }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button @click="toggleBookmark('{{ $featuredPost['id'] }}')" @mouseenter="playHover()" class="w-10 h-10 rounded-full border border-[#bacdf3] flex items-center justify-center transition-colors focus:outline-none shadow-sm" :class="isBookmarked('{{ $featuredPost['id'] }}') ? 'bg-[#7284b5] text-white' : 'bg-[#fcf9f2] text-[#7284b5] hover:bg-white hover:text-indigo-600'" title="Save to Reading List">
                                        <i class="fa-solid fa-bookmark text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </section>

    {{-- ================= 3. ALL POSTS GRID (SSR + ALPINE + CSS PATTERNS) ================= --}}
    <section class="pb-32 relative z-10 flex-1">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($posts as $idx => $post)
                    <article class="post-card bg-white rounded-[2.5rem] border border-[#bacdf3]/40 shadow-sm hover:shadow-[0_20px_50px_-10px_rgba(114,132,181,0.15)] hover:-translate-y-2 transition-all duration-500 flex flex-col h-full overflow-hidden group reveal-up"
                             data-title="{{ strtolower($post['title']) }}"
                             data-excerpt="{{ strtolower($post['excerpt']) }}"
                             data-tags="{{ strtolower(implode(',', $post['tags'])) }}"
                             x-show="matchesFilter($el)"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             style="transition-delay: {{ $idx * 50 }}ms;">
                        
                        {{-- CSS Pattern Header instead of heavy images --}}
                        <div class="relative h-48 w-full overflow-hidden bg-[#fcf9f2] {{ $post['pattern'] }} border-b border-[#bacdf3]/30">
                            <div class="absolute inset-0 bg-gradient-to-t from-white via-white/50 to-transparent z-10"></div>
                            
                            {{-- Reading Progress Simulator Hover Effect --}}
                            <div class="absolute bottom-0 left-0 h-1 bg-[#7284b5] w-0 group-hover:w-full transition-all duration-1000 ease-out z-20"></div>

                            {{-- Action Bar --}}
                            <div class="absolute top-4 right-4 z-20 flex gap-2 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                                <button @click.prevent="copyLink('{{ $post['id'] }}')" @mouseenter="playHover()" class="w-8 h-8 rounded-lg bg-white/90 backdrop-blur-md border border-[#bacdf3] text-[#7284b5] hover:bg-[#7284b5] hover:text-white flex items-center justify-center transition-colors shadow-sm focus:outline-none" title="Copy Permalink">
                                    <i class="fa-solid fa-link text-xs"></i>
                                </button>
                                <button @click.prevent="toggleBookmark('{{ $post['id'] }}')" @mouseenter="playHover()" class="w-8 h-8 rounded-lg border flex items-center justify-center transition-colors shadow-sm focus:outline-none" :class="isBookmarked('{{ $post['id'] }}') ? 'bg-[#7284b5] border-[#7284b5] text-white' : 'bg-white/90 backdrop-blur-md border-[#bacdf3] text-[#7284b5] hover:bg-[#7284b5] hover:text-white hover:border-[#7284b5]'" title="Save Post">
                                    <i class="fa-solid fa-bookmark text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <div class="p-8 flex-1 flex flex-col relative bg-white">
                            <div class="flex flex-wrap gap-2 mb-5">
                                @foreach($post['tags'] as $tag)
                                    <span class="text-[8px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md border bg-[#fcf9f2] text-[#7284b5] border-[#bacdf3] shadow-sm">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                            </div>

                            <a href="#" @mouseenter="playHover()" class="block focus:outline-none group/title flex-1">
                                <h3 class="text-xl font-black text-slate-900 mb-4 group-hover/title:text-[#7284b5] transition-colors leading-tight">
                                    {{ $post['title'] }}
                                </h3>
                                <p class="text-sm text-slate-500 font-medium leading-relaxed mb-6">
                                    {{ $post['excerpt'] }}
                                </p>
                            </a>

                            {{-- Author Footer --}}
                            <div class="flex items-center justify-between pt-6 border-t border-slate-100 mt-auto">
                                <div class="flex items-center gap-3">
                                    <img src="https://i.pravatar.cc/150?img={{ $post['avatar'] }}" class="w-8 h-8 rounded-full border border-slate-200 shadow-sm" alt="{{ $post['author'] }}">
                                    <div>
                                        <p class="text-xs font-bold text-slate-900">{{ $post['author'] }}</p>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-0.5">{{ $post['date'] }}</p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-[#7284b5] flex items-center gap-1.5 bg-[#bacdf3]/20 px-2 py-1 rounded border border-[#bacdf3]/50">
                                    <i class="fa-regular fa-clock"></i> {{ $post['readTime'] }}
                                </span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Empty State (If Search Yields Nothing) --}}
            <div x-show="filteredCount === 0" style="display: none;" class="py-20 w-full animate-fade-in-up">
                <div class="max-w-2xl mx-auto bg-white rounded-[3rem] border border-dashed border-[#bacdf3] p-16 text-center shadow-sm">
                    <div class="w-20 h-20 bg-[#fcf9f2] border border-[#bacdf3] rounded-3xl flex items-center justify-center mx-auto mb-6 text-[#9fb2df] shadow-inner">
                        <i class="fa-solid fa-ghost text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-3 tracking-tight">No engineering notes found.</h3>
                    <p class="text-base font-medium text-slate-500 mb-8 max-w-sm mx-auto">Try adjusting your search terms or clearing the category filters to find what you're looking for.</p>
                    <button @click="searchQuery = ''; activeTag = 'All'; playClick()" class="magnetic-target px-6 py-3 bg-[#7284b5] text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-md hover:bg-[#616dab] transition-colors focus:outline-none cursor-none">
                        Clear All Filters
                    </button>
                </div>
            </div>

        </div>
    </section>

    {{-- ================= 4. HACKER TERMINAL NEWSLETTER CTA ================= --}}
    <section class="py-24 px-4 sm:px-6 lg:px-8 relative z-10 border-t border-[#bacdf3]/40 bg-[#fcf9f2]">
        <div class="max-w-4xl mx-auto bg-[#0f172a] rounded-[3rem] border border-slate-700 p-2 shadow-[0_30px_80px_-15px_rgba(114,132,181,0.4)] reveal-up">
            
            {{-- Terminal Window Wrapper --}}
            <div class="bg-[#1e293b] rounded-[2.5rem] border border-slate-800 overflow-hidden flex flex-col h-[400px]">
                
                {{-- Mac Header --}}
                <div class="bg-slate-900/80 px-6 py-4 flex items-center justify-between border-b border-slate-800">
                    <div class="flex gap-2">
                        <div class="w-3 h-3 rounded-full bg-rose-500 border border-rose-600"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-500 border border-amber-600"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-500 border border-emerald-600"></div>
                    </div>
                    <span class="text-[10px] font-mono text-slate-500 font-bold tracking-widest">bash — dev@financeai:~/newsletter</span>
                    <div class="w-10"></div> {{-- Spacer for flex --}}
                </div>

                {{-- Terminal Body --}}
                <div class="p-8 font-mono text-sm flex-1 flex flex-col relative" @click="focusTerminal()">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10 pointer-events-none"></div>
                    
                    <div class="mb-6 relative z-10">
                        <p class="text-emerald-400 font-bold mb-1">Welcome to the FinanceAI Core.</p>
                        <p class="text-slate-400">Subscribe to receive architectural post-mortems and system patches directly to your inbox. Zero spam.</p>
                    </div>

                    <div class="space-y-2 relative z-10 flex-1 overflow-y-auto scrollbar-hide" id="term-output">
                        <template x-for="log in terminalLogs" :key="log.id">
                            <div x-html="log.html" class="animate-fade-in-up text-slate-300"></div>
                        </template>

                        <div x-show="subscribeState === 'idle'" class="flex items-center mt-4">
                            <span class="text-indigo-400 font-bold mr-3 flex-shrink-0">dev@node:~$</span>
                            <span class="text-sky-300 mr-2 flex-shrink-0">subscribe</span>
                            <input type="text" x-model="email" x-ref="termInput" @keydown.enter="submitTerminal" @keydown="playKeySound()" placeholder="enter_email_here" 
                                   class="flex-1 bg-transparent border-none outline-none text-white font-bold focus:ring-0 p-0 placeholder-slate-600" spellcheck="false" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

</div>

{{-- Slide-Over Reading List Panel --}}
<div x-show="showReadingList" x-cloak class="fixed inset-0 z-[9999] flex justify-end">
    <div x-show="showReadingList" x-transition.opacity @click="toggleReadingList()" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
    <div x-show="showReadingList" 
         x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
         class="relative w-full max-w-md bg-[#fcf9f2] h-full shadow-2xl flex flex-col border-l border-[#bacdf3]">
        
        <div class="px-6 py-5 border-b border-[#bacdf3]/50 flex items-center justify-between bg-white">
            <span class="text-lg font-black text-slate-900 tracking-tight flex items-center gap-2"><i class="fa-solid fa-bookmark text-[#7284b5]"></i> Reading List</span>
            <button @click="toggleReadingList()" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-50 border border-slate-200 text-slate-500 hover:bg-rose-50 hover:text-rose-500 transition-colors focus:outline-none shadow-sm"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-4">
            <template x-if="savedPosts.length === 0">
                <div class="text-center py-10">
                    <div class="w-16 h-16 bg-white border border-[#bacdf3] rounded-2xl flex items-center justify-center mx-auto mb-4 text-[#9fb2df] shadow-sm"><i class="fa-solid fa-book-open text-2xl"></i></div>
                    <p class="text-sm font-bold text-slate-500">Your reading list is empty.</p>
                </div>
            </template>
            
            <template x-for="id in savedPosts" :key="id">
                <div class="bg-white p-4 rounded-2xl border border-[#bacdf3]/50 shadow-sm flex items-start justify-between gap-4 group">
                    <div>
                        <span class="text-[8px] font-black uppercase tracking-widest text-[#7284b5] bg-[#bacdf3]/20 px-1.5 py-0.5 rounded border border-[#bacdf3]/50 mb-2 inline-block">Saved Article</span>
                        <h4 class="text-sm font-black text-slate-900 leading-tight" x-text="getPostTitle(id)"></h4>
                    </div>
                    <button @click="toggleBookmark(id)" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center shrink-0 border border-rose-100 hover:bg-rose-500 hover:text-white transition-colors focus:outline-none" title="Remove"><i class="fa-solid fa-trash text-xs"></i></button>
                </div>
            </template>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .reveal-up { opacity: 0; transform: translateY(30px); transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
    .reveal-up.is-visible { opacity: 1; transform: translateY(0); }
    
    @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-20px); } }
    .animate-float { animation: float 8s ease-in-out infinite; }

    /* Sticky Search Bar Transition */
    .is-sticky { position: fixed; top: 80px; left: 50%; transform: translateX(-50%); width: 90%; max-w: 800px; z-index: 100; background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); box-shadow: 0 20px 50px rgba(114,132,181,0.3); }

    /* PURE CSS ENGINEERING PATTERNS (Replaces Images) */
    .pattern-grid-lg { background-image: linear-gradient(#bacdf3 1px, transparent 1px), linear-gradient(90deg, #bacdf3 1px, transparent 1px); background-size: 40px 40px; }
    .pattern-dots { background-image: radial-gradient(#9fb2df 2px, transparent 2px); background-size: 20px 20px; }
    .pattern-waves { background: repeating-radial-gradient(circle at 0 0, transparent 0, #fcf9f2 10px, transparent 10px, #bacdf3 20px); }
    .pattern-boxes { background-image: linear-gradient(#bacdf3 2px, transparent 2px), linear-gradient(90deg, #bacdf3 2px, transparent 2px), linear-gradient(#bacdf3 1px, transparent 1px), linear-gradient(90deg, #bacdf3 1px, transparent 1px); background-size: 50px 50px, 50px 50px, 10px 10px, 10px 10px; background-position: -2px -2px, -2px -2px, -1px -1px, -1px -1px; }
    .pattern-circuit { background-image: radial-gradient(circle at center, #bacdf3 2px, transparent 2.5px), linear-gradient(0deg, transparent 48%, #bacdf3 48%, #bacdf3 52%, transparent 52%), linear-gradient(90deg, transparent 48%, #bacdf3 48%, #bacdf3 52%, transparent 52%); background-size: 40px 40px; background-position: 0 0, 20px 20px, 20px 20px; }
    .pattern-isometric { background-image: linear-gradient(30deg, #bacdf3 12%, transparent 12.5%, transparent 87%, #bacdf3 87.5%, #bacdf3), linear-gradient(150deg, #bacdf3 12%, transparent 12.5%, transparent 87%, #bacdf3 87.5%, #bacdf3), linear-gradient(30deg, #bacdf3 12%, transparent 12.5%, transparent 87%, #bacdf3 87.5%, #bacdf3), linear-gradient(150deg, #bacdf3 12%, transparent 12.5%, transparent 87%, #bacdf3 87.5%, #bacdf3), linear-gradient(60deg, #9fb2df77 25%, transparent 25.5%, transparent 75%, #9fb2df77 75%, #9fb2df77), linear-gradient(60deg, #9fb2df77 25%, transparent 25.5%, transparent 75%, #9fb2df77 75%, #9fb2df77); background-size: 40px 70px; background-position: 0 0, 0 0, 20px 35px, 20px 35px, 0 0, 20px 35px; }
    .pattern-hex { background-image: radial-gradient(circle at 50% 50%, transparent 60%, #fcf9f2 60%, #fcf9f2 100%), conic-gradient(from 30deg at 50% 50%, #bacdf3 0deg, #bacdf3 60deg, transparent 60deg, transparent 120deg, #bacdf3 120deg, #bacdf3 180deg, transparent 180deg, transparent 240deg, #bacdf3 240deg, #bacdf3 300deg, transparent 300deg, transparent 360deg); background-size: 40px 69.28px; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('blogEngine', () => ({
        activeTag: 'All',
        searchQuery: '',
        filteredCount: -1,
        
        // Reading List (Local Storage)
        savedPosts: [],
        showReadingList: false,
        allPostData: @json(array_merge([$featuredPost], $posts)),

        // Terminal Newsletter
        email: '',
        subscribeState: 'idle',
        terminalLogs: [],
        logCounter: 0,

        init() {
            // Load saved posts from local storage
            const stored = localStorage.getItem('financeai_reading_list');
            if (stored) this.savedPosts = JSON.parse(stored);

            // Scroll Animation Observer
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('is-visible'); });
            }, { threshold: 0.1 });
            document.querySelectorAll('.reveal-up').forEach(el => observer.observe(el));

            // Sticky Search Bar Logic
            const searchBar = document.getElementById('searchBar');
            const searchOffset = searchBar.offsetTop;
            window.addEventListener('scroll', () => {
                if (window.scrollY > searchOffset + 100) {
                    searchBar.classList.add('is-sticky');
                } else {
                    searchBar.classList.remove('is-sticky');
                }
            });

            this.$watch('searchQuery', () => this.updateFilteredCount());
            this.$watch('activeTag', () => this.updateFilteredCount());
            this.$nextTick(() => this.updateFilteredCount());
        },

        playClick() { if(window.audioEngine) window.audioEngine.playClick(); },
        playHover() { if(window.audioEngine) window.audioEngine.playHover(); },
        playSuccess() { if(window.audioEngine) window.audioEngine.playSuccess(); },
        playKeySound() {
            if(!window.audioEngine) return;
            if(!this.lastTick || Date.now() - this.lastTick > 50) {
                window.audioEngine.playClick(); 
                this.lastTick = Date.now();
            }
        },

        setFilter(tag) {
            this.playClick();
            this.activeTag = tag;
        },

        // DOM-based filtering for SSR
        matchesFilter(el) {
            let matchTag = true;
            let matchSearch = true;

            const title = el.getAttribute('data-title') || '';
            const excerpt = el.getAttribute('data-excerpt') || '';
            const tags = el.getAttribute('data-tags') || '';

            if (this.activeTag !== 'All') matchTag = tags.includes(this.activeTag.toLowerCase());
            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase();
                matchSearch = title.includes(q) || excerpt.includes(q) || tags.includes(q);
            }

            return matchTag && matchSearch;
        },

        isFeaturedVisible() {
            if (this.searchQuery.trim() !== '' || this.activeTag !== 'All') return false;
            return true;
        },

        updateFilteredCount() {
            let count = 0;
            const items = document.querySelectorAll('.post-card');
            setTimeout(() => {
                items.forEach(item => { if (item.style.display !== 'none') count++; });
                this.filteredCount = count;
            }, 50);
        },

        copyLink(id) {
            this.playClick();
            const url = window.location.origin + window.location.pathname + '#' + id;
            navigator.clipboard.writeText(url).then(() => {
                this.$dispatch('notify', { message: 'Article permalink copied to clipboard.', type: 'success' });
            });
        },

        // ================= READING LIST LOGIC =================
        toggleReadingList() {
            this.playClick();
            this.showReadingList = !this.showReadingList;
        },

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
            localStorage.setItem('financeai_reading_list', JSON.stringify(this.savedPosts));
        },

        isBookmarked(id) {
            return this.savedPosts.includes(id);
        },

        getPostTitle(id) {
            const post = this.allPostData.find(p => p.id === id);
            return post ? post.title : 'Unknown Article';
        },

        // ================= TERMINAL NEWSLETTER LOGIC =================
        focusTerminal() {
            if(this.$refs.termInput) this.$refs.termInput.focus();
        },

        addTermLog(html) {
            this.terminalLogs.push({ id: this.logCounter++, html: html });
            this.$nextTick(() => {
                const container = document.getElementById('term-output');
                if(container) container.scrollTop = container.scrollHeight;
            });
        },

        submitTerminal() {
            if(this.email.trim() === '' || !this.email.includes('@')) {
                this.addTermLog(`<span class="text-rose-400 font-bold">Error:</span> Invalid email payload format.`);
                this.playClick();
                return;
            }
            
            this.playClick();
            this.subscribeState = 'loading';
            const inputEmail = this.email;
            this.email = '';
            
            this.addTermLog(`<span class="text-sky-300">subscribe</span> <span class="text-slate-300">${inputEmail}</span>`);
            this.addTermLog(`<span class="text-slate-500">Initiating handshake protocol...</span>`);
            
            setTimeout(() => {
                this.addTermLog(`<span class="text-emerald-400 font-bold">SUCCESS:</span> Payload secured. Subscription active for ${inputEmail}.`);
                this.subscribeState = 'idle';
                this.playSuccess();
                this.$dispatch('notify', { message: 'Terminal subscription successful.', type: 'success' });
            }, 1500);
        }
    }));
});
</script>
@endpush