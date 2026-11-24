{{--
    Dark editorial page header used across public pages.
    Props: eyebrow (mono label), lead (plain words), accent (serif italic words), tail (plain words after accent), lede (paragraph).
    Slots: actions (buttons), meta (small facts row), aside (right column on desktop).
--}}
@props(['eyebrow' => null, 'lead' => '', 'accent' => null, 'tail' => null, 'lede' => null])

<section class="relative overflow-hidden bg-[#0b0b1a] text-white grain">
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 sm:pt-24 pb-16 sm:pb-20 {{ isset($aside) ? 'grid lg:grid-cols-12 gap-12 items-end' : '' }}">
        <div class="{{ isset($aside) ? 'lg:col-span-7' : 'max-w-3xl' }}">
            @if($eyebrow)
                <p class="fade-in eyebrow text-indigo-200/90" style="--d: 0ms">{{ $eyebrow }}</p>
            @endif

            <h1 class="mt-6 text-[2.5rem] leading-[1.04] sm:text-6xl font-black tracking-[-0.035em] text-white">
                <span class="rise">
                    @foreach(preg_split('/\s+/', trim($lead), -1, PREG_SPLIT_NO_EMPTY) as $i => $w)
                        <span><span style="--d: {{ 80 + $i * 60 }}ms">{{ $w }}</span></span>
                    @endforeach
                    @if($accent)
                        @foreach(preg_split('/\s+/', trim($accent), -1, PREG_SPLIT_NO_EMPTY) as $k => $w)
                            <span><span class="font-serif-i text-indigo-300 pr-1" style="--d: {{ 120 + (count(preg_split('/\s+/', trim($lead), -1, PREG_SPLIT_NO_EMPTY)) + $k) * 60 }}ms">{{ $w }}</span></span>
                        @endforeach
                    @endif
                    @if($tail)
                        @foreach(preg_split('/\s+/', trim($tail), -1, PREG_SPLIT_NO_EMPTY) as $j => $w)
                            <span><span style="--d: {{ 180 + (count(preg_split('/\s+/', trim($lead), -1, PREG_SPLIT_NO_EMPTY)) + $j) * 60 }}ms">{{ $w }}</span></span>
                        @endforeach
                    @endif
                </span>
            </h1>

            @if($lede)
                <p class="fade-in mt-7 text-base sm:text-lg text-slate-300 leading-relaxed max-w-2xl" style="--d: 450ms">{{ $lede }}</p>
            @endif

            @isset($actions)
                <div class="fade-in mt-9 flex flex-col sm:flex-row gap-3" style="--d: 560ms">{{ $actions }}</div>
            @endisset

            @isset($meta)
                <div class="fade-in mt-10 border-t border-white/15 pt-6" style="--d: 680ms">{{ $meta }}</div>
            @endisset
        </div>

        @isset($aside)
            <div class="fade-in lg:col-span-5" style="--d: 600ms">{{ $aside }}</div>
        @endisset
    </div>
</section>
