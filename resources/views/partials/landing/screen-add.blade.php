{{-- Illustration: adding an expense. Uses the app's real expense categories. --}}
<div class="rounded-2xl bg-white border border-slate-200 shadow-[0_30px_60px_-20px_rgba(30,27,75,0.18)] p-5 sm:p-6" aria-hidden="true">
    <div class="flex items-center justify-between">
        <p class="font-mono text-[11px] uppercase tracking-[0.18em] text-slate-400">New expense</p>
        <span class="font-mono text-[11px] text-slate-400">Illustration</span>
    </div>

    <div class="mt-5 space-y-4">
        <div>
            <p class="text-xs font-semibold text-slate-500">Title</p>
            <div class="mt-1.5 h-11 rounded-xl border border-slate-200 bg-slate-50 px-3.5 flex items-center text-sm text-slate-800">Groceries</div>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <p class="text-xs font-semibold text-slate-500">Amount</p>
                <div class="mt-1.5 h-11 rounded-xl border border-indigo-300 ring-4 ring-indigo-50 bg-white px-3.5 flex items-center text-sm font-bold text-slate-900 tabular-nums">₹ 2,450<span class="ml-0.5 w-px h-4 bg-indigo-500 caret"></span></div>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500">Date</p>
                <div class="mt-1.5 h-11 rounded-xl border border-slate-200 bg-slate-50 px-3.5 flex items-center text-sm text-slate-800">Today</div>
            </div>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500">Category</p>
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach([['Food','fa-burger',true],['Travel','fa-plane',false],['Bills','fa-file-invoice',false],['Shopping','fa-bag-shopping',false],['Health','fa-heart-pulse',false],['Others','fa-ellipsis',false]] as [$c, $i, $on])
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border {{ $on ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-slate-200 text-slate-600' }}">
                        <i class="fa-solid {{ $i }} text-[11px]"></i>{{ $c }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>
</div>
