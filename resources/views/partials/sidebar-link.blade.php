{{-- ====================================================================== --}}
{{-- 🚀 FINANCE AI: ENTERPRISE SIDEBAR ROUTING & NAVIGATION ENGINE          --}}
{{-- ====================================================================== --}}

@props([
    'route'      => null,          // Laravel route name (e.g., 'user.dashboard')
    'url'        => null,          // Hardcoded URL fallback
    'icon'       => 'fa-circle',   // FontAwesome icon class
    'label'      => 'Menu Item',   // Main text
    'subtitle'   => null,          // Micro text under the label
    'badge'      => null,          // Notification badge text/number
    'color'      => 'indigo',      // indigo, emerald, rose, sky, amber, slate
    'active'     => null,          // Array or string of routes to match for active state
    'permission' => null,          // Spatie or Native Gate permission requirement
    'role'       => null,          // Spatie Role requirement
    'target'     => null,          // _blank, _self, etc.
    'disabled'   => false,         // Grays out and disables clicks
    'external'   => false,         // Adds external link icon
    'dot'        => false,         // Pulsing live indicator
    'progress'   => null,          // 0-100 numeric value for quota bars 
    'chartData'  => null,          // Array for micro-sparkline [10,20,15,40] 
    'shortcut'   => null,          // Keyboard shortcut hint e.g., '⇧I' 
    'menu'       => false,         // Show 3-dot context menu on hover 
    'isPremium'  => false,         // 🔥 NEW: Locks node for Pro tier users
    'depth'      => 0,             // 🔥 NEW: Indentation depth for nested submenus
])

@php
    /** @var \App\Models\User|null $user */
    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | 🛡️ ENTERPRISE AUTHORIZATION ENGINE (CRASH-PROOF ACL)
    |--------------------------------------------------------------------------
    */
    $allowed = true;

    // 1. Authenticated User Check
    if (($permission || $role || $isPremium) && !$user) {
        $allowed = false;
    }

    // 2. Strict Permission Check 
    if ($allowed && $permission) {
        $permissions = (array) $permission;
        $hasPermission = false;
        foreach ($permissions as $perm) {
            if (method_exists($user, 'hasPermissionTo') ? $user->hasPermissionTo($perm) : $user->can($perm)) {
                $hasPermission = true;
                break;
            }
        }
        if (!$hasPermission) $allowed = false;
    }

    // 3. Strict Role Check
    if ($allowed && $role) {
        $roles = (array) $role;
        $hasRole = false;
        if (method_exists($user, 'hasAnyRole')) {
            $hasRole = $user->hasAnyRole($roles);
        } else {
            $hasRole = in_array(strtolower($user->role ?? ''), array_map('strtolower', $roles));
        }
        if (!$hasRole) $allowed = false;
    }

    /*
    |--------------------------------------------------------------------------
    | 🚦 BLAZING FAST ROUTE & ACTIVE DETECTION
    |--------------------------------------------------------------------------
    */
    $routeExists = filled($route) && Route::has($route);
    
    // External link auto-detection
    if (!$external && $url && str_starts_with($url, 'http')) {
        $external = true;
        $target = $target ?? '_blank';
    }

    // Premium Node Interception
    if ($isPremium && $user && !($user->is_pro ?? false) && !($user->role === 'admin')) {
        $href = Route::has('user.profile.subscription') ? route('user.profile.subscription') : '#';
        $disabled = false; // Keep clickable to redirect to upgrade page
        $isLocked = true;
    } else {
        $href = $routeExists ? route($route) : ($url ?? '#');
        $isLocked = false;
    }
    
    // Laravel 8 Safe Slot Validation
    $hasChildren = trim((string) $slot) !== '';

    // Resolve Active State
    $patterns = [];
    if ($active) {
        $patterns = (array) $active;
    } elseif ($route) {
        $patterns = [$route, $route . '.*'];
    }

    $isActive = !empty($patterns) && request()->routeIs($patterns);

    // Format Badge safely (99+ logic)
    $formattedBadge = $badge;
    if (is_numeric($badge) && $badge > 99) {
        $formattedBadge = '99+';
    }

    /*
    |--------------------------------------------------------------------------
    | 🎨 DYNAMIC MULTI-COLOR "LIGHT WHITE" RENDERING ENGINE
    |--------------------------------------------------------------------------
    */
    $colorMap = [
        'slate'   => [
            'activeBg' => 'bg-white shadow-[0_8px_20px_rgba(0,0,0,0.04)] border-slate-100', 'activeText' => 'text-slate-900', 
            'iconBg' => 'bg-slate-800 text-white shadow-md', 'hoverText' => 'group-hover/nav:text-slate-700', 
            'badge' => 'bg-slate-100 text-slate-700 border-slate-200', 'hex' => '#475569', 'glow' => 'rgba(71,85,105,0.4)'
        ],
        'indigo'  => [
            'activeBg' => 'bg-white shadow-[0_8px_20px_rgba(79,70,229,0.06)] border-indigo-50', 'activeText' => 'text-indigo-900', 
            'iconBg' => 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20', 'hoverText' => 'group-hover/nav:text-indigo-600', 
            'badge' => 'bg-indigo-50 text-indigo-700 border-indigo-100', 'hex' => '#4f46e5', 'glow' => 'rgba(79,70,229,0.4)'
        ],
        'emerald' => [
            'activeBg' => 'bg-white shadow-[0_8px_20px_rgba(16,185,129,0.06)] border-emerald-50', 'activeText' => 'text-emerald-900', 
            'iconBg' => 'bg-emerald-500 text-white shadow-md shadow-emerald-500/20', 'hoverText' => 'group-hover/nav:text-emerald-600', 
            'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-100', 'hex' => '#10b981', 'glow' => 'rgba(16,185,129,0.4)'
        ],
        'rose'    => [
            'activeBg' => 'bg-white shadow-[0_8px_20px_rgba(244,63,94,0.06)] border-rose-50', 'activeText' => 'text-rose-900', 
            'iconBg' => 'bg-rose-500 text-white shadow-md shadow-rose-500/20', 'hoverText' => 'group-hover/nav:text-rose-600', 
            'badge' => 'bg-rose-50 text-rose-700 border-rose-100', 'hex' => '#f43f5e', 'glow' => 'rgba(244,63,94,0.4)'
        ],
        'amber'   => [
            'activeBg' => 'bg-white shadow-[0_8px_20px_rgba(245,158,11,0.06)] border-amber-50', 'activeText' => 'text-amber-900', 
            'iconBg' => 'bg-amber-500 text-white shadow-md shadow-amber-500/20', 'hoverText' => 'group-hover/nav:text-amber-600', 
            'badge' => 'bg-amber-50 text-amber-700 border-amber-100', 'hex' => '#f59e0b', 'glow' => 'rgba(245,158,11,0.4)'
        ],
        'sky'     => [
            'activeBg' => 'bg-white shadow-[0_8px_20px_rgba(14,165,233,0.06)] border-sky-50', 'activeText' => 'text-sky-900', 
            'iconBg' => 'bg-sky-500 text-white shadow-md shadow-sky-500/20', 'hoverText' => 'group-hover/nav:text-sky-600', 
            'badge' => 'bg-sky-50 text-sky-700 border-sky-100', 'hex' => '#0ea5e9', 'glow' => 'rgba(14,165,233,0.4)'
        ],
    ];

    $theme = $colorMap[$color] ?? $colorMap['indigo'];

    // State Configuration
    if ($disabled && !$isLocked) {
        $stateClass = "opacity-40 cursor-not-allowed pointer-events-none";
        $iconClass = "text-slate-300 bg-slate-50 border border-slate-100";
        $textClass = "text-slate-400";
    } elseif ($isLocked) {
        $stateClass = "border border-amber-200/50 bg-amber-50/30 hover:bg-amber-50 hover:border-amber-200 hover:shadow-sm";
        $iconClass = "bg-gradient-to-br from-amber-300 to-amber-500 text-white shadow-md shadow-amber-500/20";
        $textClass = "text-amber-700 font-bold";
        $icon = 'fa-crown'; // Force crown icon
    } elseif ($isActive) {
        // 🔥 BEAST MODE: Elevated White Card for Active State
        $stateClass = "{$theme['activeBg']} border scale-[1.01] z-10 ring-1 ring-black/5";
        $iconClass = "{$theme['iconBg']} scale-110";
        $textClass = "{$theme['activeText']} font-black";
    } else {
        // Inactive Hover State
        $stateClass = "border border-transparent hover:bg-slate-50 hover:border-slate-200 hover:shadow-sm";
        $iconClass = "bg-white text-slate-400 border border-slate-200 shadow-sm group-hover/nav:{$theme['iconBg']}";
        $textClass = "text-slate-500 font-bold group-hover/nav:text-slate-900";
    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 BEAST MODE: SMOOTH BEZIER CURVE SPARKLINE GENERATOR (PHP)
    |--------------------------------------------------------------------------
    */
    $svgSparkline = '';
    if ($chartData && !$isLocked) {
        $dataArr = is_string($chartData) ? json_decode($chartData, true) : $chartData;
        if (is_array($dataArr) && count($dataArr) > 1) {
            $max = max($dataArr) ?: 1;
            $min = min($dataArr);
            $range = $max - $min ?: 1;
            $width = 100;
            $height = 20;
            $step = $width / (count($dataArr) - 1);
            
            // Generate Smooth Cubic Bezier Path
            $path = "";
            for ($i = 0; $i < count($dataArr); $i++) {
                $x = $i * $step;
                $y = $height - ((($dataArr[$i] - $min) / $range) * $height);
                
                if ($i === 0) {
                    $path .= "M {$x},{$y} ";
                } else {
                    $prevX = ($i - 1) * $step;
                    $prevY = $height - ((($dataArr[$i - 1] - $min) / $range) * $height);
                    $cp1x = $prevX + ($step / 2);
                    $cp1y = $prevY;
                    $cp2x = $x - ($step / 2);
                    $cp2y = $y;
                    $path .= "C {$cp1x},{$cp1y} {$cp2x},{$cp2y} {$x},{$y} ";
                }
            }
            
            $svgId = 'grad-' . uniqid();
            $svgSparkline = "
                <svg class='w-full h-5 mt-2 opacity-60 group-hover/nav:opacity-100 transition-opacity' viewBox='0 -4 100 28' preserveAspectRatio='none'>
                    <defs>
                        <filter id='shadow-{$svgId}' x='-20%' y='-20%' width='140%' height='140%'>
                            <feDropShadow dx='0' dy='2' stdDeviation='1' flood-color='{$theme['hex']}' flood-opacity='0.4'/>
                        </filter>
                    </defs>
                    <path d='{$path}' fill='none' stroke='{$theme['hex']}' stroke-width='2' stroke-linecap='round' filter='url(#shadow-{$svgId})'></path>
                </svg>";
        }
    }

    // Dynamic padding for nested depths
    $paddingClass = $depth > 0 ? 'pl-' . (2 + ($depth * 3)) . ' pr-2 py-2' : 'px-2 py-2.5';
@endphp

@if($allowed && ($routeExists || $url || $hasChildren))

    <li class="relative group/nav w-full list-none {{ $depth > 0 ? 'mb-0.5' : 'mb-1.5' }}" 
        x-data="navItemEngine({{ $isActive ? 'true' : 'false' }}, {{ $hasChildren ? 'true' : 'false' }}, '{{ $href }}')">
        
        {{-- HTML Tag logic: Use Button for accordions, Anchor for links --}}
        <{{ $hasChildren ? 'button' : 'a' }} 
            @if(!$hasChildren)
                href="{{ $disabled ? 'javascript:void(0)' : $href }}"
                @if($target || $external) target="{{ $target ?? '_blank' }}" @endif
                @if($external) rel="noopener noreferrer" @endif
                @click="triggerRipple($event); if(typeof simulateNavigation === 'function') simulateNavigation();"
            @else
                @click="triggerRipple($event); toggleSubmenu();"
                type="button"
            @endif
            @mouseenter="if(typeof playHoverSound === 'function') playHoverSound()"
            @contextmenu.prevent="openContextMenu($event)"
            aria-current="{{ $isActive ? 'page' : 'false' }}"
            aria-disabled="{{ $disabled ? 'true' : 'false' }}"
            role="menuitem"
            {{-- Merge attributes --}}
            {{ $attributes->merge(['class' => "relative flex items-center rounded-2xl w-full transition-all duration-300 ease-out focus:outline-none focus:ring-4 focus:ring-{$color}-500/10 overflow-hidden {$stateClass} {$paddingClass}"]) }}
            :class="isSidebarCollapsed ? 'justify-center mx-auto w-12 px-0' : ''">

            {{-- 🔥 BEAST MODE: Click Ripple Element --}}
            <span class="absolute rounded-full bg-slate-400/20 pointer-events-none transform scale-0" x-ref="ripple"></span>

            {{-- 1. Active Sidebar Glow Line (Left edge) --}}
            @if($isActive && !$disabled && !$isLocked)
                <div class="absolute -left-1 top-1/2 -translate-y-1/2 w-1.5 h-8 bg-{{ $color }}-500 rounded-r-full shadow-[0_0_12px_rgba(var(--tw-colors-{{ $color }}-500),0.6)] transition-all"></div>
            @endif

            {{-- 2. Master Icon Container --}}
            <div class="{{ $depth > 0 ? 'w-7 h-7 text-[10px]' : 'w-9 h-9 text-xs' }} flex items-center justify-center shrink-0 rounded-[10px] transition-all duration-500 relative {{ $iconClass }}"
                 :class="isSidebarCollapsed ? 'scale-105 shadow-sm border border-slate-100' : ''">
                
                <i class="fa-solid {{ $icon }} transition-transform duration-300 group-hover/nav:scale-110"></i>

                {{-- External Link Mini-Icon --}}
                @if($external && !$isLocked)
                    <i class="fa-solid fa-arrow-up-right-from-square absolute -top-1 -right-1 text-[8px] bg-white rounded-full p-0.5 shadow-sm text-slate-400"></i>
                @endif

                {{-- Ping Dot (Live Notification) --}}
                @if($dot && !$isLocked)
                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-rose-500 rounded-full ring-2 ring-white shadow-sm">
                        <span class="animate-ping absolute inset-0 rounded-full bg-rose-400 opacity-75"></span>
                    </span>
                @endif
            </div>

            {{-- 3. Label & Details Container (Hidden when sidebar is collapsed) --}}
            <div x-show="!isSidebarCollapsed" class="ml-3 truncate flex-1 text-left transition-opacity duration-300" x-transition.opacity>
                
                <div class="flex items-center justify-between w-full">
                    <span class="truncate {{ $textClass }} {{ $depth > 0 ? 'text-xs' : 'text-[13px]' }} tracking-wide transition-colors">
                        {{ $label }}
                    </span>

                    {{-- Badges & Locks --}}
                    @if($isLocked)
                        <span class="ml-2 text-[8px] font-black px-1.5 py-0.5 rounded-md border shadow-sm tracking-widest uppercase bg-amber-100 text-amber-700 border-amber-200">
                            PRO
                        </span>
                    @elseif($badge)
                        @php 
                            $isTextBadge = !is_numeric($badge);
                            $badgeTheme = $isTextBadge ? 'bg-fuchsia-50 text-fuchsia-600 border-fuchsia-200' : $theme['badge'];
                        @endphp
                        <span class="ml-2 text-[9px] font-black px-1.5 py-0.5 rounded-md border shadow-sm transition-opacity duration-300 tracking-wider uppercase {{ $badgeTheme }} {{ $isTextBadge ? 'animate-pulse' : '' }}">
                            {{ $formattedBadge }}
                        </span>
                    @endif
                </div>

                {{-- Subtitle (Optional) --}}
                @if($subtitle)
                    <p class="text-[9px] font-bold text-slate-400 truncate mt-0.5">{{ $subtitle }}</p>
                @endif

                {{-- 🔥 BEAST MODE: Micro Quota Progress Bar --}}
                @if($progress !== null && !$isLocked)
                    @php $progressColor = $progress > 90 ? 'bg-rose-500 shadow-[0_0_5px_rgba(244,63,94,0.5)]' : "bg-{$color}-500 shadow-[0_0_5px_{$theme['glow']}]"; @endphp
                    <div class="w-full h-[3px] bg-slate-200/60 rounded-full overflow-hidden mt-2 border border-slate-200/50 shadow-inner">
                        <div class="h-full {{ $progressColor }} transition-all duration-1000 ease-out" style="width: {{ $progress }}%"></div>
                    </div>
                @endif

                {{-- 🔥 BEAST MODE: SVG Bezier Sparkline Chart --}}
                @if($svgSparkline)
                    {!! $svgSparkline !!}
                @endif
            </div>

            {{-- 4. Context Menu / Accordion Chevron --}}
            <div x-show="!isSidebarCollapsed" class="ml-2 shrink-0 flex items-center justify-center">
                
                {{-- 3-Dot Menu (Optional Hover Action) --}}
                @if($menu && !$hasChildren && !$isLocked)
                    <div class="opacity-0 group-hover/nav:opacity-100 transition-opacity" @click.prevent.stop>
                        <button @click="openContextMenu($event)" class="w-6 h-6 flex items-center justify-center text-slate-400 hover:text-indigo-600 bg-white border border-slate-200 rounded-md shadow-sm hover:shadow transition-all focus:outline-none">
                            <i class="fa-solid fa-ellipsis-vertical text-[10px]"></i>
                        </button>
                    </div>
                @endif

                {{-- Accordion Chevron --}}
                @if($hasChildren)
                    <div class="w-5 h-5 flex items-center justify-center text-slate-400 transition-transform duration-300" :class="expanded ? 'rotate-90 text-indigo-500' : ''">
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </div>
                @endif
            </div>

        </{{ $hasChildren ? 'button' : 'a' }}>

        {{-- ========================================================= --}}
        {{-- 🔥 BEAST MODE: NATIVE RIGHT-CLICK CONTEXT MENU            --}}
        {{-- ========================================================= --}}
        @if(!$hasChildren && !$isLocked)
        <div x-show="contextMenuOpen" x-cloak
             @click.away="contextMenuOpen = false"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             class="fixed bg-white/95 backdrop-blur-xl border border-slate-200 rounded-[1rem] shadow-[0_20px_50px_-10px_rgba(0,0,0,0.15)] overflow-hidden z-[9999] py-1.5 min-w-[180px]"
             :style="`top: ${contextY}px; left: ${contextX}px;`">
            
            <div class="px-3 py-1.5 border-b border-slate-100 bg-slate-50/50 mb-1">
                <span class="text-[8px] font-black uppercase tracking-widest text-slate-400 truncate block">{{ $label }}</span>
            </div>

            <a href="{{ $href }}" target="_blank" @click="contextMenuOpen = false" class="w-full text-left px-3 py-2 text-[11px] font-bold text-slate-600 hover:bg-slate-50 hover:text-indigo-600 transition-colors flex items-center gap-2.5">
                <i class="fa-solid fa-arrow-up-right-from-square w-4 text-center"></i> Open in New Tab
            </a>
            
            <button @click="copyToClipboard('{{ $href }}'); contextMenuOpen = false;" class="w-full text-left px-3 py-2 text-[11px] font-bold text-slate-600 hover:bg-slate-50 hover:text-indigo-600 transition-colors flex items-center gap-2.5 focus:outline-none">
                <i class="fa-regular fa-copy w-4 text-center"></i> Copy Link Address
            </button>
            
            <div class="border-t border-slate-100 my-1 mx-2"></div>
            
            <button @click="contextMenuOpen = false; $dispatch('notify', {message: 'Node pinned to Quick Access', type: 'success'});" class="w-full text-left px-3 py-2 text-[11px] font-bold text-slate-600 hover:bg-slate-50 hover:text-amber-500 transition-colors flex items-center gap-2.5 focus:outline-none">
                <i class="fa-solid fa-thumbtack w-4 text-center"></i> Pin to Quick Access
            </button>
        </div>
        @endif

        {{-- ========================================================= --}}
        {{-- 🔥 BEAST MODE: NESTED SUB-MENU (ACCORDION)                --}}
        {{-- ========================================================= --}}
        @if($hasChildren)
            <div x-show="expanded && !isSidebarCollapsed" x-collapse.duration.300ms class="overflow-hidden">
                <ul class="relative mt-1 pb-1 ml-5 before:absolute before:left-4 before:top-0 before:bottom-3 before:w-px before:bg-slate-200">
                    {{-- Slot injects child <x-sidebar-link> components here --}}
                    {{ $slot }}
                </ul>
            </div>
        @endif

        {{-- ========================================================= --}}
        {{-- 🚨 ALPINE MEGA-TOOLTIP (Visible ONLY when sidebar collapsed)--}}
        {{-- ========================================================= --}}
        <div x-show="isSidebarCollapsed" x-cloak
             class="absolute left-[70px] top-1/2 -translate-y-1/2 px-4 py-3 bg-slate-900 text-white rounded-[1rem] whitespace-nowrap opacity-0 group-hover/nav:opacity-100 pointer-events-none transition-all duration-300 z-[200] shadow-[0_20px_40px_-15px_rgba(0,0,0,0.5)] flex items-center gap-4 transform translate-x-2 group-hover/nav:translate-x-0 border border-slate-700">
            
            <div class="flex flex-col">
                <span class="text-xs font-bold leading-tight flex items-center gap-1.5">
                    @if($isLocked) <i class="fa-solid fa-lock text-amber-400"></i> @endif
                    {{ $label }}
                </span>
                
                @if($subtitle || config('app.debug'))
                    <span class="text-[9px] text-slate-400 font-medium mt-0.5">
                        {{ $subtitle ?? (config('app.debug') ? "Route: {$route}" : '') }}
                    </span>
                @endif
            </div>
            
            <div class="flex items-center gap-2 border-l border-slate-700 pl-4 ml-1">
                @if($isLocked)
                    <span class="bg-amber-500/20 text-amber-400 border border-amber-500/30 text-[9px] font-black px-1.5 py-0.5 rounded shadow-inner uppercase tracking-widest">PRO</span>
                @elseif($badge)
                    <span class="bg-rose-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded shadow-inner uppercase tracking-widest border border-rose-400/50">{{ $formattedBadge }}</span>
                @endif

                {{-- Command Shortcut Hint --}}
                @if($shortcut)
                    <kbd class="px-2 py-0.5 rounded bg-slate-800 border border-slate-600 text-[10px] font-mono font-bold text-slate-300 shadow-inner">{{ $shortcut }}</kbd>
                @endif
            </div>
            
            {{-- CSS Arrow for tooltip pointing left --}}
            <div class="absolute left-[-5px] top-1/2 -translate-y-1/2 w-2.5 h-2.5 bg-slate-900 transform rotate-45 border-b border-l border-slate-700"></div>
        </div>

    </li>

@endif

{{-- ====================================================================== --}}
{{-- 🛠️ ALPINE.JS ISOLATED ENGINE (Loads once per component instance)       --}}
{{-- ====================================================================== --}}
@once
@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('navItemEngine', (isActive, hasChildren, linkUrl) => ({
            expanded: isActive && hasChildren,
            contextMenuOpen: false,
            contextX: 0,
            contextY: 0,
            linkUrl: linkUrl,
            
            // 🚨 SAFE FALLBACK: Resolves "collapsed" from parent safely without crashing
            get isSidebarCollapsed() {
                if (typeof this.$parent !== 'undefined' && this.$parent.collapsed !== undefined) {
                    return this.$parent.collapsed;
                }
                if (typeof sidebarOpen !== 'undefined') {
                    return !sidebarOpen; 
                }
                return false; 
            },

            toggleSubmenu() {
                this.expanded = !this.expanded;
                if(typeof playClickSound === 'function') playClickSound();
            },

            // Ripple Click Engine
            triggerRipple(event) {
                if(typeof playClickSound === 'function') playClickSound();
                
                const ripple = this.$refs.ripple;
                if(!ripple) return;
                
                const rect = this.$el.getBoundingClientRect();
                const x = event.clientX - rect.left;
                const y = event.clientY - rect.top;
                
                // Calculate size based on element dimensions
                const size = Math.max(rect.width, rect.height) * 2;
                
                ripple.style.width = ripple.style.height = `${size}px`;
                ripple.style.left = `${x - size/2}px`;
                ripple.style.top = `${y - size/2}px`;
                
                // Reset animation
                ripple.classList.remove('animate-ripple');
                void ripple.offsetWidth; // trigger reflow
                ripple.classList.add('animate-ripple');
            },

            // Native Context Menu
            openContextMenu(e) {
                if(this.isSidebarCollapsed) return; // Disable context menu if sidebar is closed
                
                this.contextX = e.clientX;
                this.contextY = e.clientY;
                this.contextMenuOpen = true;
                if(typeof playClickSound === 'function') playClickSound();
            },

            copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(() => {
                    this.$dispatch('notify', { message: 'Link copied to clipboard', type: 'success' });
                });
            }
        }));
    });
</script>

<style>
    /* Material Ripple Animation */
    @keyframes ripple {
        0% { transform: scale(0); opacity: 1; }
        100% { transform: scale(1); opacity: 0; }
    }
    .animate-ripple {
        animation: ripple 0.6s linear;
    }
</style>
@endpush
@endonce