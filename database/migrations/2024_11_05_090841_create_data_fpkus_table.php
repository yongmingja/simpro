<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataFpkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_fpkus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no_surat_undangan');
            $table->string('undangan_dari');
            $table->string('nama_kegiatan');
            $table->date('tgl_kegiatan');
            $table->string('peserta_kegiatan');
            $table->string('catatan')->nullable();
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
        Schema::dropIfExists('data_fpkus');
    }
}
