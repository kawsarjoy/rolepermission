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
        $table_prefix = config('permissions-config.table-prefix');
        if(\DB::getSchemaBuilder()->getColumnType('users', 'id') == 'integer'){
            Schema::create($table_prefix.'permissions', function (Blueprint $table) use($table_prefix){
                $table->increments('id');
                $table->string('name')->unique();
                $table->string('description');
                $table->integer('parent_id')->unsigned();
                $table->integer('order');
                $table->foreign('parent_id')->references('id')->on($table_prefix.'permissions');
                $table->timestamps();
            });
        }else{
            Schema::create($table_prefix.'permissions', function (Blueprint $table) use($table_prefix){
                $table->bigIncrements('id');
                $table->string('name')->unique();
                $table->string('description');
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->integer('order');
                $table->foreign('parent_id')->references('id')->on($table_prefix.'permissions');
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
        $table_prefix = config('permissions-config.table-prefix');
        Schema::dropIfExists($table_prefix.'permissions');
    }
}
