<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    public function totalxmes($anioActual){
        $this->all();
        $this->select('MONTHNAME(ventas.fecha) AS mes, SUM(ventas.monto_total) AS total_venta');
        $where = "YEAR(ventas.fecha)='$anioActual' AND ventas.estado = 1";
        $this->where($where); 
        $this->groupby('mes');
        $datos=$this->orderBy('MONTHNAME(ventas.fecha)','DESC');
        //$datos = $this->findAll();
        return $datos;
    }
}
