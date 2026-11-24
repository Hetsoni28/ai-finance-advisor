@extends('layouts.app')

@section('title', 'Edit expense · FinanceAI')

@section('content')
    @include('user.partials.entry-form', ['cfg' => [
        'type' => 'expense', 'mode' => 'edit', 'record' => $expense,
        'action' => route('user.expenses.update', $expense),
        'routes' => ['index' => route('user.expenses.index'), 'destroy' => 'user.expenses.destroy'],
    ]])
@endsection
