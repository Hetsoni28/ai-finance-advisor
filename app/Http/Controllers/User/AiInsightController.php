<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\Ai\AiInsightService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

/** JSON endpoints for the small AI features (dashboard summary, category suggestion). */
class AiInsightController extends Controller
{
    public function __construct(private AiInsightService $ai)
    {
        $this->middleware('auth');
    }

    /** Dashboard card: AI summary of the user's month. Loaded after the page so the dashboard stays fast. */
    public function summary(): JsonResponse
    {
        try {
            return response()->json($this->ai->monthSummary(Auth::user()));
        } catch (Throwable $e) {
            Log::warning('AI summary failed: ' . $e->getMessage());
            return response()->json(['text' => "The summary isn't available right now. Please try again in a moment.", 'source' => 'error'], 200);
        }
    }

    /**
     * Expense form: read a photo of a bill with Gemini and return the fields to fill in.
     * The browser shrinks the photo before upload; the file is read in memory and never stored.
     */
    public function scanReceipt(Request $request): JsonResponse
    {
        $request->validate([
            'receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'receipt.mimes' => 'Please upload a JPG, PNG or WebP photo of the bill.',
            'receipt.max'   => 'That photo is too large. Please use one under 4 MB.',
        ]);

        $file = $request->file('receipt');

        try {
            $result = $this->ai->scanReceipt((string) file_get_contents($file->getRealPath()), $file->getMimeType() ?: 'image/jpeg');
        } catch (Throwable $e) {
            Log::warning('Receipt scan failed: ' . $e->getMessage());
            $result = ['ok' => false, 'message' => 'Something went wrong while reading the bill. Please try again or fill in the details yourself.'];
        }

        return response()->json($result);
    }

    /** Entry forms: suggest a category for the title the user is typing. */
    public function categorize(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'type'  => ['required', 'in:income,expense'],
        ]);

        try {
            return response()->json($this->ai->suggestCategory($data['title'], $data['type']));
        } catch (Throwable $e) {
            Log::warning('AI categorize failed: ' . $e->getMessage());
            return response()->json(['category' => null, 'source' => 'none']);
        }
    }
}
