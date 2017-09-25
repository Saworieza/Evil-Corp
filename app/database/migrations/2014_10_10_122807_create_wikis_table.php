<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWikisTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wikis', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
            $table->integer('space_id')->unsigned();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->text('body');
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->string('access',2);

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
		Schema::drop('wikis');
	}

}
