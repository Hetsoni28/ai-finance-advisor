@extends('layouts.landing')
@include('partials.site-type')

@section('title', 'Security | FinanceAI')
@section('meta_description', 'How FinanceAI protects your account and financial records: hashed passwords, HTTPS, rate limits, private-by-default records and more.')

@section('content')
@php
    $sections = [
        'account' => 'Your account',
        'records' => 'Your records',
        'transport' => 'Connections',
        'abuse' => 'Abuse protection',
        'admin' => 'Admin access',
        'report' => 'Reporting a problem',
    ];
@endphp
<div class="paper">

    <x-site.hero eyebrow="Security" lead="How we keep your" accent="money data safe."
        lede="A plain description of the protections FinanceAI actually has in place today — no badges we haven't earned.">
        <x-slot name="meta">
            <ul class="flex flex-wrap gap-x-8 gap-y-3 font-mono text-xs uppercase tracking-wider text-slate-300">
                <li><i class="fa-solid fa-key text-indigo-300 mr-1.5" aria-hidden="true"></i>Hashed passwords</li>
                <li><i class="fa-solid fa-lock text-indigo-300 mr-1.5" aria-hidden="true"></i>HTTPS only</li>
                <li><i class="fa-solid fa-user-lock text-indigo-300 mr-1.5" aria-hidden="true"></i>Private by default</li>
            </ul>
        </x-slot>
    </x-site.hero>

    <x-site.doc :sections="$sections">
        <section id="account" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">01</p>
            <h2>Your account</h2>
            <ul class="doc-list">
                <li><strong>Passwords are never stored as text.</strong> They are hashed with bcrypt, so not even our team can read them.</li>
                <li><strong>Password rules.</strong> Passwords need at least 8 characters. When you change your password, the new one must also include upper and lower case letters, a number and a symbol.</li>
                <li><strong>Changing your password signs out your other devices.</strong> If you think someone else has your password, change it and every other session ends.</li>
                <li><strong>"Remember me" is your choice.</strong> If you turn it on, you stay signed in on that device for 30 days; otherwise you're signed out after 2 hours of inactivity.</li>
            </ul>
        </section>

        <section id="records" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">02</p>
            <h2>Your records</h2>
            <p>Every income and expense entry belongs to the account that created it. The app checks ownership on every view, edit and delete, so one user can never open another user's records.</p>
            <p>Shared family entries are visible only to members of that family. Personal entries stay personal even when you're in a family.</p>
        </section>

        <section id="transport" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">03</p>
            <h2>Connections</h2>
            <p>The whole site is served over HTTPS, so everything you send and receive is encrypted in transit. Every form includes a CSRF token, which stops other websites from submitting forms on your behalf.</p>
        </section>

        <section id="abuse" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">04</p>
            <h2>Abuse protection</h2>
            <ul class="doc-list">
                <li><strong>Sign-in attempts are limited</strong> to 5 per minute, which slows down password guessing.</li>
                <li><strong>The AI advisor is rate limited</strong> to 30 messages a minute, plus a daily limit that depends on your plan.</li>
                <li><strong>Accounts can be suspended.</strong> A suspended account can't sign in until an admin restores it.</li>
            </ul>
        </section>

        <section id="admin" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">05</p>
            <h2>Admin access</h2>
            <p>The admin panel is only available to accounts with the admin role. Important account actions, like adding, changing or deleting entries, are written to an activity log.</p>
        </section>

        <section id="report" class="doc-section">
            <p class="font-mono text-sm text-indigo-600">06</p>
            <h2>Reporting a problem</h2>
            <p>If you think you've found a security issue, please tell us through the <a href="{{ route('contact') }}">contact form</a> and include as much detail as you can. Please don't share the details publicly until we've had a chance to fix it.</p>
        </section>
    </x-site.doc>
</div>
@endsection
