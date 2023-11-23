<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngresosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingresos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->uniqid();
            $table->string('descripcion',800);
            $table->float('monto_total',8,2)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->string('tipo_ingreso')->nullable();
            $table->foreignId('id_caja_primaria')->nullable()->constrained('cajas')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('id_caja_secundaria')->nullable()->constrained('cajas')->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('ingresos');
    }
}
