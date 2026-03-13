@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto px-6 py-14">

    <div class="bg-white shadow-xl rounded-xl p-8">

        <h1 class="text-2xl font-bold mb-6">
            Income Details
        </h1>

        <div class="space-y-4">

            <div>
                <p class="text-sm text-gray-500">Source</p>
                <p class="font-semibold">{{ $income->source }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Amount</p>
                <p class="font-semibold text-green-600">
                    ₹{{ number_format($income->amount, 2) }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Date</p>
                <p class="font-semibold">
                    {{ $income->income_date->format('d M Y') }}
                </p>
            </div>

        </div>

        <div class="mt-8">
            <a href="{{ route('user.incomes.index') }}"
               class="px-4 py-2 bg-gray-200 rounded-lg">
                ← Back
            </a>
        </div>

    </div>

</div>

@endsection