<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsPersonalToIncomesAndExpensesTables extends Migration
{
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->boolean('is_personal')->default(true)->after('family_id');
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->boolean('is_personal')->default(true)->after('family_id');
        });
    }

    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('is_personal');
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->dropColumn('is_personal');
        });
    }
}