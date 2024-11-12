<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataRealisasiAnggaranFpkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_realisasi_anggaran_fpkus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_fpku');
            $table->string('item')->nullable();
            $table->bigInteger('biaya_satuan')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('frequency')->nullable();
            $table->integer('sumber_dana')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('data_realisasi_anggaran_fpkus');
    }
}
