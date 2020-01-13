<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WechatTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_templates', function (Blueprint $table) {
            $table->string('template_id')->primary();
            $table->string('template_name');
            $table->text('template_body');
            $table->bigInteger('expire');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('wechat_templates');
    }
}
