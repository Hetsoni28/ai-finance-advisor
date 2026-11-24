@extends('layouts.app')

@section('title', 'Family · FinanceAI')

@section('content')
@php
    $roleTone = ['owner' => 'indigo', 'admin' => 'sky', 'member' => 'zinc'];
@endphp
<div class="mx-auto max-w-6xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    <x-admin.page-header title="Family" eyebrow="Family" icon="fa-people-roof" :stamp="false"
        description="Shared budgets with the people you live with. Everyone can add income and expenses to a family.">
        <x-slot name="actions">
            <a href="{{ route('user.families.create') }}" class="inline-flex h-9 items-center gap-2 rounded-lg bg-zinc-900 px-3 text-sm font-medium text-white shadow-sm hover:bg-zinc-800">
                <i class="fa-solid fa-plus text-xs" aria-hidden="true"></i> New family
            </a>
        </x-slot>
    </x-admin.page-header>

    <x-admin.flash />

    @if($families->isEmpty())
        <x-admin.card>
            <x-admin.empty icon="fa-people-roof" title="You’re not in a family yet"
                text="Create one and invite people by email. Shared income and spending show up for everyone in it.">
                <a href="{{ route('user.families.create') }}" class="mt-4 inline-flex h-9 items-center gap-2 rounded-lg bg-zinc-900 px-3 text-sm font-medium text-white hover:bg-zinc-800"><i class="fa-solid fa-plus text-xs" aria-hidden="true"></i> Create a family</a>
            </x-admin.empty>
        </x-admin.card>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($families as $family)
                @php $role = strtolower($family->pivot->role ?? 'member'); @endphp
                <a href="{{ route('user.families.show', $family) }}" class="group flex flex-col rounded-xl border border-zinc-200 bg-white p-5 shadow-[0_1px_2px_rgba(24,24,27,.04)] transition hover:border-zinc-300 hover:shadow-[0_4px_16px_-6px_rgba(24,24,27,.12)]">
                    <div class="flex items-start justify-between gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-zinc-900 text-sm font-semibold text-white">{{ mb_strtoupper(mb_substr($family->name, 0, 1)) }}</span>
                        <x-admin.badge :tone="$roleTone[$role] ?? 'zinc'">{{ ucfirst($role) }}</x-admin.badge>
                    </div>
                    <h2 class="mt-4 font-semibold text-zinc-900">{{ $family->name }}</h2>
                    <p class="mt-1 line-clamp-2 flex-1 text-sm text-zinc-500">{{ $family->description ?: 'No description.' }}</p>
                    <div class="mt-5 flex items-center justify-between border-t border-zinc-100 pt-3 text-xs text-zinc-500">
                        <span><i class="fa-solid fa-users mr-1" aria-hidden="true"></i>{{ $family->member_count }} {{ \Illuminate\Support\Str::plural('member', $family->member_count) }}</span>
                        <span>Since {{ $family->created_at?->format('M Y') }}</span>
                    </div>
                </a>
            @endforeach
        </div>
        @if($families->hasPages())<div>{{ $families->links('pagination::simple-tailwind') }}</div>@endif

        <x-admin.card title="Recent family activity" flush>
            @if($activities->isEmpty())
                <p class="px-5 py-6 text-sm text-zinc-500">No activity in your families yet.</p>
            @else
                <ul class="divide-y divide-zinc-100">
                    @foreach($activities as $a)
                        <li class="flex items-start gap-3 px-4 py-3 sm:px-5">
                            <x-admin.avatar :name="$a->causer->name ?? 'System'" size="sm" />
                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-zinc-800">{{ $a->description }}</p>
                                <p class="mt-0.5 text-xs text-zinc-500">{{ $a->causer->name ?? 'System' }} · {{ $a->created_at?->diffForHumans() }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-admin.card>
    @endif
</div>
@endsection
