@extends('layouts.app')

@section('content')

@php
$originalAmount = (float) $income->amount;
@endphp

<div class="max-w-4xl mx-auto px-6 py-16">

    {{-- HEADER --}}
    <div class="mb-14">
        <h1 class="text-4xl font-extrabold tracking-tight">
            Edit Income
        </h1>
        <p class="text-slate-500 mt-3">
            Securely update your income entry with real-time financial intelligence preview.
        </p>
    </div>

    <form id="editIncomeForm"
          method="POST"
          action="{{ route('user.incomes.update', $income->id) }}"
          class="bg-white dark:bg-slate-900
                 border border-slate-200 dark:border-slate-700
                 shadow-2xl rounded-3xl p-12 space-y-12 transition">

        @csrf
        @method('PUT')

        {{-- AMOUNT --}}
        <div>
            <label class="label-green">Amount</label>

            <div class="relative mt-3">
                <span class="currency-symbol">₹</span>

                <input id="amountInput"
                       type="number"
                       step="0.01"
                       name="amount"
                       value="{{ old('amount', $income->amount) }}"
                       required
                       class="input-xl @error('amount') ring-2 ring-rose-400 @enderror">
            </div>

            <div class="flex justify-between items-center mt-3">
                <p id="amountPreview" class="text-sm text-slate-500"></p>
                <span id="changeBadge" class="change-badge hidden"></span>
            </div>
        </div>

        {{-- SOURCE --}}
        <div>
            <label class="label-blue">Source</label>

            <input id="sourceInput"
                   type="text"
                   name="source"
                   value="{{ old('source', $income->source) }}"
                   maxlength="60"
                   required
                   class="input-lg @error('source') ring-2 ring-rose-400 @enderror">

            <div class="flex justify-between text-xs text-slate-500 mt-2">
                <span>Clear description improves tracking</span>
                <span id="charCount">0 / 60</span>
            </div>
        </div>

        {{-- DATE --}}
        <div>
            <label class="label-indigo">Income Date</label>

            <input type="date"
                   name="income_date"
                   value="{{ old('income_date', optional($income->income_date)->format('Y-m-d')) }}"
                   required
                   class="input-lg @error('income_date') ring-2 ring-rose-400 @enderror">
        </div>

        {{-- SMART IMPACT CARD --}}
        <div class="impact-card">

            <h4 class="font-bold mb-3 flex items-center gap-2">
                🤖 Smart Financial Impact
            </h4>

            <p id="aiText" class="impact-text">
                Adjust the amount to preview impact on financial performance.
            </p>

            <div class="mt-4">
                <p class="text-xs uppercase tracking-wider text-slate-400">
                    Projected Change
                </p>
                <h3 id="projectedValue"
                    class="text-2xl font-bold mt-1 text-emerald-600">
                    ₹0.00
                </h3>
            </div>

        </div>

        {{-- ACTIONS --}}
        <div class="flex flex-col md:flex-row gap-5 pt-6">

            <button id="saveBtn"
                type="submit"
                class="btn-primary">
                <span class="btnText">Save Changes</span>
                <span class="loadingText hidden">Saving...</span>
            </button>

            <a href="{{ route('user.incomes.index') }}"
               class="btn-outline">
                Cancel
            </a>

        </div>

    </form>
</div>


{{-- STYLES --}}
<style>
.label-green{font-size:.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#059669}
.label-blue{font-size:.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#2563eb}
.label-indigo{font-size:.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#4f46e5}

.currency-symbol{
    position:absolute;
    left:1.25rem;
    top:50%;
    transform:translateY(-50%);
    color:#94a3b8;
}

.input-xl{
    width:100%;
    padding:1.4rem 1.5rem 1.4rem 3rem;
    font-size:1.7rem;
    font-weight:700;
    border:1px solid #e2e8f0;
    border-radius:1rem;
}
.input-lg{
    width:100%;
    padding:1.2rem 1.5rem;
    font-size:1rem;
    border:1px solid #e2e8f0;
    border-radius:1rem;
}

.impact-card{
    background:linear-gradient(135deg,#f8fafc,#ffffff);
    padding:2rem;
    border-radius:1rem;
    border:1px solid #e2e8f0;
    box-shadow:0 8px 30px rgba(0,0,0,.04);
}

.change-badge{
    font-size:.75rem;
    font-weight:600;
    padding:.3rem .7rem;
    border-radius:999px;
}

.btn-primary{
    flex:1;
    padding:1.2rem;
    border-radius:1rem;
    background:linear-gradient(135deg,#10b981,#3b82f6);
    color:white;
    font-weight:600;
    transition:.3s;
}
.btn-primary:hover{transform:scale(1.02)}

.btn-outline{
    flex:1;
    padding:1.2rem;
    border-radius:1rem;
    border:1px solid #e2e8f0;
    text-align:center;
}
</style>


{{-- SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded',function(){

const amountInput = document.getElementById('amountInput');
const preview = document.getElementById('amountPreview');
const aiText = document.getElementById('aiText');
const badge = document.getElementById('changeBadge');
const projected = document.getElementById('projectedValue');
const form = document.getElementById('editIncomeForm');
const saveBtn = document.getElementById('saveBtn');

const originalAmount = {{ $originalAmount }};

function formatINR(num){
    return new Intl.NumberFormat('en-IN',{style:'currency',currency:'INR'}).format(num);
}

function animateValue(el,start,end){
    let current=start;
    let step=(end-start)/30;
    let interval=setInterval(()=>{
        current+=step;
        if((step>0 && current>=end)||(step<0 && current<=end)){
            current=end;
            clearInterval(interval);
        }
        el.innerText=formatINR(current);
    },15);
}

function updatePreview(){
    let value=parseFloat(amountInput.value)||0;
    preview.innerText=value?"Preview: "+formatINR(value):"";

    let diff=value-originalAmount;

    animateValue(projected,0,diff);

    if(diff>0){
        badge.className="change-badge bg-emerald-100 text-emerald-700";
        badge.innerText="+"+formatINR(diff);
        badge.classList.remove('hidden');
        aiText.innerText="Income increased. Savings projection strengthened.";
    }
    else if(diff<0){
        badge.className="change-badge bg-rose-100 text-rose-700";
        badge.innerText=formatINR(diff);
        badge.classList.remove('hidden');
        aiText.innerText="Income reduced. Monitor cashflow risk.";
    }
    else{
        badge.classList.add('hidden');
        aiText.innerText="No financial impact detected.";
        projected.innerText=formatINR(0);
    }
}

amountInput.addEventListener('input',updatePreview);

form.addEventListener('submit',function(){
    saveBtn.disabled=true;
    saveBtn.querySelector('.btnText').classList.add('hidden');
    saveBtn.querySelector('.loadingText').classList.remove('hidden');
});

updatePreview();

});
</script>

@endsection