<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_financial_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->decimal('monthly_income_estimate', 12, 2)->nullable();
            $table->enum('risk_tolerance', ['conservative', 'moderate', 'aggressive'])->default('moderate');
            $table->enum('investment_experience', ['none', 'beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->enum('age_group', ['18-25', '26-35', '36-45', '46-55', '55+'])->default('26-35');
            $table->json('financial_priorities')->nullable(); // ['savings', 'investment', 'debt_reduction']
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_financial_profiles');
    }
};
