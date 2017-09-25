<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpaceUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('space_user', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('space_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->smallInteger('role')->default(0);
            $table->dateTime('last_visit')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('space_id')->references('id')->on('spaces')->onDelete('cascade');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('space_user');
	}

}
