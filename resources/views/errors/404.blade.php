@extends('layouts.landing')

@section('title', '404 - Page Not Found')

@section('content')

<section class="min-h-screen flex items-center justify-center bg-gradient-to-br 
    from-indigo-100 via-purple-100 to-pink-100 
    dark:from-slate-900 dark:via-slate-800 dark:to-slate-900">

    <div class="text-center px-6">

        <h1 class="text-9xl font-extrabold text-indigo-600">
            404
        </h1>

        <h2 class="text-3xl font-bold mt-6">
            Page Not Found
        </h2>

        <p class="mt-4 text-slate-600 dark:text-slate-300">
            The page you are looking for does not exist.
        </p>

        <div class="mt-10">
            <a href="{{ route('home') }}"
               class="px-8 py-4 bg-indigo-600 text-white rounded-xl font-semibold shadow-lg hover:scale-105 transition">
                Go Home
            </a>
        </div>

    </div>

</section>

@endsection