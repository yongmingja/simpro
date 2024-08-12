<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataProdisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_prodis', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_fakultas');
            $table->foreign('id_fakultas')->references('id')->on('data_fakultas')->onDelete('restrict');
            $table->string('nama_prodi');
            $table->string('kode_prodi',4)->nullable();
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
        Schema::dropIfExists('data_prodis');
    }
}
