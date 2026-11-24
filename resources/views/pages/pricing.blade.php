@extends('layouts.landing')
@include('partials.site-type')

@section('title', 'Pricing | FinanceAI')
@section('meta_description', 'Simple monthly plans in rupees. Start free, upgrade for the AI advisor and family sharing, change or cancel any time.')

@section('content')
@php
    /** @var \Illuminate\Support\Collection $plans */
    $highlightId = optional($plans->firstWhere('slug', 'pro') ?? $plans->sortByDesc('price')->skip(1)->first())->id;
    $paidFrom = $plans->where('price', '>', 0)->min('price');
    $ctaUrl = auth()->check() ? route('user.subscription.index') : route('register');
    $faqs = [
        ['Is there a free plan?', 'Yes. Starter is free with no time limit, and you don\'t need a card to sign up.'],
        ['Can I change or cancel my plan later?', 'Yes. Open Plan & Billing in your account to switch plans or go back to Starter at any time.'],
        ['How does the AI advisor work?', 'It answers using your own income, expenses, goals and risk profile. It uses Google Gemini when available and built-in rules otherwise, and each plan has a daily message limit.'],
        ['Who can see my data?', 'Only you, plus the family members you choose to share a family budget with. Passwords are stored hashed and the site is served over HTTPS. We don\'t sell your data.'],
        ['Can I export my records?', 'Yes. Your income and expense lists can each be downloaded as a PDF from their pages.'],
    ];
@endphp
<div class="paper">

    <x-site.hero eyebrow="Pricing" lead="Simple plans," accent="billed monthly."
        lede="Start free. Upgrade when you want the AI advisor and family sharing. Change or cancel any time from Plan & Billing.">
        <x-slot name="meta">
            <dl class="flex flex-wrap gap-x-10 gap-y-3 font-mono text-xs uppercase tracking-wider">
                <div class="flex gap-2"><dt class="text-slate-400">Plans:</dt><dd class="text-slate-200">{{ $plans->count() }}</dd></div>
                @if($paidFrom)
                    <div class="flex gap-2"><dt class="text-slate-400">Paid from:</dt><dd class="text-slate-200">₹{{ number_format($paidFrom) }}/mo</dd></div>
                @endif
                <div class="flex gap-2"><dt class="text-slate-400">Card to start:</dt><dd class="text-slate-200">Not needed</dd></div>
            </dl>
        </x-slot>
    </x-site.hero>

    {{-- Plans (from the database) --}}
    <section class="py-20 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($plans->isEmpty())
                <p class="text-center text-slate-500 py-16">Plans are being updated. Please check back shortly.</p>
            @else
                <div class="grid gap-5 items-stretch {{ $plans->count() >= 3 ? 'lg:grid-cols-3' : 'md:grid-cols-2 max-w-4xl mx-auto' }}">
                    @foreach($plans as $idx => $plan)
                        @php $hl = $plan->id === $highlightId; $free = (float) $plan->price <= 0; @endphp
                        <article class="relative flex flex-col rounded-2xl p-8 sm:p-9 {{ $hl ? 'bg-[#0b0b1a] text-white grain lg:-my-4 lg:py-12' : 'card-site' }}" data-reveal style="--d: {{ $idx * 90 }}ms">
                            <div class="flex items-center justify-between">
                                <p class="eyebrow {{ $hl ? 'text-indigo-300' : 'text-slate-400' }}">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</p>
                                @if($hl)
                                    <span class="font-mono text-[10px] uppercase tracking-wider text-indigo-300">Most popular</span>
                                @endif
                            </div>

                            <h2 class="mt-6 text-3xl font-black tracking-tight {{ $hl ? 'text-white' : 'text-slate-900' }}">{{ $plan->name }}</h2>

                            <div class="mt-8 pb-8 border-b {{ $hl ? 'border-white/10' : 'border-slate-200' }}">
                                @if($free)
                                    <p class="font-serif-i text-6xl {{ $hl ? 'text-white' : 'text-slate-900' }}">Free</p>
                                    <p class="mt-3 text-sm {{ $hl ? 'text-slate-400' : 'text-slate-500' }}">No time limit. No card needed.</p>
                                @else
                                    <p class="flex items-end gap-1">
                                        <span class="text-2xl font-bold mb-1.5 text-slate-400">₹</span>
                                        <span class="text-6xl font-black tracking-tighter tabular-nums {{ $hl ? 'text-white' : 'text-slate-900' }}">{{ number_format((float) $plan->price) }}</span>
                                        <span class="text-sm font-semibold mb-2 text-slate-400">/month</span>
                                    </p>
                                    <p class="mt-3 text-sm {{ $hl ? 'text-slate-400' : 'text-slate-500' }}">Renews every {{ $plan->duration_days ?: 30 }} days. Cancel any time.</p>
                                @endif
                            </div>

                            <ul class="mt-8 flex-1 space-y-3.5">
                                @foreach((array) $plan->features as $feature)
                                    <li class="flex gap-3 text-[15px] {{ $hl ? 'text-slate-200' : 'text-slate-700' }}"><i class="fa-solid fa-check mt-1 text-xs {{ $hl ? 'text-indigo-300' : 'text-emerald-600' }}" aria-hidden="true"></i>{{ $feature }}</li>
                                @endforeach
                            </ul>

                            <a href="{{ $ctaUrl }}"
                               class="mt-10 inline-flex items-center justify-center gap-2 h-12 rounded-xl font-bold transition-colors focus:outline-none focus-visible:ring-4
                                      {{ $hl ? 'text-white bg-gradient-to-br from-indigo-500 to-violet-600 hover:from-indigo-400 focus-visible:ring-indigo-400/40' : 'bg-slate-900 text-white hover:bg-indigo-600 focus-visible:ring-indigo-200' }}">
                                @auth
                                    Choose {{ $plan->name }}
                                @else
                                    {{ $free ? 'Start free' : 'Get ' . $plan->name }}
                                @endauth
                                <i class="fa-solid fa-arrow-right text-sm" aria-hidden="true"></i>
                            </a>
                        </article>
                    @endforeach
                </div>
                <p class="mt-10 text-center text-sm text-slate-500">Prices in Indian rupees. Switching plans takes effect straight away from Plan &amp; Billing.</p>
            @endif
        </div>
    </section>

    {{-- FAQ --}}
    <section class="py-20 sm:py-28 bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-12">
            <div class="lg:col-span-4">
                <x-site.section-head label="FAQ" title="Common" accent="questions" />
                <p class="mt-6 text-slate-600" data-reveal>Something else? <a href="{{ route('contact') }}" class="font-semibold text-indigo-600 underline underline-offset-2">Send us a message</a>.</p>
            </div>

            <div class="lg:col-span-8 border-t border-slate-900" x-data="{ open: 0 }" data-reveal style="--d: 100ms">
                @foreach($faqs as $i => [$q, $a])
                    <div class="border-b border-slate-200">
                        <button type="button" @click="open = open === {{ $i }} ? null : {{ $i }}" :aria-expanded="(open === {{ $i }}).toString()"
                                class="w-full flex items-center justify-between gap-4 py-5 text-left focus:outline-none focus-visible:bg-slate-50">
                            <span class="text-lg font-bold text-slate-900">{{ $q }}</span>
                            <i class="fa-solid fa-plus text-sm text-slate-400 transition-transform duration-300" :class="open === {{ $i }} ? 'rotate-45 text-slate-900' : ''" aria-hidden="true"></i>
                        </button>
                        <div x-show="open === {{ $i }}" x-collapse {{ $i === 0 ? '' : 'x-cloak' }}>
                            <p class="pb-6 pr-10 text-[15px] text-slate-600 leading-relaxed">{{ $a }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
@endsection
