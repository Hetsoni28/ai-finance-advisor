<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedByToFamilyInvitesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('family_invites', function (Blueprint $table) {

            // ✅ Add column
            if (!Schema::hasColumn('family_invites', 'created_by')) {

                $table->unsignedBigInteger('created_by')
                    ->nullable()
                    ->after('email');

                // ✅ Foreign key (safe)
                $table->foreign('created_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('family_invites', function (Blueprint $table) {

            if (Schema::hasColumn('family_invites', 'created_by')) {

                // Drop foreign key safely
                $table->dropForeign(['created_by']);

                // Drop column
                $table->dropColumn('created_by');
            }
        });
    }
}