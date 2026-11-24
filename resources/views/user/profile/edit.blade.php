@extends('layouts.app')

@section('title', 'Edit profile · FinanceAI')

@section('content')
<div class="mx-auto max-w-3xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    <x-admin.page-header title="Edit details" eyebrow="Account" icon="fa-user" :stamp="false" description="Update the name and email on your account.">
        <x-slot name="actions">
            <a href="{{ route('user.profile.index') }}" class="inline-flex h-9 items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50">
                <i class="fa-solid fa-arrow-left text-xs text-zinc-400" aria-hidden="true"></i> Back
            </a>
        </x-slot>
    </x-admin.page-header>

    <x-admin.flash />

    <form method="POST" action="{{ route('user.profile.update') }}" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf @method('PATCH')
        <x-admin.card flush>
            <div class="space-y-5 p-5 sm:p-6">
                <div class="flex items-center gap-4">
                    <x-admin.avatar :name="$user->name" size="lg" />
                    <p class="text-sm text-zinc-500">Your initials are shown across FinanceAI.</p>
                </div>
                @foreach([['name', 'Full name', 'text', 'name'], ['email', 'Email address', 'email', 'email']] as [$field, $label, $type, $auto])
                    <div>
                        <label for="{{ $field }}" class="text-sm font-medium text-zinc-800">{{ $label }}</label>
                        <input id="{{ $field }}" name="{{ $field }}" type="{{ $type }}" autocomplete="{{ $auto }}" required maxlength="255"
                               value="{{ old($field, $user->{$field}) }}"
                               class="mt-2 h-11 w-full rounded-lg border px-3 text-sm text-zinc-900 focus:outline-none focus:ring-2 {{ $errors->has($field) ? 'border-rose-300 focus:ring-rose-100' : 'border-zinc-200 focus:border-indigo-400 focus:ring-indigo-100' }}">
                        @error($field)<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                @endforeach
            </div>
            <div class="flex justify-end gap-2 border-t border-zinc-100 bg-zinc-50/60 px-5 py-3 sm:px-6">
                <a href="{{ route('user.profile.index') }}" class="h-10 rounded-lg px-4 text-sm font-medium leading-10 text-zinc-600 hover:text-zinc-900">Cancel</a>
                <button :disabled="submitting" class="inline-flex h-10 items-center gap-2 rounded-lg bg-zinc-900 px-5 text-sm font-medium text-white shadow-sm hover:bg-zinc-800 disabled:opacity-60">
                    <i class="fa-solid fa-circle-notch fa-spin text-xs" x-show="submitting" x-cloak aria-hidden="true"></i> Save changes
                </button>
            </div>
        </x-admin.card>
    </form>
</div>
@endsection
