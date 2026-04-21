@extends('layouts.landing')

@section('title', 'Contact FinanceAI | Enterprise Support')
@section('meta_description', 'Get in touch with FinanceAI for enterprise support, partnership inquiries, or technical assistance. We typically respond within 24 hours.')

@section('content')

<section class="relative py-20 sm:py-28 lg:py-32 bg-gradient-to-br from-indigo-50 via-white to-purple-50 overflow-hidden">

    {{-- Decorative Background Orbs --}}
    <div class="absolute top-[-15%] right-[-10%] w-[600px] h-[600px] bg-indigo-200/30 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] left-[-10%] w-[500px] h-[500px] bg-purple-200/20 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Page Header --}}
        <div class="text-center mb-12 sm:mb-16 lg:mb-20">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-full shadow-sm mb-6">
                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Support Online</span>
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight mb-4">
                Contact <span class="text-indigo-600">FinanceAI</span>
            </h1>
            <p class="text-base sm:text-lg text-slate-500 font-medium max-w-xl mx-auto leading-relaxed">
                Enterprise support. Partnership inquiries. Technical assistance.<br class="hidden sm:block">
                We typically respond within <strong class="text-slate-700">24 hours</strong>.
            </p>
        </div>

        {{-- Main Grid --}}
        <div class="grid lg:grid-cols-5 gap-8 lg:gap-12 items-start">

            {{-- LEFT COLUMN: Info + Map --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- Contact Info Cards --}}
                <div class="space-y-4">
                    @foreach([
                        ['icon' => 'fa-envelope', 'color' => 'indigo', 'title' => 'Email Support', 'text' => 'support@financeai.com'],
                        ['icon' => 'fa-globe', 'color' => 'sky', 'title' => 'Global Coverage', 'text' => 'Remote Team Worldwide'],
                        ['icon' => 'fa-bolt', 'color' => 'amber', 'title' => 'Enterprise SLA', 'text' => 'Fast Priority Response'],
                    ] as $info)
                    <div class="group flex items-center gap-4 p-4 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                        <div class="w-12 h-12 rounded-xl bg-{{ $info['color'] }}-50 text-{{ $info['color'] }}-600 flex items-center justify-center border border-{{ $info['color'] }}-100 shrink-0 group-hover:scale-110 transition-transform duration-300">
                            <i class="fa-solid {{ $info['icon'] }} text-lg"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-0.5">{{ $info['title'] }}</p>
                            <p class="text-sm font-bold text-slate-700">{{ $info['text'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Leaflet Map --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-indigo-500"></i>
                            <span class="text-xs font-black text-slate-700 uppercase tracking-widest">Your Location</span>
                        </div>
                        <span id="locationStatusBadge" class="text-[9px] font-black uppercase tracking-widest px-2 py-1 rounded-md bg-slate-100 text-slate-400 border border-slate-200">
                            Detecting...
                        </span>
                    </div>
                    <div id="contactMap" class="w-full h-[220px] sm:h-[260px]"></div>
                </div>

                {{-- Response Time Badge --}}
                <div class="hidden lg:flex items-center gap-3 p-4 bg-indigo-50/50 border border-indigo-100 rounded-2xl">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-clock text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-indigo-700">Average Response: <strong>4 hours</strong></p>
                        <p class="text-[10px] text-indigo-500 font-medium mt-0.5">Enterprise plans receive priority routing</p>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Contact Form --}}
            <div class="lg:col-span-3">
                <div class="bg-white p-6 sm:p-8 lg:p-10 rounded-[2rem] shadow-[0_10px_40px_-10px_rgba(0,0,0,0.06)] border border-slate-100 relative overflow-hidden">

                    {{-- Decorative Strip --}}
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-indigo-500"></div>

                    {{-- Form Header --}}
                    <div class="mb-8">
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Send a Transmission</h2>
                        <p class="text-sm text-slate-500 font-medium mt-1">Every field marked <span class="text-rose-500">*</span> is required</p>
                    </div>

                    {{-- Success/Error Static Banners (fallback for non-JS) --}}
                    @if(session('success'))
                        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-bold flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-emerald-500"></i>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-bold flex items-center gap-3">
                            <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Contact Form --}}
                    <form id="contactForm" method="POST" action="{{ route('contact.store') }}" class="space-y-5" novalidate>
                        @csrf

                        {{-- Hidden Geo Fields --}}
                        <input type="hidden" name="latitude" id="geoLatitude">
                        <input type="hidden" name="longitude" id="geoLongitude">

                        {{-- Name --}}
                        <div class="contact-field-group">
                            <label for="contactName" class="contact-label">Name <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-sm pointer-events-none"></i>
                                <input type="text" id="contactName" name="name"
                                       value="{{ old('name') }}"
                                       placeholder="Enter your full name"
                                       class="contact-input pl-11"
                                       required>
                            </div>
                            <p class="contact-error hidden" id="nameError">@error('name') {{ $message }} @enderror</p>
                        </div>

                        {{-- Email --}}
                        <div class="contact-field-group">
                            <label for="contactEmail" class="contact-label">Email <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-sm pointer-events-none"></i>
                                <input type="email" id="contactEmail" name="email"
                                       value="{{ old('email') }}"
                                       placeholder="you@company.com"
                                       class="contact-input pl-11"
                                       required>
                            </div>
                            <p class="contact-error hidden" id="emailError">@error('email') {{ $message }} @enderror</p>
                        </div>

                        {{-- Subject --}}
                        <div class="contact-field-group">
                            <label for="contactSubject" class="contact-label">Subject</label>
                            <div class="relative">
                                <i class="fa-solid fa-tag absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-sm pointer-events-none"></i>
                                <input type="text" id="contactSubject" name="subject"
                                       value="{{ old('subject') }}"
                                       placeholder="What is this regarding?"
                                       class="contact-input pl-11">
                            </div>
                        </div>

                        {{-- Message --}}
                        <div class="contact-field-group">
                            <div class="flex items-center justify-between">
                                <label for="contactMessage" class="contact-label">Message <span class="text-rose-500">*</span></label>
                                <span id="charCounter" class="text-[10px] font-bold text-slate-400 font-mono">0 / 5000</span>
                            </div>
                            <textarea id="contactMessage" name="message" rows="5"
                                      placeholder="Describe your inquiry or issue in detail..."
                                      class="contact-input resize-none"
                                      maxlength="5000"
                                      required>{{ old('message') }}</textarea>
                            <p class="contact-error hidden" id="messageError">@error('message') {{ $message }} @enderror</p>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit" id="contactSubmitBtn" class="w-full flex items-center justify-center gap-3 py-4 px-8 rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-black text-sm uppercase tracking-widest shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:-translate-y-1 active:translate-y-0 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-lg">
                            <span id="submitBtnText">Send Message</span>
                            <i id="submitBtnIcon" class="fa-solid fa-paper-plane text-indigo-200 text-xs"></i>
                            <div id="submitBtnSpinner" class="hidden w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                        </button>

                        {{-- Privacy Note --}}
                        <p class="text-[11px] text-slate-400 text-center font-medium leading-relaxed mt-4">
                            <i class="fa-solid fa-shield-halved text-slate-300 mr-1"></i>
                            Your data is encrypted and handled per our 
                            <a href="{{ route('privacy') ?? '#' }}" class="text-indigo-500 hover:text-indigo-700 underline underline-offset-2">Privacy Policy</a>.
                            We never share your information.
                        </p>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

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
        font-size: 0.8rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
        letter-spacing: 0.025em;
    }
    .contact-input {
        width: 100%;
        padding: 14px 16px;
        border: 1.5px solid rgba(15, 23, 42, 0.1);
        border-radius: 14px;
        font-size: 0.9rem;
        font-weight: 500;
        color: #1e293b;
        background: #fafbfc;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        outline: none;
    }
    .contact-input::placeholder {
        color: #94a3b8;
        font-weight: 400;
    }
    .contact-input:hover {
        border-color: rgba(99, 102, 241, 0.3);
        background: #fff;
    }
    .contact-input:focus {
        border-color: #6366f1;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.08), 0 2px 8px rgba(99, 102, 241, 0.06);
    }
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
                badge.className = 'text-[9px] font-black uppercase tracking-widest px-2 py-1 rounded-md bg-emerald-50 text-emerald-600 border border-emerald-200';
            },
            function(error) {
                badge.textContent = 'Not Available';
                badge.className = 'text-[9px] font-black uppercase tracking-widest px-2 py-1 rounded-md bg-slate-100 text-slate-400 border border-slate-200';
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
