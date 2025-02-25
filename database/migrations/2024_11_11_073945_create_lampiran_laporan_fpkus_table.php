<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLampiranLaporanFpkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lampiran_laporan_fpkus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_fpku');
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
        Schema::dropIfExists('lampiran_laporan_fpkus');
    }
}
