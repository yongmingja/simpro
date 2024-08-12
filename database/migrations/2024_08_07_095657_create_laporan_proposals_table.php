<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaporanProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laporan_proposals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_proposal');
            $table->text('hasil_kegiatan');
            $table->text('evaluasi_catatan_kegiatan');
            $table->text('penutup');
            $table->integer('status_laporan')->default(0);
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
        Schema::dropIfExists('laporan_proposals');
    }
}
