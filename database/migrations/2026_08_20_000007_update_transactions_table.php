<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('transactions', 'created_at')) {
                $table->timestamps();
            }
        });

        // Semântica nova: dados financeiros referenciados bloqueiam exclusão (guard 409 no CrudController)
        $this->recreateForeignKey('transactions', 'user_id', 'users', 'CASCADE');
        $this->recreateForeignKey('transactions', 'account_id', 'accounts', 'RESTRICT');
        $this->recreateForeignKey('transactions', 'category_id', 'categories', 'RESTRICT');
        $this->recreateForeignKey('transactions', 'from_to_id', 'from_to', 'SET NULL');
        $this->recreateForeignKey('transactions', 'payment_type_id', 'payment_types', 'CASCADE');
    }

    public function down(): void
    {
        //
    }

    private function recreateForeignKey(string $table, string $column, string $referenced, string $onDelete): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $schema = DB::connection()->getDatabaseName();

        foreach ($this->foreignKeyNames($table, $column, $schema) as $name) {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$name}`");
        }

        $action = $onDelete === 'SET NULL' ? 'ON DELETE SET NULL' : "ON DELETE {$onDelete}";

        DB::statement(
            "ALTER TABLE `{$table}` ADD CONSTRAINT `{$table}_{$column}_fk`
             FOREIGN KEY (`{$column}`) REFERENCES `{$referenced}`(`id`) {$action}"
        );
    }

    private function foreignKeyNames(string $table, string $column, string $schema): array
    {
        return array_map(
            fn ($row) => $row->CONSTRAINT_NAME,
            DB::select(
                'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?
                   AND REFERENCED_TABLE_NAME IS NOT NULL',
                [$schema, $table, $column]
            )
        );
    }
};
