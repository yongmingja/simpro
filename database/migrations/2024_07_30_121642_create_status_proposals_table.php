<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_proposals', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_proposal');
            $table->foreign('id_proposal')->references('id')->on('proposals')->onDelete('cascade');
            $table->integer('status_approval');
            $table->string('generate_qrcode')->nullable();
            $table->string('keterangan_ditolak')->nullable();
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
        Schema::dropIfExists('status_proposals');
    }
}
