<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemporalVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temporal_ventas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->integer('id_producto');
            $table->integer('id_almacen');
            $table->integer('id_usuario')->nullable();
            $table->integer('cantidad')->default(0);
            $table->float('precio_venta',8,2)->default(0);
            $table->float('sub_total',8,2)->default(0);
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
        Schema::dropIfExists('temporal_ventas');
    }
}
