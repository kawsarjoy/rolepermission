<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $appV = (float)substr(App::VERSION(),0,3);
        if($appV < 5.2){
            Schema::create('permissions', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->string('description');
                $table->integer('parent_id')->unsigned();
                $table->integer('order');
                $table->foreign('parent_id')->references('id')->on('permissions');
                $table->timestamps();
            });
        }else{
            Schema::create('permissions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name')->unique();
                $table->string('description');
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->integer('order');
                $table->foreign('parent_id')->references('id')->on('permissions');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}
