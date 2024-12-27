<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('from_account_id');
            $table->integer('to_account_id');
            $table->bigInteger('value');
            
            $table->foreign('from_account_id', 'transfer_account_from_fk')->references('id')->on('accounts');
            $table->foreign('to_account_id', 'transfer_account_to_fk')->references('id')->on('accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfers');
    }
}
