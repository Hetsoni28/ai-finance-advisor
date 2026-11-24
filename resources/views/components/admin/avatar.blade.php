@props(['name' => '?', 'size' => 'md', 'muted' => false])

@php
    // Deterministic colour per person (same name → same colour), not random.
    $palette = ['bg-indigo-100 text-indigo-700', 'bg-emerald-100 text-emerald-700', 'bg-amber-100 text-amber-800', 'bg-sky-100 text-sky-700', 'bg-rose-100 text-rose-700', 'bg-violet-100 text-violet-700', 'bg-teal-100 text-teal-700', 'bg-orange-100 text-orange-700'];
    $tone = $muted ? 'bg-zinc-100 text-zinc-500' : $palette[abs(crc32(mb_strtolower(trim($name)))) % count($palette)];
    $parts = array_values(array_filter(preg_split('/\s+/', trim($name))));
    $initials = mb_strtoupper(count($parts) >= 2 ? mb_substr($parts[0], 0, 1) . mb_substr(end($parts), 0, 1) : mb_substr($name ?: '?', 0, 1));
    $sizes = ['sm' => 'h-7 w-7 text-[11px]', 'md' => 'h-9 w-9 text-xs', 'lg' => 'h-11 w-11 text-sm'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center justify-center rounded-full font-semibold ' . $tone . ' ' . ($sizes[$size] ?? $sizes['md'])]) }} aria-hidden="true">{{ $initials }}</span>
