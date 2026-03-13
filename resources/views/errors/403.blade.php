@extends('layouts.landing')

@section('title', '403 - Forbidden')

@section('content')

<section class="min-h-screen flex items-center justify-center 
    bg-gradient-to-br from-yellow-100 via-orange-100 to-red-100
    dark:from-slate-900 dark:via-slate-800 dark:to-slate-900">

    <div class="text-center px-6">

        <h1 class="text-8xl font-extrabold text-orange-600">
            403
        </h1>

        <h2 class="text-3xl font-bold mt-6">
            Access Denied
        </h2>

        <p class="mt-4 text-slate-600 dark:text-slate-300">
            You don't have permission to access this page.
        </p>

        <div class="mt-10 flex justify-center gap-4">
            <a href="{{ route('home') }}"
               class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold">
                Go Home
            </a>
            <a href="{{ url()->previous() }}"
               class="px-6 py-3 border rounded-xl font-semibold">
                Go Back
            </a>
        </div>

    </div>

</section>

@endsection