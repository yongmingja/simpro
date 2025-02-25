<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFpkuKeperluansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fpku_keperluans', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_fpku');
            $table->foreign('id_fpku')->references('id')->on('data_fpkus')->onDelete('cascade');
            $table->string('isi_keperluan')->nullable();
            $table->string('link_gdrive')->nullable();
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
        Schema::dropIfExists('fpku_keperluans');
    }
}
