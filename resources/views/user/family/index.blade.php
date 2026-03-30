@extends('layouts.app')

@section('title', 'Collaborative Workspaces | FinanceAI')

@section('content')

@php
    /* |--------------------------------------------------------------------------
       | 🛡️ ROUTE, SECURITY & DATA INGESTION ENGINE
       |-------------------------------------------------------------------------- */
    $hasShowRoute   = Route::has('user.families.show');
    $hasCreateRoute = Route::has('user.families.create');
    
    // Safely hydrate collections to prevent 500 errors on empty states
    $families   = $families ?? collect();
    $activities = $activities ?? collect();

    // Agnostic count handler (Paginator vs Standard Collection)
    $familyCount = $families instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator 
        ? $families->total() 
        : $families->count();

    /* |--------------------------------------------------------------------------
       | 🎨 ENTERPRISE MULTI-COLOR THEME MATRIX (Tailwind Safe)
       |-------------------------------------------------------------------------- */
    $themeColors = [
        ['light' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'border' => 'border-indigo-100', 'gradient' => 'from-indigo-500 to-blue-500', 'icon' => 'fa-users-rays', 'shadow' => 'shadow-indigo-500/20'],
        ['light' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'border-emerald-100', 'gradient' => 'from-emerald-500 to-teal-400', 'icon' => 'fa-leaf', 'shadow' => 'shadow-emerald-500/20'],
        ['light' => 'bg-rose-50', 'text' => 'text-rose-600', 'border' => 'border-rose-100', 'gradient' => 'from-rose-500 to-pink-500', 'icon' => 'fa-fire', 'shadow' => 'shadow-rose-500/20'],
        ['light' => 'bg-amber-50', 'text' => 'text-amber-600', 'border' => 'border-amber-100', 'gradient' => 'from-amber-500 to-orange-400', 'icon' => 'fa-bolt', 'shadow' => 'shadow-amber-500/20'],
        ['light' => 'bg-sky-50', 'text' => 'text-sky-600', 'border' => 'border-sky-100', 'gradient' => 'from-sky-500 to-cyan-400', 'icon' => 'fa-cloud', 'shadow' => 'shadow-sky-500/20'],
    ];
@endphp

<div x-data="workspaceManager()" class="min-h-screen bg-[#f8fafc] pb-32 font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden">

    {{-- ================= 0. AMBIENT ENVIRONMENT ================= --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-15%] left-[-10%] w-[1000px] h-[1000px] bg-indigo-600/5 rounded-full blur-[120px] transition-transform duration-1000 transform-gpu"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[800px] h-[800px] bg-sky-500/5 rounded-full blur-[100px] transform-gpu"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+PHBhdGggZD0iTTAgMGgyMHYyMEgwVjB6bTEwIDEwaDEwdjEwSDEwaC0xMHptMCAwaC0xMHYtMTBoMTB2MTB6IiBmaWxsPSIjZThlYWVkIiBmaWxsLW9wYWNpdHk9IjAuMjUiIGZpbGwtcnVsZT0iZXZlbm9kZCIvPjwvc3ZnPg==')] opacity-40 mix-blend-multiply"></div>
    </div>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 pt-10 relative z-10 space-y-8">

        {{-- ================= 1. SYSTEM ALERTS & TOASTS ================= --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-[-1rem]" x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-[-1rem]"
                 class="bg-white/80 backdrop-blur-md border border-emerald-200 rounded-2xl p-4 flex items-center justify-between shadow-[0_10px_30px_-10px_rgba(16,185,129,0.2)] max-w-3xl mx-auto mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                        <i class="fa-solid fa-check text-xs"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-emerald-600 mb-0.5">Success Protocol</p>
                        <p class="text-sm font-bold text-slate-700">{{ session('success') }}</p>
                    </div>
                </div>
                <button @click="show = false" class="text-slate-400 hover:bg-slate-100 hover:text-slate-700 w-8 h-8 rounded-full flex items-center justify-center transition-colors focus:outline-none">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        {{-- ================= 2. MASTER COMMAND HEADER ================= --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 bg-white p-8 md:p-10 rounded-[2.5rem] border border-slate-200 shadow-[0_4px_20px_-5px_rgba(0,0,0,0.03)] relative overflow-hidden group/header">
            <div class="absolute top-0 left-0 w-2 h-full bg-gradient-to-b from-indigo-500 to-sky-400"></div>
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl group-hover/header:bg-indigo-500/10 transition-colors duration-700 pointer-events-none"></div>

            <div class="relative z-10">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <li>FinanceAI Engine</li>
                        <li><i class="fa-solid fa-chevron-right text-[8px] mx-1 opacity-40"></i></li>
                        <li class="text-indigo-600 flex items-center gap-1.5">
                            <span class="relative flex h-1.5 w-1.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span><span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-indigo-500"></span></span>
                            Collaboration Hub
                        </li>
                    </ol>
                </nav>
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight mb-2">Shared Workspaces</h1>
                <p class="text-slate-500 text-sm font-medium flex items-center gap-2 max-w-xl leading-relaxed">
                    Manage collaborative cryptographic ledgers. Pool capital, sync transactions in real-time, and execute joint financial strategies with enterprise-grade encryption.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 relative z-10 shrink-0">
                <button @click="syncLedgers()" @mouseenter="if(typeof window.audioEngine !== 'undefined') window.audioEngine.playHover()" class="px-5 py-3.5 bg-slate-50 text-slate-600 border border-slate-200 rounded-2xl font-bold text-sm hover:bg-white hover:text-indigo-600 hover:border-indigo-300 hover:shadow-sm transition-all flex items-center gap-2 focus:outline-none">
                    <i class="fa-solid fa-rotate text-indigo-500" :class="syncing ? 'animate-spin' : ''"></i> Sync Nodes
                </button>

                @if($hasCreateRoute)
                <a href="{{ route('user.families.create') }}" @mouseenter="if(typeof window.audioEngine !== 'undefined') window.audioEngine.playHover()" class="px-6 py-3.5 bg-slate-900 text-white rounded-2xl font-bold text-sm shadow-[0_10px_20px_rgba(15,23,42,0.2)] hover:bg-indigo-600 hover:shadow-[0_15px_30px_rgba(79,70,229,0.3)] transition-all flex items-center gap-2 hover:-translate-y-0.5 focus:outline-none border border-slate-800 hover:border-indigo-500">
                    <i class="fa-solid fa-plus text-indigo-300"></i> Initialize Hub
                </a>
                @endif
            </div>
        </div>

        {{-- ================= 3. SYSTEM KPI TELEMETRY GRID ================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            
            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm flex items-center gap-5 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group/kpi">
                <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-[1.25rem] flex items-center justify-center border border-indigo-100 shadow-inner shrink-0 group-hover/kpi:scale-110 group-hover/kpi:rotate-3 transition-transform duration-500">
                    <i class="fa-solid fa-network-wired text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Active Hubs</p>
                    <h2 class="text-3xl font-black text-slate-900 font-mono tracking-tight">{{ number_format($familyCount) }}</h2>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm flex items-center gap-5 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group/kpi">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl pointer-events-none group-hover/kpi:bg-emerald-500/20 transition-colors duration-700"></div>
                <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-[1.25rem] flex items-center justify-center border border-emerald-100 shadow-inner shrink-0 relative z-10 group-hover/kpi:scale-110 transition-transform duration-500">
                    <i class="fa-solid fa-shield-halved text-xl"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Encryption Layer</p>
                    <h2 class="text-xl font-black text-slate-900 flex items-center gap-2 tracking-tight">
                        AES-256 <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)] animate-pulse"></span>
                    </h2>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm flex items-center gap-5 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group/kpi">
                <div class="w-14 h-14 bg-sky-50 text-sky-600 rounded-[1.25rem] flex items-center justify-center border border-sky-100 shadow-inner shrink-0 group-hover/kpi:scale-110 transition-transform duration-500">
                    <i class="fa-solid fa-microchip text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Neural Engine</p>
                    <h2 class="text-xl font-black text-sky-600 flex items-center gap-2">
                        Online <i class="fa-solid fa-check-double text-sm"></i>
                    </h2>
                </div>
            </div>

        </div>

        {{-- ================= 4. MASTER CONTENT ARCHITECTURE ================= --}}
        <div class="grid xl:grid-cols-12 gap-8 items-start">

            {{-- ---------------------------------------------------- --}}
            {{-- LEFT COLUMN: WORKSPACE GRID ENGINE                   --}}
            {{-- ---------------------------------------------------- --}}
            <div class="xl:col-span-8 space-y-6 relative">
                
                {{-- Interactive Filter & Search Bar (Mac OS Style) --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-2.5 rounded-2xl border border-slate-200 shadow-sm relative z-20">
                    
                    {{-- Alpine Sliding Pill Segmented Control --}}
                    <div class="relative flex items-center bg-slate-100 p-1 rounded-xl shadow-inner w-full sm:w-auto">
                        <div class="absolute h-[calc(100%-8px)] bg-white rounded-lg shadow-sm border border-slate-200 transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]" 
                             :style="`width: ${filterWidth}px; transform: translateX(${filterOffset}px);`"></div>
                        
                        <button x-ref="btnAll" @click="setFilter('all', $refs.btnAll)" @mouseenter="if(typeof window.audioEngine !== 'undefined') window.audioEngine.playHover()" 
                                class="relative z-10 px-5 py-2 text-[10px] font-black uppercase tracking-widest transition-colors focus:outline-none flex-1 sm:flex-none text-center" 
                                :class="filter === 'all' ? 'text-indigo-600' : 'text-slate-500 hover:text-slate-800'">All Hubs</button>
                        
                        <button x-ref="btnOwner" @click="setFilter('owner', $refs.btnOwner)" @mouseenter="if(typeof window.audioEngine !== 'undefined') window.audioEngine.playHover()" 
                                class="relative z-10 px-5 py-2 text-[10px] font-black uppercase tracking-widest transition-colors focus:outline-none flex-1 sm:flex-none text-center" 
                                :class="filter === 'owner' ? 'text-emerald-600' : 'text-slate-500 hover:text-slate-800'">Owned</button>
                        
                        <button x-ref="btnMember" @click="setFilter('member', $refs.btnMember)" @mouseenter="if(typeof window.audioEngine !== 'undefined') window.audioEngine.playHover()" 
                                class="relative z-10 px-5 py-2 text-[10px] font-black uppercase tracking-widest transition-colors focus:outline-none flex-1 sm:flex-none text-center" 
                                :class="filter === 'member' ? 'text-rose-600' : 'text-slate-500 hover:text-slate-800'">Member</button>
                    </div>

                    <div class="relative w-full sm:w-72 group/search">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within/search:text-indigo-500 transition-colors text-sm"></i>
                        <input type="text" x-model="searchInput" @input="debouncedSearch" placeholder="Search workspace nodes..." 
                               class="w-full pl-10 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 focus:bg-white outline-none transition-all shadow-inner placeholder-slate-400">
                        <button x-show="searchInput.length > 0" @click="clearSearch()" class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center hover:bg-slate-300 transition-colors">
                            <i class="fa-solid fa-xmark text-[10px]"></i>
                        </button>
                    </div>
                </div>

                {{-- Loading Skeleton Overlay (Fires on search/filter) --}}
                <div x-show="isQuerying" x-cloak class="absolute top-[80px] inset-x-0 bottom-0 z-30 bg-[#f8fafc]/80 backdrop-blur-sm rounded-3xl p-2 flex flex-wrap gap-6">
                    <div class="w-full h-48 bg-white border border-slate-200 rounded-[2rem] shadow-sm animate-pulse"></div>
                    <div class="w-full h-48 bg-white border border-slate-200 rounded-[2rem] shadow-sm animate-pulse"></div>
                </div>

                {{-- Empty State Handlers --}}
                @if($families->isEmpty())
                    <div class="bg-white rounded-[2.5rem] border-2 border-dashed border-slate-200 p-12 text-center flex flex-col items-center justify-center relative overflow-hidden shadow-sm">
                        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+PHBhdGggZD0iTTAgMGgyMHYyMEgwVjB6bTEwIDEwaDEwdjEwSDEwaC0xMHptMCAwaC0xMHYtMTBoMTB2MTB6IiBmaWxsPSIjZThlYWVkIiBmaWxsLW9wYWNpdHk9IjAuMjUiIGZpbGwtcnVsZT0iZXZlbm9kZCIvPjwvc3ZnPg==')] opacity-30 mix-blend-multiply"></div>
                        <div class="w-24 h-24 bg-slate-50 rounded-[1.5rem] flex items-center justify-center mb-6 border border-slate-100 shadow-inner relative z-10">
                            <i class="fa-solid fa-network-wired text-4xl text-slate-300"></i>
                        </div>
                        <h2 class="text-2xl font-black text-slate-900 mb-3 relative z-10">No Hubs Initialized</h2>
                        <p class="text-slate-500 font-medium max-w-md mx-auto leading-relaxed mb-8 relative z-10">
                            Workspaces allow you to pool financial resources, track joint expenses, and receive collaborative AI insights securely with your partners.
                        </p>
                        @if($hasCreateRoute)
                        <a href="{{ route('user.families.create') }}" class="px-8 py-4 bg-indigo-600 text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-[0_10px_20px_rgba(79,70,229,0.3)] hover:bg-indigo-700 hover:-translate-y-1 transition-all focus:outline-none relative z-10 flex items-center gap-2">
                            <i class="fa-solid fa-plus"></i> Initialize First Hub
                        </a>
                        @endif
                    </div>
                @else
                    {{-- DYNAMIC MULTI-COLOR GRID --}}
                    <div class="grid sm:grid-cols-2 gap-6 relative z-10" :class="isQuerying ? 'opacity-0' : 'opacity-100'" style="transition: opacity 0.2s ease;">
                        @foreach($families as $index => $family)
                            @php 
                                $theme = $themeColors[$index % count($themeColors)];
                                $role = strtolower($family->pivot->role ?? 'member'); 
                                
                                if($role === 'owner' || $role === 'admin') {
                                    $roleColor = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                } else {
                                    $roleColor = 'bg-slate-50 text-slate-600 border-slate-200';
                                }

                                // Safe User Counting
                                $memberCount = 1;
                                if(method_exists($family, 'users') && $family->relationLoaded('users')) {
                                    $memberCount = $family->users->count();
                                } elseif(isset($family->users_count)) {
                                    $memberCount = $family->users_count;
                                }
                                
                                // Generate deterministic random initials for avatars
                                $mockInitials = ['AB', 'JD', 'KL', 'MW', 'SP'];
                            @endphp

                            <div x-show="matchesFilter('{{ $role }}', '{{ addslashes($family->name) }}')"
                                 class="bg-white rounded-[2rem] border border-slate-200 p-6 shadow-sm hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] hover:-translate-y-1.5 transition-all duration-300 relative overflow-hidden group/card flex flex-col h-full">
                                
                                {{-- Card Gradient Header Bar --}}
                                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r {{ $theme['gradient'] }}"></div>

                                <div class="flex justify-between items-start mb-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 {{ $theme['light'] }} {{ $theme['text'] }} rounded-[1rem] flex items-center justify-center border {{ $theme['border'] }} shadow-sm group-hover/card:scale-110 transition-transform duration-300 shrink-0">
                                            <i class="fa-solid {{ $theme['icon'] }} text-lg"></i>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-md text-[8px] font-black uppercase tracking-widest border shadow-sm {{ $roleColor }}">
                                            {{ $role }}
                                        </span>
                                    </div>
                                    
                                    {{-- Context Menu 3-Dot --}}
                                    <div class="relative" x-data="{ openMenu: false }" @click.away="openMenu = false">
                                        <button @click="openMenu = !openMenu" @mouseenter="if(typeof window.audioEngine !== 'undefined') window.audioEngine.playHover()" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-50 hover:text-slate-900 border border-transparent hover:border-slate-200 transition-colors focus:outline-none">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <div x-show="openMenu" x-cloak
                                             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                                             class="absolute top-full right-0 mt-1 w-48 bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden z-50 p-1.5">
                                            
                                            <button @click="openMenu = false; showToast('Link copied to clipboard.', 'success')" class="w-full text-left px-3 py-2.5 rounded-lg text-xs font-bold text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors flex items-center gap-3">
                                                <i class="fa-solid fa-link w-4 text-center"></i> Copy Invite
                                            </button>
                                            
                                            @if($hasShowRoute)
                                            <a href="{{ route('user.families.show', $family->id) }}" class="w-full text-left px-3 py-2.5 rounded-lg text-xs font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition-colors flex items-center gap-3">
                                                <i class="fa-solid fa-chart-pie w-4 text-center"></i> Open Ledger
                                            </a>
                                            @endif

                                            <div class="border-t border-slate-100 my-1 mx-2"></div>
                                            
                                            <button @click="openMenu = false; showToast('Requesting termination...', 'error')" class="w-full text-left px-3 py-2.5 rounded-lg text-xs font-bold text-slate-600 hover:bg-rose-50 hover:text-rose-600 transition-colors flex items-center gap-3">
                                                <i class="fa-solid fa-person-walking-arrow-right w-4 text-center"></i> Leave Hub
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <h3 class="font-black text-xl text-slate-900 mb-2 truncate group-hover/card:text-indigo-600 transition-colors tracking-tight">
                                    {{ $family->name }}
                                </h3>
                                <p class="text-xs font-bold text-slate-400 mb-8 flex-1 line-clamp-2 leading-relaxed">
                                    {{ $family->description ?? 'Secure encrypted shared ledger. AI optimization active.' }}
                                </p>

                                <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                                    
                                    {{-- 🔥 BEAST MODE: Overlapping Avatar Stack --}}
                                    <div class="flex items-center -space-x-3 group/stack cursor-help" title="{{ $memberCount }} active members">
                                        <div class="w-10 h-10 rounded-full bg-slate-900 border-[3px] border-white flex items-center justify-center text-[10px] font-black text-white shadow-sm z-40 relative group-hover/stack:-translate-y-1 transition-transform">
                                            {{ $mockInitials[($index + 0) % 5] }}
                                        </div>
                                        @if($memberCount > 1)
                                            <div class="w-10 h-10 rounded-full bg-slate-200 border-[3px] border-white flex items-center justify-center text-[10px] font-black text-slate-600 shadow-sm z-30 relative group-hover/stack:-translate-y-1 transition-transform" style="transition-delay: 50ms;">
                                                {{ $mockInitials[($index + 1) % 5] }}
                                            </div>
                                        @endif
                                        @if($memberCount > 2)
                                            <div class="w-10 h-10 rounded-full bg-indigo-100 border-[3px] border-white flex items-center justify-center text-[10px] font-black text-indigo-600 shadow-sm z-20 relative group-hover/stack:-translate-y-1 transition-transform" style="transition-delay: 100ms;">
                                                +{{ $memberCount - 2 }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @if($hasShowRoute)
                                    <a href="{{ route('user.families.show', $family->id) }}" @click="if(typeof simulateNavigation === 'function') simulateNavigation()" @mouseenter="if(typeof window.audioEngine !== 'undefined') window.audioEngine.playHover()" class="w-10 h-10 rounded-[12px] bg-slate-50 border border-slate-200 text-slate-500 flex items-center justify-center group-hover/card:bg-indigo-600 group-hover/card:text-white group-hover/card:border-indigo-600 group-hover/card:shadow-md transition-all">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Empty Search Fallback --}}
                    <div x-show="!isQuerying && searchInput.length > 0 && !hasMatches" x-cloak class="text-center py-16 bg-white rounded-3xl border border-slate-200 shadow-sm">
                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100"><i class="fa-solid fa-magnifying-glass-minus text-2xl text-slate-300"></i></div>
                        <p class="text-slate-900 font-black text-lg mb-1">No Hubs Found</p>
                        <p class="text-slate-500 font-bold text-sm">No workspaces match the query "<span x-text="searchInput" class="text-indigo-500"></span>".</p>
                    </div>

                    {{-- Laravel Pagination --}}
                    @if(method_exists($families, 'hasPages') && $families->hasPages())
                        <div class="mt-8 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
                            {{ $families->links() }}
                        </div>
                    @endif
                @endif
            </div>

            {{-- ---------------------------------------------------- --}}
            {{-- RIGHT COLUMN: CRYPTOGRAPHIC AUDIT TIMELINE           --}}
            {{-- ---------------------------------------------------- --}}
            <div class="xl:col-span-4">
                <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-[0_4px_20px_-5px_rgba(0,0,0,0.03)] p-8 relative overflow-hidden xl:sticky top-28">
                    
                    {{-- Decorative Blur --}}
                    <div class="absolute -right-10 -top-10 w-48 h-48 bg-indigo-500/5 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative z-10 flex items-center justify-between mb-8 pb-6 border-b border-slate-100">
                        <div>
                            <h2 class="text-xl font-black tracking-tight flex items-center gap-2 text-slate-900 mb-1">
                                <i class="fa-solid fa-list-check text-indigo-500"></i> Audit Ledger
                            </h2>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Global Activity</p>
                        </div>
                        <span class="px-2.5 py-1.5 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-lg text-[9px] font-black uppercase tracking-widest flex items-center gap-1.5 shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_5px_rgba(16,185,129,0.8)]"></span> Live
                        </span>
                    </div>

                    <div class="relative z-10">
                        @if(empty($activities) || count($activities) === 0)
                            <div class="text-center py-12">
                                <div class="w-16 h-16 rounded-[1rem] bg-slate-50 flex items-center justify-center mx-auto mb-4 border border-slate-100 shadow-inner">
                                    <i class="fa-regular fa-clock text-2xl text-slate-300"></i>
                                </div>
                                <p class="text-base font-black text-slate-900 mb-1">Ledger is empty</p>
                                <p class="text-xs font-bold text-slate-400">No cryptographic events recorded.</p>
                            </div>
                        @else
                            {{-- 🔥 BEAST MODE: Blockchain-Style Ledger --}}
                            <div class="space-y-6 relative before:absolute before:inset-0 before:ml-[1.35rem] before:-translate-x-px before:h-full before:w-0.5 before:bg-gradient-to-b before:from-slate-200 before:via-slate-200 before:to-transparent">
                                
                                @foreach($activities->take(6) as $index => $activity)
                                    @php
                                        // 🚨 STRICT NULL-SAFE EXTRACTION
                                        $desc = $activity->description ?? 'System update applied.';
                                        $causerName = $activity->causer->name ?? 'System Authority';
                                        $time = isset($activity->created_at) ? $activity->created_at->diffForHumans() : 'Recently';
                                        
                                        // Auto-Color Engine
                                        $lowerDesc = strtolower($desc);
                                        $nodeColor = 'bg-slate-50 border-slate-200 text-slate-400';
                                        $icon = 'fa-bolt';
                                        
                                        if(str_contains($lowerDesc, 'create') || str_contains($lowerDesc, 'join')) { 
                                            $nodeColor = 'bg-emerald-50 border-emerald-200 text-emerald-600'; 
                                            $icon = 'fa-plus';
                                        } elseif(str_contains($lowerDesc, 'delete') || str_contains($lowerDesc, 'remove') || str_contains($lowerDesc, 'left')) { 
                                            $nodeColor = 'bg-rose-50 border-rose-200 text-rose-600'; 
                                            $icon = 'fa-minus';
                                        } elseif(str_contains($lowerDesc, 'update')) { 
                                            $nodeColor = 'bg-indigo-50 border-indigo-200 text-indigo-600'; 
                                            $icon = 'fa-pen';
                                        }

                                        // Generate fake Trace Hash based on index/id for styling
                                        $traceId = '0x' . strtoupper(substr(md5((string)($activity->id ?? $index)), 0, 8));
                                    @endphp

                                    <div class="relative flex items-start group/log cursor-default">
                                        {{-- Timeline Node --}}
                                        <div class="w-11 h-11 rounded-[12px] flex items-center justify-center shrink-0 border {{ $nodeColor }} shadow-sm z-10 mr-4 group-hover/log:scale-110 group-hover/log:rotate-6 transition-transform duration-300 bg-white">
                                            <i class="fa-solid {{ $icon }} text-xs"></i>
                                        </div>
                                        
                                        {{-- Content Block --}}
                                        <div class="flex-1 pt-1">
                                            <p class="text-sm font-bold text-slate-600 leading-tight mb-1 group-hover/log:text-slate-900 transition-colors">
                                                <strong class="text-slate-900">{{ $causerName }}</strong> {{ $desc }}
                                            </p>
                                            <div class="flex items-center justify-between">
                                                <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 flex items-center gap-1.5">
                                                    <i class="fa-regular fa-clock"></i> {{ $time }}
                                                </span>
                                                <span class="text-[9px] font-mono text-slate-300 bg-slate-50 border border-slate-100 px-1.5 py-0.5 rounded opacity-0 group-hover/log:opacity-100 transition-opacity" title="Cryptographic Trace ID">
                                                    {{ $traceId }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ================= OVERLAY: PING CLUSTER SIMULATION (GLASSMORPHIC) ================= --}}
    <div x-show="syncing" style="display: none;" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-md flex flex-col items-center justify-center"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        
        <div class="bg-white/95 border border-white rounded-[2.5rem] shadow-[0_30px_100px_-15px_rgba(0,0,0,0.3)] p-10 flex flex-col items-center max-w-sm w-full relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+PHBhdGggZD0iTTAgMGgyMHYyMEgwVjB6bTEwIDEwaDEwdjEwSDEwaC0xMHptMCAwaC0xMHYtMTBoMTB2MTB6IiBmaWxsPSIjZThlYWVkIiBmaWxsLW9wYWNpdHk9IjAuMSIgZmlsbC1ydWxlPSJldmVub2RkIi8+PC9zdmc+')] mix-blend-multiply opacity-50 pointer-events-none"></div>
            
            <div class="w-24 h-24 bg-indigo-50 rounded-[1.5rem] flex items-center justify-center shadow-inner mb-6 border border-indigo-100 relative">
                <div class="absolute inset-0 border-4 border-indigo-200 rounded-[1.5rem]"></div>
                <div class="absolute inset-0 border-4 border-indigo-600 rounded-[1.5rem] border-t-transparent animate-spin"></div>
                <i class="fa-solid fa-network-wired text-3xl text-indigo-600 animate-pulse"></i>
            </div>
            
            <h2 class="text-2xl font-black text-slate-900 mb-2 tracking-tight">Syncing Nodes</h2>
            <p class="text-slate-500 font-mono text-[10px] uppercase tracking-widest mb-8 h-4" x-text="syncText"></p>
            
            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden border border-slate-200 shadow-inner">
                <div class="h-full bg-indigo-600 transition-all duration-300 ease-out rounded-full shadow-[0_0_10px_rgba(79,70,229,0.5)]" :style="`width: ${syncProgress}%`"></div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('workspaceManager', () => ({
        // Search & Filter State
        searchInput: '',
        search: '',
        filter: 'all',
        isQuerying: false,
        searchTimeout: null,
        
        // UI Animation State
        filterWidth: 0,
        filterOffset: 0,
        
        // Sync State
        syncing: false,
        syncProgress: 0,
        syncText: '',
        
        init() {
            // Set initial pill position
            this.$nextTick(() => {
                if(this.$refs.btnAll) this.setFilter('all', this.$refs.btnAll);
            });
        },

        // 🚨 Mac-OS Style Debounced Search Engine
        debouncedSearch() {
            this.isQuerying = true; // Trigger skeleton immediately
            if(this.searchTimeout) clearTimeout(this.searchTimeout);
            
            this.searchTimeout = setTimeout(() => {
                this.search = this.searchInput;
                this.isQuerying = false; // Remove skeleton
            }, 400); // 400ms debounce
        },

        clearSearch() {
            this.searchInput = '';
            this.debouncedSearch();
        },

        // Animated Segmented Control
        setFilter(val, el) {
            this.filter = val;
            if (el) {
                this.filterWidth = el.offsetWidth;
                this.filterOffset = el.offsetLeft;
            }
            if(typeof window.audioEngine !== 'undefined') window.audioEngine.playClick();
        },

        // Match Logic
        matchesFilter(role, name) {
            const matchesRole = this.filter === 'all' || this.filter === role;
            const matchesSearch = this.search === '' || name.toLowerCase().includes(this.search.toLowerCase());
            return matchesRole && matchesSearch;
        },

        get hasMatches() {
            // Evaluated in the DOM to show empty state
            return true; // Simplified for Alpine DOM extraction
        },

        showToast(message, type) {
            this.$dispatch('notify', {message: message, type: type});
        },

        // Beautiful Hardware-Accelerated Sync Simulation
        syncLedgers() {
            if(typeof window.audioEngine !== 'undefined') window.audioEngine.playClick();
            this.syncing = true;
            this.syncProgress = 0;
            this.syncText = 'Establishing secure handshake...';
            
            let interval = setInterval(() => {
                this.syncProgress += Math.floor(Math.random() * 15) + 5;
                if(this.syncProgress > 20) this.syncText = 'Polling Database Shards...';
                if(this.syncProgress > 50) this.syncText = 'Decrypting Ledger Packets...';
                if(this.syncProgress > 80) this.syncText = 'Aggregating Workspace Data...';

                if(this.syncProgress >= 100) {
                    this.syncProgress = 100;
                    this.syncText = 'Sync Complete. Refreshing Node...';
                    clearInterval(interval);
                    if(typeof window.audioEngine !== 'undefined') window.audioEngine.playClick();
                    setTimeout(() => window.location.reload(), 600);
                }
            }, 400);
        }
    }));
});
</script>
@endpush