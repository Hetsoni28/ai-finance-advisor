@extends('layouts.landing')

@section('title', 'Contact FinanceAI | Enterprise Support')
@section('meta_description', 'Get in touch with FinanceAI for enterprise support, partnership inquiries, or technical assistance. We typically respond within 24 hours.')

@section('content')
@include('partials.site-type')
<div class="paper">

    <x-site.hero eyebrow="Support Online" lead="Contact" accent="FinanceAI"
        lede="Enterprise support. Partnership inquiries. Technical assistance. We typically respond within 24 hours." />

    <section class="py-16 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-10 lg:gap-16 items-start">

            {{-- Form --}}
            <div class="lg:col-span-7 lg:order-2" data-reveal>
                <p class="eyebrow text-indigo-600">Write to us</p>
                <h2 class="mt-4 text-3xl sm:text-4xl font-black tracking-[-0.03em] text-slate-900">Send a <span class="font-serif-i text-indigo-600">Transmission</span></h2>
                <p class="mt-3 text-slate-500">Every field marked <span class="text-rose-500">*</span> is required</p>

                @if(session('success'))
                    <div class="mt-6 border-l-2 border-emerald-500 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="mt-6 border-l-2 border-rose-500 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">{{ session('error') }}</div>
                @endif

                <form id="contactForm" method="POST" action="{{ route('contact.store') }}" class="mt-8 space-y-6" novalidate>
                    @csrf
                    <input type="hidden" name="latitude" id="geoLatitude">
                    <input type="hidden" name="longitude" id="geoLongitude">

                    <div class="grid sm:grid-cols-2 gap-6">
                        <div class="contact-field-group">
                            <label for="contactName" class="contact-label">Name <span class="text-rose-500">*</span></label>
                            <input type="text" id="contactName" name="name" value="{{ old('name') }}" placeholder="Enter your full name" class="contact-input" required autocomplete="name">
                            <p class="contact-error {{ $errors->has('name') ? '' : 'hidden' }}" id="nameError">@error('name'){{ $message }}@enderror</p>
                        </div>
                        <div class="contact-field-group">
                            <label for="contactEmail" class="contact-label">Email <span class="text-rose-500">*</span></label>
                            <input type="email" id="contactEmail" name="email" value="{{ old('email') }}" placeholder="you@company.com" class="contact-input" required autocomplete="email">
                            <p class="contact-error {{ $errors->has('email') ? '' : 'hidden' }}" id="emailError">@error('email'){{ $message }}@enderror</p>
                        </div>
                    </div>

                    <div class="contact-field-group">
                        <label for="contactSubject" class="contact-label">Subject</label>
                        <input type="text" id="contactSubject" name="subject" value="{{ old('subject') }}" placeholder="What is this regarding?" class="contact-input">
                    </div>

                    <div class="contact-field-group">
                        <div class="flex items-center justify-between">
                            <label for="contactMessage" class="contact-label">Message <span class="text-rose-500">*</span></label>
                            <span id="charCounter" class="text-[10px] font-bold text-slate-400 font-mono">0 / 5000</span>
                        </div>
                        <textarea id="contactMessage" name="message" rows="6" placeholder="Describe your inquiry or issue in detail..." class="contact-input resize-none" maxlength="5000" required>{{ old('message') }}</textarea>
                        <p class="contact-error {{ $errors->has('message') ? '' : 'hidden' }}" id="messageError">@error('message'){{ $message }}@enderror</p>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center gap-5 pt-2">
                        <button type="submit" id="contactSubmitBtn" class="group shrink-0 inline-flex items-center justify-center gap-2 h-12 px-7 rounded-xl text-white font-bold bg-gradient-to-br from-indigo-500 to-violet-600 shadow-[0_10px_30px_-8px_rgba(99,102,241,.6)] hover:-translate-y-0.5 transition-transform focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-200 disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:translate-y-0">
                            <span id="submitBtnText">Send Message</span>
                            <i id="submitBtnIcon" class="fa-solid fa-arrow-right text-sm transition-transform group-hover:translate-x-0.5" aria-hidden="true"></i>
                            <div id="submitBtnSpinner" class="hidden w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                        </button>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Your data is encrypted and handled per our <a href="{{ route('privacy') }}" class="text-indigo-600 font-semibold underline underline-offset-2">Privacy Policy</a>. We never share your information.
                        </p>
                    </div>
                </form>
            </div>

            {{-- Details + map --}}
            <aside class="lg:col-span-5 lg:order-1 space-y-8" data-reveal style="--d: 120ms">
                <dl class="border-t border-slate-900">
                    @foreach([
                        ['icon' => 'fa-envelope', 'title' => 'Email Support', 'text' => 'support@financeai.com'],
                        ['icon' => 'fa-globe', 'title' => 'Global Coverage', 'text' => 'Remote Team Worldwide'],
                        ['icon' => 'fa-bolt', 'title' => 'Enterprise SLA', 'text' => 'Fast Priority Response'],
                    ] as $info)
                        <div class="flex items-center justify-between gap-4 py-4 border-b border-slate-200">
                            <dt class="eyebrow text-slate-400 flex items-center gap-2"><i class="fa-solid {{ $info['icon'] }}" aria-hidden="true"></i>{{ $info['title'] }}</dt>
                            <dd class="font-semibold text-slate-900 text-right">{{ $info['text'] }}</dd>
                        </div>
                    @endforeach
                </dl>

                <figure class="card-site overflow-hidden">
                    <figcaption class="flex items-center justify-between px-4 py-3 border-b border-slate-200">
                        <span class="eyebrow text-slate-500">Your Location</span>
                        <span id="locationStatusBadge" class="font-mono text-[10px] uppercase tracking-wider text-slate-400">Detecting...</span>
                    </figcaption>
                    <div id="contactMap" class="w-full h-[240px] sm:h-[280px]"></div>
                </figure>

                <p class="border-l-2 border-indigo-600 pl-4 text-sm text-slate-600">
                    <span class="font-semibold text-slate-900">Average Response: 4 hours</span><br>
                    Enterprise plans receive priority routing
                </p>
            </aside>
        </div>
    </section>
</div>

{{-- Leaflet CSS --}}
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    /* ================= CONTACT FORM FIELD SYSTEM ================= */
    .contact-field-group {
        position: relative;
    }
    .contact-label {
        display: block;
        font-family: 'JetBrains Mono', ui-monospace, monospace;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .14em;
        color: #64748b;
        margin-bottom: 8px;
    }
    .contact-input {
        width: 100%;
        padding: 13px 14px;
        border: 1px solid #e2e0da;
        border-radius: 12px;
        font-size: 0.95rem;
        color: #0f172a;
        background: #fff;
        transition: border-color .2s ease, box-shadow .2s ease;
        outline: none;
    }
    .contact-input::placeholder {
        color: #94a3b8;
        font-weight: 400;
    }
    .contact-input:hover { border-color: #cbd5e1; }
    .contact-input:focus { border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); }
    .contact-input.input-error {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.08);
    }
    .contact-input.input-success {
        border-color: #10b981;
    }
    .contact-error {
        font-size: 0.75rem;
        font-weight: 600;
        color: #ef4444;
        margin-top: 4px;
    }

    /* ================= LEAFLET OVERRIDES ================= */
    .leaflet-container {
        font-family: 'Inter', sans-serif !important;
        z-index: 1;
    }
    .leaflet-control-attribution {
        font-size: 9px !important;
        background: rgba(255,255,255,0.8) !important;
        border-radius: 6px !important;
    }

    /* ================= MAP MARKER PULSE ================= */
    .pulse-marker {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #6366f1;
        border: 3px solid #fff;
        box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.4), 0 2px 8px rgba(99, 102, 241, 0.3);
        animation: marker-pulse 2s infinite;
    }
    @keyframes marker-pulse {
        0% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.5), 0 2px 8px rgba(99, 102, 241, 0.3); }
        70% { box-shadow: 0 0 0 20px rgba(99, 102, 241, 0), 0 2px 8px rgba(99, 102, 241, 0.3); }
        100% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0), 0 2px 8px rgba(99, 102, 241, 0.3); }
    }

    /* ================= FORM SHAKE ANIMATION ================= */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        20% { transform: translateX(-6px); }
        40% { transform: translateX(6px); }
        60% { transform: translateX(-4px); }
        80% { transform: translateX(4px); }
    }
    .shake { animation: shake 0.5s ease-in-out; }
</style>
@endpush

{{-- Leaflet JS + Contact Form Logic --}}
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // ================= 1. LEAFLET MAP INITIALIZATION =================
    const map = L.map('contactMap', {
        zoomControl: false,
        attributionControl: true
    }).setView([22.5726, 78.9629], 4); // India center fallback

    L.control.zoom({ position: 'bottomright' }).addTo(map);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>',
        maxZoom: 18
    }).addTo(map);

    const badge = document.getElementById('locationStatusBadge');
    let userMarker = null;

    // Custom pulsing icon
    const pulseIcon = L.divIcon({
        className: '',
        html: '<div class="pulse-marker"></div>',
        iconSize: [18, 18],
        iconAnchor: [9, 9]
    });

    // ================= 2. GEOLOCATION API =================
    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                // Set hidden form fields
                document.getElementById('geoLatitude').value = lat;
                document.getElementById('geoLongitude').value = lng;

                // Update map
                map.setView([lat, lng], 13);
                userMarker = L.marker([lat, lng], { icon: pulseIcon }).addTo(map);
                userMarker.bindPopup('<strong class="text-sm">Your Location</strong>').openPopup();

                // Update badge
                badge.textContent = 'Located';
                badge.className = 'font-mono text-[10px] uppercase tracking-wider text-emerald-700';
            },
            function(error) {
                badge.textContent = 'Not Available';
                badge.className = 'font-mono text-[10px] uppercase tracking-wider text-slate-400';
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
        );
    } else {
        badge.textContent = 'Unsupported';
    }

    // ================= 3. CHARACTER COUNTER =================
    const messageField = document.getElementById('contactMessage');
    const charCounter = document.getElementById('charCounter');
    
    messageField.addEventListener('input', function() {
        const count = this.value.length;
        charCounter.textContent = count + ' / 5000';
        charCounter.className = count > 4500 
            ? 'text-[10px] font-bold font-mono text-rose-500' 
            : 'text-[10px] font-bold font-mono text-slate-400';
    });

    // ================= 4. AJAX FORM SUBMISSION =================
    const form = document.getElementById('contactForm');
    const submitBtn = document.getElementById('contactSubmitBtn');
    const btnText = document.getElementById('submitBtnText');
    const btnIcon = document.getElementById('submitBtnIcon');
    const btnSpinner = document.getElementById('submitBtnSpinner');

    function setLoading(loading) {
        submitBtn.disabled = loading;
        if (loading) {
            btnText.textContent = 'Transmitting...';
            btnIcon.classList.add('hidden');
            btnSpinner.classList.remove('hidden');
        } else {
            btnText.textContent = 'Send Message';
            btnIcon.classList.remove('hidden');
            btnSpinner.classList.add('hidden');
        }
    }

    function clearErrors() {
        document.querySelectorAll('.contact-error').forEach(el => { el.classList.add('hidden'); el.textContent = ''; });
        document.querySelectorAll('.contact-input').forEach(el => { el.classList.remove('input-error'); });
    }

    function showFieldError(fieldName, message) {
        const errEl = document.getElementById(fieldName + 'Error');
        const inputEl = document.getElementById('contact' + fieldName.charAt(0).toUpperCase() + fieldName.slice(1));
        if (errEl) {
            errEl.textContent = message;
            errEl.classList.remove('hidden');
        }
        if (inputEl) {
            inputEl.classList.add('input-error');
        }
    }

    function showToast(message, type) {
        // Use the landing layout's built-in toast system
        window.dispatchEvent(new CustomEvent('notify', {
            detail: { message: message, type: type }
        }));
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        clearErrors();

        // Client-side validation
        const name = document.getElementById('contactName').value.trim();
        const email = document.getElementById('contactEmail').value.trim();
        const message = document.getElementById('contactMessage').value.trim();
        let hasErrors = false;

        if (!name) { showFieldError('name', 'Please enter your name.'); hasErrors = true; }
        if (!email) { showFieldError('email', 'Please enter your email address.'); hasErrors = true; }
        else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showFieldError('email', 'Please enter a valid email address.'); hasErrors = true; }
        if (!message) { showFieldError('message', 'Please enter your message.'); hasErrors = true; }

        if (hasErrors) {
            form.classList.add('shake');
            setTimeout(() => form.classList.remove('shake'), 500);
            return;
        }

        setLoading(true);

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(({ status, body }) => {
            setLoading(false);

            if (status === 201 && body.success) {
                // Success!
                showToast(body.message || 'Message sent successfully!', 'success');
                form.reset();
                charCounter.textContent = '0 / 5000';
            } else if (status === 422 && body.errors) {
                // Validation errors
                Object.keys(body.errors).forEach(field => {
                    showFieldError(field, body.errors[field][0]);
                });
                form.classList.add('shake');
                setTimeout(() => form.classList.remove('shake'), 500);
            } else {
                showToast(body.message || 'Something went wrong. Please try again.', 'error');
            }
        })
        .catch(err => {
            setLoading(false);
            showToast('Network error. Please check your connection and try again.', 'error');
        });
    });

    // ================= 5. INPUT FOCUS ENHANCEMENT =================
    document.querySelectorAll('.contact-input').forEach(input => {
        input.addEventListener('focus', function() { this.classList.remove('input-error'); });
        input.addEventListener('blur', function() {
            // Add success state if field has content
            if (this.value.trim()) {
                this.classList.add('input-success');
            } else {
                this.classList.remove('input-success');
            }
        });
    });

    // Show server-side errors on page load
    @if($errors->any())
        @foreach($errors->all() as $error)
            showToast('{{ $error }}', 'error');
        @endforeach
    @endif
});
</script>
@endpush

@endsection
