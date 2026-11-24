@props(['label', 'value', 'hint' => null, 'icon' => null, 'tone' => 'zinc', 'href' => null])

@php
    $tones = [
        'zinc'    => 'bg-zinc-100 text-zinc-600',
        'indigo'  => 'bg-indigo-50 text-indigo-600',
        'emerald' => 'bg-emerald-50 text-emerald-600',
        'rose'    => 'bg-rose-50 text-rose-600',
        'amber'   => 'bg-amber-50 text-amber-600',
        'sky'     => 'bg-sky-50 text-sky-600',
    ];
    $iconTone = $tones[$tone] ?? $tones['zinc'];
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => 'block rounded-xl border border-zinc-200 bg-white p-4 sm:p-5' . ($href ? ' transition-colors hover:border-zinc-300' : '')]) }}>
    <div class="flex items-center justify-between gap-3">
        <p class="text-[13px] font-medium text-zinc-500">{{ $label }}</p>
        @if($icon)
            <span class="flex h-7 w-7 items-center justify-center rounded-md {{ $iconTone }}"><i class="fa-solid {{ $icon }} text-xs" aria-hidden="true"></i></span>
        @endif
    </div>
    <p class="mt-2 text-2xl font-semibold tracking-tight text-zinc-900 tabular-nums">{{ $value }}</p>
    @if($hint)
        <p class="mt-1 text-xs text-zinc-500">{!! $hint !!}</p>
    @endif
</{{ $tag }}>
