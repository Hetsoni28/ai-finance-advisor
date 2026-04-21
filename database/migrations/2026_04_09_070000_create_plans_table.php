<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');                // Free, Pro, Premium
            $table->string('slug')->unique();      // free, pro, premium
            $table->decimal('price', 10, 2)->default(0.00);
            $table->json('features');              // JSON array of feature strings
            $table->integer('duration_days')->default(30);
            $table->string('badge_color')->default('slate');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed 3 plans
        DB::table('plans')->insert([
            [
                'name'          => 'Starter',
                'slug'          => 'free',
                'price'         => 0.00,
                'features'      => json_encode([
                    '100 DB Syncs / minute',
                    'Manual Ledger Audits',
                    'Basic Dashboard Analytics',
                    'Single User Workspace',
                ]),
                'duration_days' => 0, // 0 = forever
                'badge_color'   => 'slate',
                'sort_order'    => 1,
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'Pro Advisor',
                'slug'          => 'pro',
                'price'         => 199.00,
                'features'      => json_encode([
                    'Everything in Starter',
                    'AI Predictive Forecasting',
                    'Family Collaboration (5 Nodes)',
                    'Automated PDF Intel Reports',
                    '10,000 API Calls / minute',
                    'Priority Support',
                ]),
                'duration_days' => 30,
                'badge_color'   => 'indigo',
                'sort_order'    => 2,
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'Enterprise',
                'slug'          => 'premium',
                'price'         => 499.00,
                'features'      => json_encode([
                    'Everything in Pro Advisor',
                    'Dedicated Database Instance',
                    'RESTful API Access Key',
                    'Unlimited API Calls',
                    'Custom AI Model Training',
                    'White-Glove Onboarding',
                ]),
                'duration_days' => 30,
                'badge_color'   => 'amber',
                'sort_order'    => 3,
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
