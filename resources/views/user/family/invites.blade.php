@extends('layouts.app')

@section('title', 'Invites · ' . $family->name . ' · FinanceAI')

@section('content')
@php
    $now = now();
    $accepted = $invites->filter(fn ($i) => $i->accepted_at);
    $pending  = $invites->filter(fn ($i) => !$i->accepted_at && (!$i->expires_at || \Carbon\Carbon::parse($i->expires_at)->isFuture()));
    $expired  = $invites->filter(fn ($i) => !$i->accepted_at && $i->expires_at && \Carbon\Carbon::parse($i->expires_at)->isPast());
@endphp
<div class="mx-auto max-w-5xl space-y-6 px-4 py-6 sm:px-6 lg:px-8" x-data="{ selected: [] }">

    <x-admin.page-header title="Invites" eyebrow="Family" icon="fa-people-roof" :stamp="false" :description="'Who you’ve invited to ' . $family->name . '.'">
        <x-slot name="actions">
            <a href="{{ route('user.families.show', $family) }}" class="inline-flex h-9 items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50">
                <i class="fa-solid fa-arrow-left text-xs text-zinc-400" aria-hidden="true"></i> Back to family
            </a>
        </x-slot>
    </x-admin.page-header>

    <x-admin.flash />

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <x-admin.stat label="Pending" :value="$pending->count()" icon="fa-hourglass-half" tone="amber" />
        <x-admin.stat label="Accepted" :value="$accepted->count()" icon="fa-user-check" tone="emerald" />
        <x-admin.stat label="Expired" :value="$expired->count()" icon="fa-clock" tone="zinc" />
        <x-admin.stat label="Average time to accept" :value="$accepted->count() ? $avgWait . ' h' : '—'" icon="fa-stopwatch" tone="sky" :hint="$accepted->count() ? null : 'No accepted invites yet'" />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-admin.card title="Waiting for a reply" flush>
                @if($pending->isEmpty())
                    <p class="px-5 py-6 text-sm text-zinc-500">No pending invites.</p>
                @else
                    <ul class="divide-y divide-zinc-100">
                        @foreach($pending as $inv)
                            <li class="flex items-center gap-3 px-4 py-3 sm:px-5">
                                <x-admin.avatar :name="$inv->email" size="sm" />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-zinc-900">{{ $inv->email }}</p>
                                    <p class="text-xs text-zinc-500">Sent {{ $inv->created_at?->diffForHumans() }}@if($inv->expires_at) · expires {{ \Carbon\Carbon::parse($inv->expires_at)->diffForHumans() }}@endif</p>
                                </div>
                                <form method="POST" action="{{ route('user.families.invites.destroy', [$family, $inv]) }}" data-confirm-title="Cancel this invite?" data-confirm="The invite link sent to {{ $inv->email }} will stop working." data-confirm-ok="Cancel invite" data-confirm-cancel="Keep it">
                                    @csrf @method('DELETE')
                                    <button class="h-8 rounded-md px-2.5 text-xs font-medium text-zinc-600 hover:bg-rose-50 hover:text-rose-600">Cancel invite</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-admin.card>

            <x-admin.card title="Expired" flush>
                @if($expired->isEmpty())
                    <p class="px-5 py-6 text-sm text-zinc-500">No expired invites.</p>
                @else
                    <form method="POST" action="{{ route('user.families.invites.bulkDestroy', $family) }}" data-confirm-title="Delete the selected invites?" data-confirm="The selected expired invites will be removed from this list." data-confirm-ok="Delete invites">
                        @csrf @method('DELETE')
                        <ul class="divide-y divide-zinc-100">
                            @foreach($expired as $inv)
                                <li class="flex items-center gap-3 px-4 py-3 sm:px-5">
                                    <input type="checkbox" name="invite_ids[]" value="{{ $inv->id }}" x-model="selected" class="rounded border-zinc-300" aria-label="Select invite to {{ $inv->email }}">
                                    <p class="min-w-0 flex-1 truncate text-sm text-zinc-500 line-through decoration-zinc-300">{{ $inv->email }}</p>
                                    <span class="text-xs text-zinc-400">Expired {{ \Carbon\Carbon::parse($inv->expires_at)->diffForHumans() }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="flex items-center justify-between border-t border-zinc-100 px-4 py-3 sm:px-5">
                            <button type="button" class="text-xs font-medium text-zinc-500 hover:text-zinc-900" @click="selected = selected.length ? [] : {{ json_encode($expired->pluck('id')->map(fn ($i) => (string) $i)->values()) }}">Select all</button>
                            <button :disabled="!selected.length" class="h-8 rounded-md bg-zinc-900 px-3 text-xs font-medium text-white hover:bg-zinc-800 disabled:opacity-40">Delete selected</button>
                        </div>
                    </form>
                @endif
            </x-admin.card>

            @if($accepted->isNotEmpty())
                <x-admin.card title="Accepted" flush>
                    <ul class="divide-y divide-zinc-100">
                        @foreach($accepted as $inv)
                            <li class="flex items-center gap-3 px-4 py-3 sm:px-5">
                                <i class="fa-solid fa-circle-check text-emerald-500" aria-hidden="true"></i>
                                <p class="min-w-0 flex-1 truncate text-sm text-zinc-800">{{ $inv->email }}</p>
                                <span class="text-xs text-zinc-500">Joined {{ $inv->accepted_at->format('d M Y') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </x-admin.card>
            @endif
        </div>

        <x-admin.card title="Invite someone">
            <form method="POST" action="{{ route('user.families.invite', $family) }}" class="space-y-3">
                @csrf
                <label for="invite-email" class="sr-only">Email address</label>
                <input id="invite-email" type="email" name="email" required value="{{ old('email') }}" placeholder="name@example.com"
                       class="h-10 w-full rounded-lg border border-zinc-200 px-3 text-sm placeholder:text-zinc-400 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                @error('email')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                <button class="inline-flex h-9 w-full items-center justify-center gap-2 rounded-lg bg-zinc-900 text-sm font-medium text-white hover:bg-zinc-800"><i class="fa-solid fa-paper-plane text-xs" aria-hidden="true"></i> Send invite</button>
                <p class="text-xs text-zinc-500">They’ll get an email with a one-click link to join {{ $family->name }}.</p>
            </form>
        </x-admin.card>
    </div>
</div>
@endsection
