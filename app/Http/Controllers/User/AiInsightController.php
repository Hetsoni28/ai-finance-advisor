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
