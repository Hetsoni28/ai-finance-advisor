@extends('layouts.app')

@section('title', 'View Message — ' . $contact->name . ' | Admin Panel')

@section('content')

<div class="min-h-screen bg-[#f8fafc] pb-32 font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden">

    {{-- Ambient Background --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[900px] h-[900px] bg-violet-50/50 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 pt-10 relative z-10 space-y-8">

        {{-- ================= 1. HEADER ================= --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 sm:p-8 rounded-[2rem] border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-violet-500 to-indigo-400"></div>
            <div class="flex-1">
                <nav class="flex mb-3" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <li><a href="{{ route('admin.contacts.index') }}" class="hover:text-violet-600 transition-colors">Messages</a></li>
                        <li><i class="fa-solid fa-chevron-right text-[8px] mx-1 opacity-50"></i></li>
                        <li class="text-violet-600 truncate max-w-[200px]">{{ $contact->name }}</li>
                    </ol>
                </nav>
                <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">Message Detail</h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.contacts.index') }}" class="px-5 py-3 bg-slate-50 border border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-white hover:text-slate-900 hover:border-slate-300 transition-all flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-arrow-left text-xs"></i> Back to Inbox
                </a>
                <form method="POST" action="{{ route('admin.contacts.destroy', $contact) }}" onsubmit="return confirm('Are you sure you want to permanently delete this message?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-5 py-3 bg-rose-50 border border-rose-200 text-rose-600 rounded-xl font-bold text-sm hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all flex items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-trash text-xs"></i> Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">

            {{-- ================= 2. MESSAGE BODY ================= --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Message Card --}}
                <div class="bg-white p-6 sm:p-8 rounded-[2rem] border border-slate-200 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-violet-500 via-indigo-500 to-violet-500"></div>

                    {{-- Sender Info --}}
                    <div class="flex items-start gap-4 mb-8 pb-6 border-b border-slate-100">
                        <div class="w-14 h-14 rounded-[1rem] bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-violet-500/20 shrink-0">
                            {{ strtoupper(substr($contact->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h2 class="text-xl font-black text-slate-900 truncate">{{ $contact->name }}</h2>
                            <a href="mailto:{{ $contact->email }}" class="text-sm font-mono text-indigo-600 hover:text-indigo-800 transition-colors">{{ $contact->email }}</a>
                            <div class="flex items-center gap-3 mt-2 flex-wrap">
                                <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 font-mono">
                                    {{ $contact->created_at->format('M d, Y \a\t h:i A') }}
                                </span>
                                <span class="inline-flex items-center gap-1 text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-md border {{ $contact->is_read ? 'bg-slate-50 text-slate-400 border-slate-200' : 'bg-violet-50 text-violet-600 border-violet-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $contact->is_read ? 'bg-slate-300' : 'bg-violet-500' }}"></span>
                                    {{ $contact->is_read ? 'Read' : 'Unread' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Subject --}}
                    @if($contact->subject)
                    <div class="mb-6">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Subject</p>
                        <h3 class="text-lg font-bold text-slate-900">{{ $contact->subject }}</h3>
                    </div>
                    @endif

                    {{-- Message Body --}}
                    <div class="mb-6">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Message</p>
                        <div class="prose prose-slate max-w-none">
                            <p class="text-base text-slate-700 leading-relaxed whitespace-pre-wrap font-medium">{{ $contact->message }}</p>
                        </div>
                    </div>

                    {{-- Quick Reply Action --}}
                    <div class="pt-6 border-t border-slate-100">
                        <a href="mailto:{{ $contact->email }}?subject=Re: {{ urlencode($contact->subject ?? 'Your Inquiry') }}"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-violet-600 to-indigo-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-violet-500/20 hover:shadow-violet-500/40 hover:-translate-y-0.5 transition-all">
                            <i class="fa-solid fa-reply text-violet-200"></i> Reply via Email
                        </a>
                    </div>
                </div>
            </div>

            {{-- ================= 3. METADATA SIDEBAR ================= --}}
            <div class="space-y-6">

                {{-- Technical Metadata --}}
                <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-black text-slate-900 tracking-tight mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-fingerprint text-violet-500"></i> Transmission Metadata
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1">IP Address</p>
                            <p class="text-sm font-mono font-bold text-slate-700">{{ $contact->ip_address ?? 'Not captured' }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1">User Agent</p>
                            <p class="text-xs font-mono text-slate-500 break-all leading-relaxed">{{ Str::limit($contact->user_agent, 120) ?? 'Not captured' }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1">Received</p>
                            <p class="text-sm font-bold text-slate-700">{{ $contact->created_at->format('F j, Y') }}</p>
                            <p class="text-xs font-mono text-slate-500 mt-0.5">{{ $contact->created_at->format('h:i:s A T') }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1">Time Since</p>
                            <p class="text-sm font-bold text-slate-700">{{ $contact->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>

                {{-- Location Map --}}
                <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-map-location-dot text-violet-500"></i>
                            <span class="text-sm font-black text-slate-900 tracking-tight">Geolocation</span>
                        </div>
                        @if($contact->has_location)
                            <span class="text-[9px] font-black uppercase tracking-widest px-2 py-1 rounded-md bg-emerald-50 text-emerald-600 border border-emerald-200">
                                Captured
                            </span>
                        @else
                            <span class="text-[9px] font-black uppercase tracking-widest px-2 py-1 rounded-md bg-slate-100 text-slate-400 border border-slate-200">
                                Not Available
                            </span>
                        @endif
                    </div>

                    @if($contact->has_location)
                        <div id="adminContactMap" class="w-full h-[250px]"></div>
                        <div class="p-4 bg-slate-50/50 border-t border-slate-100">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-crosshairs text-slate-400 text-xs"></i>
                                <span class="text-xs font-mono font-bold text-slate-600">
                                    {{ number_format($contact->latitude, 6) }}, {{ number_format($contact->longitude, 6) }}
                                </span>
                            </div>
                            @if($contact->location_label)
                            <p class="text-[10px] font-bold text-slate-400 mt-1 ml-5">{{ $contact->location_label }}</p>
                            @endif
                        </div>
                    @else
                        <div class="py-12 text-center">
                            <div class="w-14 h-14 bg-slate-50 rounded-full flex items-center justify-center border border-slate-100 mb-4 mx-auto shadow-inner">
                                <i class="fa-solid fa-location-crosshairs text-xl text-slate-300"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-500">Location not shared</p>
                            <p class="text-xs text-slate-400 mt-1">The user didn't grant geolocation access</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@if($contact->has_location)
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .leaflet-container { font-family: 'Inter', sans-serif !important; z-index: 1; }
    .admin-marker {
        width: 20px; height: 20px; border-radius: 50%;
        background: #8b5cf6; border: 3px solid #fff;
        box-shadow: 0 0 0 0 rgba(139, 92, 246, 0.4), 0 2px 10px rgba(139, 92, 246, 0.3);
        animation: admin-pulse 2s infinite;
    }
    @keyframes admin-pulse {
        0% { box-shadow: 0 0 0 0 rgba(139,92,246,0.5), 0 2px 10px rgba(139,92,246,0.3); }
        70% { box-shadow: 0 0 0 18px rgba(139,92,246,0), 0 2px 10px rgba(139,92,246,0.3); }
        100% { box-shadow: 0 0 0 0 rgba(139,92,246,0), 0 2px 10px rgba(139,92,246,0.3); }
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const lat = {{ $contact->latitude }};
    const lng = {{ $contact->longitude }};

    const map = L.map('adminContactMap', {
        zoomControl: true,
        attributionControl: true
    }).setView([lat, lng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>',
        maxZoom: 18
    }).addTo(map);

    const icon = L.divIcon({
        className: '',
        html: '<div class="admin-marker"></div>',
        iconSize: [20, 20],
        iconAnchor: [10, 10]
    });

    L.marker([lat, lng], { icon: icon }).addTo(map)
        .bindPopup('<strong>{{ $contact->name }}</strong><br><span style="font-size:11px;color:#64748b">{{ $contact->location_label ?? "User Location" }}</span>');

    // Fix map rendering in hidden containers
    setTimeout(() => map.invalidateSize(), 200);
});
</script>
@endpush
@endif
