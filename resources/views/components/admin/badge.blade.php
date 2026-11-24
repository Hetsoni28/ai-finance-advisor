@props(['tone' => 'zinc', 'dot' => false])

@php
    $tones = [
        'zinc'    => 'bg-zinc-100 text-zinc-700 ring-zinc-200',
        'indigo'  => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
        'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'rose'    => 'bg-rose-50 text-rose-700 ring-rose-200',
        'amber'   => 'bg-amber-50 text-amber-800 ring-amber-200',
        'sky'     => 'bg-sky-50 text-sky-700 ring-sky-200',
    ];
    $dots = ['zinc' => 'bg-zinc-400', 'indigo' => 'bg-indigo-500', 'emerald' => 'bg-emerald-500', 'rose' => 'bg-rose-500', 'amber' => 'bg-amber-500', 'sky' => 'bg-sky-500'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset ' . ($tones[$tone] ?? $tones['zinc'])]) }}>
    @if($dot)<span class="h-1.5 w-1.5 rounded-full {{ $dots[$tone] ?? $dots['zinc'] }}" aria-hidden="true"></span>@endif
    {{ $slot }}
</span>
