@extends('layouts.app')

@section('title', 'Add income · FinanceAI')

@section('content')
    @include('user.partials.entry-form', ['cfg' => [
        'type' => 'income', 'mode' => 'create', 'record' => null,
        'action' => route('user.incomes.store'),
        'families' => $families ?? collect(),
        'recent' => $recentIncome ?? collect(),
        'routes' => ['index' => route('user.incomes.index')],
    ]])
@endsection
