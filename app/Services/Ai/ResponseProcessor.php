<?php

declare(strict_types=1);

namespace App\Services\Ai;

use Illuminate\Support\Str;

/**
 * Post-processes LLM responses with confidence scoring,
 * source citations, and financial disclaimers.
 */
final class ResponseProcessor
{
    /**
     * Process the raw LLM output into a final user-ready response.
     *
     * @param string $rawContent The LLM-generated text
     * @param array  $intentData Intent classification result
     * @param array  $sources    Data sources used (e.g., ['CoinGecko', 'Database'])
     * @return array{content: string, confidence: string, confidence_label: string, sources: array}
     */
    public function process(string $rawContent, array $intentData, array $sources = []): array
    {
        $content = $this->sanitize($rawContent);

        // Calculate confidence based on data availability
        $confidence = $this->calculateConfidence($intentData, $sources);

        // Add source citation footer if external data was used
        if (!empty($sources)) {
            $sourceStr = implode(', ', $sources);
            $timestamp = now()->setTimezone('Asia/Kolkata')->format('h:i A');
            $content .= "\n\n---\n📊 **Sources**: {$sourceStr} · Updated at {$timestamp}";
        }

        // Append disclaimer for investment/financial advice
        if ($intentData['is_investment'] || $intentData['intent'] === 'investment_advice') {
            $disclaimer = config('financeai.disclaimer', '');
            if ($disclaimer && !Str::contains($content, 'not financial advice')) {
                $content .= "\n\n" . $disclaimer;
            }
        }

        return [
            'content'          => $content,
            'confidence'       => $confidence['level'],
            'confidence_label' => $confidence['label'],
            'sources'          => $sources,
        ];
    }

    /**
     * Calculate confidence score based on available data.
     */
    private function calculateConfidence(array $intentData, array $sources): array
    {
        $score = 50; // Base confidence

        // Having real DB data increases confidence
        if (in_array('User Database', $sources)) {
            $score += 30;
        }

        // Live market data increases confidence for market queries
        if ($intentData['needs_market'] && in_array('CoinGecko API', $sources)) {
            $score += 15;
        }

        // General chat doesn't need high confidence
        if ($intentData['intent'] === 'general_chat' || $intentData['intent'] === 'greeting') {
            $score = 85;
        }

        // Investment advice is inherently less certain
        if ($intentData['is_investment']) {
            $score = min($score, 75);
        }

        // Market queries without live data = low confidence
        if ($intentData['needs_market'] && !in_array('CoinGecko API', $sources)) {
            $score = max($score - 25, 30);
        }

        return match (true) {
            $score >= 75 => ['level' => 'high',   'label' => '🟢 High Confidence'],
            $score >= 50 => ['level' => 'medium', 'label' => '🟡 Medium Confidence'],
            default      => ['level' => 'low',    'label' => '🔴 Low Confidence — Verify Independently'],
        };
    }

    /**
     * Sanitize LLM output to prevent injection or malformed content.
     */
    private function sanitize(string $content): string
    {
        // Remove any system prompt leakage
        $content = preg_replace('/={3,}\s*(USER\'S REAL|END OF DATA|SYSTEM).*?={3,}/si', '', $content);

        // Remove excessive whitespace
        $content = preg_replace('/\n{4,}/', "\n\n\n", $content);

        return trim($content);
    }
}
