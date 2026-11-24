<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Monthly spending limit per expense category. alert_level / alert_month remember which
 * alert (80% or 100%) was already sent this month, so each one is sent only once.
 */
class CreateBudgetsTable extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('category', 50);
            $table->decimal('monthly_limit', 12, 2);
            $table->unsignedTinyInteger('alert_level')->default(0);
            $table->char('alert_month', 7)->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
}
