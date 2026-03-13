@props([
    'title' => '',
    'value' => '',
    'color' => 'green',
    'growth' => null,
    'positive' => null,   // auto-detect if null
    'icon' => null,
    'animate' => false,
    'loading' => false,
])

@php

/* ================= COLOR SYSTEM ================= */

$colors = [
    'green'  => [
        'bg' => 'bg-emerald-50 dark:bg-emerald-500/10',
        'text' => 'text-emerald-700 dark:text-emerald-400',
        'border' => 'border-emerald-200 dark:border-emerald-500/20',
    ],
    'red'    => [
        'bg' => 'bg-rose-50 dark:bg-rose-500/10',
        'text' => 'text-rose-700 dark:text-rose-400',
        'border' => 'border-rose-200 dark:border-rose-500/20',
    ],
    'cyan'   => [
        'bg' => 'bg-cyan-50 dark:bg-cyan-500/10',
        'text' => 'text-cyan-700 dark:text-cyan-400',
        'border' => 'border-cyan-200 dark:border-cyan-500/20',
    ],
    'purple' => [
        'bg' => 'bg-purple-50 dark:bg-purple-500/10',
        'text' => 'text-purple-700 dark:text-purple-400',
        'border' => 'border-purple-200 dark:border-purple-500/20',
    ],
    'amber'  => [
        'bg' => 'bg-amber-50 dark:bg-amber-500/10',
        'text' => 'text-amber-700 dark:text-amber-400',
        'border' => 'border-amber-200 dark:border-amber-500/20',
    ],
];

$colorSet = $colors[$color] ?? $colors['green'];

/* ================= GROWTH AUTO DETECT ================= */

if ($growth !== null && $positive === null) {
    $positive = $growth >= 0;
}

$trendIcon = $positive ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down';
$trendColor = $positive ? 'text-emerald-600' : 'text-rose-600';

@endphp


<div class="relative rounded-2xl border 
            p-6 shadow-sm
            transition-all duration-300
            hover:-translate-y-1 hover:shadow-lg
            {{ $colorSet['bg'] }} {{ $colorSet['border'] }}"
     role="region"
     aria-label="{{ $title }}">

    {{-- Loading Overlay --}}
    @if($loading)
        <div class="absolute inset-0 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm 
                    flex items-center justify-center rounded-2xl z-20">
            <div class="h-6 w-6 border-4 border-current border-t-transparent rounded-full animate-spin {{ $colorSet['text'] }}"></div>
        </div>
    @endif


    {{-- Header --}}
    <div class="flex justify-between items-start">

        <div>
            <p class="text-xs uppercase font-semibold tracking-wider opacity-70 {{ $colorSet['text'] }}">
                {{ $title }}
            </p>

            <h3 class="text-3xl font-bold mt-2 text-slate-900 dark:text-white"
                @if($animate) data-animate="true" data-value="{{ $value }}" @endif>
                {{ $value }}
            </h3>
        </div>

        @if($icon)
            <div class="h-10 w-10 flex items-center justify-center rounded-xl
                        {{ $colorSet['bg'] }}">
                <i class="fa-solid {{ $icon }} {{ $colorSet['text'] }}"></i>
            </div>
        @endif

    </div>


    {{-- Growth --}}
    @if(!is_null($growth))
        <div class="flex items-center gap-1 text-xs mt-3 font-medium {{ $trendColor }}">
            <i class="fa-solid {{ $trendIcon }}"></i>
            {{ abs($growth) }}%
        </div>
    @endif

</div>


{{-- Optional Counter Animation --}}
<script>
document.addEventListener('DOMContentLoaded',function(){
    document.querySelectorAll('[data-animate="true"]').forEach(el=>{
        let target=parseFloat(el.dataset.value);
        if(isNaN(target)) return;
        let start=0;
        let duration=700;
        let startTime=null;

        function animate(time){
            if(!startTime) startTime=time;
            let progress=Math.min((time-startTime)/duration,1);
            let value=start+(target-start)*progress;
            el.innerText=Math.floor(value).toLocaleString();
            if(progress<1){
                requestAnimationFrame(animate);
            }
        }
        requestAnimationFrame(animate);
    });
});
</script>