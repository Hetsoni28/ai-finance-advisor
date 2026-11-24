@extends('layouts.app')

@section('title', 'Income · FinanceAI')

@section('content')
    @include('user.partials.ledger', ['cfg' => [
        'type'        => 'income',
        'label'       => 'Income',
        'description' => 'Money coming in — salary, freelance work and everything else you record.',
        'addLabel'    => 'Add income',
        'items'       => $incomes,
        'model'       => \App\Models\Income::class,
        'dateCol'     => 'income_date',
        'titleCol'    => 'source',
        'categories'  => ['Salary', 'Freelance', 'Investments', 'Business', 'Gifts', 'Others'],
        'filteredTotal' => $stats['total'] ?? 0,
        'emptyTitle'  => 'No income recorded yet',
        'emptyText'   => 'Add your salary or any other money that comes in.',
        'routes'      => [
            'index'   => route('user.incomes.index'),
            'create'  => route('user.incomes.create'),
            'pdf'     => route('user.incomes.export.pdf'),
            'edit'    => 'user.incomes.edit',
            'destroy' => 'user.incomes.destroy',
        ],
    ]])
@endsection
