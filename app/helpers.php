<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

if (!function_exists('inr_number')) {
    /**
     * number_format() with Indian digit grouping: 1960614 -> "19,60,614".
     */
    function inr_number($number, int $decimals = 0): string
    {
        $n = (float) $number;
        $s = number_format(abs($n), $decimals, '.', '');
        [$int, $frac] = array_pad(explode('.', $s, 2), 2, '');
        if (strlen($int) > 3) {
            $int = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', substr($int, 0, -3)) . ',' . substr($int, -3);
        }
        $out = $int . ($decimals > 0 ? '.' . $frac : '');
        return ($n < 0 && (float) $s != 0.0 ? '-' : '') . $out;
    }
}

if (!function_exists('generateChart')) {
    /**
     * Safely generates a Base64 encoded chart image via QuickChart API.
     * Engineered with strict timeouts to prevent server blocking.
     *
     * @param array $labels
     * @param array $income
     * @param array $expense
     * @return string|null
     */
    function generateChart(array $labels, array $income, array $expense): ?string
    {
        try {
            // 1. Build the Configuration Natively (No 3rd-party packages required)
            $chartConfig = [
                'type' => 'line',
                'data' => [
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label'           => 'Gross Inflow',
                            'data'            => $income,
                            'borderColor'     => '#10b981', // Emerald 500
                            'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                            'fill'            => true,
                            'borderWidth'     => 2,
                            'pointRadius'     => 0, // Clean SaaS look
                        ],
                        [
                            'label'           => 'Total Outflow',
                            'data'            => $expense,
                            'borderColor'     => '#f43f5e', // Rose 500
                            'backgroundColor' => 'rgba(244, 63, 94, 0.1)',
                            'fill'            => true,
                            'borderWidth'     => 2,
                            'pointRadius'     => 0, // Clean SaaS look
                        ],
                    ],
                ],
                'options' => [
                    'legend' => ['position' => 'bottom'],
                    'scales' => [
                        'yAxes' => [['ticks' => ['beginAtZero' => true]]],
                        'xAxes' => [['gridLines' => ['display' => false]]]
                    ]
                ]
            ];

            // 2. Encode payload for URL
            $encodedConfig = urlencode(json_encode($chartConfig));
            
            // Define width, height, and version for rendering stability
            $url = "https://quickchart.io/chart?w=700&h=250&v=2.9.4&c={$encodedConfig}";

            // 3. SECURE HTTP REQUEST: Strict 3-second timeout to prevent server hangs
            $response = Http::timeout(3)->get($url);

            // 4. Validate Response
            if ($response->successful()) {
                $base64 = base64_encode($response->body());
                
                // 🚨 FIX: Must append the Data URI Scheme for HTML <img> tags to read it
                return 'data:image/png;base64,' . $base64;
            }

            // Log non-200 responses (e.g., rate limits)
            Log::warning("QuickChart API failed to render chart. HTTP Status: " . $response->status());
            return null;

        } catch (\Throwable $e) {
            // Log exact exception (timeout, DNS failure, etc.) for debugging
            Log::error("Chart Generation Exception: " . $e->getMessage());
            return null;
        }
    }
}