<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('content', function(Blueprint $table)
		{
            $table->increments('id');
            $table->timestamps();
            $table->integer('space_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->text('content_text');
            $table->integer('class_id')->unsigned();
            $table->longText('content_data');
            $table->foreign('space_id')->references('id')->on('spaces')->onDelete('cascade');
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
		Schema::drop('content');
	}

}
