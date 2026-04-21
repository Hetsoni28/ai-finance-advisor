{{-- ====================================================================== --}}
{{-- 🚀 FINANCE AI: ENTERPRISE MASTER SIDEBAR ENGINE                        --}}
{{-- ====================================================================== --}}

@php
    /** @var \App\Models\User|null $user */
    $user = auth()->user();

    // ================= 1. ENTERPRISE MENU ARCHITECTURE =================
    // Structured with dynamic color themes and role-based access visibility
    $menu = [
        [
            'section' => 'Financial Command',
            'expanded' => true, // Controls default accordion state
            'items' => [
                ['route' => 'user.dashboard', 'match' => ['user.dashboard*', 'dashboard'], 'icon' => 'fa-chart-pie', 'label' => 'Global Dashboard', 'color' => 'indigo', 'shortcut' => '⌘D'],
                ['route' => 'user.incomes.index', 'match' => 'user.incomes.*', 'icon' => 'fa-arrow-trend-up', 'label' => 'Capital Inflow', 'color' => 'emerald', 'shortcut' => '⇧I'],
                ['route' => 'user.expenses.index', 'match' => 'user.expenses.*', 'icon' => 'fa-arrow-trend-down', 'label' => 'Capital Outflow', 'color' => 'rose', 'shortcut' => '⇧E'],
                ['route' => 'user.families.index', 'match' => 'user.families.*', 'icon' => 'fa-network-wired', 'label' => 'Shared Ledgers', 'color' => 'sky'],
            ]
        ],
        [
            'section' => 'Intelligence & Data',
            'expanded' => true,
            'items' => [
                ['route' => 'user.reports.index', 'match' => ['user.reports.*', 'reports.*'], 'icon' => 'fa-file-invoice-dollar', 'label' => 'Analytics Hub', 'color' => 'amber'],
                ['route' => 'user.notifications.index', 'match' => 'user.notifications.*', 'icon' => 'fa-bell', 'label' => 'System Alerts', 'color' => 'orange', 'badge' => 3, 'dot' => true],
                ['route' => 'user.ai.chat', 'match' => 'user.ai.*', 'icon' => 'fa-brain', 'label' => 'AI Assistant', 'color' => 'fuchsia', 'badge' => 'BETA'],
            ]
        ],
        [
            'section' => 'Configuration',
            'expanded' => false, // Hidden by default to keep UI clean
            'items' => [
                ['route' => 'user.profile.index', 'match' => 'user.profile.index', 'icon' => 'fa-id-card-clip', 'label' => 'Identity Profile', 'color' => 'slate'],
                ['route' => 'user.profile.password.form', 'match' => 'user.profile.password.*', 'icon' => 'fa-shield-halved', 'label' => 'Security & Auth', 'color' => 'slate'],
                ['route' => 'user.profile.subscription', 'match' => 'user.profile.subscription*', 'icon' => 'fa-credit-card', 'label' => 'Billing Quotas', 'color' => 'indigo'],
            ]
        ]
    ];

    // ================= 2. ROLE-BASED ACCESS CONTROL (RBAC) =================
    $isAdmin = false;
    if ($user && method_exists($user, 'isAdmin')) {
        $isAdmin = $user->isAdmin();
    } elseif ($user && isset($user->role) && strtolower($user->role) === 'admin') {
        $isAdmin = true;
    }

    if ($isAdmin) {
        $menu[] = [
            'section' => 'Master Node (Admin)',
            'expanded' => true,
            'items' => [
                ['route' => 'admin.dashboard', 'match' => 'admin.dashboard*', 'icon' => 'fa-server', 'label' => 'Infrastructure', 'color' => 'emerald'],
                ['route' => 'admin.users.index', 'match' => 'admin.users.*', 'icon' => 'fa-users-gear', 'label' => 'Node Identities', 'color' => 'sky'],
                ['route' => 'admin.contacts.index', 'match' => 'admin.contacts.*', 'icon' => 'fa-envelope-open-text', 'label' => 'Contact Messages', 'color' => 'violet'],
                ['route' => 'admin.activities.index', 'match' => 'admin.activities.*', 'icon' => 'fa-clipboard-list', 'label' => 'Global Audit Log', 'color' => 'indigo'],
                ['route' => 'admin.reports.index', 'match' => 'admin.reports.*', 'icon' => 'fa-chart-column', 'label' => 'Telemetry Data', 'color' => 'amber'],
            ]
        ];
    }
    
    // SAFE Contextual Workspace Name
    $currentWorkspace = isset($family) && $family ? $family->name : 'Personal Ledger';
@endphp

{{-- ================= MOBILE OVERLAY ================= --}}
<div x-show="sidebarOpen" x-cloak
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 lg:hidden no-print"
     @click="sidebarOpen = false"
     aria-hidden="true">
</div>

{{-- ================= THE SIDEBAR SHELL ================= --}}
<aside x-data="{ collapsed: false, userMenuOpen: false, workspaceOpen: false }"
       class="fixed lg:static inset-y-0 left-0 bg-white/95 backdrop-blur-2xl border-r border-slate-200 shadow-[4px_0_24px_rgba(0,0,0,0.02)] flex flex-col transition-all duration-300 ease-in-out z-50 h-screen no-print"
       :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0', collapsed ? 'w-[88px]' : 'w-72']">

    {{-- ================= 1. BRAND HEADER & WORKSPACE SWITCHER ================= --}}
    <div class="h-24 flex items-center justify-between px-4 sm:px-5 border-b border-slate-100 shrink-0 transition-all relative group/brand" @click.away="workspaceOpen = false">
        
        <button @click="workspaceOpen = !workspaceOpen" @mouseenter="if(typeof playHoverSound === 'function') playHoverSound()" class="flex items-center gap-3 w-full p-2.5 rounded-2xl hover:bg-slate-50 transition-colors focus:outline-none" :class="collapsed ? 'justify-center px-0' : ''">
            {{-- Holographic Logo Icon --}}
            <div class="h-10 w-10 rounded-[12px] bg-white border border-slate-200 flex items-center justify-center text-indigo-600 shadow-[0_4px_12px_rgba(0,0,0,0.04)] group-hover/brand:shadow-[0_8px_20px_rgba(79,70,229,0.15)] group-hover/brand:border-indigo-300 transition-all duration-500 shrink-0 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-tr from-indigo-50 to-sky-50 opacity-100 z-0"></div>
                <i class="fa-solid fa-cube text-lg relative z-10 bg-clip-text text-transparent bg-gradient-to-br from-indigo-600 to-sky-500"></i>
            </div>
            
            {{-- Workspace Name --}}
            <div x-show="!collapsed" class="flex flex-col text-left flex-1 min-w-0" x-transition.opacity.duration.300ms>
                <span class="font-black text-[15px] text-slate-900 tracking-tight leading-tight truncate">{{ $currentWorkspace }}</span>
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-tight mt-0.5 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse inline-block"></span> FinanceAI Engine
                </span>
            </div>
            
            <i x-show="!collapsed" class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-300 mr-1" :class="workspaceOpen ? 'rotate-180 text-indigo-500' : ''"></i>
        </button>

        {{-- Workspace Switcher Dropdown --}}
        <div x-show="workspaceOpen" x-cloak
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-2 scale-95"
             class="absolute top-full left-4 right-4 mt-2 bg-white/95 backdrop-blur-2xl border border-slate-200 rounded-[1.5rem] shadow-[0_20px_60px_rgba(0,0,0,0.12)] overflow-hidden z-[100]">
            
            <div class="p-3 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 pl-2">Switch Ledger Context</p>
                <span class="px-2 py-0.5 bg-white border border-slate-200 rounded text-[8px] font-black text-slate-500 shadow-sm uppercase tracking-widest">v3.1</span>
            </div>
            <div class="p-2 space-y-1">
                <a href="{{ url('/dashboard') }}" @click="if(typeof simulateNavigation === 'function') simulateNavigation()" class="flex items-center gap-3 p-2.5 rounded-xl bg-indigo-50/80 border border-indigo-100 text-indigo-700 transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-white border border-indigo-200 flex items-center justify-center shadow-sm shrink-0"><i class="fa-solid fa-user text-[11px] text-indigo-500"></i></div>
                    <div class="flex flex-col">
                        <span class="text-sm font-bold">Personal Ledger</span>
                        <span class="text-[10px] font-bold text-indigo-400">Default View</span>
                    </div>
                    <i class="fa-solid fa-circle-check ml-auto text-indigo-500 text-sm mr-2"></i>
                </a>
                <a href="{{ Route::has('user.families.index') ? route('user.families.index') : '#' }}" @click="if(typeof simulateNavigation === 'function') simulateNavigation()" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-100 text-slate-600 transition-colors group">
                    <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center shadow-sm shrink-0 group-hover:border-slate-300 group-hover:text-slate-900 transition-colors"><i class="fa-solid fa-network-wired text-[11px]"></i></div>
                    <div class="flex flex-col">
                        <span class="text-sm font-bold group-hover:text-slate-900 transition-colors">View All Family Hubs</span>
                    </div>
                    <i class="fa-solid fa-arrow-right ml-auto text-slate-300 group-hover:text-slate-400 text-[10px] mr-2 opacity-0 group-hover:opacity-100 transition-all"></i>
                </a>
            </div>
            <div class="p-2 border-t border-slate-100 bg-slate-50">
                <a href="{{ Route::has('user.families.create') ? route('user.families.create') : '#' }}" @click="if(typeof simulateNavigation === 'function') simulateNavigation()" class="w-full flex items-center justify-center gap-2 p-2.5 rounded-xl text-xs font-black uppercase tracking-widest text-slate-500 bg-white border border-slate-200 shadow-sm hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 transition-all focus:outline-none">
                    <i class="fa-solid fa-plus text-[10px]"></i> Create New Workspace
                </a>
            </div>
        </div>

        {{-- Desktop Collapse Toggle --}}
        <button @click="collapsed = !collapsed" 
                @mouseenter="if(typeof playHoverSound === 'function') playHoverSound()"
                class="hidden lg:flex absolute -right-3.5 top-1/2 -translate-y-1/2 w-7 h-7 bg-white border border-slate-200 rounded-full text-slate-400 items-center justify-center shadow-sm hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50 transition-all focus:outline-none z-[60] group/toggle">
            <i class="fa-solid fa-chevron-left text-[10px] transition-transform duration-300" :class="collapsed ? 'rotate-180' : 'group-hover/toggle:-translate-x-0.5'"></i>
        </button>
    </div>

    {{-- ================= 2. QUICK SEARCH COMMAND ================= --}}
    <div class="px-5 py-5 shrink-0 border-b border-slate-50" x-show="!collapsed" x-transition.opacity.duration.300ms>
        <button @click="if(typeof toggleCommandPalette === 'function') toggleCommandPalette()" @mouseenter="if(typeof playHoverSound === 'function') playHoverSound()" class="w-full flex items-center justify-between px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-400 hover:bg-white hover:border-indigo-300 hover:text-indigo-500 hover:shadow-sm transition-all focus:outline-none group">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-magnifying-glass text-[13px] group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-bold">Quick search...</span>
            </div>
            <div class="flex gap-1">
                <kbd class="text-[10px] font-mono font-black border border-slate-200 rounded-md px-1.5 py-0.5 bg-white group-hover:border-indigo-200 group-hover:text-indigo-500 transition-colors shadow-sm">⌘</kbd>
                <kbd class="text-[10px] font-mono font-black border border-slate-200 rounded-md px-1.5 py-0.5 bg-white group-hover:border-indigo-200 group-hover:text-indigo-500 transition-colors shadow-sm">K</kbd>
            </div>
        </button>
    </div>

    {{-- ================= 3. NAVIGATION ENGINE (WITH ACCORDION) ================= --}}
    <div class="flex-1 overflow-y-auto overflow-x-visible px-4 pb-8 pt-4 space-y-2 scroll-smooth custom-scrollbar relative" 
         :class="collapsed ? 'pt-6' : 'pt-2'">
        
        @foreach($menu as $group)
            {{-- Accordion Data Group --}}
            <div x-data="{ expanded: {{ $group['expanded'] ? 'true' : 'false' }} }" class="relative pb-4">
                
                {{-- Group Section Header (Clickable Accordion Trigger) --}}
                @if($group['section'])
                    <button @click="expanded = !expanded" @mouseenter="if(typeof playHoverSound === 'function') playHoverSound()" x-show="!collapsed" x-transition.opacity.duration.300ms class="w-full px-4 mb-2 flex items-center justify-between group/header focus:outline-none rounded-lg hover:bg-slate-50 py-1.5 transition-colors border border-transparent hover:border-slate-100">
                        <span class="text-[10px] font-black uppercase tracking-[0.15em] text-slate-400 group-hover/header:text-indigo-500 transition-colors">{{ $group['section'] }}</span>
                        <i class="fa-solid fa-chevron-down text-[9px] text-slate-300 transition-transform duration-300" :class="expanded ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="collapsed" class="w-full flex justify-center mb-4 mt-2"><div class="w-1.5 h-1.5 rounded-full bg-slate-200"></div></div>
                @endif

                {{-- Accordion Body --}}
                <div x-show="expanded || collapsed" x-collapse.duration.300ms class="space-y-1.5">
                    @foreach($group['items'] as $item)
                        @php
                            // Native array route matching
                            $active = false;
                            if (isset($item['match'])) {
                                $patterns = (array) $item['match'];
                                $active = request()->routeIs($patterns);
                            } elseif (isset($item['route'])) {
                                $active = request()->routeIs([$item['route'], $item['route'] . '.*']);
                            }
                            
                            $url = Route::has($item['route']) ? route($item['route']) : '#';
                            $baseColor = $item['color'];
                            
                            // Dynamic Multi-Color Theme Generation
                            if ($active) {
                                $bgClass = "bg-white shadow-[0_4px_15px_rgba(0,0,0,0.03)] border-slate-100 scale-[1.02] z-10";
                                $textClass = "text-slate-900";
                                $iconClass = "bg-{$baseColor}-500 text-white shadow-md shadow-{$baseColor}-500/20 scale-110";
                                $ringClass = "ring-1 ring-{$baseColor}-200/50";
                            } else {
                                $bgClass = "border border-transparent hover:bg-slate-50 hover:border-slate-200 hover:shadow-sm";
                                $textClass = "text-slate-500 hover:text-slate-900";
                                $iconClass = "bg-white border border-slate-200 text-slate-400 group-hover/nav:text-{$baseColor}-600 group-hover/nav:bg-{$baseColor}-50 group-hover/nav:border-{$baseColor}-200 group-hover/nav:scale-110 shadow-sm";
                                $ringClass = "";
                            }
                        @endphp

                        <div class="relative group/nav">
                            <a href="{{ $url }}" 
                               @click="if(typeof simulateNavigation === 'function') simulateNavigation()"
                               @mouseenter="if(typeof playHoverSound === 'function') playHoverSound()"
                               class="relative flex items-center px-2 py-2.5 rounded-2xl w-full transition-all duration-300 ease-out focus:outline-none focus:ring-4 focus:ring-{{ $baseColor }}-500/10 {{ $bgClass }} {{ $textClass }} {{ $ringClass }}"
                               :class="collapsed ? 'justify-center mx-2' : ''">

                                {{-- Active Glow Line --}}
                                @if($active)
                                    <div class="absolute -left-2 top-1/2 -translate-y-1/2 w-1.5 h-8 bg-{{ $baseColor }}-500 rounded-r-full shadow-[0_0_12px_rgba(var(--tw-colors-{{ $baseColor }}-500),0.6)]"></div>
                                @endif

                                {{-- Icon Container --}}
                                <div class="w-9 h-9 flex items-center justify-center shrink-0 rounded-[10px] transition-all duration-500 relative {{ $iconClass }}"
                                     :class="collapsed ? 'scale-105' : ''">
                                    <i class="fa-solid {{ $item['icon'] }} text-[13px] transition-transform duration-300"></i>
                                    
                                    @if(isset($item['dot']) && $item['dot'])
                                        <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-rose-500 rounded-full ring-2 ring-white shadow-sm">
                                            <span class="animate-ping absolute inset-0 rounded-full bg-rose-400 opacity-75"></span>
                                        </span>
                                    @endif
                                </div>

                                {{-- Label & Badges (Hidden when collapsed) --}}
                                <span x-show="!collapsed" class="ml-4 truncate flex-1 font-bold text-[13px] tracking-wide transition-opacity duration-300" x-transition.opacity>
                                    {{ $item['label'] }}
                                </span>

                                @if(isset($item['badge']))
                                    @php 
                                        $isTextBadge = !is_numeric($item['badge']);
                                        $badgeBg = $isTextBadge ? 'bg-fuchsia-50 text-fuchsia-600 border-fuchsia-200' : 'bg-rose-50 text-rose-600 border-rose-200';
                                    @endphp
                                    <span x-show="!collapsed" class="ml-2 text-[9px] font-black px-1.5 py-0.5 rounded-md border shadow-sm transition-opacity duration-300 tracking-wider uppercase {{ $badgeBg }} {{ $isTextBadge ? 'animate-pulse' : '' }}" x-transition.opacity>
                                        {{ $item['badge'] > 99 ? '99+' : $item['badge'] }}
                                    </span>
                                @endif
                            </a>

                            {{-- 🚨 ALPINE TOOLTIP (Visible ONLY when collapsed) --}}
                            <div x-show="collapsed" x-cloak
                                 class="absolute left-[70px] top-1/2 -translate-y-1/2 px-4 py-2.5 bg-slate-900 text-white rounded-xl whitespace-nowrap opacity-0 group-hover/nav:opacity-100 pointer-events-none transition-all duration-300 z-[200] shadow-[0_20px_40px_-15px_rgba(0,0,0,0.5)] flex items-center gap-3 transform translate-x-2 group-hover/nav:translate-x-0 border border-slate-700">
                                
                                <span class="text-xs font-bold leading-tight">{{ $item['label'] }}</span>
                                
                                <div class="flex items-center gap-2">
                                    @if(isset($item['badge']))
                                        <span class="{{ $isTextBadge ? 'bg-fuchsia-500' : 'bg-rose-500' }} text-white text-[9px] font-black px-1.5 py-0.5 rounded-lg shadow-inner uppercase tracking-widest">{{ $item['badge'] > 99 ? '99+' : $item['badge'] }}</span>
                                    @endif
                                    @if(isset($item['shortcut']))
                                        <kbd class="px-2 py-0.5 rounded-md bg-slate-800 border border-slate-600 text-[10px] font-mono font-bold text-slate-300 shadow-inner ml-1">{{ $item['shortcut'] }}</kbd>
                                    @endif
                                </div>
                                <div class="absolute left-[-5px] top-1/2 -translate-y-1/2 w-2.5 h-2.5 bg-slate-900 transform rotate-45 border-b border-l border-slate-700"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- ================= 4. TELEMETRY WIDGETS ================= --}}
        <div x-show="!collapsed" x-transition.opacity.duration.500ms class="mt-4 pt-6 border-t border-slate-100 px-2 space-y-4 pb-4">
            
            {{-- Storage & Quota Widget --}}
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-[0_2px_10px_rgba(0,0,0,0.02)] relative overflow-hidden group/quota">
                <div class="absolute top-0 right-0 w-20 h-20 bg-indigo-500/5 rounded-full blur-2xl transition-colors group-hover/quota:bg-indigo-500/10"></div>
                
                {{-- AI Compute --}}
                <div class="mb-4 relative z-10">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-500 flex items-center gap-1.5"><i class="fa-solid fa-microchip text-indigo-500"></i> AI Ops</span>
                        <span class="text-[9px] font-bold text-slate-400">62%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1 overflow-hidden shadow-inner">
                        <div class="bg-indigo-500 h-1 rounded-full shadow-[0_0_5px_rgba(79,70,229,0.5)]" style="width: 62%"></div>
                    </div>
                </div>

                {{-- Secure Vault Storage --}}
                <div class="mb-4 relative z-10">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-500 flex items-center gap-1.5"><i class="fa-solid fa-database text-sky-500"></i> Vault Data</span>
                        <span class="text-[9px] font-bold text-slate-400">8.4GB</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1 overflow-hidden shadow-inner">
                        <div class="bg-sky-500 h-1 rounded-full shadow-[0_0_5px_rgba(14,165,233,0.5)]" style="width: 45%"></div>
                    </div>
                </div>

                <a href="{{ Route::has('user.profile.subscription') ? route('user.profile.subscription') : '#' }}" @click="if(typeof simulateNavigation === 'function') simulateNavigation()" class="block w-full text-[9px] font-black uppercase tracking-widest text-indigo-600 hover:text-indigo-700 hover:bg-indigo-100 transition-colors relative z-10 text-center bg-indigo-50 py-2.5 rounded-lg border border-indigo-100">Upgrade Nodes &rarr;</a>
            </div>

            {{-- System Health Pulse --}}
            <div class="flex items-center justify-between px-4 py-3 bg-slate-50 rounded-xl border border-slate-100 shadow-inner">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-500">System Status</span>
                </div>
                <span class="text-[10px] font-mono font-bold text-emerald-600 bg-white px-2 py-0.5 rounded border border-emerald-100 shadow-sm">14ms</span>
            </div>
        </div>

    </div>

    {{-- ================= 5. USER IDENTITY FOOTER ================= --}}
    @if($user)
    <div class="p-4 border-t border-slate-200 shrink-0 bg-white relative z-[60]">
        
        {{-- Upward-Opening Context Menu --}}
        <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="absolute bottom-[calc(100%+10px)] left-4 right-4 bg-white/95 backdrop-blur-2xl border border-slate-200 rounded-[2rem] shadow-[0_20px_60px_-10px_rgba(0,0,0,0.15)] overflow-hidden origin-bottom z-[100]">
            
            <div class="p-6 bg-slate-50/80 border-b border-slate-100 relative overflow-hidden">
                <div class="absolute right-0 top-0 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>
                <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1 relative z-10">Active Cryptographic Session</p>
                <p class="text-sm font-bold text-slate-900 truncate relative z-10 font-mono" title="{{ $user->email }}">{{ $user->email }}</p>
            </div>
            
            <div class="p-2 space-y-1">
                <a href="{{ Route::has('user.profile.index') ? route('user.profile.index') : '#' }}" @click="if(typeof simulateNavigation === 'function') simulateNavigation()" class="flex items-center justify-between px-4 py-3 text-xs font-bold text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-colors group">
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded border border-transparent group-hover:border-indigo-200 group-hover:bg-white flex items-center justify-center transition-all"><i class="fa-solid fa-sliders text-slate-400 group-hover:text-indigo-500"></i></div>
                        Identity Settings
                    </div>
                    <kbd class="text-[9px] font-mono text-slate-400 bg-white border border-slate-200 px-1.5 py-0.5 rounded shadow-sm">⌘,</kbd>
                </a>
                <a href="#" class="flex items-center justify-between px-4 py-3 text-xs font-bold text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-colors group">
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded border border-transparent group-hover:border-indigo-200 group-hover:bg-white flex items-center justify-center transition-all"><i class="fa-solid fa-book-open text-slate-400 group-hover:text-indigo-500"></i></div>
                        API Documentation
                    </div>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-300 group-hover:text-indigo-400"></i>
                </a>
            </div>
            
            <div class="p-3 border-t border-slate-100 bg-slate-50">
                <form method="POST" action="{{ route('logout') ?? '#' }}">
                    @csrf
                    <button type="submit" @mouseenter="if(typeof playHoverSound === 'function') playHoverSound()" class="w-full flex items-center justify-center gap-2 px-4 py-3.5 text-xs font-black uppercase tracking-widest text-rose-600 bg-white border border-rose-100 hover:bg-rose-600 hover:text-white hover:border-rose-600 rounded-xl shadow-sm hover:shadow-[0_5px_15px_rgba(244,63,94,0.3)] transition-all focus:outline-none group">
                        Terminate Session <i class="fa-solid fa-power-off text-[10px] opacity-70 group-hover:opacity-100 transition-opacity ml-1"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- Footer Click Trigger --}}
        <button class="w-full flex items-center justify-between p-2.5 rounded-2xl hover:bg-slate-50 hover:shadow-[0_2px_10px_rgba(0,0,0,0.02)] border border-transparent hover:border-slate-200 transition-all cursor-pointer group focus:outline-none"
             :class="collapsed ? 'justify-center mx-auto' : ''"
             @click="userMenuOpen = !userMenuOpen"
             @mouseenter="if(typeof playHoverSound === 'function') playHoverSound()">
            
            <div class="flex items-center gap-4 overflow-hidden">
                {{-- Avatar --}}
                <div class="relative shrink-0">
                    <div class="h-10 w-10 rounded-[14px] bg-gradient-to-br from-slate-800 to-slate-900 flex items-center justify-center text-white font-black shadow-md border border-slate-700 group-hover:shadow-[0_5px_15px_rgba(15,23,42,0.3)] transition-shadow">
                        {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                    </div>
                    <span class="absolute -bottom-1 -right-1 h-3.5 w-3.5 bg-emerald-500 border-2 border-white rounded-full shadow-sm"></span>
                </div>

                {{-- Text Details --}}
                <div x-show="!collapsed" class="min-w-0 flex-1 text-left" x-transition.opacity.duration.300ms>
                    <p class="text-sm font-black text-slate-900 truncate group-hover:text-indigo-600 transition-colors">
                        {{ $user->name ?? 'Operator' }}
                    </p>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 truncate mt-0.5">
                        {{ $isAdmin ? 'Master Admin' : ($user->role ?? 'Operator Node') }}
                    </p>
                </div>
            </div>

            <div x-show="!collapsed" class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-slate-400 flex items-center justify-center shrink-0 transition-all shadow-sm group-hover:border-indigo-200 group-hover:text-indigo-500" :class="userMenuOpen ? 'bg-indigo-50 border-indigo-200 text-indigo-600' : ''">
                <i class="fa-solid fa-chevron-up text-[10px] transition-transform duration-300" :class="userMenuOpen ? 'rotate-180' : ''"></i>
            </div>
        </button>
    </div>
    @endif

</aside>

<style>
    /* Clean scrollbar for sidebar specifically */
    .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: transparent; border-radius: 10px; }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #cbd5e1; }
</style>