@extends('layouts.landing')
@include('partials.site-type')

@section('title', 'Blog | FinanceAI')
@section('meta_description', 'Notes from the FinanceAI team. Until our first article is out, see the changelog for what has shipped.')

@section('content')
<div class="paper">

    <x-site.hero eyebrow="Blog" lead="Notes from the" accent="FinanceAI team."
        lede="We haven't published any articles yet. When we do, you'll find guides on budgeting in rupees, using the AI advisor and running a family budget here." />

    <section class="py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-12 items-start">
            <div class="lg:col-span-7" data-reveal>
                <p class="eyebrow text-indigo-600">In the meantime</p>
                <h2 class="mt-4 text-3xl sm:text-5xl font-black tracking-[-0.03em] leading-[1.05] text-slate-900">See what we've <span class="font-serif-i text-indigo-600">shipped</span></h2>
                <p class="mt-5 text-lg text-slate-600 leading-relaxed max-w-xl">Every new feature and fix is listed in the changelog. If there's a topic you'd like us to write about first, tell us.</p>
                <div class="mt-8 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('changelog') }}" class="group inline-flex items-center justify-center gap-2 h-12 px-6 rounded-xl bg-slate-900 text-white font-bold hover:bg-indigo-600 transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-200">
                        Read the changelog <i class="fa-solid fa-arrow-right text-sm transition-transform group-hover:translate-x-0.5" aria-hidden="true"></i>
                    </a>
                    <a href="{{ route('contact') }}" class="inline-flex items-center justify-center h-12 px-6 rounded-xl border border-slate-300 bg-white font-bold text-slate-800 hover:border-slate-900 transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-100">Suggest a topic</a>
                </div>
            </div>
            <ul class="lg:col-span-5 border-t border-slate-900" data-reveal style="--d: 120ms">
                @foreach(['Budgeting in rupees: a simple monthly routine', 'Getting useful answers from the AI advisor', 'Setting up a shared family budget'] as $i => $topic)
                    <li class="flex items-baseline gap-4 py-5 border-b border-slate-200">
                        <span class="font-mono text-xs text-slate-400">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="text-lg font-bold text-slate-900">{{ $topic }}</span>
                        <span class="ml-auto font-mono text-[10px] uppercase tracking-wider text-slate-400 shrink-0">Planned</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
</div>
@endsection
