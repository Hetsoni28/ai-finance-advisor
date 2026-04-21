<?php

declare(strict_types=1);

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Google Gemini API integration via pure HTTP (no SDK needed).
 * Supports graceful fallback to rule-based engine if no API key is set.
 */
final class GeminiService
{
    private string $apiKey;
    private string $model;
    private string $endpoint;
    private int $maxTokens;
    private float $temperature;
    private int $timeout;

    public function __construct()
    {
        $config = config('financeai.gemini');
        $this->apiKey      = (string) ($config['api_key'] ?? '');
        $this->model       = (string) ($config['model'] ?? 'gemini-2.0-flash');
        $this->endpoint    = (string) ($config['endpoint'] ?? 'https://generativelanguage.googleapis.com/v1beta/models/');
        $this->maxTokens   = (int) ($config['max_tokens'] ?? 2048);
        $this->temperature = (float) ($config['temperature'] ?? 0.7);
        $this->timeout     = (int) ($config['timeout'] ?? 30);
    }

    /**
     * Check if the Gemini API is configured and ready.
     */
    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Send a prompt to Gemini and get a response.
     *
     * @param string $systemPrompt The system instructions
     * @param string $context The RAG context data
     * @param string $userMessage The actual user message
     * @return array{success: bool, content: string, tokens_used: int, model: string}
     */
    public function generate(string $systemPrompt, string $context, string $userMessage): array
    {
        if (!$this->isAvailable()) {
            return [
                'success'     => false,
                'content'     => '',
                'tokens_used' => 0,
                'model'       => 'fallback',
                'error'       => 'no_api_key',
            ];
        }

        try {
            $url = $this->endpoint . $this->model . ':generateContent?key=' . $this->apiKey;

            $payload = [
                'contents' => [
                    [
                        'role'  => 'user',
                        'parts' => [
                            ['text' => $this->buildPrompt($systemPrompt, $context, $userMessage)],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature'     => $this->temperature,
                    'maxOutputTokens' => $this->maxTokens,
                    'topP'            => 0.95,
                    'topK'            => 40,
                ],
                'safetySettings' => [
                    ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                    ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_ONLY_HIGH'],
                    ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                    ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                ],
            ];

            $response = Http::timeout($this->timeout)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();

                $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $tokensUsed = $data['usageMetadata']['totalTokenCount'] ?? 0;

                if (empty($content)) {
                    Log::warning('Gemini returned empty response', ['data' => $data]);
                    return [
                        'success'     => false,
                        'content'     => '',
                        'tokens_used' => 0,
                        'model'       => $this->model,
                        'error'       => 'empty_response',
                    ];
                }

                return [
                    'success'     => true,
                    'content'     => $content,
                    'tokens_used' => (int) $tokensUsed,
                    'model'       => $this->model,
                ];
            }

            $errorBody = $response->json();
            $errorMsg = $errorBody['error']['message'] ?? 'Unknown API error';
            Log::error('Gemini API error: ' . $errorMsg, [
                'status' => $response->status(),
                'body'   => $errorBody,
            ]);

            return [
                'success'     => false,
                'content'     => '',
                'tokens_used' => 0,
                'model'       => $this->model,
                'error'       => $errorMsg,
            ];

        } catch (Throwable $e) {
            Log::error('Gemini API exception: ' . $e->getMessage());
            return [
                'success'     => false,
                'content'     => '',
                'tokens_used' => 0,
                'model'       => $this->model,
                'error'       => $e->getMessage(),
            ];
        }
    }

    /**
     * Construct the full prompt combining system instructions, context, and user message.
     */
    private function buildPrompt(string $systemPrompt, string $context, string $userMessage): string
    {
        return <<<PROMPT
{$systemPrompt}

===== USER'S REAL FINANCIAL DATA (FROM DATABASE — DO NOT HALLUCINATE) =====

{$context}

===== END OF DATA =====

User's Current Query: {$userMessage}

Respond with accurate, data-grounded analysis using ONLY the financial data provided above. Use Markdown formatting with tables, bold text, and bullet points for clarity. If market data is provided, cite the source and time.
PROMPT;
    }
}
