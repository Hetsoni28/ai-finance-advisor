<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_cache', function (Blueprint $table) {
            $table->id();
            $table->string('cache_key', 100)->unique();
            $table->json('data');
            $table->string('source', 50); // coingecko, alphavantage
            $table->timestamp('fetched_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_cache');
    }
};
