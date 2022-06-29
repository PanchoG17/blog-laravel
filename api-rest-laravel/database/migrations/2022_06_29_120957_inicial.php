<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Inicial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('categorias', function (Blueprint $table){

            $table->id();
            $table->timestamps();

        });

        Schema::create('posts', function(Blueprint $table){

            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();

        });

        Schema::table('users', function (Blueprint $table){

            $table->string('surname');
            $table->string('role');
            $table->string('description')->nullable();

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
