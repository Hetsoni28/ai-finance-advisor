@extends('layouts.app')

@section('title', 'Contact Messages | Admin Panel')

@section('content')

<div x-data="contactAdmin()" class="min-h-screen bg-[#f8fafc] pb-32 font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden">

    {{-- Ambient Background --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[900px] h-[900px] bg-violet-50/50 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[800px] h-[800px] bg-indigo-50/40 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 pt-10 relative z-10 space-y-8">

        {{-- ================= 1. COMMAND HEADER ================= --}}
        <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-6 bg-white p-6 sm:p-8 rounded-[2rem] border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-violet-500 to-indigo-400"></div>
            <div class="flex-1">
                <nav class="flex mb-3" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <li>FinanceAI Engine</li>
                        <li><i class="fa-solid fa-chevron-right text-[8px] mx-1 opacity-50"></i></li>
                        <li>Master Node</li>
                        <li><i class="fa-solid fa-chevron-right text-[8px] mx-1 opacity-50"></i></li>
                        <li class="text-violet-600">Contact Messages</li>
                    </ol>
                </nav>
                <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">Message Inbox</h1>
                <p class="text-slate-500 text-sm font-medium mt-1">Monitor and manage all contact form submissions</p>
            </div>

            {{-- Search & Filter --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full xl:w-auto">
                <form method="GET" action="{{ route('admin.contacts.index') }}" class="flex items-center gap-3 flex-1 xl:flex-none">
                    <input type="hidden" name="filter" value="{{ $filter }}">
                    <div class="relative flex-1 xl:w-72">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search messages..."
                               class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100 transition-all">
                    </div>
                    <button type="submit" class="px-5 py-3 bg-slate-900 text-white rounded-xl font-bold text-sm shadow-lg hover:bg-violet-600 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-search text-xs"></i> Search
                    </button>
                </form>
            </div>
        </div>

        {{-- ================= 2. KPI CARDS ================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            
            {{-- Total Messages --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-violet-500/10 rounded-full blur-xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-4 relative z-10">
                    <div class="w-12 h-12 bg-violet-50 text-violet-600 rounded-2xl flex items-center justify-center border border-violet-100 shadow-sm">
                        <i class="fa-solid fa-envelope text-lg"></i>
                    </div>
                </div>
                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1 relative z-10">Total Messages</p>
                <h2 class="text-3xl font-black text-violet-600 relative z-10">{{ number_format($totalCount) }}</h2>
            </div>

            {{-- Unread --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-rose-500/10 rounded-full blur-xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-4 relative z-10">
                    <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center border border-rose-100 shadow-sm">
                        <i class="fa-solid fa-envelope-circle-check text-lg"></i>
                    </div>
                    @if($unreadCount > 0)
                    <span class="px-2.5 py-1 rounded-md text-[9px] font-black uppercase tracking-widest border shadow-sm bg-rose-50 text-rose-700 border-rose-200 animate-pulse">Pending</span>
                    @endif
                </div>
                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1 relative z-10">Unread Messages</p>
                <h2 class="text-3xl font-black text-rose-600 relative z-10">{{ number_format($unreadCount) }}</h2>
            </div>

            {{-- Today --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-sky-500/10 rounded-full blur-xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-start mb-4 relative z-10">
                    <div class="w-12 h-12 bg-sky-50 text-sky-600 rounded-2xl flex items-center justify-center border border-sky-100 shadow-sm">
                        <i class="fa-solid fa-calendar-day text-lg"></i>
                    </div>
                </div>
                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1 relative z-10">Today's Messages</p>
                <h2 class="text-3xl font-black text-sky-600 relative z-10">{{ number_format($todayCount) }}</h2>
            </div>
        </div>

        {{-- ================= 3. FILTER TABS ================= --}}
        <div class="flex items-center gap-2 flex-wrap">
            @foreach(['all' => 'All Messages', 'unread' => 'Unread', 'read' => 'Read'] as $key => $label)
                <a href="{{ route('admin.contacts.index', ['filter' => $key, 'search' => $search]) }}"
                   class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all border {{ $filter === $key ? 'bg-slate-900 text-white border-slate-900 shadow-md' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50 hover:text-slate-900' }}">
                    {{ $label }}
                    @if($key === 'unread' && $unreadCount > 0)
                        <span class="ml-1.5 text-[9px] px-1.5 py-0.5 rounded bg-rose-500 text-white font-black">{{ $unreadCount }}</span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- ================= 4. MESSAGE TABLE ================= --}}
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">

            @if($contacts->count())
            <div class="overflow-x-auto">
                <table class="w-full text-left" id="contactsTable">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-5 text-xs font-black text-slate-400 uppercase tracking-widest w-12">Status</th>
                            <th class="px-6 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Sender</th>
                            <th class="px-6 py-5 text-xs font-black text-slate-400 uppercase tracking-widest hidden md:table-cell">Subject</th>
                            <th class="px-6 py-5 text-xs font-black text-slate-400 uppercase tracking-widest hidden lg:table-cell">Message Preview</th>
                            <th class="px-6 py-5 text-xs font-black text-slate-400 uppercase tracking-widest hidden sm:table-cell">Location</th>
                            <th class="px-6 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Time</th>
                            <th class="px-6 py-5 text-xs font-black text-slate-400 uppercase tracking-widest text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($contacts as $contact)
                        <tr class="group hover:bg-slate-50/50 transition-colors {{ !$contact->is_read ? 'bg-violet-50/30' : '' }}" id="row-{{ $contact->id }}">
                            
                            {{-- Status Dot (Toggle) --}}
                            <td class="px-6 py-4">
                                <button onclick="toggleRead({{ $contact->id }})" 
                                        id="statusDot-{{ $contact->id }}"
                                        class="w-3.5 h-3.5 rounded-full cursor-pointer transition-all duration-300 hover:scale-150 focus:outline-none {{ $contact->is_read ? 'bg-slate-300' : 'bg-violet-500 shadow-[0_0_8px_rgba(139,92,246,0.5)] animate-pulse' }}"
                                        title="{{ $contact->is_read ? 'Mark as unread' : 'Mark as read' }}">
                                </button>
                            </td>

                            {{-- Sender --}}
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.contacts.show', $contact) }}" class="group/link">
                                    <p class="text-sm font-bold text-slate-900 group-hover/link:text-violet-600 transition-colors {{ !$contact->is_read ? 'font-black' : '' }}">
                                        {{ $contact->name }}
                                    </p>
                                    <p class="text-xs text-slate-500 font-mono mt-0.5">{{ $contact->email }}</p>
                                </a>
                            </td>

                            {{-- Subject --}}
                            <td class="px-6 py-4 hidden md:table-cell">
                                <span class="text-sm text-slate-600 font-medium">{{ Str::limit($contact->subject, 30) }}</span>
                            </td>

                            {{-- Message Preview --}}
                            <td class="px-6 py-4 hidden lg:table-cell">
                                <p class="text-xs text-slate-500 font-medium truncate max-w-[200px]">{{ Str::limit($contact->message, 50) }}</p>
                            </td>

                            {{-- Location --}}
                            <td class="px-6 py-4 hidden sm:table-cell">
                                @if($contact->has_location)
                                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md border border-emerald-200">
                                        <i class="fa-solid fa-location-dot text-[8px]"></i>
                                        {{ $contact->location_label ?? 'Located' }}
                                    </span>
                                @else
                                    <span class="text-[10px] font-bold text-slate-400">—</span>
                                @endif
                            </td>

                            {{-- Timestamp --}}
                            <td class="px-6 py-4">
                                <div class="text-[10px] font-black uppercase tracking-widest text-slate-400 font-mono">
                                    {{ $contact->created_at->diffForHumans() }}
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.contacts.show', $contact) }}" 
                                       class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400 hover:text-violet-600 hover:bg-violet-50 hover:border-violet-200 transition-all shadow-sm"
                                       title="View Message">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <button onclick="confirmDelete({{ $contact->id }})" 
                                            class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 transition-all shadow-sm"
                                            title="Delete Message">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $contacts->links() }}
            </div>

            @else
                {{-- Empty State --}}
                <div class="py-20 text-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center border border-slate-100 mb-6 mx-auto shadow-inner">
                        <i class="fa-solid fa-inbox text-4xl text-slate-300"></i>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 mb-2">Inbox Clear</h3>
                    <p class="text-sm text-slate-500 font-medium max-w-sm mx-auto">
                        @if($search)
                            No messages match "<strong>{{ $search }}</strong>". Try a different search term.
                        @elseif($filter !== 'all')
                            No {{ $filter }} messages found.
                        @else
                            No contact messages have been received yet.
                        @endif
                    </p>
                </div>
            @endif
        </div>

    </div>

    {{-- ================= DELETE CONFIRMATION MODAL ================= --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[1000] flex items-center justify-center p-4">
        <div x-show="deleteModal" x-transition.opacity.duration.300ms class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="deleteModal = false"></div>
        <div x-show="deleteModal"
             x-transition:enter="transition ease-out duration-200 transform" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150 transform" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white rounded-[2rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)] max-w-md w-full overflow-hidden border border-slate-200" @click.stop>
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-rose-50 rounded-full flex items-center justify-center border border-rose-100 mb-6 mx-auto">
                    <i class="fa-solid fa-trash text-2xl text-rose-500"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900 mb-2">Purge Message?</h3>
                <p class="text-sm text-slate-500 font-medium">This action is permanent and cannot be reversed. The message will be permanently removed from the system.</p>
            </div>
            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center gap-3">
                <button @click="deleteModal = false" class="flex-1 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl font-bold text-sm shadow-sm hover:bg-slate-50 transition-colors">
                    Cancel
                </button>
                <form :action="deleteUrl" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-3 bg-rose-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-rose-500/20 hover:bg-rose-700 transition-colors">
                        Delete Permanently
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('contactAdmin', () => ({
        deleteModal: false,
        deleteUrl: '',

        init() {}
    }));
});

// Toggle read/unread via AJAX
function toggleRead(id) {
    const dot = document.getElementById('statusDot-' + id);
    const row = document.getElementById('row-' + id);

    fetch('/admin/contacts/' + id + '/toggle-read', {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (data.is_read) {
                dot.className = 'w-3.5 h-3.5 rounded-full cursor-pointer transition-all duration-300 hover:scale-150 focus:outline-none bg-slate-300';
                dot.title = 'Mark as unread';
                row.classList.remove('bg-violet-50/30');
            } else {
                dot.className = 'w-3.5 h-3.5 rounded-full cursor-pointer transition-all duration-300 hover:scale-150 focus:outline-none bg-violet-500 shadow-[0_0_8px_rgba(139,92,246,0.5)] animate-pulse';
                dot.title = 'Mark as read';
                row.classList.add('bg-violet-50/30');
            }
            // Show toast
            if (typeof window.dispatchEvent === 'function') {
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: data.message, type: 'success' } }));
            }
        }
    })
    .catch(err => {
        if (typeof window.dispatchEvent === 'function') {
            window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Failed to update status.', type: 'error' } }));
        }
    });
}

// Delete confirmation
function confirmDelete(id) {
    const component = document.querySelector('[x-data]').__x.$data || Alpine.$data(document.querySelector('[x-data="contactAdmin()"]'));
    component.deleteUrl = '/admin/contacts/' + id;
    component.deleteModal = true;
}
</script>
@endpush
