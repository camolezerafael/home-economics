<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->foreign(['from_account_id'], 'transfer_account_from_fk')->references(['id'])->on('accounts')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['to_account_id'], 'transfer_account_to_fk')->references(['id'])->on('accounts')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropForeign('transfer_account_from_fk');
            $table->dropForeign('transfer_account_to_fk');
        });
    }
};
