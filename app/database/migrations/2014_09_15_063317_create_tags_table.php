<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tags', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('space_id')->unsigned();
            $table->string('tag',100);
            $table->integer('counter')->default(0);

            $table->foreign('space_id')->references('id')->on('spaces')->onDelete('cascade');
            $table->unique(array('tag', 'space_id'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tags');
	}

}
