@extends('layouts.app')

@section('title', 'Configure Identity | FinanceAI')

@section('content')

@php
    // ================= 1. SAFE DATA EXTRACTION =================
    $user = auth()->user();
    abort_unless($user, 403, 'Unauthorized Access.');

    $role = $user->role ?? 'Operator';
    $isBlocked = property_exists($user, 'is_blocked') ? $user->is_blocked : false;
    $emailVerified = method_exists($user, 'hasVerifiedEmail') ? $user->hasVerifiedEmail() : false;

    // Smart Role Formatting
    $roleTheme = match(strtolower($role)) {
        'admin', 'master admin' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'border' => 'border-rose-200', 'icon' => 'fa-crown'],
        'manager' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'border-emerald-200', 'icon' => 'fa-shield-halved'],
        default => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'border' => 'border-indigo-200', 'icon' => 'fa-user-astronaut']
    };
@endphp

<div x-data="profileConfiguration('{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}')" class="min-h-screen bg-[#f8fafc] pb-32 font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden">

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
                        <li class="text-indigo-600">Configuration</li>
                    </ol>
                </nav>
                <h1 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight">Identity Settings</h1>
                <p class="text-slate-500 text-sm font-medium mt-3 flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-indigo-400"></i> Manage your personal telemetry and secure access credentials.
                </p>
            </div>
            
            <a href="{{ Route::has('user.profile.index') ? route('user.profile.index') : url('/dashboard') }}" class="px-5 py-3 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-sm shadow-sm hover:bg-slate-50 hover:text-indigo-600 hover:border-indigo-300 transition-all flex items-center gap-2 focus:outline-none w-full sm:w-auto justify-center">
                <i class="fa-solid fa-arrow-left"></i> Return to Hub
            </a>
        </div>

        {{-- ================= 2. GLOBAL ERROR CATCHER ================= --}}
        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 rounded-[1.5rem] p-6 shadow-sm flex items-start gap-4 animate-[pulse_0.5s_ease-in-out_1]">
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 border border-rose-200">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-rose-900 uppercase tracking-widest mb-2">Configuration Rejected</h3>
                    <ul class="space-y-1.5">
                        @foreach($errors->all() as $error)
                            <li class="text-xs font-bold text-rose-600 flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-rose-400"></span> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- ================= 3. DUAL COLUMN ARCHITECTURE ================= --}}
        <div class="grid xl:grid-cols-12 gap-8 items-start">

            {{-- LEFT COLUMN: LIVE IDENTITY PREVIEW --}}
            <div class="xl:col-span-4 space-y-6 sticky top-28">
                
                {{-- Live Profile Card --}}
                <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8 relative overflow-hidden group text-center flex flex-col items-center">
                    <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(#4f46e5 1px, transparent 1px); background-size: 24px 24px;"></div>
                    
                    {{-- Reactive Holographic Avatar --}}
                    <div class="relative shrink-0 mb-6">
                        <div class="h-32 w-32 rounded-[2rem] bg-gradient-to-br from-indigo-600 to-sky-400 p-[3px] shadow-xl shadow-indigo-500/20 transform group-hover:scale-105 transition-transform duration-500">
                            <div class="h-full w-full bg-white rounded-[1.8rem] flex items-center justify-center overflow-hidden">
                                <span class="text-5xl font-black bg-clip-text text-transparent bg-gradient-to-br from-indigo-600 to-sky-500" x-text="initials"></span>
                            </div>
                        </div>
                        
                        <div class="absolute -bottom-2 -right-2 h-10 w-10 border-[4px] border-white rounded-full flex items-center justify-center shadow-sm transition-colors duration-300"
                             :class="isBlocked ? 'bg-rose-50' : 'bg-emerald-50'">
                            <span x-show="!isBlocked" class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                            </span>
                            <i x-show="isBlocked" style="display:none;" class="fa-solid fa-lock text-[10px] text-rose-500"></i>
                        </div>
                    </div>

                    {{-- Reactive Text --}}
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight w-full truncate px-4 transition-colors" :class="hasChanges ? 'text-indigo-600' : ''" x-text="name || 'Unnamed Node'"></h2>
                    <p class="text-xs font-bold text-slate-500 w-full truncate px-4 mt-1 font-mono" x-text="email || 'user@network.local'"></p>

                    <div class="mt-6 flex flex-wrap justify-center gap-2">
                        <span class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest border shadow-sm {{ $roleTheme['bg'] }} {{ $roleTheme['text'] }} {{ $roleTheme['border'] }}">
                            <i class="fa-solid {{ $roleTheme['icon'] }} mr-1"></i> {{ $role }}
                        </span>
                        
                        <span class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest border shadow-sm flex items-center gap-1.5"
                              :class="emailVerified ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'">
                            <i class="fa-solid" :class="emailVerified ? 'fa-shield-check' : 'fa-triangle-exclamation'"></i>
                            <span x-text="emailVerified ? 'Verified' : 'Unverified'"></span>
                        </span>
                    </div>
                </div>

                {{-- Cryptographic Status --}}
                <div class="bg-slate-900 rounded-[2rem] border border-slate-800 p-6 shadow-xl relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="flex items-center justify-between relative z-10">
                        <div>
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1">Network Join Date</p>
                            <p class="text-sm font-bold text-white">{{ optional($user->created_at)->format('d M Y') }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-400">
                            <i class="fa-regular fa-calendar-check"></i>
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN: THE FORM ENGINE --}}
            <div class="xl:col-span-8 bg-white rounded-[2.5rem] border border-slate-200 shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8 md:p-12 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-indigo-50 to-transparent rounded-bl-full pointer-events-none"></div>
                
                <h2 class="text-2xl font-black text-slate-900 mb-8 flex items-center gap-3 relative z-10">
                    <i class="fa-solid fa-fingerprint text-indigo-500"></i> Identity Parameters
                </h2>

                <form method="POST" action="{{ route('user.profile.update') }}" @submit="isSubmitting = true" class="space-y-8 relative z-10">
                    @csrf
                    {{-- Note: Usually updates require PUT/PATCH, check your specific route requirements --}}
                    {{-- @method('PUT') --}}

                    {{-- 1. Full Name --}}
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <label for="name" class="text-[11px] font-black text-slate-900 uppercase tracking-widest">Authorized Node Name <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] font-bold text-slate-400 font-mono" :class="name.length < 3 ? 'text-amber-500' : 'text-emerald-500'">Min 3 chars</span>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-user text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                            </div>
                            <input type="text" id="name" name="name" x-model="name" required
                                   class="w-full pl-11 pr-4 py-4 bg-slate-50 border {{ $errors->has('name') ? 'border-rose-400 ring-4 ring-rose-500/10' : 'border-slate-200' }} rounded-xl text-sm font-bold text-slate-900 placeholder-slate-400 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 focus:bg-white outline-none transition-all shadow-inner">
                            
                            @error('name')
                                <div class="absolute right-0 top-full mt-2 px-3 py-1.5 bg-rose-600 text-white text-[10px] font-bold rounded-lg shadow-lg z-20 flex items-center gap-2 animate-[pulse_0.5s_ease-in-out_1]">
                                    <div class="absolute -top-1 right-4 w-2 h-2 bg-rose-600 transform rotate-45"></div>
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- 2. Email Address --}}
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <label for="email" class="text-[11px] font-black text-slate-900 uppercase tracking-widest">Primary Comm Link (Email) <span class="text-rose-500">*</span></label>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-envelope text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                            </div>
                            <input type="email" id="email" name="email" x-model="email" required
                                   class="w-full pl-11 pr-4 py-4 bg-slate-50 border {{ $errors->has('email') ? 'border-rose-400 ring-4 ring-rose-500/10' : 'border-slate-200' }} rounded-xl text-sm font-bold text-slate-900 placeholder-slate-400 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 focus:bg-white outline-none transition-all shadow-inner">
                            
                            @error('email')
                                <div class="absolute right-0 top-full mt-2 px-3 py-1.5 bg-rose-600 text-white text-[10px] font-bold rounded-lg shadow-lg z-20 flex items-center gap-2">
                                    <div class="absolute -top-1 right-4 w-2 h-2 bg-rose-600 transform rotate-45"></div>
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div x-show="email !== originalEmail" x-collapse>
                            <p class="text-[10px] font-bold text-amber-600 mt-2 flex items-center gap-1.5 bg-amber-50 px-3 py-2 rounded-lg border border-amber-200">
                                <i class="fa-solid fa-circle-info"></i> Modifying this link will require re-verification of your identity.
                            </p>
                        </div>
                    </div>

                    <div class="h-px w-full bg-slate-100 my-8"></div>

                    {{-- 3. Security Credentials (Optional) --}}
                    <div>
                        <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-slate-400"></i> Update Security Credentials
                        </h3>
                        <p class="text-xs font-bold text-slate-400 mb-6">Leave these fields completely blank if you wish to retain your current cryptographic key.</p>

                        <div class="grid md:grid-cols-2 gap-6">
                            {{-- New Password --}}
                            <div>
                                <label for="password" class="block text-[10px] font-black text-slate-900 uppercase tracking-widest mb-2">New Key Sequence</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-lock text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input type="password" id="password" name="password" x-model="password" placeholder="••••••••"
                                           class="w-full pl-11 pr-4 py-4 bg-slate-50 border {{ $errors->has('password') ? 'border-rose-400' : 'border-slate-200' }} rounded-xl text-sm font-bold text-slate-900 placeholder-slate-400 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 focus:bg-white outline-none transition-all shadow-inner">
                                    
                                    {{-- Live Strength Meter (NEW FUN) --}}
                                    <div class="absolute bottom-1 left-3 right-3 flex gap-1 h-0.5 opacity-0 group-focus-within:opacity-100 transition-opacity" x-show="password.length > 0">
                                        <div class="h-full flex-1 rounded-full transition-colors duration-300" :class="passwordStrength > 0 ? 'bg-rose-500' : 'bg-slate-200'"></div>
                                        <div class="h-full flex-1 rounded-full transition-colors duration-300" :class="passwordStrength > 25 ? 'bg-amber-500' : 'bg-slate-200'"></div>
                                        <div class="h-full flex-1 rounded-full transition-colors duration-300" :class="passwordStrength > 50 ? 'bg-emerald-400' : 'bg-slate-200'"></div>
                                        <div class="h-full flex-1 rounded-full transition-colors duration-300" :class="passwordStrength > 75 ? 'bg-emerald-600' : 'bg-slate-200'"></div>
                                    </div>
                                </div>
                                @error('password') <p class="text-[10px] font-bold text-rose-500 mt-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
                            </div>

                            {{-- Confirm Password --}}
                            <div>
                                <label for="password_confirmation" class="block text-[10px] font-black text-slate-900 uppercase tracking-widest mb-2">Verify Key Sequence</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-check-double text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input type="password" id="password_confirmation" name="password_confirmation" x-model="passwordConfirmation" placeholder="••••••••"
                                           class="w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-900 placeholder-slate-400 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 focus:bg-white outline-none transition-all shadow-inner">
                                    
                                    {{-- Live Match Check --}}
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none" x-show="password.length > 0 && passwordConfirmation.length > 0">
                                        <i class="fa-solid" :class="password === passwordConfirmation ? 'fa-check text-emerald-500' : 'fa-xmark text-rose-500'"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Command Action Bar --}}
                    <div class="pt-8 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                        
                        <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest" :class="hasChanges ? 'text-amber-500' : 'text-slate-400'">
                            <span class="w-1.5 h-1.5 rounded-full" :class="hasChanges ? 'bg-amber-500 animate-pulse' : 'bg-slate-300'"></span>
                            <span x-text="hasChanges ? 'Unsaved Modifications' : 'System Synchronized'"></span>
                        </div>

                        <div class="flex gap-3 w-full sm:w-auto">
                            <button type="button" @click="resetForm()" x-show="hasChanges" x-transition 
                                    class="px-6 py-4 bg-slate-100 text-slate-600 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-colors focus:outline-none">
                                Revert
                            </button>

                            <button type="submit" :disabled="!hasChanges || isSubmitting"
                                    class="flex-1 sm:flex-none px-8 py-4 rounded-xl font-black text-xs uppercase tracking-widest transition-all duration-300 flex items-center justify-center gap-2 focus:outline-none focus:ring-4 focus:ring-indigo-500/20"
                                    :class="hasChanges && !isSubmitting ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20 hover:bg-indigo-600 hover:shadow-indigo-500/30 hover:-translate-y-0.5' : 'bg-slate-100 text-slate-400 cursor-not-allowed'">
                                
                                <span x-show="!isSubmitting">Deploy Configuration</span>
                                <span x-show="isSubmitting" style="display: none;"><i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Deploying...</span>
                            </button>
                        </div>

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
    Alpine.data('profileConfiguration', (initName, initEmail) => ({
        originalName: initName,
        originalEmail: initEmail,
        
        name: initName,
        email: initEmail,
        password: '',
        passwordConfirmation: '',
        
        isBlocked: {{ $isBlocked ? 'true' : 'false' }},
        emailVerified: {{ $emailVerified ? 'true' : 'false' }},
        isSubmitting: false,

        // Computes if any field has been touched
        get hasChanges() {
            return this.name.trim() !== this.originalName || 
                   this.email.trim() !== this.originalEmail || 
                   this.password.length > 0 || 
                   this.passwordConfirmation.length > 0;
        },

        // Generates Avatar Initials dynamically
        get initials() {
            let n = this.name.trim();
            return n.length > 0 ? n.charAt(0).toUpperCase() : 'U';
        },

        // Calculates Password Strength (Length, Upper, Num, Special)
        get passwordStrength() {
            let score = 0;
            let val = this.password;
            if(!val) return 0;
            if(val.length >= 8) score += 25;
            if(val.match(/[A-Z]/)) score += 25;
            if(val.match(/[0-9]/)) score += 25;
            if(val.match(/[^A-Za-z0-9]/)) score += 25;
            return score;
        },

        resetForm() {
            this.name = this.originalName;
            this.email = this.originalEmail;
            this.password = '';
            this.passwordConfirmation = '';
        },

        // Utilizes the Global Toast Engine built previously
        async copyToClipboard(text) {
            try {
                await navigator.clipboard.writeText(text);
                this.$dispatch('notify', { message: 'Primary Comm Link copied to clipboard.', type: 'success' });
            } catch (err) {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                this.$dispatch('notify', { message: 'Primary Comm Link copied to clipboard.', type: 'success' });
            }
        }
    }));
});
</script>
@endpush