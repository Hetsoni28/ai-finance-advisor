@extends('layouts.landing')
@include('partials.site-type')

@section('title', 'Terms of Service | FinanceAI')
@section('meta_description', 'The terms for using FinanceAI: your account, your data, plans, the AI advisor, and our responsibilities.')

@section('content')
@php
    $sections = [
        'agree' => 'Using FinanceAI',
        'account' => 'Your account',
        'use' => 'Acceptable use',
        'data' => 'Your data',
        'plans' => 'Plans',
        'advice' => 'Not financial advice',
        'liability' => 'Our responsibility',
        'ending' => 'Closing an account',
        'law' => 'Changes & governing law',
    ];
@endphp
<div class="paper">

    <x-site.hero eyebrow="Legal" lead="Terms of" accent="Service"
        lede="The rules for using FinanceAI, written to be read. By creating an account or using the site, you agree to them.">
        <x-slot name="actions">
            <button type="button" onclick="window.print()" class="inline-flex items-center justify-center gap-2 h-12 px-6 rounded-xl font-bold text-white border border-white/25 bg-white/5 hover:bg-white/10 transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-white/20">
                <i class="fa-solid fa-print text-sm" aria-hidden="true"></i> Print or save as PDF
            </button>
        </x-slot>
        <x-slot name="meta">
            <p class="font-mono text-xs uppercase tracking-wider text-slate-400">Last updated: <span class="text-slate-200">24 November 2026</span></p>
        </x-slot>
    </x-site.hero>

    <x-site.doc :sections="$sections">
        <section id="agree" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">01</p>
            <h2>Using FinanceAI</h2>
            <p>FinanceAI is a personal and family finance app run by Hetvi Prajapati in India. These terms apply when you visit the site or use an account. You must be at least 18 years old to create an account.</p>
        </section>

        <section id="account" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">02</p>
            <h2>Your account</h2>
            <p>Give accurate details when you sign up and keep your password private. You're responsible for what happens in your account. If you think someone else has access, change your password straight away — this signs out your other devices — and tell us through the <a href="{{ route('contact') }}">contact form</a>.</p>
        </section>

        <section id="use" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">03</p>
            <h2>Acceptable use</h2>
            <p>Please don't:</p>
            <ul class="doc-list">
                <li>try to access other people's accounts or data;</li>
                <li>overload the service, for example with automated scripts or bots;</li>
                <li>use FinanceAI for anything illegal, including fraud or money laundering;</li>
                <li>send abusive or harmful content through the AI advisor, invites or contact form.</li>
            </ul>
            <p>We may suspend accounts that break these rules.</p>
        </section>

        <section id="data" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">04</p>
            <h2>Your data</h2>
            <p>Your financial records belong to you. You allow us to store and process them only to run FinanceAI for you — for example to show reports, calculate your health score and answer your AI advisor questions. Our <a href="{{ route('privacy') }}">privacy policy</a> explains this in detail.</p>
        </section>

        <section id="plans" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">05</p>
            <h2>Plans</h2>
            <p>Starter is free. Paid plans are listed on the <a href="{{ route('pricing') }}">pricing page</a> and mainly raise the daily AI advisor limit and add live market prices. You can switch plans or go back to Starter at any time from Plan &amp; Billing. We may change plans or prices in future; we'll show any change on the pricing page before it applies to you.</p>
        </section>

        <section id="advice" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">06</p>
            <h2>Not financial advice</h2>
            <div class="mt-5 bg-amber-50 border-l-2 border-amber-500 px-6 py-5">
                <p>FinanceAI is software, not a bank, broker or registered financial adviser. Reports, the health score and AI advisor answers are for <strong>general information only</strong> and can be wrong. They are not financial, tax, legal or investment advice. Check important decisions with a qualified professional.</p>
            </div>
        </section>

        <section id="liability" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">07</p>
            <h2>Our responsibility</h2>
            <p>We work to keep FinanceAI available, accurate and secure, but we can't promise it will always be uninterrupted or error-free. As far as the law allows, we aren't responsible for indirect losses, or for decisions you make based on information in the app.</p>
        </section>

        <section id="ending" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">08</p>
            <h2>Closing an account</h2>
            <p>You can stop using FinanceAI at any time and ask us to delete your account through the <a href="{{ route('contact') }}">contact form</a>. We may suspend or close accounts that break these terms.</p>
        </section>

        <section id="law" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">09</p>
            <h2>Changes &amp; governing law</h2>
            <p>We may update these terms; the date at the top shows the latest version, and continuing to use FinanceAI after a change means you accept it. These terms are governed by the laws of India.</p>
        </section>
    </x-site.doc>
</div>
@endsection
