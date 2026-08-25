<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->restrictOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('from_to_id')->nullable()->constrained('from_to')->nullOnDelete();
            $table->foreignId('payment_type_id')->constrained()->cascadeOnDelete();
            $table->char('transaction_type', 5); // RECEI, FIXEX, VAREX, PEOPL, TAXES, TRANS
            $table->string('description', 30);
            $table->bigInteger('amount'); // stored as value * 10^precision
            $table->boolean('status')->default(false); // 0 = a pagar, 1 = pago
            $table->timestamp('date_due');
            $table->timestamp('date_payment')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'account_id']);
            $table->index(['user_id', 'transaction_type']);
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'date_due']);
            $table->index(['user_id', 'date_payment']);
            $table->index(['user_id', 'category_id']);
            $table->index(['user_id', 'from_to_id']);
            $table->index(['user_id', 'payment_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
