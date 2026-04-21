@extends('layouts.landing')

@section('title', 'System Changelog & Release Notes | FinanceAI Enterprise')
@section('meta_description', 'Track every deployment, feature release, and security patch in the FinanceAI architecture.')

@section('content')

@php
    // ================= ENTERPRISE DATA PAYLOAD (SSR FOR SEO) =================
    $metrics = [
        ['label' => 'Total Deployments', 'value' => '142', 'icon' => 'fa-rocket'],
        ['label' => 'Security Patches', 'value' => '38', 'icon' => 'fa-shield-halved'],
        ['label' => 'Platform Uptime', 'value' => '99.99%', 'icon' => 'fa-server'],
    ];

    $releases = [
        [
            'version' => '3.1.0',
            'date' => 'April 14, 2026',
            'type' => 'major',
            'title' => 'Contact Module & Geolocation Tracking',
            'description' => 'A complete overhaul of the public ingress system. We introduced a highly secure, rate-limited contact architecture with automated geographical routing.',
            'authors' => ['12', '33'],
            'changes' => [
                ['tag' => 'feature', 'text' => 'Full contact form with AJAX submission and secure geolocation capture.'],
                ['tag' => 'feature', 'text' => 'Admin contact inbox with KPI cards, read/unread toggle, and interactive Leaflet maps.'],
                ['tag' => 'improvement', 'text' => 'Enhanced admin sidebar with Contact Messages navigation tree.'],
                ['tag' => 'security', 'text' => 'Strict XSS protection via server-side strip_tags() on all message inputs.']
            ]
        ],
        [
            'version' => '3.0.0',
            'date' => 'March 28, 2026',
            'type' => 'major',
            'title' => 'Neural Engine 3.0 & AI Financial Assistant',
            'description' => 'The largest update in FinanceAI history. We replaced standard algorithmic categorization with a custom-trained LLM architecture utilizing Retrieval-Augmented Generation (RAG).',
            'authors' => ['45', '11', '68'],
            'changes' => [
                ['tag' => 'feature', 'text' => 'Gemini-powered AI financial advisor integrated directly into the user dashboard.'],
                ['tag' => 'feature', 'text' => 'Real-time market data ingestion via Alpha Vantage and CoinGecko APIs.'],
                ['tag' => 'feature', 'text' => 'Streaming AI chat with source citations and mathematical confidence scoring.'],
                ['tag' => 'improvement', 'text' => 'Subscription-gated AI features specifically for Pro and Enterprise tiers.'],
                ['tag' => 'security', 'text' => 'Strict prompt-injection sanitization to prevent LLM jailbreaks.']
            ]
        ],
        [
            'version' => '2.5.0',
            'date' => 'February 12, 2026',
            'type' => 'minor',
            'title' => 'SaaS Subscription System',
            'description' => 'Transitioned from a single-tenant model to a fully scalable SaaS architecture with integrated billing limits and compute quotas.',
            'authors' => ['32', '59'],
            'changes' => [
                ['tag' => 'feature', 'text' => 'Three-tier subscription model (Core Node, Pro Hub, Enterprise) deployed.'],
                ['tag' => 'feature', 'text' => 'Custom Laravel middleware engineered for feature-based access control.'],
                ['tag' => 'improvement', 'text' => 'Simulated payment flow with a comprehensive subscription management UI.'],
                ['tag' => 'fix', 'text' => 'Resolved a race condition during concurrent user registration.']
            ]
        ],
        [
            'version' => '2.4.0',
            'date' => 'January 05, 2026',
            'type' => 'minor',
            'title' => 'Family Hub & Shared Ledgers',
            'description' => 'Enabled multi-node collaboration. Families and financial teams can now operate securely within isolated, shared database clusters.',
            'authors' => ['12'],
            'changes' => [
                ['tag' => 'feature', 'text' => 'Multi-user workspace system with strict Role-Based Access Control (RBAC).'],
                ['tag' => 'feature', 'text' => 'Cryptographic family invitation system with secure email token dispatch.'],
                ['tag' => 'improvement', 'text' => 'Workspace context switcher added to the master sidebar navigation.'],
                ['tag' => 'fix', 'text' => 'Fixed a pagination offset bug on large family member listings.']
            ]
        ],
        [
            'version' => '2.3.1',
            'date' => 'December 18, 2025',
            'type' => 'patch',
            'title' => 'Security Hardening & Performance',
            'description' => 'Routine infrastructure maintenance and database optimization to support scaling load.',
            'authors' => ['68', '33'],
            'changes' => [
                ['tag' => 'security', 'text' => 'Upgraded to TLS 1.3 requirement for all API endpoint communications.'],
                ['tag' => 'security', 'text' => 'Rate limiting applied to all authentication and password reset endpoints.'],
                ['tag' => 'improvement', 'text' => '40% improvement in dashboard query performance via composite SQL indexing.'],
                ['tag' => 'fix', 'text' => 'Fixed a memory leak in the global notification polling service.']
            ]
        ]
    ];
@endphp

<div class="bg-[#fcf9f2] font-sans selection:bg-[#bacdf3] selection:text-[#0f172a] relative overflow-hidden min-h-screen flex flex-col pt-24"
     x-data="changelogEngine()">

    {{-- Ambient Backgrounds (New Palette) --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[1000px] h-[1000px] bg-[#bacdf3]/30 blur-[150px] rounded-full animate-float"></div>
        <div class="absolute bottom-[10%] right-[-10%] w-[800px] h-[800px] bg-[#9fb2df]/20 blur-[120px] rounded-full animate-float" style="animation-delay: -3s;"></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.03]"></div>
    </div>

    {{-- ================= 1. HERO & SEARCH ================= --}}
    <section class="relative pt-20 pb-16 lg:pt-32 lg:pb-24 overflow-hidden z-10 border-b border-[#bacdf3]/40 bg-white/80 backdrop-blur-2xl">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">

            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#fcf9f2] border border-[#bacdf3] text-[#7284b5] text-[10px] font-black uppercase tracking-widest mb-8 shadow-sm reveal-up">
                <i class="fa-solid fa-code-commit"></i> Deployment History
            </div>

            <h1 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tight leading-[1.05] reveal-up" style="transition-delay: 100ms;">
                System Changelog
            </h1>
            <p class="mt-6 text-lg text-slate-500 font-medium leading-relaxed max-w-2xl mx-auto reveal-up" style="transition-delay: 200ms;">
                Every cryptographic patch, neural engine upgrade, and infrastructure enhancement — documented with absolute mathematical precision.
            </p>

            {{-- Deployment Velocity Metrics --}}
            <div class="mt-12 grid grid-cols-3 gap-4 max-w-3xl mx-auto reveal-up" style="transition-delay: 300ms;">
                @foreach($metrics as $metric)
                <div class="bg-white rounded-2xl border border-[#bacdf3]/50 p-5 shadow-[0_10px_30px_-15px_rgba(114,132,181,0.2)]">
                    <i class="fa-solid {{ $metric['icon'] }} text-[#9fb2df] text-xl mb-3"></i>
                    <p class="text-3xl font-black text-slate-900 tracking-tight mb-1">{{ $metric['value'] }}</p>
                    <p class="text-[9px] font-black uppercase tracking-widest text-[#7284b5]">{{ $metric['label'] }}</p>
                </div>
                @endforeach
            </div>

            {{-- Interactive Search & Filter Toolbar --}}
            <div class="mt-16 max-w-4xl mx-auto bg-white p-2 rounded-[1.5rem] border border-[#bacdf3]/60 shadow-[0_20px_50px_-20px_rgba(114,132,181,0.2)] flex flex-col md:flex-row items-center gap-4 reveal-up" style="transition-delay: 400ms;">
                
                {{-- Search Input --}}
                <div class="relative w-full md:w-1/2 flex-shrink-0 group/search">
                    <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-[#9fb2df] group-focus-within/search:text-[#7284b5] transition-colors"></i>
                    <input type="text" x-model="searchQuery" @input="playKeySound()" placeholder="Search updates..." 
                           class="w-full pl-12 pr-4 py-3.5 bg-[#fcf9f2]/50 border border-transparent focus:border-[#bacdf3] focus:bg-white rounded-xl text-sm font-bold text-slate-900 placeholder-slate-400 outline-none transition-all focus:ring-4 focus:ring-[#bacdf3]/20">
                </div>

                {{-- Filter Tabs --}}
                <div class="flex overflow-x-auto w-full scrollbar-hide gap-2 p-1">
                    <template x-for="cat in ['all', 'feature', 'improvement', 'security', 'fix']" :key="cat">
                        <button @click="setFilter(cat)" @mouseenter="playHover()"
                                class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest border transition-all focus:outline-none whitespace-nowrap flex-1 md:flex-none text-center"
                                :class="activeFilter === cat ? 'bg-[#7284b5] text-white border-[#7284b5] shadow-md' : 'bg-transparent text-slate-500 border-transparent hover:bg-[#fcf9f2] hover:text-[#7284b5]'">
                            <span x-text="cat === 'all' ? 'All' : cat"></span>
                        </button>
                    </template>
                </div>
            </div>

        </div>
    </section>

    {{-- ================= 2. CHANGELOG TIMELINE (Blade Rendered, Alpine Filtered) ================= --}}
    <section class="py-24 relative z-10 flex-1">
        <div class="max-w-[1000px] mx-auto px-4 sm:px-6 lg:px-8">

            <div class="relative">
                {{-- Center Glowing Track --}}
                <div class="absolute left-8 md:left-32 top-0 bottom-0 w-1 bg-gradient-to-b from-[#bacdf3] via-[#fcf9f2] to-transparent rounded-full"></div>

                <div class="space-y-16">
                    @foreach($releases as $idx => $release)
                        {{-- Alpine x-show for dynamic filtering, Blade for SEO --}}
                        <div class="relative pl-24 md:pl-48 release-item reveal-up" 
                             style="transition-delay: {{ $idx * 50 }}ms"
                             data-version="v{{ $release['version'] }}"
                             x-show="matchesFilter({{ json_encode($release) }})" 
                             x-transition:enter="transition ease-out duration-300" 
                             x-transition:enter-start="opacity-0 translate-y-4" 
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-200" 
                             x-transition:leave-start="opacity-100 scale-100" 
                             x-transition:leave-end="opacity-0 scale-95 hidden">

                            {{-- Left Side: Date & Version Badge (Desktop) --}}
                            <div class="absolute left-0 md:left-0 top-0 w-16 md:w-24 text-right hidden md:block mt-2">
                                <span class="text-xs font-black text-slate-400 uppercase tracking-widest block">{{ $release['date'] }}</span>
                                @if($idx === 0)
                                    <span class="inline-flex items-center gap-1 text-[9px] font-black text-emerald-500 uppercase tracking-widest mt-2 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Latest
                                    </span>
                                @endif
                            </div>

                            {{-- Timeline Node Dot --}}
                            <div class="absolute left-[30px] md:left-[126px] top-4 w-4 h-4 rounded-full border-[4px] border-white shadow-md transform -translate-x-1/2 z-10 transition-all duration-300 {{ $release['type'] === 'major' ? 'bg-[#7284b5] ring-4 ring-[#bacdf3]/50' : 'bg-[#bacdf3]' }}"></div>

                            {{-- Release Card --}}
                            <div id="v{{ str_replace('.', '-', $release['version']) }}" class="bg-white rounded-[2.5rem] border border-[#bacdf3]/40 shadow-[0_10px_40px_-15px_rgba(114,132,181,0.1)] p-8 sm:p-10 hover:shadow-[0_20px_50px_-10px_rgba(114,132,181,0.2)] hover:-translate-y-1 transition-all duration-500 group relative overflow-hidden">
                                
                                {{-- Subtle Card Glow --}}
                                @if($release['type'] === 'major')
                                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-[#bacdf3]/20 rounded-full blur-3xl pointer-events-none group-hover:bg-[#bacdf3]/30 transition-colors duration-700"></div>
                                @endif

                                {{-- Header & Meta --}}
                                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6 relative z-10 border-b border-slate-100 pb-6">
                                    <div>
                                        <div class="flex items-center gap-3 mb-3">
                                            <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest border shadow-sm {{ $release['type'] === 'major' ? 'bg-[#7284b5] text-white border-[#7284b5]' : 'bg-[#fcf9f2] text-[#7284b5] border-[#bacdf3]' }}">
                                                v{{ $release['version'] }}
                                            </span>
                                            <span class="md:hidden text-xs font-black uppercase tracking-widest text-slate-400">{{ $release['date'] }}</span>
                                        </div>
                                        <h3 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight group-hover:text-[#7284b5] transition-colors">{{ $release['title'] }}</h3>
                                    </div>
                                    
                                    {{-- Actions (Copy Link) --}}
                                    <button @click="copyLink('v{{ str_replace('.', '-', $release['version']) }}')" @mouseenter="playHover()" class="shrink-0 w-10 h-10 rounded-xl bg-[#fcf9f2] border border-[#bacdf3] text-[#7284b5] hover:bg-white hover:text-indigo-600 transition-colors shadow-sm flex items-center justify-center focus:outline-none" title="Copy Permalink">
                                        <i class="fa-solid fa-link text-sm"></i>
                                    </button>
                                </div>

                                <p class="text-slate-600 font-medium text-base leading-relaxed mb-8 relative z-10 max-w-2xl">
                                    {{ $release['description'] }}
                                </p>

                                {{-- Changes List --}}
                                <div class="space-y-4 relative z-10">
                                    @foreach($release['changes'] as $item)
                                        <div class="flex items-start gap-4 change-item" data-tag="{{ $item['tag'] }}">
                                            @php
                                                $tagStyles = [
                                                    'feature' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'border-emerald-200', 'icon' => 'fa-plus'],
                                                    'improvement' => ['bg' => 'bg-sky-50', 'text' => 'text-sky-600', 'border' => 'border-sky-200', 'icon' => 'fa-arrow-up'],
                                                    'security' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'border' => 'border-rose-200', 'icon' => 'fa-shield-halved'],
                                                    'fix' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'border' => 'border-amber-200', 'icon' => 'fa-wrench'],
                                                ];
                                                $style = $tagStyles[$item['tag']] ?? $tagStyles['feature'];
                                            @endphp
                                            
                                            <div class="mt-1 w-7 h-7 rounded-lg flex items-center justify-center shrink-0 border shadow-sm {{ $style['bg'] }} {{ $style['text'] }} {{ $style['border'] }}">
                                                <i class="fa-solid {{ $style['icon'] }} text-[10px]"></i>
                                            </div>
                                            <div>
                                                <span class="text-[9px] font-black uppercase tracking-widest block mb-0.5 {{ $style['text'] }}">{{ $item['tag'] }}</span>
                                                <p class="text-sm font-bold text-slate-700 leading-relaxed">{{ $item['text'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Contributors Footer --}}
                                <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-between relative z-10">
                                    <div class="flex items-center gap-3">
                                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Shipped By</span>
                                        <div class="flex -space-x-2">
                                            @foreach($release['authors'] as $author)
                                                <img src="https://i.pravatar.cc/100?img={{ $author }}" class="w-8 h-8 rounded-full border-2 border-white shadow-sm hover:-translate-y-1 transition-transform cursor-pointer" alt="Engineer">
                                            @endforeach
                                        </div>
                                    </div>
                                    <a href="#" class="text-[10px] font-black uppercase tracking-widest text-[#7284b5] hover:text-[#9fb2df] transition-colors">View PR Details &rarr;</a>
                                </div>

                            </div>
                        </div>
                    @endforeach

                    {{-- Empty State (If Search Yields Nothing) --}}
                    <div x-show="filteredCount === 0" style="display: none;" class="pl-24 md:pl-48 py-10">
                        <div class="bg-white rounded-[2rem] border border-dashed border-[#bacdf3] p-12 text-center">
                            <div class="w-16 h-16 bg-[#fcf9f2] border border-[#bacdf3] rounded-2xl flex items-center justify-center mx-auto mb-4 text-[#9fb2df]">
                                <i class="fa-solid fa-ghost text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-black text-slate-900 mb-2">No matching updates found</h3>
                            <p class="text-sm font-medium text-slate-500">Try adjusting your search terms or clearing the category filters.</p>
                            <button @click="searchQuery = ''; activeFilter = 'all'; playClick()" class="mt-6 px-4 py-2 bg-[#7284b5] text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm hover:bg-[#616dab] transition-colors focus:outline-none">
                                Clear Filters
                            </button>
                        </div>
                    </div>

                </div>

                {{-- End of Timeline Marker --}}
                <div class="absolute bottom-[-40px] left-8 md:left-32 transform -translate-x-1/2">
                    <div class="w-10 h-10 rounded-full bg-[#fcf9f2] border-2 border-[#bacdf3] flex items-center justify-center shadow-sm text-[#9fb2df]">
                        <i class="fa-solid fa-flag-checkered text-[10px]"></i>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- ================= 3. CTA SUBSCRIBE SECTION ================= --}}
    <section class="py-24 px-4 sm:px-6 lg:px-8 relative z-10 border-t border-[#bacdf3]/40 bg-white">
        <div class="max-w-4xl mx-auto bg-[#0f172a] rounded-[3rem] border border-slate-800 p-10 md:p-16 text-center relative overflow-hidden shadow-2xl reveal-up">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20 mix-blend-overlay pointer-events-none"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-lg h-64 bg-indigo-500/30 rounded-full blur-[120px] pointer-events-none"></div>

            <div class="w-20 h-20 bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl flex items-center justify-center mx-auto mb-8 relative z-10 shadow-inner">
                <i class="fa-solid fa-satellite-dish text-3xl text-[#bacdf3]"></i>
            </div>
            
            <h2 class="text-3xl md:text-5xl font-black text-white tracking-tight mb-6 relative z-10">Subscribe to Engine Telemetry</h2>
            <p class="text-[#bacdf3] font-medium text-lg relative z-10 mb-10 max-w-lg mx-auto leading-relaxed">Join 12,000+ engineers receiving our cryptographic updates, AI financial forecasts, and feature drops directly.</p>
            
            <form @submit.prevent="playClick(); $dispatch('notify', {message: 'Payload secured. Welcome to the network.', type: 'success'})" class="flex flex-col sm:flex-row items-center gap-3 max-w-lg mx-auto relative z-10">
                <input type="email" required placeholder="developer@node.com" class="flex-1 w-full px-6 py-5 bg-slate-900/50 border border-slate-700 rounded-2xl text-white placeholder-slate-500 font-bold text-sm focus:outline-none focus:border-[#bacdf3] focus:ring-4 focus:ring-[#bacdf3]/20 backdrop-blur-sm transition-all shadow-inner">
                <button type="submit" @mouseenter="playHover()" class="magnetic-target w-full sm:w-auto px-8 py-5 bg-white text-[#0f172a] rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-[0_10px_20px_rgba(255,255,255,0.1)] hover:bg-[#bacdf3] hover:text-[#0f172a] transition-all cursor-none focus:outline-none">
                    Subscribe <i class="fa-solid fa-arrow-right ml-1"></i>
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
    
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .reveal-up { opacity: 0; transform: translateY(30px); transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
    .reveal-up.is-visible { opacity: 1; transform: translateY(0); }
    
    @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-20px); } }
    .animate-float { animation: float 8s ease-in-out infinite; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('changelogEngine', () => ({
        activeFilter: 'all',
        searchQuery: '',
        filteredCount: -1, // Initialize to prevent flash

        init() {
            // Scroll Animation Observer
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('is-visible'); });
            }, { threshold: 0.1 });
            document.querySelectorAll('.reveal-up').forEach(el => observer.observe(el));

            // Watch for changes to update the count
            this.$watch('searchQuery', () => this.updateFilteredCount());
            this.$watch('activeFilter', () => this.updateFilteredCount());
            
            this.$nextTick(() => this.updateFilteredCount());
        },

        playClick() { if(window.audioEngine) window.audioEngine.playClick(); },
        playHover() { if(window.audioEngine) window.audioEngine.playHover(); },
        playKeySound() {
            if(!window.audioEngine) return;
            if(!this.lastTick || Date.now() - this.lastTick > 50) {
                window.audioEngine.playClick(); 
                this.lastTick = Date.now();
            }
        },

        setFilter(cat) {
            this.playClick();
            this.activeFilter = cat;
            this.searchQuery = ''; // Clear search when changing category for better UX
        },

        // Complex matching for Search + Category
        matchesFilter(release) {
            let matchCategory = true;
            let matchSearch = true;

            // 1. Check Category
            if (this.activeFilter !== 'all') {
                // Return true if ANY of the changes in this release match the tag
                matchCategory = release.changes.some(c => c.tag === this.activeFilter);
            }

            // 2. Check Search (Title, Description, or individual change text)
            if (this.searchQuery.trim() !== '') {
                const query = this.searchQuery.toLowerCase();
                const titleMatch = release.title.toLowerCase().includes(query);
                const descMatch = release.description.toLowerCase().includes(query);
                const changesMatch = release.changes.some(c => c.text.toLowerCase().includes(query));
                
                matchSearch = titleMatch || descMatch || changesMatch;
            }

            return matchCategory && matchSearch;
        },

        updateFilteredCount() {
            // Since we render via Blade, we need to count how many items evaluate to true
            let count = 0;
            const items = document.querySelectorAll('.release-item');
            
            // We use a small timeout to allow Alpine x-show evaluation to finish
            setTimeout(() => {
                items.forEach(item => {
                    if (item.style.display !== 'none') count++;
                });
                this.filteredCount = count;
            }, 50);
        },

        copyLink(elementId) {
            this.playClick();
            const url = window.location.origin + window.location.pathname + '#' + elementId;
            navigator.clipboard.writeText(url).then(() => {
                this.$dispatch('notify', { message: 'Release permalink copied to clipboard.', type: 'success' });
            }).catch(() => {
                this.$dispatch('notify', { message: 'Failed to copy link.', type: 'error' });
            });
        }
    }));
});
</script>
@endpush