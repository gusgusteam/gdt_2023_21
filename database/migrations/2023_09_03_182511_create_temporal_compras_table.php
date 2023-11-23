<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemporalComprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temporal_compras', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->integer('id_producto');
            $table->integer('id_almacen');
            $table->integer('id_usuario')->nullable();
            $table->integer('cantidad');
            $table->float('precio_compra',8,2);
            $table->float('sub_total',8,2);
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
        Schema::dropIfExists('temporal_compras');
    }
}
