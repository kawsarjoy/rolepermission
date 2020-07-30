<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(\DB::getSchemaBuilder()->getColumnType('users', 'id') == 'integer'){
            Schema::create('roles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 50)->unique();
                $table->string('description', 200);
                $table->timestamps();
            });
        }else{
            Schema::create('roles', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name', 50)->unique();
                $table->string('description', 200);
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
        Schema::dropIfExists('roles');
    }
}
