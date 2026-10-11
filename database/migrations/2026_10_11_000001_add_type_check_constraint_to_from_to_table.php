<?php

use App\Models\FromTo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const CONSTRAINT = 'from_to_type_check';

    /**
     * SQLite (usado nos testes) não suporta ADD CONSTRAINT; lá a regra é garantida pelo FromToRequest.
     */
    public function up(): void
    {
        if (! $this->supportsCheckConstraints()) {
            return;
        }

        $invalid = DB::table('from_to')->whereNotIn('type', FromTo::TYPES)->distinct()->pluck('type')->all();

        if ($invalid !== []) {
            throw new RuntimeException(
                'from_to.type contém valores fora de '.implode('/', FromTo::TYPES).': '.implode(', ', $invalid)
                .'. Corrija os dados antes de aplicar a constraint.'
            );
        }

        $types = "'".implode("','", FromTo::TYPES)."'";

        DB::statement('ALTER TABLE from_to ADD CONSTRAINT '.self::CONSTRAINT." CHECK (type IN ({$types}))");
    }

    public function down(): void
    {
        if (! $this->supportsCheckConstraints()) {
            return;
        }

        DB::statement('ALTER TABLE from_to DROP CONSTRAINT '.self::CONSTRAINT);
    }

    private function supportsCheckConstraints(): bool
    {
        return in_array(DB::getDriverName(), ['mysql', 'mariadb', 'pgsql'], true);
    }
};
