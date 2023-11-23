<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfiguracionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuracions', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('nombre2');
            $table->string('direccion')->nullable();
            $table->integer('telefono');
            $table->tinyInteger('tabla')->default(1);
            $table->string('correo')->nullable();
            $table->string('nic')->nullable()->default('----------');
            $table->string('leyenda')->default("gracias");
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
        Schema::dropIfExists('configuracions');
    }
}
