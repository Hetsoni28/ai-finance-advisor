@extends('layouts.landing')
@include('partials.vault-intro')

@section('title', 'FinanceAI — Your AI finance advisor that reads your real numbers')
@section('meta_description', 'An AI finance advisor powered by Google Gemini. Track income and expenses in rupees, get an AI summary of your month, AI-explained reports and answers based on your own numbers. Free to start.')

@push('styles')
<style>
    .font-serif-i { font-family: 'Instrument Serif', Georgia, serif; font-style: italic; font-weight: 400; letter-spacing: -0.01em; }
    .font-mono { font-family: 'JetBrains Mono', ui-monospace, monospace; }
    .paper { background-color: #fbfbfa; }
    .hairline { background-image: linear-gradient(to right, transparent, rgba(15,23,42,.12), transparent); height: 1px; }
    .dots { background-image: radial-gradient(rgba(79,70,229,.18) 1px, transparent 1px); background-size: 18px 18px; }

    /* Scroll reveal */
    [data-reveal] { opacity: 0; transform: translateY(24px); transition: opacity .8s cubic-bezier(.2,.7,.2,1), transform .8s cubic-bezier(.2,.7,.2,1); transition-delay: var(--d, 0ms); }
    [data-reveal].is-visible { opacity: 1; transform: none; }

    /* Hero headline: words rise out of a mask on load */
    .rise > span { display: inline-block; overflow: hidden; vertical-align: bottom; padding-bottom: .08em; }
    .rise > span > span { display: inline-block; transform: translateY(105%); animation: rise .9s cubic-bezier(.2,.7,.2,1) forwards; animation-delay: var(--d, 0ms); }
    @keyframes rise { to { transform: none; } }
    .fade-in { opacity: 0; animation: fadeIn .8s ease forwards; animation-delay: var(--d, 0ms); }
    @keyframes fadeIn { to { opacity: 1; } }
    .typing-line { animation: fadeIn .3s ease forwards, typingOut .3s ease 2.5s forwards; animation-delay: var(--d, 0ms), 2500ms; }
    @keyframes typingOut { to { opacity: 0; height: 0; padding-top: 0; padding-bottom: 0; margin-top: -.75rem; } }

    /* Hero photo: slow settle-in zoom, and a gently floating example chip */
    .hero-img { transform: scale(1.08); animation: heroZoom 1.8s cubic-bezier(.2,.7,.2,1) .2s forwards; }
    @keyframes heroZoom { to { transform: scale(1); } }
    .float-chip { animation: chipFloat 6s ease-in-out 1.5s infinite; }
    @keyframes chipFloat { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }

    /* Category marquee */
    .marquee { -webkit-mask-image: linear-gradient(to right, transparent, #000 8%, #000 92%, transparent); mask-image: linear-gradient(to right, transparent, #000 8%, #000 92%, transparent); }
    .marquee-track { display: flex; width: max-content; animation: marquee 38s linear infinite; }
    .marquee:hover .marquee-track { animation-play-state: paused; }
    @keyframes marquee { to { transform: translateX(-50%); } }

    /* Data animations (triggered when a parent gets .is-visible) */
    .grow-bar { height: 0; transition: height .9s cubic-bezier(.2,.7,.2,1); transition-delay: var(--d, 0ms); }
    .is-visible .grow-bar { height: var(--h); }
    .fill { width: 0; transition: width 1.1s cubic-bezier(.2,.7,.2,1); transition-delay: var(--d, 0ms); }
    .is-visible .fill { width: var(--w); }
    .ring-draw { stroke-dashoffset: 97.4; transition: stroke-dashoffset 1.4s cubic-bezier(.2,.7,.2,1) .2s; }
    .is-visible .ring-draw { stroke-dashoffset: var(--off); }
    .caret { animation: blink 1s steps(1) infinite; }
    @keyframes blink { 50% { opacity: 0; } }
    .typing span { width: 6px; height: 6px; border-radius: 9999px; background: #94a3b8; display: inline-block; animation: bounce 1.2s infinite; }
    .typing span:nth-child(2) { animation-delay: .15s; } .typing span:nth-child(3) { animation-delay: .3s; }
    @keyframes bounce { 0%, 60%, 100% { transform: translateY(0); opacity: .5; } 30% { transform: translateY(-4px); opacity: 1; } }

    /* Film-grain texture for dark panels (adds depth without gradients/blobs) */
    .grain { position: relative; isolation: isolate; }
    .grain::after {
        content: ''; position: absolute; inset: 0; pointer-events: none; z-index: 0; opacity: .09; mix-blend-mode: overlay;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='160' height='160'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='3' stitchTiles='stitch'/></filter><rect width='100%' height='100%' filter='url(%23n)'/></svg>");
    }
    .grain > *:not(.absolute) { position: relative; z-index: 1; }

    /* Bento cards: quiet hover */
    .bento > article { transition: border-color .3s ease, box-shadow .3s ease; }
    .bento > article:hover { border-color: #c7d2fe; box-shadow: 0 24px 48px -24px rgba(30,27,75,.25); }

    /* Story panel cross-fade */
    .screen { transition: opacity .5s ease, transform .5s cubic-bezier(.2,.7,.2,1); }

    /* Range input */
    input[type=range].range { -webkit-appearance: none; appearance: none; width: 100%; height: 6px; border-radius: 9999px; background: linear-gradient(to right, #4f46e5 var(--p, 50%), #e2e8f0 var(--p, 50%)); outline: none; }
    input[type=range].range::-webkit-slider-thumb { -webkit-appearance: none; width: 22px; height: 22px; border-radius: 9999px; background: #fff; border: 2px solid #4f46e5; box-shadow: 0 2px 8px rgba(79,70,229,.35); cursor: pointer; }
    input[type=range].range::-moz-range-thumb { width: 20px; height: 20px; border-radius: 9999px; background: #fff; border: 2px solid #4f46e5; cursor: pointer; }
    input[type=range].range:focus-visible::-webkit-slider-thumb { box-shadow: 0 0 0 6px rgba(79,70,229,.25); }

    @media (prefers-reduced-motion: reduce) {
        [data-reveal], .rise > span > span, .fade-in { opacity: 1 !important; transform: none !important; animation: none !important; transition: none !important; }
        .marquee-track, .caret, .typing span, .float-chip { animation: none !important; }
        .typing-line { display: none !important; }
        .hero-img { animation: none !important; transform: none !important; }
        .grow-bar, .fill, .ring-draw, .screen { transition: none !important; }
    }
</style>
@endpush

@section('content')
<div class="paper">

{{-- =====================================================================
     HERO — full-bleed photo background (Unsplash License, photographer Julio Lopez)
     ===================================================================== --}}
<section class="relative overflow-hidden bg-[#0b0b1a] text-white grain">
    {{-- Background photo: full-bleed on mobile, right ~65% fading into the dark on desktop --}}
    <div class="absolute inset-0 lg:left-auto lg:w-[66%]" aria-hidden="true">
        <picture>
            <source type="image/webp" sizes="(min-width: 1024px) 66vw, 100vw"
                    srcset="{{ asset('img/landing/hero-couple.webp') }} 1400w, {{ asset('img/landing/hero-couple-2400.webp') }} 2400w">
            <img src="{{ asset('img/landing/hero-couple.jpg') }}"
                 srcset="{{ asset('img/landing/hero-couple.jpg') }} 1400w, {{ asset('img/landing/hero-couple-2400.jpg') }} 2400w"
                 sizes="(min-width: 1024px) 66vw, 100vw"
                 alt="" width="2400" height="1600" fetchpriority="high" decoding="async"
                 class="hero-img absolute inset-0 w-full h-full object-cover object-[55%_30%] lg:object-[50%_35%]">
        </picture>
        {{-- Mobile: darken evenly, heavier at the bottom where the buttons sit --}}
        <div class="absolute inset-0 bg-[#0b0b1a]/65 lg:hidden"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#0b0b1a] via-[#0b0b1a]/40 to-transparent lg:hidden"></div>
        {{-- Desktop: fade the photo's left edge into the dark panel behind the text --}}
        <div class="absolute inset-0 hidden lg:block bg-gradient-to-r from-[#0b0b1a] via-[#0b0b1a]/55 to-transparent"></div>
        <div class="absolute inset-x-0 bottom-0 h-40 hidden lg:block bg-gradient-to-t from-[#0b0b1a]/80 to-transparent"></div>
        {{-- Brand tint so the photo sits inside the indigo palette --}}
        <div class="absolute inset-0 bg-indigo-950/25 mix-blend-multiply"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 min-h-[38rem] sm:min-h-[42rem] lg:min-h-[46rem] flex items-end lg:items-center pt-40 sm:pt-48 lg:pt-20 pb-14 lg:pb-24">
        <div class="max-w-2xl lg:max-w-xl xl:max-w-2xl">
            <p class="fade-in font-mono text-[11px] sm:text-xs uppercase tracking-[0.2em] text-indigo-200/90" style="--d: 0ms">
                <i class="fa-solid fa-wand-magic-sparkles mr-1 text-indigo-300" aria-hidden="true"></i> AI finance advisor <span class="text-indigo-400">·</span> ₹ INR
            </p>

            <h1 class="mt-6 text-[2.6rem] leading-[1.02] sm:text-6xl xl:text-7xl font-black tracking-[-0.035em] text-white">
                <span class="rise block"><span><span style="--d: 80ms">An</span></span> <span><span style="--d: 130ms">AI</span></span> <span><span style="--d: 180ms">advisor</span></span></span>
                <span class="rise block"><span><span style="--d: 240ms">that</span></span> <span><span style="--d: 290ms">reads</span></span> <span><span style="--d: 340ms">your</span></span></span>
                <span class="rise block"><span><span class="font-serif-i text-indigo-300 pr-1" style="--d: 400ms">real</span></span> <span><span style="--d: 460ms">numbers.</span></span></span>
            </h1>

            <p class="fade-in mt-7 text-base sm:text-lg text-slate-200/90 leading-relaxed max-w-xl" style="--d: 560ms">
                Record your income and spending in rupees. FinanceAI's advisor — powered by Google Gemini — reads them,
                summarises your month, explains your reports and answers your money questions with your own figures.
            </p>

            <div class="fade-in mt-9 flex flex-col sm:flex-row gap-3" style="--d: 660ms">
                <a href="{{ auth()->check() ? route('user.ai.chat') : route('register') }}" class="group inline-flex items-center justify-center gap-2 h-12 px-6 rounded-xl text-white font-bold bg-gradient-to-br from-indigo-500 to-violet-600 shadow-[0_10px_30px_-8px_rgba(99,102,241,.7)] hover:-translate-y-0.5 transition-transform focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-300/60">
                    <i class="fa-solid fa-wand-magic-sparkles text-sm" aria-hidden="true"></i>
                    {{ auth()->check() ? 'Ask your AI advisor' : 'Try the AI advisor free' }}
                    <i class="fa-solid fa-arrow-right text-sm transition-transform group-hover:translate-x-0.5" aria-hidden="true"></i>
                </a>
                <a href="{{ route('features') }}#how-ai" class="inline-flex items-center justify-center gap-2 h-12 px-6 rounded-xl font-bold text-white border border-white/25 bg-white/5 backdrop-blur-sm hover:bg-white/10 transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-white/30">
                    How the AI works
                </a>
            </div>

            <dl class="fade-in mt-12 grid grid-cols-3 max-w-lg border-t border-white/15 pt-6" style="--d: 780ms">
                <div><dt class="font-mono text-[11px] uppercase tracking-wider text-slate-400">AI engine</dt><dd class="mt-1 text-xl sm:text-2xl font-black text-white">Gemini</dd></div>
                <div><dt class="font-mono text-[11px] uppercase tracking-wider text-slate-400">Starts at</dt><dd class="mt-1 text-xl sm:text-2xl font-black text-white">₹0</dd></div>
                <div><dt class="font-mono text-[11px] uppercase tracking-wider text-slate-400">Bank login</dt><dd class="mt-1 text-xl sm:text-2xl font-black text-white">Never</dd></div>
            </dl>
        </div>

        {{-- Sample AI conversation over the photo (desktop only). Clearly labelled as an example. --}}
        <figure class="hidden lg:block absolute right-8 xl:right-16 bottom-14 w-[23rem] rounded-2xl bg-white/95 backdrop-blur border border-white/60 shadow-[0_24px_48px_-16px_rgba(0,0,0,.55)] p-4 text-slate-900" aria-label="Example conversation with the AI advisor">
            <figcaption class="flex items-center justify-between pb-3 border-b border-slate-100">
                <span class="flex items-center gap-2 text-sm font-bold"><span class="h-7 w-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center"><i class="fa-solid fa-wand-magic-sparkles text-[11px]" aria-hidden="true"></i></span>AI advisor</span>
                <span class="font-mono text-[10px] uppercase tracking-wider text-slate-400">Example</span>
            </figcaption>
            <div class="mt-3 space-y-3 text-[13px] leading-relaxed">
                <p class="fade-in ml-auto w-fit max-w-[85%] rounded-2xl rounded-br-md bg-slate-900 px-3.5 py-2 text-white" style="--d: 1000ms">Can I afford a ₹15,000 phone this month?</p>
                <p class="fade-in typing-line flex gap-1 w-fit rounded-2xl rounded-tl-md bg-slate-100 px-3.5 py-3" style="--d: 1500ms" aria-hidden="true"><span class="h-1.5 w-1.5 rounded-full bg-slate-400 animate-bounce"></span><span class="h-1.5 w-1.5 rounded-full bg-slate-400 animate-bounce [animation-delay:.15s]"></span><span class="h-1.5 w-1.5 rounded-full bg-slate-400 animate-bounce [animation-delay:.3s]"></span></p>
                <div class="fade-in max-w-[92%] rounded-2xl rounded-tl-md bg-slate-100 px-3.5 py-2.5 text-slate-700" style="--d: 2600ms">
                    You've earned <b class="text-slate-900">₹62,000</b> and spent <b class="text-slate-900">₹41,500</b> so far, so <b class="text-slate-900">₹20,500</b> is left. The phone would leave ₹5,500 — below your usual 20% savings. Waiting until next month keeps you on target.
                </div>
            </div>
            <p class="fade-in mt-3 font-mono text-[10px] uppercase tracking-wider text-slate-400" style="--d: 2800ms"><i class="fa-solid fa-microchip mr-1" aria-hidden="true"></i>Answers use your own records</p>
        </figure>
    </div>
</section>

{{-- =====================================================================
     CALCULATOR — same formula the app uses for the first 70 points of the health score
     ===================================================================== --}}
<section id="calculator" class="scroll-mt-20 py-20 sm:py-24 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-12 lg:gap-16 items-center">
        <div class="lg:col-span-5" data-reveal>
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-indigo-600">Try it</p>
            <h2 class="mt-4 text-4xl sm:text-5xl font-black tracking-[-0.03em] text-slate-900 leading-[1.05]">
                How much are you <span class="font-serif-i text-indigo-600">keeping?</span>
            </h2>
            <p class="mt-6 text-lg text-slate-600 leading-relaxed">Move the sliders to see your savings rate and how many health-score points it earns — using the same formula FinanceAI uses.</p>
            <ul class="mt-8 space-y-3 text-slate-700">
                <li class="flex gap-3"><i class="fa-solid fa-lock text-indigo-600 mt-1" aria-hidden="true"></i>Runs in your browser. Nothing is saved or sent.</li>
                <li class="flex gap-3"><i class="fa-solid fa-calculator text-indigo-600 mt-1" aria-hidden="true"></i>Savings rate counts for 40 points, spending control for 30.</li>
                <li class="flex gap-3"><i class="fa-solid fa-chart-line text-indigo-600 mt-1" aria-hidden="true"></i>Track it for real and FinanceAI adds the other 30 from your history.</li>
            </ul>
        </div>
        <div class="lg:col-span-7" data-reveal style="--d: 120ms"
             x-data="{
                income: 60000, expense: 42000,
                fmt(n) { return '₹' + Math.round(n).toLocaleString('en-IN'); },
                get saved() { return this.income - this.expense; },
                get rate() { return this.income > 0 ? Math.max(this.saved, 0) / this.income * 100 : 0; },
                get ratio() { return this.income > 0 ? this.expense / this.income * 100 : 100; },
                get points() { return this.rate * 0.40 + (100 - Math.min(100, this.ratio)) * 0.30; },
                pct(v, max) { return (v / max * 100) + '%'; }
             }">
            <div class="relative rounded-[1.75rem] bg-white border border-slate-200 shadow-[0_40px_80px_-30px_rgba(30,27,75,.25)] p-6 sm:p-8">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="font-mono text-[11px] uppercase tracking-[0.18em] text-indigo-600">Savings calculator</p>
                        <p class="mt-1.5 text-xl font-black tracking-tight text-slate-900">Move the sliders</p>
                    </div>
                    <span class="shrink-0 inline-flex items-center gap-1.5 font-mono text-[11px] text-slate-500 bg-slate-50 border border-slate-200 rounded-md px-2 py-1">
                        <i class="fa-solid fa-lock text-[11px]" aria-hidden="true"></i> Not saved
                    </span>
                </div>

                <div class="mt-7 space-y-6">
                    <div>
                        <div class="flex justify-between items-baseline">
                            <label for="calc-income" class="text-sm font-semibold text-slate-600">Monthly income</label>
                            <span class="text-lg font-black text-slate-900 tabular-nums" x-text="fmt(income)">₹60,000</span>
                        </div>
                        <input id="calc-income" type="range" min="5000" max="300000" step="1000" x-model.number="income"
                               class="range mt-3" :style="'--p:' + pct(income - 5000, 295000)">
                    </div>
                    <div>
                        <div class="flex justify-between items-baseline">
                            <label for="calc-expense" class="text-sm font-semibold text-slate-600">Monthly spending</label>
                            <span class="text-lg font-black text-slate-900 tabular-nums" x-text="fmt(expense)">₹42,000</span>
                        </div>
                        <input id="calc-expense" type="range" min="0" max="300000" step="1000" x-model.number="expense"
                               class="range mt-3" :style="'--p:' + pct(expense, 300000)">
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-3">
                    <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                        <p class="text-xs font-semibold text-slate-500" x-text="saved >= 0 ? 'You keep each month' : 'You overspend by'">You keep each month</p>
                        <p class="mt-1 text-2xl font-black tabular-nums" :class="saved >= 0 ? 'text-emerald-600' : 'text-rose-600'" x-text="fmt(Math.abs(saved))">₹18,000</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                        <p class="text-xs font-semibold text-slate-500">Savings rate</p>
                        <p class="mt-1 text-2xl font-black text-slate-900 tabular-nums" x-text="rate.toFixed(0) + '%'">30%</p>
                    </div>
                </div>

                <div class="mt-3 rounded-2xl border border-indigo-100 bg-indigo-50/60 p-4">
                    <div class="flex items-baseline justify-between gap-3">
                        <p class="text-xs font-semibold text-indigo-900">Health score points from these two numbers</p>
                        <p class="shrink-0 whitespace-nowrap text-lg font-black text-indigo-700 tabular-nums"><span x-text="points.toFixed(0)">21</span><span class="text-sm text-indigo-400"> / 70</span></p>
                    </div>
                    <div class="mt-2.5 h-2 rounded-full bg-white overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-indigo-600 to-violet-600 transition-[width] duration-300" :style="'width:' + (points / 70 * 100) + '%'"></div>
                    </div>
                    <p class="mt-2.5 text-[12px] leading-snug text-indigo-900/70">The other 30 points come from how steady your money is month to month and your emergency runway — FinanceAI works those out from your history.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =====================================================================
     CATEGORY MARQUEE — the app's real built-in categories
     ===================================================================== --}}
<section class="border-y border-slate-200 bg-white py-5" aria-label="Built-in categories">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center gap-6">
        <p class="hidden md:block shrink-0 font-mono text-[11px] uppercase tracking-[0.18em] text-slate-500">Built-in<br>categories</p>
        <div class="marquee overflow-hidden flex-1">
            <ul class="marquee-track gap-3">
                @php
                    $cats = [
                        ['Salary','fa-briefcase','in'],['Food','fa-burger','out'],['Freelance','fa-laptop-code','in'],['Travel','fa-plane','out'],
                        ['Investments','fa-chart-pie','in'],['Bills','fa-file-invoice','out'],['Business','fa-store','in'],['Shopping','fa-bag-shopping','out'],
                        ['Gifts','fa-gift','in'],['Health','fa-heart-pulse','out'],
                    ];
                @endphp
                @foreach(array_merge($cats, $cats) as $k => [$name, $icon, $dir])
                    <li class="shrink-0 inline-flex items-center gap-2.5 pl-2 pr-4 py-2 rounded-full border border-slate-200 bg-white text-sm font-semibold text-slate-700" @if($k >= count($cats)) aria-hidden="true" @endif>
                        <span class="w-7 h-7 rounded-full flex items-center justify-center {{ $dir === 'in' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-500' }}"><i class="fa-solid {{ $icon }} text-xs" aria-hidden="true"></i></span>
                        {{ $name }}
                        <span class="font-mono text-[11px] {{ $dir === 'in' ? 'text-emerald-600' : 'text-rose-500' }}">{{ $dir === 'in' ? 'IN' : 'OUT' }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

{{-- =====================================================================
     HOW IT WORKS — pinned product panel that changes as you scroll
     ===================================================================== --}}
<section id="how" class="scroll-mt-20 py-20 sm:py-28" x-data="{ active: 0 }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl" data-reveal>
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-indigo-600">How it works</p>
            <h2 class="mt-4 text-4xl sm:text-5xl font-black tracking-[-0.03em] text-slate-900 leading-[1.05]">
                Three steps. <span class="font-serif-i text-slate-500">No spreadsheets.</span>
            </h2>
        </div>

        <div class="mt-14 lg:mt-20 grid lg:grid-cols-2 gap-10 lg:gap-20">
            {{-- Pinned screen (desktop) --}}
            <div class="hidden lg:block">
                <div class="sticky top-28">
                    <div class="relative h-[26rem]">
                        <div class="absolute -inset-6 rounded-[2rem] dots opacity-70" aria-hidden="true"></div>
                        @foreach(['add','see','ask'] as $i => $screen)
                            <div class="screen absolute inset-x-0 top-6"
                                 :class="active === {{ $i }} ? 'opacity-100 translate-y-0 is-visible' : 'opacity-0 translate-y-4 pointer-events-none'">
                                @include('partials.landing.screen-' . $screen)
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Steps --}}
            <ol class="space-y-16 lg:space-y-0">
                @foreach([
                    ['01', 'Add', 'Log money as it moves', 'Add income and expenses in seconds. Pick a category, a date and an amount — Salary, Freelance, Food, Bills and more are built in. Edit or delete anything later.', 'add'],
                    ['02', 'See', 'Watch the picture form', 'Your dashboard totals everything automatically: what came in, what went out, what you kept, and where it went by category and by month.', 'see'],
                    ['03', 'Ask', 'Get answers, not charts', 'Ask the AI advisor in plain English. It reads your own numbers, goals and risk profile before it replies, so the advice fits your life.', 'ask'],
                ] as $i => [$num, $kicker, $title, $body, $screen])
                    <li class="lg:min-h-[70vh] lg:flex lg:items-center" x-intersect:enter.half="active = {{ $i }}">
                        <div data-reveal>
                            <div class="flex items-center gap-4">
                                <span class="font-mono text-sm font-semibold transition-colors" :class="active === {{ $i }} ? 'text-indigo-600' : 'text-slate-400'">{{ $num }}</span>
                                <span class="h-px w-12 transition-colors" :class="active === {{ $i }} ? 'bg-indigo-600' : 'bg-slate-300'"></span>
                                <span class="font-mono text-xs uppercase tracking-[0.2em] text-slate-500">{{ $kicker }}</span>
                            </div>
                            <h3 class="mt-5 text-3xl sm:text-4xl font-black tracking-[-0.03em] text-slate-900">{{ $title }}</h3>
                            <p class="mt-4 text-lg text-slate-600 leading-relaxed max-w-md">{{ $body }}</p>
                            <div class="mt-8 lg:hidden" data-reveal>
                                @include('partials.landing.screen-' . $screen)
                            </div>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</section>

{{-- =====================================================================
     MADE FOR — three real use cases, each mapped to features that exist
     ===================================================================== --}}
<section class="py-20 sm:py-28 bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6" data-reveal>
            <div class="max-w-2xl">
                <p class="font-mono text-xs uppercase tracking-[0.2em] text-indigo-600">Made for</p>
                <h2 class="mt-4 text-4xl sm:text-5xl font-black tracking-[-0.03em] text-slate-900 leading-[1.05]">
                    However your money <span class="font-serif-i text-indigo-600">moves.</span>
                </h2>
            </div>
            <p class="text-lg text-slate-600 max-w-sm">One app, three very different households. Pick the one that sounds like you.</p>
        </div>

        <div class="mt-14 grid lg:grid-cols-3 border-t border-slate-200">
            @foreach([
                ['01', 'fa-user', 'Individuals', 'Salaried and want to know where it all goes.', ['Log salary and every expense by category', 'Watch your savings rate and health score', 'Ask the advisor before big purchases']],
                ['02', 'fa-people-roof', 'Families', 'Running a household budget together.', ['Invite family members by email', 'Track shared income and spending', 'Owners, admins and members with clear roles']],
                ['03', 'fa-laptop-code', 'Freelancers & side earners', 'Income that changes from month to month.', ['Separate Salary, Freelance and Business income', 'Consistency score shows how steady you are', 'Set an emergency-fund goal for lean months']],
            ] as $k => [$num, $icon, $title, $lede, $points])
                <article class="group relative pt-10 pb-10 lg:pr-10 {{ $k > 0 ? 'lg:pl-10 lg:border-l border-slate-200' : '' }} {{ $k > 0 ? 'border-t lg:border-t-0 border-slate-200' : '' }}" data-reveal style="--d: {{ $k * 100 }}ms">
                    <span class="absolute top-0 left-0 {{ $k > 0 ? 'lg:left-10' : '' }} h-[2px] w-0 bg-gradient-to-r from-indigo-600 to-violet-600 transition-all duration-500 group-hover:w-24" aria-hidden="true"></span>
                    <div class="flex items-center justify-between">
                        <span class="w-11 h-11 rounded-xl bg-slate-950 text-white flex items-center justify-center"><i class="fa-solid {{ $icon }}" aria-hidden="true"></i></span>
                        <span class="font-mono text-sm text-slate-400">{{ $num }}</span>
                    </div>
                    <h3 class="mt-8 text-2xl font-black tracking-tight text-slate-900">{{ $title }}</h3>
                    <p class="mt-2 text-slate-600">{{ $lede }}</p>
                    <ul class="mt-6 space-y-3">
                        @foreach($points as $p)
                            <li class="flex gap-3 text-sm text-slate-700"><i class="fa-solid fa-arrow-right text-xs text-indigo-600 mt-1" aria-hidden="true"></i>{{ $p }}</li>
                        @endforeach
                    </ul>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- =====================================================================
     FEATURES — bento grid with real product details
     ===================================================================== --}}
<section id="features" class="scroll-mt-20 py-20 sm:py-28 bg-white border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-6 items-end" data-reveal>
            <div>
                <p class="font-mono text-xs uppercase tracking-[0.2em] text-indigo-600">What's inside</p>
                <h2 class="mt-4 text-4xl sm:text-5xl font-black tracking-[-0.03em] text-slate-900 leading-[1.05]">
                    Small tools. <span class="font-serif-i text-indigo-600">Big</span> clarity.
                </h2>
            </div>
            <p class="text-lg text-slate-600 leading-relaxed lg:max-w-md lg:justify-self-end">Everything you need to understand your money — and nothing you'll never use.</p>
        </div>

        <div class="bento mt-14 grid md:grid-cols-2 lg:grid-cols-3 gap-4">

            {{-- Health score (wide) --}}
            <article class="lg:col-span-2 rounded-[1.5rem] border border-slate-200 bg-[#fbfbfa] p-6 sm:p-8 grid sm:grid-cols-5 gap-8 items-center" data-reveal>
                <div class="sm:col-span-2 flex flex-col items-center sm:items-start">
                    <span class="font-mono text-[11px] uppercase tracking-[0.18em] text-slate-500">Financial health score</span>
                    <div class="relative mt-5 w-36 h-36">
                        <svg viewBox="0 0 36 36" class="w-full h-full -rotate-90" aria-hidden="true">
                            <circle cx="18" cy="18" r="15.5" fill="none" stroke="#e0e7ff" stroke-width="3"/>
                            <circle class="ring-draw" cx="18" cy="18" r="15.5" fill="none" stroke="url(#hs)" stroke-width="3" stroke-linecap="round" stroke-dasharray="97.4" style="--off: 0"/>
                            <defs><linearGradient id="hs" x1="0" x2="1"><stop offset="0" stop-color="#4f46e5"/><stop offset="1" stop-color="#7c3aed"/></linearGradient></defs>
                        </svg>
                        <span class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-4xl font-black text-slate-900">100</span>
                            <span class="font-mono text-[11px] text-slate-500 uppercase tracking-wider">points</span>
                        </span>
                    </div>
                </div>
                <div class="sm:col-span-3">
                    <h3 class="text-2xl font-black tracking-tight text-slate-900">One number for how stable you are</h3>
                    <p class="mt-2 text-slate-600 leading-relaxed">Here's exactly how the 0–100 score is built:</p>
                    <ul class="mt-5 space-y-3.5">
                        @foreach([['Savings rate', 40], ['Spending control', 30], ['Month-to-month consistency', 20], ['Emergency runway (6-month target)', 10]] as $k => [$label, $w])
                            <li>
                                <div class="flex justify-between text-sm"><span class="font-semibold text-slate-700">{{ $label }}</span><span class="font-mono font-semibold text-slate-900">{{ $w }}%</span></div>
                                <div class="mt-1.5 h-2 rounded-full bg-slate-200/70 overflow-hidden"><div class="fill h-full rounded-full bg-gradient-to-r from-indigo-600 to-violet-600" style="--w: {{ $w / 40 * 100 }}%; --d: {{ $k * 120 }}ms"></div></div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </article>

            {{-- Family hub --}}
            <article class="rounded-[1.5rem] border border-slate-200 bg-white p-6 sm:p-8 flex flex-col" data-reveal style="--d: 80ms">
                <span class="font-mono text-[11px] uppercase tracking-[0.18em] text-slate-500">Family hub</span>
                <h3 class="mt-3 text-2xl font-black tracking-tight text-slate-900">One budget, the whole house</h3>
                <p class="mt-2 text-slate-600 leading-relaxed">Invite people by email. They join with a one-click link and add to the shared income and spending.</p>
                <ul class="mt-auto pt-6 space-y-2" aria-label="Family roles">
                    @foreach([['Owner','Manages members and invites','bg-indigo-600 text-white'],['Admin','Helps manage the family','bg-indigo-100 text-indigo-700'],['Member','Adds and views shared money','bg-slate-100 text-slate-700']] as [$role, $desc, $tone])
                        <li class="flex items-center gap-3 rounded-xl border border-slate-100 p-2.5">
                            <span class="font-mono text-[11px] font-semibold px-2 py-1 rounded-md {{ $tone }}">{{ strtoupper($role) }}</span>
                            <span class="text-sm text-slate-600">{{ $desc }}</span>
                        </li>
                    @endforeach
                </ul>
            </article>

            {{-- Reports --}}
            <article class="rounded-[1.5rem] border border-slate-200 bg-white p-6 sm:p-8 flex flex-col" data-reveal>
                <span class="font-mono text-[11px] uppercase tracking-[0.18em] text-slate-500">Reports</span>
                <h3 class="mt-3 text-2xl font-black tracking-tight text-slate-900">Take it with you</h3>
                <p class="mt-2 text-slate-600 leading-relaxed">Monthly trends and category breakdowns in the app — and clean PDF ledgers to download.</p>
                <div class="mt-auto pt-6 space-y-2" aria-hidden="true">
                    @foreach(['Income ledger', 'Expense ledger'] as $doc)
                        <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3">
                            <span class="w-9 h-11 rounded-md bg-white border border-slate-200 flex items-end justify-center pb-1 font-mono text-[9px] font-semibold text-rose-500">PDF</span>
                            <div class="flex-1"><p class="text-sm font-bold text-slate-800">{{ $doc }}</p><p class="font-mono text-[11px] text-slate-500">Summary · categories · entries</p></div>
                        </div>
                    @endforeach
                </div>
            </article>

            {{-- Markets --}}
            <article class="rounded-[1.5rem] border border-slate-200 bg-slate-950 text-white p-6 sm:p-8 flex flex-col" data-reveal style="--d: 80ms">
                <span class="font-mono text-[11px] uppercase tracking-[0.18em] text-slate-400">Market snapshot</span>
                <h3 class="mt-3 text-2xl font-black tracking-tight">Markets, in rupees</h3>
                <p class="mt-2 text-slate-400 leading-relaxed">Bitcoin, Ethereum and gold prices in ₹ inside the app — and the AI considers them when you ask about investing.</p>
                <div class="mt-auto pt-6 grid grid-cols-3 gap-2" aria-hidden="true">
                    @foreach([['BTC','fa-bitcoin','fa-brands'],['ETH','fa-ethereum','fa-brands'],['GOLD','fa-coins','fa-solid']] as [$t, $i, $set])
                        <div class="rounded-xl border border-white/10 bg-white/5 p-3 text-center">
                            <i class="{{ $set }} {{ $i }} text-lg text-indigo-300"></i>
                            <p class="mt-1 font-mono text-xs font-semibold">{{ $t }}</p>
                            <p class="font-mono text-[11px] text-slate-500">in ₹</p>
                        </div>
                    @endforeach
                </div>
            </article>

            {{-- Goals --}}
            <article class="rounded-[1.5rem] border border-slate-200 bg-white p-6 sm:p-8 flex flex-col" data-reveal style="--d: 160ms">
                <span class="font-mono text-[11px] uppercase tracking-[0.18em] text-slate-500">Goals</span>
                <h3 class="mt-3 text-2xl font-black tracking-tight text-slate-900">Save for something real</h3>
                <p class="mt-2 text-slate-600 leading-relaxed">Set a target amount and date, give it a priority, and track it with the advisor.</p>
                <div class="mt-auto pt-6 flex flex-wrap gap-2">
                    @foreach(['Emergency fund','House','Car','Education','Retirement','Travel','Wedding','Gadget'] as $g)
                        <span class="px-3 py-1.5 rounded-full border border-slate-200 text-xs font-semibold text-slate-600">{{ $g }}</span>
                    @endforeach
                </div>
            </article>
        </div>
    </div>
</section>

{{-- =====================================================================
     COMPARISON — honest, including where a spreadsheet wins
     ===================================================================== --}}
<section class="py-20 sm:py-28">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto" data-reveal>
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-indigo-600">Why not a spreadsheet?</p>
            <h2 class="mt-4 text-4xl sm:text-5xl font-black tracking-[-0.03em] text-slate-900 leading-[1.05]">
                Spreadsheets are great. <span class="font-serif-i text-slate-500">Until they aren't.</span>
            </h2>
        </div>

        <div class="mt-14 rounded-[1.5rem] border border-slate-200 bg-white overflow-hidden" data-reveal style="--d: 100ms">
            <table class="w-full text-left text-sm sm:text-base">
                <caption class="sr-only">Comparison of a spreadsheet and FinanceAI</caption>
                <thead>
                    <tr class="border-b border-slate-200">
                        <th scope="col" class="py-5 px-4 sm:px-8 font-mono text-[11px] uppercase tracking-[0.16em] text-slate-500 font-semibold w-1/2">What you need</th>
                        <th scope="col" class="py-5 px-3 sm:px-6 font-bold text-slate-500 text-center">Spreadsheet</th>
                        <th scope="col" class="py-5 px-3 sm:px-6 text-center bg-indigo-50/60">
                            <span class="inline-flex items-center gap-2 font-black text-slate-900"><span class="w-6 h-6 rounded-md bg-gradient-to-br from-indigo-500 to-violet-600 text-white hidden sm:inline-flex items-center justify-center"><i class="fa-solid fa-cube text-[11px]" aria-hidden="true"></i></span>FinanceAI</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        $yes = '<i class="fa-solid fa-circle-check text-emerald-500 text-lg" aria-hidden="true"></i><span class="sr-only">Yes</span>';
                        $no  = '<i class="fa-solid fa-minus text-slate-300" aria-hidden="true"></i><span class="sr-only">No</span>';
                    @endphp
                    @foreach([
                        ['Totals and savings rate', 'Write formulas', $yes],
                        ['Spending by category and month', 'Build a pivot table', $yes],
                        ['A health score with a clear breakdown', $no, $yes],
                        ['A shared budget with roles', 'Share a file', $yes],
                        ['Ask questions in plain English', $no, $yes],
                        ['PDF ledgers', 'Export by hand', $yes],
                        ['Fully custom layouts and formulas', $yes, 'Focused on personal & family finance'],
                        ['Price to start', 'Free', 'Free'],
                    ] as [$label, $sheet, $us])
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <th scope="row" class="py-4 sm:py-5 px-4 sm:px-8 font-semibold text-slate-800">{{ $label }}</th>
                            <td class="py-4 sm:py-5 px-3 sm:px-6 text-center text-slate-500 text-xs sm:text-sm">{!! $sheet !!}</td>
                            <td class="py-4 sm:py-5 px-3 sm:px-6 text-center bg-indigo-50/40 text-slate-700 text-xs sm:text-sm font-semibold">{!! $us !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- =====================================================================
     AI ADVISOR — dark section, chat types itself out when it scrolls in
     ===================================================================== --}}
<section id="ai" class="scroll-mt-20 grain overflow-hidden bg-[#0b0b1a] text-white py-20 sm:py-28">
    <div class="absolute inset-0 opacity-[0.15] dots pointer-events-none" aria-hidden="true"></div>
    <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-indigo-500/70 to-transparent" aria-hidden="true"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-12 lg:gap-16 items-center">
        <div class="lg:col-span-5" data-reveal>
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-indigo-300">AI advisor</p>
            <h2 class="mt-4 text-4xl sm:text-5xl font-black tracking-[-0.03em] leading-[1.05]">
                Advice that knows <span class="font-serif-i text-indigo-300">your</span> rent.
            </h2>
            <p class="mt-6 text-lg text-slate-300 leading-relaxed">
                Generic tips can't see your salary or your spending. FinanceAI's advisor reads your income, expenses,
                savings goals and risk profile first — then answers.
            </p>
            <dl class="mt-10 grid grid-cols-2 gap-6 border-t border-white/10 pt-8">
                <div><dt class="font-mono text-[11px] uppercase tracking-wider text-slate-500">Risk profiles</dt><dd class="mt-1.5 text-sm text-slate-200">Conservative, moderate or aggressive</dd></div>
                <div><dt class="font-mono text-[11px] uppercase tracking-wider text-slate-500">Model</dt><dd class="mt-1.5 text-sm text-slate-200">Google Gemini</dd></div>
            </dl>
        </div>

        <div class="lg:col-span-7" data-reveal style="--d: 120ms"
             x-data="{ step: 0, started: false, run() { if (this.started) return; this.started = true; const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches; if (reduce) { this.step = 5; return; } [500, 1700, 2900, 4000, 5000].forEach((t, i) => setTimeout(() => this.step = i + 1, t)); } }"
             x-intersect.half="run()">
            <div class="rounded-[1.5rem] border border-white/10 bg-white/[0.04] backdrop-blur p-5 sm:p-7 shadow-2xl" role="img" aria-label="Example conversation: a user asks if they can save two lakh rupees for a trip by next December; the advisor says yes, about fifteen thousand four hundred a month, and saves it as a goal.">
                <div class="flex items-center justify-between pb-5 border-b border-white/10">
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center"><i class="fa-solid fa-cube text-sm" aria-hidden="true"></i></span>
                        <p class="text-sm font-bold">FinanceAI Advisor</p>
                    </div>
                    <span class="font-mono text-[11px] text-slate-500">Example conversation</span>
                </div>

                <div class="pt-6 space-y-4 text-[15px] min-h-[19rem]" aria-hidden="true">
                    <div class="flex justify-end transition-all duration-500" :class="step >= 1 ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'">
                        <p class="max-w-[85%] rounded-2xl rounded-br-sm bg-gradient-to-br from-indigo-600 to-violet-600 px-4 py-3">Can I save ₹2 lakh for a trip by next December?</p>
                    </div>

                    <div class="flex" x-show="step === 2" x-cloak>
                        <p class="rounded-2xl rounded-bl-sm bg-white/10 px-4 py-3.5 typing flex gap-1"><span></span><span></span><span></span></p>
                    </div>

                    <div class="flex transition-all duration-500" :class="step >= 3 ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2 h-0 overflow-hidden'">
                        <div class="max-w-[90%] rounded-2xl rounded-bl-sm bg-white/10 border border-white/10 px-4 py-3 text-slate-200 leading-relaxed">
                            <p>Yes. That's about <strong class="text-white">₹15,400 a month</strong>. You currently keep around ₹18,000 a month, so it fits — with a little room to spare.</p>
                        </div>
                    </div>

                    <div class="flex justify-end transition-all duration-500" :class="step >= 4 ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'">
                        <p class="max-w-[85%] rounded-2xl rounded-br-sm bg-gradient-to-br from-indigo-600 to-violet-600 px-4 py-3">Save it as a goal.</p>
                    </div>

                    <div class="flex transition-all duration-500" :class="step >= 5 ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'">
                        <p class="rounded-2xl rounded-bl-sm bg-emerald-500/10 border border-emerald-400/20 text-emerald-200 px-4 py-3"><i class="fa-solid fa-circle-check mr-1.5" aria-hidden="true"></i>Goal “Trip” added · ₹2,00,000</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =====================================================================
     SECURITY — only measures that exist in the code
     ===================================================================== --}}
<section class="py-20 sm:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-12">
        <div class="lg:col-span-5" data-reveal>
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-indigo-600">Privacy &amp; security</p>
            <h2 class="mt-4 text-4xl sm:text-5xl font-black tracking-[-0.03em] text-slate-900 leading-[1.05]">
                We never ask for your <span class="font-serif-i text-indigo-600">bank</span> password.
            </h2>
            <p class="mt-6 text-lg text-slate-600 leading-relaxed">You decide what goes in. There's no bank connection to leak, and the basics are locked down.</p>
            @if(Route::has('privacy'))
                <a href="{{ route('privacy') }}" class="mt-8 inline-flex items-center gap-2 font-bold text-indigo-700 hover:text-indigo-900 group">
                    Read the privacy policy <i class="fa-solid fa-arrow-right text-sm transition-transform group-hover:translate-x-0.5" aria-hidden="true"></i>
                </a>
            @endif
        </div>

        <ul class="lg:col-span-7 grid sm:grid-cols-2 gap-px bg-slate-200 rounded-[1.5rem] overflow-hidden border border-slate-200" data-reveal style="--d: 100ms">
            @foreach([
                ['fa-building-columns', 'No bank connection', 'You add income and expenses yourself. FinanceAI never needs your bank login.'],
                ['fa-key', 'Hashed passwords', 'Passwords are stored with bcrypt hashing — never in plain text.'],
                ['fa-gauge-high', 'Sign-in limits', 'Login is limited to 5 attempts a minute to slow down password guessing.'],
                ['fa-shield-halved', 'Protected forms', 'Every form is protected against cross-site request forgery.'],
                ['fa-user-lock', 'Family permissions', 'Owners, admins and members can each only do what their role allows.'],
                ['fa-file-export', 'Your data, exportable', 'Download your income and expense ledgers as PDF whenever you want.'],
            ] as $k => [$icon, $title, $body])
                <li class="bg-white p-6 sm:p-7">
                    <i class="fa-solid {{ $icon }} text-indigo-600" aria-hidden="true"></i>
                    <h3 class="mt-4 font-bold text-slate-900">{{ $title }}</h3>
                    <p class="mt-1.5 text-sm text-slate-600 leading-relaxed">{{ $body }}</p>
                </li>
            @endforeach
        </ul>
    </div>
</section>

{{-- =====================================================================
     PRICING — plans and prices come straight from the database
     ===================================================================== --}}
@if(isset($plans) && $plans->isNotEmpty())
<section id="pricing" class="scroll-mt-20 py-20 sm:py-28 bg-white border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto" data-reveal>
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-indigo-600">Pricing</p>
            <h2 class="mt-4 text-4xl sm:text-5xl font-black tracking-[-0.03em] text-slate-900 leading-[1.05]">
                Start at <span class="font-serif-i text-indigo-600">₹0</span>. Stay as long as you like.
            </h2>
        </div>

        <div class="mt-14 grid md:grid-cols-{{ min(3, $plans->count()) }} gap-4 max-w-5xl mx-auto items-stretch">
            @foreach($plans as $k => $plan)
                @php $featured = $plan->slug === 'pro'; $paid = (float) $plan->price > 0; @endphp
                <div class="relative rounded-[1.5rem] p-7 sm:p-8 flex flex-col {{ $featured ? 'bg-[#0b0b1a] text-white shadow-[0_40px_80px_-30px_rgba(30,27,75,.6)]' : 'bg-[#fbfbfa] border border-slate-200' }}" data-reveal style="--d: {{ $k * 90 }}ms">
                    <div class="flex items-center justify-between">
                        <h3 class="font-mono text-xs uppercase tracking-[0.18em] {{ $featured ? 'text-indigo-300' : 'text-slate-500' }}">{{ $plan->name }}</h3>
                        @if($featured)<span class="font-mono text-[11px] font-semibold uppercase tracking-wider px-2 py-1 rounded-md bg-gradient-to-br from-indigo-600 to-violet-600">Popular</span>@endif
                    </div>
                    <p class="mt-6 flex items-baseline gap-1.5">
                        <span class="text-5xl font-black tracking-tight tabular-nums">₹{{ inr_number((float) $plan->price) }}</span>
                        <span class="text-sm {{ $featured ? 'text-slate-400' : 'text-slate-500' }}">{{ $paid ? '/ ' . ($plan->duration_days ?: 30) . ' days' : 'forever' }}</span>
                    </p>
                    <div class="my-7 h-px {{ $featured ? 'bg-white/10' : 'bg-slate-200' }}"></div>
                    <ul class="space-y-3 text-sm flex-1">
                        @foreach((array) $plan->features as $feature)
                            <li class="flex gap-3"><i class="fa-solid fa-check mt-0.5 {{ $featured ? 'text-indigo-300' : 'text-indigo-600' }}" aria-hidden="true"></i><span class="{{ $featured ? 'text-slate-300' : 'text-slate-600' }}">{{ $feature }}</span></li>
                        @endforeach
                    </ul>
                    <a href="{{ auth()->check() ? route('user.subscription.index') : route('register') }}"
                       class="mt-8 inline-flex justify-center items-center h-12 rounded-xl font-bold transition-all focus:outline-none focus-visible:ring-4 {{ $featured ? 'bg-gradient-to-br from-indigo-600 to-violet-600 text-white hover:-translate-y-0.5 focus-visible:ring-indigo-400' : 'bg-white border border-slate-200 text-slate-900 hover:border-slate-300 focus-visible:ring-indigo-200' }}">
                        {{ $paid ? 'Choose ' . $plan->name : 'Start free' }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- =====================================================================
     FAQ
     ===================================================================== --}}
<section id="faq" class="scroll-mt-20 py-20 sm:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-12">
        <div class="lg:col-span-4" data-reveal>
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-indigo-600">FAQ</p>
            <h2 class="mt-4 text-4xl sm:text-5xl font-black tracking-[-0.03em] text-slate-900 leading-[1.05]">Good <span class="font-serif-i text-slate-500">questions.</span></h2>
            <p class="mt-6 text-slate-600">Something else? <a href="{{ route('contact') }}" class="font-bold text-indigo-700 hover:text-indigo-900 underline underline-offset-4 decoration-indigo-200">Contact us</a>.</p>
        </div>
        <div class="lg:col-span-8 border-t border-slate-200" x-data="{ open: 0 }" data-reveal style="--d: 100ms">
            @foreach([
                ['Is FinanceAI free?', 'Yes. The Starter plan costs ₹0 and needs no card. Paid plans are optional and billed every 30 days.'],
                ['Does it connect to my bank?', 'No. You add income and expenses yourself, so FinanceAI never needs your bank login.'],
                ['How is the health score calculated?', 'It\'s a 0–100 score: 40% savings rate, 30% spending control, 20% how consistent your money is month to month, and 10% emergency runway, where 6 months of runway is the target.'],
                ['How does family sharing work?', 'Create a family and invite people by email. They join with a one-click link and can add to the shared income and spending. Owners manage members and invites.'],
                ['What does the AI advisor see?', 'Your own income, expenses, goals and risk profile, plus current market prices. Its answers are guidance, not regulated financial advice.'],
                ['Can I take my data with me?', 'Yes. Download PDF ledgers of your income and your expenses at any time.'],
            ] as $i => [$q, $a])
                <div class="border-b border-slate-200">
                    <h3>
                        <button type="button" @click="open = open === {{ $i }} ? null : {{ $i }}" :aria-expanded="open === {{ $i }}"
                                class="w-full flex items-center justify-between gap-6 py-6 text-left text-lg font-bold text-slate-900 hover:text-indigo-700 transition-colors focus:outline-none focus-visible:text-indigo-700">
                            <span>{{ $q }}</span>
                            <span class="w-8 h-8 shrink-0 rounded-full border border-slate-200 flex items-center justify-center transition-all duration-300" :class="open === {{ $i }} && 'bg-indigo-600 border-indigo-600 text-white rotate-45'">
                                <i class="fa-solid fa-plus text-xs" aria-hidden="true"></i>
                            </span>
                        </button>
                    </h3>
                    <div x-show="open === {{ $i }}" x-collapse>
                        <p class="pb-6 pr-14 text-slate-600 leading-relaxed">{{ $a }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- =====================================================================
     BUILT ON PROVEN TOOLS — the real stack
     ===================================================================== --}}
<section class="px-4 sm:px-6 lg:px-8 pb-6">
    <div class="relative max-w-7xl mx-auto overflow-hidden rounded-[2rem] bg-[#0b0b1a] text-white grain" data-reveal>
        <div class="relative grid lg:grid-cols-12 gap-10 p-8 sm:p-12 lg:p-16 items-center">
            <div class="lg:col-span-5">
                <p class="font-mono text-xs uppercase tracking-[0.2em] text-indigo-300">Under the hood</p>
                <h2 class="mt-4 text-3xl sm:text-4xl font-black tracking-[-0.03em] leading-[1.08]">
                    Built on tools <span class="font-serif-i text-indigo-300">you can trust.</span>
                </h2>
                <p class="mt-5 text-slate-400 leading-relaxed">No experimental tech holding your money data. FinanceAI runs on proven, widely used software — each piece doing one job well.</p>
            </div>
            <ul class="lg:col-span-7 grid grid-cols-2 sm:grid-cols-3 gap-3" aria-label="Technology used">
                @foreach([
                    ['fa-brands fa-laravel', 'Laravel 8', 'Application'],
                    ['fa-solid fa-database', 'MySQL', 'Your data'],
                    ['fa-solid fa-wand-magic-sparkles', 'Google Gemini', 'AI advisor'],
                    ['fa-solid fa-file-pdf', 'DomPDF', 'PDF ledgers'],
                    ['fa-brands fa-bitcoin', 'CoinGecko', 'Market prices'],
                    ['fa-solid fa-shield-halved', 'bcrypt', 'Password hashing'],
                ] as $k => [$icon, $name, $role])
                    <li class="rounded-2xl border border-white/10 bg-white/[0.03] p-4 sm:p-5 hover:border-indigo-400/40 hover:bg-white/[0.06] transition-colors">
                        <i class="{{ $icon }} text-xl text-indigo-300" aria-hidden="true"></i>
                        <p class="mt-4 font-bold">{{ $name }}</p>
                        <p class="font-mono text-[11px] uppercase tracking-wider text-slate-500 mt-0.5">{{ $role }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

{{-- =====================================================================
     FINAL CTA
     ===================================================================== --}}
<section class="px-4 sm:px-6 lg:px-8 pb-20 sm:pb-28">
    <div class="relative max-w-7xl mx-auto overflow-hidden rounded-[2rem] bg-gradient-to-br from-indigo-600 to-violet-700 px-6 py-16 sm:px-16 sm:py-24" data-reveal>
        <div class="absolute inset-0 opacity-30 pointer-events-none" style="background-image: radial-gradient(rgba(255,255,255,.35) 1px, transparent 1px); background-size: 18px 18px;" aria-hidden="true"></div>
        <div class="relative grid lg:grid-cols-12 gap-10 items-end">
            <h2 class="lg:col-span-8 text-4xl sm:text-6xl font-black tracking-[-0.035em] text-white leading-[1.02]">
                Know where every rupee goes — <span class="font-serif-i text-indigo-200">starting today.</span>
            </h2>
            <div class="lg:col-span-4 flex flex-col gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex justify-center items-center gap-2 h-14 rounded-xl bg-white text-indigo-700 font-bold hover:bg-indigo-50 transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-white/40">
                        Open your dashboard <i class="fa-solid fa-arrow-right text-sm" aria-hidden="true"></i>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex justify-center items-center gap-2 h-14 rounded-xl bg-white text-indigo-700 font-bold hover:bg-indigo-50 transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-white/40">
                        Create a free account <i class="fa-solid fa-arrow-right text-sm" aria-hidden="true"></i>
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex justify-center items-center h-14 rounded-xl border border-white/30 text-white font-bold hover:bg-white/10 transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-white/40">
                        I already have an account
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>

</div>
@endsection

@push('scripts')
<script>
    // Reveal-on-scroll for [data-reveal] blocks and data visuals inside them.
    (function () {
        var els = document.querySelectorAll('[data-reveal]');
        if (!('IntersectionObserver' in window) || matchMedia('(prefers-reduced-motion: reduce)').matches) {
            els.forEach(function (el) { el.classList.add('is-visible'); });
            return;
        }
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); }
            });
        }, { rootMargin: '0px 0px -10% 0px', threshold: 0.1 });
        els.forEach(function (el) { io.observe(el); });
    })();
</script>
@endpush
