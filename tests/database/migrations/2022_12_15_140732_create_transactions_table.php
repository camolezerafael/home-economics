<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('user_id');
            $table->integer('account_id');
            $table->string('transaction_type', 5);
            $table->string('description', 30);
            $table->integer('from_id')->nullable();
            $table->integer('to_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->integer('payment_type_id');
            $table->bigInteger('amount')->default(0);
            $table->boolean('status')->default(0);
            $table->timestamp('date_due')->nullable();
            $table->timestamp('date_payment')->nullable();

            $table->foreign('account_id', 'transaction_account_fk')->references('id')->on('accounts');
            $table->foreign('category_id', 'transaction_category_fk')->references('id')->on('categories');
            $table->foreign('from_id', 'transaction_from_fk')->references('id')->on('from_to');
            $table->foreign('payment_type_id', 'transaction_payment_type_fk')->references('id')->on('payment_types');
            $table->foreign('to_id', 'transaction_to_fk')->references('id')->on('from_to');
            $table->foreign('user_id', 'transaction_user_fk')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
