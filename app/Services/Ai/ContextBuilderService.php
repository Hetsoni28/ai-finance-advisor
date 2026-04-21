<?php

declare(strict_types=1);

namespace App\Services\Ai;

use App\Models\AiChat;
use Illuminate\Support\Facades\Auth;

/**
 * RAG Context Builder — Assembles the complete data payload for the LLM.
 * Injects user financial data, market data, goals, and chat history.
 */
final class ContextBuilderService
{
    private MarketDataService $marketService;

    public function __construct(MarketDataService $marketService)
    {
        $this->marketService = $marketService;
    }

    /**
     * Build the full context string for the LLM prompt.
     */
    public function build(int $userId, array $intentData): string
    {
        $user = Auth::user();
        $sections = [];

        // 1. User Financial Snapshot (always included)
        $sections[] = $this->buildFinancialContext($user);

        // 2. Market Data (only if intent requires it)
        if ($intentData['needs_market']) {
            $sections[] = $this->buildMarketContext();
        }

        // 3. Goals Context (only if intent requires it)
        if ($intentData['needs_goals']) {
            $sections[] = $this->buildGoalsContext($user);
        }

        // 4. Chat History (always included for memory)
        $sections[] = $this->buildChatHistory($userId);

        // 5. Investment disclaimer trigger
        if ($intentData['is_investment']) {
            $sections[] = "IMPORTANT: The user is asking about investments. Include specific allocation percentages based on their risk profile. Always end with the financial disclaimer.";
        }

        return implode("\n\n---\n\n", array_filter($sections));
    }

    private function buildFinancialContext($user): string
    {
        $snapshot = $user->financial_snapshot;

        $lines = [
            "## User Financial Profile",
            "- **Name**: " . explode(' ', $user->name ?? 'User')[0],
            "- **Net Worth (all time)**: ₹" . number_format($snapshot['net_worth'], 2),
            "- **Lifetime Income**: ₹" . number_format($snapshot['total_income'], 2),
            "- **Lifetime Expenses**: ₹" . number_format($snapshot['total_expense'], 2),
            "- **Monthly Income (30d)**: ₹" . number_format($snapshot['monthly_income'], 2),
            "- **Monthly Expenses (30d)**: ₹" . number_format($snapshot['monthly_expense'], 2),
            "- **Savings Rate**: {$snapshot['saving_rate']}%",
            "- **Financial Runway**: {$snapshot['runway_months']} months",
            "- **Risk Tolerance**: {$snapshot['risk_tolerance']}",
            "- **Investment Experience**: {$snapshot['investment_exp']}",
            "- **Age Group**: {$snapshot['age_group']}",
        ];

        if (!empty($snapshot['top_categories'])) {
            $lines[] = "\n### Top Expense Categories (Last 30 Days)";
            foreach ($snapshot['top_categories'] as $cat => $amount) {
                $lines[] = "- **" . ucfirst($cat) . "**: ₹" . number_format((float) $amount, 2);
            }
        }

        // Warnings for AI to consider
        if ($snapshot['saving_rate'] < 10) {
            $lines[] = "\n⚠️ WARNING: User has a dangerously low savings rate. Prioritize expense reduction advice.";
        }
        if ($snapshot['runway_months'] < 3) {
            $lines[] = "⚠️ WARNING: User has less than 3 months of financial runway. Emergency fund should be priority #1.";
        }
        if ($snapshot['monthly_expense'] > $snapshot['monthly_income'] && $snapshot['monthly_income'] > 0) {
            $lines[] = "🚨 CRITICAL: User is spending more than they earn. This is unsustainable.";
        }

        return implode("\n", $lines);
    }

    private function buildMarketContext(): string
    {
        return $this->marketService->getMarketContext();
    }

    private function buildGoalsContext($user): string
    {
        $goals = $user->financialGoals()->active()->get();

        if ($goals->isEmpty()) {
            return "## Financial Goals\nThe user has not set any financial goals yet. You may suggest creating one.";
        }

        $lines = ["## Active Financial Goals"];
        foreach ($goals as $goal) {
            $status = $goal->isOnTrack() ? '✅ On Track' : '⚠️ Behind Schedule';
            $lines[] = "- **{$goal->title}** ({$goal->category}): ₹" .
                       number_format($goal->current_amount) . " / ₹" .
                       number_format($goal->target_amount) .
                       " ({$goal->progress_percent}% — {$status})";
            if ($goal->monthly_required) {
                $lines[] = "  → Needs ₹" . number_format($goal->monthly_required) . "/month to stay on track";
            }
        }

        return implode("\n", $lines);
    }

    private function buildChatHistory(int $userId): string
    {
        $limit = (int) config('financeai.context.max_history_messages', 10);

        $messages = AiChat::where('user_id', $userId)
            ->latest('id')
            ->limit($limit)
            ->get()
            ->reverse();

        if ($messages->isEmpty()) {
            return '';
        }

        $lines = ["## Recent Conversation History"];
        foreach ($messages as $msg) {
            $role = $msg->sender === AiChat::SENDER_USER ? 'User' : 'FinanceAI';
            // Truncate long messages in history to save tokens
            $text = mb_strlen($msg->message) > 300
                ? mb_substr($msg->message, 0, 300) . '...'
                : $msg->message;
            $lines[] = "**{$role}**: {$text}";
        }

        return implode("\n", $lines);
    }
}
