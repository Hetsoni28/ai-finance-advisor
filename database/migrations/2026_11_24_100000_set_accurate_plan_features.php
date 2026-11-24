<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The original plan rows listed features the app does not have (API keys, dedicated databases,
 * "10,000 API calls / minute", custom model training). Replace them with what each plan really
 * gives, matching the limits in config/financeai.php and the hasPlan('pro') checks in AiChatController.
 */
class SetAccuratePlanFeatures extends Migration
{
    private const FEATURES = [
        'free' => [
            'Income & expense tracking',
            'Monthly reports & health score',
            'Family budgets & invites',
            'PDF exports',
            'AI advisor — 20 messages a day',
        ],
        'pro' => [
            'Everything in Starter',
            'AI advisor — 200 messages a day',
            'Live crypto & gold prices in the AI advisor',
        ],
        'premium' => [
            'Everything in Pro Advisor',
            'AI advisor — 1,000 messages a day',
        ],
    ];

    public function up(): void
    {
        foreach (self::FEATURES as $slug => $features) {
            DB::table('plans')->where('slug', $slug)->update([
                'features'   => json_encode($features, JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Not restoring the previous lists: they described features that don't exist.
    }
}
