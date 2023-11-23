<?php

namespace Database\Factories;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            'nombre' =>  'hola',
            'descripcion' =>  'nueo',
            'stock' =>  10,
            'stock_minimo' => 10,
            'precio_compra' => 15 ,
            'precio_venta' => 18,
            'id_categoria' => 1
        ];
    }
}
