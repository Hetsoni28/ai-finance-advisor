@extends('layouts.landing')
@include('partials.site-type')

@section('title', 'Data Processing | FinanceAI')
@section('meta_description', 'Which services process data for FinanceAI, what each one receives, and why.')

@section('content')
@php
    $sections = [
        'overview' => 'Overview',
        'processors' => 'Services we use',
        'ai' => 'The AI advisor',
        'notsent' => 'What we never send',
        'questions' => 'Questions',
    ];
    $processors = [
        ['Vercel', 'Website hosting', 'Every page request, including the data shown on that page'],
        ['Aiven', 'Database hosting', 'Your account, records, goals, chats, families and messages'],
        ['Google (Gemini)', 'AI advisor answers', 'Your question and a summary of your relevant figures'],
        ['Email provider', 'Sending family invites', 'The invited email address and the family name'],
        ['CoinGecko, Alpha Vantage', 'Crypto and gold prices', 'No personal data'],
        ['OpenStreetMap', 'Map on the contact page', 'Map requests from your browser'],
    ];
@endphp
<div class="paper">

    <x-site.hero eyebrow="Legal" lead="Data" accent="Processing"
        lede="The outside services that handle data for FinanceAI, what each one receives, and why. This page goes with our privacy policy.">
        <x-slot name="meta">
            <p class="font-mono text-xs uppercase tracking-wider text-slate-400">Last updated: <span class="text-slate-200">24 November 2026</span></p>
        </x-slot>
    </x-site.hero>

    <x-site.doc :sections="$sections">
        <section id="overview" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">01</p>
            <h2>Overview</h2>
            <p>FinanceAI is run by Hetvi Prajapati in India. To run the app we rely on a few outside services. Each one gets only the data it needs to do its job, and none of them receives your data for advertising.</p>
        </section>

        <section id="processors" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">02</p>
            <h2>Services we use</h2>
            <div class="mt-6 overflow-x-auto">
                <table class="w-full text-left text-base">
                    <thead>
                        <tr class="border-b border-slate-900">
                            <th class="py-3 pr-4 eyebrow text-slate-500 font-normal">Service</th>
                            <th class="py-3 pr-4 eyebrow text-slate-500 font-normal">Used for</th>
                            <th class="py-3 eyebrow text-slate-500 font-normal">What it receives</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($processors as [$name, $purpose, $data])
                            <tr class="border-b border-slate-200 align-top">
                                <td class="py-4 pr-4 font-bold text-slate-900">{{ $name }}</td>
                                <td class="py-4 pr-4">{{ $purpose }}</td>
                                <td class="py-4 text-[15px]">{{ $data }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section id="ai" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">03</p>
            <h2>The AI advisor</h2>
            <p>When you send a message to the AI advisor, we send your question to Google's Gemini service together with a short summary of the figures it needs — for example your monthly income, spending, saving rate, goals and risk profile — so the answer fits your situation. If Gemini isn't available, the advisor answers with built-in rules and nothing is sent to Google.</p>
        </section>

        <section id="notsent" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">04</p>
            <h2>What we never send</h2>
            <ul class="doc-list">
                <li>Your password — it's stored only as a hash and never leaves our system.</li>
                <li>Any of your data to advertisers or data brokers.</li>
                <li>Personal data to the market price services.</li>
            </ul>
        </section>

        <section id="questions" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">05</p>
            <h2>Questions</h2>
            <p>If you have a question about how your data is processed, use our <a href="{{ route('contact') }}">contact form</a>. See the <a href="{{ route('privacy') }}">privacy policy</a> for what we collect and your choices.</p>
        </section>
    </x-site.doc>
</div>
@endsection
