<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_tahun_akademik');
            $table->foreign('id_tahun_akademik')->references('id')->on('tahun_akademiks')->onDelete('restrict');
            $table->integer('id_jenis_kegiatan');
            $table->integer('id_form_rkat')->nullable();
            $table->string('user_id');
            $table->integer('id_fakultas_biro')->nullable();
            $table->integer('id_prodi_biro')->nullable();
            $table->string('nama_kegiatan');
            $table->string('tgl_event');
            $table->string('lokasi_tempat')->nullable();
            $table->text('pendahuluan');
            $table->text('tujuan_manfaat');
            $table->text('peserta');
            $table->text('detil_kegiatan');
            $table->text('penutup');
            $table->integer('validasi')->default(0);
            $table->integer('is_archived')->default(0);
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
        Schema::dropIfExists('proposals');
    }
}
