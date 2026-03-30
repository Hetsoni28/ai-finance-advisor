@extends('layouts.app')

@section('content')

@php
abort_unless(Route::has('user.families.store'), 404);
abort_unless(Route::has('user.families.index'), 404);

$routeStore = route('user.families.store');
$routeIndex = route('user.families.index');
@endphp

<div class="min-h-screen bg-gradient-to-br from-slate-100 via-white to-indigo-100 flex items-center justify-center px-4 py-20">

<div class="w-full max-w-2xl space-y-8">

{{-- 🔥 STEP INDICATOR --}}
<div class="flex justify-center gap-4 text-xs">
<span class="step active">Create</span>
<span class="step">Invite</span>
<span class="step">Track</span>
<span class="step">Analyze</span>
</div>


{{-- SUCCESS --}}
@if(session('success'))
<div class="alert-success">
✔ {{ session('success') }}
</div>
@endif


<div class="card">

{{-- HEADER --}}
<div class="text-center mb-10">

<div class="icon">🏠</div>

<h1>Create Family Workspace</h1>

<p>Secure • Intelligent • Collaborative</p>

</div>


{{-- ERROR --}}
@if ($errors->any())
<div class="alert-error">
@foreach ($errors->all() as $error)
<div>• {{ $error }}</div>
@endforeach
</div>
@endif


<form method="POST" action="{{ $routeStore }}" id="familyForm" class="space-y-8">
@csrf

{{-- NAME --}}
<div>

<label>Workspace Name</label>

<input type="text"
id="name"
name="name"
maxlength="100"
required
value="{{ old('name') }}"
placeholder="e.g. My Family Budget"
class="input">

<div class="meta">
<span id="nameStatus">Enter name</span>
<span><span id="charCount">0</span>/100</span>
</div>

</div>


{{-- PREVIEW --}}
<div id="previewCard" class="preview hidden">

<h3>Preview</h3>

<div id="previewName">—</div>

<p>AI-powered financial system</p>

</div>


{{-- SMART SUGGESTIONS --}}
<div class="chips">

@foreach(['Family','Wealth Hub','Budget System','Finance AI','Smart Wallet'] as $s)
<button type="button" class="chip" data-suggest="{{ $s }}">{{ $s }}</button>
@endforeach

</div>


{{-- AI TIP --}}
<div class="ai-box">
🤖 Tip: Short names improve dashboard clarity & analytics grouping.
</div>


{{-- ACTION --}}
<div class="actions">

<a href="{{ $routeIndex }}" class="back">← Back</a>

<button id="submitBtn" type="submit" class="submit">

<span class="submit-label">Create Workspace</span>
<span class="loading-label hidden">Creating...</span>

</button>

</div>

</form>

</div>


{{-- FOOTER --}}
<div class="text-center text-xs text-slate-400">
Next → Invite → Add Transactions → AI Insights
</div>

</div>
</div>


<style>

.card{
background:white;
padding:40px;
border-radius:20px;
box-shadow:0 20px 60px rgba(0,0,0,.08);
}

.icon{
width:70px;
height:70px;
margin:auto;
background:linear-gradient(135deg,#6366f1,#06b6d4);
display:flex;
align-items:center;
justify-content:center;
border-radius:20px;
font-size:30px;
color:white;
}

h1{
font-size:26px;
font-weight:800;
}

label{
font-size:12px;
font-weight:600;
}

.input{
width:100%;
padding:14px;
border-radius:12px;
border:1px solid #ddd;
}

.meta{
display:flex;
justify-content:space-between;
font-size:11px;
color:#64748b;
margin-top:4px;
}

.preview{
background:#f1f5f9;
padding:16px;
border-radius:12px;
}

.chips{
display:flex;
flex-wrap:wrap;
gap:8px;
}

.chip{
padding:6px 12px;
background:#f8fafc;
border-radius:999px;
cursor:pointer;
}

.ai-box{
background:#eef2ff;
padding:12px;
border-radius:10px;
font-size:12px;
}

.actions{
display:flex;
justify-content:space-between;
align-items:center;
}

.submit{
background:linear-gradient(135deg,#6366f1,#06b6d4);
color:white;
padding:12px 24px;
border-radius:12px;
}

.step{
padding:4px 10px;
border-radius:999px;
background:#e2e8f0;
}
.step.active{
background:#6366f1;
color:white;
}

.alert-success{
background:#dcfce7;
padding:10px;
border-radius:10px;
}

.alert-error{
background:#fee2e2;
padding:10px;
border-radius:10px;
}

</style>


<script>
document.addEventListener('DOMContentLoaded', function(){

const input = document.getElementById('name');
const preview = document.getElementById('previewCard');
const previewName = document.getElementById('previewName');
const count = document.getElementById('charCount');
const status = document.getElementById('nameStatus');

input.addEventListener('input', function(){

let val = this.value.trim();
count.innerText = val.length;

if(val.length > 2){
preview.classList.remove('hidden');
previewName.innerText = val;
status.innerText = "Good ✔";
status.style.color = "green";
}else{
preview.classList.add('hidden');
status.innerText = "Min 3 chars";
status.style.color = "gray";
}

});


document.querySelectorAll('[data-suggest]').forEach(btn=>{
btn.onclick = ()=>{
input.value = input.value ? input.value + ' ' + btn.dataset.suggest : btn.dataset.suggest;
input.dispatchEvent(new Event('input'));
};
});


const form = document.getElementById('familyForm');
const submitBtn = document.getElementById('submitBtn');

form.addEventListener('submit',()=>{
submitBtn.disabled = true;
submitBtn.querySelector('.submit-label').classList.add('hidden');
submitBtn.querySelector('.loading-label').classList.remove('hidden');
});

});
</script>

@endsection