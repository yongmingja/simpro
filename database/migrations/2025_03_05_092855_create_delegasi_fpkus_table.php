<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDelegasiFpkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delegasi_fpkus', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_fpku');
            $table->foreign('id_fpku')->references('id')->on('data_fpkus')->onDelete('cascade');
            $table->text('catatan_delegator')->nullable();
            $table->string('delegasi')->nullable();
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
        Schema::dropIfExists('delegasi_fpkus');
    }
}
