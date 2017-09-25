<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeWikiType extends Migration {

    public function up()
    {
        DB::statement('ALTER TABLE wikis MODIFY COLUMN body LONGTEXT');
    }


}
