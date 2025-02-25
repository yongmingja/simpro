<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusFpkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_fpkus', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_fpku');
            $table->foreign('id_fpku')->references('id')->on('data_fpkus')->onDelete('cascade');
            $table->integer('status_approval');
            $table->integer('broadcast_email')->nullable();
            $table->string('generate_qrcode')->nullable();
            $table->string('keterangan')->nullable();
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
        Schema::dropIfExists('status_fpkus');
    }
}
