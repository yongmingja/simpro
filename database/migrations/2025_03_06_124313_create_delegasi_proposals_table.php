<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDelegasiProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delegasi_proposals', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_proposal');
            $table->foreign('id_proposal')->references('id')->on('proposals')->onDelete('cascade');
            $table->text('catatan_delegator')->nullable();
            $table->string('delegasi')->nullable();
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
        Schema::dropIfExists('delegasi_proposals');
    }
}
