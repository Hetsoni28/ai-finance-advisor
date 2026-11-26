@extends('layouts.app')

@section('title', 'Profile · FinanceAI')

@section('content')
@php
    $inr = fn ($v) => '₹' . inr_number(abs((float) $v), 0);
    $signed = fn ($v) => ($v < 0 ? '−' : '') . '₹' . inr_number(abs((float) $v), 0);
    $rate = $totalIncome > 0 ? round($savings / $totalIncome * 100, 1) : null;
    $planSlug = $user->current_plan_slug ?? 'free';
    $activityByDay = $activities->groupBy(fn ($a) => $a->created_at?->isToday() ? 'Today' : ($a->created_at?->isYesterday() ? 'Yesterday' : $a->created_at?->format('d M Y')));
@endphp

<div class="mx-auto max-w-6xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    <x-admin.page-header title="Profile" eyebrow="Account" icon="fa-user" :stamp="false" description="Your account details, security and recent activity.">
        <x-slot name="actions">
            <a href="{{ route('user.profile.password.form') }}" class="inline-flex h-9 items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50">
                <i class="fa-solid fa-key text-xs text-zinc-400" aria-hidden="true"></i> Change password
            </a>
            <a href="{{ route('user.profile.edit') }}" class="inline-flex h-9 items-center gap-2 rounded-lg bg-zinc-900 px-3 text-sm font-medium text-white shadow-sm hover:bg-zinc-800">
                <i class="fa-solid fa-pen text-xs" aria-hidden="true"></i> Edit details
            </a>
        </x-slot>
    </x-admin.page-header>

    <x-admin.flash />

    {{-- Identity --}}
    <section class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-[0_1px_2px_rgba(24,24,27,.04)]">
        <div class="h-20 bg-zinc-950"></div>
        <div class="-mt-10 px-5 pb-5 sm:px-6">
            <div class="flex items-end justify-between gap-3">
                <x-admin.avatar :name="$user->name" size="lg" class="!h-20 !w-20 !text-2xl ring-4 ring-white" />
                <div class="flex flex-wrap justify-end gap-2 pb-1">
                    @if($user->isAdmin())<x-admin.badge tone="indigo">Admin</x-admin.badge>@endif
                    <x-admin.badge :tone="$planSlug === 'free' ? 'zinc' : 'emerald'">{{ $user->current_plan_name }} plan</x-admin.badge>
                    @if($user->email_verified_at)
                        <x-admin.badge tone="emerald" dot>Email verified</x-admin.badge>
                    @else
                        <x-admin.badge tone="amber" dot>Email not verified</x-admin.badge>
                    @endif
                </div>
            </div>
            <h2 class="mt-3 text-xl font-semibold text-zinc-900">{{ $user->name }}</h2>
            <p class="text-sm text-zinc-500">{{ $user->email }}</p>
        </div>
        <dl class="grid grid-cols-2 border-t border-zinc-100 text-sm sm:grid-cols-4">
            <div class="border-r border-zinc-100 px-5 py-4 sm:px-6"><dt class="text-xs text-zinc-500">Member since</dt><dd class="mt-0.5 font-medium text-zinc-900">{{ $user->created_at?->format('d M Y') }}</dd></div>
            <div class="px-5 py-4 sm:border-r sm:border-zinc-100 sm:px-6"><dt class="text-xs text-zinc-500">Last updated</dt><dd class="mt-0.5 font-medium text-zinc-900">{{ $user->updated_at?->diffForHumans() }}</dd></div>
            <div class="border-r border-t border-zinc-100 px-5 py-4 sm:border-t-0 sm:px-6"><dt class="text-xs text-zinc-500">Records</dt><dd class="mt-0.5 font-medium tabular-nums text-zinc-900">{{ inr_number($user->incomes()->count() + $user->expenses()->count()) }}</dd></div>
            <div class="border-t border-zinc-100 px-5 py-4 sm:border-t-0 sm:px-6"><dt class="text-xs text-zinc-500">Family hubs</dt><dd class="mt-0.5 font-medium tabular-nums text-zinc-900">{{ inr_number($user->families()->count()) }}</dd></div>
        </dl>
    </section>

    <div class="grid grid-cols-3 gap-3">
        <x-admin.stat label="Lifetime income" :value="$inr($totalIncome)" icon="fa-arrow-trend-up" tone="emerald" :href="route('user.incomes.index')" />
        <x-admin.stat label="Lifetime expenses" :value="$inr($totalExpense)" icon="fa-arrow-trend-down" tone="rose" :href="route('user.expenses.index')" />
        <x-admin.stat label="Net saved" :value="$signed($savings)" icon="fa-piggy-bank" tone="indigo" :hint="$rate !== null ? $rate . '% of income kept' : null" />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-admin.card class="lg:col-span-2" title="Your recent activity" flush>
            @if($activities->isEmpty())
                <x-admin.empty icon="fa-clock-rotate-left" title="No activity yet" text="Things you add or change will be listed here." />
            @else
                <div class="px-4 py-2 sm:px-5">
                    @foreach($activityByDay as $day => $items)
                        <p class="pb-1 pt-3 text-[11px] font-semibold uppercase tracking-wider text-zinc-400">{{ $day }}</p>
                        <ol class="relative ml-1.5 border-l border-zinc-200">
                            @foreach($items as $a)
                                @php
                                    $d = strtolower($a->description);
                                    $dot = \Illuminate\Support\Str::contains($d, ['delete', 'remove']) ? 'bg-rose-500' : (\Illuminate\Support\Str::contains($d, ['modif', 'update', 'edit', 'change', 'password']) ? 'bg-amber-500' : 'bg-emerald-500');
                                @endphp
                                <li class="relative py-2.5 pl-5">
                                    <span class="absolute -left-[5px] top-[15px] h-2.5 w-2.5 rounded-full ring-4 ring-white {{ $dot }}" aria-hidden="true"></span>
                                    <p class="text-sm text-zinc-800">{{ $a->description }}</p>
                                    <p class="mt-0.5 text-xs text-zinc-500">{{ $a->created_at?->format('h:i A') }}</p>
                                </li>
                            @endforeach
                        </ol>
                    @endforeach
                </div>
            @endif
        </x-admin.card>

        <div class="space-y-6">
            <x-admin.card title="Security">
                <ul class="space-y-3 text-sm">
                    <li class="flex items-center justify-between gap-3">
                        <span class="text-zinc-600">Password</span>
                        <a href="{{ route('user.profile.password.form') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">Change</a>
                    </li>
                    <li class="flex items-center justify-between gap-3">
                        <span class="text-zinc-600">Email address</span>
                        <a href="{{ route('user.profile.edit') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">Update</a>
                    </li>
                </ul>
            </x-admin.card>

            <x-admin.card title="This session">
                <dl class="space-y-3 text-sm">
                    <div><dt class="text-xs text-zinc-500">IP address</dt><dd class="mt-0.5 font-mono text-[13px] text-zinc-800">{{ request()->ip() }}</dd></div>
                    <div><dt class="text-xs text-zinc-500">Browser</dt><dd class="mt-0.5 break-words text-[13px] text-zinc-600">{{ \Illuminate\Support\Str::limit(request()->userAgent() ?? 'Unknown', 90) }}</dd></div>
                </dl>
                @if(!empty($activeSessions) && count($activeSessions) > 1)
                    <p class="mt-4 border-t border-zinc-100 pt-3 text-xs text-zinc-500">{{ count($activeSessions) }} signed-in sessions on your account.</p>
                @endif
            </x-admin.card>

            <x-admin.card title="Plan">
                <p class="text-sm text-zinc-600">You’re on the <span class="font-semibold text-zinc-900">{{ $user->current_plan_name }}</span> plan.</p>
                <a href="{{ route('user.subscription.index') }}" class="mt-4 inline-flex h-9 w-full items-center justify-center rounded-lg border border-zinc-200 text-sm font-medium text-zinc-700 hover:bg-zinc-50">{{ $planSlug === 'free' ? 'See plans' : 'Manage plan' }}</a>
            </x-admin.card>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="inline-flex h-9 w-full items-center justify-center gap-2 rounded-lg text-sm font-medium text-rose-600 hover:bg-rose-50">
                    <i class="fa-solid fa-arrow-right-from-bracket text-xs" aria-hidden="true"></i> Sign out
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
