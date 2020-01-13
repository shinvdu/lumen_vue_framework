<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCache extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('cache', function (Blueprint $table) {
            $table->string('cid')->primary();
            $table->text('value');
            $table->bigInteger('expeire');
            $table->tinyInteger('is_serialized');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('cache');
    }
}
