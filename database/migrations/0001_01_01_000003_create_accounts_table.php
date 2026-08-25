<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('type_id')->constrained('account_types')->cascadeOnDelete();
            $table->string('name', 60);
            $table->string('description', 255)->nullable();
            $table->bigInteger('initial_balance')->default(0);
            $table->unsignedSmallInteger('decimal_precision')->default(2);
            $table->timestamps();

            $table->index(['user_id', 'type_id']);
            $table->index(['user_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
