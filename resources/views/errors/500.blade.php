@extends('layouts.landing')

@section('title', '500 - Server Error')

@section('content')

<section class="min-h-screen flex items-center justify-center 
    bg-gradient-to-br from-red-100 via-pink-100 to-rose-100
    dark:from-slate-900 dark:via-slate-800 dark:to-slate-900">

    <div class="text-center px-6 max-w-xl">

        <h1 class="text-8xl font-extrabold text-rose-600">
            500
        </h1>

        <h2 class="text-3xl font-bold mt-6">
            Something Went Wrong
        </h2>

        <p class="mt-4 text-slate-600 dark:text-slate-300">
            Our system encountered an unexpected error.
            Please try again later.
        </p>

        <div class="mt-10">
            <a href="{{ route('home') }}"
               class="px-8 py-4 bg-indigo-600 text-white rounded-xl font-semibold shadow-lg">
                Back To Home
            </a>
        </div>

    </div>

</section>

@endsection