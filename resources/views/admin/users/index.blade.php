@extends('layouts.app')

@section('content')

@php
    $stats = $stats ?? ['total'=>0,'active'=>0,'blocked'=>0,'admins'=>0];
    $users = $users ?? collect();

    $total   = (int) ($stats['total'] ?? 0);
    $active  = (int) ($stats['active'] ?? 0);
    $blocked = (int) ($stats['blocked'] ?? 0);
    $admins  = (int) ($stats['admins'] ?? 0);

    $health = $total > 0 ? round(($active / $total) * 100) : 0;

    if ($health >= 80) {
        $healthColor = 'bg-emerald-500';
    } elseif ($health >= 60) {
        $healthColor = 'bg-indigo-500';
    } elseif ($health >= 40) {
        $healthColor = 'bg-amber-500';
    } else {
        $healthColor = 'bg-rose-500';
    }
@endphp


<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">

<div class="max-w-7xl mx-auto px-6 py-12 space-y-12">

{{-- ================= HEADER ================= --}}
<div class="flex flex-col md:flex-row md:justify-between md:items-center gap-6">

    <div>
        <h1 class="text-4xl font-black tracking-tight text-slate-900 dark:text-white">
            Admin Control Matrix
        </h1>
        <p class="text-slate-500 dark:text-slate-400 mt-2">
            Enterprise User Governance Console
        </p>
    </div>

    <div class="text-right">
        <p class="text-xs uppercase text-slate-400">Total Users</p>
        <p class="text-3xl font-bold counter text-indigo-600" data-target="{{ $total }}">0</p>
    </div>

</div>


{{-- ================= KPI CARDS ================= --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

    @foreach([
        ['label'=>'Active Users','value'=>$active,'color'=>'text-emerald-500'],
        ['label'=>'Blocked Users','value'=>$blocked,'color'=>'text-rose-500'],
        ['label'=>'Admin Accounts','value'=>$admins,'color'=>'text-indigo-500'],
    ] as $card)

    <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl shadow-lg hover:-translate-y-1 transition">
        <p class="text-xs uppercase text-slate-400 font-semibold">{{ $card['label'] }}</p>
        <p class="text-3xl font-black mt-2 {{ $card['color'] }} counter"
           data-target="{{ $card['value'] }}">0</p>
    </div>

    @endforeach

    {{-- Health --}}
    <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl shadow-lg">
        <p class="text-xs uppercase text-slate-400 font-semibold">Governance Health</p>
        <p class="text-3xl font-black mt-2">{{ $health }}%</p>

        <div class="h-2 bg-slate-200 dark:bg-slate-700 rounded-full mt-4 overflow-hidden">
            <div class="h-2 {{ $healthColor }} transition-all duration-700"
                 style="width:{{ $health }}%">
            </div>
        </div>
    </div>

</div>


{{-- ================= FILTER BAR ================= --}}
<div class="bg-white dark:bg-slate-900 p-6 rounded-3xl shadow-lg sticky top-20 z-30 backdrop-blur">

<form method="GET" class="flex flex-wrap gap-4 items-center">

    <input type="text"
           name="search"
           value="{{ request('search') }}"
           placeholder="Search name or email..."
           class="flex-1 min-w-[200px] px-4 py-2 border border-slate-300 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">

    <select name="status"
            class="px-4 py-2 border border-slate-300 dark:border-slate-700 rounded-xl">
        <option value="">All Status</option>
        <option value="active" @selected(request('status')=='active')>Active</option>
        <option value="blocked" @selected(request('status')=='blocked')>Blocked</option>
    </select>

    <button class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition">
        Apply
    </button>

    <button type="button"
            onclick="exportCSV()"
            class="px-4 py-2 bg-slate-200 dark:bg-slate-700 rounded-xl hover:scale-105 transition">
        Export CSV
    </button>

</form>

</div>


{{-- ================= TABLE ================= --}}
<div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl overflow-hidden">

<div class="overflow-x-auto">

<table id="userTable" class="min-w-full text-sm">

<thead class="bg-slate-100 dark:bg-slate-800 text-xs uppercase sticky top-0 z-20">
<tr>
    <th class="px-6 py-4">
        <input type="checkbox" onclick="toggleAll(this)">
    </th>
    <th class="px-6 py-4 text-left">User</th>
    <th class="px-6 py-4 text-left">Email</th>
    <th class="px-6 py-4 text-center">Role</th>
    <th class="px-6 py-4 text-center">Status</th>
    <th class="px-6 py-4 text-right">Actions</th>
</tr>
</thead>

<tbody class="divide-y divide-slate-200 dark:divide-slate-800">

@forelse($users as $user)

@php
    $isAdmin = method_exists($user,'isAdmin') && $user->isAdmin();
@endphp

<tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition duration-150">

<td class="px-6 py-4">
    <input type="checkbox" class="rowCheck">
</td>

<td class="px-6 py-4">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center font-bold text-indigo-600">
            {{ strtoupper(substr($user->name,0,1)) }}
        </div>
        <div>
            <p class="font-semibold text-slate-900 dark:text-white">
                {{ e($user->name) }}
            </p>
            <p class="text-xs text-slate-400">
                ID #{{ $user->id }}
            </p>
        </div>
    </div>
</td>

<td class="px-6 py-4 text-slate-600 dark:text-slate-300">
    {{ e($user->email) }}
</td>

<td class="px-6 py-4 text-center">
    <span class="px-3 py-1 text-xs font-semibold rounded-full
        {{ $isAdmin
            ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-400'
            : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300' }}">
        {{ $isAdmin ? 'Admin' : 'User' }}
    </span>
</td>

<td class="px-6 py-4 text-center">
    <span class="px-3 py-1 text-xs font-semibold rounded-full
        {{ $user->is_blocked
            ? 'bg-rose-100 text-rose-600'
            : 'bg-emerald-100 text-emerald-600' }}">
        {{ $user->is_blocked ? 'Blocked' : 'Active' }}
    </span>
</td>

<td class="px-6 py-4 text-right">

@if(auth()->id() !== $user->id)

<form action="{{ route('admin.users.block',$user) }}"
      method="POST" class="inline">
@csrf @method('PATCH')

<button class="text-xs px-3 py-1.5 rounded-lg
    {{ $user->is_blocked
        ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'
        : 'bg-rose-100 text-rose-700 hover:bg-rose-200' }}">
    {{ $user->is_blocked ? 'Unblock' : 'Block' }}
</button>
</form>

@endif

</td>

</tr>

@empty

<tr>
<td colspan="6" class="px-6 py-20 text-center text-slate-500">
    No users found.
</td>
</tr>

@endforelse

</tbody>
</table>

</div>

</div>


@if(method_exists($users,'links'))
<div>
    {{ $users->withQueryString()->links('pagination::tailwind') }}
</div>
@endif


</div>
</div>


{{-- ================= JS ================= --}}
<script>
document.addEventListener('DOMContentLoaded',function(){

    document.querySelectorAll('.counter').forEach(c=>{
        const target=parseInt(c.dataset.target||0);
        let count=0;
        const step=Math.max(target/40,1);
        function update(){
            count+=step;
            if(count<target){
                c.innerText=Math.floor(count).toLocaleString();
                requestAnimationFrame(update);
            }else{
                c.innerText=target.toLocaleString();
            }
        }
        update();
    });

});

function toggleAll(master){
    document.querySelectorAll('.rowCheck')
        .forEach(cb=>cb.checked=master.checked);
}

function exportCSV(){
    const rows=document.querySelectorAll("#userTable tbody tr");
    let csv="Name,Email,Role,Status\n";

    rows.forEach(row=>{
        const cols=row.querySelectorAll("td");
        if(cols.length>4){
            const rowData=[
                cols[1].innerText.replace(/,/g,""),
                cols[2].innerText.replace(/,/g,""),
                cols[3].innerText.replace(/,/g,""),
                cols[4].innerText.replace(/,/g,"")
            ];
            csv+=rowData.join(",")+"\n";
        }
    });

    const blob=new Blob([csv],{type:"text/csv"});
    const link=document.createElement("a");
    link.href=URL.createObjectURL(blob);
    link.download="users_export.csv";
    link.click();
}
</script>

@endsection