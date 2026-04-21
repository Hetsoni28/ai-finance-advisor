@extends('layouts.landing')

@section('title', 'Sign In — FinanceAI')
@section('meta_description', 'Securely sign in to your FinanceAI dashboard to track income, monitor expenses, and unlock AI-powered financial insights.')

@section('content')

<section id="auth-login-section" class="auth-section" x-data="loginEngine()" x-cloak>

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
        <div class="auth-hero" aria-hidden="true">
            <div class="auth-hero__inner">

                {{-- Headline --}}
                <div class="auth-hero__content">
                    <h1 class="auth-hero__title">
                        Welcome back to
                        <span class="auth-hero__brand">FinanceAI</span>
                    </h1>

                    <p class="auth-hero__subtitle">
                        Your AI-powered financial intelligence system.
                        Track income, monitor expenses, and unlock predictive insights.
                    </p>
                </div>

                {{-- Finance Illustration --}}
                <div class="auth-hero__image-wrap">
                    <img
                        src="{{ asset('img/auth/login-hero.png') }}"
                        alt="FinanceAI Dashboard Analytics"
                        class="auth-hero__image"
                        loading="eager"
                        width="520"
                        height="340">

                    <div class="auth-hero__badge">
                        <i class="fa-solid fa-microchip"></i>
                        AI Analytics
                    </div>
                </div>

                {{-- Feature Grid --}}
                <div class="auth-hero__features">
                    <div class="auth-hero__feature">
                        <span class="auth-hero__feature-icon">📊</span>
                        Smart Financial Reports
                    </div>
                    <div class="auth-hero__feature">
                        <span class="auth-hero__feature-icon">⚡</span>
                        AI Spending Insights
                    </div>
                    <div class="auth-hero__feature">
                        <span class="auth-hero__feature-icon">🔐</span>
                        Bank-grade Encryption
                    </div>
                    <div class="auth-hero__feature">
                        <span class="auth-hero__feature-icon">📈</span>
                        Real-time Analytics
                    </div>
                </div>

                {{-- Trust Indicators --}}
                <div class="auth-hero__trust">
                    <div class="auth-hero__trust-item">
                        <i class="fa-solid fa-shield-halved"></i>
                        SOC2 Certified
                    </div>
                    <div class="auth-hero__trust-item">
                        <i class="fa-solid fa-lock"></i>
                        256-bit TLS
                    </div>
                    <div class="auth-hero__trust-item">
                        <i class="fa-solid fa-award"></i>
                        GDPR Compliant
                    </div>
                </div>
            </div>
        </div>


        {{-- ═══════════════════════════════════════════════════
             RIGHT PANEL — LOGIN FORM CARD
        ═══════════════════════════════════════════════════ --}}
        <div class="auth-form-wrap">
            <div class="auth-card" :class="{ 'auth-card--shake': shakeCard }">

                {{-- Mobile Brand (Visible only on small screens) --}}
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
                    <h2 id="login-heading" class="auth-card__title">Sign In</h2>
                    <p class="auth-card__subtitle">Access your financial dashboard</p>
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

                {{-- AJAX Toast Notification --}}
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

                {{-- Login Form --}}
                <form id="login-form"
                      method="POST"
                      action="{{ route('login.attempt') }}"
                      class="auth-form"
                      @submit.prevent="handleLogin"
                      novalidate
                      aria-labelledby="login-heading">

                    @csrf

                    {{-- EMAIL FIELD --}}
                    <div class="auth-field" :class="{ 'auth-field--error': errors.email, 'auth-field--focus': focused === 'email' }">
                        <label for="login-email" class="auth-field__label">Email Address</label>
                        <div class="auth-field__input-wrap">
                            <i class="fa-solid fa-envelope auth-field__icon"></i>
                            <input
                                type="email"
                                id="login-email"
                                name="email"
                                x-model="form.email"
                                @focus="focused = 'email'"
                                @blur="focused = null; validateEmail()"
                                required
                                autocomplete="email"
                                placeholder="you@company.com"
                                class="auth-field__input"
                                :aria-invalid="errors.email ? 'true' : 'false'"
                                aria-describedby="email-error">
                        </div>
                        <p id="email-error" class="auth-field__error" x-show="errors.email" x-text="errors.email" x-cloak></p>
                    </div>

                    {{-- PASSWORD FIELD --}}
                    <div class="auth-field" :class="{ 'auth-field--error': errors.password, 'auth-field--focus': focused === 'password' }">
                        <label for="login-password" class="auth-field__label">Password</label>
                        <div class="auth-field__input-wrap">
                            <i class="fa-solid fa-lock auth-field__icon"></i>
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                id="login-password"
                                name="password"
                                x-model="form.password"
                                @focus="focused = 'password'"
                                @blur="focused = null; validatePassword()"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="auth-field__input auth-field__input--password"
                                :aria-invalid="errors.password ? 'true' : 'false'"
                                aria-describedby="password-error">
                            <button type="button"
                                    @click="showPassword = !showPassword"
                                    class="auth-field__toggle"
                                    :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                    tabindex="-1">
                                <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        <p id="password-error" class="auth-field__error" x-show="errors.password" x-text="errors.password" x-cloak></p>
                    </div>

                    {{-- OPTIONS ROW --}}
                    <div class="auth-options">
                        <label class="auth-toggle" for="remember-toggle">
                            <input type="checkbox"
                                   name="remember"
                                   id="remember-toggle"
                                   x-model="form.remember"
                                   class="auth-toggle__input">
                            <div class="auth-toggle__track">
                                <div class="auth-toggle__thumb"></div>
                            </div>
                            <span class="auth-toggle__label">Remember me</span>
                        </label>

                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="auth-link">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    {{-- SUBMIT BUTTON --}}
                    <button id="login-submit-btn"
                            type="submit"
                            class="auth-btn auth-btn--primary"
                            :disabled="isLoading"
                            :class="{ 'auth-btn--loading': isLoading }">
                        <span x-show="!isLoading" class="auth-btn__text">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            Sign In
                        </span>
                        <span x-show="isLoading" class="auth-btn__loader" x-cloak>
                            <svg class="auth-spinner" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="auth-spinner__track"/>
                                <path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="3" stroke-linecap="round" class="auth-spinner__head"/>
                            </svg>
                            Authenticating...
                        </span>
                    </button>

                    {{-- DIVIDER --}}
                    <div class="auth-divider">
                        <span>OR</span>
                    </div>

                    {{-- SOCIAL LOGIN (UI Only) --}}
                    <button type="button" class="auth-btn auth-btn--social" @click="showSocialToast()">
                        <img src="https://www.svgrepo.com/show/355037/google.svg" alt="" class="auth-btn__social-icon" width="20" height="20">
                        Continue with Google
                    </button>

                    {{-- REGISTER LINK --}}
                    <p class="auth-footer-link">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="auth-link auth-link--bold">
                            Create one
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
     SCOPED STYLES — Auth Module
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

/* ─── BACKGROUND BLOBS ─── */
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

.auth-hero__inner {
    max-width: 520px;
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.auth-hero__title {
    font-size: clamp(2rem, 3.5vw, 3rem);
    font-weight: 800;
    color: #0f172a;
    line-height: 1.15;
    letter-spacing: -0.02em;
}

.auth-hero__brand {
    display: block;
    background: linear-gradient(135deg, #4f46e5, #9333ea);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.auth-hero__subtitle {
    font-size: 1.05rem;
    color: #64748b;
    line-height: 1.7;
    max-width: 440px;
}

/* Hero Image */
.auth-hero__image-wrap {
    position: relative;
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 25px 60px -15px rgba(79, 70, 229, 0.2);
    transition: transform 0.4s ease, box-shadow 0.4s ease;
}

.auth-hero__image-wrap:hover {
    transform: translateY(-4px);
    box-shadow: 0 35px 80px -15px rgba(79, 70, 229, 0.3);
}

.auth-hero__image {
    width: 100%;
    height: auto;
    display: block;
    object-fit: cover;
    max-height: 280px;
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
    letter-spacing: 0.02em;
}

/* Feature Grid */
.auth-hero__features {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.auth-hero__feature {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: #475569;
    font-weight: 500;
    padding: 0.5rem 0.75rem;
    border-radius: 0.75rem;
    background: rgba(255,255,255,0.6);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(226, 232, 240, 0.6);
    transition: all 0.25s ease;
}

.auth-hero__feature:hover {
    background: rgba(255,255,255,0.9);
    border-color: rgba(99, 102, 241, 0.2);
    transform: translateX(4px);
}

.auth-hero__feature-icon {
    font-size: 1rem;
}

/* Trust Indicators */
.auth-hero__trust {
    display: flex;
    gap: 1.25rem;
    flex-wrap: wrap;
}

.auth-hero__trust-item {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.72rem;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.auth-hero__trust-item i {
    color: #6366f1;
    font-size: 0.7rem;
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
    .auth-form-wrap { padding: 3rem; }
}

/* ─── AUTH CARD ─── */
.auth-card {
    width: 100%;
    max-width: 440px;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    border-radius: 1.75rem;
    box-shadow: 0 50px 120px rgba(15, 23, 42, 0.12);
    padding: 2.5rem;
    animation: auth-fadeIn 0.6s ease forwards;
    transition: box-shadow 0.3s ease;
}

.auth-card:hover {
    box-shadow: 0 60px 150px rgba(15, 23, 42, 0.16);
}

@media (min-width: 640px) {
    .auth-card { padding: 3rem; }
}

.auth-card--shake {
    animation: auth-shake 0.5s ease;
}

/* Mobile Brand */
.auth-card__mobile-brand {
    display: flex;
    justify-content: center;
    margin-bottom: 1.5rem;
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
    margin-bottom: 1.75rem;
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

.auth-alert i {
    margin-top: 1px;
    flex-shrink: 0;
}

.auth-alert--info {
    background: #eef2ff;
    border: 1px solid #c7d2fe;
    color: #4338ca;
}

.auth-alert--error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
}

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

.auth-toast--success {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #16a34a;
}

.auth-toast--error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
}

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
    gap: 1.25rem;
}

/* ─── FIELD ─── */
.auth-field__label {
    display: block;
    font-size: 0.78rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 0.4rem;
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
    padding: 13px 16px 13px 42px;
    border-radius: 0.85rem;
    border: 1.5px solid #e2e8f0;
    background: rgba(255,255,255,0.8);
    font-size: 0.92rem;
    font-weight: 500;
    color: #0f172a;
    transition: all 0.25s ease;
    outline: none;
    font-family: inherit;
}

.auth-field__input::placeholder {
    color: #cbd5e1;
    font-weight: 400;
}

.auth-field__input:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
    background: white;
}

.auth-field--focus .auth-field__icon {
    color: #6366f1;
}

.auth-field--error .auth-field__input {
    border-color: #f87171;
    box-shadow: 0 0 0 3px rgba(248, 113, 113, 0.1);
}

.auth-field--error .auth-field__icon {
    color: #f87171;
}

.auth-field__input--password {
    padding-right: 48px;
}

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

.auth-field__toggle:hover {
    color: #6366f1;
}

.auth-field__error {
    font-size: 0.75rem;
    color: #ef4444;
    font-weight: 600;
    margin-top: 0.35rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

/* ─── OPTIONS ROW ─── */
.auth-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Toggle Switch */
.auth-toggle {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    cursor: pointer;
    user-select: none;
}

.auth-toggle__input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.auth-toggle__track {
    width: 36px;
    height: 20px;
    background: #cbd5e1;
    border-radius: 999px;
    position: relative;
    transition: background 0.25s ease;
    flex-shrink: 0;
}

.auth-toggle__input:checked + .auth-toggle__track {
    background: #6366f1;
}

.auth-toggle__thumb {
    position: absolute;
    top: 3px;
    left: 3px;
    width: 14px;
    height: 14px;
    background: white;
    border-radius: 50%;
    transition: transform 0.25s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.15);
}

.auth-toggle__input:checked + .auth-toggle__track .auth-toggle__thumb {
    transform: translateX(16px);
}

.auth-toggle__label {
    font-size: 0.82rem;
    color: #64748b;
    font-weight: 600;
}

.auth-toggle__input:focus-visible + .auth-toggle__track {
    outline: 2px solid #6366f1;
    outline-offset: 2px;
}

/* Links */
.auth-link {
    font-size: 0.82rem;
    color: #6366f1;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.2s ease;
}

.auth-link:hover {
    color: #4338ca;
    text-decoration: underline;
}

.auth-link--bold {
    font-weight: 700;
}

/* ─── BUTTONS ─── */
.auth-btn {
    width: 100%;
    padding: 14px 20px;
    border-radius: 0.85rem;
    font-size: 0.92rem;
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

.auth-btn--primary:active:not(:disabled) {
    transform: translateY(0);
}

.auth-btn--loading {
    opacity: 0.85;
    cursor: not-allowed;
}

.auth-btn__text {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.auth-btn__loader {
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
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

.auth-btn__social-icon {
    width: 20px;
    height: 20px;
}

/* Spinner */
.auth-spinner {
    width: 20px;
    height: 20px;
    animation: auth-spin 0.8s linear infinite;
}

.auth-spinner__track {
    opacity: 0.25;
}

.auth-spinner__head {
    opacity: 0.9;
}

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

/* ─── FOOTER LINK ─── */
.auth-footer-link {
    text-align: center;
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 500;
}

/* ─── SSL BADGE ─── */
.auth-card__ssl {
    margin-top: 1.75rem;
    text-align: center;
    font-size: 0.7rem;
    color: #94a3b8;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    letter-spacing: 0.02em;
}

.auth-card__ssl i {
    color: #6366f1;
}

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

/* ─── RESPONSIVE TWEAKS ─── */
@media (max-width: 639px) {
    .auth-card {
        padding: 1.75rem;
        border-radius: 1.25rem;
    }

    .auth-card__title {
        font-size: 1.5rem;
    }

    .auth-options {
        flex-direction: column;
        gap: 0.75rem;
        align-items: flex-start;
    }
}

@media (max-width: 374px) {
    .auth-form-wrap {
        padding: 1rem 0.75rem;
    }

    .auth-card {
        padding: 1.5rem;
    }
}
</style>
@endpush


{{-- ═══════════════════════════════════════════════════════════
     ALPINE.JS — Login Engine
═══════════════════════════════════════════════════════════ --}}
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('loginEngine', () => ({
        form: {
            email: '{{ old("email") }}',
            password: '',
            remember: false,
        },
        errors: {
            email: null,
            password: null,
        },
        focused: null,
        showPassword: false,
        isLoading: false,
        shakeCard: false,
        toast: {
            show: false,
            message: '',
            type: 'error',
        },
        attemptCount: 0,

        // ── Frontend Validation ──
        validateEmail() {
            if (!this.form.email.trim()) {
                this.errors.email = 'Email address is required.';
                return false;
            }
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!regex.test(this.form.email)) {
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
            this.errors.password = null;
            return true;
        },

        validate() {
            const emailValid = this.validateEmail();
            const passValid = this.validatePassword();
            return emailValid && passValid;
        },

        // ── AJAX Login Handler ──
        async handleLogin() {
            if (!this.validate()) {
                this.triggerShake();
                return;
            }

            this.isLoading = true;
            this.toast.show = false;

            try {
                const response = await fetch('{{ route("login.attempt") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        email: this.form.email.trim().toLowerCase(),
                        password: this.form.password,
                        remember: this.form.remember,
                    }),
                });

                // Rate limited
                if (response.status === 429) {
                    this.showToast('Too many attempts. Please try again in a minute.', 'error');
                    this.triggerShake();
                    this.isLoading = false;
                    return;
                }

                // Validation errors
                if (response.status === 422) {
                    const data = await response.json();
                    const firstError = data.errors
                        ? Object.values(data.errors)[0][0]
                        : (data.message || 'Invalid credentials.');
                    this.showToast(firstError, 'error');
                    this.triggerShake();
                    this.attemptCount++;
                    this.isLoading = false;
                    return;
                }

                // Successful redirect (non-AJAX fallback)
                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }

                // JSON success response from AuthController
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.redirect) {
                        this.showToast(data.message || 'Login successful! Redirecting...', 'success');
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 600);
                        return;
                    }
                    // Fallback
                    window.location.href = '{{ url("/user/dashboard") }}';
                    return;
                }

                // Unexpected error
                this.showToast('Invalid email or password.', 'error');
                this.triggerShake();

            } catch (error) {
                // Network error — fall back to standard form submission
                console.warn('AJAX login failed, falling back to form submit:', error);
                document.getElementById('login-form').removeEventListener('submit', () => {});
                const form = document.getElementById('login-form');
                form.setAttribute('onsubmit', '');
                form.submit();
            } finally {
                this.isLoading = false;
            }
        },

        triggerShake() {
            this.shakeCard = true;
            setTimeout(() => { this.shakeCard = false; }, 500);
        },

        showToast(message, type = 'error') {
            this.toast = { show: true, message, type };
            setTimeout(() => { this.toast.show = false; }, 5000);
        },

        showSocialToast() {
            this.showToast('Google login is coming soon!', 'error');
        },
    }));
});
</script>
@endpush

@endsection