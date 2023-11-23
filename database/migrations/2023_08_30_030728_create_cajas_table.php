<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCajasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('nro_caja');
            $table->string('nombre');
            $table->string('descripcion');
            $table->float('monto_ingreso',8,2)->nullable()->default(0);
            $table->float('monto_ingreso_caja',8,2)->nullable()->default(0);
            $table->float('monto_egreso',8,2)->nullable()->default(0);
            $table->float('monto_total_generado',8,2)->nullable()->default(0);
            $table->date('fecha_inicio')->nullable()->default(date("y-m-d"));
            $table->time('hora_inicio')->nullable()->default(date("H:i:s"));;
            $table->date('fecha_final')->nullable();
            $table->time('hora_final')->nullable();
            $table->tinyInteger('estado')->default(1);
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
        Schema::dropIfExists('cajas');
    }
}
