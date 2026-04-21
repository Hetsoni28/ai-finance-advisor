<?php

return [

    /*
    |--------------------------------------------------------------------------
    | LLM Provider Configuration (Google Gemini)
    |--------------------------------------------------------------------------
    */
    'gemini' => [
        'api_key'     => env('GEMINI_API_KEY', ''),
        'model'       => env('FINANCEAI_MODEL', 'gemini-2.0-flash'),
        'endpoint'    => 'https://generativelanguage.googleapis.com/v1beta/models/',
        'max_tokens'  => 2048,
        'temperature' => 0.7,
        'timeout'     => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Market Data APIs
    |--------------------------------------------------------------------------
    */
    'market' => [
        'coingecko' => [
            'base_url' => 'https://api.coingecko.com/api/v3',
            'timeout'  => 10,
        ],
        'alphavantage' => [
            'api_key'  => env('ALPHA_VANTAGE_API_KEY', ''),
            'base_url' => 'https://www.alphavantage.co/query',
            'timeout'  => 10,
        ],
        'cache_ttl' => 900, // 15 minutes in seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | AI System Prompt & Behavior
    |--------------------------------------------------------------------------
    */
    'system_prompt' => <<<'PROMPT'
You are FinanceAI, an expert Indian financial intelligence assistant built into a personal finance management platform. You have direct access to the user's real financial data which is provided in the context below.

CRITICAL RULES:
1. NEVER fabricate or hallucinate financial numbers. Only use the data provided in the context.
2. All monetary values must be in INR (₹) unless the user specifically asks about foreign currencies.
3. When discussing investments, ALWAYS include the disclaimer that you are not a SEBI-registered advisor.
4. When market data is available in context, cite the source and timestamp.
5. For investment suggestions, always consider the user's risk tolerance and financial situation.
6. Use Markdown formatting: tables, bold, bullet points for clarity.
7. Be concise but thorough. Prioritize actionable insights over generic advice.
8. If you don't have enough data to answer accurately, say so honestly.
9. Focus on Indian financial instruments: Mutual Funds, PPF, NPS, FDs, ELSS, Gold ETFs, NIFTY/SENSEX indices.
10. Never recommend specific stock picks. Recommend asset classes and index funds only.
PROMPT,

    /*
    |--------------------------------------------------------------------------
    | Disclaimer Text
    |--------------------------------------------------------------------------
    */
    'disclaimer' => '⚠️ *This is AI-generated analysis, not financial advice. Consult a SEBI-registered advisor before making investment decisions.*',

    /*
    |--------------------------------------------------------------------------
    | Rate Limits (Per User Per Day)
    |--------------------------------------------------------------------------
    */
    'limits' => [
        'free'    => 20,
        'pro'     => 200,
        'premium' => 1000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Context Window Settings
    |--------------------------------------------------------------------------
    */
    'context' => [
        'max_history_messages' => 10,
        'max_display_messages' => 50,
    ],

];
