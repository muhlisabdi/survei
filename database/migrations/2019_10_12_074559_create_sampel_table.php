<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSampelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sampel', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama')->nullable();
            $table->integer('layanan_id');
            $table->date('tanggal');
            $table->tinyInteger('jam_id');
            $table->tinyInteger('jk_id');
            $table->tinyInteger('pendidikan_id');
            $table->tinyInteger('pekerjaan_id');
            $table->tinyInteger('umur');
            $table->tinyInteger('u1');
            $table->tinyInteger('u2');
            $table->tinyInteger('u3');
            $table->tinyInteger('u4');
            $table->tinyInteger('u5');
            $table->tinyInteger('u6');
            $table->tinyInteger('u7');
            $table->tinyInteger('u8');
            $table->tinyInteger('u9');
            $table->text('saran')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sampel');
    }
}
