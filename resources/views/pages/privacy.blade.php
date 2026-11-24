@extends('layouts.landing')
@include('partials.site-type')

@section('title', 'Privacy Policy | FinanceAI')
@section('meta_description', 'What FinanceAI collects, why, who it is shared with, and how to access or delete your data.')

@section('content')
@php
    $sections = [
        'who' => 'Who we are',
        'collect' => 'What we collect',
        'use' => 'How we use it',
        'share' => 'Who we share it with',
        'cookies' => 'Cookies',
        'keep' => 'How long we keep it',
        'rights' => 'Your choices',
        'contact' => 'Contact',
    ];
@endphp
<div class="paper">

    <x-site.hero eyebrow="Legal" lead="Privacy" accent="Policy"
        lede="What we collect when you use FinanceAI, why we need it, and what you can do about it — in plain language.">
        <x-slot name="actions">
            <button type="button" onclick="window.print()" class="inline-flex items-center justify-center gap-2 h-12 px-6 rounded-xl font-bold text-white border border-white/25 bg-white/5 hover:bg-white/10 transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-white/20">
                <i class="fa-solid fa-print text-sm" aria-hidden="true"></i> Print or save as PDF
            </button>
        </x-slot>
        <x-slot name="meta">
            <p class="font-mono text-xs uppercase tracking-wider text-slate-400">Last updated: <span class="text-slate-200">24 November 2026</span></p>
        </x-slot>
    </x-site.hero>

    <section class="border-b border-slate-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-200">
            @foreach([
                ['fa-ban', 'We don\'t sell your data', 'Your financial records are never sold or shared with advertisers.'],
                ['fa-user-lock', 'Private by default', 'Only you can see your records, plus family members you choose to share with.'],
                ['fa-trash-can', 'You stay in control', 'Edit or delete any entry at any time, and ask us to delete your account.'],
            ] as $i => [$icon, $title, $desc])
                <div class="py-10 md:px-8 first:md:pl-0 last:md:pr-0" data-reveal style="--d: {{ $i * 80 }}ms">
                    <p class="eyebrow text-slate-400 flex items-center gap-2"><i class="fa-solid {{ $icon }} text-indigo-500" aria-hidden="true"></i> {{ $title }}</p>
                    <p class="mt-4 text-slate-700 leading-relaxed">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <x-site.doc :sections="$sections">
        <section id="who" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">01</p>
            <h2>Who we are</h2>
            <p>FinanceAI is a personal and family finance app run by Hetvi Prajapati in India. In this policy, "we" means FinanceAI and "you" means anyone who visits the site or has an account.</p>
        </section>

        <section id="collect" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">02</p>
            <h2>What we collect</h2>
            <ul class="doc-list">
                <li><strong>Account details:</strong> your name, email address and password (stored only as a hash).</li>
                <li><strong>Your financial records:</strong> the income and expenses you enter — amount, title or source, category, date, and whether it's personal or shared with a family.</li>
                <li><strong>Goals and risk profile:</strong> savings goals you create, and the risk tolerance, investing experience, age group and income estimate you choose to give the AI advisor.</li>
                <li><strong>AI advisor conversations:</strong> the questions you ask and the answers you receive, so you can see your chat history.</li>
                <li><strong>Families and invites:</strong> the families you create or join, and the email addresses you invite.</li>
                <li><strong>Plan details:</strong> which plan you're on and when it started and ends.</li>
                <li><strong>Contact form messages:</strong> your name, email, subject and message, plus your IP address and browser type. If you allow it in your browser, we also store your approximate location.</li>
                <li><strong>Activity log:</strong> a short record of actions such as adding, editing or deleting entries.</li>
            </ul>
        </section>

        <section id="use" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">03</p>
            <h2>How we use it</h2>
            <p>We use your data only to run FinanceAI for you: to show your dashboard, reports and health score, to answer your AI advisor questions, to run shared family budgets, to send family invite emails, to reply to messages you send us, and to keep accounts secure. We don't use your data for advertising.</p>
        </section>

        <section id="share" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">04</p>
            <h2>Who we share it with</h2>
            <p>We use a small number of services to run the app. They process data only to provide their service to us:</p>
            <ul class="doc-list">
                <li><strong>Vercel</strong> hosts the website.</li>
                <li><strong>Aiven</strong> hosts the database where your account and records are stored.</li>
                <li><strong>Google (Gemini)</strong> generates AI advisor answers. When you ask a question, your message and a summary of your relevant figures are sent to Google to produce the reply.</li>
                <li><strong>Our email provider</strong> delivers family invite emails.</li>
                <li><strong>CoinGecko and Alpha Vantage</strong> provide crypto and gold prices. No personal data is sent to them.</li>
                <li><strong>OpenStreetMap</strong> provides the map on the contact page.</li>
            </ul>
            <p>We may also disclose data if the law requires it.</p>
        </section>

        <section id="cookies" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">05</p>
            <h2>Cookies</h2>
            <p>We only use essential cookies: one to keep you signed in, one to protect forms, and — if you tick "Remember me" — one that keeps you signed in on that device for 30 days and remembers your email for the sign-in page. We don't use advertising or tracking cookies.</p>
        </section>

        <section id="keep" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">06</p>
            <h2>How long we keep it</h2>
            <p>We keep your account and records for as long as your account exists. Entries you delete are removed straight away. If you ask us to delete your account, we delete your account and records.</p>
        </section>

        <section id="rights" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">07</p>
            <h2>Your choices</h2>
            <ul class="doc-list">
                <li><strong>See and correct:</strong> update your name and email in your profile, and edit any entry.</li>
                <li><strong>Download:</strong> export your income and expense lists as PDF.</li>
                <li><strong>Delete:</strong> delete any entry yourself, or ask us to delete your whole account.</li>
                <li><strong>Leave a family:</strong> family owners can remove members, and you can ask the owner to remove you.</li>
            </ul>
        </section>

        <section id="contact" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">08</p>
            <h2>Contact</h2>
            <p>For any privacy question or request, including deleting your account, use our <a href="{{ route('contact') }}">contact form</a>. If we change this policy, we'll update the date at the top of this page.</p>
        </section>
    </x-site.doc>
</div>
@endsection
