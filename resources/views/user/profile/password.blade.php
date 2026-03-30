@extends('layouts.app')

@section('title', 'Security Credentials | FinanceAI')

@section('content')

@php
    // ================= 1. SAFE DATA EXTRACTION =================
    $user = auth()->user();
    abort_unless($user, 403, 'Unauthorized Access.');

    $emailVerified = method_exists($user, 'hasVerifiedEmail') ? $user->hasVerifiedEmail() : true;
@endphp

<div x-data="securityEngine()" class="min-h-screen bg-[#f8fafc] pb-32 font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden">

    {{-- Pristine Light Ambient Backgrounds --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[900px] h-[900px] bg-indigo-50/60 rounded-full blur-[120px] transition-colors duration-1000"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[800px] h-[800px] bg-sky-50/40 rounded-full blur-[100px]"></div>
    </div>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 pt-12 relative z-10 space-y-10">

        {{-- ================= 1. COMMAND HEADER & BREADCRUMBS ================= --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-2">
            <div>
                <nav class="flex mb-3" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <li><a href="{{ Route::has('user.profile.index') ? route('user.profile.index') : '#' }}" class="hover:text-indigo-600 transition-colors">Identity Hub</a></li>
                        <li><i class="fa-solid fa-chevron-right text-[8px] mx-1 opacity-50"></i></li>
                        <li class="text-indigo-600">Security Credentials</li>
                    </ol>
                </nav>
                <h1 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight">Access Control</h1>
                <p class="text-slate-500 text-sm font-medium mt-3 flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-indigo-400"></i> Manage your cryptographic keys and active sessions.
                </p>
            </div>
            
            <a href="{{ Route::has('user.profile.index') ? route('user.profile.index') : url('/dashboard') }}" class="px-5 py-3 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-sm shadow-sm hover:bg-slate-50 hover:text-indigo-600 hover:border-indigo-300 transition-all flex items-center gap-2 focus:outline-none w-full sm:w-auto justify-center">
                <i class="fa-solid fa-arrow-left"></i> Return to Hub
            </a>
        </div>

        {{-- ================= 2. GLOBAL ERROR CATCHER (FALLBACK) ================= --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="bg-emerald-50 border border-emerald-200 rounded-[1.5rem] p-4 flex items-center justify-between shadow-sm max-w-3xl mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-check"></i></div>
                    <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if($errors->has('current_password') || $errors->any())
            <div class="bg-rose-50 border border-rose-200 rounded-[1.5rem] p-6 shadow-sm flex items-start gap-4 animate-[pulse_0.5s_ease-in-out_1] max-w-3xl mb-6">
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 border border-rose-200">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-rose-900 uppercase tracking-widest mb-1">Authorization Failed</h3>
                    <ul class="space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-xs font-bold text-rose-600 flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-rose-400"></span> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- ================= 3. DUAL COLUMN ARCHITECTURE ================= --}}
        <div class="grid xl:grid-cols-12 gap-8 items-start">

            {{-- LEFT COLUMN: SECURITY POSTURE --}}
            <div class="xl:col-span-4 space-y-6 sticky top-28">
                
                {{-- Security Context Card (Pristine Light White Version) --}}
                <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8 md:p-10 relative overflow-hidden group flex flex-col items-center text-center">
                    <div class="absolute inset-0 opacity-[0.02] pointer-events-none" style="background-image: radial-gradient(#4f46e5 1px, transparent 1px); background-size: 24px 24px;"></div>
                    <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
                    
                    <div class="h-24 w-24 rounded-[2rem] bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-500 shadow-inner mb-6 relative z-10 transform group-hover:scale-110 transition-transform duration-500">
                        <i class="fa-solid fa-shield-halved text-4xl"></i>
                    </div>

                    <h2 class="text-2xl font-black text-slate-900 tracking-tight relative z-10">Security Posture</h2>
                    
                    <div class="mt-6 w-full space-y-4 relative z-10">
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 flex items-center justify-between shadow-inner">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Protocol</span>
                            <span class="px-2.5 py-1 rounded bg-white text-slate-700 text-[9px] font-black uppercase tracking-widest border border-slate-200 shadow-sm flex items-center gap-1.5">
                                <i class="fa-brands fa-laravel text-rose-500"></i> Bcrypt
                            </span>
                        </div>
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 flex items-center justify-between shadow-inner">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Identity Status</span>
                            <span class="px-2.5 py-1 rounded text-[9px] font-black uppercase tracking-widest border shadow-sm {{ $emailVerified ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                {{ $emailVerified ? 'Verified' : 'Unverified' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Session Termination Notice --}}
                <div class="bg-white rounded-[2.5rem] border border-slate-200 p-8 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 text-slate-50 text-7xl pointer-events-none group-hover:scale-110 transition-transform duration-500"><i class="fa-solid fa-network-wired"></i></div>
                    <h4 class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-3 flex items-center gap-2 relative z-10">
                        <i class="fa-solid fa-circle-info"></i> Session Protocol
                    </h4>
                    <p class="text-xs font-bold text-slate-500 leading-relaxed relative z-10">
                        Deploying a new cryptographic key will instantly invalidate your current token and <strong class="text-slate-900">terminate all other active network sessions</strong> across all devices to guarantee account security.
                    </p>
                </div>

            </div>

            {{-- RIGHT COLUMN: THE KEY EXCHANGE FORM --}}
            <div class="xl:col-span-8 bg-white rounded-[2.5rem] border border-slate-200 shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8 md:p-12 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-indigo-500 to-sky-400"></div>
                <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-indigo-50 to-transparent rounded-bl-full pointer-events-none"></div>
                
                <h2 class="text-2xl font-black text-slate-900 mb-8 flex items-center gap-3 relative z-10">
                    <i class="fa-solid fa-key text-indigo-500"></i> Key Exchange Protocol
                </h2>

                <form method="POST" action="{{ route('user.profile.password.update') ?? '#' }}" @submit="isSubmitting = true" class="space-y-8 relative z-10">
                    @csrf
                    
                    {{-- Note: Usually password updates require PUT/PATCH. Ensure your route accepts POST if you don't define @method here --}}

                    {{-- 1. Current Password --}}
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <label for="current_password" class="text-[11px] font-black text-slate-900 uppercase tracking-widest">Current Cryptographic Key <span class="text-rose-500">*</span></label>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-unlock-keyhole text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                            </div>
                            <input :type="showCurrent ? 'text' : 'password'" id="current_password" name="current_password" required
                                   class="w-full pl-11 pr-12 py-4 bg-slate-50 border {{ $errors->has('current_password') ? 'border-rose-400 ring-4 ring-rose-500/10' : 'border-slate-200' }} rounded-xl text-sm font-bold text-slate-900 placeholder-slate-400 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 focus:bg-white outline-none transition-all shadow-inner">
                            
                            {{-- Alpine Visibility Toggle --}}
                            <button type="button" @click="showCurrent = !showCurrent" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-indigo-600 focus:outline-none transition-colors">
                                <i class="fa-solid" :class="showCurrent ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <div class="h-px w-full bg-slate-100 my-8"></div>

                    {{-- 2. New Password --}}
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <label for="password" class="text-[11px] font-black text-slate-900 uppercase tracking-widest">New Key Sequence <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] font-bold text-slate-400 font-mono" :class="password.length < 8 ? 'text-amber-500' : 'text-emerald-500'">Min 8 chars</span>
                        </div>
                        <div class="relative group mb-4">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-lock text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                            </div>
                            <input :type="showNew ? 'text' : 'password'" id="password" name="password" x-model="password" required minlength="8"
                                   class="w-full pl-11 pr-12 py-4 bg-slate-50 border {{ $errors->has('password') ? 'border-rose-400 ring-4 ring-rose-500/10' : 'border-slate-200' }} rounded-xl text-sm font-bold text-slate-900 placeholder-slate-400 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 focus:bg-white outline-none transition-all shadow-inner">
                            
                            {{-- Alpine Visibility Toggle --}}
                            <button type="button" @click="showNew = !showNew" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-indigo-600 focus:outline-none transition-colors">
                                <i class="fa-solid" :class="showNew ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>

                            @error('password')
                                <div class="absolute right-0 top-full mt-2 px-3 py-1.5 bg-rose-600 text-white text-[10px] font-bold rounded-lg shadow-lg z-20 flex items-center gap-2">
                                    <div class="absolute -top-1 right-4 w-2 h-2 bg-rose-600 transform rotate-45"></div>
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Live Entropy Meter (Alpine Reactivity) --}}
                        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 shadow-inner mt-2">
                            <div class="flex gap-1.5 h-1.5 mb-3">
                                <div class="h-full flex-1 rounded-full transition-colors duration-500" :class="passwordScore > 0 ? 'bg-rose-500' : 'bg-slate-200'"></div>
                                <div class="h-full flex-1 rounded-full transition-colors duration-500" :class="passwordScore > 1 ? 'bg-amber-500' : 'bg-slate-200'"></div>
                                <div class="h-full flex-1 rounded-full transition-colors duration-500" :class="passwordScore > 2 ? 'bg-emerald-400' : 'bg-slate-200'"></div>
                                <div class="h-full flex-1 rounded-full transition-colors duration-500" :class="passwordScore > 3 ? 'bg-indigo-600 shadow-[0_0_8px_rgba(79,70,229,0.6)]' : 'bg-slate-200'"></div>
                            </div>
                            <div class="flex items-center justify-between">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Entropy Level</p>
                                <p class="text-[10px] font-black uppercase tracking-widest transition-colors duration-300" :class="scoreColor" x-text="scoreLabel"></p>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Confirm Password --}}
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <label for="password_confirmation" class="text-[11px] font-black text-slate-900 uppercase tracking-widest">Verify Key Sequence <span class="text-rose-500">*</span></label>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-check-double text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                            </div>
                            <input :type="showConfirm ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" x-model="confirmPassword" required minlength="8"
                                   class="w-full pl-11 pr-20 py-4 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-900 placeholder-slate-400 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 focus:bg-white outline-none transition-all shadow-inner"
                                   :class="confirmPassword.length > 0 && !passwordsMatch ? 'border-rose-400 ring-4 ring-rose-500/10 focus:border-rose-400 focus:ring-rose-500/10' : ''">
                            
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center gap-3">
                                {{-- Live Match Check Icon --}}
                                <div x-show="confirmPassword.length > 0" x-transition.opacity>
                                    <i class="fa-solid text-base" :class="passwordsMatch ? 'fa-circle-check text-emerald-500 shadow-emerald-500/50' : 'fa-circle-xmark text-rose-500'"></i>
                                </div>
                                <div class="w-px h-5 bg-slate-200" x-show="confirmPassword.length > 0"></div>
                                {{-- Alpine Visibility Toggle --}}
                                <button type="button" @click="showConfirm = !showConfirm" class="text-slate-400 hover:text-indigo-600 focus:outline-none transition-colors">
                                    <i class="fa-solid" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>
                        
                        {{-- Soft Error Message --}}
                        <div x-show="confirmPassword.length > 0 && !passwordsMatch" x-collapse>
                            <p class="text-[10px] font-bold text-rose-500 mt-2 flex items-center gap-1.5">
                                <i class="fa-solid fa-triangle-exclamation"></i> Key sequences do not match.
                            </p>
                        </div>
                    </div>

                    {{-- 4. Command Action Bar --}}
                    <div class="pt-8 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-4">
                        
                        <button type="submit" :disabled="!isFormValid || isSubmitting"
                                class="w-full sm:w-auto px-10 py-4 rounded-xl font-black text-xs uppercase tracking-widest transition-all duration-300 flex items-center justify-center gap-2 focus:outline-none focus:ring-4 focus:ring-indigo-500/20"
                                :class="isFormValid && !isSubmitting ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20 hover:bg-indigo-600 hover:shadow-indigo-500/30 hover:-translate-y-0.5' : 'bg-slate-100 text-slate-400 cursor-not-allowed'">
                            
                            <span x-show="!isSubmitting"><i class="fa-solid fa-lock mr-1.5 opacity-70"></i> Deploy Security Update</span>
                            <span x-show="isSubmitting" style="display: none;"><i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Encrypting...</span>
                        </button>

                    </div>
                </form>

            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('securityEngine', () => ({
        password: '',
        confirmPassword: '',
        
        showCurrent: false,
        showNew: false,
        showConfirm: false,
        
        isSubmitting: false,

        // Dynamic Cryptographic Strength Calculation
        get passwordScore() {
            let score = 0;
            let val = this.password;
            if(!val) return 0;
            if(val.length >= 8) score++;         // Length
            if(val.match(/[A-Z]/)) score++;      // Uppercase
            if(val.match(/[0-9]/)) score++;      // Number
            if(val.match(/[^A-Za-z0-9]/)) score++; // Special Character
            return score;
        },

        get scoreLabel() {
            if(this.password.length === 0) return 'Awaiting Input';
            const labels = ['Weak', 'Fair', 'Good', 'Military Grade'];
            return labels[this.passwordScore - 1] || 'Weak';
        },

        get scoreColor() {
            if(this.password.length === 0) return 'text-slate-400';
            const colors = ['text-rose-500', 'text-amber-500', 'text-emerald-500', 'text-indigo-600'];
            return colors[this.passwordScore - 1] || 'text-rose-500';
        },

        get passwordsMatch() {
            return this.password.length > 0 && this.password === this.confirmPassword;
        },

        // Prevents form submission until rules are met
        get isFormValid() {
            return this.passwordScore >= 2 && this.passwordsMatch;
        }
    }));
});
</script>
@endpush