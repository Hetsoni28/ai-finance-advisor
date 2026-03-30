<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Throwable;

class ContactController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | STORE SECURE CONTACT MESSAGE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request): RedirectResponse
    {
        // 1. Strict Validation Boundaries
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'], // Capped to prevent payload attacks
        ]);

        try {
            DB::transaction(function () use ($validated, $request) {
                
                // 2. Data Normalization & Insertion
                Contact::create([
                    'name'       => trim($validated['name']),
                    'email'      => strtolower(trim($validated['email'])),
                    'subject'    => isset($validated['subject']) ? trim($validated['subject']) : 'General Inquiry',
                    // 🔥 BEAST MODE: Strip HTML tags to prevent XSS payloads in the Admin Panel
                    'message'    => strip_tags(trim($validated['message'])), 
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'is_read'    => false,
                ]);

            }, 3);

            return back()->with('success', 'Transmission successful. Our support nodes will review your message shortly.');

        } catch (Throwable $e) {
            // 3. Silent Error Logging
            Log::error('Contact Form Transmission Failed: ' . $e->getMessage(), [
                'ip_address' => $request->ip(),
                'email'      => $request->input('email')
            ]);

            // Return withInput() so the user doesn't lose what they typed!
            return back()
                ->withInput()
                ->with('error', 'System failure during transmission. Please try again later.');
        }
    }
}