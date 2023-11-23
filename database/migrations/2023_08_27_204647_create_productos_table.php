<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('descripcion',800);
            $table->float('precio_venta',8,2);
            $table->float('precio_compra',8,2);
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo');
            $table->tinyInteger('inventariable')->default(1);
            $table->tinyInteger('estado')->default(1);
            $table->foreignId('id_categoria')->nullable()->constrained('categorias')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('id_provedor')->nullable()->constrained('proveedors')->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('productos');
    }
}
