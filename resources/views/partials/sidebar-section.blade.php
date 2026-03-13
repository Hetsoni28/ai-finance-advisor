@props([
    'title' => null,
    'icon' => null,
    'color' => 'default',
    'permission' => null,
    'role' => null,
    'divider' => true,
    'badge' => null,
    'badgeColor' => 'indigo',
])

@php
    /** @var \App\Models\User|null $user */
    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | Visibility Control (Role + Permission Safe)
    |--------------------------------------------------------------------------
    */
    $visible = true;

    if ($permission && $user) {
        foreach ((array) $permission as $perm) {
            if (! $user->can($perm)) {
                $visible = false;
                break;
            }
        }
    }

    if ($role && $user) {
        if (! in_array($user->role, (array) $role)) {
            $visible = false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Color System
    |--------------------------------------------------------------------------
    */
    $colorMap = [
        'default' => 'text-slate-400',
        'indigo'  => 'text-indigo-500',
        'blue'    => 'text-blue-500',
        'green'   => 'text-emerald-500',
        'red'     => 'text-rose-500',
        'yellow'  => 'text-amber-500',
        'purple'  => 'text-purple-500',
    ];

    $badgeMap = [
        'indigo' => 'bg-indigo-500',
        'blue'   => 'bg-blue-500',
        'green'  => 'bg-emerald-500',
        'red'    => 'bg-rose-500',
        'yellow' => 'bg-amber-500',
        'purple' => 'bg-purple-500',
    ];

    $colorClass = $colorMap[$color] ?? $colorMap['default'];
    $badgeClass = $badgeMap[$badgeColor] ?? $badgeMap['indigo'];
@endphp


@if($visible && filled($title))

<div class="relative group">

    {{-- Divider --}}
    @if($divider)
        <div class="border-t border-slate-200 dark:border-slate-800 my-6 first:hidden"></div>
    @endif

    {{-- Section Label --}}
    <div
        class="flex items-center justify-between
               px-3 py-3
               text-[10px] font-semibold uppercase
               tracking-[0.18em]
               transition-all duration-200
               {{ $colorClass }}
               opacity-70 group-hover:opacity-100">

        <div class="flex items-center gap-2">

            @if($icon)
                <i class="fa-solid {{ $icon }}
                          text-[11px]
                          transition-transform duration-200
                          group-hover:scale-110">
                </i>
            @endif

            <span class="truncate sidebar-text">
                {{ $title }}
            </span>

        </div>

        {{-- Optional Badge --}}
        @if(!is_null($badge))
            <span class="text-[9px] font-bold px-2 py-[2px]
                         rounded-full text-white
                         {{ $badgeClass }}
                         shadow-sm animate-pulse">
                {{ $badge }}
            </span>
        @endif

    </div>

</div>

@endif