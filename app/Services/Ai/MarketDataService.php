<?php

declare(strict_types=1);

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Real-time financial data aggregator.
 * CoinGecko (free, no key) + Alpha Vantage (free tier).
 * Caches aggressively to respect rate limits.
 */
final class MarketDataService
{
    private int $cacheTtl;

    public function __construct()
    {
        $this->cacheTtl = (int) config('financeai.market.cache_ttl', 900);
    }

    /**
     * Get crypto snapshot: BTC, ETH, and top coins in INR.
     */
    public function getCryptoSnapshot(): array
    {
        $cacheKey = 'market:crypto:snapshot';

        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            try {
                $response = Http::timeout(10)->get(
                    config('financeai.market.coingecko.base_url') . '/simple/price',
                    [
                        'ids'            => 'bitcoin,ethereum,solana,ripple',
                        'vs_currencies'  => 'inr,usd',
                        'include_24hr_change'     => 'true',
                        'include_market_cap'      => 'true',
                        'include_last_updated_at' => 'true',
                    ]
                );

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'available' => true,
                        'source'    => 'CoinGecko',
                        'fetched_at' => now()->setTimezone('Asia/Kolkata')->format('h:i A'),
                        'coins'     => $this->formatCryptoData($data),
                    ];
                }

                return $this->cryptoFallback('API returned non-200');
            } catch (Throwable $e) {
                Log::warning('CoinGecko API failed: ' . $e->getMessage());
                return $this->cryptoFallback($e->getMessage());
            }
        });
    }

    /**
     * Get gold price (approximation via CoinGecko commodities).
     */
    public function getGoldPrice(): array
    {
        $cacheKey = 'market:gold:price';

        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            try {
                $response = Http::timeout(10)->get(
                    config('financeai.market.coingecko.base_url') . '/simple/price',
                    [
                        'ids'           => 'tether-gold',
                        'vs_currencies' => 'inr',
                        'include_24hr_change' => 'true',
                    ]
                );

                if ($response->successful()) {
                    $data = $response->json();
                    $gold = $data['tether-gold'] ?? null;
                    return [
                        'available'  => (bool) $gold,
                        'source'     => 'CoinGecko',
                        'price_inr'  => $gold['inr'] ?? null,
                        'change_24h' => round($gold['inr_24h_change'] ?? 0, 2),
                        'fetched_at' => now()->setTimezone('Asia/Kolkata')->format('h:i A'),
                    ];
                }
                return ['available' => false, 'source' => 'CoinGecko'];
            } catch (Throwable $e) {
                Log::warning('Gold price fetch failed: ' . $e->getMessage());
                return ['available' => false, 'source' => 'CoinGecko'];
            }
        });
    }

    /**
     * Get a combined market summary for the AI context.
     */
    public function getMarketContext(): string
    {
        $crypto = $this->getCryptoSnapshot();
        $gold = $this->getGoldPrice();

        $lines = ["## Live Market Data (as of " . now()->setTimezone('Asia/Kolkata')->format('h:i A T') . ")"];

        if ($crypto['available'] && !empty($crypto['coins'])) {
            $lines[] = "\n### Cryptocurrency Prices";
            foreach ($crypto['coins'] as $coin) {
                $changeIcon = $coin['change_24h'] >= 0 ? '📈' : '📉';
                $lines[] = "- **{$coin['name']}**: ₹" . number_format($coin['price_inr'], 0) .
                           " ({$changeIcon} " . ($coin['change_24h'] >= 0 ? '+' : '') .
                           number_format($coin['change_24h'], 2) . "% 24h)";
            }
        }

        if ($gold['available'] && $gold['price_inr']) {
            $changeIcon = ($gold['change_24h'] ?? 0) >= 0 ? '📈' : '📉';
            $lines[] = "\n### Gold";
            $lines[] = "- **Gold (XAUT)**: ₹" . number_format($gold['price_inr'], 0) .
                       " ({$changeIcon} " . ($gold['change_24h'] >= 0 ? '+' : '') .
                       number_format($gold['change_24h'], 2) . "% 24h)";
        }

        $lines[] = "\n*Source: CoinGecko API (free tier). Data may have 1-5 min delay.*";

        return implode("\n", $lines);
    }

    /**
     * Get a quick sidebar-friendly market summary.
     */
    public function getSidebarData(): array
    {
        $crypto = $this->getCryptoSnapshot();

        $sidebar = [
            'btc'  => null,
            'eth'  => null,
            'gold' => null,
            'fetched_at' => $crypto['fetched_at'] ?? now()->setTimezone('Asia/Kolkata')->format('h:i A'),
            'available' => $crypto['available'],
        ];

        if ($crypto['available'] && !empty($crypto['coins'])) {
            foreach ($crypto['coins'] as $coin) {
                if ($coin['id'] === 'bitcoin') {
                    $sidebar['btc'] = [
                        'price'  => '₹' . number_format($coin['price_inr'], 0),
                        'change' => round($coin['change_24h'], 1),
                    ];
                }
                if ($coin['id'] === 'ethereum') {
                    $sidebar['eth'] = [
                        'price'  => '₹' . number_format($coin['price_inr'], 0),
                        'change' => round($coin['change_24h'], 1),
                    ];
                }
            }
        }

        $gold = $this->getGoldPrice();
        if ($gold['available'] && $gold['price_inr']) {
            $sidebar['gold'] = [
                'price'  => '₹' . number_format($gold['price_inr'], 0),
                'change' => round($gold['change_24h'] ?? 0, 1),
            ];
        }

        return $sidebar;
    }

    private function formatCryptoData(array $data): array
    {
        $coins = [];
        $nameMap = [
            'bitcoin'  => 'Bitcoin (BTC)',
            'ethereum' => 'Ethereum (ETH)',
            'solana'   => 'Solana (SOL)',
            'ripple'   => 'XRP',
        ];

        foreach ($data as $id => $info) {
            $coins[] = [
                'id'         => $id,
                'name'       => $nameMap[$id] ?? ucfirst($id),
                'price_inr'  => (float) ($info['inr'] ?? 0),
                'price_usd'  => (float) ($info['usd'] ?? 0),
                'change_24h' => (float) ($info['inr_24h_change'] ?? 0),
                'market_cap' => (float) ($info['inr_market_cap'] ?? 0),
            ];
        }

        return $coins;
    }

    private function cryptoFallback(string $reason): array
    {
        Log::info("Market data fallback triggered: {$reason}");
        return [
            'available'  => false,
            'source'     => 'CoinGecko',
            'fetched_at' => now()->setTimezone('Asia/Kolkata')->format('h:i A'),
            'coins'      => [],
            'error'      => 'Market data temporarily unavailable',
        ];
    }
}
