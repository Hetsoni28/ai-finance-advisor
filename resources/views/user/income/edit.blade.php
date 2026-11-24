@extends('layouts.app')

@section('title', 'Edit income · FinanceAI')

@section('content')
    @include('user.partials.entry-form', ['cfg' => [
        'type' => 'income', 'mode' => 'edit', 'record' => $income,
        'action' => route('user.incomes.update', $income),
        'routes' => ['index' => route('user.incomes.index'), 'destroy' => 'user.incomes.destroy'],
    ]])
@endsection
