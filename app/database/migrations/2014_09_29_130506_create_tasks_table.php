<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tasks', function(Blueprint $table)
		{
			$table->increments("id");
            $table->timestamps();
            $table->integer('content_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned();
            $table->string('title',50);
            $table->text('description');
            $table->smallInteger('state')->default(0);
            $table->boolean('archived')->default(0);
            $table->smallInteger('priority')->default(0);
            $table->integer('space_id')->unsigned()->nullable();
            $table->text('history');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('content_id')->references('id')->on('content')->onDelete('cascade');
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
		Schema::drop('tasks');
	}

}
