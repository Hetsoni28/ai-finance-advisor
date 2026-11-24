@extends('layouts.app')

@section('title', 'Change password · FinanceAI')

@section('content')
<div class="mx-auto max-w-3xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    <x-admin.page-header title="Change password" eyebrow="Account" icon="fa-user" :stamp="false" description="Use a password you don’t use anywhere else.">
        <x-slot name="actions">
            <a href="{{ route('user.profile.index') }}" class="inline-flex h-9 items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50">
                <i class="fa-solid fa-arrow-left text-xs text-zinc-400" aria-hidden="true"></i> Back
            </a>
        </x-slot>
    </x-admin.page-header>

    <x-admin.flash />

    <form method="POST" action="{{ route('user.profile.password.update') }}"
          x-data="{ pw: '', confirm: '', show: false, submitting: false,
                    get checks() { return [
                        ['At least 8 characters', this.pw.length >= 8],
                        ['Upper and lower case letters', /[a-z]/.test(this.pw) && /[A-Z]/.test(this.pw)],
                        ['A number', /\d/.test(this.pw)],
                        ['A symbol, like ! or #', /[^A-Za-z0-9]/.test(this.pw)],
                    ] },
                    get valid() { return this.checks.every(c => c[1]) && this.pw === this.confirm } }"
          @submit="submitting = true">
        @csrf @method('PATCH')
        <x-admin.card flush>
            <div class="space-y-5 p-5 sm:p-6">
                <div>
                    <label for="current_password" class="text-sm font-medium text-zinc-800">Current password</label>
                    <input id="current_password" name="current_password" :type="show ? 'text' : 'password'" autocomplete="current-password" required
                           class="mt-2 h-11 w-full rounded-lg border px-3 text-sm focus:outline-none focus:ring-2 {{ $errors->has('current_password') ? 'border-rose-300 focus:ring-rose-100' : 'border-zinc-200 focus:border-indigo-400 focus:ring-indigo-100' }}">
                    @error('current_password')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="password" class="text-sm font-medium text-zinc-800">New password</label>
                        <input id="password" name="password" :type="show ? 'text' : 'password'" autocomplete="new-password" required x-model="pw"
                               class="mt-2 h-11 w-full rounded-lg border px-3 text-sm focus:outline-none focus:ring-2 {{ $errors->has('password') ? 'border-rose-300 focus:ring-rose-100' : 'border-zinc-200 focus:border-indigo-400 focus:ring-indigo-100' }}">
                        @error('password')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="text-sm font-medium text-zinc-800">Confirm new password</label>
                        <input id="password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" autocomplete="new-password" required x-model="confirm"
                               class="mt-2 h-11 w-full rounded-lg border border-zinc-200 px-3 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                        <p class="mt-1.5 text-xs text-rose-600" x-show="confirm && pw !== confirm" x-cloak>Passwords don’t match.</p>
                    </div>
                </div>
                <ul class="grid gap-2 rounded-lg bg-zinc-50 p-4 text-sm sm:grid-cols-2" aria-label="Password requirements">
                    <template x-for="c in checks" :key="c[0]">
                        <li class="flex items-center gap-2" :class="c[1] ? 'text-emerald-700' : 'text-zinc-500'">
                            <i class="fa-solid text-xs" :class="c[1] ? 'fa-circle-check' : 'fa-circle'" aria-hidden="true"></i><span x-text="c[0]"></span>
                        </li>
                    </template>
                </ul>
                <label class="inline-flex items-center gap-2 text-sm text-zinc-600"><input type="checkbox" x-model="show" class="rounded border-zinc-300"> Show passwords</label>
            </div>
            <div class="flex justify-end gap-2 border-t border-zinc-100 bg-zinc-50/60 px-5 py-3 sm:px-6">
                <a href="{{ route('user.profile.index') }}" class="h-10 rounded-lg px-4 text-sm font-medium leading-10 text-zinc-600 hover:text-zinc-900">Cancel</a>
                <button :disabled="!valid || submitting" class="inline-flex h-10 items-center gap-2 rounded-lg bg-zinc-900 px-5 text-sm font-medium text-white shadow-sm hover:bg-zinc-800 disabled:opacity-50">
                    <i class="fa-solid fa-circle-notch fa-spin text-xs" x-show="submitting" x-cloak aria-hidden="true"></i> Update password
                </button>
            </div>
        </x-admin.card>
    </form>
</div>
@endsection
