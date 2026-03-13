<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RestructureIncomesTable extends Migration
{
    public function up()
    {
        Schema::table('incomes', function (Blueprint $table) {

            // Add income_date if not exists
            if (!Schema::hasColumn('incomes', 'income_date')) {
                $table->date('income_date')->after('source');
            }

        });

        // Optional: If you want to remove month & year
        Schema::table('incomes', function (Blueprint $table) {

            if (Schema::hasColumn('incomes', 'month')) {
                $table->dropColumn('month');
            }

            if (Schema::hasColumn('incomes', 'year')) {
                $table->dropColumn('year');
            }

        });
    }

    public function down()
    {
        Schema::table('incomes', function (Blueprint $table) {

            if (!Schema::hasColumn('incomes', 'month')) {
                $table->tinyInteger('month')->nullable();
            }

            if (!Schema::hasColumn('incomes', 'year')) {
                $table->smallInteger('year')->nullable();
            }

            if (Schema::hasColumn('incomes', 'income_date')) {
                $table->dropColumn('income_date');
            }

        });
    }
}