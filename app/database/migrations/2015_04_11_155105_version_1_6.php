<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Version16 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('folders', function($table)
        {
            $table->integer('parent_id')->nullable();
        });

        Schema::table('content', function($table)
        {
            $table->integer('shared_from_id')->nullable();
        });

        Schema::table('users', function($table)
        {
            $table->smallInteger('notif_post')->default(0);
            $table->smallInteger('email_post')->default(0);
        });

        DB::statement(DB::raw("INSERT INTO ftindex (indexable_type, indexable_id, body) select 'Wiki', id, body from wikis"));

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
