<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFromToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('from_to', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 30);
            $table->string('type', 3);
			$table->unsignedInteger('user_id');

			$table->foreign('user_id', 'from_to_user_fk')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('from_to');
    }
}
