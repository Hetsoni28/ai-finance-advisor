{{-- Section heading: mono index/label, big title with optional serif-italic accent, optional lede. --}}
@props(['label' => null, 'title' => '', 'accent' => null, 'lede' => null, 'dark' => false, 'center' => false])

<div {{ $attributes->merge(['class' => ($center ? 'text-center mx-auto ' : '') . 'max-w-3xl']) }} data-reveal>
    @if($label)
        <p class="eyebrow {{ $dark ? 'text-indigo-300' : 'text-indigo-600' }}">{{ $label }}</p>
    @endif
    <h2 class="mt-4 text-3xl sm:text-5xl font-black tracking-[-0.03em] leading-[1.05] {{ $dark ? 'text-white' : 'text-slate-900' }}">
        {{ $title }}@if($accent) <span class="font-serif-i {{ $dark ? 'text-indigo-300' : 'text-indigo-600' }}">{{ $accent }}</span>@endif
    </h2>
    @if($lede)
        <p class="mt-5 text-base sm:text-lg leading-relaxed {{ $dark ? 'text-slate-300' : 'text-slate-600' }} {{ $center ? 'mx-auto' : '' }} max-w-2xl">{{ $lede }}</p>
    @endif
</div>
