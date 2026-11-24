@extends('layouts.app')

@section('title', 'Budgets · FinanceAI')

@section('content')
@php
    $inr = fn ($v) => '₹' . number_format(abs((float) $v), 0);
    $badges = [
        'ok'   => ['On track', 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
        'pace' => ['Spending fast', 'bg-amber-50 text-amber-700 ring-amber-200'],
        'warn' => ['80% used', 'bg-amber-50 text-amber-800 ring-amber-300'],
        'over' => ['Over budget', 'bg-rose-50 text-rose-700 ring-rose-200'],
        'none' => ['No budget', 'bg-zinc-50 text-zinc-500 ring-zinc-200'],
    ];
    $bar = ['ok' => 'bg-emerald-500', 'pace' => 'bg-amber-400', 'warn' => 'bg-amber-500', 'over' => 'bg-rose-500', 'none' => 'bg-zinc-300'];
    $left = $totalLimit - $totalSpent;
    $budgeted = $rows->whereNotNull('limit');
    $unbudgeted = $rows->whereNull('limit');
@endphp

<div class="mx-auto max-w-6xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    <x-admin.page-header title="Budgets" eyebrow="My money" icon="fa-wallet" :stamp="false"
        :description="'Set a monthly limit for each category. Your AI advisor warns you at 80% and again at 100%. ' . $daysLeft . ' ' . \Illuminate\Support\Str::plural('day', $daysLeft) . ' left in ' . now()->format('F') . '.'" />

    <x-admin.flash />

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <x-admin.stat label="Budgeted this month" :value="$inr($totalLimit)" icon="fa-gauge-high" tone="indigo"
            :hint="$budgetedCount ? $budgetedCount . ' ' . \Illuminate\Support\Str::plural('category', $budgetedCount) : 'No budgets yet'" />
        <x-admin.stat label="Spent in those categories" :value="$inr($totalSpent)" icon="fa-arrow-trend-down" tone="rose"
            :hint="$totalLimit > 0 ? round($totalSpent / $totalLimit * 100) . '% of your budgets' : '—'" />
        <x-admin.stat label="Left to spend" :value="($left < 0 ? '−' : '') . $inr($left)" icon="fa-piggy-bank" :tone="$left < 0 ? 'rose' : 'emerald'"
            :hint="$left > 0 && $daysLeft ? 'About ' . $inr($left / $daysLeft) . ' a day' : ($left < 0 ? 'Over your total budget' : '—')" />
        <x-admin.stat label="Days left" :value="$daysLeft" icon="fa-calendar-day" tone="sky" :hint="now()->format('F Y')" />
    </div>

    {{-- How the alerts work --}}
    <div class="flex items-start gap-3 rounded-xl border border-indigo-100 bg-indigo-50/60 px-4 py-3.5">
        <i class="fa-solid fa-wand-magic-sparkles mt-0.5 text-indigo-500" aria-hidden="true"></i>
        <p class="text-sm text-zinc-700"><span class="font-semibold text-zinc-900">AI alerts:</span> when a category reaches 80% of its limit, and again at 100%, Google Gemini writes you a short alert with a tip. It shows in the
            <a href="{{ route('user.notifications.index') }}" class="font-medium text-indigo-600 underline underline-offset-2">notification bell</a> — once per level each month.</p>
    </div>

    <x-admin.card title="Categories" description="Limits reset on the 1st of every month. Suggestions are based on your average spend over the last 3 months." flush>
        <ul class="divide-y divide-zinc-100">
            @foreach($budgeted->concat($unbudgeted) as $r)
                @php [$badgeText, $badgeCls] = $badges[$r['status']]; @endphp
                <li class="grid gap-4 px-4 py-5 sm:px-5 lg:grid-cols-12 lg:items-center" x-data="{ v: '{{ $r['limit'] ? (int) $r['limit'] : '' }}' }">
                    {{-- Category + status --}}
                    <div class="flex items-center gap-3 lg:col-span-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-600" aria-hidden="true"><i class="fa-solid {{ $r['icon'] }} text-sm"></i></span>
                        <div class="min-w-0">
                            <p class="font-semibold text-zinc-900">{{ $r['category'] }}</p>
                            <span class="mt-0.5 inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium ring-1 ring-inset {{ $badgeCls }}">{{ $badgeText }}</span>
                        </div>
                    </div>

                    {{-- Progress --}}
                    <div class="lg:col-span-5">
                        @if($r['limit'])
                            <div class="flex items-baseline justify-between gap-3 text-sm">
                                <span class="tabular-nums text-zinc-900"><span class="font-semibold">{{ $inr($r['spent']) }}</span> <span class="text-zinc-500">of {{ $inr($r['limit']) }}</span></span>
                                <span class="tabular-nums font-semibold {{ $r['status'] === 'over' ? 'text-rose-600' : ($r['status'] === 'warn' ? 'text-amber-700' : 'text-zinc-700') }}">{{ $r['pct'] }}%</span>
                            </div>
                            <div class="relative mt-2 h-2 overflow-hidden rounded-full bg-zinc-100" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ min(100, $r['pct']) }}" aria-label="{{ $r['category'] }} budget used">
                                <div class="h-full rounded-full {{ $bar[$r['status']] }}" style="width: {{ min(100, $r['pct']) }}%"></div>
                                <span class="absolute inset-y-0 left-[80%] w-px bg-zinc-400/60" title="80% alert" aria-hidden="true"></span>
                            </div>
                            <p class="mt-1.5 text-xs text-zinc-500">
                                @if($r['remaining'] >= 0)
                                    {{ $inr($r['remaining']) }} left{{ $r['perDay'] ? ' · about ' . $inr($r['perDay']) . ' a day' : '' }}
                                @else
                                    <span class="text-rose-600">{{ $inr(-$r['remaining']) }} over the limit</span>
                                @endif
                                @if($r['status'] === 'pace')
                                    · <span class="text-amber-700">at this pace you'll spend {{ $inr($r['projected']) }} by month end</span>
                                @endif
                            </p>
                        @else
                            <p class="text-sm text-zinc-500">
                                {{ $r['spent'] > 0 ? $inr($r['spent']) . ' spent this month · no limit set' : 'No spending this month' }}
                            </p>
                        @endif
                    </div>

                    {{-- Limit form --}}
                    <div class="lg:col-span-4">
                        <form method="POST" action="{{ route('user.budgets.store') }}" class="flex items-center gap-2">
                            @csrf
                            <input type="hidden" name="category" value="{{ $r['category'] }}">
                            <label class="sr-only" for="limit-{{ $loop->index }}">Monthly limit for {{ $r['category'] }}</label>
                            <div class="flex h-9 min-w-0 flex-1 items-center rounded-lg border border-zinc-200 bg-white px-2.5 focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-100">
                                <span class="text-sm text-zinc-400">₹</span>
                                <input id="limit-{{ $loop->index }}" name="monthly_limit" type="number" inputmode="numeric" min="100" step="100" required x-model="v"
                                       placeholder="{{ $r['suggested'] ? number_format($r['suggested'], 0, '', '') : 'Monthly limit' }}"
                                       class="h-full w-full border-0 bg-transparent px-1.5 text-sm tabular-nums text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-0">
                            </div>
                            <button class="h-9 shrink-0 rounded-lg {{ $r['limit'] ? 'border border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50' : 'bg-zinc-900 text-white hover:bg-zinc-800' }} px-3 text-sm font-medium">
                                {{ $r['limit'] ? 'Update' : 'Set limit' }}
                            </button>
                        </form>
                        <div class="mt-1.5 flex items-center justify-between gap-2 text-xs">
                            @if($r['suggested'] && (int) $r['suggested'] !== (int) $r['limit'])
                                <button type="button" @click="v = '{{ (int) $r['suggested'] }}'" class="text-indigo-600 hover:text-indigo-700">
                                    <i class="fa-solid fa-wand-magic-sparkles mr-0.5" aria-hidden="true"></i>Suggested: {{ $inr($r['suggested']) }} (3-month average)
                                </button>
                            @else
                                <span></span>
                            @endif
                            @if($r['budget'])
                                <form method="POST" action="{{ route('user.budgets.destroy', $r['budget']) }}"
                                      data-confirm-title="Remove the {{ $r['category'] }} budget?" data-confirm="You'll stop getting alerts for {{ $r['category'] }}. Your expenses aren't affected." data-confirm-ok="Remove budget">
                                    @csrf @method('DELETE')
                                    <button class="text-zinc-400 hover:text-rose-600">Remove</button>
                                </form>
                            @endif
                        </div>
                        @error('monthly_limit')
                            @if(old('category') === $r['category'])<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@endif
                        @enderror
                    </div>
                </li>
            @endforeach
        </ul>
    </x-admin.card>
</div>
@endsection
