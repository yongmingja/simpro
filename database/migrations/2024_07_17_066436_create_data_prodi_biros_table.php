<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataProdiBirosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_prodi_biros', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_fakultas_biro');
            $table->foreign('id_fakultas_biro')->references('id')->on('data_fakultas_biros')->onDelete('restrict');
            $table->string('nama_prodi_biro');
            $table->string('kode_prodi_biro',4)->nullable();
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
        Schema::dropIfExists('data_prodi_biros');
    }
}
