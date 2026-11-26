@extends('layouts.app')

@section('title', 'Notifications · FinanceAI')

@section('content')
@php
    $tones = [
        'danger'  => ['fa-circle-exclamation', 'bg-rose-50 text-rose-600', 'border-l-rose-500'],
        'warning' => ['fa-triangle-exclamation', 'bg-amber-50 text-amber-600', 'border-l-amber-500'],
        'info'    => ['fa-circle-info', 'bg-sky-50 text-sky-600', 'border-l-sky-500'],
        'success' => ['fa-circle-check', 'bg-emerald-50 text-emerald-600', 'border-l-emerald-500'],
    ];
    $unread = $insights->where('is_read', false)->count();
    $inr = fn ($v) => '₹' . inr_number(abs((float) $v), 0);
@endphp

<div class="mx-auto max-w-4xl space-y-6 px-4 py-6 sm:px-6 lg:px-8"
     x-data="{
        read: {{ json_encode((object) $insights->pluck('is_read', 'id')->all()) }},
        markRead(id) {
            if (this.read[id]) return;
            this.read[id] = true;
            fetch('{{ url('user/notifications') }}/' + id + '/read', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } })
                .catch(() => { this.read[id] = false; });
        },
        markAll() { Object.keys(this.read).forEach(id => this.markRead(id)); },
        get unread() { return Object.values(this.read).filter(v => !v).length; }
     }">

    <x-admin.page-header title="Notifications" eyebrow="My money" icon="fa-wallet" :stamp="false"
        description="Insights worked out from your records just now, plus any alerts we’ve sent you.">
        <x-slot name="actions">
            @if($insights->isNotEmpty())
                <button type="button" @click="markAll()" :disabled="unread === 0"
                        class="inline-flex h-9 items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50 disabled:cursor-default disabled:opacity-50">
                    <i class="fa-solid fa-check-double text-xs text-zinc-400" aria-hidden="true"></i> Mark all as read
                </button>
            @endif
        </x-slot>
    </x-admin.page-header>

    {{-- This month at a glance --}}
    <div class="grid grid-cols-3 gap-3">
        <x-admin.stat label="In this month" :value="$inr($thisMonthIncome)" tone="emerald" icon="fa-arrow-trend-up" />
        <x-admin.stat label="Out this month" :value="$inr($thisMonthExpense)" tone="rose" icon="fa-arrow-trend-down" />
        <x-admin.stat label="Kept" :value="$thisMonthIncome > 0 ? round($savingRate) . '%' : '—'" tone="indigo" icon="fa-piggy-bank" :hint="$thisMonthIncome > 0 ? 'of this month’s income' : 'No income this month yet'" />
    </div>

    <x-admin.card title="Insights" flush>
        <x-slot name="action">
            <span class="text-xs text-zinc-500" x-text="unread ? unread + ' unread' : 'All read'">{{ $unread ? $unread . ' unread' : 'All read' }}</span>
        </x-slot>
        @if($insights->isEmpty())
            <x-admin.empty icon="fa-face-smile" title="Nothing needs your attention" text="We’ll flag things like overspending or a drop in income here." />
        @else
            <ul class="divide-y divide-zinc-100">
                @foreach($insights as $n)
                    @php [$icon, $iconTone, $edge] = $tones[$n->tone] ?? $tones['info']; @endphp
                    <li class="relative flex gap-4 border-l-2 px-4 py-4 transition-colors sm:px-5"
                        :class="read[{{ $n->id }}] ? 'border-l-transparent' : '{{ $edge }} bg-zinc-50/50'"
                        @mouseenter.once="setTimeout(() => markRead({{ $n->id }}), 1500)">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $iconTone }}"><i class="fa-solid {{ $icon }} text-sm" aria-hidden="true"></i></span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <p class="text-sm font-semibold text-zinc-900">{{ $n->title }}</p>
                                <span x-show="!read[{{ $n->id }}]" class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-indigo-500" aria-label="Unread"></span>
                            </div>
                            <p class="mt-1 text-sm text-zinc-600">{{ $n->message }}</p>
                            <div class="mt-3 flex flex-wrap items-center gap-3">
                                @if($n->action_url)
                                    <a href="{{ $n->action_url }}" @click="markRead({{ $n->id }})" class="inline-flex h-8 items-center gap-1.5 rounded-md border border-zinc-200 px-2.5 text-xs font-medium text-zinc-700 hover:bg-zinc-50">{{ $n->action_label }} <i class="fa-solid fa-arrow-right text-[10px]" aria-hidden="true"></i></a>
                                @endif
                                <button type="button" x-show="!read[{{ $n->id }}]" @click="markRead({{ $n->id }})" class="text-xs font-medium text-zinc-500 hover:text-zinc-900">Mark as read</button>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-admin.card>

    <x-admin.card title="Alerts" description="Messages saved to your account" flush>
        @if($storedAlerts->isEmpty())
            <p class="px-5 py-6 text-sm text-zinc-500">No alerts yet.</p>
        @else
            <ul class="divide-y divide-zinc-100">
                @foreach($storedAlerts as $alert)
                    <li class="flex gap-4 px-4 py-3.5 sm:px-5">
                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $alert->is_read ? 'bg-zinc-200' : 'bg-indigo-500' }}" aria-hidden="true"></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-zinc-800">{{ $alert->message }}</p>
                            <p class="mt-0.5 text-xs text-zinc-500">{{ ucfirst($alert->type ?? 'alert') }} · <time datetime="{{ $alert->created_at?->toIso8601String() }}" title="{{ $alert->created_at }}">{{ $alert->created_at?->diffForHumans() }}</time></p>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-admin.card>
</div>
@endsection
