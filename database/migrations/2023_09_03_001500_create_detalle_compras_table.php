<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleComprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_compras', function (Blueprint $table) {
            $table->id();
            $table->integer('cantidad');
            $table->float('precio_unidad',8,2);
            $table->float('subtotal',8,2);
            $table->bigInteger('id_producto')->unsigned()->nullable();
            $table->bigInteger('id_almacen')->unsigned()->nullable();
            $table->foreignId('id_compra')->nullable()->constrained('compras')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('id_producto')->nullable()->references('id_producto')->on('producto_almacens');
            $table->foreign('id_almacen')->nullable()->references('id_almacen')->on('producto_almacens');
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
        Schema::dropIfExists('detalle_compras');
    }
}
