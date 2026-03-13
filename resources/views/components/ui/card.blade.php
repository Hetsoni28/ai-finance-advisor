@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'value' => null,
    'trend' => null,
    'padding' => 'p-6',
    'variant' => 'default',   // default | soft | elevated | gradient | glass
    'accent' => null,
    'hover' => true,
    'loading' => false,
    'animate' => false,
])

@php

/* ================= VARIANTS ================= */

$variants = [
    'default'  => 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm',
    'soft'     => 'bg-slate-50 dark:bg-slate-800/60 border border-transparent',
    'elevated' => 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl',
    'gradient' => 'bg-gradient-to-br from-white to-slate-100 dark:from-slate-900 dark:to-slate-800 border border-slate-200 dark:border-slate-800 shadow-lg',
    'glass'    => 'bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/20 dark:border-white/10 shadow-2xl',
];

$base = $variants[$variant] ?? $variants['default'];


/* ================= ACCENTS ================= */

$accents = [
    'blue'   => ['border' => 'border-l-4 border-blue-600',   'bg' => 'bg-blue-100 dark:bg-blue-900/30',   'text' => 'text-blue-600 dark:text-blue-400'],
    'green'  => ['border' => 'border-l-4 border-emerald-600','bg' => 'bg-emerald-100 dark:bg-emerald-900/30','text' => 'text-emerald-600 dark:text-emerald-400'],
    'red'    => ['border' => 'border-l-4 border-rose-600',   'bg' => 'bg-rose-100 dark:bg-rose-900/30',   'text' => 'text-rose-600 dark:text-rose-400'],
    'purple' => ['border' => 'border-l-4 border-purple-600', 'bg' => 'bg-purple-100 dark:bg-purple-900/30','text' => 'text-purple-600 dark:text-purple-400'],
    'amber'  => ['border' => 'border-l-4 border-amber-500',  'bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-600 dark:text-amber-400'],
];

$accentSet = $accent && isset($accents[$accent]) ? $accents[$accent] : null;

$accentBorder = $accentSet['border'] ?? '';
$iconBg       = $accentSet['bg'] ?? 'bg-blue-100 dark:bg-blue-900/30';
$iconColor    = $accentSet['text'] ?? 'text-blue-600 dark:text-blue-400';


/* ================= HOVER ================= */

$hoverClass = $hover
    ? 'transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl'
    : '';


/* ================= TREND ================= */

$trendColor = null;
$trendIcon  = null;

if ($trend !== null) {

    $numeric = floatval(str_replace(['%','+'], '', $trend));

    if ($numeric < 0) {
        $trendColor = 'text-rose-600';
        $trendIcon  = 'fa-arrow-down';
    } else {
        $trendColor = 'text-emerald-600';
        $trendIcon  = 'fa-arrow-up';
    }
}

@endphp


<div {{ $attributes->merge([
        'class' => "relative rounded-2xl overflow-hidden $base $accentBorder $hoverClass $padding"
    ]) }}
    role="region"
    aria-label="{{ $title ?? 'KPI Card' }}">

    {{-- Loading Overlay --}}
    @if($loading)
        <div class="absolute inset-0 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm z-20 flex items-center justify-center">
            <div class="h-8 w-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
        </div>
    @endif


    {{-- Header --}}
    @if($title || isset($actions))
        <div class="flex items-start justify-between mb-5">

            <div class="flex items-center gap-3">

                @if($icon)
                    <div class="h-10 w-10 flex items-center justify-center rounded-xl {{ $iconBg }}">
                        <i class="fa-solid {{ $icon }} {{ $iconColor }}"></i>
                    </div>
                @endif

                <div>
                    @if($title)
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            {{ $title }}
                        </h3>
                    @endif

                    @if($subtitle)
                        <p class="text-xs text-slate-400 mt-1">
                            {{ $subtitle }}
                        </p>
                    @endif
                </div>

            </div>

            @isset($actions)
                <div>
                    {{ $actions }}
                </div>
            @endisset

        </div>
    @endif


    {{-- KPI VALUE --}}
    @if($value !== null)
        <div class="flex items-end justify-between">

            <div class="text-3xl font-bold text-slate-900 dark:text-white
                        {{ is_numeric($value) && $value < 0 ? 'text-rose-600' : '' }}"
                 @if($animate) data-animate="true" data-value="{{ $value }}" @endif>
                {{ $value }}
            </div>

            @if($trend !== null)
                <div class="flex items-center gap-1 text-sm font-medium {{ $trendColor }}">
                    <i class="fa-solid {{ $trendIcon }}"></i>
                    {{ $trend }}
                </div>
            @endif

        </div>
    @else
        <div>
            {{ $slot }}
        </div>
    @endif


    {{-- Footer --}}
    @isset($footer)
        <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-800 text-sm text-slate-500">
            {{ $footer }}
        </div>
    @endisset

</div>


{{-- Optional Value Animation --}}
<script>
document.addEventListener('DOMContentLoaded',function(){
    document.querySelectorAll('[data-animate="true"]').forEach(el=>{
        let target=parseFloat(el.dataset.value);
        if(isNaN(target)) return;
        let start=0;
        let duration=800;
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