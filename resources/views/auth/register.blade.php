@extends('layouts.landing')

@section('title', 'Create Account — FinanceAI')
@section('meta_description', 'Join FinanceAI to track income, monitor expenses, and unlock AI-powered financial insights. Start your free account today.')

@section('content')

<section id="auth-register-section" class="auth-section" x-data="registerEngine()" x-cloak>

    {{-- ═══════════════════════════════════════════════════
         ANIMATED BACKGROUND ELEMENTS
    ═══════════════════════════════════════════════════ --}}
    <div class="auth-blob auth-blob--top"></div>
    <div class="auth-blob auth-blob--bottom"></div>
    <div class="auth-grid-overlay"></div>

    <div class="auth-container">

        {{-- ═══════════════════════════════════════════════════
             LEFT PANEL — BRAND HERO (Desktop Only)
        ═══════════════════════════════════════════════════ --}}
        <div class="auth-hero auth-hero--gradient" aria-hidden="true">
            <div class="auth-hero__inner">

                {{-- Headline --}}
                <div class="auth-hero__content">
                    <h1 class="auth-hero__title auth-hero__title--light">
                        Unlock Financial
                        <span class="auth-hero__brand auth-hero__brand--light">Intelligence</span>
                    </h1>

                    <p class="auth-hero__subtitle auth-hero__subtitle--light">
                        FinanceAI helps you track, optimize and grow your financial system with AI precision.
                    </p>
                </div>

                {{-- Finance Illustration --}}
                <div class="auth-hero__image-wrap auth-hero__image-wrap--glow">
                    <img
                        src="{{ asset('img/auth/register-hero.png') }}"
                        alt="FinanceAI Financial Intelligence"
                        class="auth-hero__image"
                        loading="eager"
                        width="520"
                        height="340">

                    <div class="auth-hero__badge auth-hero__badge--dark">
                        <i class="fa-solid fa-brain"></i>
                        AI Insights
                    </div>
                </div>

                {{-- Feature List --}}
                <div class="auth-hero__checklist">
                    <div class="auth-hero__check-item">
                        <i class="fa-solid fa-circle-check"></i>
                        Bank-level encryption
                    </div>
                    <div class="auth-hero__check-item">
                        <i class="fa-solid fa-circle-check"></i>
                        AI-powered insights
                    </div>
                    <div class="auth-hero__check-item">
                        <i class="fa-solid fa-circle-check"></i>
                        Smart budgeting system
                    </div>
                    <div class="auth-hero__check-item">
                        <i class="fa-solid fa-circle-check"></i>
                        Real-time analytics
                    </div>
                </div>

                {{-- Social Proof --}}
                <div class="auth-hero__social-proof">
                    <div class="auth-hero__avatars">
                        <div class="auth-hero__avatar" style="background:#6366f1;">H</div>
                        <div class="auth-hero__avatar" style="background:#8b5cf6;">S</div>
                        <div class="auth-hero__avatar" style="background:#a78bfa;">M</div>
                        <div class="auth-hero__avatar" style="background:#c084fc;">R</div>
                    </div>
                    <p class="auth-hero__proof-text">
                        Join <strong>2,400+</strong> professionals already using FinanceAI
                    </p>
                </div>
            </div>
        </div>


        {{-- ═══════════════════════════════════════════════════
             RIGHT PANEL — REGISTER FORM CARD
        ═══════════════════════════════════════════════════ --}}
        <div class="auth-form-wrap">
            <div class="auth-card" :class="{ 'auth-card--shake': shakeCard }">

                {{-- Mobile Brand --}}
                <div class="auth-card__mobile-brand">
                    <a href="{{ route('home') }}" class="auth-card__logo-link">
                        <div class="auth-card__logo-icon">
                            <i class="fa-solid fa-cube"></i>
                        </div>
                        <span class="auth-card__logo-text">Finance<span>AI</span></span>
                    </a>
                </div>

                {{-- Header --}}
                <div class="auth-card__header">
                    <h2 id="register-heading" class="auth-card__title">Create Account</h2>
                    <p class="auth-card__subtitle">Start your financial journey today</p>
                </div>

                {{-- Server-side Flash Messages --}}
                @if(session('info'))
                    <div class="auth-alert auth-alert--info" role="alert">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="auth-alert auth-alert--error" role="alert">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                {{-- AJAX Toast --}}
                <div class="auth-toast"
                     x-show="toast.show"
                     x-transition:enter="auth-toast--enter"
                     x-transition:enter-start="auth-toast--enter-start"
                     x-transition:enter-end="auth-toast--enter-end"
                     x-transition:leave="auth-toast--leave"
                     x-transition:leave-start="auth-toast--leave-start"
                     x-transition:leave-end="auth-toast--leave-end"
                     :class="'auth-toast--' + toast.type"
                     role="alert"
                     aria-live="assertive">
                    <i class="fa-solid" :class="toast.type === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation'"></i>
                    <span x-text="toast.message"></span>
                </div>

                {{-- Register Form --}}
                <form id="register-form"
                      method="POST"
                      action="{{ route('register.store') }}"
                      class="auth-form"
                      @submit.prevent="handleRegister"
                      novalidate
                      aria-labelledby="register-heading">

                    @csrf

                    {{-- NAME FIELD --}}
                    <div class="auth-field" :class="{ 'auth-field--error': errors.name, 'auth-field--focus': focused === 'name' }">
                        <label for="register-name" class="auth-field__label">Full Name</label>
                        <div class="auth-field__input-wrap">
                            <i class="fa-solid fa-user auth-field__icon"></i>
                            <input
                                type="text"
                                id="register-name"
                                name="name"
                                x-model="form.name"
                                @focus="focused = 'name'"
                                @blur="focused = null; validateName()"
                                required
                                autocomplete="name"
                                placeholder="John Doe"
                                class="auth-field__input"
                                :aria-invalid="errors.name ? 'true' : 'false'"
                                aria-describedby="name-error">
                        </div>
                        <p id="name-error" class="auth-field__error" x-show="errors.name" x-text="errors.name" x-cloak></p>
                    </div>

                    {{-- EMAIL FIELD --}}
                    <div class="auth-field" :class="{ 'auth-field--error': errors.email, 'auth-field--focus': focused === 'email' }">
                        <label for="register-email" class="auth-field__label">Email Address</label>
                        <div class="auth-field__input-wrap">
                            <i class="fa-solid fa-envelope auth-field__icon"></i>
                            <input
                                type="email"
                                id="register-email"
                                name="email"
                                x-model="form.email"
                                @focus="focused = 'email'"
                                @blur="focused = null; validateEmail()"
                                required
                                autocomplete="email"
                                placeholder="you@company.com"
                                class="auth-field__input"
                                {{ session('invited_email') ? 'readonly' : '' }}
                                :style="isInvited ? 'background-color: #f1f5f9; cursor: not-allowed;' : ''"
                                :aria-invalid="errors.email ? 'true' : 'false'"
                                aria-describedby="email-error">
                        </div>
                        <p id="email-error" class="auth-field__error" x-show="errors.email" x-text="errors.email" x-cloak></p>
                    </div>

                    {{-- PASSWORD FIELD --}}
                    <div class="auth-field" :class="{ 'auth-field--error': errors.password, 'auth-field--focus': focused === 'password' }">
                        <label for="register-password" class="auth-field__label">Password</label>
                        <div class="auth-field__input-wrap">
                            <i class="fa-solid fa-lock auth-field__icon"></i>
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                id="register-password"
                                name="password"
                                x-model="form.password"
                                @focus="focused = 'password'"
                                @blur="focused = null; validatePassword()"
                                @input="updateStrength()"
                                required
                                autocomplete="new-password"
                                placeholder="Min 8 characters"
                                class="auth-field__input auth-field__input--password"
                                :aria-invalid="errors.password ? 'true' : 'false'"
                                aria-describedby="password-error password-strength">
                            <button type="button"
                                    @click="showPassword = !showPassword"
                                    class="auth-field__toggle"
                                    :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                    tabindex="-1">
                                <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>

                        {{-- Strength Meter --}}
                        <div class="auth-strength" id="password-strength">
                            <div class="auth-strength__track">
                                <div class="auth-strength__bar"
                                     :style="'width:' + (strength.score * 25) + '%; background:' + strength.color"></div>
                            </div>
                            <p class="auth-strength__text" :style="'color:' + strength.color" x-text="strength.label" x-show="form.password.length > 0"></p>
                        </div>

                        {{-- Strength Criteria --}}
                        <div class="auth-criteria" x-show="form.password.length > 0" x-collapse>
                            <div class="auth-criteria__item" :class="form.password.length >= 8 ? 'auth-criteria__item--pass' : ''">
                                <i class="fa-solid" :class="form.password.length >= 8 ? 'fa-circle-check' : 'fa-circle'"></i>
                                At least 8 characters
                            </div>
                            <div class="auth-criteria__item" :class="/[A-Z]/.test(form.password) ? 'auth-criteria__item--pass' : ''">
                                <i class="fa-solid" :class="/[A-Z]/.test(form.password) ? 'fa-circle-check' : 'fa-circle'"></i>
                                One uppercase letter
                            </div>
                            <div class="auth-criteria__item" :class="/[0-9]/.test(form.password) ? 'auth-criteria__item--pass' : ''">
                                <i class="fa-solid" :class="/[0-9]/.test(form.password) ? 'fa-circle-check' : 'fa-circle'"></i>
                                One number
                            </div>
                            <div class="auth-criteria__item" :class="/[^A-Za-z0-9]/.test(form.password) ? 'auth-criteria__item--pass' : ''">
                                <i class="fa-solid" :class="/[^A-Za-z0-9]/.test(form.password) ? 'fa-circle-check' : 'fa-circle'"></i>
                                One special character
                            </div>
                        </div>

                        <p class="auth-field__error" x-show="errors.password" x-text="errors.password" x-cloak></p>
                    </div>

                    {{-- CONFIRM PASSWORD --}}
                    <div class="auth-field" :class="{ 'auth-field--error': errors.password_confirmation, 'auth-field--focus': focused === 'confirm' }">
                        <label for="register-confirm" class="auth-field__label">Confirm Password</label>
                        <div class="auth-field__input-wrap">
                            <i class="fa-solid fa-lock auth-field__icon"></i>
                            <input
                                :type="showConfirm ? 'text' : 'password'"
                                id="register-confirm"
                                name="password_confirmation"
                                x-model="form.password_confirmation"
                                @focus="focused = 'confirm'"
                                @blur="focused = null; validateConfirm()"
                                required
                                autocomplete="new-password"
                                placeholder="Repeat password"
                                class="auth-field__input auth-field__input--password"
                                :aria-invalid="errors.password_confirmation ? 'true' : 'false'"
                                aria-describedby="confirm-error">
                            <button type="button"
                                    @click="showConfirm = !showConfirm"
                                    class="auth-field__toggle"
                                    :aria-label="showConfirm ? 'Hide password' : 'Show password'"
                                    tabindex="-1">
                                <i class="fa-solid" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>

                        {{-- Match Indicator --}}
                        <div class="auth-match" x-show="form.password_confirmation.length > 0">
                            <template x-if="form.password === form.password_confirmation && form.password.length > 0">
                                <span class="auth-match--yes"><i class="fa-solid fa-circle-check"></i> Passwords match</span>
                            </template>
                            <template x-if="form.password !== form.password_confirmation && form.password_confirmation.length > 0">
                                <span class="auth-match--no"><i class="fa-solid fa-circle-xmark"></i> Passwords don't match</span>
                            </template>
                        </div>

                        <p id="confirm-error" class="auth-field__error" x-show="errors.password_confirmation" x-text="errors.password_confirmation" x-cloak></p>
                    </div>

                    {{-- TERMS CHECKBOX --}}
                    <div class="auth-field">
                        <label class="auth-checkbox" for="terms-checkbox">
                            <input type="checkbox"
                                   id="terms-checkbox"
                                   x-model="form.terms"
                                   required
                                   class="auth-checkbox__input">
                            <div class="auth-checkbox__box" :class="{ 'auth-checkbox__box--checked': form.terms }">
                                <i class="fa-solid fa-check auth-checkbox__icon" x-show="form.terms"></i>
                            </div>
                            <span class="auth-checkbox__text">
                                I agree to the
                                <a href="{{ route('terms') }}" class="auth-link" target="_blank">Terms</a>
                                &amp;
                                <a href="{{ route('privacy') }}" class="auth-link" target="_blank">Privacy Policy</a>
                            </span>
                        </label>
                        <p class="auth-field__error" x-show="errors.terms" x-text="errors.terms" x-cloak></p>
                    </div>

                    {{-- SUBMIT BUTTON --}}
                    <button id="register-submit-btn"
                            type="submit"
                            class="auth-btn auth-btn--primary"
                            :disabled="isLoading"
                            :class="{ 'auth-btn--loading': isLoading }">
                        <span x-show="!isLoading" class="auth-btn__text">
                            <i class="fa-solid fa-rocket"></i>
                            Create Account
                        </span>
                        <span x-show="isLoading" class="auth-btn__loader" x-cloak>
                            <svg class="auth-spinner" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="auth-spinner__track"/>
                                <path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="3" stroke-linecap="round" class="auth-spinner__head"/>
                            </svg>
                            Creating account...
                        </span>
                    </button>

                    {{-- DIVIDER --}}
                    <div class="auth-divider">
                        <span>OR</span>
                    </div>

                    {{-- SOCIAL SIGNUP (UI Only) --}}
                    <button type="button" class="auth-btn auth-btn--social" @click="showSocialToast()">
                        <img src="https://www.svgrepo.com/show/355037/google.svg" alt="" class="auth-btn__social-icon" width="20" height="20">
                        Sign up with Google
                    </button>

                    {{-- LOGIN LINK --}}
                    <p class="auth-footer-link">
                        Already have an account?
                        <a href="{{ route('login') }}" class="auth-link auth-link--bold">
                            Sign In
                        </a>
                    </p>
                </form>

                {{-- SSL Badge --}}
                <div class="auth-card__ssl">
                    <i class="fa-solid fa-shield-halved"></i>
                    Secured with 256-bit SSL Encryption
                </div>
            </div>
        </div>

    </div>
</section>


{{-- ═══════════════════════════════════════════════════════════
     SCOPED STYLES — Auth Module (Register Additions)
═══════════════════════════════════════════════════════════ --}}
@push('styles')
<style>
/* ─── AUTH LAYOUT ─── */
.auth-section {
    min-height: 100vh;
    display: grid;
    grid-template-columns: 1fr;
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #eef2ff 0%, #ffffff 40%, #faf5ff 100%);
}

@media (min-width: 1024px) {
    .auth-section {
        grid-template-columns: 1fr 1fr;
    }
}

/* ─── BACKGROUND ─── */
.auth-blob {
    position: absolute;
    width: 600px;
    height: 600px;
    border-radius: 50%;
    filter: blur(160px);
    pointer-events: none;
    z-index: 0;
    will-change: transform;
}

.auth-blob--top {
    top: -200px;
    left: -200px;
    background: rgba(129, 140, 248, 0.25);
    animation: auth-float 16s ease-in-out infinite;
}

.auth-blob--bottom {
    bottom: -200px;
    right: -200px;
    background: rgba(192, 132, 252, 0.25);
    animation: auth-float 16s ease-in-out infinite 3s;
}

.auth-grid-overlay {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(99, 102, 241, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(99, 102, 241, 0.03) 1px, transparent 1px);
    background-size: 60px 60px;
    pointer-events: none;
    z-index: 0;
}

/* ─── AUTH CONTAINER ─── */
.auth-container {
    display: contents;
}

/* ─── LEFT HERO PANEL ─── */
.auth-hero {
    display: none;
    flex-direction: column;
    justify-content: center;
    padding: 3rem 4rem;
    position: relative;
    z-index: 10;
}

@media (min-width: 1024px) {
    .auth-hero { display: flex; }
}

.auth-hero--gradient {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #9333ea 100%);
    color: white;
}

.auth-hero__inner {
    max-width: 520px;
    display: flex;
    flex-direction: column;
    gap: 1.75rem;
}

.auth-hero__title {
    font-size: clamp(2rem, 3.5vw, 3rem);
    font-weight: 800;
    color: #0f172a;
    line-height: 1.15;
    letter-spacing: -0.02em;
}

.auth-hero__title--light {
    color: white;
}

.auth-hero__brand {
    display: block;
    background: linear-gradient(135deg, #4f46e5, #9333ea);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.auth-hero__brand--light {
    background: none;
    -webkit-background-clip: unset;
    background-clip: unset;
    color: rgba(255,255,255,0.7);
}

.auth-hero__subtitle {
    font-size: 1.05rem;
    color: #64748b;
    line-height: 1.7;
    max-width: 440px;
}

.auth-hero__subtitle--light {
    color: rgba(255,255,255,0.8);
}

/* Hero Image */
.auth-hero__image-wrap {
    position: relative;
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 25px 60px -15px rgba(79, 70, 229, 0.2);
    transition: transform 0.4s ease, box-shadow 0.4s ease;
}

.auth-hero__image-wrap--glow {
    box-shadow: 0 25px 60px -15px rgba(0,0,0,0.3);
}

.auth-hero__image-wrap:hover {
    transform: translateY(-4px);
    box-shadow: 0 35px 80px -15px rgba(0,0,0,0.35);
}

.auth-hero__image {
    width: 100%;
    height: auto;
    display: block;
    object-fit: cover;
    max-height: 260px;
}

.auth-hero__badge {
    position: absolute;
    top: -2px;
    right: -2px;
    background: white;
    color: #4f46e5;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 0 1.25rem 0 0.75rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 6px;
}

.auth-hero__badge--dark {
    background: rgba(0,0,0,0.7);
    color: white;
    backdrop-filter: blur(8px);
}

/* Checklist */
.auth-hero__checklist {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}

.auth-hero__check-item {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 0.88rem;
    font-weight: 500;
    color: rgba(255,255,255,0.85);
}

.auth-hero__check-item i {
    color: #a5f3fc;
    font-size: 0.8rem;
}

/* Social Proof */
.auth-hero__social-proof {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding-top: 0.5rem;
    border-top: 1px solid rgba(255,255,255,0.15);
}

.auth-hero__avatars {
    display: flex;
}

.auth-hero__avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.65rem;
    font-weight: 800;
    color: white;
    border: 2px solid rgba(255,255,255,0.3);
    margin-left: -8px;
}

.auth-hero__avatar:first-child {
    margin-left: 0;
}

.auth-hero__proof-text {
    font-size: 0.78rem;
    color: rgba(255,255,255,0.7);
    font-weight: 500;
}

.auth-hero__proof-text strong {
    color: white;
}

/* ─── FORM PANEL ─── */
.auth-form-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1.5rem;
    position: relative;
    z-index: 10;
}

@media (min-width: 640px) {
    .auth-form-wrap { padding: 2.5rem; }
}

/* ─── AUTH CARD ─── */
.auth-card {
    width: 100%;
    max-width: 460px;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    border-radius: 1.75rem;
    box-shadow: 0 50px 120px rgba(15, 23, 42, 0.12);
    padding: 2.25rem;
    animation: auth-fadeIn 0.6s ease forwards;
    transition: box-shadow 0.3s ease;
}

.auth-card:hover {
    box-shadow: 0 60px 150px rgba(15, 23, 42, 0.16);
}

@media (min-width: 640px) {
    .auth-card { padding: 2.5rem; }
}

.auth-card--shake {
    animation: auth-shake 0.5s ease;
}

/* Mobile Brand */
.auth-card__mobile-brand {
    display: flex;
    justify-content: center;
    margin-bottom: 1.25rem;
}

@media (min-width: 1024px) {
    .auth-card__mobile-brand { display: none; }
}

.auth-card__logo-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.auth-card__logo-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

.auth-card__logo-text {
    font-size: 1.35rem;
    font-weight: 900;
    color: #0f172a;
    letter-spacing: -0.02em;
}

.auth-card__logo-text span {
    color: #4f46e5;
}

/* Header */
.auth-card__header {
    text-align: center;
    margin-bottom: 1.5rem;
}

.auth-card__title {
    font-size: 1.75rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.02em;
}

.auth-card__subtitle {
    color: #94a3b8;
    font-size: 0.9rem;
    margin-top: 0.35rem;
    font-weight: 500;
}

/* ─── ALERTS ─── */
.auth-alert {
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    padding: 0.85rem 1rem;
    border-radius: 0.85rem;
    font-size: 0.82rem;
    font-weight: 600;
    margin-bottom: 1.25rem;
    animation: auth-fadeIn 0.3s ease;
}

.auth-alert i { margin-top: 1px; flex-shrink: 0; }
.auth-alert--info { background: #eef2ff; border: 1px solid #c7d2fe; color: #4338ca; }
.auth-alert--error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }

/* ─── TOAST ─── */
.auth-toast {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border-radius: 0.85rem;
    font-size: 0.82rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.auth-toast--success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; }
.auth-toast--error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
.auth-toast--enter { transition: all 0.3s ease; }
.auth-toast--enter-start { opacity: 0; transform: translateY(-8px); }
.auth-toast--enter-end { opacity: 1; transform: translateY(0); }
.auth-toast--leave { transition: all 0.2s ease; }
.auth-toast--leave-start { opacity: 1; }
.auth-toast--leave-end { opacity: 0; transform: translateY(-8px); }

/* ─── FORM ─── */
.auth-form {
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
}

/* ─── FIELD ─── */
.auth-field__label {
    display: block;
    font-size: 0.75rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 0.35rem;
    letter-spacing: 0.02em;
    text-transform: uppercase;
}

.auth-field__input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}

.auth-field__icon {
    position: absolute;
    left: 14px;
    color: #94a3b8;
    font-size: 0.85rem;
    pointer-events: none;
    transition: color 0.25s ease;
}

.auth-field__input {
    width: 100%;
    padding: 12px 16px 12px 42px;
    border-radius: 0.85rem;
    border: 1.5px solid #e2e8f0;
    background: rgba(255,255,255,0.8);
    font-size: 0.9rem;
    font-weight: 500;
    color: #0f172a;
    transition: all 0.25s ease;
    outline: none;
    font-family: inherit;
}

.auth-field__input::placeholder { color: #cbd5e1; font-weight: 400; }

.auth-field__input:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
    background: white;
}

.auth-field--focus .auth-field__icon { color: #6366f1; }

.auth-field--error .auth-field__input {
    border-color: #f87171;
    box-shadow: 0 0 0 3px rgba(248, 113, 113, 0.1);
}

.auth-field--error .auth-field__icon { color: #f87171; }

.auth-field__input--password { padding-right: 48px; }

.auth-field__toggle {
    position: absolute;
    right: 12px;
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 4px;
    font-size: 0.9rem;
    transition: color 0.2s ease;
    line-height: 1;
}

.auth-field__toggle:hover { color: #6366f1; }

.auth-field__error {
    font-size: 0.75rem;
    color: #ef4444;
    font-weight: 600;
    margin-top: 0.3rem;
}

/* ─── PASSWORD STRENGTH ─── */
.auth-strength {
    margin-top: 0.5rem;
}

.auth-strength__track {
    height: 4px;
    background: #e2e8f0;
    border-radius: 999px;
    overflow: hidden;
}

.auth-strength__bar {
    height: 100%;
    border-radius: 999px;
    transition: all 0.4s ease;
    width: 0;
}

.auth-strength__text {
    font-size: 0.72rem;
    font-weight: 700;
    margin-top: 0.3rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* ─── PASSWORD CRITERIA ─── */
.auth-criteria {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.25rem 0.75rem;
    margin-top: 0.4rem;
}

.auth-criteria__item {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.7rem;
    font-weight: 600;
    color: #94a3b8;
    transition: color 0.2s ease;
}

.auth-criteria__item--pass {
    color: #16a34a;
}

.auth-criteria__item i {
    font-size: 0.6rem;
}

/* ─── PASSWORD MATCH ─── */
.auth-match {
    margin-top: 0.35rem;
    font-size: 0.72rem;
    font-weight: 700;
}

.auth-match--yes { color: #16a34a; display: flex; align-items: center; gap: 0.3rem; }
.auth-match--no { color: #ef4444; display: flex; align-items: center; gap: 0.3rem; }

/* ─── CHECKBOX ─── */
.auth-checkbox {
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    cursor: pointer;
    user-select: none;
}

.auth-checkbox__input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.auth-checkbox__box {
    width: 20px;
    height: 20px;
    border-radius: 6px;
    border: 2px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 1px;
    transition: all 0.2s ease;
}

.auth-checkbox__box--checked {
    background: #6366f1;
    border-color: #6366f1;
}

.auth-checkbox__icon {
    color: white;
    font-size: 0.6rem;
}

.auth-checkbox__input:focus-visible + .auth-checkbox__box {
    outline: 2px solid #6366f1;
    outline-offset: 2px;
}

.auth-checkbox__text {
    font-size: 0.82rem;
    color: #64748b;
    font-weight: 500;
    line-height: 1.4;
}

/* ─── LINKS ─── */
.auth-link {
    font-size: inherit;
    color: #6366f1;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.2s ease;
}

.auth-link:hover { color: #4338ca; text-decoration: underline; }
.auth-link--bold { font-weight: 700; }

/* ─── BUTTONS ─── */
.auth-btn {
    width: 100%;
    padding: 13px 20px;
    border-radius: 0.85rem;
    font-size: 0.9rem;
    font-weight: 700;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.25s ease;
    font-family: inherit;
    position: relative;
    overflow: hidden;
}

.auth-btn--primary {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: white;
    box-shadow: 0 8px 24px rgba(79, 70, 229, 0.3);
}

.auth-btn--primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 12px 32px rgba(79, 70, 229, 0.4);
}

.auth-btn--loading { opacity: 0.85; cursor: not-allowed; }

.auth-btn__text, .auth-btn__loader {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.auth-btn--social {
    background: white;
    color: #334155;
    border: 1.5px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.auth-btn--social:hover {
    background: #f8fafc;
    border-color: #c7d2fe;
    transform: translateY(-1px);
}

.auth-btn__social-icon { width: 20px; height: 20px; }

/* Spinner */
.auth-spinner {
    width: 20px;
    height: 20px;
    animation: auth-spin 0.8s linear infinite;
}

.auth-spinner__track { opacity: 0.25; }
.auth-spinner__head { opacity: 0.9; }

/* ─── DIVIDER ─── */
.auth-divider {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.auth-divider::before,
.auth-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #e2e8f0;
}

.auth-divider span {
    font-size: 0.7rem;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

/* ─── FOOTER ─── */
.auth-footer-link {
    text-align: center;
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 500;
}

.auth-card__ssl {
    margin-top: 1.5rem;
    text-align: center;
    font-size: 0.7rem;
    color: #94a3b8;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
}

.auth-card__ssl i { color: #6366f1; }

/* ─── ANIMATIONS ─── */
@keyframes auth-float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    33% { transform: translateY(-20px) rotate(2deg); }
    66% { transform: translateY(10px) rotate(-1deg); }
}

@keyframes auth-fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes auth-shake {
    0%, 100% { transform: translateX(0); }
    20% { transform: translateX(-8px); }
    40% { transform: translateX(8px); }
    60% { transform: translateX(-5px); }
    80% { transform: translateX(5px); }
}

@keyframes auth-spin {
    to { transform: rotate(360deg); }
}

/* ─── RESPONSIVE ─── */
@media (max-width: 639px) {
    .auth-card {
        padding: 1.75rem;
        border-radius: 1.25rem;
    }
    .auth-card__title { font-size: 1.5rem; }
    .auth-criteria { grid-template-columns: 1fr; }
}

@media (max-width: 374px) {
    .auth-form-wrap { padding: 1rem 0.75rem; }
    .auth-card { padding: 1.5rem; }
}
</style>
@endpush


{{-- ═══════════════════════════════════════════════════════════
     ALPINE.JS — Register Engine
═══════════════════════════════════════════════════════════ --}}
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('registerEngine', () => ({
        form: {
            name: '{{ old("name") }}',
            email: '{{ old("email", session("invited_email", "")) }}',
            password: '',
            password_confirmation: '',
            terms: false,
        },
        errors: {
            name: null,
            email: null,
            password: null,
            password_confirmation: null,
            terms: null,
        },
        focused: null,
        showPassword: false,
        showConfirm: false,
        isLoading: false,
        shakeCard: false,
        isInvited: {{ session('invited_email') ? 'true' : 'false' }},
        toast: { show: false, message: '', type: 'error' },
        strength: { score: 0, label: '', color: '#e2e8f0' },

        // ── Validators ──
        validateName() {
            if (!this.form.name.trim()) {
                this.errors.name = 'Full name is required.';
                return false;
            }
            if (this.form.name.trim().length < 2) {
                this.errors.name = 'Name must be at least 2 characters.';
                return false;
            }
            this.errors.name = null;
            return true;
        },

        validateEmail() {
            if (!this.form.email.trim()) {
                this.errors.email = 'Email address is required.';
                return false;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email)) {
                this.errors.email = 'Please enter a valid email address.';
                return false;
            }
            this.errors.email = null;
            return true;
        },

        validatePassword() {
            if (!this.form.password) {
                this.errors.password = 'Password is required.';
                return false;
            }
            if (this.form.password.length < 8) {
                this.errors.password = 'Password must be at least 8 characters.';
                return false;
            }
            this.errors.password = null;
            return true;
        },

        validateConfirm() {
            if (!this.form.password_confirmation) {
                this.errors.password_confirmation = 'Please confirm your password.';
                return false;
            }
            if (this.form.password !== this.form.password_confirmation) {
                this.errors.password_confirmation = 'Passwords do not match.';
                return false;
            }
            this.errors.password_confirmation = null;
            return true;
        },

        validate() {
            const results = [
                this.validateName(),
                this.validateEmail(),
                this.validatePassword(),
                this.validateConfirm(),
            ];
            if (!this.form.terms) {
                this.errors.terms = 'You must accept the Terms & Privacy Policy.';
                results.push(false);
            } else {
                this.errors.terms = null;
            }
            return results.every(Boolean);
        },

        // ── Strength Meter ──
        updateStrength() {
            const val = this.form.password;
            let score = 0;

            if (val.length > 6) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const levels = [
                { label: '', color: '#e2e8f0' },
                { label: 'Weak', color: '#ef4444' },
                { label: 'Fair', color: '#f59e0b' },
                { label: 'Strong', color: '#10b981' },
                { label: 'Very Strong', color: '#6366f1' },
            ];

            this.strength = { score, ...levels[score] };
        },

        // ── AJAX Register ──
        async handleRegister() {
            if (!this.validate()) {
                this.triggerShake();
                return;
            }

            this.isLoading = true;
            this.toast.show = false;

            try {
                const response = await fetch('{{ route("register.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        name: this.form.name.trim(),
                        email: this.form.email.trim().toLowerCase(),
                        password: this.form.password,
                        password_confirmation: this.form.password_confirmation,
                    }),
                });

                if (response.status === 429) {
                    this.showToastMsg('Too many attempts. Please try again later.', 'error');
                    this.isLoading = false;
                    return;
                }

                if (response.status === 422) {
                    const data = await response.json();
                    // Map server errors to field errors
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            if (this.errors.hasOwnProperty(key)) {
                                this.errors[key] = data.errors[key][0];
                            }
                        });
                    }
                    const firstError = data.errors
                        ? Object.values(data.errors)[0][0]
                        : 'Please fix the errors below.';
                    this.showToastMsg(firstError, 'error');
                    this.triggerShake();
                    this.isLoading = false;
                    return;
                }

                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }

                // JSON success response from AuthController
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.redirect) {
                        this.showToastMsg(data.message || 'Account created! Redirecting...', 'success');
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 600);
                        return;
                    }
                    // Fallback
                    window.location.href = '{{ url("/user/dashboard") }}';
                    return;
                }

                this.showToastMsg('Registration failed. Please try again.', 'error');
                this.triggerShake();

            } catch (error) {
                console.warn('AJAX register failed, falling back:', error);
                const form = document.getElementById('register-form');
                form.submit();
            } finally {
                this.isLoading = false;
            }
        },

        triggerShake() {
            this.shakeCard = true;
            setTimeout(() => { this.shakeCard = false; }, 500);
        },

        showToastMsg(message, type = 'error') {
            this.toast = { show: true, message, type };
            setTimeout(() => { this.toast.show = false; }, 5000);
        },

        showSocialToast() {
            this.showToastMsg('Google signup is coming soon!', 'error');
        },
    }));
});
</script>
@endpush

@endsection