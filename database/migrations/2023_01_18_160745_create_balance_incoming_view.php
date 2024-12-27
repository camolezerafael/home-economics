<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		DB::statement("
			CREATE OR REPLACE VIEW balance_incoming
			AS
			SELECT
				user_id,
				account_id,
				from_to_id,
				category_id,
				payment_type_id,
				amount,
				status,
				date_due,
				transaction_type

			FROM transactions
			WHERE transaction_type = 'RECEI';
		");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		DB::statement("
			DROP VIEW balance_receipts;
		");
    }
};
