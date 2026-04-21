<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Throwable;

class ContactController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | STORE SECURE CONTACT MESSAGE
    |--------------------------------------------------------------------------
    | Supports both traditional form POST and AJAX (JSON) requests.
    */
    public function store(Request $request)
    {
        // 1. Strict Validation Boundaries
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255'],
            'subject'   => ['nullable', 'string', 'max:255'],
            'message'   => ['required', 'string', 'max:5000'], // Capped to prevent payload attacks
            'latitude'  => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        try {
            DB::transaction(function () use ($validated, $request) {
                
                // 2. Data Normalization & Insertion
                Contact::create([
                    'name'           => trim($validated['name']),
                    'email'          => strtolower(trim($validated['email'])),
                    'subject'        => isset($validated['subject']) ? trim($validated['subject']) : 'General Inquiry',
                    // 🔥 BEAST MODE: Strip HTML tags to prevent XSS payloads in the Admin Panel
                    'message'        => strip_tags(trim($validated['message'])), 
                    'ip_address'     => $request->ip(),
                    'user_agent'     => $request->userAgent(),
                    'is_read'        => false,
                    'latitude'       => isset($validated['latitude']) ? (float) $validated['latitude'] : null,
                    'longitude'      => isset($validated['longitude']) ? (float) $validated['longitude'] : null,
                    'location_label' => $this->buildLocationLabel(
                        isset($validated['latitude']) ? (float) $validated['latitude'] : null,
                        isset($validated['longitude']) ? (float) $validated['longitude'] : null
                    ),
                ]);

            }, 3);

            $successMessage = 'Transmission successful. Our support nodes will review your message shortly.';

            // 3. Return JSON for AJAX or redirect for traditional form
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                ], 201);
            }

            return back()->with('success', $successMessage);

        } catch (Throwable $e) {
            // 4. Silent Error Logging
            Log::error('Contact Form Transmission Failed: ' . $e->getMessage(), [
                'ip_address' => $request->ip(),
                'email'      => $request->input('email')
            ]);

            $errorMessage = 'System failure during transmission. Please try again later.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 500);
            }

            // Return withInput() so the user doesn't lose what they typed!
            return back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    /**
     * Build a human-readable location label from coordinates.
     */
    private function buildLocationLabel(?float $lat, ?float $lng): ?string
    {
        if (is_null($lat) || is_null($lng)) {
            return null;
        }

        // Generate a simple coordinate label
        $latDir = $lat >= 0 ? 'N' : 'S';
        $lngDir = $lng >= 0 ? 'E' : 'W';

        return sprintf('%.4f°%s, %.4f°%s', abs($lat), $latDir, abs($lng), $lngDir);
    }
}