@extends('layouts.app')

@section('content')

@php
    abort_unless(Route::has('user.families.store'), 404);
    abort_unless(Route::has('user.families.index'), 404);

    $routeStore = route('user.families.store');
    $routeIndex = route('user.families.index');
@endphp

<div class="min-h-screen bg-gradient-to-br from-slate-100 via-white to-indigo-100 
            flex items-center justify-center px-4 py-20">

    <div class="w-full max-w-2xl">

        {{-- SUCCESS --}}
        @if(session('success'))
            <div class="mb-8 rounded-2xl border border-emerald-200
                        bg-emerald-50 px-6 py-4 text-sm text-emerald-700
                        shadow animate-fade-in">
                {{ session('success') }}
            </div>
        @endif

        <div class="relative bg-white/80 backdrop-blur-xl
                    border border-slate-200
                    rounded-3xl shadow-2xl px-12 py-16">

            {{-- HEADER --}}
            <div class="text-center mb-14">

                <div class="mx-auto mb-6 h-20 w-20 rounded-3xl
                            bg-gradient-to-br from-indigo-600 to-cyan-500
                            flex items-center justify-center
                            text-3xl text-white shadow-xl">
                    🏠
                </div>

                <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">
                    Create Family Workspace
                </h1>

                <p class="mt-3 text-sm text-slate-500">
                    Secure shared financial collaboration environment
                </p>

            </div>

            {{-- ERROR BLOCK --}}
            @if ($errors->any())
                <div class="mb-8 rounded-2xl p-5
                            bg-rose-50 border border-rose-200
                            text-rose-600 text-sm">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORM --}}
            <form method="POST"
                  action="{{ $routeStore }}"
                  id="familyForm"
                  class="space-y-10">
                @csrf

                {{-- FAMILY NAME --}}
                <div>

                    <label class="block text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">
                        Workspace Name
                    </label>

                    <input type="text"
                           id="name"
                           name="name"
                           maxlength="100"
                           required
                           value="{{ old('name') }}"
                           placeholder="e.g. Sharma Family Finance"
                           class="w-full rounded-2xl px-6 py-4 text-lg
                                  border border-slate-300
                                  focus:border-indigo-600
                                  focus:ring-4 focus:ring-indigo-200
                                  transition duration-200 shadow-sm">

                    <div class="flex justify-between mt-2 text-xs text-slate-400">
                        <span id="nameStatus">Enter a meaningful name</span>
                        <span><span id="charCount">0</span>/100</span>
                    </div>
                </div>

                {{-- PREVIEW CARD --}}
                <div id="previewCard"
                     class="rounded-2xl border border-slate-200 p-6 bg-slate-50 hidden">

                    <h3 class="text-sm font-semibold text-slate-600 mb-2">
                        Workspace Preview
                    </h3>

                    <div class="text-lg font-bold text-slate-800"
                         id="previewName">
                        —
                    </div>

                    <div class="text-xs text-slate-400 mt-1">
                        Secure • Role-Based • Auditable
                    </div>
                </div>

                {{-- QUICK SUGGESTIONS --}}
                <div class="flex flex-wrap gap-3 text-xs">
                    @foreach(['Family','Household','Finance Hub','Budget Group','Wealth Circle'] as $suggest)
                        <button type="button"
                                class="suggest-chip"
                                data-suggest="{{ $suggest }}">
                            {{ $suggest }}
                        </button>
                    @endforeach
                </div>

                {{-- SECURITY INFO --}}
                <div class="rounded-2xl bg-gradient-to-r from-slate-50 to-indigo-50
                            border border-slate-200 p-6 text-xs text-slate-600">
                    🔐 <strong>Enterprise Security</strong>
                    <ul class="mt-3 space-y-1">
                        <li>• Personal & family data isolation</li>
                        <li>• Role-based permissions</li>
                        <li>• Audit activity tracking</li>
                        <li>• Financial encryption layer</li>
                    </ul>
                </div>

                {{-- ACTIONS --}}
                <div class="flex items-center justify-between pt-6 border-t border-slate-200">

                    <a href="{{ $routeIndex }}"
                       class="text-sm font-medium text-slate-500 hover:text-slate-900 transition">
                        ← Back
                    </a>

                    <button id="submitBtn"
                            type="submit"
                            class="relative px-12 py-4 rounded-2xl
                                   bg-gradient-to-r from-indigo-600 to-cyan-500
                                   text-white font-semibold text-lg
                                   hover:scale-105 active:scale-95
                                   transition shadow-xl">

                        <span class="submit-label">Create Workspace</span>
                        <span class="loading-label hidden">Creating...</span>

                    </button>

                </div>

            </form>

            <p class="mt-12 text-center text-xs text-slate-400">
                Next → Invite members → Add income → View family analytics
            </p>

        </div>
    </div>
</div>

<style>
.suggest-chip {
    padding: 7px 16px;
    border-radius: 999px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    transition: .2s;
}
.suggest-chip:hover {
    background: #e0e7ff;
}
.animate-fade-in {
    animation: fadeIn .4s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('familyForm');
    const submitBtn = document.getElementById('submitBtn');
    const nameInput = document.getElementById('name');
    const charCount = document.getElementById('charCount');
    const previewCard = document.getElementById('previewCard');
    const previewName = document.getElementById('previewName');
    const nameStatus = document.getElementById('nameStatus');

    charCount.innerText = nameInput.value.length;

    nameInput.addEventListener('input', function(){
        const value = this.value.trim();
        charCount.innerText = value.length;

        if(value.length > 2){
            previewCard.classList.remove('hidden');
            previewName.innerText = value;
            nameStatus.innerText = "Looks good ✔";
            nameStatus.classList.add('text-emerald-500');
        } else {
            previewCard.classList.add('hidden');
            nameStatus.innerText = "Minimum 3 characters required";
            nameStatus.classList.remove('text-emerald-500');
        }
    });

    form.addEventListener('submit', function () {
        submitBtn.disabled = true;
        submitBtn.querySelector('.submit-label').classList.add('hidden');
        submitBtn.querySelector('.loading-label').classList.remove('hidden');
    });

    document.querySelectorAll('[data-suggest]').forEach(btn => {
        btn.addEventListener('click', function () {
            const text = this.dataset.suggest;
            nameInput.value = nameInput.value
                ? nameInput.value.trim() + ' ' + text
                : 'My ' + text;
            nameInput.dispatchEvent(new Event('input'));
            nameInput.focus();
        });
    });

});
</script>

@endsection