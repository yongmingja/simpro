<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataPengajuanSarprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_pengajuan_sarpras', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_proposal');
            $table->foreign('id_proposal')->references('id')->on('proposals')->onDelete('cascade');
            $table->date('tgl_kegiatan')->nullable();
            $table->text('sarpras_item')->nullable();
            $table->integer('jumlah')->nullable();
            $table->integer('sumber_dana');
            $table->integer('status')->nullable();
            $table->text('keterangan')->nullable();
            $table->text('alasan')->nullable();
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
        Schema::dropIfExists('data_pengajuan_sarpras');
    }
}
