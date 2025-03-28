<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaporanFpkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laporan_fpkus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_fpku');
            $table->string('nama_kegiatan')->nullable();
            $table->date('tgl_kegiatan')->nullable();
            $table->integer('id_fakultas_biro')->nullable();
            $table->integer('id_prodi_biro')->nullable();
            $table->string('lokasi_tempat')->nullable();
            $table->text('pendahuluan');
            $table->text('tujuan_manfaat');
            $table->text('peserta');
            $table->text('detil_kegiatan');
            $table->text('hasil_kegiatan');
            $table->text('evaluasi_catatan_kegiatan');
            $table->text('penutup');
            $table->integer('dibuat_oleh')->nullable();
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
        Schema::dropIfExists('laporan_fpkus');
    }
}
