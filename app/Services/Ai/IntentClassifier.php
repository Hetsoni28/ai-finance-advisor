<?php

declare(strict_types=1);

namespace App\Services\Ai;

use Illuminate\Support\Str;

/**
 * Lightweight intent classifier for financial queries.
 * Determines which data sources to activate per request.
 */
final class IntentClassifier
{
    /**
     * Classify a user message into a financial intent.
     *
     * @return array{intent: string, needs_market: bool, needs_goals: bool, is_investment: bool}
     */
    public function classify(string $message): array
    {
        $input = strtolower(trim($message));

        // Slash commands (highest priority)
        if ($input === '/analyze' || $input === '/report') {
            return $this->result('expense_analysis', false, false, false);
        }
        if ($input === '/runway' || $input === '/risk') {
            return $this->result('risk_assessment', false, false, false);
        }
        if ($input === '/market' || $input === '/prices') {
            return $this->result('market_query', true, false, false);
        }
        if ($input === '/goals') {
            return $this->result('goal_planning', false, true, false);
        }
        if ($input === '/profile') {
            return $this->result('profile_setup', false, false, false);
        }
        if (Str::startsWith($input, '/invest')) {
            return $this->result('investment_advice', true, false, true);
        }

        // Market & Investment queries
        if (Str::contains($input, ['stock', 'nifty', 'sensex', 'share market', 'bitcoin', 'btc', 'ethereum', 'eth', 'crypto', 'gold price', 'market'])) {
            return $this->result('market_query', true, false, false);
        }

        if (Str::contains($input, ['invest', 'where to put', 'portfolio', 'mutual fund', 'sip', 'ppf', 'nps', 'fd', 'fixed deposit', 'elss', 'etf', 'allocat'])) {
            return $this->result('investment_advice', true, false, true);
        }

        // Savings & Budget
        if (Str::contains($input, ['save', 'saving', 'budget', 'cut', 'reduce', 'spend less', 'frugal'])) {
            return $this->result('savings_strategy', false, false, false);
        }

        // Expense Analysis
        if (Str::contains($input, ['expense', 'spending', 'spent', 'category', 'categories', 'biggest', 'top', 'highest', 'analyze', 'analysis', 'overview', 'summary', 'report', 'audit'])) {
            return $this->result('expense_analysis', false, false, false);
        }

        // Goal Planning
        if (Str::contains($input, ['goal', 'target', 'house', 'car', 'education', 'retire', 'wedding', 'emergency fund', 'plan for'])) {
            return $this->result('goal_planning', false, true, false);
        }

        // Risk Assessment
        if (Str::contains($input, ['risk', 'runway', 'emergency', 'safe', 'danger', 'financial health', 'score'])) {
            return $this->result('risk_assessment', false, false, false);
        }

        // Greetings
        if (Str::contains($input, ['hi', 'hello', 'hey', 'good morning', 'good evening', 'help', 'what can you'])) {
            return $this->result('greeting', false, false, false);
        }

        // Default: general financial chat
        return $this->result('general_chat', false, false, false);
    }

    private function result(string $intent, bool $needsMarket, bool $needsGoals, bool $isInvestment): array
    {
        return [
            'intent'        => $intent,
            'needs_market'  => $needsMarket,
            'needs_goals'   => $needsGoals,
            'is_investment' => $isInvestment,
        ];
    }
}
