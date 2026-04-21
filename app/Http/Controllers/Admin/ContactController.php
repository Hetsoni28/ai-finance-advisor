<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ContactController extends Controller
{
    /**
     * Enforce strict Master Node authentication.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX: LIST ALL CONTACT MESSAGES
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        $filter = $request->get('filter', 'all');
        $search = $request->get('search');

        $query = Contact::recent()->search($search);

        // Apply filter
        if ($filter === 'unread') {
            $query->unread();
        } elseif ($filter === 'read') {
            $query->read();
        }

        $contacts    = $query->paginate(15)->withQueryString();
        $totalCount  = Contact::count();
        $unreadCount = Contact::unread()->count();
        $todayCount  = Contact::whereDate('created_at', today())->count();

        return view('admin.contacts.index', compact(
            'contacts',
            'totalCount',
            'unreadCount',
            'todayCount',
            'filter',
            'search'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW: VIEW SINGLE MESSAGE DETAIL
    |--------------------------------------------------------------------------
    */
    public function show(Contact $contact): View
    {
        // Auto-mark as read when admin views the message
        $contact->markAsRead();

        return view('admin.contacts.show', compact('contact'));
    }

    /*
    |--------------------------------------------------------------------------
    | TOGGLE READ/UNREAD STATUS (AJAX)
    |--------------------------------------------------------------------------
    */
    public function toggleRead(Request $request, Contact $contact): JsonResponse
    {
        try {
            if ($contact->is_read) {
                $contact->markAsUnread();
            } else {
                $contact->markAsRead();
            }

            return response()->json([
                'success'  => true,
                'is_read'  => $contact->fresh()->is_read,
                'message'  => $contact->fresh()->is_read ? 'Marked as read.' : 'Marked as unread.',
            ]);
        } catch (Throwable $e) {
            Log::error('Contact Toggle Read Failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY: DELETE A CONTACT MESSAGE
    |--------------------------------------------------------------------------
    */
    public function destroy(Contact $contact): RedirectResponse
    {
        try {
            $contact->delete();

            return back()->with('success', 'Contact message purged from the system.');
        } catch (Throwable $e) {
            Log::error('Contact Delete Failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete message. Check server logs.');
        }
    }
}
