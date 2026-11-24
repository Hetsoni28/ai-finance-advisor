@extends('layouts.app')

@section('title', 'Messages · Admin · FinanceAI')

@section('content')
@php
    $tabs = ['all' => ['All', $totalCount], 'unread' => ['Unread', $unreadCount], 'read' => ['Read', max(0, $totalCount - $unreadCount)]];
@endphp

<div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    <x-admin.page-header title="Messages" description="Messages sent through the contact form on the website." />

    <x-admin.flash />

    <div class="grid grid-cols-3 gap-3">
        <x-admin.stat label="Total messages" :value="number_format($totalCount)" icon="fa-envelope" tone="indigo" />
        <x-admin.stat label="Unread" :value="number_format($unreadCount)" icon="fa-envelope-open" tone="amber" />
        <x-admin.stat label="Received today" :value="number_format($todayCount)" icon="fa-calendar-day" tone="sky" />
    </div>

    <x-admin.card flush>
        <div class="flex flex-col gap-3 border-b border-zinc-100 p-3 sm:flex-row sm:items-center sm:justify-between">
            <nav class="flex gap-1" aria-label="Filter messages">
                @foreach($tabs as $key => [$label, $count])
                    <a href="{{ route('admin.contacts.index', array_filter(['filter' => $key === 'all' ? null : $key, 'search' => $search])) }}"
                       @if($filter === $key) aria-current="page" @endif
                       class="inline-flex h-8 items-center gap-2 rounded-md px-3 text-sm font-medium {{ $filter === $key ? 'bg-zinc-900 text-white' : 'text-zinc-600 hover:bg-zinc-100' }}">
                        {{ $label }} <span class="tabular-nums text-xs {{ $filter === $key ? 'text-zinc-300' : 'text-zinc-400' }}">{{ $count }}</span>
                    </a>
                @endforeach
            </nav>
            <form method="GET" action="{{ route('admin.contacts.index') }}" class="flex gap-2 sm:w-80">
                @if($filter !== 'all')<input type="hidden" name="filter" value="{{ $filter }}">@endif
                <label class="relative flex-1">
                    <span class="sr-only">Search messages</span>
                    <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-zinc-400" aria-hidden="true"></i>
                    <input type="search" name="search" value="{{ $search }}" placeholder="Search name, email, subject"
                           class="h-8 w-full rounded-lg border border-zinc-200 pl-8 pr-3 text-sm placeholder:text-zinc-400 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                </label>
            </form>
        </div>

        @if($contacts->isEmpty())
            <x-admin.empty icon="fa-inbox"
                :title="$search ? 'No messages match “' . $search . '”' : ($filter === 'unread' ? 'You’re all caught up' : 'No messages yet')"
                :text="$search ? 'Try a different search.' : ($filter === 'unread' ? 'There are no unread messages.' : 'Messages from the website contact form will appear here.')" />
        @else
            <ul class="divide-y divide-zinc-100">
                @foreach($contacts as $c)
                    <li class="group relative flex items-start gap-3 px-4 py-3.5 hover:bg-zinc-50/60 sm:px-5 {{ $c->is_read ? '' : 'bg-indigo-50/30' }}">
                        <span class="mt-2 h-2 w-2 shrink-0 rounded-full {{ $c->is_read ? 'bg-transparent' : 'bg-indigo-500' }}" aria-hidden="true"></span>
                        <x-admin.avatar :name="$c->name" />
                        <a href="{{ route('admin.contacts.show', $c) }}" class="min-w-0 flex-1 focus:outline-none">
                            <span class="absolute inset-0" aria-hidden="true"></span>
                            <div class="flex items-baseline justify-between gap-3">
                                <p class="truncate text-sm {{ $c->is_read ? 'text-zinc-700' : 'font-semibold text-zinc-900' }}">{{ $c->name }} <span class="font-normal text-zinc-400">· {{ $c->email }}</span></p>
                                <time class="shrink-0 text-xs text-zinc-500" datetime="{{ $c->created_at?->toIso8601String() }}" title="{{ $c->created_at }}">{{ $c->created_at?->diffForHumans() }}</time>
                            </div>
                            <p class="mt-0.5 truncate text-sm {{ $c->is_read ? 'text-zinc-600' : 'font-medium text-zinc-800' }}">{{ $c->subject ?: 'No subject' }}</p>
                            <p class="mt-0.5 truncate text-sm text-zinc-500">{{ \Illuminate\Support\Str::limit($c->message, 140) }}</p>
                        </a>
                        <form method="POST" action="{{ route('admin.contacts.destroy', $c) }}" class="relative z-10 opacity-0 transition-opacity group-hover:opacity-100 focus-within:opacity-100"
                              onsubmit="return confirm('Delete this message from {{ addslashes($c->email) }}?')">
                            @csrf @method('DELETE')
                            <button class="h-8 w-8 rounded-md text-zinc-400 hover:bg-rose-50 hover:text-rose-600" aria-label="Delete message from {{ $c->email }}"><i class="fa-solid fa-trash-can text-xs" aria-hidden="true"></i></button>
                        </form>
                    </li>
                @endforeach
            </ul>
            <div class="flex flex-col gap-2 border-t border-zinc-100 px-4 py-3 text-xs text-zinc-500 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <span>Showing {{ $contacts->firstItem() }}–{{ $contacts->lastItem() }} of {{ number_format($contacts->total()) }}</span>
                @if($contacts->hasPages())<div>{{ $contacts->links('pagination::simple-tailwind') }}</div>@endif
            </div>
        @endif
    </x-admin.card>
</div>
@endsection
