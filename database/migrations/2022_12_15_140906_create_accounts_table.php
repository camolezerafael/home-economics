<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 60);
            $table->string('description', 60);
            $table->bigInteger('initial_balance')->default(0);
            $table->integer('decimal_precision');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('type_id');

            $table->foreign('user_id', 'account_user_fk')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('type_id', 'account_type_fk')->references('id')->on('account_types')->onDelete('cascade');
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
}
