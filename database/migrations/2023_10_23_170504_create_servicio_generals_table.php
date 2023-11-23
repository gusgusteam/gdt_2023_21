<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicioGeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servicio_generals', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->uniqid();
            $table->string('descripcion',800);
            $table->float('monto_total',8,2)->nullable();
            $table->float('interes',8,2)->default(0);
            $table->tinyInteger('estado')->default(1);
            $table->tinyInteger('tipo_pago')->default(1);
            $table->tinyInteger('tipo_lab')->nullable();
            $table->timestamp('fecha_deuda')->nullable();
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
        Schema::dropIfExists('servicio_generals');
    }
}
