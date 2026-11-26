@extends('layouts.app')

@section('title', 'Plan & billing · FinanceAI')

@section('content')
@php
    $user = auth()->user();
    $inr = fn ($v) => '₹' . inr_number((float) $v, 0);
    $isFree = ($currentPlanSlug ?? 'free') === 'free';
    $current = $currentPlan ?? $plans->firstWhere('slug', 'free');
    $history = \App\Models\UserSubscription::with('plan')->where('user_id', $user->id)->latest()->limit(12)->get();
    $daysLeft = $subscription && $subscription->end_date ? $subscription->remaining_days : null;
    $periodDays = $subscription && $subscription->start_date && $subscription->end_date ? max(1, $subscription->start_date->diffInDays($subscription->end_date)) : null;
    $usedShare = $periodDays && $daysLeft !== null ? max(0, min(100, round(($periodDays - $daysLeft) / $periodDays * 100))) : null;

    // Real usage this month
    $monthStart = now()->startOfMonth();
    $aiThisMonth = \App\Models\AiChat::where('user_id', $user->id)->where('sender', 'user')->where('created_at', '>=', $monthStart)->count();
    $recordsThisMonth = \App\Models\Income::where('user_id', $user->id)->where('created_at', '>=', $monthStart)->count()
        + \App\Models\Expense::where('user_id', $user->id)->where('created_at', '>=', $monthStart)->count();
@endphp

<div class="mx-auto max-w-6xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    <x-admin.page-header title="Plan & billing" eyebrow="Account" icon="fa-user" :stamp="false" description="Your current plan, what you’ve used this month, and your plan history." />

    <x-admin.flash />

    {{-- Current plan --}}
    <section class="grid gap-6 rounded-2xl bg-zinc-950 p-6 text-white ring-1 ring-zinc-900 sm:p-8 lg:grid-cols-12 lg:items-center">
        <div class="lg:col-span-5">
            <p class="text-xs font-medium uppercase tracking-wider text-zinc-400">Current plan</p>
            <p class="mt-2 text-3xl font-semibold tracking-tight">{{ $current->name ?? 'Starter' }}</p>
            <p class="mt-1 text-sm text-zinc-400">
                @if($isFree)
                    Free forever · no expiry
                @else
                    {{ $inr($current->price) }} for {{ $current->duration_days ?: 30 }} days
                @endif
            </p>
        </div>
        <div class="lg:col-span-4">
            @if(!$isFree && $subscription?->end_date)
                <div class="flex justify-between text-xs text-zinc-400"><span>{{ $subscription->start_date?->format('d M') }}</span><span>Ends {{ $subscription->end_date->format('d M Y') }}</span></div>
                <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-white/10"><div class="h-full rounded-full {{ $daysLeft <= 5 ? 'bg-rose-400' : 'bg-indigo-400' }}" style="width: {{ $usedShare }}%"></div></div>
                <p class="mt-2 text-sm {{ $daysLeft <= 5 ? 'text-rose-300' : 'text-zinc-300' }}">{{ $subscription->remaining_text }}</p>
            @else
                <p class="text-sm text-zinc-400">Upgrade any time to unlock more of FinanceAI. You can switch back to Starter whenever you like.</p>
            @endif
        </div>
        <dl class="grid grid-cols-2 gap-4 border-t border-white/10 pt-6 lg:col-span-3 lg:border-l lg:border-t-0 lg:pl-8 lg:pt-0">
            <div><dt class="text-xs text-zinc-400">AI messages</dt><dd class="mt-1 text-xl font-semibold tabular-nums">{{ inr_number($aiThisMonth) }}</dd><dd class="text-[11px] text-zinc-500">this month</dd></div>
            <div><dt class="text-xs text-zinc-400">Records added</dt><dd class="mt-1 text-xl font-semibold tabular-nums">{{ inr_number($recordsThisMonth) }}</dd><dd class="text-[11px] text-zinc-500">this month</dd></div>
        </dl>
    </section>

    {{-- Plans --}}
    <div>
        <h2 class="text-sm font-semibold text-zinc-900">All plans</h2>
        <p class="mt-0.5 text-xs text-zinc-500">Prices are per 30 days. Switching takes effect straight away. No card is charged in this version of FinanceAI.</p>
        <div class="mt-4 grid gap-4 md:grid-cols-{{ min(3, max(1, $plans->count())) }}">
            @foreach($plans as $plan)
                @php
                    $isCurrent = $plan->slug === ($currentPlanSlug ?? 'free');
                    $paid = (float) $plan->price > 0;
                    $isUpgrade = $paid && ((float) $plan->price > (float) ($current->price ?? 0));
                @endphp
                <div class="relative flex flex-col rounded-xl border bg-white p-5 shadow-[0_1px_2px_rgba(24,24,27,.04)] {{ $isCurrent ? 'border-zinc-900 ring-1 ring-zinc-900' : 'border-zinc-200' }}">
                    @if($isCurrent)<span class="absolute -top-2.5 left-5 rounded-md bg-zinc-900 px-2 py-0.5 text-[11px] font-semibold text-white">Your plan</span>@endif
                    <h3 class="text-base font-semibold text-zinc-900">{{ $plan->name }}</h3>
                    <p class="mt-3 flex items-baseline gap-1"><span class="text-3xl font-semibold tracking-tight tabular-nums text-zinc-900">{{ $inr($plan->price) }}</span><span class="text-sm text-zinc-500">{{ $paid ? '/ ' . ($plan->duration_days ?: 30) . ' days' : 'forever' }}</span></p>
                    <ul class="mt-5 flex-1 space-y-2.5 text-sm">
                        @foreach((array) $plan->features as $feature)
                            <li class="flex gap-2.5 text-zinc-600"><i class="fa-solid fa-check mt-1 text-xs text-emerald-600" aria-hidden="true"></i>{{ $feature }}</li>
                        @endforeach
                    </ul>
                    <div class="mt-6">
                        @if($isCurrent)
                            <span class="flex h-10 items-center justify-center rounded-lg bg-zinc-100 text-sm font-medium text-zinc-500">Current plan</span>
                        @elseif($paid)
                            <form method="POST" action="{{ route('user.subscription.subscribe', $plan) }}" data-confirm-title="Switch to {{ $plan->name }}?" data-confirm="{{ $inr($plan->price) }} for {{ $plan->duration_days ?: 30 }} days. The change takes effect straight away — no card is charged in this version of FinanceAI." data-confirm-ok="Switch to {{ $plan->name }}" data-confirm-tone="primary">
                                @csrf
                                <button class="h-10 w-full rounded-lg text-sm font-medium {{ $isUpgrade ? 'bg-zinc-900 text-white hover:bg-zinc-800' : 'border border-zinc-200 text-zinc-700 hover:bg-zinc-50' }}">{{ $isUpgrade ? 'Upgrade to ' . $plan->name : 'Switch to ' . $plan->name }}</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('user.subscription.cancel') }}" data-confirm-title="Go back to Starter?" data-confirm="Your {{ $current->name ?? '' }} plan ends now and you'll move to the free Starter plan. You can upgrade again any time." data-confirm-ok="Switch to Starter" data-confirm-cancel="Keep my plan">
                                @csrf @method('DELETE')
                                <button class="h-10 w-full rounded-lg border border-zinc-200 text-sm font-medium text-zinc-700 hover:bg-zinc-50">Switch to Starter</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- History --}}
    <x-admin.card title="Plan history" flush>
        @if($history->isEmpty())
            <p class="px-5 py-6 text-sm text-zinc-500">You’ve always been on the free Starter plan.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-zinc-100 text-left text-xs text-zinc-500">
                        <th class="px-5 py-2.5 font-medium">Plan</th><th class="px-4 py-2.5 font-medium">Started</th><th class="px-4 py-2.5 font-medium">Ends</th><th class="px-4 py-2.5 font-medium">Status</th><th class="px-5 py-2.5 text-right font-medium">Amount</th>
                    </tr></thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach($history as $h)
                            @php $tone = ['active' => 'emerald', 'cancelled' => 'zinc', 'expired' => 'amber'][$h->status] ?? 'zinc'; @endphp
                            <tr>
                                <td class="px-5 py-3 font-medium text-zinc-900">{{ $h->plan->name ?? 'Plan removed' }}</td>
                                <td class="px-4 py-3 text-zinc-600">{{ $h->start_date?->format('d M Y') ?? '—' }}</td>
                                <td class="px-4 py-3 text-zinc-600">{{ $h->end_date?->format('d M Y') ?? '—' }}</td>
                                <td class="px-4 py-3"><x-admin.badge :tone="$tone" dot>{{ ucfirst($h->status) }}</x-admin.badge></td>
                                <td class="px-5 py-3 text-right tabular-nums text-zinc-800">{{ $h->amount_paid !== null ? $inr($h->amount_paid) : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-admin.card>
</div>
@endsection
