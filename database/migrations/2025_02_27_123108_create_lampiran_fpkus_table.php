<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLampiranFpkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lampiran_fpkus', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_fpku');
            $table->foreign('id_fpku')->references('id')->on('data_fpkus')->onDelete('cascade');
            $table->string('nama_berkas')->nullable();
            $table->string('berkas')->nullable();
            $table->string('link_gdrive')->nullable();
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
        Schema::dropIfExists('lampiran_fpkus');
    }
}
