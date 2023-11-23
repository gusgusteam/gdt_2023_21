<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Caja;
use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\Empleado;
use App\Models\Producto;
use App\Models\ProductoAlmacen;
use App\Models\TemporalVenta;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class TemporalVentaController extends Controller
{
    public function show(){
        $key='';
        $monto=0;
        if(session()->has('id_venta')){
            $key=session('id_venta');
            $monto=session('monto_cliente');
        }else{
          session(['id_venta'=>uniqid()]);
          $key=session('id_venta');
          $monto=null;
        }
        return view('ventas/show_venta',compact('key','monto'));
    }
    
    public function insertar($id_producto,$id_almacen,$codigo,$cantidad){
       $data=$this->actualizar($id_producto,$id_almacen,$codigo,$cantidad,1);
        return json_encode($data);
    }

    public function actualizar($id_producto,$id_almacen,$codigo,$cantidad,$option){
        //metodo para actualizar el temporal venta su cantidad sabiendo que existe
        // opcion  
        //1: para aumentar la cantidad 
        //0: para reducir la cantidad
        //-1: para cambiar a cantidad exacta
        //-2: para eliminar un producto de temporal venta
        $data['estado']=true;
        $data['error']=0;
        $data['mensaje']='';
        $data['datos']='';
        $data['total']=0;
        $data['cant']=0;
        if($cantidad>=0){
            $temporal_venta=TemporalVenta::
            where('id_producto','=',$id_producto)
            ->where('id_almacen','=',$id_almacen)
            ->where('codigo','=',$codigo)->first();
            $producto_almacen=ProductoAlmacen::
            where('id_almacen','=',$id_almacen)
            ->where('id_producto','=',$id_producto)
            ->first();
            $producto=Producto::findOrFail($id_producto);
            $es_inventariable=$producto->inventariable;

            if(isset($temporal_venta) && isset($producto_almacen)){
                if($option==1){
                    $calculo=$producto_almacen->stock-($temporal_venta->cantidad+$cantidad);
                    if($calculo>=0 || $es_inventariable==0){
                        $temporal_venta->cantidad+=$cantidad;
                        $temporal_venta->sub_total=$temporal_venta->precio_venta*$temporal_venta->cantidad;
                        $temporal_venta->update();   
                    }else{
                        //cuando ingrese un nuevo producto ingresara con cantidad 0 y si se equivoca se tiene q eliminar el temporal
                        if($temporal_venta->cantidad==0){
                            $temporal_venta->delete();
                        }
                        $data['error']=1;
                        $data['mensaje']='no se puede aumentar debido a que el stock del producto es insuficiente';
                    }
                }
                if($option==0){
                    $calculo=$temporal_venta->cantidad-$cantidad;
                    if($calculo>0){
                        $temporal_venta->cantidad-=$cantidad;
                        $temporal_venta->sub_total=$temporal_venta->precio_venta*$temporal_venta->cantidad;
                        $temporal_venta->update();
                    }else{
                        $temporal_venta->delete();
                    }
                }
                if($option==-1){
                    $calculo=$producto_almacen->stock-$cantidad;
                    if($calculo>=0 || $es_inventariable==0){
                        $temporal_venta->cantidad=$cantidad;
                        $temporal_venta->sub_total=$temporal_venta->precio_venta*$temporal_venta->cantidad;
                        $temporal_venta->update();
                        if($temporal_venta->cantidad==0){$temporal_venta->delete();}
                    }else{
                        $data['error']=1;
                        $data['mensaje']='no se puede actualizar debido a que el stock del producto es insuficiente';
                    }
                }
            }else{
                if(isset($producto_almacen) && $cantidad!=0){
                    // cuando es la primera ves que se registra en el temporl venta
                    //$producto=Producto::findOrFail($id_producto);
                    $precio_venta=$producto->precio_venta;
                    $temporal_venta= new TemporalVenta();
                    $temporal_venta->codigo=$codigo;
                    $temporal_venta->id_producto=$id_producto;
                    $temporal_venta->id_almacen=$id_almacen;
                    $temporal_venta->id_usuario=Auth::user()->id;
                    $temporal_venta->precio_venta=$precio_venta;
                    $temporal_venta->save();
                    $data=$this->actualizar($id_producto,$id_almacen,$codigo,$cantidad,1);
                }else{
                    $data['estado']=false;
                    $data['error']=1;
                    if($cantidad==0){
                        $data['mensaje']='al agregar el producto la cantidad no puede ser 0';
                    }
                    else{
                        $data['mensaje']='su producto de almacen no existe';
                    }
                    
                }  
            }
        }else{
            $data['error']=1;
            $data['mensaje']='la cantidad no puede ser valor negativo';
        }
        //if($data['error']==0){
            $datos_venta=$this->cargar_datos_tabla_venta($codigo);
            $data['datos']=$datos_venta['fila'];
            $data['total']=$datos_venta['total'];
            $sql = "SELECT tv.id
            FROM temporal_ventas tv
            WHERE tv.codigo = '$codigo'";
            $cantidad = DB::select($sql);
            $data['cant']=count($cantidad);
       // }
        return $data;
    }

    public function aumentar_cantidad($id_producto,$id_almacen,$codigo,$cantidad){
        $data=$this->actualizar($id_producto,$id_almacen,$codigo,$cantidad,1);
        return json_encode($data);
    }
    public function reducir_cantidad($id_producto,$id_almacen,$codigo,$cantidad){
        $data=$this->actualizar($id_producto,$id_almacen,$codigo,$cantidad,0);
        return json_encode($data);
    }
    public function modificar_cantidad($id_producto,$id_almacen,$codigo,$cantidad){
        $data=$this->actualizar($id_producto,$id_almacen,$codigo,$cantidad,-1);
        return json_encode($data);
    }
    public function eliminar_producto($id_producto,$id_almacen,$codigo){
        $data=$this->actualizar($id_producto,$id_almacen,$codigo,0,-1);
        return json_encode($data);
    }

    public function get_datos($codigo){
        $datos_venta=$this->cargar_datos_tabla_venta($codigo);
        $data['datos']=$datos_venta['fila'];
        $data['total']=$datos_venta['total'];
        $sql = "SELECT tv.id
        FROM temporal_ventas tv
        WHERE tv.codigo = '$codigo'";
        $cantidad = DB::select($sql);
        $data['cant']=count($cantidad);

        return json_encode($data);
    }

    public function cargar_datos_tabla_venta($codigo_venta)
    {
        $resultado = TemporalVenta::all()->where('codigo','=',$codigo_venta);
        $fila = '';
        $numFila = 0;
        $total=0;
        $r="'";
        foreach ($resultado as $row){
            $producto=Producto::findOrFail($row['id_producto']);
            $almacen=Almacen::findOrFail($row['id_almacen']);
            $numFila++;
            $btn_editar_cant='';
            $btn_editar_cant.='<div class="input-group input-group-sm mb-0">';
            $btn_editar_cant.='<input type="number" class="form-control" style="width:25px;" id="iden'.$row['id'].'" name="iden'.$row['id'].'" title="cantidad" value="'.$row['cantidad'].'" min="1" > ';
            $btn_editar_cant.='<button class="btn btn-success btn-sm btn-flat" type ="button" onclick="modificarCantidad('.$row['id_almacen'].','.$row['id_producto'].','.$row['id'].','.$r.$row['codigo'].$r.')" title="aumentar"><i class="fas fa-plus-circle"></i></button>';
            $btn_editar_cant.='</div>';

            $fila .= "<tr id='fila".$numFila."'>";
            $fila .= "<td>".$numFila."</td>";
            $fila .= "<td class=\"text-center\" >".$producto->nombre."</td>";
            $fila .= "<td class=\"text-center\" >".$almacen->nombre."</td>";
            $fila .= "<td class=\"text-center\" >".$row['precio_venta']."</td>";
           // $fila .= "<td>".$row['cantidad']."</td>";
            $fila .= "<td class=\"text-center\" >".$btn_editar_cant."</td>";
            $fila .= "<td class=\"text-center\" >".number_format($row['sub_total'],2,'.','')."</td>";
            $fila .= "<td ><div class=\"btn-group btn-group-sm \"> 
                <a class=\"btn \" role = 'button' onclick=\"actualizarProducto(".$row['id_producto'].",".$row['id_almacen'].","."'".$row['codigo']."'".","."-1".")\"  ><i class=\"fas fa-minus-circle\"></i></a>
                <a class=\"btn \" role = 'button' onclick=\"eliminarProducto(".$row['id_producto'].",".$row['id_almacen'].","."'".$row['codigo']."'".","."0".")\"  ><i class=\"fas fa-trash\"></i></a>
                <a class=\"btn \" role = 'button' onclick=\"actualizarProducto(".$row['id_producto'].",".$row['id_almacen'].","."'".$row['codigo']."'".","."1".")\"  ><i class=\"fas fa-plus-circle\"></i></a>
            </div></td>";
            $fila .= "</tr>";
            $total+=$row['sub_total'];
            
        }

        $data['fila']=$fila;
        $data['total']=number_format($total,2,'.','');
        return $data;
    }

   
    public function guardar_venta(Request $request)
    {
        $id_cliente=$request->id_cliente;
        $codigo=$request->codigo;
        $data['error']=0;
        $data['id_venta']=null;
        $data['mensaje']='';
        $verificar_caja=Caja::findOrFail(1);

        if($verificar_caja->estado==0){
            $data['error']=1;
            $data['mensaje']='la caja de inventario a sido cerrado preguntar al encargado';
            return json_encode($data);
        }

        $empleado=Empleado::all()->where('id_usuario','=',Auth::user()->id)->first();
        if($empleado && $codigo!=null && $codigo!=''){

            $cliente=Cliente::all()->where('id','=',$id_cliente)->first();
            if($request->tipo_pago==0 && $request->modo==0){
                $data['error']=1;$data['mensaje']="es obligacion seleccionar el cliente cuando se realiza la venta al credito";
                return json_encode($data);
            }
            if(isset($cliente) || $request->modo==0){

                $suma=TemporalVenta::where('codigo','=',$codigo)->sum('sub_total');
                if(((session('monto_cliente') < $suma) && $request->modo==1 && $request->tipo_pago==1) || ($request->modo==0 && session('monto_cliente')!=null && session('monto_cliente')< $suma)){
                    $data['error']=1;
                    $data['mensaje']='el monto del cliente no es suficiente para realizar esta venta';  
                }else{
                    $resultado = TemporalVenta::all()->where('codigo','=',$codigo);
                    if(count($resultado)<=0){
                        $data['error']=1;$data['mensaje']="no selecciono ningun producto para la venta";
                        return $data;
                    }else{
                        $venta = new Venta();
                        $sw=0;
                        while($sw==0){
                            $codigo_generado = uniqid();
                            $existe=Venta::all()->where('codigo','=',$codigo_generado)->first();
                            if(!$existe){ $sw=1;}
                        }
                        $venta->fecha=date("y-m-d");
                        $venta->hora=date('H:i:s');
                        //$venta->hora=date("h:i:s");
                       // $venta->hora=date("G");
                        if($request->modo==0){
                            $venta->id_cliente=null;
                        }else{
                            $venta->id_cliente=$id_cliente;
                        }
                        
                        $venta->codigo=$codigo_generado;
                        if($request->tipo_pago==0){
                            $venta->estado=-1;
                            $venta->tipo_pago=$request->tipo_pago;
                        }
                        if(isset($request->descuento)){
                            $venta->descuento=$request->descuento;
                        }
                        $venta->save();
                        $total=0;

                        foreach ($resultado as $row){
                            $producto_almacen=ProductoAlmacen::all()
                            ->where('id_producto','=',$row->id_producto)
                            ->where('id_almacen','=',$row->id_almacen)->first();
                            $calcuno_nuevo = $producto_almacen->stock-$row->cantidad;
                            $producto_a=Producto::findOrFail($row['id_producto']);
                            $almacen_a=Almacen::findOrFail($row['id_almacen']);
                            if($calcuno_nuevo<0 && $producto_a->inventariable ==1 ){
                                $data['error']=1;
                                $data['mensaje'].=$producto_a->nombre." en el ".$almacen_a->nombre. " existe solo ".$producto_almacen->stock." , ";
                            }

                        }
                        if($data['error']==1){
                            $venta->delete(); // la venta tiene que eliminarse por un error
                            return json_encode($data);
                        }
                    
                        foreach ($resultado as $resultado){
                            $producto=Producto::findOrFail($resultado['id_producto']);
                            $producto_almacen=ProductoAlmacen::all()
                            ->where('id_producto','=',$resultado->id_producto)
                            ->where('id_almacen','=',$resultado->id_almacen)->first();
                            if($producto->inventariable==1){
                                $producto_almacen->stock -= $resultado->cantidad;
                            }                       
                            $producto_almacen->update();
                            $detalle_venta= new DetalleVenta();
                            $detalle_venta->cantidad=$resultado->cantidad;
                            $detalle_venta->precio_unidad=$resultado->precio_venta;
                            $detalle_venta->precio_unidad_compra=$producto->precio_compra;
                            $detalle_venta->subtotal=$resultado->sub_total;
                            $detalle_venta->id_producto=$resultado->id_producto;
                            $detalle_venta->id_almacen=$resultado->id_almacen;
                            $detalle_venta->id_venta=$venta->id;
                            $detalle_venta->save();
                            $total+=$resultado['sub_total'];
                        }
                        $venta->monto_total=$total;
                        if($request->modo==0 && (session('monto_cliente')==null || session('monto_cliente')==0 ) ){
                            $venta->monto_cliente=$total;
                        }else{
                            $venta->monto_cliente=session('monto_cliente');                 
                        }
                        $venta->id_empleado=$empleado->id;
                        $venta->id_caja=1;
                        $venta->update();
                        TemporalVenta::where('id_usuario','=', Auth::user()->id)->delete();
                        $data['id_venta']=$venta->id;
                        session(['monto_cliente'=>null]);
                    }
                }
            }else{
                $data['error']=1;$data['mensaje']="no selecciono ningun cliente";
                return json_encode($data);
            }
         
        }else{
            $data['error']=1;
            if($codigo==null || $codigo==''){
                $data['mensaje']="el codigo de compra no funciona";
            }else{
                $data['mensaje']="no cuenta con registro de empleado";
            }
        }
        return json_encode($data);
    }

    public function destroy(){
        $data['error']=0;
        session(['id_venta'=>null]);
        session(['monto_cliente'=>null]);
        TemporalVenta::where('id_usuario','=', Auth::user()->id)->delete();
        return json_encode($data);
    }

    
}
