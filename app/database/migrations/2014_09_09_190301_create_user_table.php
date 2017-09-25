<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
            $table->timestamps();
            $table->string('first_name', 100)->nullable();;
            $table->string('last_name', 100)->nullable();;
            $table->string('full_name', 100);
            $table->string('code', 50);
            $table->string('email', 100)->unique();
            $table->string('password', 100);
            $table->boolean('state')->default(1);
            $table->boolean('admin')->default(0);
            $table->text('status')->nullable();
            $table->string('organization',100)->nullable();
            $table->string('position',100)->nullable();
            $table->text('skills')->nullable();
            $table->boolean('showemail')->default(0);
            $table->text('interests')->nullable();
            $table->text('workingon')->nullable();
            $table->string('phone',100)->nullable();
            $table->string('skype',100)->nullable();
            $table->string('facebook',100)->nullable();
            $table->string('twitter',100)->nullable();
            $table->string('googleplus',100)->nullable();
            $table->string('github',100)->nullable();
            $table->string('linkedin',100)->nullable();
            $table->dateTime('last_login')->nullable();
            $table->longText('favorites')->nullable();
            $table->string('activation_code',100)->nullable();
            $table->date('activation_expire')->nullable();
            $table->date('activation_date')->nullable();
            $table->boolean('create_spaces')->default(0);
            $table->string('remember_token',100)->nullable();


            $table->boolean('notif_content')->default(0);
            $table->boolean('notif_like')->default(0);
            $table->boolean('notif_content_starred')->default(0);
            $table->boolean('notif_comment')->default(0);
            $table->boolean('notif_task')->default(0);
            $table->boolean('notif_mention')->default(0);
            $table->boolean('notif_invite')->default(0);
            $table->boolean('email_content')->default(0);
            $table->boolean('email_like')->default(0);
            $table->boolean('email_content_starred')->default(0);
            $table->boolean('email_task')->default(0);
            $table->boolean('email_mention')->default(0);
            $table->boolean('email_invite')->default(0);
            $table->boolean('email_private_msg')->default(0);

        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('user');
	}

}
