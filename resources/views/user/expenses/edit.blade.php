@extends('layouts.app')

@section('content')

@php
$originalAmount = (float)($expense->amount ?? 0);
@endphp

<div class="max-w-5xl mx-auto px-6 py-16">

{{-- HEADER --}}
<div class="mb-12">
    <h1 class="text-4xl font-extrabold">
        Edit Expense
    </h1>
    <p class="text-sm text-slate-500 mt-2">
        {{ $expense->is_personal ? 'Personal expense' : 'Family expense' }}
        • created {{ $expense->created_at?->diffForHumans() ?? '—' }}
    </p>
</div>

<form method="POST"
      action="{{ route('user.expenses.update', $expense->id) }}"
      id="expenseForm"
      class="bg-white dark:bg-slate-900
             border border-slate-200 dark:border-slate-700
             shadow-2xl rounded-3xl p-12 space-y-12">

@csrf
@method('PUT')

{{-- TITLE --}}
<div>
    <label class="label">Title</label>

    <input id="titleInput"
           type="text"
           name="title"
           maxlength="150"
           required
           value="{{ old('title', $expense->title) }}"
           class="input-field @error('title') ring-2 ring-rose-400 @enderror">

    <div class="meta-row">
        <span>Keep title concise</span>
        <span id="charCount">0 / 150</span>
    </div>
</div>

{{-- CATEGORY --}}
<div>
    <label class="label">Category</label>

    <select name="category"
            required
            class="input-field @error('category') ring-2 ring-rose-400 @enderror">

        @foreach(['Food','Travel','Bills','Shopping','Other'] as $cat)
            <option value="{{ $cat }}"
                {{ old('category', $expense->category)===$cat?'selected':'' }}>
                {{ $cat }}
            </option>
        @endforeach

    </select>
</div>

{{-- AMOUNT --}}
<div>
    <label class="label text-rose-600">Amount</label>

    <div class="relative mt-3">
        <span class="currency">₹</span>

        <input id="amountInput"
               type="number"
               name="amount"
               min="0.01"
               step="0.01"
               required
               value="{{ old('amount', $originalAmount) }}"
               class="amount-field @error('amount') ring-2 ring-rose-400 @enderror">
    </div>

    <div class="meta-row mt-3">
        <span id="amountPreview" class="text-slate-500"></span>
        <span id="impactBadge" class="impact-badge hidden"></span>
    </div>

    {{-- Quick Adjust --}}
    <div class="flex flex-wrap gap-3 mt-4">
        <button type="button" onclick="adjust(100)" class="adjust-btn">+100</button>
        <button type="button" onclick="adjust(500)" class="adjust-btn">+500</button>
        <button type="button" onclick="adjust(-500)" class="adjust-btn danger">−500</button>
    </div>
</div>

{{-- DATE --}}
<div>
    <label class="label">Expense Date</label>

    <input type="date"
           name="expense_date"
           required
           value="{{ old('expense_date', optional($expense->expense_date)->format('Y-m-d')) }}"
           class="input-field @error('expense_date') ring-2 ring-rose-400 @enderror">
</div>

{{-- AI PANEL --}}
<div class="ai-card">
    <h4 class="font-bold mb-2">🤖 Financial Impact</h4>
    <p id="aiText" class="text-sm text-slate-600 dark:text-slate-300">
        Editing {{ $expense->is_personal ? 'personal' : 'family' }} expense.
    </p>

    <div class="mt-4">
        <p class="text-xs uppercase text-slate-400">Projected Difference</p>
        <h3 id="projectedValue" class="text-2xl font-bold text-rose-600">
            ₹0.00
        </h3>
    </div>
</div>

{{-- ACTIONS --}}
<div class="flex flex-col md:flex-row justify-between gap-6 pt-6 border-t">

    <a href="{{ route('user.expenses.index') }}"
       class="text-sm text-slate-500 hover:underline">
        ← Back
    </a>

    <button type="submit"
            id="submitBtn"
            class="btn-primary">
        <span class="submitText">Update Expense</span>
        <span class="loadingText hidden">Updating...</span>
    </button>

</div>

</form>
</div>

{{-- STYLES --}}
<style>
.label{font-size:.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#64748b}
.input-field{width:100%;margin-top:.8rem;padding:1rem 1.2rem;border:1px solid #e2e8f0;border-radius:1rem}
.amount-field{width:100%;padding:1.4rem 1.2rem 1.4rem 2.5rem;font-size:1.7rem;font-weight:700;border:1px solid #e2e8f0;border-radius:1rem}
.currency{position:absolute;left:1rem;top:50%;transform:translateY(-50%);color:#94a3b8}
.meta-row{display:flex;justify-content:space-between;font-size:.75rem}
.adjust-btn{padding:.4rem .9rem;border:1px solid #e2e8f0;border-radius:999px;font-size:.8rem;transition:.2s}
.adjust-btn:hover{background:#f1f5f9}
.adjust-btn.danger:hover{background:#fee2e2}
.ai-card{background:linear-gradient(135deg,#f8fafc,#ffffff);padding:2rem;border-radius:1rem;border:1px solid #e2e8f0}
.impact-badge{font-size:.75rem;padding:.3rem .8rem;border-radius:999px;font-weight:600}
.btn-primary{padding:1rem 2rem;border-radius:1rem;background:linear-gradient(135deg,#ef4444,#ec4899);color:white;font-weight:600;transition:.3s}
.btn-primary:hover{transform:scale(1.03)}
</style>

{{-- SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded',function(){

const amountInput=document.getElementById('amountInput');
const preview=document.getElementById('amountPreview');
const badge=document.getElementById('impactBadge');
const aiText=document.getElementById('aiText');
const projected=document.getElementById('projectedValue');
const form=document.getElementById('expenseForm');
const submitBtn=document.getElementById('submitBtn');
const titleInput=document.getElementById('titleInput');
const charCount=document.getElementById('charCount');

const originalAmount={{ $originalAmount }};

function formatINR(n){
return new Intl.NumberFormat('en-IN',{style:'currency',currency:'INR'}).format(n);
}

function animateValue(el,start,end){
let current=start;
let step=(end-start)/30;
let interval=setInterval(()=>{
current+=step;
if((step>0 && current>=end)||(step<0 && current<=end)){
current=end;clearInterval(interval);}
el.innerText=formatINR(current);
},15);
}

window.adjust=function(val){
let newVal=(Number(amountInput.value)||0)+val;
if(newVal<0)newVal=0;
amountInput.value=newVal;
updateImpact();
}

function updateImpact(){
let current=Number(amountInput.value||0);
preview.innerText=current?"Preview: "+formatINR(current):"";

let diff=current-originalAmount;
animateValue(projected,0,diff);

if(diff>0){
badge.className="impact-badge bg-rose-100 text-rose-700";
badge.innerText="+"+formatINR(diff);
badge.classList.remove('hidden');
aiText.innerText="Expense increased. Higher financial strain detected.";
}
else if(diff<0){
badge.className="impact-badge bg-emerald-100 text-emerald-700";
badge.innerText=formatINR(diff);
badge.classList.remove('hidden');
aiText.innerText="Expense reduced. Positive impact on balance.";
}
else{
badge.classList.add('hidden');
projected.innerText=formatINR(0);
aiText.innerText="No financial change detected.";
}
}

amountInput.addEventListener('input',updateImpact);

titleInput.addEventListener('input',function(){
charCount.innerText=this.value.length+" / 150";
});

form.addEventListener('submit',function(){
submitBtn.disabled=true;
submitBtn.querySelector('.submitText').classList.add('hidden');
submitBtn.querySelector('.loadingText').classList.remove('hidden');
});

charCount.innerText=titleInput.value.length+" / 150";
updateImpact();

});
</script>

@endsection