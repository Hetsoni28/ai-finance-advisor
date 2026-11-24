@extends('layouts.app')

@section('title', 'Expenses · FinanceAI')

@section('content')
    @include('user.partials.ledger', ['cfg' => [
        'type'        => 'expense',
        'label'       => 'Expenses',
        'description' => 'Everything you spend, by category and month.',
        'addLabel'    => 'Add expense',
        'items'       => $expenses,
        'model'       => \App\Models\Expense::class,
        'dateCol'     => 'expense_date',
        'titleCol'    => 'title',
        'categories'  => ['Food', 'Travel', 'Bills', 'Shopping', 'Health', 'Others'],
        'filteredTotal' => $total ?? 0,
        'emptyTitle'  => 'No expenses recorded yet',
        'emptyText'   => 'Add what you spend — food, bills, travel — to see where your money goes.',
        'routes'      => [
            'index'   => route('user.expenses.index'),
            'create'  => route('user.expenses.create'),
            'pdf'     => route('user.expenses.export.pdf'),
            'edit'    => 'user.expenses.edit',
            'destroy' => 'user.expenses.destroy',
        ],
    ]])
@endsection
