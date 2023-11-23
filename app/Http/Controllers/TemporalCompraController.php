<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Caja;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Empleado;
use App\Models\Producto;
use App\Models\ProductoAlmacen;
use App\Models\TemporalCompra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemporalCompraController extends Controller
{
   
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        //
    }

    public function insertar($id_producto,$id_almacen,$cantidad,$codigo){
        $data['error']=0;
        if(!$this->verificar_compra($id_producto,$id_almacen,$codigo)){
        $producto=Producto::findOrFail($id_producto);
        $precio_compra=$producto->precio_compra;

        $temporal_compra= new TemporalCompra();
        $temporal_compra->codigo=$codigo;
        $temporal_compra->id_producto=$id_producto;
        $temporal_compra->id_almacen=$id_almacen;
        $temporal_compra->id_usuario=Auth::user()->id;
        $temporal_compra->precio_compra=$precio_compra;
        $temporal_compra->cantidad=$cantidad;
        $temporal_compra->sub_total=$precio_compra*$cantidad;
        $temporal_compra->save();
    
        }else{
            if($cantidad<=0){
                $data['error']=1;
            }else{
            $this->actulizar_cantidad($id_producto,$id_almacen,$codigo,$cantidad);
            }
        }
        $registros=$this->cargar_datos_tabla_compra($codigo);
        $data['datos']=$registros['fila'];
        $data['total']=$registros['total'];
       // return json_encode($data);
       // $data['datos']= $this->cargar_datos_tabla_compra($codigo);
        return json_encode($data);
    }

    public function cargar_datos_tabla_compra($codigo_compra)
    {
        $resultado = TemporalCompra::all()->where('codigo','=',$codigo_compra);
        $fila = '';
        $numFila = 0;
        $total=0;
        foreach ($resultado as $row){
            $producto=Producto::findOrFail($row['id_producto']);
            $almacen=Almacen::findOrFail($row['id_almacen']);
            $numFila++;
            $fila .= "<tr id='fila".$numFila."'>";
            $fila .= "<td>".$numFila."</td>";
            $fila .= "<td>".$producto->nombre."</td>";
            $fila .= "<td>".$almacen->nombre."</td>";
            $fila .= "<td>".$row['precio_compra']."</td>";
            $fila .= "<td>".$row['cantidad']."</td>";
            $fila .= "<td>".number_format($row['sub_total'],2,'.','')."</td>";
            $fila .= "<td><div class=\"btn-group btn-group-sm \"> 
                <a class=\"btn btn-sm\" role = 'button' onclick=\"actualizarProducto(".$row['id_producto'].",".$row['id_almacen'].","."'".$row['codigo']."'".","."-1".")\"  ><i class=\"fas fa-minus-circle\"></i></a>
                <a class=\"btn btn-sm\" role = 'button' onclick=\"actualizarProducto(".$row['id_producto'].",".$row['id_almacen'].","."'".$row['codigo']."'".","."0".")\"  ><i class=\"fas fa-trash\"></i></a>
                <a class=\"btn btn-sm\" role = 'button' onclick=\"actualizarProducto(".$row['id_producto'].",".$row['id_almacen'].","."'".$row['codigo']."'".","."1".")\"  ><i class=\"fas fa-plus-circle\"></i></a>
            </div></td>";
            $fila .= "</tr>";
            $total+=$row['sub_total'];
        }

        $data['fila']=$fila;
        $data['total']=number_format($total,2,'.','');
        return $data;
    }

    public function get_datos($codigo){
        $r=$this->cargar_datos_tabla_compra($codigo);
        $data['datos']=$r['fila'];
        $data['total']=$r['total'];
        return json_encode($data);
    }

    public function verificar_compra($id_producto,$id_almacen,$codigo){
        
        $temporal_compra=TemporalCompra::all()
        ->where('id_producto','=',$id_producto)
        ->where('id_almacen','=',$id_almacen)
        ->where('codigo','=',$codigo)->first();
        if($temporal_compra){
            return true;
        }else{
            return false;
        }
    }

    public function actulizar_cantidad($id_producto,$id_almacen,$codigo,$cant){
        $temporal_compra=TemporalCompra::all()
        ->where('id_producto','=',$id_producto)
        ->where('id_almacen','=',$id_almacen)
        ->where('codigo','=',$codigo)->first();
        $nueva_cantidad=$temporal_compra->cantidad + $cant;
        if(($cant==0) || ($nueva_cantidad==0)){
            $temporal_compra->delete();
        }else{
        $temporal_compra->cantidad=$nueva_cantidad;
        $temporal_compra->sub_total=$temporal_compra->precio_compra*$nueva_cantidad;
        $temporal_compra->update();
        }
    }

    public function actualizar_producto($id_producto,$id_almacen,$codigo,$cantidad){
        $this->actulizar_cantidad($id_producto,$id_almacen,$codigo,$cantidad);
       // $data['datos']=$this->cargar_datos_tabla_compra($codigo);
        $registros=$this->cargar_datos_tabla_compra($codigo);
        $data['datos']=$registros['fila'];
        $data['total']=$registros['total'];
        return json_encode($data);
    }
   

    public function guardar_compra($codigo)
    {
        $data['error']=0;
        $verificar_caja=Caja::findOrFail(1);

        if($verificar_caja->estado==0){
            $data['error']=1;
            $data['mensaje']='la caja de inventario a sido cerrado preguntar al encargado';
            return json_encode($data);
        }

        $empleado=Empleado::all()->where('id_usuario','=',Auth::user()->id)->first();
        if($empleado && $codigo!=null && $codigo!=''){
            $resultado = TemporalCompra::all()->where('codigo','=',$codigo);
            if(count($resultado)<=0){
                $data['error']=1;$data['mensaje']="no selecciono ningun producto para la compra";
                return $data;
            }else{
            $compra = new Compra();
            $sw=0;
            while($sw==0){
            $codigo_generado = uniqid();
            $existe=Compra::all()->where('codigo','=',$codigo_generado)->first();
            if(!$existe){ $sw=1;}
            }
            $compra->fecha=date("y-m-d");
            $compra->hora=date("H:i:s");
            $compra->codigo=$codigo_generado;
            $compra->save();
            $total=0;
            foreach ($resultado as $resultado){
                if (!$this->existe_ProductoAlmacen($resultado->id_producto,$resultado->id_almacen)){
                    $registro= new ProductoAlmacen();
                    $registro->id_producto=$resultado->id_producto;
                    $registro->id_almacen=$resultado->id_almacen;
                    $registro->save();
                }
                $producto_almacen=ProductoAlmacen::all()
                ->where('id_producto','=',$resultado->id_producto)
                ->where('id_almacen','=',$resultado->id_almacen)->first();
                $producto_almacen->stock = $producto_almacen->stock+$resultado->cantidad;
                $producto_almacen->update();
                $detalle_compra= new DetalleCompra();
                $detalle_compra->cantidad=$resultado->cantidad;
                $detalle_compra->precio_unidad=$resultado->precio_compra;
                $detalle_compra->subtotal=$resultado->sub_total;
                $detalle_compra->id_producto=$resultado->id_producto;
                $detalle_compra->id_almacen=$resultado->id_almacen;
                $detalle_compra->id_compra=$compra->id;
                $detalle_compra->save();
                $total+=$resultado['sub_total'];
            }
            $compra->monto_total=$total;
            $compra->id_empleado=$empleado->id;
            $compra->id_caja=1;
            $compra->update();
            $caja=Caja::all()->where('id','=',1)->first();
            $caja->monto_egreso+= $compra->monto_total;
            $caja->update();
            TemporalCompra::where('id_usuario', '=', Auth::user()->id)->delete();
            }
        }else{
            $data['error']=1;
            if($codigo==null || $codigo==''){
                $data['mensaje']="el codigo de compra no funciona";
            }else{
                $data['mensaje']="no cuenta con registro de empleado";
            }
        }
        return $data;
    }

    public function existe_ProductoAlmacen($id_producto,$id_almacen){
        $producto_almacen=ProductoAlmacen::all()
        ->where('id_producto','=',$id_producto)
        ->where('id_almacen','=',$id_almacen)
        ->first();
        if($producto_almacen){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TemporalCompra  $temporalCompra
     * @return \Illuminate\Http\Response
     */
    public function show(TemporalCompra $temporalCompra)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TemporalCompra  $temporalCompra
     * @return \Illuminate\Http\Response
     */
    public function edit(TemporalCompra $temporalCompra)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TemporalCompra  $temporalCompra
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TemporalCompra $temporalCompra)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TemporalCompra  $temporalCompra
     * @return \Illuminate\Http\Response
     */
    public function destroy(TemporalCompra $temporalCompra)
    {
        //
    }
}
