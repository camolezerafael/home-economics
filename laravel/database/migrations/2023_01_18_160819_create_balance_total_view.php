<?php

use Illuminate\Database\Migrations\Migration;

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
			CREATE VIEW balance_total
			AS
			SELECT
				user_id,
				account_id,
				from_to_id,
				category_id,
				payment_type_id,
				value,
				status

			FROM transactions
			WHERE transaction_type NOT IN ('RECEI', 'TRANS');
		");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('
			DROP VIEW balance_total;
		');
	}
};
