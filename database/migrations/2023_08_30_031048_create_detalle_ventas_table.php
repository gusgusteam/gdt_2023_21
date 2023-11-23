<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id();
            $table->integer('cantidad');
            $table->float('precio_unidad',8,2);
            $table->float('precio_unidad_compra',8,2);
            $table->float('subtotal',8,2);
            $table->foreignId('id_venta')->nullable()->constrained('ventas')->cascadeOnUpdate()->nullOnDelete();
            $table->bigInteger('id_producto')->unsigned()->nullable();
            $table->bigInteger('id_almacen')->unsigned()->nullable();
            $table->foreign('id_producto')->nullable()->references('id_producto')->on('producto_almacens');
            $table->foreign('id_almacen')->nullable()->references('id_almacen')->on('producto_almacens');
         //   $table->foreignId('id_producto')->nullable()->constrained('producto_almacens')->cascadeOnUpdate()->nullOnDelete();
         //   $table->foreignId('id_almacen')->nullable()->constrained('producto_almacens')->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('detalle_ventas');
    }
}
