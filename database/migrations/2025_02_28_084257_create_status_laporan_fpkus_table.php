<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusLaporanFpkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_laporan_fpkus', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_laporan_fpku');
            $table->foreign('id_laporan_fpku')->references('id')->on('laporan_fpkus')->onDelete('cascade');
            $table->integer('status_approval')->default(0);
            $table->string('generate_qrcode')->nullable();
            $table->text('keterangan_ditolak')->nullable();
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
        Schema::dropIfExists('status_laporan_fpkus');
    }
}
