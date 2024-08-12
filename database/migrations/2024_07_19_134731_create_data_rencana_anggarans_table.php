<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataRencanaAnggaransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_rencana_anggarans', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_proposal');
            $table->foreign('id_proposal')->references('id')->on('proposals')->onDelete('cascade');
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
        Schema::dropIfExists('data_rencana_anggarans');
    }
}
