<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductoAlmacensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('producto_almacens', function (Blueprint $table) {
            $table->id();
            $table->integer('stock')->default(0);
            $table->integer('inventariable')->default(1);
            $table->tinyInteger('estado')->default(1);
            $table->foreignId('id_producto')->nullable()->constrained('productos')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('id_almacen')->nullable()->constrained('almacens')->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('producto_almacens');
    }
}
