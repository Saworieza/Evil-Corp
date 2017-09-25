<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFtindexTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ftindex', function(Blueprint $table)
		{
            $table->engine = 'MYISAM';
			$table->increments('id');
			$table->integer('indexable_id')->unsigned();
			$table->string('indexable_type',100);
            $table->text('body');
        });

        DB::statement('ALTER TABLE `ftindex` ADD FULLTEXT search(body)');

        DB::statement(DB::raw("INSERT INTO ftindex (indexable_type, indexable_id, body) select 'Content', id, content_text from content"));

        DB::statement('ALTER TABLE `users` MODIFY `first_name` varchar(100) NULL;');
        DB::statement('ALTER TABLE `users` MODIFY `last_name` varchar(100) NULL;');
        DB::statement('ALTER TABLE `users` MODIFY `activation_code` varchar(100) NULL;');
        DB::statement('ALTER TABLE `wikis` MODIFY `summary` text NULL;');

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ftindex');
	}

}
