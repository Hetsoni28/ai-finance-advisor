@extends('layouts.app')

@section('title', 'Activity log · Admin · FinanceAI')

@section('content')
@php
    $a = $analysis ?? [];
    $ranges = [1 => '24 hours', 7 => '7 days', 30 => '30 days', 90 => '90 days', 365 => '12 months'];
    $toneFor = ['Critical' => 'rose', 'Warning' => 'amber', 'Secure' => 'emerald', 'Info' => 'zinc'];
    $labelFor = ['Critical' => 'Removal / block', 'Warning' => 'Change', 'Secure' => 'Created / sign-in', 'Info' => 'Other'];
    $filtered = request()->filled('search') || request()->filled('severity');
@endphp

<div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    <x-admin.page-header title="Activity log" :description="'Everything users and admins did in the last ' . ($ranges[$days] ?? $days . ' days') . '.'" />

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <x-admin.stat label="Events" :value="number_format($a['totalLogs'] ?? 0)" icon="fa-list-check" tone="indigo" />
        <x-admin.stat label="Changes" :value="number_format($a['updateCount'] ?? 0)" icon="fa-pen" tone="amber" hint="Edits and updates" />
        <x-admin.stat label="Deletions" :value="number_format($a['deleteCount'] ?? 0)" icon="fa-trash-can" tone="rose" />
        <x-admin.stat label="Blocks & failures" :value="number_format($a['criticalCount'] ?? 0)" icon="fa-triangle-exclamation" tone="rose" />
    </div>

    <x-admin.card flush>
        <form method="GET" action="{{ route('admin.activities.index') }}" class="flex flex-col gap-2 border-b border-zinc-100 p-3 lg:flex-row lg:items-center">
            <div class="flex gap-1 overflow-x-auto" role="group" aria-label="Time range">
                @foreach($ranges as $value => $label)
                    <a href="{{ route('admin.activities.index', array_filter(['days' => $value, 'search' => request('search'), 'severity' => request('severity')])) }}"
                       @if((int) $days === $value) aria-current="true" @endif
                       class="inline-flex h-8 shrink-0 items-center rounded-md px-3 text-sm font-medium {{ (int) $days === $value ? 'bg-zinc-900 text-white' : 'text-zinc-600 hover:bg-zinc-100' }}">{{ $label }}</a>
                @endforeach
            </div>
            <input type="hidden" name="days" value="{{ $days }}">
            <label class="relative flex-1 lg:ml-2">
                <span class="sr-only">Search activity</span>
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-zinc-400" aria-hidden="true"></i>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search event, user name or email"
                       class="h-8 w-full rounded-lg border border-zinc-200 pl-8 pr-3 text-sm placeholder:text-zinc-400 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
            </label>
            <select name="severity" aria-label="Type" class="h-8 rounded-lg border border-zinc-200 bg-white px-3 text-sm text-zinc-700 focus:border-indigo-400 focus:outline-none">
                <option value="">All events</option>
                <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Removals & blocks</option>
                <option value="info" {{ request('severity') === 'info' ? 'selected' : '' }}>Everything else</option>
            </select>
            <button class="h-8 rounded-lg bg-zinc-900 px-4 text-sm font-medium text-white hover:bg-zinc-800">Apply</button>
            @if($filtered)
                <a href="{{ route('admin.activities.index', ['days' => $days]) }}" class="h-8 px-2 text-sm font-medium leading-8 text-zinc-500 hover:text-zinc-900">Clear</a>
            @endif
        </form>

        @if($activities->isEmpty())
            <x-admin.empty icon="fa-clock-rotate-left" :title="$filtered ? 'No events match these filters' : 'No activity in this period'"
                :text="$filtered ? 'Try a different search or a longer time range.' : 'Try a longer time range.'" />
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-100 text-left text-xs text-zinc-500">
                            <th class="px-4 py-2.5 font-medium sm:px-5">When</th>
                            <th class="px-4 py-2.5 font-medium">Who</th>
                            <th class="px-4 py-2.5 font-medium">What happened</th>
                            <th class="px-4 py-2.5 font-medium sm:px-5">Type</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach($activities as $act)
                            @php $sev = $severities[$act->id]['label'] ?? 'Info'; @endphp
                            <tr class="align-top hover:bg-zinc-50/60">
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <p class="text-zinc-800">{{ $act->created_at?->format('d M, h:i A') }}</p>
                                    <p class="text-xs text-zinc-500">{{ $act->created_at?->diffForHumans() }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @if($act->user)
                                        <p class="font-medium text-zinc-900">{{ $act->user->name }}</p>
                                        <p class="text-xs text-zinc-500">{{ $act->user->email }}</p>
                                    @else
                                        <p class="text-zinc-500">System or deleted user</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-zinc-800">{{ $act->description }}</td>
                                <td class="px-4 py-3 sm:px-5"><x-admin.badge :tone="$toneFor[$sev] ?? 'zinc'">{{ $labelFor[$sev] ?? $sev }}</x-admin.badge></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex flex-col gap-2 border-t border-zinc-100 px-4 py-3 text-xs text-zinc-500 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <span>Showing {{ $activities->firstItem() }}–{{ $activities->lastItem() }} of {{ number_format($activities->total()) }}</span>
                @if($activities->hasPages())<div>{{ $activities->links('pagination::simple-tailwind') }}</div>@endif
            </div>
        @endif
    </x-admin.card>
</div>
@endsection
