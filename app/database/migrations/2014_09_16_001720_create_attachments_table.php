<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attachments', function(Blueprint $table)
		{
			$table->increments('id');
            $table->timestamps();
            $table->string('file_name',255);
            $table->string('code',35);
            $table->integer('file_size')->unsigned();
            $table->dateTime('file_date');
            $table->string('file_ext',10)->nullable();
            $table->string('attachable_type',20);
            $table->integer('attachable_id');
            $table->string('storage',20)->nullable();
            $table->integer('user_id')->unsigned();
            $table->text('description')->nullable();
            $table->boolean('status')->default(0);

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
		Schema::table('attachments', function(Blueprint $table)
		{
			//
		});
	}

}
