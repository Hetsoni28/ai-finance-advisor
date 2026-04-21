<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationFieldsToContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {

            // Geolocation Data (captured via browser Geolocation API)
            $table->decimal('latitude', 10, 8)->nullable()->after('user_agent');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('location_label')->nullable()->after('longitude');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'location_label']);
        });
    }
}
