@props([
    'route' => null,
    'url' => null,
    'icon' => 'fa-circle',
    'label' => 'Menu',
    'badge' => null,
    'badgeColor' => 'blue',
    'match' => null,
    'permission' => null,
    'role' => null,
    'target' => null,
    'disabled' => false,
    'tooltip' => true,
])

@php
    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | Permission Check
    |--------------------------------------------------------------------------
    */
    $hasPermission = true;

    if ($permission && $user) {
        foreach ((array) $permission as $perm) {
            if (! $user->can($perm)) {
                $hasPermission = false;
                break;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Role Check
    |--------------------------------------------------------------------------
    */
    $hasRole = true;

    if ($role && $user) {
        $hasRole = in_array($user->role, (array) $role, true);
    }

    /*
    |--------------------------------------------------------------------------
    | Route Handling
    |--------------------------------------------------------------------------
    */
    $routeExists = $route && \Illuminate\Support\Facades\Route::has($route);
    $href = $routeExists ? route($route) : ($url ?? null);

    /*
    |--------------------------------------------------------------------------
    | Active Detection
    |--------------------------------------------------------------------------
    */
    $patterns = [];

    if ($match) {
        $patterns = (array) $match;
    } elseif ($route) {
        $patterns = [$route, $route.'.*'];
    }

    $isActive = collect($patterns)->contains(fn ($pattern) => request()->routeIs($pattern));

    /*
    |--------------------------------------------------------------------------
    | Visibility
    |--------------------------------------------------------------------------
    */
    $visible = $hasPermission && $hasRole;

    /*
    |--------------------------------------------------------------------------
    | Badge Color System
    |--------------------------------------------------------------------------
    */
    $badgeColors = [
        'blue'   => 'bg-blue-600',
        'red'    => 'bg-rose-600',
        'green'  => 'bg-emerald-600',
        'yellow' => 'bg-amber-500',
        'purple' => 'bg-purple-600',
        'gray'   => 'bg-slate-500',
    ];

    $badgeClass = $badgeColors[$badgeColor] ?? $badgeColors['blue'];

    /*
    |--------------------------------------------------------------------------
    | Final State
    |--------------------------------------------------------------------------
    */
    $isExternal = $target === '_blank';
@endphp


@if($visible && $href)

<a href="{{ $disabled ? 'javascript:void(0)' : $href }}"
   @if($target) target="{{ $target }}" @endif
   @if($isExternal) rel="noopener noreferrer" @endif
   @if($disabled) tabindex="-1" aria-disabled="true" @endif
   aria-current="{{ $isActive ? 'page' : 'false' }}"
   aria-label="{{ $label }}"
   @if($tooltip) title="{{ $label }}" @endif

   class="relative flex items-center gap-3
          px-4 py-2.5 rounded-xl
          text-sm font-medium
          transition-all duration-200 ease-out
          focus:outline-none focus:ring-2 focus:ring-blue-500

          {{ $disabled
                ? 'opacity-40 cursor-not-allowed pointer-events-none'
                : ($isActive
                    ? 'bg-blue-50 text-blue-700 font-semibold dark:bg-blue-900/30 dark:text-blue-400'
                    : 'text-slate-600 hover:bg-slate-100 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-800')
          }}">

    {{-- LEFT ACTIVE GLOW --}}
    <span class="absolute left-0 top-0 bottom-0 w-1
                 rounded-r-full transition-all duration-300
                 {{ $isActive ? 'bg-blue-600' : 'bg-transparent' }}">
    </span>

    {{-- ICON --}}
    <i class="fa {{ $icon }}
              text-base transition-all duration-200
              {{ $isActive
                    ? 'text-blue-600 dark:text-blue-400'
                    : 'text-slate-400 group-hover:text-blue-600 dark:text-slate-500'
              }}">
    </i>

    {{-- LABEL --}}
    <span class="truncate sidebar-text transition-opacity duration-200">
        {{ $label }}
    </span>

    {{-- BADGE --}}
    @if($badge)
        <span class="ml-auto {{ $badgeClass }}
                     text-white text-xs font-semibold
                     px-2 py-0.5 rounded-full shadow-sm
                     animate-pulse">
            {{ $badge }}
        </span>
    @endif

    {{-- EXTERNAL ICON --}}
    @if($isExternal)
        <i class="fa-solid fa-arrow-up-right-from-square text-xs text-slate-400 ml-1"></i>
    @endif

</a>

@endif