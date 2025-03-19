<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormRkatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_rkats', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_tahun_akademik');
            $table->foreign('id_tahun_akademik')->references('id')->on('tahun_akademiks')->onDelete('restrict');
            $table->text('sasaran_strategi');
            $table->text('program_strategis');
            $table->text('program_kerja');
            $table->string('kode_renstra');
            $table->text('nama_kegiatan');
            $table->integer('penanggung_jawab')->nullable();
            $table->string('kode_pagu');
            $table->bigInteger('total')->nullable()->default(0);
            $table->integer('status_validasi')->default(0);
            $table->integer('validated_by')->nullable();
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
        Schema::dropIfExists('form_rkats');
    }
}
