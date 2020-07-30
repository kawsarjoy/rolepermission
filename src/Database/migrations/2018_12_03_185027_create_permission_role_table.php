<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(\DB::getSchemaBuilder()->getColumnType('users', 'id') == 'integer'){
            Schema::create('permission_role', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('permission_id')->unsigned();
                $table->integer('role_id')->unsigned();
            });
        }else{
            Schema::create('permission_role', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('permission_id')->unsigned();
                $table->bigInteger('role_id')->unsigned();
            });
        }

        Schema::table('permission_role', function(Blueprint $table){
            $table->foreign('permission_id')
                  ->references('id')->on('permissions')
                  ->onDelete('cascade');
        });

        Schema::table('permission_role', function(Blueprint $table){
            $table->foreign('role_id')
                  ->references('id')->on('roles')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permission_role');
    }
}
