@extends('layouts.app')

@section('title', 'Plan updated · FinanceAI')

@section('content')
@php
    $sub = \App\Models\UserSubscription::with('plan')->where('user_id', auth()->id())->where('status', 'active')->latest()->first();
@endphp
<div class="mx-auto flex max-w-lg flex-col items-center px-4 py-16 text-center sm:px-6">
    <span class="flex h-14 w-14 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 ring-8 ring-emerald-50/50">
        <i class="fa-solid fa-check text-xl" aria-hidden="true"></i>
    </span>
    <h1 class="mt-6 text-2xl font-bold tracking-tight text-zinc-900">You’re on {{ $planName ?? ($sub->plan->name ?? 'your new plan') }}</h1>
    <p class="mt-2 text-sm text-zinc-500">Your plan has been switched and is active now.</p>

    @if($sub)
        <dl class="mt-8 w-full divide-y divide-zinc-100 rounded-xl border border-zinc-200 bg-white text-left text-sm">
            <div class="flex justify-between px-4 py-3"><dt class="text-zinc-500">Plan</dt><dd class="font-medium text-zinc-900">{{ $sub->plan->name ?? '—' }}</dd></div>
            <div class="flex justify-between px-4 py-3"><dt class="text-zinc-500">Started</dt><dd class="text-zinc-900">{{ $sub->start_date?->format('d M Y, h:i A') }}</dd></div>
            <div class="flex justify-between px-4 py-3"><dt class="text-zinc-500">Ends</dt><dd class="text-zinc-900">{{ $sub->end_date?->format('d M Y') ?? 'No end date' }}</dd></div>
        </dl>
    @endif

    <div class="mt-8 flex gap-2">
        <a href="{{ route('user.subscription.index') }}" class="inline-flex h-10 items-center rounded-lg border border-zinc-200 px-4 text-sm font-medium text-zinc-700 hover:bg-zinc-50">Plan & billing</a>
        <a href="{{ route('dashboard') }}" class="inline-flex h-10 items-center rounded-lg bg-zinc-900 px-4 text-sm font-medium text-white hover:bg-zinc-800">Go to dashboard</a>
    </div>
</div>
@endsection
