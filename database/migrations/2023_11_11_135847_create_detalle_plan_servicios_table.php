<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetallePlanServiciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_plan_servicios', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('estado')->default(1);
            $table->foreignId('id_plan')->nullable()->constrained('planpagos')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('id_servicio')->nullable()->constrained('servicio_generals')->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('detalle_plan_servicios');
    }
}
