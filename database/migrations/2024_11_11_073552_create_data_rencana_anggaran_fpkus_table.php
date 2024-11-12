<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataRencanaAnggaranFpkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_rencana_anggaran_fpkus', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_fpku');
            $table->foreign('id_fpku')->references('id')->on('data_fpkus')->onDelete('cascade');
            $table->string('item')->nullable();
            $table->bigInteger('biaya_satuan')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('frequency')->nullable();
            $table->integer('sumber_dana')->nullable();
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
        Schema::dropIfExists('data_rencana_anggaran_fpkus');
    }
}
