<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpaceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('spaces', function(Blueprint $table)
		{
            $table->increments('id');
            $table->timestamps();
            $table->string('code',50)->unique();
            $table->string('title',100);
            $table->text('description')->nullable();
            $table->boolean('active')->default(1);
            $table->string('access',2);
            $table->text('options');
            $table->integer('user_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');

        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('space');

	}

}
