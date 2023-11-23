<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanpagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planpagos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->nullable();
            $table->string('descripcion')->nullable();
            $table->float('monto_total',8,2)->nullable();
            $table->float('descuento',8,2)->default(0);
            $table->float('interes',8,2)->default(0);
            $table->tinyInteger('estado')->default(1);
            $table->foreignId('id_usuario')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('id_cliente')->nullable()->constrained('clientes')->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('planpagos');
    }
}
