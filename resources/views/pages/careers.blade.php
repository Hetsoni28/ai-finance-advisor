@extends('layouts.landing')
@include('partials.site-type')

@section('title', 'Careers | FinanceAI')
@section('meta_description', 'There are no open roles at FinanceAI right now. You can still send us a message about working together.')

@section('content')
<div class="paper">

    <x-site.hero eyebrow="Careers" lead="Want to build" accent="FinanceAI with us?"
        lede="We're a small team and we don't have any open roles right now. If you'd like to be considered when that changes, send us a message." />

    <section class="py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-12 items-start">
            <div class="lg:col-span-7" data-reveal>
                <p class="eyebrow text-indigo-600">Open roles</p>
                <div class="mt-6 border-t border-b border-slate-900 py-10">
                    <p class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900">No open roles <span class="font-serif-i text-indigo-600">right now</span></p>
                    <p class="mt-4 text-lg text-slate-600 leading-relaxed max-w-xl">We'll list positions on this page as soon as we're hiring.</p>
                </div>
            </div>
            <div class="lg:col-span-5" data-reveal style="--d: 120ms">
                <p class="eyebrow text-indigo-600">Get in touch anyway</p>
                <p class="mt-6 text-lg text-slate-600 leading-relaxed">Tell us who you are, what you'd like to work on, and include a link to your CV or portfolio. We read every message.</p>
                <a href="{{ route('contact') }}" class="group mt-8 inline-flex items-center gap-2 h-12 px-6 rounded-xl bg-slate-900 text-white font-bold hover:bg-indigo-600 transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-200">
                    Send a message <i class="fa-solid fa-arrow-right text-sm transition-transform group-hover:translate-x-0.5" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
