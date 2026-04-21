@extends('layouts.app')

@section('title', 'Payment Successful - FinanceAI')

@section('content')

<div class="min-h-screen bg-[#f8fafc] flex items-center justify-center pb-32 font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden">
    
    {{-- Ambient Light Backgrounds --}}
    <div class="absolute top-[-20%] left-[-10%] w-[800px] h-[800px] bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="absolute bottom-[-20%] right-[-10%] w-[600px] h-[600px] bg-indigo-500/5 rounded-full blur-[100px] pointer-events-none z-0"></div>

    <div class="max-w-xl mx-auto px-4 sm:px-6 relative z-10 w-full">

        {{-- Success Card --}}
        <div class="bg-white/90 backdrop-blur-xl rounded-[2.5rem] p-10 md:p-14 border border-white shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] text-center animate-fade-in-up relative overflow-hidden">
            
            {{-- Top Emerald Accent --}}
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-emerald-400 via-emerald-500 to-teal-500"></div>

            {{-- Background Glow --}}
            <div class="absolute top-[-30%] left-1/2 -translate-x-1/2 w-[400px] h-[400px] bg-emerald-500/10 rounded-full blur-[100px] pointer-events-none"></div>

            {{-- Animated Check Icon --}}
            <div class="relative z-10">
                <div class="w-28 h-28 mx-auto mb-8 relative">
                    {{-- Pulsing Ring --}}
                    <div class="absolute inset-0 rounded-[2rem] bg-emerald-500/20 animate-ping" style="animation-duration: 2s;"></div>
                    <div class="absolute inset-0 rounded-[2rem] bg-emerald-500/10 animate-pulse"></div>
                    
                    {{-- Icon Container --}}
                    <div class="relative w-full h-full rounded-[2rem] bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-[0_15px_40px_-10px_rgba(16,185,129,0.5)] border border-emerald-400">
                        <i class="fa-solid fa-check text-white text-5xl drop-shadow-lg"></i>
                    </div>
                </div>

                {{-- Success Content --}}
                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-600 text-[10px] font-black tracking-widest uppercase shadow-sm mb-6">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Transaction Validated
                </span>

                <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight mb-4 leading-tight">
                    Payment Successful
                </h1>

                <p class="text-slate-500 font-medium text-base leading-relaxed mb-8 max-w-md mx-auto">
                    Your subscription to <strong class="text-slate-900">{{ $planName }}</strong> has been activated successfully. 
                    All premium features are now unlocked for your account.
                </p>

                {{-- Transaction Details Card --}}
                <div class="bg-slate-50 rounded-[1.5rem] p-6 border border-slate-200 mb-8 text-left shadow-inner">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Transaction Details</span>
                        <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">Confirmed</span>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                            <span class="text-xs font-bold text-slate-500">Plan Activated</span>
                            <span class="text-sm font-black text-slate-900">{{ $planName }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                            <span class="text-xs font-bold text-slate-500">Transaction Time</span>
                            <span class="text-sm font-black text-slate-900 font-mono">{{ now()->setTimezone('Asia/Kolkata')->format('H:i:s T') }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-xs font-bold text-slate-500">Billing Cycle</span>
                            <span class="text-sm font-black text-slate-900">30 Days</span>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('user.subscription.index') }}" 
                       class="flex-1 py-4 px-6 rounded-xl bg-slate-900 text-white font-black uppercase tracking-widest text-[11px] hover:bg-indigo-600 shadow-md hover:shadow-xl hover:shadow-indigo-500/30 transition-all flex items-center justify-center gap-2 hover:-translate-y-0.5 focus:outline-none">
                        <i class="fa-solid fa-credit-card"></i> Manage Subscription
                    </a>
                    <a href="{{ route('user.dashboard') }}" 
                       class="flex-1 py-4 px-6 rounded-xl bg-white border-2 border-slate-200 text-slate-700 font-black uppercase tracking-widest text-[11px] hover:bg-slate-50 hover:border-slate-300 transition-all flex items-center justify-center gap-2 hover:-translate-y-0.5 focus:outline-none shadow-sm">
                        <i class="fa-solid fa-chart-pie"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>

        {{-- Bottom Assurance --}}
        <p class="text-center text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-8 flex items-center justify-center gap-2">
            <i class="fa-solid fa-lock text-emerald-500"></i> Secured by 256-bit encryption — No real charges applied (Demo Mode)
        </p>
    </div>
</div>

@endsection

@push('styles')
<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in-up { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
@endpush
