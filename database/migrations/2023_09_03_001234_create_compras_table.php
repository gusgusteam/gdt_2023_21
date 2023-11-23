<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->float('monto_total',8,2)->nullable();
            $table->date('fecha')->nullable();
            $table->time('hora')->nullable();
            $table->string('codigo');
            $table->tinyInteger('estado')->default(1);
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
        Schema::dropIfExists('compras');
    }
}
