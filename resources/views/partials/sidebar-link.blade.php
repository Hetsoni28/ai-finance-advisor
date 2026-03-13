@props([
    'route' => null,
    'url' => null,
    'icon' => 'fa-circle',
    'label' => 'Menu',
    'badge' => null,
    'badgeColor' => 'indigo',
    'permission' => null,
    'role' => null,
    'active' => null,
    'target' => null,
    'disabled' => false,
    'external' => false,
])

@php
    /** @var \App\Models\User|null $user */
    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | Access Control (Safe + Optimized)
    |--------------------------------------------------------------------------
    */
    $allowed = true;

    if ($permission && $user) {
        foreach ((array) $permission as $perm) {
            if (! $user->can($perm)) {
                $allowed = false;
                break;
            }
        }
    }

    if ($role && $user) {
        if (! in_array($user->role, (array) $role)) {
            $allowed = false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Route Resolution
    |--------------------------------------------------------------------------
    */
    $routeExists = $route && Route::has($route);
    $href = $routeExists
        ? route($route)
        : ($url ?? '#');

    /*
    |--------------------------------------------------------------------------
    | Active Detection (Advanced)
    |--------------------------------------------------------------------------
    */
    $patterns = [];

    if ($active) {
        $patterns = (array) $active;
    } elseif ($route) {
        $patterns = [$route, $route.'.*'];
    }

    $isActive = collect($patterns)->contains(
        fn($pattern) => request()->routeIs($pattern)
    );

    /*
    |--------------------------------------------------------------------------
    | Badge Color System (Safe Fallback)
    |--------------------------------------------------------------------------
    */
    $badgeMap = [
        'indigo' => 'bg-indigo-600',
        'red'    => 'bg-rose-600',
        'green'  => 'bg-emerald-600',
        'yellow' => 'bg-amber-500',
        'purple' => 'bg-purple-600',
        'blue'   => 'bg-blue-600',
        'gray'   => 'bg-slate-600',
    ];

    $badgeClass = $badgeMap[$badgeColor] ?? $badgeMap['indigo'];

    /*
    |--------------------------------------------------------------------------
    | Badge Display Logic
    |--------------------------------------------------------------------------
    */
    if (is_numeric($badge) && $badge > 99) {
        $badge = '99+';
    }

    /*
    |--------------------------------------------------------------------------
    | State Classes
    |--------------------------------------------------------------------------
    */
    $baseClass = "group relative flex items-center gap-3 px-4 py-3
                  rounded-xl text-sm font-medium transition-all duration-200
                  focus:outline-none focus:ring-2 focus:ring-indigo-500";

    if ($disabled) {
        $stateClass = "opacity-40 cursor-not-allowed pointer-events-none";
    } elseif ($isActive) {
        $stateClass = "bg-indigo-50 text-indigo-700
                       dark:bg-indigo-900/40 dark:text-indigo-300
                       font-semibold shadow-sm";
    } else {
        $stateClass = "text-slate-600 hover:bg-slate-100 hover:text-indigo-600
                       dark:text-slate-300 dark:hover:bg-slate-800";
    }
@endphp


@if($allowed && ($routeExists || $url))

<a href="{{ $disabled ? '#' : $href }}"
   @if($target || $external)
       target="{{ $target ?? '_blank' }}"
   @endif
   @if($external)
       rel="noopener noreferrer"
   @endif
   aria-current="{{ $isActive ? 'page' : 'false' }}"
   aria-disabled="{{ $disabled ? 'true' : 'false' }}"
   title="{{ $label }}"
   class="{{ $baseClass }} {{ $stateClass }}">

    {{-- Active Side Indicator --}}
    <span class="absolute left-0 top-0 bottom-0 w-1
                 rounded-r-full transition-all duration-300
                 {{ $isActive ? 'bg-indigo-600' : 'bg-transparent' }}">
    </span>

    {{-- ICON --}}
    <div class="relative">
        <i class="fa-solid {{ $icon }}
                  w-5 text-center transition-all duration-200
                  {{ $isActive
                        ? 'text-indigo-600 dark:text-indigo-300'
                        : 'text-slate-400 group-hover:text-indigo-600 dark:text-slate-500'
                  }}">
        </i>

        {{-- External Link Indicator --}}
        @if($external)
            <i class="fa-solid fa-arrow-up-right-from-square
                      text-[9px] absolute -top-1 -right-2 opacity-70"></i>
        @endif
    </div>

    {{-- LABEL --}}
    <span class="truncate">
        {{ $label }}
    </span>

    {{-- BADGE --}}
    @if($badge)
        <span class="ml-auto {{ $badgeClass }}
                     text-white text-[10px] font-semibold
                     px-2 py-0.5 rounded-full shadow-sm
                     {{ is_numeric($badge) ? 'animate-pulse' : '' }}">
            {{ $badge }}
        </span>
    @endif

    {{-- Active Glow Overlay --}}
    @if($isActive)
        <span class="absolute inset-0 rounded-xl
                     ring-1 ring-indigo-300/40 pointer-events-none">
        </span>
    @endif

</a>

@endif