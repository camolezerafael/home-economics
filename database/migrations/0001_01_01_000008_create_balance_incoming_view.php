<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS balance_incoming');
        DB::statement("
            CREATE VIEW balance_incoming AS
            SELECT
                id,
                user_id,
                account_id,
                category_id,
                from_to_id,
                payment_type_id,
                transaction_type,
                description,
                amount,
                status,
                date_due,
                date_payment,
                created_at,
                updated_at
            FROM transactions
            WHERE transaction_type = 'RECEI'
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS balance_incoming');
    }
};
