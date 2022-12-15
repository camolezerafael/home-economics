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
        Schema::create('accounts', function (Blueprint $table) {
            $table->comment('table of accounts: wallet, bank, credit cards, cryptos');
            $table->integer('id', true);
            $table->integer('user_id')->index('account_user_fk');
            $table->string('name', 60);
            $table->string('description', 60);
            $table->bigInteger('initial_balance')->default(0);
            $table->integer('decimal_precision');
            $table->integer('type_id')->index('account_type_fk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
};
