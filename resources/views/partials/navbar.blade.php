{{-- ====================================================================== --}}
{{-- 🚀 FINANCE AI: ENTERPRISE MASTER NAVBAR & TELEMETRY HUD                --}}
{{-- ====================================================================== --}}

@php
    /** @var \App\Models\User|null $user */
    $user = auth()->user();
    
    // 1. Telemetry & Alert Data Simulation (Fallback safe)
    $unreadAlerts = $unreadAlerts ?? 3;
    $hasAlerts = $unreadAlerts > 0;
    
    // 2. User Identity & Role Logic (CRASH-PROOF ENGINE)
    $userRole = 'Standard Node';
    $userInitials = 'U';
    $userName = 'Operator';
    $userEmail = 'node@finance.ai';

    if ($user) {
        $userName = $user->name ?? 'System Operator';
        $userEmail = $user->email ?? 'node@finance.ai';

        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            $userRole = 'Master Administrator';
        } else {
            $userRole = ucfirst($user->role ?? 'Node Operator');
        }

        // Bulletproof Initials Extraction
        $nameParts = array_filter(explode(' ', trim($userName)));
        if (count($nameParts) >= 2) {
            $userInitials = strtoupper(substr(array_shift($nameParts), 0, 1) . substr(array_pop($nameParts), 0, 1));
        } else {
            $userInitials = strtoupper(substr($userName, 0, 2));
        }
        $userInitials = empty($userInitials) ? 'U' : $userInitials;
    }

    // 3. Dynamic Breadcrumb Engine
    $currentRoute = Route::currentRouteName() ?? '';
    $breadcrumbs = ['Network' => '#'];
    
    if (str_contains($currentRoute, 'dashboard')) {
        $breadcrumbs['Command Center'] = Route::has('user.dashboard') ? route('user.dashboard') : '#';
    } elseif (str_contains($currentRoute, 'income')) {
        $breadcrumbs['Ledgers'] = '#';
        $breadcrumbs['Inflows'] = Route::has('user.incomes.index') ? route('user.incomes.index') : '#';
    } elseif (str_contains($currentRoute, 'expense')) {
        $breadcrumbs['Ledgers'] = '#';
        $breadcrumbs['Outflows'] = Route::has('user.expenses.index') ? route('user.expenses.index') : '#';
    } elseif (str_contains($currentRoute, 'profile')) {
        $breadcrumbs['Identity'] = '#';
        $breadcrumbs['Security Settings'] = Route::has('user.profile.index') ? route('user.profile.index') : '#';
    } else {
        $breadcrumbs['Active Session'] = '#';
    }

    // Contextual Workspace Name (Safe Resolution)
    $activeContext = isset($family) && $family ? $family->name : 'Personal Workspace';
    $systemTime = now()->setTimezone('Asia/Kolkata')->format('H:i:s T');
@endphp

{{-- ================= THE ENTERPRISE NAVBAR SHELL ================= --}}
<nav x-data="navbarEngine()" 
     class="sticky top-0 z-[45] transition-all duration-500 w-full no-print transform-gpu"
     :class="isScrolled ? 'bg-white/85 backdrop-blur-2xl border-b border-slate-200/80 shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)]' : 'bg-transparent border-b border-transparent'">

    <div class="max-w-[1800px] mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">

        {{-- ================= 1. LEFT: CONTEXT & BREADCRUMBS ================= --}}
        <div class="flex items-center gap-4 md:gap-8 shrink-0">
            
            {{-- Mobile Sidebar Command Trigger --}}
            <button @click="sidebarOpen = true" 
                    @mouseenter="triggerHoverSound()"
                    aria-label="Toggle Navigation"
                    class="lg:hidden w-11 h-11 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center shadow-sm hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50/50 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-indigo-500/10">
                <i class="fa-solid fa-bars-staggered text-sm"></i>
            </button>

            {{-- Mobile Identity Brand (Only shows on mobile) --}}
            <a href="{{ Route::has('user.dashboard') ? route('user.dashboard') : '#' }}" class="flex lg:hidden items-center gap-3 group focus:outline-none">
                <div class="h-10 w-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-cube text-lg"></i>
                </div>
            </a>

            {{-- Workspace Navigation Context (Desktop Only) --}}
            <div class="hidden lg:flex items-center">
                <div class="flex flex-col">
                    {{-- Environment Badge & Clock --}}
                    <div class="flex items-center gap-3 mb-1">
                        <div class="flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-indigo-50 border border-indigo-100 shadow-inner group cursor-default">
                            <span class="relative flex h-1.5 w-1.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-indigo-500"></span>
                            </span>
                            <span class="text-[9px] font-black uppercase tracking-[0.2em] text-indigo-600">Production Node</span>
                            <span class="text-[9px] font-bold text-indigo-400 font-mono ml-1 opacity-0 group-hover:opacity-100 transition-opacity" x-text="networkPing + 'ms'"></span>
                        </div>
                        <span class="text-[9px] font-bold text-slate-400 font-mono tracking-widest">{{ $systemTime }}</span>
                    </div>

                    {{-- Dynamic Breadcrumbs --}}
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-black text-slate-900 tracking-tight leading-none">{{ $activeContext }}</h2>
                        <div class="h-4 w-px bg-slate-300 mx-1"></div>
                        
                        <nav class="flex items-center gap-1.5 text-xs font-bold text-slate-400">
                            @foreach($breadcrumbs as $label => $url)
                                @if(!$loop->first)
                                    <i class="fa-solid fa-chevron-right text-[8px] opacity-40 mx-0.5"></i>
                                @endif
                                @if($url !== '#' && !$loop->last)
                                    <a href="{{ $url }}" class="hover:text-indigo-600 transition-colors focus:outline-none">{{ $label }}</a>
                                @else
                                    <span class="{{ $loop->last ? 'text-indigo-600' : '' }}">{{ $label }}</span>
                                @endif
                            @endforeach
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= 2. MIDDLE: INTELLIGENT SEARCH HUB (TYPEWRITER) ================= --}}
        <div class="hidden md:flex flex-1 max-w-2xl px-8">
            <button @click="if(typeof toggleCommandPalette === 'function') toggleCommandPalette()" 
                    @mouseenter="triggerHoverSound()"
                    class="w-full flex items-center justify-between px-5 py-3 bg-white border border-slate-200 rounded-2xl text-slate-400 hover:border-indigo-300 hover:text-indigo-500 hover:shadow-[0_8px_20px_-5px_rgba(79,70,229,0.15)] transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 group relative overflow-hidden">
                
                {{-- Ambient Hover Glow inside search bar --}}
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-indigo-50/40 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-in-out pointer-events-none"></div>

                <div class="flex items-center gap-4 relative z-10 w-full overflow-hidden">
                    <i class="fa-solid fa-magnifying-glass text-sm group-hover:scale-110 transition-transform duration-300 text-indigo-400 shrink-0"></i>
                    
                    {{-- 🔥 BEAST MODE: Alpine Typewriter Effect --}}
                    <div class="flex items-center text-sm font-bold tracking-wide truncate">
                        <span x-text="typewriterText"></span><span class="w-1 h-4 bg-indigo-400 ml-0.5 animate-pulse"></span>
                    </div>
                </div>

                <div class="flex gap-1.5 relative z-10 shrink-0 ml-4">
                    <kbd class="text-[10px] font-mono font-black border border-slate-200 rounded-md px-2 py-1 bg-slate-50 shadow-sm group-hover:border-indigo-200 group-hover:text-indigo-600 group-hover:bg-indigo-50 transition-all">⌘</kbd>
                    <kbd class="text-[10px] font-mono font-black border border-slate-200 rounded-md px-2 py-1 bg-slate-50 shadow-sm group-hover:border-indigo-200 group-hover:text-indigo-600 group-hover:bg-indigo-50 transition-all">K</kbd>
                </div>
            </button>
        </div>

        {{-- ================= 3. RIGHT: ACTIONS, ALERTS & IDENTITY ================= --}}
        @auth
        <div class="flex items-center gap-2 sm:gap-4 relative z-10 shrink-0">

            {{-- ---------------------------------------------------- --}}
            {{-- A. MEGA QUICK ADD SUITE                              --}}
            {{-- ---------------------------------------------------- --}}
            <div class="hidden sm:block relative" x-data="{ quickAdd: false }" @click.away="quickAdd = false">
                <button @click="quickAdd = !quickAdd"
                        @mouseenter="triggerHoverSound()"
                        class="h-11 px-5 rounded-2xl bg-slate-900 text-white text-[11px] font-black uppercase tracking-widest flex items-center gap-2.5 shadow-[0_10px_20px_-5px_rgba(0,0,0,0.3)] hover:bg-indigo-600 hover:-translate-y-0.5 transition-all duration-300 focus:outline-none border border-slate-800 hover:border-indigo-500 hover:shadow-[0_15px_30px_-5px_rgba(79,70,229,0.4)]">
                    <i class="fa-solid fa-plus text-indigo-400"></i> Record
                </button>
                
                {{-- Quick Add Mega-Menu --}}
                <div x-show="quickAdd" x-cloak
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                     class="absolute top-full right-0 mt-4 w-[450px] bg-white/95 backdrop-blur-2xl border border-slate-200 rounded-[2rem] shadow-[0_25px_60px_-15px_rgba(0,0,0,0.15)] overflow-hidden z-[100] p-3 flex gap-3">
                    
                    {{-- Left Column: Actions --}}
                    <div class="flex-1 flex flex-col gap-1">
                        <div class="px-3 pt-2 pb-2 border-b border-slate-100 mb-2 flex justify-between items-center">
                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Ledger Entry</span>
                        </div>

                        <a href="{{ Route::has('user.incomes.create') ? route('user.incomes.create') : '#' }}" 
                           @click="triggerClick()" @mouseenter="triggerHoverSound()"
                           class="flex items-center gap-4 px-3 py-3 rounded-2xl hover:bg-emerald-50 group transition-all duration-300 border border-transparent hover:border-emerald-100">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center border border-emerald-200 shrink-0 group-hover:scale-110 transition-transform shadow-sm"><i class="fa-solid fa-arrow-trend-up text-sm"></i></div>
                            <div class="flex-1">
                                <span class="block text-sm font-black text-slate-900 group-hover:text-emerald-700 transition-colors">Capital Inflow</span>
                                <span class="block text-[9px] font-bold text-slate-400 uppercase mt-0.5">Record revenue</span>
                            </div>
                            <span class="text-[9px] font-black text-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity bg-emerald-100/50 px-1.5 py-0.5 rounded">⇧I</span>
                        </a>

                        <a href="{{ Route::has('user.expenses.create') ? route('user.expenses.create') : '#' }}" 
                           @click="triggerClick()" @mouseenter="triggerHoverSound()"
                           class="flex items-center gap-4 px-3 py-3 rounded-2xl hover:bg-rose-50 group transition-all duration-300 border border-transparent hover:border-rose-100">
                            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center border border-rose-200 shrink-0 group-hover:scale-110 transition-transform shadow-sm"><i class="fa-solid fa-arrow-trend-down text-sm"></i></div>
                            <div class="flex-1">
                                <span class="block text-sm font-black text-slate-900 group-hover:text-rose-700 transition-colors">Capital Outflow</span>
                                <span class="block text-[9px] font-bold text-slate-400 uppercase mt-0.5">Record expense</span>
                            </div>
                            <span class="text-[9px] font-black text-rose-400 opacity-0 group-hover:opacity-100 transition-opacity bg-rose-100/50 px-1.5 py-0.5 rounded">⇧E</span>
                        </a>
                    </div>

                    {{-- Right Column: Intelligence --}}
                    <div class="w-40 bg-slate-50 rounded-2xl p-4 border border-slate-100 flex flex-col justify-between">
                        <div>
                            <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center mb-3"><i class="fa-solid fa-brain text-xs"></i></div>
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-900 mb-1">Smart Sync</h4>
                            <p class="text-[9px] font-bold text-slate-500 leading-relaxed">Connect your bank API to automate these entries.</p>
                        </div>
                        <a href="#" class="text-[9px] font-black uppercase tracking-widest text-indigo-600 hover:text-indigo-800 transition-colors mt-4">Setup API &rarr;</a>
                    </div>
                </div>
            </div>

            <div class="w-px h-8 bg-slate-200 mx-1 hidden sm:block"></div>

            {{-- ---------------------------------------------------- --}}
            {{-- B. BACKGROUND TASK RUNNER (DYNAMIC)                  --}}
            {{-- ---------------------------------------------------- --}}
            <div class="relative hidden sm:block" x-data="{ tasksOpen: false }" @click.away="tasksOpen = false">
                <button @click="tasksOpen = !tasksOpen" 
                        @mouseenter="triggerHoverSound()"
                        class="relative w-11 h-11 rounded-2xl bg-white border border-slate-200 text-slate-500 hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50/50 flex items-center justify-center shadow-sm transition-all duration-300 focus:outline-none group">
                    <i class="fa-solid fa-gear text-base transition-transform duration-1000" :class="activeTasks.length > 0 ? 'animate-spin-slow text-indigo-500' : ''"></i>
                    {{-- Dynamic Task Indicator --}}
                    <span x-show="activeTasks.length > 0" class="absolute -top-1 -right-1 w-3 h-3 bg-indigo-500 border-2 border-white rounded-full shadow-sm"></span>
                </button>

                <div x-show="tasksOpen" x-cloak
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                     class="absolute top-full right-0 mt-4 w-80 bg-white/95 backdrop-blur-2xl border border-slate-200 rounded-[2rem] shadow-[0_25px_60px_-15px_rgba(0,0,0,0.15)] overflow-hidden z-[100] p-2.5">
                    
                    <div class="px-4 pt-3 pb-3 border-b border-slate-100 mb-2 flex justify-between items-center bg-slate-50 rounded-xl">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-700 flex items-center gap-2"><i class="fa-solid fa-server text-indigo-500"></i> Background Ops</span>
                        <span class="text-[10px] font-bold text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200 shadow-sm"><span x-text="activeTasks.length"></span> Running</span>
                    </div>

                    <div class="space-y-1 p-1">
                        <template x-for="task in activeTasks" :key="task.id">
                            <div class="p-3 rounded-xl bg-white border border-slate-100 shadow-sm">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-black text-slate-900" x-text="task.name"></span>
                                    <span class="text-[9px] font-bold uppercase tracking-widest" :class="'text-' + task.color + '-600'" x-text="task.status"></span>
                                </div>
                                <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden shadow-inner">
                                    <div class="h-full transition-all duration-300 ease-out" 
                                         :class="'bg-' + task.color + '-500 shadow-[0_0_5px_rgba(var(--tw-colors-' + task.color + '-500),0.5)]'" 
                                         :style="`width: ${task.progress}%`"></div>
                                </div>
                            </div>
                        </template>
                        <div x-show="activeTasks.length === 0" class="py-6 text-center">
                            <i class="fa-solid fa-check-double text-2xl text-emerald-400 mb-2"></i>
                            <p class="text-xs font-bold text-slate-500">All queues processed.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ---------------------------------------------------- --}}
            {{-- C. NOTIFICATION HUB                                  --}}
            {{-- ---------------------------------------------------- --}}
            <div class="relative" x-data="{ alertsOpen: false }" @click.away="alertsOpen = false">
                <button @click="alertsOpen = !alertsOpen" 
                        @mouseenter="triggerHoverSound()"
                        class="relative w-11 h-11 rounded-2xl bg-white border border-slate-200 text-slate-500 hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50/50 flex items-center justify-center shadow-sm transition-all duration-300 focus:outline-none group">
                    <i class="fa-regular fa-bell text-base group-hover:animate-swing"></i>
                    @if($hasAlerts)
                        <span class="absolute -top-1 -right-1 flex h-5 min-w-[20px] px-1.5 items-center justify-center">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-5 w-full bg-rose-500 border-2 border-white text-[9px] font-black text-white shadow-sm">{{ $unreadAlerts > 99 ? '99+' : $unreadAlerts }}</span>
                        </span>
                    @endif
                </button>

                <div x-show="alertsOpen" x-cloak
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                     class="absolute top-full right-0 mt-4 w-[320px] sm:w-[380px] bg-white/95 backdrop-blur-2xl border border-slate-200 rounded-[2rem] shadow-[0_25px_60px_-15px_rgba(0,0,0,0.15)] overflow-hidden z-[100]">
                    
                    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                        <span class="text-sm font-black text-slate-900 tracking-tight">System Telemetry</span>
                        <button class="text-[9px] font-black uppercase tracking-widest text-indigo-500 hover:text-indigo-700 transition-colors focus:outline-none bg-indigo-50 px-2 py-1 rounded border border-indigo-100 shadow-sm hover:shadow">Mark Read</button>
                    </div>

                    <div class="max-h-[60vh] overflow-y-auto p-2">
                        {{-- Dummy Alert 1 --}}
                        <a href="#" class="flex gap-4 p-4 hover:bg-slate-50 rounded-2xl transition-colors border border-transparent hover:border-slate-100 group">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-200 shadow-sm"><i class="fa-solid fa-check text-sm"></i></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-black text-slate-900 group-hover:text-indigo-600 transition-colors truncate">Stripe Sync Complete</p>
                                <p class="text-xs text-slate-500 font-medium line-clamp-2 mt-0.5">Successfully pulled 42 new transactions from connected bank nodes.</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-2">2 Mins Ago</p>
                            </div>
                        </a>
                        {{-- Dummy Alert 2 --}}
                        <a href="#" class="flex gap-4 p-4 hover:bg-slate-50 rounded-2xl transition-colors border border-transparent hover:border-slate-100 group">
                            <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 border border-rose-200 shadow-sm"><i class="fa-solid fa-triangle-exclamation text-sm"></i></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-black text-slate-900 group-hover:text-indigo-600 transition-colors truncate">Burn Rate Anomaly</p>
                                <p class="text-xs text-slate-500 font-medium line-clamp-2 mt-0.5">Cloud infrastructure expenses exceeded projected budget by 14%.</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-2">1 Hour Ago</p>
                            </div>
                        </a>
                    </div>
                    
                    @if(Route::has('user.notifications.index'))
                    <div class="p-3 bg-slate-50 border-t border-slate-100 text-center">
                        <a href="{{ route('user.notifications.index') }}" @click="triggerClick()" class="text-xs font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-widest transition-colors block py-1">View Audit Log &rarr;</a>
                    </div>
                    @endif
                </div>
            </div>

            <div class="w-px h-8 bg-slate-200 mx-1"></div>

            {{-- ---------------------------------------------------- --}}
            {{-- D. SECURITY & IDENTITY DROPDOWN (MEGA MENU)          --}}
            {{-- ---------------------------------------------------- --}}
            <div x-data="{ userMenuOpen: false }" @click.away="userMenuOpen = false" class="relative">
                
                <button @click="userMenuOpen = !userMenuOpen"
                        @mouseenter="triggerHoverSound()"
                        class="flex items-center gap-3 pl-2 pr-2 py-2 rounded-2xl hover:bg-white hover:shadow-lg hover:shadow-slate-200/50 border border-transparent hover:border-slate-200 transition-all duration-500 focus:outline-none group">
                    
                    <div class="hidden xl:block text-right pr-1">
                        <p class="text-sm font-black text-slate-900 group-hover:text-indigo-600 transition-colors tracking-tight leading-none mb-1">{{ $userName }}</p>
                        <p class="text-[9px] font-black uppercase tracking-[0.1em] text-slate-400">{{ $userRole }}</p>
                    </div>

                    <div class="relative">
                        <div class="h-11 w-11 rounded-[14px] bg-gradient-to-br from-slate-800 to-slate-900 p-[2px] shadow-lg shadow-slate-900/20 transition-transform duration-500 group-hover:scale-105 group-hover:rotate-3">
                            <div class="h-full w-full bg-white rounded-[12px] flex items-center justify-center overflow-hidden border border-slate-100">
                                <span class="text-slate-900 font-black text-sm">{{ $userInitials }}</span>
                            </div>
                        </div>
                        <div class="absolute -bottom-1 -right-1 h-4 w-4 bg-emerald-500 border-[3px] border-white rounded-full shadow-sm"></div>
                    </div>
                </button>

                {{-- Profile Mega-Dropdown --}}
                <div x-show="userMenuOpen" x-cloak
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                     class="absolute right-0 mt-4 w-[340px] bg-white/95 backdrop-blur-2xl border border-slate-200 rounded-[2.5rem] shadow-[0_30px_100px_-20px_rgba(0,0,0,0.25)] overflow-hidden z-[100] origin-top-right">
                    
                    {{-- Header / Security Status --}}
                    <div class="p-6 bg-slate-900 border-b border-slate-800 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
                        <div class="flex items-start justify-between relative z-10">
                            <div class="flex items-center gap-4">
                                <div class="h-14 w-14 rounded-2xl bg-white border border-slate-700 flex items-center justify-center shadow-md">
                                    <span class="text-xl font-black text-slate-900">{{ $userInitials }}</span>
                                </div>
                                <div>
                                    <p class="text-base font-black text-white truncate max-w-[160px]">{{ $userName }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 font-mono truncate max-w-[160px]">{{ $userEmail }}</p>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Security Shield UI --}}
                        <div class="mt-6 bg-slate-800/80 rounded-xl p-3 border border-slate-700 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-shield-halved text-emerald-400 text-sm"></i>
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Session Secure</span>
                                    <span class="text-[10px] font-mono font-bold text-emerald-400" x-text="sessionIp">192.168.1.1</span>
                                </div>
                            </div>
                            <span class="px-2 py-1 bg-emerald-500/10 text-emerald-400 rounded border border-emerald-500/20 text-[8px] font-black uppercase tracking-widest">MFA Active</span>
                        </div>
                    </div>

                    {{-- Navigation Hub --}}
                    <div class="p-3 space-y-1">
                        @if(Route::has('user.profile.index'))
                        <a href="{{ route('user.profile.index') }}" @click="triggerClick()" class="flex items-center gap-4 p-3 rounded-2xl hover:bg-slate-50 group transition-all duration-300 border border-transparent hover:border-slate-100">
                            <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 group-hover:text-indigo-600 group-hover:bg-indigo-50 transition-all shrink-0"><i class="fa-solid fa-fingerprint text-xs"></i></div>
                            <div class="flex-1">
                                <span class="block text-sm font-bold text-slate-900">Identity Profile</span>
                            </div>
                        </a>
                        @endif

                        @if(Route::has('user.profile.subscription'))
                        <a href="{{ route('user.profile.subscription') }}" @click="triggerClick()" class="flex items-center gap-4 p-3 rounded-2xl hover:bg-slate-50 group transition-all duration-300 border border-transparent hover:border-slate-100">
                            <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 group-hover:text-indigo-600 group-hover:bg-indigo-50 transition-all shrink-0"><i class="fa-solid fa-credit-card text-xs"></i></div>
                            <div class="flex-1">
                                <span class="block text-sm font-bold text-slate-900">Billing & Quotas</span>
                            </div>
                            <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 text-[8px] font-black uppercase tracking-widest rounded border border-indigo-100">Pro</span>
                        </a>
                        @endif
                    </div>

                    {{-- Master Admin Context --}}
                    @if($user && method_exists($user, 'isAdmin') && $user->isAdmin() && Route::has('admin.dashboard'))
                    <div class="px-3 py-2 bg-indigo-50/50 border-t border-b border-indigo-100">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-indigo-600 hover:text-white text-indigo-700 transition-all duration-300 group shadow-sm border border-transparent hover:border-indigo-500">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 group-hover:bg-indigo-500 flex items-center justify-center shrink-0 transition-colors"><i class="fa-solid fa-server text-sm"></i></div>
                            <div class="flex-1">
                                <span class="block text-sm font-black tracking-tight leading-tight">Master Control</span>
                            </div>
                        </a>
                    </div>
                    @endif

                    {{-- Termination Protocol --}}
                    <div class="p-3 bg-slate-50 border-t border-slate-100">
                        <form method="POST" action="{{ route('logout') ?? '#' }}">
                            @csrf
                            <button type="submit" @mouseenter="triggerHoverSound()" class="w-full flex items-center justify-between p-4 rounded-2xl hover:bg-rose-600 text-rose-600 hover:text-white border border-transparent hover:border-rose-600 transition-all duration-300 focus:outline-none group hover:shadow-[0_5px_15px_rgba(244,63,94,0.3)]">
                                <span class="text-xs font-black uppercase tracking-widest">Terminate Session</span>
                                <i class="fa-solid fa-power-off text-rose-400 group-hover:text-white transition-colors"></i>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        @endauth

    </div>
</nav>

{{-- ================= INJECTED STYLES & SCRIPTS ================= --}}
@push('styles')
<style>
    /* Premium Swing Animation for Notification Icon */
    @keyframes swing { 20% { transform: rotate(15deg); } 40% { transform: rotate(-10deg); } 60% { transform: rotate(5deg); } 80% { transform: rotate(-5deg); } 100% { transform: rotate(0deg); } }
    .animate-swing { transform-origin: top center; animation: swing 1s ease-in-out infinite; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('navbarEngine', () => ({
        
        // 1. Search Typewriter Engine
        searchPlaceholders: [
            'Search secure ledgers...', 
            'Calculate runway projection...', 
            'Jump to security settings...', 
            'Generate PDF tax report...'
        ],
        typewriterText: '',
        phIndex: 0,
        charIndex: 0,
        isDeleting: false,
        
        // 2. Simulated Telemetry
        networkPing: 12,
        sessionIp: 'Scanning...',
        
        // 3. Dynamic Task Engine (SAFE FALLBACK FOR ERROR AVOIDANCE)
        activeTasks: [
            { id: 1, name: 'Stripe Webhook Sync', status: 'Processing', progress: 65, color: 'indigo' },
            { id: 2, name: 'ML Category Heuristics', status: 'Analyzing', progress: 82, color: 'emerald' },
            { id: 3, name: 'AES-256 Vault Backup', status: 'Pending', progress: 10, color: 'slate' }
        ],

        init() {
            // Start Typewriter
            this.typeEffect();
            
            // Simulate Network Ping Fluctuations
            setInterval(() => {
                this.networkPing = Math.floor(Math.random() * (18 - 10 + 1) + 10);
            }, 2000);

            // Fetch simulated IP
            setTimeout(() => {
                this.sessionIp = '192.168.' + Math.floor(Math.random() * 255) + '.' + Math.floor(Math.random() * 255);
            }, 1000);

            // Dynamic Task Progression Simulation
            setInterval(() => {
                if(this.activeTasks.length > 0) {
                    this.activeTasks.forEach((task, index) => {
                        if(task.progress < 100) {
                            task.progress += Math.floor(Math.random() * 5);
                            if(task.progress >= 100) {
                                task.progress = 100;
                                task.status = 'Completed';
                                setTimeout(() => { this.activeTasks.splice(index, 1); }, 2000);
                            }
                        }
                    });
                }
            }, 1000);
        },

        typeEffect() {
            const currentString = this.searchPlaceholders[this.phIndex];
            
            if (this.isDeleting) {
                this.typewriterText = currentString.substring(0, this.charIndex - 1);
                this.charIndex--;
            } else {
                this.typewriterText = currentString.substring(0, this.charIndex + 1);
                this.charIndex++;
            }

            let typeSpeed = this.isDeleting ? 30 : 80;

            if (!this.isDeleting && this.charIndex === currentString.length) {
                typeSpeed = 2000; // Pause at end
                this.isDeleting = true;
            } else if (this.isDeleting && this.charIndex === 0) {
                this.isDeleting = false;
                this.phIndex = (this.phIndex + 1) % this.searchPlaceholders.length;
                typeSpeed = 500; // Pause before typing next
            }

            setTimeout(() => this.typeEffect(), typeSpeed);
        },

        triggerHoverSound() {
            if(typeof playHoverSound === 'function') playHoverSound();
        },
        
        triggerClick() {
            if(typeof playClickSound === 'function') playClickSound();
            if(typeof simulateNavigation === 'function') simulateNavigation();
        }
    }));
});
</script>
@endpush