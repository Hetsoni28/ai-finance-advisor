@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-slate-100 via-white to-indigo-50 py-16 px-6">

<div class="max-w-7xl mx-auto">

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="mb-8 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl shadow animate-fade-in">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-8 bg-rose-50 border border-rose-200 text-rose-600 px-6 py-5 rounded-2xl shadow text-sm">
            <ul class="space-y-2">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-14">
        <div>
            <h1 class="text-4xl font-extrabold text-slate-900">
                Add Expense
            </h1>
            <p class="text-sm text-slate-500 mt-2">
                Intelligent Financial Processing Engine
            </p>
        </div>

        <a href="{{ route('user.expenses.index') }}"
           class="text-sm text-slate-500 hover:text-slate-900 transition">
            ← Back
        </a>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

        {{-- ================= MAIN FORM ================= --}}
        <div class="lg:col-span-2">

        <form method="POST"
              action="{{ route('user.expenses.store') }}"
              id="expenseForm"
              class="bg-white border border-slate-200
                     rounded-3xl p-12 shadow-2xl space-y-10">

            @csrf

            {{-- TYPE SWITCH --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-indigo-600 mb-4">
                    Expense Mode
                </label>

                <div class="flex gap-6">
                    @foreach(['personal'=>'👤 Personal','family'=>'👨‍👩‍👧‍👦 Family'] as $value=>$label)
                        <label class="flex-1 cursor-pointer">
                            <input type="radio"
                                   name="expense_type"
                                   value="{{ $value }}"
                                   {{ old('expense_type','personal')==$value?'checked':'' }}
                                   class="hidden peer">

                            <div class="rounded-2xl p-5 text-center font-semibold border
                                        border-slate-200
                                        peer-checked:border-indigo-600
                                        peer-checked:ring-4 peer-checked:ring-indigo-200
                                        transition duration-200">
                                {{ $label }}
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- FAMILY --}}
            <div id="familyBox" class="hidden transition-all duration-300">
                <label class="block text-xs uppercase font-bold text-slate-600 mb-3">
                    Select Family
                </label>

                <select id="familySelect"
                        name="family_id"
                        class="w-full rounded-2xl border border-slate-300 px-5 py-4">
                    <option value="">Choose family</option>

                    @foreach($families ?? [] as $family)
                        <option value="{{ $family->id }}">
                            {{ $family->name }}
                        </option>
                    @endforeach
                </select>

                <p id="familyError"
                   class="text-rose-600 text-xs mt-2 hidden">
                   Please select a family.
                </p>
            </div>

            {{-- AMOUNT --}}
            <div>
                <label class="block text-xs uppercase font-bold text-rose-600 mb-4">
                    Amount
                </label>

                <input id="amountInput"
                       type="number"
                       name="amount"
                       min="0.01"
                       step="0.01"
                       required
                       class="w-full px-6 py-5 text-3xl font-bold
                              rounded-2xl border border-slate-300 focus:ring-2 focus:ring-rose-500">

                <div class="flex justify-between mt-3 text-sm">
                    <span id="amountPreview" class="text-slate-500"></span>
                    <span id="riskScore" class="font-semibold"></span>
                </div>
            </div>

            {{-- TITLE --}}
            <div>
                <input type="text"
                       id="titleInput"
                       name="title"
                       required
                       maxlength="150"
                       placeholder="Groceries / Rent / EMI"
                       class="w-full px-6 py-5 rounded-2xl border border-slate-300">

                <div class="text-xs text-right text-slate-500 mt-2">
                    <span id="charCount">0</span> / 150
                </div>
            </div>

            {{-- DATE --}}
            <input type="date"
                   name="expense_date"
                   value="{{ now()->toDateString() }}"
                   class="w-full rounded-2xl border border-slate-300 px-5 py-4">

            {{-- CATEGORY --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach(['Food'=>'🍔','Travel'=>'🚕','Shopping'=>'🛍️','Bills'=>'🧾'] as $key=>$icon)
                    <label class="cursor-pointer">
                        <input type="radio"
                               name="category"
                               value="{{ $key }}"
                               class="hidden peer">

                        <div class="rounded-2xl p-6 text-center border border-slate-200
                                    peer-checked:border-rose-600
                                    peer-checked:ring-4 peer-checked:ring-rose-200
                                    transition">
                            <div class="text-2xl">{{ $icon }}</div>
                            <div class="font-semibold mt-2">{{ $key }}</div>
                        </div>
                    </label>
                @endforeach
            </div>

            {{-- SUBMIT --}}
            <button type="submit"
                    id="submitBtn"
                    class="w-full py-5 rounded-2xl
                           bg-gradient-to-r from-rose-500 to-pink-600
                           text-white font-semibold text-lg
                           hover:scale-[1.02] transition shadow-xl">

                <span class="submitText">Save Expense</span>
                <span class="loadingText hidden">Saving...</span>
            </button>

        </form>

        </div>

        {{-- SIDEBAR --}}
        <aside class="space-y-8">

            <div class="bg-white border border-slate-200 rounded-3xl p-8 shadow-lg">
                <h3 class="font-bold mb-4">📊 Impact Preview</h3>
                <p id="impactText" class="text-sm text-slate-600">
                    Enter amount to see impact analysis.
                </p>
            </div>

        </aside>

    </div>
</div>
</div>


<script>
document.addEventListener('DOMContentLoaded',function(){

    const typeRadios = document.querySelectorAll('[name="expense_type"]');
    const familyBox = document.getElementById('familyBox');
    const familySelect = document.getElementById('familySelect');
    const familyError = document.getElementById('familyError');
    const amountInput = document.getElementById('amountInput');
    const amountPreview = document.getElementById('amountPreview');
    const riskScore = document.getElementById('riskScore');
    const impactText = document.getElementById('impactText');
    const titleInput = document.getElementById('titleInput');
    const charCount = document.getElementById('charCount');
    const form = document.getElementById('expenseForm');

    function toggleFamily(){
        const selected = document.querySelector('[name="expense_type"]:checked').value;
        familyBox.classList.toggle('hidden', selected !== 'family');
    }

    toggleFamily();
    typeRadios.forEach(r => r.addEventListener('change', toggleFamily));

    function formatINR(num){
        return new Intl.NumberFormat('en-IN',{style:'currency',currency:'INR'}).format(num);
    }

    amountInput.addEventListener('input', function(){
        let value = parseFloat(this.value) || 0;
        amountPreview.innerText = value ? formatINR(value) : '';

        if(value > 20000){
            riskScore.innerText = "High Impact";
            riskScore.className = "text-rose-600";
        } else if(value > 5000){
            riskScore.innerText = "Medium Impact";
            riskScore.className = "text-yellow-600";
        } else {
            riskScore.innerText = "Low Impact";
            riskScore.className = "text-emerald-600";
        }

        impactText.innerText = value
            ? `This expense of ${formatINR(value)} will affect analytics instantly.`
            : "Enter amount to see impact analysis.";
    });

    titleInput.addEventListener('input', function(){
        charCount.innerText = this.value.length;
    });

    form.addEventListener('submit',function(e){

        const selected = document.querySelector('[name="expense_type"]:checked').value;

        if(selected === 'family' && !familySelect.value){
            e.preventDefault();
            familyError.classList.remove('hidden');
            return;
        }

        document.querySelector('.submitText').classList.add('hidden');
        document.querySelector('.loadingText').classList.remove('hidden');
    });

});
</script>

@endsection