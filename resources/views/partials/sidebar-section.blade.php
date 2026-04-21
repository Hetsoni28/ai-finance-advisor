{{-- ====================================================================== --}}
{{-- 🚀 FINANCE AI: ENTERPRISE SIDEBAR NODE HUB & ACCORDION ENGINE          --}}
{{-- ====================================================================== --}}

@props([
    'title'           => null,         // e.g., 'Financial Hub'
    'icon'            => null,         // e.g., 'fa-wallet' (Optional section icon)
    'color'           => 'slate',      // indigo, emerald, rose, sky, amber, fuchsia, slate
    'permission'      => null,         // Spatie or Native Gate permission requirement
    'role'            => null,         // Spatie Role requirement
    'divider'         => true,         // Show top border separator
    'badge'           => null,         // Notification text/number
    'badgeColor'      => null,         // Auto-syncs to $color if null
    'collapsible'     => true,         // Make section expandable/collapsible
    'expanded'        => true,         // Default state on page load
    'quickAddRoute'   => null,         // Route name for the instant '+' action button
    'quickAddTooltip' => 'Create New Node', // Tooltip for the quick action button
    'progress'        => null,         // 0-100 value for micro-quota bars
    'metric'          => null,         // Micro-text metric next to title (e.g., '14 Nodes')
])

@php
    /** @var \App\Models\User|null $user */
    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | 🛡️ ENTERPRISE AUTHORIZATION ENGINE (CRASH-PROOF ACL)
    |--------------------------------------------------------------------------
    */
    $visible = true;
    $isSecuredNode = false; // Tracks if this node requires special clearance

    // 1. Authenticated User Check
    if (($permission || $role) && !$user) {
        $visible = false;
    }

    // 2. Strict Permission Check (Compatible with Spatie & Native Gates)
    if ($visible && $permission) {
        $isSecuredNode = true;
        $permissions = (array) $permission;
        $hasPermission = false;
        foreach ($permissions as $perm) {
            if (method_exists($user, 'hasPermissionTo') ? $user->hasPermissionTo($perm) : $user->can($perm)) {
                $hasPermission = true;
                break;
            }
        }
        if (!$hasPermission) $visible = false;
    }

    // 3. Strict Role Check
    if ($visible && $role) {
        $isSecuredNode = true;
        $roles = (array) $role;
        $hasRole = false;
        if (method_exists($user, 'hasAnyRole')) {
            $hasRole = $user->hasAnyRole($roles);
        } else {
            $hasRole = in_array(strtolower($user->role ?? ''), array_map('strtolower', $roles));
        }
        if (!$hasRole) $visible = false;
    }

    /*
    |--------------------------------------------------------------------------
    | 🚦 LARAVEL 8 SAFE SLOT VALIDATION
    |--------------------------------------------------------------------------
    */
    // Prevents fatal errors on Illuminate\Support\HtmlString in Laravel 8
    $hasChildren = trim((string) $slot) !== '';

    /*
    |--------------------------------------------------------------------------
    | 🎨 DYNAMIC MULTI-COLOR "LIGHT WHITE" RENDERING ENGINE
    |--------------------------------------------------------------------------
    */
    $resolvedBadgeColor = $badgeColor ?? $color;

    // Text & Icon Colors (Pristine Light Theme)
    $colorMap = [
        'slate'   => 'text-slate-400 group-hover/section:text-slate-900',
        'indigo'  => 'text-indigo-400 group-hover/section:text-indigo-600',
        'emerald' => 'text-emerald-400 group-hover/section:text-emerald-600',
        'rose'    => 'text-rose-400 group-hover/section:text-rose-600',
        'amber'   => 'text-amber-400 group-hover/section:text-amber-600',
        'sky'     => 'text-sky-400 group-hover/section:text-sky-600',
        'fuchsia' => 'text-fuchsia-400 group-hover/section:text-fuchsia-600',
    ];

    // Background Highlight Engine (Hover States & Neumorphism)
    $highlightMap = [
        'slate'   => 'hover:bg-white hover:shadow-[0_4px_15px_rgba(0,0,0,0.03)] border-slate-200',
        'indigo'  => 'hover:bg-white hover:shadow-[0_4px_15px_rgba(79,70,229,0.05)] hover:ring-1 hover:ring-indigo-50 border-indigo-100',
        'emerald' => 'hover:bg-white hover:shadow-[0_4px_15px_rgba(16,185,129,0.05)] hover:ring-1 hover:ring-emerald-50 border-emerald-100',
        'rose'    => 'hover:bg-white hover:shadow-[0_4px_15px_rgba(244,63,94,0.05)] hover:ring-1 hover:ring-rose-50 border-rose-100',
        'amber'   => 'hover:bg-white hover:shadow-[0_4px_15px_rgba(245,158,11,0.05)] hover:ring-1 hover:ring-amber-50 border-amber-100',
        'sky'     => 'hover:bg-white hover:shadow-[0_4px_15px_rgba(14,165,233,0.05)] hover:ring-1 hover:ring-sky-50 border-sky-100',
        'fuchsia' => 'hover:bg-white hover:shadow-[0_4px_15px_rgba(217,70,239,0.05)] hover:ring-1 hover:ring-fuchsia-50 border-fuchsia-100',
    ];

    // Enterprise Badge Engine
    $badgeMap = [
        'slate'   => 'bg-slate-100 text-slate-600 border-slate-200 shadow-sm',
        'indigo'  => 'bg-indigo-50 text-indigo-700 border-indigo-200 shadow-[0_0_8px_rgba(99,102,241,0.15)]',
        'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-200 shadow-[0_0_8px_rgba(16,185,129,0.15)]',
        'rose'    => 'bg-rose-50 text-rose-700 border-rose-200 shadow-[0_0_8px_rgba(244,63,94,0.15)]',
        'amber'   => 'bg-amber-50 text-amber-700 border-amber-200 shadow-[0_0_8px_rgba(245,158,11,0.15)]',
        'sky'     => 'bg-sky-50 text-sky-700 border-sky-200 shadow-[0_0_8px_rgba(14,165,233,0.15)]',
        'fuchsia' => 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200 shadow-[0_0_8px_rgba(217,70,239,0.15)]',
    ];

    // Quick Action Button Styling
    $quickAddMap = [
        'slate'   => 'hover:bg-slate-900 text-slate-400 hover:text-white border-slate-200 hover:shadow-md',
        'indigo'  => 'hover:bg-indigo-600 text-indigo-400 hover:text-white border-indigo-200 hover:shadow-md hover:shadow-indigo-500/30',
        'emerald' => 'hover:bg-emerald-500 text-emerald-400 hover:text-white border-emerald-200 hover:shadow-md hover:shadow-emerald-500/30',
        'rose'    => 'hover:bg-rose-600 text-rose-400 hover:text-white border-rose-200 hover:shadow-md hover:shadow-rose-500/30',
        'amber'   => 'hover:bg-amber-500 text-amber-400 hover:text-white border-amber-200 hover:shadow-md hover:shadow-amber-500/30',
        'sky'     => 'hover:bg-sky-500 text-sky-400 hover:text-white border-sky-200 hover:shadow-md hover:shadow-sky-500/30',
    ];

    // Micro Progress Bar Intelligence (Auto-switches to red if over 90%)
    $progressColor = 'slate';
    $progressAnimate = '';
    if ($progress !== null) {
        $pVal = (int) $progress;
        if ($pVal >= 90) {
            $progressColor = 'rose';
            $progressAnimate = 'animate-pulse';
        } else {
            $progressColor = $color;
        }
    }
    
    $progressMap = [
        'slate'   => 'bg-slate-400 shadow-[0_0_5px_rgba(148,163,184,0.5)]',
        'indigo'  => 'bg-indigo-500 shadow-[0_0_5px_rgba(79,70,229,0.5)]',
        'emerald' => 'bg-emerald-500 shadow-[0_0_5px_rgba(16,185,129,0.5)]',
        'rose'    => 'bg-rose-500 shadow-[0_0_5px_rgba(244,63,94,0.5)]',
        'amber'   => 'bg-amber-500 shadow-[0_0_5px_rgba(245,158,11,0.5)]',
        'sky'     => 'bg-sky-500 shadow-[0_0_5px_rgba(14,165,233,0.5)]',
    ];

    $resolvedColorClass = $colorMap[$color] ?? $colorMap['slate'];
    $resolvedHighlightClass = $highlightMap[$color] ?? $highlightMap['slate'];
    $resolvedBadgeClass = $badgeMap[$resolvedBadgeColor] ?? $badgeMap['slate'];
    $resolvedQuickAddClass = $quickAddMap[$color] ?? $quickAddMap['slate'];
    $resolvedProgressClass = $progressMap[$progressColor] ?? $progressMap['slate'];

    // Format Badge safely
    $formattedBadge = $badge;
    if (is_numeric($badge) && $badge > 99) {
        $formattedBadge = '99+';
    }
@endphp

@if($visible)

    {{-- 🔥 ALPINE ARCHITECTURE: Native isolated engine with DOM Scanning --}}
    <div x-data="sidebarSectionEngine({{ $expanded ? 'true' : 'false' }})" 
         class="w-full relative group/section transition-all duration-300 flex flex-col mb-1">

        {{-- 1. Elegant Gradient Divider & Orb --}}
        @if($divider)
            <div x-show="!isSidebarCollapsed" x-transition.opacity class="relative w-full h-4 mt-6 mb-2 flex items-center justify-center">
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-slate-200 to-transparent h-px top-1/2 opacity-70"></div>
                {{-- Micro-Orb connecting sections --}}
                <div class="w-1.5 h-1.5 rounded-full bg-white border border-slate-300 relative z-10 shadow-sm"></div>
            </div>
            
            {{-- "Ghost" divider for when master sidebar is collapsed --}}
            <div x-show="isSidebarCollapsed" x-cloak class="w-full flex justify-center mt-5 mb-3">
                <div class="w-4 h-1 rounded-full bg-slate-200"></div>
            </div>
        @endif

        {{-- 2. Interactive Section Header (The Master Node) --}}
        @if(filled($title))
            <div class="relative flex items-center justify-between px-3 py-2 rounded-xl transition-all duration-300 {{ $collapsible ? 'cursor-pointer ' . $resolvedHighlightClass : 'cursor-default' }} border border-transparent"
                 x-show="!isSidebarCollapsed" 
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
                 @if($collapsible) 
                    @click="toggleAccordion()" 
                    :aria-expanded="isExpanded.toString()" 
                 @endif
                 :class="hasActiveChild && !isExpanded ? 'bg-slate-50 border-slate-200 shadow-sm' : ''">

                {{-- Hover Ambient Glow (Visible on hover only) --}}
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-{{ $color }}-50/30 to-transparent opacity-0 group-hover/section:opacity-100 transition-opacity duration-500 rounded-xl pointer-events-none"></div>

                {{-- Left Side: Icon + Title + Progress --}}
                <div class="flex-1 min-w-0 pr-2 relative z-10">
                    <div class="flex items-center gap-2">
                        @if($icon)
                            <div class="w-4 flex items-center justify-center shrink-0">
                                <i class="fa-solid {{ $icon }} text-[11px] {{ $resolvedColorClass }} transition-transform duration-300 group-hover/section:scale-110 group-hover/section:rotate-6"
                                   :class="hasActiveChild ? 'text-{{ $color }}-600 scale-110' : ''"></i>
                            </div>
                        @endif

                        <span class="text-[10.5px] font-black uppercase tracking-[0.15em] {{ $resolvedColorClass }} truncate transition-colors duration-300 select-none"
                              :class="hasActiveChild ? 'text-slate-900' : ''">
                            {{ $title }}
                        </span>

                        {{-- Security Clearance Indicator --}}
                        @if($isSecuredNode)
                            <i class="fa-solid fa-lock text-[8px] text-slate-300 ml-1" title="Secured Node"></i>
                        @endif

                        {{-- Micro Metric Text --}}
                        @if($metric)
                            <span class="ml-1 text-[8px] font-bold text-slate-400 bg-white border border-slate-200 px-1 py-0.5 rounded shadow-sm leading-none">{{ $metric }}</span>
                        @endif
                    </div>

                    {{-- 🔥 BEAST MODE: Micro Quota Progress Bar --}}
                    @if($progress !== null)
                        <div class="w-full h-[3px] bg-slate-100 rounded-full overflow-hidden mt-2 border border-slate-200/50 shadow-inner group-hover/section:bg-slate-200/60 transition-colors">
                            <div class="h-full {{ $resolvedProgressClass }} {{ $progressAnimate }} transition-all duration-1000 ease-out" style="width: {{ $progress }}%"></div>
                        </div>
                    @endif
                </div>

                {{-- Right Side: Quick Action, Badge, Chevron --}}
                <div class="flex items-center gap-1.5 shrink-0 relative z-10">
                    
                    {{-- 🔥 BEAST MODE: Context Action Dropdown Button (Visible on Hover) --}}
                    @if($quickAddRoute && Route::has($quickAddRoute))
                        <div class="relative opacity-0 -translate-x-2 group-hover/section:opacity-100 group-hover/section:translate-x-0 transition-all duration-300"
                             @click.stop="quickMenuOpen = !quickMenuOpen"
                             @click.away="quickMenuOpen = false">
                            
                            <button title="{{ $quickAddTooltip }}"
                                    @mouseenter="if(typeof playHoverSound === 'function') playHoverSound()"
                                    class="w-5 h-5 flex items-center justify-center rounded-md border shadow-sm transition-all duration-300 bg-white {{ $resolvedQuickAddClass }} focus:outline-none">
                                <i class="fa-solid fa-plus text-[9px]"></i>
                            </button>

                            {{-- Mini Context Menu --}}
                            <div x-show="quickMenuOpen" x-cloak
                                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                                 class="absolute top-full right-0 mt-2 w-48 bg-white border border-slate-200 rounded-xl shadow-[0_10px_30px_rgba(0,0,0,0.1)] overflow-hidden z-[100] py-1">
                                <div class="px-3 py-1.5 border-b border-slate-100 bg-slate-50/50">
                                    <span class="text-[8px] font-black uppercase tracking-widest text-slate-400">Node Actions</span>
                                </div>
                                <a href="{{ route($quickAddRoute) }}" @click="triggerNavigation()" class="w-full text-left px-3 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-indigo-600 transition-colors flex items-center gap-2">
                                    <i class="fa-solid fa-plus w-4 text-center"></i> {{ $quickAddTooltip }}
                                </a>
                                <button @click="triggerNavigation(); $dispatch('notify', {message: 'Syncing ledger data...', type: 'info'}); quickMenuOpen = false;" class="w-full text-left px-3 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-indigo-600 transition-colors flex items-center gap-2">
                                    <i class="fa-solid fa-rotate w-4 text-center"></i> Force Node Sync
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- Animated Enterprise Badge --}}
                    @if(!is_null($badge))
                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded-md border tracking-wider uppercase {{ $resolvedBadgeClass }} {{ strtolower($badge) === 'new' ? 'animate-pulse' : '' }}">
                            {{ $formattedBadge }}
                        </span>
                    @endif

                    {{-- Accordion Chevron --}}
                    @if($collapsible)
                        <div class="w-4 h-4 flex items-center justify-center bg-white border border-slate-200 rounded shadow-sm group-hover/section:border-{{ $color }}-200 transition-colors">
                            <i class="fa-solid fa-chevron-down text-[8px] text-slate-400 transition-transform duration-300"
                               :class="[isExpanded ? 'rotate-180 text-indigo-500' : '', hasActiveChild && !isExpanded ? 'text-indigo-500 animate-pulse' : '']"></i>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- 3. The Slot Payload (Navigation Links) --}}
        @if($hasChildren)
            <div x-show="isExpanded || isSidebarCollapsed" 
                 x-collapse.duration.300ms
                 class="overflow-hidden relative">
                
                {{-- 🔥 BEAST MODE: SVG Routing Tree Line --}}
                <div x-show="!isSidebarCollapsed" class="absolute left-4 top-2 bottom-4 w-px bg-slate-200/80 z-0">
                    <div class="w-full bg-{{ $color }}-400 transition-all duration-1000 ease-in-out shadow-[0_0_8px_rgba(var(--tw-colors-{{ $color }}-500),0.8)]" :style="`height: ${hasActiveChild ? '100%' : '0%'}`"></div>
                </div>

                <div class="pt-2 pb-2 flex flex-col gap-1 relative z-10" :class="isSidebarCollapsed ? '' : 'pl-7 pr-2'" x-ref="slotContainer">
                    {{-- Slot injects child <x-sidebar-link> components here --}}
                    {{ $slot }}
                </div>
            </div>
        @endif

    </div>

@endif

{{-- ====================================================================== --}}
{{-- 🛠️ ALPINE.JS ISOLATED ENGINE (Loads once per component instance)       --}}
{{-- ====================================================================== --}}
@once
@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('sidebarSectionEngine', (initialExpanded) => ({
            isExpanded: initialExpanded,
            hasActiveChild: false,
            quickMenuOpen: false,
            
            init() {
                // 🚨 DOM SCANNING ENGINE: Checks if any child link is currently 'active'
                // If an active link is found inside this section, force it open and highlight it.
                this.$nextTick(() => {
                    if (this.$refs.slotContainer) {
                        // Look for elements with the active class structure (ring-1, scale-[1.01], etc.)
                        // Or explicitly look for aria-current="page"
                        const activeLinks = this.$refs.slotContainer.querySelectorAll('[aria-current="page"], .ring-1.scale-\\[1\\.01\\]');
                        if (activeLinks.length > 0) {
                            this.hasActiveChild = true;
                            this.isExpanded = true; // Auto-expand to show active child
                        }
                    }
                });
            },

            // 🚨 SAFE FALLBACK: Resolves "collapsed" from parent safely without crashing
            get isSidebarCollapsed() {
                if (typeof this.$parent !== 'undefined' && this.$parent.collapsed !== undefined) {
                    return this.$parent.collapsed;
                }
                // Fallback for global Alpine store if used instead of parent component
                if (typeof this.$store !== 'undefined' && this.$store.sidebar !== undefined) {
                    return this.$store.sidebar.collapsed;
                }
                return false; // Default safe state
            },

            toggleAccordion() {
                this.isExpanded = !this.isExpanded;
                
                // Haptic feedback integration (Calls layout's global audio engine)
                if (typeof playClickSound === 'function') {
                    playClickSound();
                }
            },

            // Intercepts Quick Add clicks to simulate SPA navigation
            triggerNavigation() {
                if (typeof simulateNavigation === 'function') {
                    simulateNavigation();
                }
            }
        }));
    });
</script>
@endpush
@endonce