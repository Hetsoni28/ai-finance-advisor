{{-- Illustration: dashboard view. Numbers are sample values, labelled as such. --}}
<div class="rounded-2xl bg-white border border-slate-200 shadow-[0_30px_60px_-20px_rgba(30,27,75,0.18)] p-5 sm:p-6" aria-hidden="true">
    <div class="flex items-center justify-between">
        <p class="font-mono text-[11px] uppercase tracking-[0.18em] text-slate-400">This month</p>
        <span class="font-mono text-[11px] text-slate-400">Sample data</span>
    </div>

    <div class="mt-4 flex items-end justify-between gap-4">
        <div>
            <p class="text-xs text-slate-500">Saved so far</p>
            <p class="text-3xl font-black tracking-tight text-slate-900 tabular-nums">₹18,600</p>
        </div>
        <span class="font-mono text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-md px-2 py-1">31% saved</span>
    </div>

    <div class="mt-6 h-32 flex items-end gap-2">
        @foreach([['Jun',52],['Jul',68],['Aug',45],['Sep',74],['Oct',60],['Nov',56]] as $k => [$m, $h])
            <div class="flex-1 h-full flex flex-col justify-end items-center gap-2">
                <div class="grow-bar w-full rounded-md {{ $k === 5 ? 'bg-indigo-600' : 'bg-slate-200' }}" style="--h: {{ $h }}%; --d: {{ $k * 70 }}ms"></div>
                <span class="font-mono text-[11px] text-slate-500">{{ $m }}</span>
            </div>
        @endforeach
    </div>

    <div class="mt-5 pt-4 border-t border-slate-100 grid grid-cols-3 gap-3 text-xs">
        @foreach([['Food','₹9,800'],['Bills','₹7,200'],['Travel','₹4,100']] as [$c, $v])
            <div><p class="text-slate-500">{{ $c }}</p><p class="font-bold text-slate-900 tabular-nums">{{ $v }}</p></div>
        @endforeach
    </div>
</div>
