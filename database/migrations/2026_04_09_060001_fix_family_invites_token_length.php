<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('family_invites', function (Blueprint $table) {
            // HMAC-SHA256 produces exactly 64 hex chars.
            // Expanding to 128 for safety margin and future flexibility.
            $table->string('token', 128)->change();
        });
    }

    public function down(): void
    {
        Schema::table('family_invites', function (Blueprint $table) {
            $table->string('token', 64)->change();
        });
    }
};
