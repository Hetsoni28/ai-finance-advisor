@extends('layouts.app')

@section('content')

@php
    $hasShowRoute = Route::has('user.families.show');
    $hasCreateRoute = Route::has('user.families.create');
@endphp

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 
            dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 
            px-6 py-14 transition-colors duration-300">

<div class="max-w-7xl mx-auto">

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="mb-10 relative overflow-hidden rounded-2xl 
                    bg-emerald-500/10 border border-emerald-500/20 
                    text-emerald-700 dark:text-emerald-400
                    px-6 py-4 shadow-lg backdrop-blur">
            <div class="flex items-center gap-3">
                <span class="text-lg">✔</span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif


    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-14">

        <div>
            <h1 class="text-4xl font-extrabold tracking-tight 
                       text-slate-900 dark:text-white">
                Family Spaces
            </h1>
            <p class="mt-3 text-slate-500 dark:text-slate-400 text-lg">
                Secure shared financial collaboration environments.
            </p>
        </div>

        @if($hasCreateRoute)
            <a href="{{ route('user.families.create') }}"
               class="group inline-flex items-center gap-2 px-7 py-3 rounded-2xl
                      bg-gradient-to-r from-emerald-500 to-cyan-500
                      text-white font-semibold shadow-xl
                      hover:scale-105 active:scale-95 transition duration-300
                      focus:outline-none focus:ring-4 focus:ring-emerald-300/50">
                <span class="text-lg">+</span>
                Create Family
            </a>
        @endif
    </div>


    {{-- EMPTY STATE --}}
    @if($families->isEmpty())

        <div class="bg-white dark:bg-slate-900 rounded-3xl 
                    border border-slate-200 dark:border-slate-800
                    shadow-xl p-16 text-center max-w-2xl mx-auto">

            <div class="text-6xl mb-6">👨‍👩‍👧‍👦</div>

            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
                No Family Spaces Yet
            </h2>

            <p class="text-slate-500 dark:text-slate-400 mt-4">
                Create your first shared budgeting workspace and collaborate securely.
            </p>

            @if($hasCreateRoute)
                <a href="{{ route('user.families.create') }}"
                   class="inline-block mt-8 px-8 py-3 rounded-2xl
                          bg-emerald-600 text-white font-semibold
                          hover:bg-emerald-500 transition shadow-lg
                          focus:outline-none focus:ring-4 focus:ring-emerald-300/40">
                    Create First Family
                </a>
            @endif
        </div>

    @else

        {{-- QUICK STATS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-14">

            <div class="bg-white dark:bg-slate-900 rounded-2xl 
                        border border-slate-200 dark:border-slate-800
                        p-6 shadow-lg hover:shadow-xl transition">
                <div class="text-sm text-slate-500 dark:text-slate-400 mb-2">
                    👥 Total Families
                </div>
                <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                    {{ $families->count() }}
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl 
                        border border-slate-200 dark:border-slate-800
                        p-6 shadow-lg hover:shadow-xl transition">
                <div class="text-sm text-slate-500 dark:text-slate-400 mb-2">
                    🔐 Data Isolation
                </div>
                <div class="text-lg font-semibold text-slate-800 dark:text-white">
                    Personal Finances Secure
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl 
                        border border-slate-200 dark:border-slate-800
                        p-6 shadow-lg hover:shadow-xl transition">
                <div class="text-sm text-slate-500 dark:text-slate-400 mb-2">
                    🤖 AI Analytics
                </div>
                <div class="text-lg font-semibold text-slate-800 dark:text-white">
                    Real-time Smart Insights
                </div>
            </div>

        </div>


        {{-- FAMILY GRID --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            @foreach($families as $family)

                @php
                    $role = optional($family->pivot)->role ?? 'member';
                @endphp

                @if($hasShowRoute)
                <a href="{{ route('user.families.show', $family->id) }}"
                   class="group relative overflow-hidden
                          rounded-3xl p-8
                          bg-gradient-to-br from-slate-900 to-slate-800
                          dark:from-slate-800 dark:to-slate-900
                          text-white shadow-2xl
                          hover:scale-[1.02] hover:shadow-3xl
                          transition-all duration-300">

                    {{-- Hover Glow --}}
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-20
                                bg-gradient-to-r from-indigo-500 to-cyan-500
                                transition duration-500"></div>

                    <div class="relative z-10">

                        <div class="flex justify-between items-center mb-6">

                            <h3 class="text-xl font-bold truncate">
                                {{ $family->name }}
                            </h3>

                            <span class="text-xs px-3 py-1 rounded-full
                                {{ $role === 'owner'
                                    ? 'bg-emerald-500'
                                    : 'bg-slate-600' }}">
                                {{ ucfirst($role) }}
                            </span>
                        </div>

                        <p class="text-slate-300 text-sm mb-8">
                            Shared income, expense management and real-time analytics.
                        </p>

                        <div class="flex justify-between text-xs text-slate-400">
                            <span>
                                Created {{ optional($family->created_at)->format('M Y') }}
                            </span>
                            <span class="group-hover:text-white transition">
                                Open →
                            </span>
                        </div>

                    </div>
                </a>
                @endif

            @endforeach
        </div>


        {{-- PAGINATION --}}
        @if(method_exists($families, 'links'))
            <div class="mt-16">
                {{ $families->links('pagination::tailwind') }}
            </div>
        @endif

    @endif

</div>
</div>

@endsection