@extends('layouts.app')

@section('title', 'Add expense · FinanceAI')

@section('content')
    @include('user.partials.entry-form', ['cfg' => [
        'type' => 'expense', 'mode' => 'create', 'record' => null,
        'action' => route('user.expenses.store'),
        'families' => $families ?? collect(),
        'recent' => $recentExpenses ?? collect(),
        'routes' => ['index' => route('user.expenses.index')],
    ]])
@endsection
