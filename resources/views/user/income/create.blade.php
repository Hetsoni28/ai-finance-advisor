@extends('layouts.app')

@section('content')

<div class="max-w-[1400px] mx-auto px-6 pb-24 space-y-12">

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="mb-8 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl shadow">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-8 bg-rose-50 border border-rose-200 text-rose-700 px-6 py-5 rounded-2xl shadow-sm">
            <ul class="space-y-2 text-sm">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-extrabold tracking-tight">
                Add Income
            </h1>
            <p class="text-sm text-slate-500 mt-2">
                Secure • Intelligent • Real-time Synced
            </p>
        </div>

        <a href="{{ route('user.incomes.index') }}"
           class="text-sm text-slate-500 hover:text-slate-900 transition">
            ← Back
        </a>
    </div>


    <div class="grid lg:grid-cols-3 gap-14">

        {{-- FORM --}}
        <div class="lg:col-span-2">

            <form id="incomeForm"
                  action="{{ route('user.incomes.store') }}"
                  method="POST"
                  class="bg-white border border-slate-200 shadow-2xl rounded-3xl p-12 space-y-10 transition">

                @csrf

                {{-- STEP PROGRESS --}}
                <div class="flex items-center justify-between text-xs uppercase tracking-widest">
                    <span class="step active">Type</span>
                    <span class="step">Details</span>
                    <span class="step">Confirm</span>
                </div>

                {{-- TYPE --}}
                <div>
                    <label class="label">Income Type</label>

                    <div class="grid grid-cols-2 gap-5 mt-5">

                        @foreach(['personal' => '👤 Personal', 'family' => '👨‍👩‍👧‍👦 Family'] as $value => $label)

                            <label>
                                <input type="radio"
                                       name="income_type"
                                       value="{{ $value }}"
                                       {{ old('income_type','personal') == $value ? 'checked' : '' }}
                                       class="hidden peer">

                                <div class="select-card peer-checked:ring-4 peer-checked:ring-indigo-200 peer-checked:border-indigo-500">
                                    {{ $label }}
                                </div>
                            </label>

                        @endforeach

                    </div>
                </div>


                {{-- FAMILY --}}
                <div id="familyBox"
                     class="{{ old('income_type')=='family'?'':'hidden' }} transition-all duration-300">

                    <label class="label text-blue-600">Select Family</label>

                    <select id="familySelect"
                            name="family_id"
                            class="input mt-4">
                        <option value="">Choose family</option>

                        @foreach($families as $family)
                            <option value="{{ $family->id }}"
                                {{ old('family_id')==$family->id?'selected':'' }}>
                                {{ $family->name }}
                            </option>
                        @endforeach

                    </select>

                    <p id="familyError" class="text-rose-600 text-xs mt-2 hidden">
                        Please select a family.
                    </p>

                </div>


                {{-- AMOUNT --}}
                <div>
                    <label class="label text-emerald-600">Amount</label>

                    <div class="relative mt-4">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-xl text-slate-400">₹</span>

                        <input id="amountInput"
                               type="number"
                               min="0.01"
                               step="0.01"
                               name="amount"
                               value="{{ old('amount') }}"
                               required
                               class="input pl-12 text-2xl font-bold">
                    </div>

                    <div class="flex justify-between mt-3 text-sm">
                        <span id="amountPreview" class="text-slate-500"></span>
                        <span id="impactBadge" class="badge hidden"></span>
                    </div>
                </div>


                {{-- DATE --}}
                <div>
                    <label class="label">Income Date</label>

                    <input type="date"
                           name="income_date"
                           value="{{ old('income_date', now()->toDateString()) }}"
                           required
                           class="input mt-4">
                </div>


                {{-- SOURCE --}}
                <div>
                    <input id="sourceInput"
                           type="text"
                           name="source"
                           maxlength="60"
                           value="{{ old('source') }}"
                           placeholder="Income Source"
                           required
                           class="input">

                    <div class="flex justify-between text-xs text-slate-500 mt-2">
                        <span id="sourceHint">Describe income clearly</span>
                        <span id="charCount">0 / 60</span>
                    </div>
                </div>


                {{-- SUBMIT --}}
                <button id="submitBtn"
                        type="submit"
                        class="w-full py-6 rounded-2xl bg-gradient-to-r from-emerald-500 to-blue-600 text-white font-semibold text-lg shadow-xl hover:scale-[1.02] transition">

                    <span class="btnText">Save Income</span>
                    <span class="loadingText hidden">Processing...</span>

                </button>

            </form>
        </div>


        {{-- SIDEBAR --}}
        <div class="space-y-10">

            <div class="panel">
                <h3 class="font-bold mb-4">🤖 Smart Insight</h3>
                <p id="aiInsight" class="text-sm text-slate-600">
                    Add income to strengthen financial stability.
                </p>
            </div>

            <div class="panel">
                <h4 class="text-xs font-bold text-slate-500 uppercase mb-6 tracking-widest">
                    Recent Income
                </h4>

                @forelse($recentIncome as $inc)
                    <div class="flex justify-between mb-4 text-sm">
                        <span>{{ $inc->source }}</span>
                        <span class="text-emerald-600 font-bold">
                            +₹{{ number_format($inc->amount,2) }}
                        </span>
                    </div>
                @empty
                    <p class="text-slate-500 text-sm">No income yet</p>
                @endforelse

            </div>

        </div>
    </div>
</div>


<style>
.input{
    width:100%;
    padding:1.25rem 1.5rem;
    border-radius:1rem;
    border:1px solid #e2e8f0;
    transition:.2s;
}
.input:focus{
    border-color:#10b981;
    box-shadow:0 0 0 3px rgba(16,185,129,.15);
    outline:none;
}
.label{
    font-size:.75rem;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.1em;
}
.select-card{
    padding:1.5rem;
    text-align:center;
    border-radius:1rem;
    border:1px solid #e2e8f0;
    font-weight:600;
    cursor:pointer;
    transition:.2s;
}
.select-card:hover{
    box-shadow:0 6px 20px rgba(0,0,0,.05);
}
.panel{
    background:white;
    border:1px solid #e2e8f0;
    border-radius:1rem;
    padding:2rem;
    box-shadow:0 6px 20px rgba(0,0,0,.05);
}
.badge{
    padding:.3rem .8rem;
    border-radius:999px;
    font-size:.75rem;
    font-weight:600;
}
.step.active{
    color:#4f46e5;
    font-weight:bold;
}
</style>


<script>
document.addEventListener('DOMContentLoaded',function(){

const radios=document.querySelectorAll('input[name="income_type"]');
const familyBox=document.getElementById('familyBox');
const familySelect=document.getElementById('familySelect');
const familyError=document.getElementById('familyError');
const amountInput=document.getElementById('amountInput');
const amountPreview=document.getElementById('amountPreview');
const impactBadge=document.getElementById('impactBadge');
const sourceInput=document.getElementById('sourceInput');
const charCount=document.getElementById('charCount');
const submitBtn=document.getElementById('submitBtn');
const form=document.getElementById('incomeForm');

function formatINR(num){
    return new Intl.NumberFormat('en-IN',{style:'currency',currency:'INR'}).format(num);
}

radios.forEach(r=>{
    r.addEventListener('change',function(){
        if(this.value==='family'){
            familyBox.classList.remove('hidden');
        }else{
            familyBox.classList.add('hidden');
            familySelect.value="";
        }
    });
});

amountInput.addEventListener('input',function(){
    let value=parseFloat(this.value)||0;
    if(value<0) value=0;
    this.value=value;

    amountPreview.innerText=value? "Preview: "+formatINR(value):"";

    if(value>50000){
        impactBadge.innerText="High Impact";
        impactBadge.className="badge bg-emerald-100 text-emerald-700";
        impactBadge.classList.remove('hidden');
    }else if(value>10000){
        impactBadge.innerText="Moderate Impact";
        impactBadge.className="badge bg-indigo-100 text-indigo-700";
        impactBadge.classList.remove('hidden');
    }else{
        impactBadge.classList.add('hidden');
    }
});

sourceInput.addEventListener('input',function(){
    charCount.innerText=this.value.length+" / 60";
});

form.addEventListener('submit',function(e){

    const selected=document.querySelector('input[name="income_type"]:checked').value;

    if(selected==='family' && !familySelect.value){
        e.preventDefault();
        familyError.classList.remove('hidden');
        return;
    }

    submitBtn.disabled=true;
    submitBtn.querySelector('.btnText').classList.add('hidden');
    submitBtn.querySelector('.loadingText').classList.remove('hidden');

});

charCount.innerText=sourceInput.value.length+" / 60";

});
</script>

@endsection