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
            $table->integer('id_jenis_kegiatan');
            $table->integer('user_id');
            $table->integer('id_fakultas');
            $table->integer('id_prodi');
            $table->text('pendahuluan');
            $table->text('tujuan_manfaat');
            $table->text('peserta');
            $table->text('detil_kegiatan');
            $table->text('penutup');
            $table->integer('validasi')->default(0);
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
