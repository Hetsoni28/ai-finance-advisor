@extends('layouts.landing')
@include('partials.site-type')

@section('title', 'Features | FinanceAI')
@section('meta_description', 'Track income and expenses in rupees, see monthly reports and a financial health score, share a family budget, and ask an AI advisor that knows your numbers.')

@section('content')
@php
    $tabs = [
        'track' => ['Track every rupee', 'Add income and expenses in seconds, pick a category, and mark each entry as personal or shared with your family.'],
        'family' => ['Budget together', 'Create a family, invite people by email, and see shared spending, members and activity in one place.'],
        'export' => ['Take it with you', 'Download your income and expense lists as PDF whenever you need them — for your records, a CA or a loan application.'],
    ];
    $ctaUrl = auth()->check() ? route('dashboard') : route('register');
@endphp
<div class="paper" x-data="{ activeTab: 'track' }">

    <x-site.hero eyebrow="Features" lead="Everything you need for" accent="financial clarity."
        lede="FinanceAI keeps your income, spending and savings in one place, turns them into clear reports and a health score, and lets you ask an AI advisor that has actually seen your numbers.">
        <x-slot name="actions">
            <a href="{{ $ctaUrl }}" class="group inline-flex items-center justify-center gap-2 h-12 px-6 rounded-xl text-white font-bold bg-gradient-to-br from-indigo-500 to-violet-600 shadow-[0_10px_30px_-8px_rgba(99,102,241,.7)] hover:-translate-y-0.5 transition-transform focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-400/40">
                {{ auth()->check() ? 'Open your dashboard' : 'Create a free account' }} <i class="fa-solid fa-arrow-right text-sm transition-transform group-hover:translate-x-0.5" aria-hidden="true"></i>
            </a>
            <a href="{{ route('pricing') }}" class="inline-flex items-center justify-center h-12 px-6 rounded-xl font-bold text-white border border-white/25 bg-white/5 hover:bg-white/10 transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-white/20">See pricing</a>
        </x-slot>
        <x-slot name="aside">
            <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-6" aria-hidden="true">
                <div class="flex items-center justify-between">
                    <span class="font-mono text-[11px] uppercase tracking-wider text-slate-400">Last 6 months</span>
                    <span class="font-mono text-[10px] uppercase tracking-wider text-slate-500">Example</span>
                </div>
                <div class="mt-6 flex items-end gap-3 h-40 border-b border-white/10">
                    @foreach([40, 70, 90, 60, 75, 52] as $i => $h)
                        <div class="flex-1 rounded-t {{ $i === 2 ? 'bg-indigo-400' : 'bg-white/15' }}" style="height: {{ $h }}%"></div>
                    @endforeach
                </div>
                <div class="mt-5 flex items-end justify-between">
                    <div>
                        <p class="font-mono text-[11px] uppercase tracking-wider text-slate-400">Kept this month</p>
                        <p class="mt-1 text-3xl font-black text-white">30%</p>
                    </div>
                    <i class="fa-solid fa-chart-column text-2xl text-slate-600"></i>
                </div>
            </div>
        </x-slot>
    </x-site.hero>

    {{-- Before / after --}}
    <section class="py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-site.section-head label="01 — Why switch" title="Better than a" accent="spreadsheet"
                lede="Spreadsheets work until they don't. FinanceAI does the adding up, the charts and the explaining for you." />

            <div class="mt-14 grid md:grid-cols-2 border-t border-slate-900">
                <div class="py-10 md:pr-12 md:border-r border-slate-200" data-reveal>
                    <p class="eyebrow text-slate-400">Spreadsheets &amp; notes</p>
                    <ul class="mt-6 space-y-7">
                        @foreach([
                            ['Manual totals', 'Formulas break, rows get missed, and month-end totals take an evening to check.'],
                            ['No clear picture', 'You find out you overspent only after the month is over.'],
                            ['Hard to share', 'Family members keep separate notes, so nobody sees the whole household.'],
                        ] as [$t, $d])
                            <li class="flex gap-4">
                                <i class="fa-solid fa-xmark mt-1.5 text-sm text-slate-300" aria-hidden="true"></i>
                                <div>
                                    <h3 class="font-bold text-slate-500 line-through decoration-slate-300">{{ $t }}</h3>
                                    <p class="mt-1 text-[15px] text-slate-500 leading-relaxed">{{ $d }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="py-10 md:pl-12 border-t md:border-t-0 border-slate-200" data-reveal style="--d: 100ms">
                    <p class="eyebrow text-indigo-600">With FinanceAI</p>
                    <ul class="mt-6 space-y-7">
                        @foreach([
                            ['Totals that add themselves', 'Every entry updates your monthly totals, categories and charts straight away.'],
                            ['See it as it happens', 'A health score and plain-language insights show how the month is going while there\'s still time to act.'],
                            ['One shared view', 'Invite family members to a shared budget and see household spending together.'],
                        ] as [$t, $d])
                            <li class="flex gap-4">
                                <i class="fa-solid fa-check mt-1.5 text-sm text-emerald-600" aria-hidden="true"></i>
                                <div>
                                    <h3 class="font-black text-slate-900">{{ $t }}</h3>
                                    <p class="mt-1 text-[15px] text-slate-600 leading-relaxed">{{ $d }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Core capabilities --}}
    <section class="py-20 sm:py-28 bg-white border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-site.section-head label="02 — What's inside" title="Built around your" accent="money"
                lede="Every screen works from the entries you record — nothing is estimated or made up." />

            <div class="mt-14 grid md:grid-cols-3 gap-5">
                <article class="md:col-span-2 card-site p-8 sm:p-10 grid sm:grid-cols-2 gap-8 items-center" data-reveal>
                    <div>
                        <i class="fa-solid fa-wallet text-slate-400" aria-hidden="true"></i>
                        <h3 class="mt-6 text-2xl sm:text-3xl font-black tracking-tight text-slate-900">Income &amp; expenses</h3>
                        <p class="mt-3 text-slate-600 leading-relaxed">Record what comes in and goes out with a date, amount and category. Search, filter and edit any entry later.</p>
                    </div>
                    <ul class="border-t border-slate-200 text-sm text-slate-600" aria-hidden="true">
                        @foreach([['Food', '₹6,200'], ['Bills', '₹4,850'], ['Travel', '₹2,300']] as [$cat, $amt])
                            <li class="flex items-center justify-between py-3 border-b border-slate-200"><span>{{ $cat }}</span><span class="font-mono text-slate-900">{{ $amt }}</span></li>
                        @endforeach
                        <li class="pt-2 font-mono text-[10px] uppercase tracking-wider text-slate-400">Example</li>
                    </ul>
                </article>

                <article class="bg-[#0b0b1a] text-white grain rounded-2xl p-8 sm:p-10 flex flex-col justify-between min-h-[260px]" data-reveal style="--d: 80ms">
                    <i class="fa-solid fa-robot text-slate-500" aria-hidden="true"></i>
                    <div>
                        <h3 class="text-2xl font-black tracking-tight">AI advisor</h3>
                        <p class="mt-3 text-slate-400 leading-relaxed">Ask about your spending, savings or goals. Answers use your own records, goals and risk profile.</p>
                    </div>
                </article>

                <article class="card-site p-8 sm:p-10 flex flex-col justify-between min-h-[260px]" data-reveal>
                    <i class="fa-solid fa-people-roof text-slate-400" aria-hidden="true"></i>
                    <div>
                        <h3 class="text-2xl font-black tracking-tight text-slate-900">Family budgets</h3>
                        <p class="mt-3 text-slate-600 leading-relaxed">Invite family members by email and track shared spending together.</p>
                    </div>
                </article>

                <article class="md:col-span-2 card-site p-8 sm:p-10 grid sm:grid-cols-2 gap-8 items-center" data-reveal style="--d: 80ms">
                    <div>
                        <i class="fa-solid fa-heart-pulse text-slate-400" aria-hidden="true"></i>
                        <h3 class="mt-6 text-2xl sm:text-3xl font-black tracking-tight text-slate-900">Health score</h3>
                        <p class="mt-3 text-slate-600 leading-relaxed">A score out of 100 based on how much you save (40%), how well you control spending (30%), how steady your income is (20%) and how many months your savings would last (10%).</p>
                    </div>
                    <dl class="border-t border-slate-200 text-sm" aria-hidden="true">
                        @foreach([['Saving rate', 40], ['Spending control', 30], ['Income steadiness', 20], ['Emergency runway', 10]] as [$k, $w])
                            <div class="py-3 border-b border-slate-200">
                                <div class="flex justify-between"><dt class="text-slate-600">{{ $k }}</dt><dd class="font-mono text-slate-900">{{ $w }}%</dd></div>
                                <div class="mt-2 h-1 bg-slate-100 rounded-full"><div class="h-full rounded-full bg-indigo-500" style="width: {{ $w * 2.5 }}%"></div></div>
                            </div>
                        @endforeach
                    </dl>
                </article>
            </div>
        </div>
    </section>

    {{-- More tools --}}
    <section class="py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl" data-reveal>
                <p class="eyebrow text-indigo-600">03 — Everyday tools</p>
                <h2 class="mt-4 text-3xl sm:text-5xl font-black tracking-[-0.03em] leading-[1.05] text-slate-900">Simple to use. <span class="font-serif-i text-indigo-600">Easy to share.</span></h2>
            </div>

            <div class="mt-14 grid lg:grid-cols-12 gap-10 items-start">
                <div class="lg:col-span-5 border-t border-slate-200" role="tablist" aria-label="Tools" data-reveal>
                    @foreach($tabs as $key => [$title, $desc])
                        <button type="button" role="tab" @click="activeTab = '{{ $key }}'" :aria-selected="activeTab === '{{ $key }}'"
                                class="relative w-full text-left py-6 pl-6 border-b border-slate-200 focus:outline-none focus-visible:bg-slate-50">
                            <span class="absolute left-0 top-6 bottom-6 w-0.5 transition-colors" :class="activeTab === '{{ $key }}' ? 'bg-indigo-600' : 'bg-transparent'" aria-hidden="true"></span>
                            <span class="block text-xl font-black tracking-tight transition-colors" :class="activeTab === '{{ $key }}' ? 'text-slate-900' : 'text-slate-400'">{{ $title }}</span>
                            <span class="mt-2 block text-[15px] leading-relaxed transition-colors" :class="activeTab === '{{ $key }}' ? 'text-slate-600' : 'text-slate-400'">{{ $desc }}</span>
                        </button>
                    @endforeach
                </div>

                <div class="lg:col-span-7" data-reveal style="--d: 120ms">
                    <div class="h-[400px] bg-[#0b0b1a] rounded-2xl border border-slate-800 overflow-hidden flex flex-col" aria-hidden="true">
                        <div class="h-12 border-b border-white/10 flex items-center justify-between px-5 shrink-0">
                            <span class="font-mono text-[11px] text-slate-500">FinanceAI</span>
                            <span class="font-mono text-[10px] uppercase tracking-wider text-slate-600">Example</span>
                        </div>
                        <div class="flex-1 relative p-7">
                            <div x-show="activeTab === 'track'" x-transition.opacity.duration.300ms class="absolute inset-7">
                                @foreach([['Salary', 'Income', '+₹65,000', 'text-emerald-400'], ['Groceries', 'Food', '−₹3,200', 'text-rose-300'], ['Electricity', 'Bills', '−₹1,850', 'text-rose-300'], ['Metro card', 'Travel', '−₹500', 'text-rose-300']] as [$t, $c, $a, $tone])
                                    <div class="flex items-center justify-between py-3.5 border-b border-white/10">
                                        <div><p class="font-bold text-white">{{ $t }}</p><p class="font-mono text-xs text-slate-500">{{ $c }}</p></div>
                                        <span class="font-mono {{ $tone }}">{{ $a }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div x-show="activeTab === 'family'" x-cloak x-transition.opacity.duration.300ms class="absolute inset-7">
                                <p class="font-mono text-[11px] uppercase tracking-wider text-slate-500">Sharma household · 3 members</p>
                                @foreach([['You', 'Owner'], ['Priya', 'Member'], ['Arjun', 'Invite sent']] as [$n, $r])
                                    <div class="flex items-center justify-between py-3.5 border-b border-white/10">
                                        <span class="flex items-center gap-3"><span class="h-8 w-8 rounded-full bg-white/10 flex items-center justify-center text-xs font-bold text-white">{{ $n[0] }}</span><span class="font-bold text-white">{{ $n }}</span></span>
                                        <span class="font-mono text-xs text-slate-400">{{ $r }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div x-show="activeTab === 'export'" x-cloak x-transition.opacity.duration.300ms class="absolute inset-7 flex flex-col justify-center">
                                <i class="fa-solid fa-file-pdf text-3xl text-slate-500"></i>
                                <p class="mt-6 text-xl font-black text-white">Expenses — November</p>
                                <p class="mt-2 text-sm text-slate-400 max-w-sm">All entries with dates, categories and totals, ready to download as a PDF.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Insights & goals --}}
    <section class="py-20 sm:py-28 bg-[#0b0b1a] text-white grain">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-site.section-head dark label="04 — Stay on track" title="Insights, goals and" accent="reports"
                lede="FinanceAI keeps an eye on the numbers so you don't have to." />

            <div class="mt-14 grid sm:grid-cols-2 lg:grid-cols-4 border-t border-l border-white/10">
                @foreach([
                    ['fa-bell', 'Insights', 'Plain-language notes when spending jumps, savings drop or a category grows.'],
                    ['fa-bullseye', 'Goals', 'Set targets like an emergency fund or a trip and track progress.'],
                    ['fa-chart-pie', 'Reports', 'Compare any period with the one before, by month and by category.'],
                    ['fa-shield-halved', 'Private', 'Only you — and the family members you invite — can see your records.'],
                ] as $i => [$icon, $t, $d])
                    <article class="p-7 border-b border-r border-white/10" data-reveal style="--d: {{ $i * 70 }}ms">
                        <i class="fa-solid {{ $icon }} text-slate-500" aria-hidden="true"></i>
                        <h3 class="mt-8 text-lg font-black tracking-tight">{{ $t }}</h3>
                        <p class="mt-2 text-[15px] text-slate-400 leading-relaxed">{{ $d }}</p>
                    </article>
                @endforeach
            </div>

            <div class="mt-14 flex flex-col sm:flex-row gap-3" data-reveal>
                <a href="{{ $ctaUrl }}" class="group inline-flex items-center justify-center gap-2 h-12 px-6 rounded-xl bg-white text-slate-900 font-bold hover:bg-indigo-100 transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-white/30">
                    {{ auth()->check() ? 'Open your dashboard' : 'Start free' }} <i class="fa-solid fa-arrow-right text-sm transition-transform group-hover:translate-x-0.5" aria-hidden="true"></i>
                </a>
                <a href="{{ route('pricing') }}" class="inline-flex items-center justify-center h-12 px-6 rounded-xl font-bold text-white border border-white/25 hover:bg-white/10 transition-colors">Compare plans</a>
            </div>
        </div>
    </section>
</div>
@endsection
