<?php

namespace Database\Factories;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoAlmacenFactory extends Factory
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
            'id_producto' => 2 ,
            'id_almacen' =>  1,
            'stock' =>  100,
            'estado'=> 1
        ];
    }
}
