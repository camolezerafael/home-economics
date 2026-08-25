<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account_types', function (Blueprint $table) {
            if (! Schema::hasColumn('account_types', 'created_at')) {
                $table->timestamps();
            }
        });

        $this->recreateForeignKey('account_types', 'user_id', 'users', 'CASCADE');
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

        DB::statement(
            "ALTER TABLE `{$table}` ADD CONSTRAINT `{$table}_{$column}_fk`
             FOREIGN KEY (`{$column}`) REFERENCES `{$referenced}`(`id`) ON DELETE {$onDelete}"
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
