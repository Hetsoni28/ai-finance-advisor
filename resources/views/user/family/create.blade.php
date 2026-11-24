@extends('layouts.app')

@section('title', 'New family · FinanceAI')

@section('content')
<div class="mx-auto max-w-3xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    <x-admin.page-header title="New family" eyebrow="Family" icon="fa-people-roof" :stamp="false" description="You’ll be the owner. You can invite people by email straight after.">
        <x-slot name="actions">
            <a href="{{ route('user.families.index') }}" class="inline-flex h-9 items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50">
                <i class="fa-solid fa-arrow-left text-xs text-zinc-400" aria-hidden="true"></i> Back
            </a>
        </x-slot>
    </x-admin.page-header>

    <x-admin.flash />

    <form method="POST" action="{{ route('user.families.store') }}" x-data="{ submitting: false, desc: {{ json_encode(old('description', '')) }} }" @submit="submitting = true">
        @csrf
        <x-admin.card flush>
            <div class="space-y-5 p-5 sm:p-6">
                <div>
                    <label for="name" class="text-sm font-medium text-zinc-800">Family name</label>
                    <input id="name" name="name" type="text" required maxlength="255" autofocus value="{{ old('name') }}" placeholder="e.g. The Sharma household"
                           class="mt-2 h-11 w-full rounded-lg border px-3 text-sm text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-2 {{ $errors->has('name') ? 'border-rose-300 focus:ring-rose-100' : 'border-zinc-200 focus:border-indigo-400 focus:ring-indigo-100' }}">
                    @error('name')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <div class="flex justify-between"><label for="description" class="text-sm font-medium text-zinc-800">Description <span class="font-normal text-zinc-400">(optional)</span></label><span class="text-xs tabular-nums text-zinc-400" x-text="desc.length + '/500'"></span></div>
                    <textarea id="description" name="description" rows="3" maxlength="500" x-model="desc" placeholder="What this family budget is for"
                              class="mt-2 w-full rounded-lg border border-zinc-200 px-3 py-2.5 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100"></textarea>
                    @error('description')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <ul class="space-y-2 rounded-lg bg-zinc-50 p-4 text-sm text-zinc-600">
                    <li class="flex gap-2"><i class="fa-solid fa-crown mt-0.5 text-xs text-indigo-500" aria-hidden="true"></i>You’ll be the <strong class="font-medium text-zinc-800">owner</strong> — you manage members and invites.</li>
                    <li class="flex gap-2"><i class="fa-solid fa-envelope mt-0.5 text-xs text-indigo-500" aria-hidden="true"></i>People join with a one-click link sent to their email.</li>
                    <li class="flex gap-2"><i class="fa-solid fa-scale-balanced mt-0.5 text-xs text-indigo-500" aria-hidden="true"></i>Shared income and expenses stay separate from everyone’s personal records.</li>
                </ul>
            </div>
            <div class="flex justify-end gap-2 border-t border-zinc-100 bg-zinc-50/60 px-5 py-3 sm:px-6">
                <a href="{{ route('user.families.index') }}" class="h-10 rounded-lg px-4 text-sm font-medium leading-10 text-zinc-600 hover:text-zinc-900">Cancel</a>
                <button :disabled="submitting" class="inline-flex h-10 items-center gap-2 rounded-lg bg-zinc-900 px-5 text-sm font-medium text-white shadow-sm hover:bg-zinc-800 disabled:opacity-60">
                    <i class="fa-solid fa-circle-notch fa-spin text-xs" x-show="submitting" x-cloak aria-hidden="true"></i> Create family
                </button>
            </div>
        </x-admin.card>
    </form>
</div>
@endsection
