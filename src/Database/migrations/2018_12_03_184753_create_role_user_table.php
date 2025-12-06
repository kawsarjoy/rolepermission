<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_prefix = config('permissions-config.table-prefix');
        if(\DB::getSchemaBuilder()->getColumnType('users', 'id') == 'integer'){
            Schema::create($table_prefix.'role_user', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('role_id')->unsigned();
                $table->integer('user_id')->unsigned();
            });
        }else{
            Schema::create($table_prefix.'role_user', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('role_id')->unsigned();
                $table->bigInteger('user_id')->unsigned();
            });
        }

        Schema::table($table_prefix.'role_user', function(Blueprint $table) use($table_prefix){
            $table->foreign('role_id')
                  ->references('id')->on($table_prefix.'roles')
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
        $table_prefix = config('permissions-config.table-prefix');
        Schema::dropIfExists($table_prefix.'role_user');
    }
}
