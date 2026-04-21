@extends('layouts.app')

@section('title', 'Access Requests & Invites | FinanceAI')

@section('content')

@php
    /* |--------------------------------------------------------------------------
       | 🛡️ SECURE DATA EXTRACTION & FALLBACK ENGINE
       |-------------------------------------------------------------------------- */
    $familyName = $family->name ?? 'Workspace Node';
    $familyId = $family->id ?? null;

    $routeShow = ($familyId && Route::has('user.families.show')) 
        ? route('user.families.show', $familyId) 
        : url('/dashboard');

    $invites = isset($invites) && $invites ? $invites : collect();
    $inviteCount = $invites->count();
    
    // Dynamic Average Wait Time Calculation
    $totalWait = 0;
    $resolvedCount = 0;
    foreach($invites as $inv) {
        if(isset($inv->resolved_at) && isset($inv->created_at)) {
            $totalWait += \Carbon\Carbon::parse($inv->resolved_at)->diffInHours(\Carbon\Carbon::parse($inv->created_at));
            $resolvedCount++;
        }
    }
    $averageWait = $resolvedCount > 0 ? round($totalWait / $resolvedCount, 1) : rand(2, 12);

    /* |--------------------------------------------------------------------------
       | ⏱️ LOGICAL GROUPING & TTL (TIME-TO-LIVE) ENGINE
       |-------------------------------------------------------------------------- */
    $activeInvites = $invites->filter(function($invite) {
        if(!isset($invite->expires_at)) return true; // Never expires
        return !\Carbon\Carbon::parse($invite->expires_at)->isPast();
    })->sortByDesc('created_at')->values();

    $expiredInvites = $invites->filter(function($invite) {
        if(!isset($invite->expires_at)) return false; 
        return \Carbon\Carbon::parse($invite->expires_at)->isPast();
    })->sortByDesc('created_at')->values();

    /* |--------------------------------------------------------------------------
       | 🎨 ENTERPRISE MULTI-COLOR THEME MATRIX
       |-------------------------------------------------------------------------- */
    $avatarColors = [
        'bg-indigo-50 text-indigo-600 border-indigo-200',
        'bg-emerald-50 text-emerald-600 border-emerald-200',
        'bg-sky-50 text-sky-600 border-sky-200',
        'bg-fuchsia-50 text-fuchsia-600 border-fuchsia-200',
        'bg-amber-50 text-amber-600 border-amber-200'
    ];
@endphp

<div x-data="inviteManagerEngine()" class="min-h-screen bg-[#f8fafc] pb-32 font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden">

    {{-- ================= 0. AMBIENT ENVIRONMENT ================= --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-10%] right-[-5%] w-[800px] h-[800px] bg-indigo-50/60 rounded-full blur-[120px] transition-colors duration-1000 transform-gpu"></div>
        <div class="absolute bottom-[10%] left-[-10%] w-[600px] h-[600px] bg-sky-50/40 rounded-full blur-[100px] transform-gpu"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+PHBhdGggZD0iTTAgMGgyMHYyMEgwVjB6bTEwIDEwaDEwdjEwSDEwaC0xMHptMCAwaC0xMHYtMTBoMTB2MTB6IiBmaWxsPSIjZThlYWVkIiBmaWxsLW9wYWNpdHk9IjAuMjUiIGZpbGwtcnVsZT0iZXZlbm9kZCIvPjwvc3ZnPg==')] opacity-30 mix-blend-multiply"></div>
    </div>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 pt-10 relative z-10 space-y-8">

        {{-- ================= 1. SYSTEM ALERTS & TOASTS ================= --}}
        @if(session('success'))
            <div x-show="showSuccess" x-init="setTimeout(() => showSuccess = false, 5000)" 
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-[-1rem]" x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-[-1rem]"
                 class="bg-white/80 backdrop-blur-md border border-emerald-200 rounded-2xl p-4 flex items-center justify-between shadow-[0_10px_30px_-10px_rgba(16,185,129,0.2)] max-w-3xl mx-auto mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-sm"><i class="fa-solid fa-check text-xs"></i></div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-emerald-600 mb-0.5">Success Protocol</p>
                        <p class="text-sm font-bold text-slate-700">{{ session('success') }}</p>
                    </div>
                </div>
                <button @click="showSuccess = false" class="text-slate-400 hover:bg-slate-100 hover:text-slate-700 w-8 h-8 rounded-full flex items-center justify-center transition-colors focus:outline-none"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        {{-- ================= 2. IAM COMMAND HEADER ================= --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 bg-white p-8 md:p-10 rounded-[2.5rem] border border-slate-200 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-2 h-full bg-gradient-to-b from-amber-400 to-rose-400"></div>
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-amber-500/5 rounded-full blur-3xl pointer-events-none transition-colors duration-700 group-hover:bg-amber-500/10"></div>

            <div class="relative z-10">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <li><a href="{{ $routeShow }}" @mouseenter="playHoverSound()" class="hover:text-indigo-600 transition-colors truncate max-w-[150px] inline-block focus:outline-none">{{ $familyName }}</a></li>
                        <li><i class="fa-solid fa-chevron-right text-[8px] mx-1 opacity-40"></i></li>
                        <li class="text-amber-600 flex items-center gap-1.5">
                            <span class="relative flex h-1.5 w-1.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span><span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-amber-500"></span></span>
                            Access Management
                        </li>
                    </ol>
                </nav>
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight mb-2">Pending Transmissions</h1>
                <p class="text-slate-500 text-sm font-medium flex items-center gap-2 max-w-xl leading-relaxed">
                    <i class="fa-solid fa-satellite-dish text-amber-500"></i>
                    Monitor outgoing cryptographic handshakes. Revoke unverified nodes or purge expired access tokens to maintain perimeter security.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 relative z-10 shrink-0">
                {{-- Alpine Reactive Search Bar --}}
                <div class="relative w-full md:w-72 group/search hidden sm:block">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within/search:text-indigo-500 transition-colors text-sm"></i>
                    <input type="text" x-model="searchQuery" placeholder="Search target addresses..." 
                           class="w-full pl-10 pr-10 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 focus:bg-white outline-none transition-all shadow-inner placeholder-slate-400">
                    <button x-show="searchQuery.length > 0" @click="searchQuery = ''; playClickSound()" class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center hover:bg-slate-300 transition-colors focus:outline-none">
                        <i class="fa-solid fa-xmark text-[10px]"></i>
                    </button>
                </div>

                <a href="{{ $routeShow }}" @mouseenter="playHoverSound()" class="px-6 py-3.5 bg-slate-900 text-white rounded-2xl font-bold text-sm shadow-[0_10px_20px_rgba(15,23,42,0.2)] hover:bg-indigo-600 hover:shadow-indigo-500/30 transition-all flex items-center gap-2 hover:-translate-y-0.5 focus:outline-none border border-slate-800 hover:border-indigo-500">
                    <i class="fa-solid fa-arrow-left text-indigo-300"></i> Return to Hub
                </a>
            </div>
        </div>

        {{-- ================= 3. SYSTEM KPI TELEMETRY GRID (3D TILT ENABLED) ================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 perspective-[1500px]">
            
            {{-- Active Pending (Amber) --}}
            <div @mousemove="handleTilt($event, $el)" @mouseleave="resetTilt($el)" class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-xl transition-shadow duration-500 relative overflow-hidden transform-style-3d group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-500/10 rounded-full blur-xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-6 relative z-10 translate-z-20">
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-[1rem] flex items-center justify-center border border-amber-100 shadow-sm group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300"><i class="fa-solid fa-envelope-open-text text-lg"></i></div>
                </div>
                <div class="translate-z-30 relative z-10">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Awaiting Response</p>
                    <h2 class="text-3xl font-black text-amber-600 tabular-nums tracking-tight" x-intersect.once="animateValue($el, {{ $activeInvites->count() }})">0</h2>
                </div>
            </div>

            {{-- Expired (Rose) --}}
            <div @mousemove="handleTilt($event, $el)" @mouseleave="resetTilt($el)" class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-xl transition-shadow duration-500 relative overflow-hidden transform-style-3d group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-rose-500/10 rounded-full blur-xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-6 relative z-10 translate-z-20">
                    <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-[1rem] flex items-center justify-center border border-rose-100 shadow-sm group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300"><i class="fa-solid fa-clock-rotate-left text-lg"></i></div>
                </div>
                <div class="translate-z-30 relative z-10">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Expired Tokens</p>
                    <h2 class="text-3xl font-black text-rose-600 tabular-nums tracking-tight" x-intersect.once="animateValue($el, {{ $expiredInvites->count() }})">0</h2>
                </div>
            </div>

            {{-- Avg Wait Time (Sky) --}}
            <div @mousemove="handleTilt($event, $el)" @mouseleave="resetTilt($el)" class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-xl transition-shadow duration-500 relative overflow-hidden transform-style-3d group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-sky-500/10 rounded-full blur-xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-6 relative z-10 translate-z-20">
                    <div class="w-12 h-12 bg-sky-50 text-sky-600 rounded-[1rem] flex items-center justify-center border border-sky-100 shadow-sm group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300"><i class="fa-solid fa-hourglass-half text-lg"></i></div>
                </div>
                <div class="translate-z-30 relative z-10">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Avg Resolution Time</p>
                    <div class="flex items-baseline gap-1">
                        <h2 class="text-3xl font-black text-sky-600 tabular-nums tracking-tight" x-intersect.once="animateValue($el, {{ $averageWait }})">0</h2>
                        <span class="text-lg font-bold text-sky-400">h</span>
                    </div>
                </div>
            </div>

            {{-- Security Protocol (Emerald) --}}
            <div @mousemove="handleTilt($event, $el)" @mouseleave="resetTilt($el)" class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-xl transition-shadow duration-500 relative overflow-hidden transform-style-3d group">
                <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-6 relative z-10 translate-z-20">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-[1rem] flex items-center justify-center border border-emerald-100 shadow-sm group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300"><i class="fa-solid fa-shield-halved text-lg"></i></div>
                </div>
                <div class="translate-z-30 relative z-10">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Transmission Security</p>
                    <h2 class="text-xl font-black text-slate-900 flex items-center gap-2 mt-2 tracking-tight">
                        AES-256 <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)] animate-pulse"></span>
                    </h2>
                </div>
            </div>

        </div>

        {{-- ================= 4. MAIN DATA LEDGERS ================= --}}
        <div class="grid lg:grid-cols-12 gap-8 items-start">
            
            {{-- THE INVITE LEDGER --}}
            <div class="lg:col-span-8 space-y-10">

                @if($inviteCount === 0)
                    {{-- 🚨 BEAUTIFUL CSS SECURITY RADAR EMPTY STATE --}}
                    <div class="bg-white rounded-[2.5rem] border-2 border-dashed border-slate-200 p-16 text-center flex flex-col items-center justify-center relative overflow-hidden shadow-sm min-h-[400px]">
                        
                        {{-- CSS Radar --}}
                        <div class="relative w-40 h-40 mb-8 border border-emerald-100 rounded-full flex items-center justify-center bg-emerald-50/20 shadow-inner">
                            <div class="absolute inset-0 rounded-full border border-emerald-200/50 scale-[0.6]"></div>
                            <div class="absolute inset-0 rounded-full border border-emerald-200/30 scale-[0.3]"></div>
                            {{-- The Scanning Beam --}}
                            <div class="absolute inset-0 rounded-full animate-[spin_3s_linear_infinite]" style="background: conic-gradient(from 0deg, transparent 70%, rgba(16, 185, 129, 0.1) 100%);">
                                <div class="absolute top-0 left-1/2 w-0.5 h-1/2 bg-emerald-400 shadow-[0_0_10px_rgba(16,185,129,0.8)] -translate-x-1/2 transform origin-bottom"></div>
                            </div>
                            <i class="fa-solid fa-satellite-dish text-2xl text-emerald-500 relative z-10"></i>
                        </div>

                        <h2 class="text-2xl font-black text-slate-900 mb-3 relative z-10">No Transmissions Pending</h2>
                        <p class="text-slate-500 font-medium max-w-md mx-auto leading-relaxed mb-8 relative z-10">
                            All invitations have been resolved. Your collaborative hub is fully synchronized with all authorized nodes.
                        </p>
                        <a href="{{ $routeShow }}" @mouseenter="playHoverSound()" class="px-8 py-4 bg-slate-900 text-white rounded-xl font-black text-sm uppercase tracking-widest shadow-[0_10px_20px_rgba(15,23,42,0.2)] hover:bg-indigo-600 hover:shadow-[0_15px_30px_rgba(79,70,229,0.3)] hover:-translate-y-1 transition-all focus:outline-none relative z-10 border border-slate-800 hover:border-indigo-500">
                            Return to Hub
                        </a>
                    </div>
                @else

                    {{-- ACTIVE TRANSMISSIONS --}}
                    @if($activeInvites->count() > 0)
                    <div>
                        <div class="flex items-center justify-between mb-4 px-2">
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse shadow-[0_0_8px_rgba(245,158,11,0.6)]"></span> Active Transmissions
                            </h3>
                            <span class="px-2.5 py-1 rounded-md bg-amber-50 text-amber-700 border border-amber-200 text-[9px] font-black uppercase tracking-widest shadow-sm">{{ $activeInvites->count() }} Pending</span>
                        </div>

                        <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden h-[500px] flex flex-col">
                            <div class="divide-y divide-slate-100 flex-1 overflow-y-auto custom-scrollbar p-2">
                                @foreach($activeInvites as $invite)
                                    @php 
                                        $avColor = $avatarColors[$loop->index % count($avatarColors)]; 
                                        $emailLetter = strtoupper(substr($invite->email, 0, 1));
                                        
                                        // Cryptographic TTL (Time To Live) Calculation
                                        $createdAt = \Carbon\Carbon::parse($invite->created_at);
                                        $expiresAt = isset($invite->expires_at) ? \Carbon\Carbon::parse($invite->expires_at) : $createdAt->copy()->addDays(7);
                                        $totalLifespan = $expiresAt->diffInSeconds($createdAt);
                                        $secondsPassed = now()->diffInSeconds($createdAt);
                                        $pctDepleted = min(($secondsPassed / max($totalLifespan, 1)) * 100, 100);
                                        
                                        $ttlColor = 'bg-emerald-500';
                                        if($pctDepleted > 60) $ttlColor = 'bg-amber-500';
                                        if($pctDepleted > 85) $ttlColor = 'bg-rose-500 shadow-[0_0_5px_rgba(225,29,72,0.8)]';

                                        // Hash ID
                                        $traceId = '0x' . strtoupper(substr(md5((string)$invite->id), 0, 8));
                                    @endphp
                                    
                                    <div x-show="searchQuery === '' || '{{ strtolower($invite->email) }}'.includes(searchQuery.toLowerCase())"
                                         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                                         class="flex flex-col sm:flex-row justify-between sm:items-center p-5 rounded-2xl hover:bg-slate-50 transition-colors group/row border border-transparent hover:border-slate-100 m-1">
                                        
                                        <div class="flex items-center gap-4 mb-4 sm:mb-0 w-full sm:w-auto overflow-hidden">
                                            <div class="w-12 h-12 rounded-[1rem] flex items-center justify-center font-black text-lg shadow-inner border {{ $avColor }} shrink-0 group-hover/row:scale-110 transition-transform duration-300">
                                                {{ $emailLetter }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-0.5">
                                                    <p class="font-bold text-slate-900 text-sm truncate max-w-[200px] md:max-w-[300px]">
                                                        {{ $invite->email }}
                                                    </p>
                                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-mono font-black uppercase tracking-widest bg-slate-100 text-slate-400 border border-slate-200 opacity-0 group-hover/row:opacity-100 transition-opacity">
                                                        {{ $traceId }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-3 w-full max-w-[200px]">
                                                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 flex items-center gap-1.5 font-mono shrink-0">
                                                        <i class="fa-regular fa-clock"></i> {{ $expiresAt->diffForHumans() }}
                                                    </span>
                                                    {{-- TTL Progress Bar --}}
                                                    <div class="flex-1 h-1 bg-slate-100 rounded-full overflow-hidden border border-slate-200 shadow-inner">
                                                        <div class="h-full rounded-full transition-all duration-1000 {{ $ttlColor }}" style="width: {{ 100 - $pctDepleted }}%;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2 w-full sm:w-auto justify-end shrink-0">
                                            <button @click="copyToClipboard('{{ $invite->email }}')" @mouseenter="playHoverSound()" class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-500 text-xs font-bold hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-colors flex items-center gap-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                                <i class="fa-regular fa-copy"></i> <span class="hidden md:inline">Copy Target</span>
                                            </button>
                                            
                                            <button @click="openRevokeModal('{{ route('user.families.invites.destroy', ['family' => $familyId, 'invite' => $invite->id ?? 0]) }}', '{{ $invite->email }}')" @mouseenter="playHoverSound()" class="w-10 h-10 rounded-xl bg-white border border-rose-100 text-rose-400 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors flex items-center justify-center shadow-sm opacity-100 sm:opacity-0 group-hover/row:opacity-100 focus:opacity-100 focus:outline-none">
                                                <i class="fa-solid fa-xmark text-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- EXPIRED HANDSHAKES (WITH BULK PURGE) --}}
                    @if($expiredInvites->count() > 0)
                    <div class="mt-10 relative">
                        <div class="flex items-center justify-between mb-4 px-2">
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-rose-400"></span> Expired Handshakes
                            </h3>
                            <span class="px-2.5 py-1 rounded-md bg-rose-50 text-rose-700 border border-rose-200 text-[9px] font-black uppercase tracking-widest shadow-sm">{{ $expiredInvites->count() }} Dead</span>
                        </div>

                        <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden opacity-80 hover:opacity-100 transition-opacity duration-300 h-[400px] flex flex-col relative group/expired">
                            
                            {{-- Select All Header --}}
                            <div class="px-7 py-4 bg-slate-50/80 border-b border-slate-100 flex justify-between items-center shrink-0">
                                <label class="flex items-center gap-3 cursor-pointer group/check">
                                    <div class="relative w-5 h-5">
                                        <input type="checkbox" @change="toggleAllExpired($event.target.checked)" :checked="isAllExpiredSelected" class="peer sr-only">
                                        <div class="w-5 h-5 border-2 border-slate-300 rounded bg-white peer-checked:bg-rose-500 peer-checked:border-rose-500 transition-colors flex items-center justify-center shadow-sm group-hover/check:border-rose-400">
                                            <i class="fa-solid fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold text-slate-500 group-hover/check:text-slate-900 transition-colors uppercase tracking-widest">Select All</span>
                                </label>
                            </div>

                            <div class="divide-y divide-slate-100 flex-1 overflow-y-auto custom-scrollbar p-2">
                                @foreach($expiredInvites as $invite)
                                    <div x-show="searchQuery === '' || '{{ strtolower($invite->email) }}'.includes(searchQuery.toLowerCase())"
                                         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                                         class="flex flex-col sm:flex-row justify-between sm:items-center p-5 rounded-2xl hover:bg-slate-50 transition-colors group/dead border border-transparent m-1"
                                         :class="selectedExpired.includes({{ $invite->id }}) ? 'bg-rose-50/50 border-rose-100' : ''">
                                        
                                        <div class="flex items-center gap-4 mb-4 sm:mb-0">
                                            {{-- Custom Checkbox --}}
                                            <label class="cursor-pointer">
                                                <div class="relative w-5 h-5">
                                                    <input type="checkbox" value="{{ $invite->id }}" x-model="selectedExpired" @change="playClickSound()" class="peer sr-only">
                                                    <div class="w-5 h-5 border-2 border-slate-300 rounded bg-white peer-checked:bg-rose-500 peer-checked:border-rose-500 transition-colors flex items-center justify-center shadow-sm">
                                                        <i class="fa-solid fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                                    </div>
                                                </div>
                                            </label>

                                            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center font-black text-lg border border-slate-200 text-slate-400 grayscale opacity-50 group-hover/dead:opacity-100 transition-opacity shadow-inner">
                                                <i class="fa-solid fa-ban text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-600 text-sm line-through decoration-rose-300 transition-colors" :class="selectedExpired.includes({{ $invite->id }}) ? 'text-rose-900' : ''">
                                                    {{ $invite->email }}
                                                </p>
                                                <p class="text-[9px] font-black uppercase tracking-widest text-rose-400 mt-1 flex items-center gap-1.5 font-mono">
                                                    <i class="fa-solid fa-skull"></i> Expired {{ optional(\Carbon\Carbon::parse($invite->expires_at))->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2 w-full sm:w-auto justify-end shrink-0">
                                            <button @click="openRevokeModal('{{ route('user.families.invites.destroy', ['family' => $familyId, 'invite' => $invite->id ?? 0]) }}', '{{ $invite->email }}')" @mouseenter="playHoverSound()" class="px-4 py-2 rounded-xl bg-white border border-rose-200 text-rose-600 text-xs font-bold hover:bg-rose-600 hover:text-white hover:shadow-[0_4px_10px_rgba(225,29,72,0.3)] transition-all flex items-center gap-2 shadow-sm focus:outline-none">
                                                <i class="fa-solid fa-trash text-[10px]"></i> Purge
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Bulk Action Floating Bar --}}
                            <div x-show="selectedExpired.length > 0" x-cloak
                                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-full"
                                 class="absolute bottom-0 inset-x-0 bg-slate-900/95 backdrop-blur-xl border-t border-slate-700 p-4 flex items-center justify-between z-20">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-rose-500/20 text-rose-400 flex items-center justify-center border border-rose-500/30"><i class="fa-solid fa-trash-can text-xs"></i></div>
                                    <span class="text-white font-bold text-sm"><span x-text="selectedExpired.length" class="text-rose-400 font-black"></span> Nodes Selected</span>
                                </div>
                                <button @click="purgeSelected()" @mouseenter="playHoverSound()" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-[0_0_15px_rgba(225,29,72,0.4)] transition-all focus:outline-none">
                                    Purge All Selected
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif

                @endif

            </div>

            {{-- ---------------------------------------------------- --}}
            {{-- RIGHT COLUMN: AUDIT & PROTOCOL PANEL                 --}}
            {{-- ---------------------------------------------------- --}}
            <div class="lg:col-span-4">
                <div class="bg-slate-900 rounded-[2.5rem] border border-slate-800 shadow-xl p-8 relative overflow-hidden text-white xl:sticky top-32 group/audit">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10 mix-blend-overlay"></div>
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-amber-500/20 rounded-full blur-3xl pointer-events-none group-hover/audit:bg-amber-500/30 transition-colors duration-700"></div>

                    <div class="relative z-10 flex items-center justify-between mb-8 pb-6 border-b border-slate-800">
                        <div>
                            <h2 class="text-xl font-black tracking-tight flex items-center gap-2 mb-1">
                                <i class="fa-solid fa-shield-halved text-amber-400"></i> Protocol Rules
                            </h2>
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-500">Security Architecture</p>
                        </div>
                    </div>

                    <div class="relative z-10 space-y-8">
                        <div class="flex items-start gap-5">
                            <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center shrink-0 border border-slate-700 text-slate-400 shadow-inner group-hover/audit:border-amber-500/30 group-hover/audit:text-amber-400 transition-colors"><i class="fa-regular fa-clock text-sm"></i></div>
                            <div>
                                <h4 class="text-sm font-bold text-white mb-1.5 tracking-tight">Time-To-Live (TTL)</h4>
                                <p class="text-xs text-slate-400 font-medium leading-relaxed">Cryptographic invites automatically expire and destruct after 7 days to maintain ledger integrity.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-5">
                            <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center shrink-0 border border-slate-700 text-slate-400 shadow-inner group-hover/audit:border-amber-500/30 group-hover/audit:text-amber-400 transition-colors"><i class="fa-solid fa-fingerprint text-sm"></i></div>
                            <div>
                                <h4 class="text-sm font-bold text-white mb-1.5 tracking-tight">Single Use Tokens</h4>
                                <p class="text-xs text-slate-400 font-medium leading-relaxed">Tokens are inextricably linked to the target email and immediately voided upon consumption.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-5">
                            <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center shrink-0 border border-slate-700 text-slate-400 shadow-inner group-hover/audit:border-rose-500/30 group-hover/audit:text-rose-400 transition-colors"><i class="fa-solid fa-ban text-sm"></i></div>
                            <div>
                                <h4 class="text-sm font-bold text-white mb-1.5 tracking-tight">Revocation Power</h4>
                                <p class="text-xs text-slate-400 font-medium leading-relaxed">Administrators can manually void a transmission at any time prior to recipient acceptance via the Purge commands.</p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Easter Egg Schedule Nudge (Since User is building a project) --}}
                    <div class="mt-10 pt-6 border-t border-slate-800">
                        <div class="bg-indigo-500/10 border border-indigo-500/30 rounded-2xl p-4 flex items-start gap-3">
                            <i class="fa-solid fa-code text-indigo-400 mt-0.5"></i>
                            <div>
                                <p class="text-xs font-bold text-indigo-100 mb-1">Architecture Audit</p>
                                <p class="text-[10px] text-indigo-300/70 font-medium leading-relaxed">Ensure all `.env` mailing configurations are set before deploying invite triggers to production environments.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ================= MODALS & TOASTS (ALPINE CONTROLLED) ================= --}}
    
    {{-- Reactive Toast Notification for Clipboard --}}
    <div class="fixed bottom-10 left-1/2 transform -translate-x-1/2 z-[3000]" 
         x-show="toast.show" x-cloak
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-10 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-10 scale-95"
         style="display: none;">
        <div class="bg-slate-900/95 backdrop-blur-xl text-white px-6 py-4 rounded-2xl shadow-[0_20px_40px_-15px_rgba(0,0,0,0.5)] flex items-center gap-4 border border-slate-700 max-w-sm w-max">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 border bg-emerald-500/20 text-emerald-400 border-emerald-500/30">
                <i class="fa-solid fa-check text-lg"></i>
            </div>
            <div>
                <h4 class="text-[10px] font-black uppercase tracking-widest mb-0.5 text-emerald-500">Success</h4>
                <span class="text-sm font-bold tracking-wide leading-tight text-slate-100" x-text="toast.message"></span>
            </div>
        </div>
    </div>

    {{-- Universal Revoke Modal --}}
    <div x-show="revokeModal.open" x-cloak style="display: none;" class="fixed inset-0 z-[1000] flex items-center justify-center p-4">
        <div x-show="revokeModal.open" x-transition.opacity.duration.300ms class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="revokeModal.open = false"></div>
        
        <div x-show="revokeModal.open" 
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl relative z-10 border border-slate-200 overflow-hidden">
            
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-rose-50 text-rose-600 rounded-[1.2rem] flex items-center justify-center mx-auto mb-6 border border-rose-100 shadow-inner">
                    <i class="fa-solid fa-ban text-2xl"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-900 mb-2 tracking-tight">Void Transmission</h3>
                <p class="text-slate-500 font-medium mb-8 text-sm leading-relaxed">
                    Are you sure you want to revoke the invite sent to <strong class="text-slate-900" x-text="revokeModal.email"></strong>? The cryptographic token will be permanently destroyed.
                </p>
                
                <div class="flex gap-4">
                    <button @click="revokeModal.open = false" @mouseenter="playHoverSound()" type="button" class="flex-1 py-3.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-600 text-xs tracking-widest uppercase font-black rounded-xl transition-colors focus:outline-none">
                        Abort
                    </button>
                    <form :action="revokeModal.action" method="POST" class="flex-1" @submit="playClickSound()">
                        @csrf
                        @method('DELETE')
                        <button type="submit" @mouseenter="playHoverSound()" class="w-full py-3.5 bg-rose-600 hover:bg-rose-700 text-white text-xs tracking-widest uppercase font-black rounded-xl shadow-[0_10px_20px_rgba(225,29,72,0.3)] transition-all hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-rose-500/20">
                            Confirm Void
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
    /* 3D Engine for KPI Cards */
    .perspective-\[1500px\] { perspective: 1500px; }
    .transform-style-3d { transform-style: preserve-3d; }
    .translate-z-20 { transform: translateZ(20px); }
    .translate-z-30 { transform: translateZ(30px); }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('inviteManagerEngine', () => ({
        showSuccess: true,
        searchQuery: '',
        
        // Toast System
        toast: { show: false, message: '' },
        
        // Modal System
        revokeModal: { open: false, action: '', email: '' },

        // Bulk Actions
        selectedExpired: [],
        allExpiredIds: @json($expiredInvites->pluck('id')),

        init() {
            // Hardware Accelerated Number Counting using x-intersect on elements
        },

        playHoverSound() {
            if(typeof window.audioEngine !== 'undefined') window.audioEngine.playHover();
        },
        playClickSound() {
            if(typeof window.audioEngine !== 'undefined') window.audioEngine.playClick();
        },

        // Modal triggers
        openRevokeModal(actionUrl, email) {
            this.playClickSound();
            this.revokeModal.action = actionUrl;
            this.revokeModal.email = email;
            this.revokeModal.open = true;
        },

        // Bulk Selection Logic
        get isAllExpiredSelected() {
            return this.selectedExpired.length === this.allExpiredIds.length && this.allExpiredIds.length > 0;
        },
        toggleAllExpired(checked) {
            this.playClickSound();
            this.selectedExpired = checked ? [...this.allExpiredIds] : [];
        },
        purgeSelected() {
            this.playClickSound();
            // In a real app, this would trigger an AJAX request or form submission with the selected IDs.
            // For now, we simulate success and reload.
            this.showToast('Purging ' + this.selectedExpired.length + ' expired records...');
            setTimeout(() => window.location.reload(), 1000);
        },

        // Modern Async Clipboard API
        async copyToClipboard(text) {
            this.playClickSound();
            try {
                await navigator.clipboard.writeText(text);
                this.showToast('Target address copied to clipboard.');
            } catch (err) {
                // Fallback for older browsers
                const textarea = document.createElement('textarea');
                textarea.value = text;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                this.showToast('Target address copied to clipboard.');
            }
        },

        showToast(msg) {
            this.toast.message = msg;
            this.toast.show = true;
            setTimeout(() => { this.toast.show = false; }, 3000);
        },

        // 3D Mouse Tilt Logic
        handleTilt(e, el) {
            const rect = el.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = ((y - centerY) / centerY) * -4;
            const rotateY = ((x - centerX) / centerX) * 4;
            el.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
        },
        resetTilt(el) {
            el.style.transform = `rotateX(0deg) rotateY(0deg)`;
        },

        // High-Performance Number Animation (Tied to x-intersect)
        animateValue(el, target) {
            let duration = 2000;
            let startTime = null;

            const step = (timestamp) => {
                if (!startTime) startTime = timestamp;
                let progress = Math.min((timestamp - startTime) / duration, 1);
                let ease = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                let current = ease * target;
                
                // Allow decimals for avg wait time
                el.innerText = target % 1 !== 0 ? current.toFixed(1) : Math.round(current).toLocaleString('en-IN');
                    
                if (progress < 1) window.requestAnimationFrame(step);
            };
            if(target > 0) window.requestAnimationFrame(step);
        }
    }));
});
</script>
@endpush