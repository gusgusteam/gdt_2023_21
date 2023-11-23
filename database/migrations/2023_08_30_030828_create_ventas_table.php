<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->float('monto_total',8,2)->nullable();
            $table->float('monto_cliente',8,2)->nullable();
            $table->float('descuento',8,2)->default(0);
            $table->float('interes',8,2)->default(0);
            $table->date('fecha')->nullable();
            $table->time('hora')->nullable();
            $table->date('fecha_deuda')->nullable();
            $table->time('hora_deuda')->nullable();
            $table->tinyInteger('estado')->default(1);//1 completado 0 cancelado -1 credito
            $table->tinyInteger('tipo_pago')->default(1);
            $table->string('codigo');
           // $table->string('nro_mesa')->nullable();
            $table->foreignId('id_cliente')->nullable()->constrained('clientes')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('id_empleado')->nullable()->constrained('empleados')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('id_caja')->nullable()->constrained('cajas')->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('ventas');
    }
}
