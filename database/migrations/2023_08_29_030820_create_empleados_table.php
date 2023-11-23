<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpleadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellidos');
            $table->integer('edad')->nullable();
            $table->string('sexo')->default('prefiero no decirlo');
            $table->integer('telefono')->nullable();
            $table->integer('sueldo')->nullable();
            $table->string('direccion')->nullable();
            $table->integer('ci');
            $table->tinyInteger('estado')->default(1);
            $table->foreignId('id_usuario')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('empleados');
    }
}
