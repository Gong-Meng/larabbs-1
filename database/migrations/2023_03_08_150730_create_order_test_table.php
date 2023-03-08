<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_test', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 255)->default('')->comment('类型');
            $table->string('value', 255)->default('')->comment('值');
            $table->string('name', 255)->default('')->comment('字段名称');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_test');
    }
}
