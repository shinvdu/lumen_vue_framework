<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AuthAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth_accounts', function (Blueprint $table) {
            $table->string('app_id')->primary();
            $table->text('app_name');
            $table->text('app_screct');
            $table->bigInteger('expeire');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('auth_accounts');
    }
}
