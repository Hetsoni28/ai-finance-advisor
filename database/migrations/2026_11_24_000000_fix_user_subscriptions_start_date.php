<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * user_subscriptions.start_date was created as the first TIMESTAMP column, so MySQL/MariaDB
 * gave it "ON UPDATE CURRENT_TIMESTAMP". Any update to a row (e.g. marking it expired)
 * silently overwrote the real start date. This removes the auto-update and repairs rows
 * whose start date ended up after their end date by restoring it from created_at.
 */
class FixUserSubscriptionsStartDate extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE user_subscriptions MODIFY start_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');

        DB::table('user_subscriptions')
            ->whereNotNull('end_date')
            ->whereColumn('start_date', '>', 'end_date')
            ->update(['start_date' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        // Intentionally not restoring the auto-update behaviour: it corrupts data.
    }
}
