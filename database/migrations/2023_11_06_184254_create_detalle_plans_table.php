<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetallePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_plans', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('estado')->default(1);
            $table->foreignId('id_plan')->nullable()->constrained('planpagos')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('id_venta')->nullable()->constrained('ventas')->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('detalle_plans');
    }
}
