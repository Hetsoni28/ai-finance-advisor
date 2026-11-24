@extends('layouts.app')

@section('title', ($contact->subject ?: 'Message') . ' · Messages · Admin')

@section('content')
<div class="mx-auto max-w-5xl space-y-6 px-4 py-6 sm:px-6 lg:px-8" x-data="{ read: {{ $contact->is_read ? 'true' : 'false' }}, busy: false, note: '' }">

    <a href="{{ route('admin.contacts.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-zinc-500 hover:text-zinc-900">
        <i class="fa-solid fa-arrow-left text-xs" aria-hidden="true"></i> All messages
    </a>

    <x-admin.flash />

    <div class="grid gap-6 lg:grid-cols-3">
        <x-admin.card class="lg:col-span-2" flush>
            <div class="border-b border-zinc-100 px-5 py-4">
                <h1 class="text-lg font-semibold text-zinc-900">{{ $contact->subject ?: 'No subject' }}</h1>
                <div class="mt-3 flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-zinc-100 text-xs font-semibold text-zinc-600">{{ strtoupper(mb_substr($contact->name, 0, 1)) }}</span>
                    <div class="min-w-0 text-sm">
                        <p class="font-medium text-zinc-900">{{ $contact->name }}</p>
                        <p class="truncate text-zinc-500">{{ $contact->email }}</p>
                    </div>
                    <time class="ml-auto shrink-0 text-xs text-zinc-500" datetime="{{ $contact->created_at?->toIso8601String() }}">{{ $contact->created_at?->format('d M Y, h:i A') }}</time>
                </div>
            </div>
            <div class="whitespace-pre-line px-5 py-5 text-[15px] leading-relaxed text-zinc-800">{{ $contact->message }}</div>
            <div class="flex flex-wrap items-center gap-2 border-t border-zinc-100 px-5 py-3">
                <a href="mailto:{{ $contact->email }}?subject={{ rawurlencode('Re: ' . ($contact->subject ?: 'Your message to FinanceAI')) }}"
                   class="inline-flex h-9 items-center gap-2 rounded-lg bg-zinc-900 px-3 text-sm font-medium text-white hover:bg-zinc-800">
                    <i class="fa-solid fa-reply text-xs" aria-hidden="true"></i> Reply by email
                </a>
                <button type="button" :disabled="busy"
                        @click="busy = true; fetch('{{ route('admin.contacts.toggleRead', $contact) }}', { method: 'PATCH', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } })
                            .then(r => r.json()).then(d => { if (d.success) { read = d.is_read; note = d.message; } else { note = d.message || 'Could not update.'; } })
                            .catch(() => note = 'Could not update.').finally(() => busy = false)"
                        class="inline-flex h-9 items-center gap-2 rounded-lg border border-zinc-200 px-3 text-sm font-medium text-zinc-700 hover:bg-zinc-50 disabled:opacity-50">
                    <i class="fa-solid text-xs text-zinc-400" :class="read ? 'fa-envelope' : 'fa-envelope-open'" aria-hidden="true"></i>
                    <span x-text="read ? 'Mark as unread' : 'Mark as read'">Mark as unread</span>
                </button>
                <span class="text-xs text-zinc-500" x-text="note" role="status"></span>
                <form method="POST" action="{{ route('admin.contacts.destroy', $contact) }}" class="ml-auto"
                      onsubmit="return confirm('Delete this message? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button class="inline-flex h-9 items-center gap-2 rounded-lg px-3 text-sm font-medium text-rose-600 hover:bg-rose-50">
                        <i class="fa-solid fa-trash-can text-xs" aria-hidden="true"></i> Delete
                    </button>
                </form>
            </div>
        </x-admin.card>

        <x-admin.card title="Details">
            <dl class="space-y-4 text-sm">
                <div>
                    <dt class="text-xs text-zinc-500">Status</dt>
                    <dd class="mt-1">
                        <span x-show="read"><x-admin.badge tone="zinc">Read</x-admin.badge></span>
                        <span x-show="!read" x-cloak><x-admin.badge tone="indigo" dot>Unread</x-admin.badge></span>
                    </dd>
                </div>
                <div><dt class="text-xs text-zinc-500">Received</dt><dd class="mt-1 text-zinc-800">{{ $contact->created_at?->format('l, d M Y · h:i A') }}</dd></div>
                <div><dt class="text-xs text-zinc-500">IP address</dt><dd class="mt-1 font-mono text-[13px] text-zinc-800">{{ $contact->ip_address ?: '—' }}</dd></div>
                <div><dt class="text-xs text-zinc-500">Browser</dt><dd class="mt-1 break-words text-[13px] text-zinc-600">{{ $contact->user_agent ?: '—' }}</dd></div>
                @if($contact->location_label || ($contact->latitude && $contact->longitude))
                    <div>
                        <dt class="text-xs text-zinc-500">Location (shared by sender)</dt>
                        <dd class="mt-1 text-zinc-800">
                            {{ $contact->location_label ?: number_format((float) $contact->latitude, 4) . ', ' . number_format((float) $contact->longitude, 4) }}
                            @if($contact->latitude && $contact->longitude)
                                <a href="https://www.google.com/maps?q={{ $contact->latitude }},{{ $contact->longitude }}" target="_blank" rel="noopener noreferrer" class="ml-1 text-xs font-medium text-indigo-600 hover:text-indigo-700">Map</a>
                            @endif
                        </dd>
                    </div>
                @endif
            </dl>
        </x-admin.card>
    </div>
</div>
@endsection
