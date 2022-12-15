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
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign(['account_id'], 'transaction_account_fk')->references(['id'])->on('accounts')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['category_id'], 'transaction_category_fk')->references(['id'])->on('categories')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['from_id'], 'transaction_from_fk')->references(['id'])->on('from_to')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['payment_type_id'], 'transaction_payment_type_fk')->references(['id'])->on('payment_types')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['to_id'], 'transaction_to_fk')->references(['id'])->on('from_to')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['user_id'], 'transaction_user_fk')->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign('transaction_account_fk');
            $table->dropForeign('transaction_category_fk');
            $table->dropForeign('transaction_from_fk');
            $table->dropForeign('transaction_payment_type_fk');
            $table->dropForeign('transaction_to_fk');
            $table->dropForeign('transaction_user_fk');
        });
    }
};
