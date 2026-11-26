@extends('layouts.landing')
@include('partials.site-type')

@section('title', 'About | FinanceAI')
@section('meta_description', 'Why we built FinanceAI: a simple way to track money in rupees, share a family budget and get answers from an AI advisor that knows your numbers.')

@section('content')
@php
    $principles = [
        ['fa-receipt', 'Your numbers, not guesses', 'Every chart, score and AI answer is worked out from the income and expenses you record — nothing is invented to fill the screen.'],
        ['fa-language', 'Plain language', 'No jargon. Clear labels, clear amounts in rupees, and messages that tell you what happened.'],
        ['fa-user-lock', 'Private by default', 'Your records are visible only to you. Passwords are stored hashed and the whole site runs over HTTPS.'],
        ['fa-people-roof', 'Share on your terms', 'Invite family members to a shared budget when you want to. Personal entries stay personal.'],
    ];
    $team = [
        ['F', 'Founder & CEO', 'Vision & Strategy', 'Directing the product and defining how the AI advisor fits into everyday money decisions.'],
        ['T', 'Head of Technology', 'Infrastructure', 'Keeping the platform fast, secure and reliable.'],
        ['G', 'Head of Growth', 'Users & Partnerships', 'Bringing FinanceAI to more households and listening to what they need next.'],
    ];
    $fmt = fn ($n) => inr_number((int) $n);
@endphp
<div class="paper">

    <x-site.hero eyebrow="About FinanceAI" lead="We are building the future of" accent="financial intelligence."
        lede="FinanceAI helps people and families track every rupee, see where their money goes, and get answers from an AI advisor that has actually seen their numbers.">
        <x-slot name="meta">
            <dl class="grid grid-cols-3 max-w-xl">
                <div>
                    <dt class="font-mono text-[11px] uppercase tracking-wider text-slate-400">Accounts</dt>
                    <dd class="mt-1 text-xl sm:text-2xl font-black text-white tabular-nums">{{ $fmt($stats['accounts']) }}</dd>
                </div>
                <div>
                    <dt class="font-mono text-[11px] uppercase tracking-wider text-slate-400">Entries recorded</dt>
                    <dd class="mt-1 text-xl sm:text-2xl font-black text-white tabular-nums">{{ $fmt($stats['entries']) }}</dd>
                </div>
                <div>
                    <dt class="font-mono text-[11px] uppercase tracking-wider text-slate-400">AI answers given</dt>
                    <dd class="mt-1 text-xl sm:text-2xl font-black text-white tabular-nums">{{ $fmt($stats['answers']) }}</dd>
                </div>
            </dl>
            <p class="mt-4 font-mono text-[10px] uppercase tracking-wider text-slate-500">Live counts from our database · updated every 10 minutes</p>
        </x-slot>
    </x-site.hero>

    {{-- Mission & vision --}}
    <section class="py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 border-t border-slate-900">
                <article class="py-10 lg:pr-12 lg:border-r border-slate-200" data-reveal>
                    <p class="eyebrow text-indigo-600">01 — Mission</p>
                    <h2 class="mt-4 text-3xl sm:text-4xl font-black tracking-[-0.03em] text-slate-900">Why we <span class="font-serif-i text-indigo-600">exist</span></h2>
                    <p class="mt-5 text-lg text-slate-600 leading-relaxed">To make everyday money simple: record what comes in and goes out, see it clearly, and get practical guidance — without spreadsheets or financial jargon.</p>
                </article>
                <article class="py-10 lg:pl-12 border-t lg:border-t-0 border-slate-200" data-reveal style="--d: 100ms">
                    <p class="eyebrow text-indigo-600">02 — Vision</p>
                    <h2 class="mt-4 text-3xl sm:text-4xl font-black tracking-[-0.03em] text-slate-900">Where we're <span class="font-serif-i text-indigo-600">headed</span></h2>
                    <p class="mt-5 text-lg text-slate-600 leading-relaxed">A world where every household makes money decisions with confidence — together, with clear numbers and an advisor that understands their situation.</p>
                </article>
            </div>

            <div class="mt-6 bg-[#0b0b1a] text-white grain rounded-2xl p-8 sm:p-12 grid lg:grid-cols-12 gap-6 items-center" data-reveal>
                <div class="lg:col-span-4">
                    <p class="eyebrow text-indigo-300 flex items-center gap-2"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Privacy</p>
                    <h2 class="mt-3 text-2xl sm:text-3xl font-black tracking-tight">Your data stays yours</h2>
                </div>
                <p class="lg:col-span-8 text-lg text-slate-300 leading-relaxed">We believe financial privacy is a basic right. Your records are visible only to you and the family members you invite, passwords are stored hashed, the site runs over HTTPS, and we never sell your data. Read the details in our <a href="{{ route('privacy') }}" class="text-white underline underline-offset-4 decoration-white/40 hover:decoration-white">privacy policy</a>.</p>
            </div>
        </div>
    </section>

    {{-- Principles --}}
    <section class="py-20 sm:py-28 bg-white border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-site.section-head label="How we build" title="What we" accent="care about"
                lede="The rules we follow every time we add something to FinanceAI." />

            <div class="mt-14 grid sm:grid-cols-2 lg:grid-cols-4 border-t border-l border-slate-200">
                @foreach($principles as $i => [$icon, $title, $desc])
                    <article class="p-7 border-b border-r border-slate-200" data-reveal style="--d: {{ $i * 70 }}ms">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-sm text-slate-400">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <i class="fa-solid {{ $icon }} text-slate-300" aria-hidden="true"></i>
                        </div>
                        <h3 class="mt-10 text-lg font-black tracking-tight text-slate-900">{{ $title }}</h3>
                        <p class="mt-2 text-[15px] text-slate-600 leading-relaxed">{{ $desc }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Team --}}
    <section class="py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-site.section-head label="Team" title="The people" accent="behind it"
                lede="A small team that cares about finance, design and reliable software." />

            <div class="mt-14 grid md:grid-cols-3 gap-6">
                @foreach($team as $i => [$initial, $role, $area, $desc])
                    <article class="card-site p-7 flex flex-col" data-reveal style="--d: {{ $i * 80 }}ms">
                        <span class="h-16 w-16 rounded-2xl bg-[#0b0b1a] text-white flex items-center justify-center font-serif-i text-4xl">{{ $initial }}</span>
                        <h3 class="mt-8 text-2xl font-black tracking-tight text-slate-900">{{ $role }}</h3>
                        <p class="mt-1 eyebrow text-indigo-600">{{ $area }}</p>
                        <p class="mt-4 text-[15px] text-slate-600 leading-relaxed">{{ $desc }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Roadmap --}}
    <section class="py-20 sm:py-28 bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-site.section-head label="Roadmap" title="What's" accent="next"
                lede="Where FinanceAI is today and what we're working towards." />

            <ol class="mt-14 grid md:grid-cols-3 border-t border-slate-200">
                @foreach([
                    ['Phase 01', 'Foundations', 'Income and expense tracking, categories, reports, PDF exports, and secure accounts.', 'Done', 'done'],
                    ['Phase 02', 'AI advisor & families', 'An AI advisor that reads your own numbers, a financial health score, and shared family budgets.', 'Live now', 'now'],
                    ['Phase 03', 'Bank sync', 'Connecting bank accounts so entries can be imported automatically instead of typed in.', 'Planned', 'next'],
                ] as $i => [$phase, $title, $desc, $status, $state])
                    <li class="relative pt-8 pb-4 md:pr-8 {{ $state === 'next' ? 'opacity-60' : '' }}" data-reveal style="--d: {{ $i * 90 }}ms">
                        <span class="absolute top-0 left-0 h-0.5 {{ $state === 'done' ? 'w-full bg-slate-900' : ($state === 'now' ? 'w-1/2 bg-indigo-600' : 'w-0') }}" aria-hidden="true"></span>
                        <p class="font-mono text-xs uppercase tracking-wider text-slate-400">{{ $phase }}</p>
                        <h3 class="mt-3 text-2xl font-black tracking-tight text-slate-900">{{ $title }}</h3>
                        <p class="mt-3 text-[15px] text-slate-600 leading-relaxed">{{ $desc }}</p>
                        <p class="mt-5 font-mono text-[11px] uppercase tracking-wider {{ $state === 'done' ? 'text-slate-900' : ($state === 'now' ? 'text-indigo-600' : 'text-slate-400') }}">{{ $status }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-20 sm:py-28 bg-[#0b0b1a] text-white grain">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-10 items-end">
            <div class="lg:col-span-8" data-reveal>
                <h2 class="text-4xl sm:text-6xl font-black tracking-[-0.035em] leading-[1.02]">Ready to see your money <span class="font-serif-i text-indigo-300">clearly?</span></h2>
                <p class="mt-6 text-lg text-slate-300 max-w-2xl">Create a free account in a minute. No card needed.</p>
            </div>
            <div class="lg:col-span-4 lg:text-right" data-reveal style="--d: 120ms">
                <a href="{{ auth()->check() ? route('dashboard') : route('register') }}" class="group inline-flex items-center gap-2 h-12 px-6 rounded-xl text-white font-bold bg-gradient-to-br from-indigo-500 to-violet-600 shadow-[0_10px_30px_-8px_rgba(99,102,241,.7)] hover:-translate-y-0.5 transition-transform focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-400/40">
                    {{ auth()->check() ? 'Open your dashboard' : 'Create a free account' }} <i class="fa-solid fa-arrow-right text-sm transition-transform group-hover:translate-x-0.5" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
