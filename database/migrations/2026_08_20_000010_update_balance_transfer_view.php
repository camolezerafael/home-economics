<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Somente a perna de crédito da transferência: o saldo total não pode
        // contabilizar o débito correspondente (é movimento interno).
        DB::statement('DROP VIEW IF EXISTS balance_transfer');
        DB::statement("
            CREATE VIEW balance_transfer AS
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
            WHERE transaction_type = 'TRANS'
                AND amount > 0
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS balance_transfer');
    }
};
