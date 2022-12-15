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
        Schema::create('transactions', function (Blueprint $table) {
            $table->comment('');
            $table->integer('id', true);
            $table->integer('user_id')->index('transaction_user_fk_idx');
            $table->integer('account_id')->index('transaction_account_fk_idx');
            $table->string('transaction_type', 5);
            $table->string('description', 30);
            $table->integer('from_id')->nullable()->index('transaction_from_fk_idx');
            $table->integer('to_id')->nullable()->index('transaction_to_fk_idx');
            $table->integer('category_id')->nullable()->index('transaction_category_fk_idx');
            $table->integer('payment_type_id')->index('transaction_payment_type_fk_idx');
            $table->bigInteger('value')->default(0);
            $table->boolean('status')->default(false);
            $table->timestamp('date_due')->nullable();
            $table->timestamp('date_payment')->nullable();
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
};
