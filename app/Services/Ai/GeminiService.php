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
    /** @var string[] Models to try in order; later ones are used when earlier ones are busy or retired. */
    private array $models;
    private string $endpoint;
    private int $maxTokens;
    private float $temperature;
    private int $timeout;

    public function __construct()
    {
        $config = config('financeai.gemini');
        $this->apiKey      = (string) ($config['api_key'] ?? '');
        $this->models      = array_values(array_filter((array) ($config['models'] ?? ['gemini-flash-latest'])));
        $this->endpoint    = (string) ($config['endpoint'] ?? 'https://generativelanguage.googleapis.com/v1beta/models/');
        $this->maxTokens   = (int) ($config['max_tokens'] ?? 2048);
        $this->temperature = (float) ($config['temperature'] ?? 0.7);
        $this->timeout     = (int) ($config['timeout'] ?? 25);
    }

    /**
     * Check if the Gemini API is configured and ready.
     */
    public function isAvailable(): bool
    {
        return !empty($this->apiKey) && !empty($this->models);
    }

    /**
     * Send a prompt to Gemini and get a response. Tries each configured model in turn when one is
     * busy (429/503), retired (404) or erroring (5xx), so a single model outage doesn't break the advisor.
     *
     * @return array{success: bool, content: string, tokens_used: int, model: string, error?: string}
     */
    public function generate(string $systemPrompt, string $context, string $userMessage): array
    {
        return $this->run([['text' => $this->buildPrompt($systemPrompt, $context, $userMessage)]], $this->maxTokens, $this->temperature, 20);
    }

    /**
     * Short, single-purpose request (dashboard summary, category suggestion, report explanation).
     * The caller writes the whole prompt; output is plain text. $budget caps the total wait in seconds.
     */
    public function complete(string $prompt, int $maxTokens = 400, float $temperature = 0.3, int $budget = 12): array
    {
        return $this->run([['text' => $prompt]], $maxTokens, $temperature, $budget);
    }

    /**
     * Ask Gemini about an image (e.g. read a receipt). The answer is forced to be JSON.
     * $imageData is the raw image bytes; $mime e.g. image/jpeg.
     */
    public function vision(string $prompt, string $imageData, string $mime, int $maxTokens = 1024, int $budget = 25): array
    {
        $parts = [
            ['inline_data' => ['mime_type' => $mime, 'data' => base64_encode($imageData)]],
            ['text' => $prompt],
        ];
        return $this->run($parts, $maxTokens, 0.1, $budget, 'application/json');
    }

    private function run(array $parts, int $maxTokens, float $temperature, int $budget, ?string $responseMime = null): array
    {
        if (!$this->isAvailable()) {
            return ['success' => false, 'content' => '', 'tokens_used' => 0, 'model' => 'fallback', 'error' => 'no_api_key'];
        }

        $payload = [
            'contents' => [
                ['role' => 'user', 'parts' => $parts],
            ],
            'generationConfig' => array_filter([
                'temperature'      => $temperature,
                'maxOutputTokens'  => $maxTokens,
                'topP'             => 0.95,
                'topK'             => 40,
                'responseMimeType' => $responseMime,
            ], fn ($v) => $v !== null),
            'safetySettings' => [
                ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_ONLY_HIGH'],
                ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_ONLY_HIGH'],
            ],
        ];

        $last = ['success' => false, 'content' => '', 'tokens_used' => 0, 'model' => 'fallback', 'error' => 'no_model_tried'];

        // Up to two rounds through the model list ("high demand" 503s usually clear within seconds),
        // but never keep the user waiting longer than $budget seconds — after that the built-in rules answer.
        $started = microtime(true);
        for ($round = 1; $round <= 2; $round++) {
            foreach ($this->models as $model) {
                if (microtime(true) - $started > $budget) {
                    break 2;
                }
                $last = $this->callModel($model, $payload);
                if ($last['success'] || empty($last['retryable'])) {
                    break 2;
                }
            }
            if ($round === 1) {
                usleep(1_000_000);
            }
        }

        unset($last['retryable']);
        return $last;
    }

    /** One request to one model. Marks failures that another model might not have as retryable. */
    private function callModel(string $model, array $payload): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['Content-Type' => 'application/json', 'x-goog-api-key' => $this->apiKey])
                ->post($this->endpoint . $model . ':generateContent', $payload);

            if ($response->successful()) {
                $data = $response->json();
                // The answer can arrive split over several parts; join them all (skipping "thought" parts).
                $content = collect($data['candidates'][0]['content']['parts'] ?? [])
                    ->reject(fn ($p) => !empty($p['thought']))
                    ->pluck('text')->filter()->implode('');
                $finish = $data['candidates'][0]['finishReason'] ?? '';

                // A reply that stopped early (token limit, safety, etc.) ends mid-sentence — never show it.
                if ($finish !== '' && $finish !== 'STOP') {
                    Log::info("Gemini {$model} reply was cut off ({$finish}, " . ($data['usageMetadata']['thoughtsTokenCount'] ?? 0) . ' thinking tokens)');
                    return ['success' => false, 'content' => '', 'tokens_used' => 0, 'model' => $model, 'error' => 'incomplete_' . strtolower($finish), 'retryable' => true];
                }

                if ($content === '') {
                    Log::warning('Gemini returned empty response', ['model' => $model, 'finish' => $data['candidates'][0]['finishReason'] ?? null]);
                    return ['success' => false, 'content' => '', 'tokens_used' => 0, 'model' => $model, 'error' => 'empty_response', 'retryable' => true];
                }

                return [
                    'success'     => true,
                    'content'     => $content,
                    'tokens_used' => (int) ($data['usageMetadata']['totalTokenCount'] ?? 0),
                    'model'       => $data['modelVersion'] ?? $model,
                ];
            }

            $status = $response->status();
            $errorMsg = $response->json('error.message') ?? 'Unknown API error';
            Log::warning("Gemini {$model} failed ({$status}): {$errorMsg}");

            return [
                'success'     => false,
                'content'     => '',
                'tokens_used' => 0,
                'model'       => $model,
                'error'       => $errorMsg,
                'retryable'   => in_array($status, [404, 408, 429], true) || $status >= 500,
            ];
        } catch (Throwable $e) {
            Log::warning("Gemini {$model} exception: " . $e->getMessage());
            return ['success' => false, 'content' => '', 'tokens_used' => 0, 'model' => $model, 'error' => $e->getMessage(), 'retryable' => true];
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
