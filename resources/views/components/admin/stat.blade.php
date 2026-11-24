@props([
    'label', 'value', 'hint' => null, 'icon' => null, 'tone' => 'zinc', 'href' => null,
    'delta' => null,        // % change vs previous period (real), or null to hide
    'goodWhen' => 'up',     // 'up' if an increase is good, 'down' if a decrease is good
    'spark' => null,        // array of real values (oldest → newest) for a mini trend line
    'deltaLabel' => 'vs last month',
])

@php
    $tones = [
        'zinc'    => ['bg-zinc-100 text-zinc-600',     '#71717a'],
        'indigo'  => ['bg-indigo-50 text-indigo-600',  '#6366f1'],
        'emerald' => ['bg-emerald-50 text-emerald-600','#10b981'],
        'rose'    => ['bg-rose-50 text-rose-600',      '#f43f5e'],
        'amber'   => ['bg-amber-50 text-amber-600',    '#f59e0b'],
        'sky'     => ['bg-sky-50 text-sky-600',        '#0ea5e9'],
    ];
    [$iconTone, $stroke] = $tones[$tone] ?? $tones['zinc'];
    $tag = $href ? 'a' : 'div';

    // Sparkline path from real values
    $sparkPath = null; $sparkArea = null;
    if (is_array($spark) && count($spark) > 1 && max($spark) > 0) {
        $w = 100; $h = 28; $n = count($spark) - 1; $max = max($spark); $min = min(0, min($spark));
        $pts = [];
        foreach (array_values($spark) as $i => $v) {
            $x = round($i / $n * $w, 2);
            $y = round($h - (($v - $min) / max(1e-9, $max - $min)) * ($h - 3) - 1.5, 2);
            $pts[] = "$x,$y";
        }
        $sparkPath = 'M' . implode(' L', $pts);
        $sparkArea = $sparkPath . " L$w,$h L0,$h Z";
    }

    $deltaTone = null;
    if ($delta !== null) {
        $isGood = $delta == 0 ? null : (($delta > 0) === ($goodWhen === 'up'));
        $deltaTone = $isGood === null ? 'text-zinc-500 bg-zinc-100' : ($isGood ? 'text-emerald-700 bg-emerald-50' : 'text-rose-700 bg-rose-50');
    }
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => 'group relative block overflow-hidden rounded-xl border border-zinc-200 bg-white p-4 sm:p-5 shadow-[0_1px_2px_rgba(24,24,27,.04)]' . ($href ? ' transition hover:border-zinc-300 hover:shadow-[0_4px_16px_-6px_rgba(24,24,27,.12)]' : '')]) }}>
    <div class="flex items-center justify-between gap-3">
        <p class="text-[13px] font-medium text-zinc-500">{{ $label }}</p>
        @if($icon)
            <span class="flex h-7 w-7 items-center justify-center rounded-md {{ $iconTone }}"><i class="fa-solid {{ $icon }} text-xs" aria-hidden="true"></i></span>
        @endif
    </div>

    <div class="mt-2 flex items-end justify-between gap-3">
        <p class="text-[26px] font-semibold leading-none tracking-tight text-zinc-900 tabular-nums">{{ $value }}</p>
        @if($sparkPath)
            <svg viewBox="0 0 100 28" preserveAspectRatio="none" class="h-7 w-20 shrink-0" aria-hidden="true">
                <path d="{{ $sparkArea }}" fill="{{ $stroke }}" fill-opacity=".1" />
                <path d="{{ $sparkPath }}" fill="none" stroke="{{ $stroke }}" stroke-width="1.75" stroke-linejoin="round" stroke-linecap="round" vector-effect="non-scaling-stroke" />
            </svg>
        @endif
    </div>

    @if($delta !== null || $hint)
        <div class="mt-2.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs">
            @if($delta !== null)
                <span class="inline-flex items-center gap-1 rounded px-1.5 py-0.5 font-medium tabular-nums {{ $deltaTone }}">
                    <i class="fa-solid {{ $delta > 0 ? 'fa-arrow-up' : ($delta < 0 ? 'fa-arrow-down' : 'fa-minus') }} text-[9px]" aria-hidden="true"></i>{{ abs($delta) }}%
                </span>
                <span class="text-zinc-500">{{ $deltaLabel }}</span>
            @endif
            @if($hint)<span class="text-zinc-500">{!! $hint !!}</span>@endif
        </div>
    @endif

    @if($href)
        <i class="fa-solid fa-arrow-right absolute bottom-4 right-4 text-[10px] text-zinc-300 opacity-0 transition group-hover:opacity-100" aria-hidden="true"></i>
    @endif
</{{ $tag }}>
