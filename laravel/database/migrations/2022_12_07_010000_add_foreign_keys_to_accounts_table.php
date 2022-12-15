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
        Schema::table('accounts', function (Blueprint $table) {
            $table->foreign(['type_id'], 'account_type_fk')->references(['id'])->on('account_types')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['user_id'], 'account_user_fk')->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign('account_type_fk');
            $table->dropForeign('account_user_fk');
        });
    }
};
