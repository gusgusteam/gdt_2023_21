<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Configuracion;
use App\Models\detalle_plan;
use App\Models\DetalleVenta;
use App\Models\Empleado;
use App\Models\planpago;
use App\Models\Producto;
use App\Models\ProductoAlmacen;
use App\Models\TemporalVenta;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Codedge\Fpdf\Fpdf\Fpdf;

class VentaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view('ventas/index');
    }

    public function deuda_cliente_mes($id_cliente){
        $registro=Cliente::findOrFail($id_cliente);
        $nombre_cliente=$registro->nombre. ' '.$registro->apellidos;
        $sql2="SELECT YEAR(va.fecha) as anio_tiempo, MONTH(va.fecha) as mes_tiempo, (SELECT SUM(v.monto_total) FROM ventas v WHERE MONTH(v.fecha)=MONTH(va.fecha) AND YEAR(v.fecha)=YEAR(va.fecha) AND v.id_cliente= $id_cliente AND v.tipo_pago=0  AND (v.estado=-1 OR v.estado=1) ) as monto_general , (SELECT SUM(vv.monto_total) FROM ventas vv WHERE vv.id_cliente=$id_cliente AND  MONTH(vv.fecha)=MONTH(va.fecha) AND YEAR(vv.fecha)=YEAR(va.fecha) AND vv.estado=1 AND vv.tipo_pago=0 ) as saldo_pagado
        FROM ventas va
        WHERE 
        (va.estado=-1  OR va.estado=1) AND
        va.tipo_pago=0 AND
        va.id_cliente=$id_cliente
        GROUP BY anio_tiempo,mes_tiempo,monto_general,saldo_pagado";
        $datos=DB::select($sql2);
        return view('ventas/deuda_mes',compact('datos','nombre_cliente','id_cliente'));
    }

    public function deuda_cliente_dias($id_cliente,$anio,$mes){
        $registro=Cliente::findOrFail($id_cliente);
        $nombre_cliente=$registro->nombre. ' '.$registro->apellidos;
        $sql2="SELECT va.fecha_deuda,va.hora_deuda, va.id,va.hora ,va.codigo, va.monto_total,va.id_empleado,va.interes, va.fecha,va.estado
        FROM ventas va
        WHERE 
        (va.estado=-1 OR va.estado=1) AND
        va.tipo_pago=0 AND
        YEAR(va.fecha)=$anio AND
        MONTH(va.fecha)=$mes AND
        va.id_cliente=$id_cliente";
        $datos=DB::select($sql2);
       

        $fila = '';
        $numFila = 0;
        $aux_fecha="";
        $total=0;
        foreach ($datos as $row){
            $numFila++;
            
            $r=Empleado::findOrFail($row->id_empleado);
            $fila .= "<tr id='fila".$numFila."'>";
            $fila .= "<td>".$numFila."</td>";
            $fila .= "<td>".$row->codigo."</td>";
            $fila .= "<td>".$row->fecha.' '.$row->hora."</td>";
            $fila .= "<td>".$row->fecha_deuda.' '.$row->hora_deuda."</td>";
            $fila .= "<td>".$r->nombre.' '.$r->apellidos."</td>";
            $fila .= "<td>".$row->monto_total."</td>";
            $fila .= "<td>".$row->interes."</td>";
            if($row->estado==1){
              $fila .= "<td> <span class=\"badge bg-success\">Completado</span> </td>";  
            }else{
              $fila .= "<td> <span class=\"badge bg-warning\">Incompleto</span> </td>";  
            }
            $aux_fecha="'".$row->fecha." : ".$row->hora."'";
            $btn="<a class=\"btn btn-sm btn-dark\" onclick=\"completar_credito(".$row->id.','.$aux_fecha.','.$row->monto_total.")\" title=\"cancelar deuda\" ><i class=\"far fa-check-circle\"></i></a>";
 
            $fila .= "<td><div class=\"text-right\"><div class=\"btn-group btn-group-sm \">";
            if($row->estado==-1){
                $fila .= $btn;
            } 
                
            $fila.="<a class=\"btn btn-sm btn-danger\" onclick=\"mostrar_detalle(".$row->id.")\" title=\"cancelar deuda\" ><i class=\"far fa-file-pdf\"></i></a>

            </div></div></td>";
            $fila .= "</tr>";
        }

        $data['fila']=$fila;
        $data['nombre_cliente']=$nombre_cliente;
      //  $data['total']=number_format($total,2,'.','');
        return json_encode($data);
       // return view('ventas/deuda_mes',compact('datos','nombre_cliente'));
    }

    public function cancelar_credito(Request $request){
        $data['error']=0;
        $data['mensaje']="";
        $verificar_caja=Caja::findOrFail(1);

        if($verificar_caja->estado==0){
            $data['error']=1;
            $data['mensaje']='la caja de inventario a sido cerrado preguntar al encargado';
            return json_encode($data);
        }

        $venta=Venta::findOrFail($request->id_venta);
        if(isset($venta)){
            $venta->estado=1;
            $venta->monto_cliente=$venta->monto_total;
            $venta->descuento=$request->descuento;
            $venta->interes=$request->interes;
            $venta->fecha_deuda=date("y-m-d");
            $venta->hora_deuda=date("H:i:s");
            $venta->update();
            $caja=Caja::all()->where('id','=',$venta->id_caja)->first();
            $caja->monto_ingreso += ($venta->monto_total-$venta->descuento+$venta->interes);
            $caja->update();
            return json_encode($data);
        }
        return json_encode($data);

    }

    public function nueva_venta()
    {
        //$sql="SELECT p.inventariable, pa.id,pa.id_producto,pa.id_almacen, p.nombre as nombre_producto,a.nombre as nombre_almacen,pa.stock , p.precio_venta
        //FROM producto_almacens pa,productos p,almacens a
        //WHERE pa.id_producto=p.id AND
        //pa.id_almacen=a.id AND
        //p.estado=1 AND
        //(pa.stock>0 OR
        //p.inventariable=0)
        //";
        //$producto_almacen= DB::select($sql);
        $categorias=Categoria::all()->where('estado','=',1);
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
        return view('ventas/create',compact('key','monto','categorias'));
    }

    public function generar_producto_venta($producto_almacen){
        $imagen = "img/productos/".$producto_almacen->id_producto.".png";
        if (!file_exists($imagen)) {$imagen = "img/productos/150x150.png";}
        $imagen=asset($imagen);
       // $r="'";

      $row='<div class="col"> 
                <div class="card card-outline card-primary">'.
                      '<div class="card-header">'.
                        '<h3 class="card-title">'.$producto_almacen->nombre_producto.'</h3>'.
                      '</div>'.
                        '<img  style="cursor:  pointer;  margin: auto; justify-content: center; width:150px; height:150px;" src="'.$imagen.'" alt="imagen producto">'.
                    '<div class="card-body">'.
                            '<p class="card-text  mb-0 text-left"> <b>'.$producto_almacen->nombre_almacen.'</b> </p>'.
                            '<p class="card-text  mb-0">'.$producto_almacen->precio_venta.' Bs </p>';
                            if ($producto_almacen->inventariable==0){
                             $row.= '<p class="card-text mb-2 text-right">Stock: --- </p>';
                            }
                            else{
                             $row.= '<p class="card-text mb-2 text-right">Stock:'.$producto_almacen->stock.'</p>';
                            }
                            
                            $row.= '<div class="d-flex justify-content-between align-items-center">'.
                              '<div class="btn-group"> ' .
                              '</div>'.
                               '<a class="btn btn-sm btn-danger" onclick="agregarProducto(id_venta.value,'.$producto_almacen->id_producto .','.$producto_almacen->id_almacen.')" >Agregar Producto </a>'.
                            //'</div>'.
                        '</div>'.
                    '</div>'.
                '</div> 
            </div>';
        return $row;        
    }

    public function DatosServerSide($nombre,$descripcion,$id_categoria)
    {  
        //dd($nombre,$descripcion,$id_categoria);
        $qua1="";
        $qua2="";
        $qua3="";
        if($nombre!='-1'){
            $qua1=" p.nombre LIKE '%$nombre%' AND";
        }
        if($descripcion!='-1'){
            $qua2=" p.descripcion LIKE '%$descripcion%' AND";
        }
        if($id_categoria!=-1){
            $qua3=" p.id_categoria = '$id_categoria' AND";
        }
        
        $consulta=$qua1.$qua2.$qua3;
        //$sql="SELECT p.inventariable, pa.id,pa.id_producto,pa.id_almacen, p.nombre as nombre_producto,a.nombre as nombre_almacen,pa.stock , p.precio_venta
        //FROM producto_almacens pa,productos p,almacens a
        //WHERE pa.id_producto=p.id AND
        //pa.id_almacen=a.id AND
        //p.estado=1 AND
        //$consulta
        //(pa.stock>0 OR
        //p.inventariable=0)
        //LIMIT 50
        //";
        $sql="SELECT p.inventariable, pa.id,pa.id_producto,pa.id_almacen, p.nombre as nombre_producto,a.nombre as nombre_almacen,pa.stock , p.precio_venta,
        (
        SELECT IFNULL(COUNT(dv.id_venta),0)
        FROM producto_almacens paa,productos pp,almacens aa ,detalle_ventas dv
        WHERE paa.id_producto=pa.id_producto AND
            paa.id_almacen=pa.id_almacen AND
            pp.id=p.id AND
            aa.id=a.id AND
            dv.id_producto=paa.id_producto AND
            dv.id_almacen=paa.id_almacen
        ) AS cantidad
                FROM producto_almacens pa,productos p,almacens a
                WHERE pa.id_producto=p.id AND
                pa.id_almacen=a.id AND
                p.estado=1 AND
                $consulta
                (pa.stock>0 OR
                p.inventariable=0)
                ORDER BY cantidad DESC
                LIMIT 30";
        //$producto=DB::table('productos p')->join('categorias','p.id_categoria','=','categorias.id')->where('p.nombre','like','%acei%')->select('*')->paginate(15);

        $producto_almacen= DB::select($sql);
        $row='';
        foreach ($producto_almacen as $item){
            $row.= $this->generar_producto_venta($item);
            
        }
       return json_encode($row);
    }

    

    public function datos()
    {  
        $caja=Caja::findOrFail(1);
        //$aux_fecha_inicio="'".$caja->fecha_inicio." ".$caja->hora_inicio."'";
       // $aux_fecha_final="'".$caja->fecha_final." ".$caja->hora_final."'";
        $ventas=Venta::select(
            'ventas.*'
        )
        //concat( ventas.fecha, ' ', ventas.hora)

        ->where('ventas.created_at','>=',$caja->fecha_inicio . "  ".$caja->hora_inicio)
        //->orwhere('ventas.fecha_deuda','>=',$caja->fecha_inicio)
        //->orwhere('ventas.created_at','>=',$caja->fecha_inicio . "  ".$caja->hora_inicio)
        ->orderBy('ventas.created_at','desc');
     
        return DataTables::of($ventas)
            // anadir nueva columna botones
           ->addColumn('actions', function($ventas){
             $url_detalle=route('venta.pdf',$ventas->id);

             $btn_detalle='<a class="btn btn-primary" rel="tooltip" data-placement="top" title="Ver Detalles" onclick="mostrar_detalle('.$ventas->id.')" ><i class="fas fa-stream"></i></a>';
             $btn_eliminar='<a class="btn btn-warning" rel="tooltip" data-placement="top" title="Eliminar" onclick="Eliminar('.$ventas->id.')"><i class="far fa-trash-alt"></i></a>';
             $btn_pdf='<a class="btn btn-danger" rel="tooltip" data-placement="top" title="PDF" href="'.$url_detalle.'"><i class="fas fa-file-pdf"></i></a>';
             $btn_ticket='<a class="btn btn-danger" rel="tooltip" data-placement="top" title="ticket termica" onclick="mostrar_detalle_venta('.$ventas->id.')" ><i class="fas fa-file-pdf"></i></a>';
                $btn= '<div class="text-right">  <div class="btn-group btn-group-sm ">';
                if(($ventas->estado==1 || $ventas->estado==-1) && auth()->user()->can('venta.cancelar')  ){
                   $btn= $btn.$btn_eliminar.$btn_detalle.$btn_pdf.$btn_ticket;
                }else{
                   $btn= $btn.$btn_detalle.$btn_pdf.$btn_ticket;
                }
                $btn=$btn.'</div> </div> ';
             return  $btn;
           })
           ->addColumn('estado', function($ventas){
            if($ventas->estado==0){
                $span= '<span class="badge bg-danger">Cancelado</span>';
            }
            if($ventas->estado==1){
                $span= '<span class="badge bg-success">Completdo</span>';
            }
            if($ventas->estado==-1){
                $span= '<span class="badge bg-warning">Incompleto</span>';
            }
            return  $span;
            })
            ->addColumn('tipo', function($ventas){
                if($ventas->tipo_pago==0){
                    $span= '<span class="badge bg-dark">Credito</span>';
                }
                if($ventas->tipo_pago==1){
                    $span= '<span class="badge bg-white">Contado</span>';
                }
                return  $span;
            })
            ->addColumn('fecha_hora', function($ventas){
            
                return  $ventas->fecha . "  ".$ventas->hora;
            })
            ->addColumn('monto', function($ventas){
                return  $ventas->monto_total . " bs";
            })
            ->addColumn('descuento_monto', function($ventas){
                return  $ventas->descuento . " bs";
            })
            ->addColumn('interes_monto', function($ventas){
                return  $ventas->interes . " bs";
            })
            ->addColumn('usuario', function($ventas){
                if($ventas->id_cliente==null){
                    return "publico general";
                }else{
                    $cliente=Cliente::findOrFail($ventas->id_cliente);
                    return $cliente->nombre.' '.$cliente->apellidos;
                }
              
            })
            ->addColumn('empleado', function($ventas){
                if($ventas->id_empleado==null){
                    return "error de venta";
                }else{
                    $empleado=Empleado::findOrFail($ventas->id_empleado);
                    return $empleado->nombre.' '.$empleado->apellidos;
                }
              
            })
            
           ->rawColumns(['actions','usuario','tipo','monto','descuento_monto','interes_monto','empleado','fecha_hora','estado']) // incorporar columnas
           ->make(true); // convertir a codigo
    }

    public function detalle_venta(Venta $venta){
        
        $sql = "SELECT p.nombre as nombre_producto , a.nombre as nombre_almacen, dv.cantidad ,dv.precio_unidad,dv.subtotal
        FROM detalle_ventas dv ,ventas v, producto_almacens pa, productos p, almacens a
        WHERE 
        dv.id_venta=v.id AND
        dv.id_producto=pa.id_producto AND
        dv.id_almacen=pa.id_almacen AND
        pa.id_producto=p.id AND
        pa.id_almacen=a.id AND
        v.id=$venta->id
        ";

        $detalle = DB::select($sql);

        $fila='';
        $c=1;
        $total=0;
        foreach($detalle as $row){
            $fila .="<tr>";
            $fila .="<td>".$c."</td>";
            $fila .="<td>".$row->nombre_producto."</td>";
            $fila .="<td>".$row->nombre_almacen."</td>";
            $fila .="<td>".$row->cantidad."</td>";
            $fila .="<td>".$row->precio_unidad."</td>";
            $fila .="<td>".number_format($row->subtotal,2,'.','')."</td>";
            $fila .="</tr>";
            $c+=1;
            $total+=$row->subtotal;
        }

        $data['datos']=$fila;
        $data['total']=$total;
        $data['empleado']=Empleado::findOrFail($venta->id_empleado);
        return json_encode($data);
       // return view('compras/detalle',compact('detalle'));     
    }

    public function destroy(Venta $venta)
    {
        $data['error']=0;
        $data['mensaje']='';
        if($venta->estado==0){
            $data['error']=1;
            $data['mensaje']='no se puede eliminar 2 veces';
            return json_encode($data);
        }
        $codigo=app(PlanpagoController::class)->comprobar_eliminar_venta($venta->id);
        
        if($venta->tipo_pago==0 && $codigo!=''){
            $data['error']=1;
            $data['mensaje']='esta venta al credito a sido ya pagada y completada no puede eliminar VERIFIQUE EN EL PLAN DE PAGO CODIGO= '.$codigo;
            return json_encode($data);
        }
        $verificar_caja=Caja::findOrFail(1);
        if($verificar_caja->estado==0){
            $data['error']=1;
            $data['mensaje']='la caja de inventario a sido cerrado preguntar al encargado';
            return json_encode($data);
        }
        $detalle=DetalleVenta::all()->where('id_venta', '=',$venta->id);
        foreach ($detalle as $row){
            $producto_almacen=ProductoAlmacen::all()
            ->where('id_producto','=',$row->id_producto)
            ->where('id_almacen','=',$row->id_almacen)
            ->first();
            $nuevo_stock=$producto_almacen->stock + $row->cantidad;
            $producto_almacen->stock=$nuevo_stock;
            $producto_almacen->update(); 
        }
        
        $venta->estado=0;
        $venta->update(); 
        $plan_detalle=detalle_plan::where('id_venta','=',$venta->id)->first();
        if(isset($plan_detalle)){
            if($plan_detalle->estado!=0){
                $plan_detalle->estado=0;
                $plan_detalle->update();
                $plan=planpago::findOrFail($plan_detalle->id_plan);
                $plan->monto_total-=($venta->monto_total-$venta->descuento);
                $plan->update();
            }
        }

        return json_encode($data);

    }

    public function ssesion_monto($monto)
    {
        $data['error']=0;
        $data['mensaje']='';
        $suma=TemporalVenta::where('codigo','=',session('id_venta'))->sum('sub_total');
        if($monto>0 && $monto!= ''){
            if($monto >= $suma){
                session(['monto_cliente'=>$monto]);
            }else{
                $data['error']=2;
                $data['mensaje']="el monto del cliente tiene q ser mayor o igual al total de la venta"; 
            }
        }else{
            $data['error']=1;
            $data['mensaje']="monto invalido para la venta"; 
        }
        return json_encode($data);
    }

    public function pdf_detalle(Venta $venta){
        $sql = "SELECT p.nombre as nombre_producto , a.sigla as nombre_almacen, dv.cantidad ,dv.precio_unidad,dv.subtotal
        FROM detalle_ventas dv ,ventas v, producto_almacens pa, productos p, almacens a
        WHERE 
        dv.id_venta=v.id AND
        dv.id_producto=pa.id_producto AND
        dv.id_almacen=pa.id_almacen AND
        pa.id_producto=p.id AND
        pa.id_almacen=a.id AND
        v.id=$venta->id
        ";
        $empleado=Empleado::all()->where('id_usuario','=',$venta->id_empleado)->first();
        $cliente=Cliente::all()->where('id','=',$venta->id_cliente)->first();
        if(isset($cliente)){
            $nombre_cliente=$cliente->nombre.' '.$cliente->apellidos;
            $carnet=$cliente->ci;
            $telefono=$cliente->telefono;
        }else{
            $nombre_cliente='publico general';
            $carnet='';
            $telefono='';
        }
        $configuracion=Configuracion::all()->first();
        $detalle = DB::select($sql);
        if($venta->estado==1){
            $estado="Completado";
        }else{
            if($venta->estado==-1){
                $estado="Incompleto";
            }else{
                $estado="Cancelado";  
            }
        }
            
        $fecha=$venta->fecha;
        $hora=$venta->hora;
        $total=$venta->monto_total;
        $pdf = new Fpdf('P','mm',array(200,200));
        
            $sw=1;
            $contador = 1;
            $color=0;
            foreach ($detalle as $row){
                if ($sw==1){
                    $pdf->AddPage();
                    $pdf->SetMargins(5,5,5);
                    $pdf->SetTitle("Detalle Venta");
                    $pdf->SetFont('Arial','B',16);
                    $pdf->image(asset('vendor/adminlte/dist/img/AdminLTELogo.png'),10,5,10,10,'PNG');
                    $pdf->Cell(190,4,'',0,1,'C');
                    $pdf->Cell(190,4,'Nota De Venta',0,1,'C');
                    $pdf->Ln(6);
                    $pdf->SetFont('Arial','B',10);
                    $pdf->SetFont('Arial','',9);
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(17,5,utf8_decode('Dirección: '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,$configuracion->direccion,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(22,5,utf8_decode('Fecha y hora: '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,$fecha.' '.$hora,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(22,5,utf8_decode('Remitente: '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,''.$empleado->nombre.''.' '.''.$empleado->apellidos.'',0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(15,5,'Codigo: ',0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(30,5,$venta->codigo,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(15,5,'Cliente: ',0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(30,5,$nombre_cliente.'  ci: '.$carnet.' cel: '.$telefono,0,1,'L');
                   // $pdf->SetFont('Arial','',9); 
                    $pdf->SetFont('Arial','B',11);
                    $pdf->Ln(10);
                    $pdf->SetFillColor(2,157,200);//Fondo verde de celda
                    $pdf->SetTextColor(240, 255, 240); //Letra color blanco
                    $pdf->Cell(10,5,'Nº',1,0,'L',true);
                    $pdf->Cell(95,5,'Producto',1,0,'L',true);
                    $pdf->Cell(20,5,'Almacen',1,0,'L',true);
                    $pdf->Cell(20,5,'precio v',1,0,'L',true);
                    $pdf->Cell(20,5,'cantidad',1,0,'L',true);
                    $pdf->MultiCell(25,5,utf8_decode('sub total'),1,1,'L',true);
                    $pdf->SetFont('Arial','',11);
                   // $pdf->Ln(5);
                    $sw=0;
                }

                if($color==1){
                $pdf->SetFillColor(229, 232, 232 ); //gris tenue de cada fila
                $pdf->SetTextColor(3, 3, 3); //Color del texto: Negro
                $color=0;
                }else{
                $pdf->SetFillColor(255, 255, 255 ); //blanco tenue de cada fila
                $pdf->SetTextColor(3, 3, 3); //Color del texto: Negro
                $color=1;
                }

                $pdf->Cell(10,5,$contador,'LR',0,'L',true);
                $pdf->Cell(95,5,$row->nombre_producto,'LR',0,'L',true);
                $pdf->Cell(20,5,$row->nombre_almacen,'LR',0,'L',true);
                $pdf->Cell(20,5,$row->precio_unidad.' BS','LR',0,'L',true);
                $pdf->Cell(20,5,$row->cantidad,'LR',0,'L',true);
                $pdf->Cell(25,5,number_format($row->subtotal,2,'.','').' bs','LR',1,'L',true); // L= IZQUIERDA R= DERECHA
              
                if ($contador%19==0){$sw=1;}
                $contador++;
            }
           
            $pdf->ln();
            $pdf->SetFont('Arial','',12);
            $pdf->Cell(17,5,$configuracion->leyenda,0,1,'L');
            $pdf->Cell(30,5,utf8_decode('interes: '),0,0,'L');
            $pdf->Cell(20,5,number_format($venta->interes,2,'.','').' bs',0,1,'L');
            $pdf->Cell(30,5,utf8_decode('descuento: '),0,0,'L');
            $pdf->Cell(20,5,number_format($venta->descuento,2,'.','').' bs',0,1,'L');
            $pdf->Cell(30,5,utf8_decode('total: '),0,0,'L');
            $pdf->Cell(20,5,number_format($total,2,'.','').' bs',0,1,'L');
            $pdf->Cell(30,5,utf8_decode('Total a Pagar: '),0,0,'L');
          
            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(20,5,number_format($total+$venta->interes-$venta->descuento,2,'.','').' bs',0,1,'L');
            $pdf->ln();
            
            
            $pdf->Cell(20,5,$estado,0,1,'L');
            $pdf->SetFont('Arial','',10);
            
            $pdf->Output('I','detalle_venta'.$fecha.'_'.$hora.'.pdf');

    }

    public function ticket(Venta $venta){
        $sql = "SELECT p.nombre as nombre_producto , a.nombre as nombre_almacen, dv.cantidad ,dv.precio_unidad,dv.subtotal
        FROM detalle_ventas dv ,ventas v, producto_almacens pa, productos p, almacens a
        WHERE 
        dv.id_venta=v.id AND
        dv.id_producto=pa.id_producto AND
        dv.id_almacen=pa.id_almacen AND
        pa.id_producto=p.id AND
        pa.id_almacen=a.id AND
        v.id=$venta->id
        ";
        $empleado=Empleado::all()->where('id_usuario','=',$venta->id_empleado)->first();
        $cliente=Cliente::all()->where('id','=',$venta->id_cliente)->first();
        if(isset($cliente)){
            $nombre_cliente=$cliente->nombre.' '.$cliente->apellidos;
            $carnet=$cliente->ci;
            $telefono=$cliente->telefono;
        }else{
            $nombre_cliente='publico general';
            $carnet='';
            $telefono='';
        }
        
        if($venta->tipo_pago==1){
            $tipo_pago="CONTADO";
        }else{
            $tipo_pago="CREDITO";
        }
        $configuracion=Configuracion::all()->first();
        $detalle = DB::select($sql);
        if($venta->estado==1){
            $estado="Completado";
        }else{
            if($venta->estado==-1){
                $estado="Incompleto";
            }else{
                $estado="Cancelado";  
            }
        }
        $fecha=$venta->fecha;
        $hora=$venta->hora;
        $total=$venta->monto_total;
        $dimension=count($detalle)*9;

    $pdf = new Fpdf('P','mm',array(80,(160+$dimension)));
    $pdf->SetMargins(4,10,4);
    $pdf->AddPage();

    $pdf->SetTitle("Detalle Venta");
    $pdf->image(asset('vendor/adminlte/dist/img/AdminLTELogo.png'),4,10,10,10,'PNG');
    
    # Encabezado y datos de la empresa #
    $pdf->SetFont('Arial','B',10);
    $pdf->SetTextColor(0,0,0);
    $pdf->MultiCell(0,5,utf8_decode(strtoupper($configuracion->nombre." ".$configuracion->nombre2)),0,'C',false);
    $pdf->SetFont('Arial','',9);
    $pdf->MultiCell(0,5,utf8_decode("COD: ".$venta->codigo),0,'C',false);
    $pdf->MultiCell(0,5,utf8_decode("Direccion: ".$configuracion->direccion),0,'C',false);
    $pdf->MultiCell(0,5,utf8_decode("Teléfono: ".$configuracion->telefono),0,'C',false);
    $pdf->MultiCell(0,5,utf8_decode("Email: ".$configuracion->correo),0,'C',false);

    $pdf->Ln(1);
    $pdf->Cell(0,3,utf8_decode("------------------------------------------------------"),0,0,'C');
    $pdf->Ln(5);

   // $pdf->MultiCell(0,5,utf8_decode("Fecha: ".date("d/m/Y", strtotime("13-09-2022"))." ".date("h:s A")),0,'C',false);
    $pdf->MultiCell(0,5,utf8_decode("Fecha: ".$fecha." ".$hora ),0,'C',false);
    //$pdf->MultiCell(0,5,utf8_decode("Caja Nro: 1"),0,'C',false);
    $pdf->MultiCell(0,5,utf8_decode("Cajero: ".$empleado->nombre." ".$empleado->apellidos),0,'C',false);
    $pdf->SetFont('Arial','',9);

    $pdf->Ln(1);
    $pdf->Cell(0,3,utf8_decode("------------------------------------------------------"),0,0,'C');
    $pdf->Ln(5);

    $pdf->MultiCell(0,5,utf8_decode("Cliente: ".$nombre_cliente),0,'C',false);
    if(isset($cliente)){
    $pdf->MultiCell(0,5,utf8_decode("Documento: ".$carnet),0,'C',false);
    $pdf->MultiCell(0,5,utf8_decode("Teléfono: ".$telefono),0,'C',false);
    }
    //$pdf->MultiCell(0,5,utf8_decode("Dirección: ".$cliente->direccion),0,'C',false);

    $pdf->Ln(1);
    $pdf->Cell(0,5,utf8_decode("-------------------------------------------------------------------"),0,0,'C');
    $pdf->Ln(3);

    # Tabla de productos #
    $pdf->Cell(10,5,utf8_decode("Cant."),0,0,'C');
    $pdf->Cell(19,5,utf8_decode("Precio"),0,0,'C');
    $pdf->Cell(15,5,utf8_decode("Desc."),0,0,'C');
    $pdf->Cell(28,5,utf8_decode("Total"),0,0,'C');

    $pdf->Ln(3);
    $pdf->Cell(72,5,utf8_decode("-------------------------------------------------------------------"),0,0,'C');
    $pdf->Ln(3);


    foreach ($detalle as $row){
        $pdf->MultiCell(0,4,utf8_decode($row->nombre_producto." : ".$row->nombre_almacen),0,'C',false);
        $pdf->Cell(10,4,utf8_decode($row->cantidad),0,0,'C');
        $pdf->Cell(19,4,utf8_decode($row->precio_unidad." bs"),0,0,'C');
        $pdf->Cell(19,4,utf8_decode("0 bs"),0,0,'C');
        $pdf->Cell(28,4,number_format($row->subtotal,2,'.','')." bs",0,0,'C');
       // $pdf->Ln(4);
       // $pdf->MultiCell(0,4,utf8_decode("Garantía de fábrica: 2 Meses"),0,'C',false);
        $pdf->Ln(5);
    }
    /*----------  Detalles de la tabla  ----------*/
   
    /*----------  Fin Detalles de la tabla  ----------*/



    $pdf->Cell(72,5,utf8_decode("-------------------------------------------------------------------"),0,0,'C');

    $pdf->SetFont('Arial','',7);
    $pdf->Ln(3);

    $pdf->Cell(18,5,utf8_decode(""),0,0,'C');
    $pdf->Cell(22,5,utf8_decode("TOTAL A PAGAR"),0,0,'C');
    $pdf->SetFont('Arial','B',7);
    $pdf->Cell(32,5,utf8_decode($total-$venta->descuento+$venta->interes." bs"),0,0,'C');
    $pdf->SetFont('Arial','',7);
    if($venta->descuento>0){
        $pdf->Ln(3);
        $pdf->Cell(18,5,utf8_decode(""),0,0,'C');
        $pdf->Cell(22,5,utf8_decode("DESCUENTO"),0,0,'C');
        $pdf->Cell(32,5,utf8_decode($venta->descuento." bs"),0,0,'C');
    }
   
    if($venta->monto_cliente-$total>0){
        $pdf->Ln(3);
        $pdf->Cell(18,5,utf8_decode(""),0,0,'C');
        $pdf->Cell(22,5,utf8_decode("TOTAL PAGADO"),0,0,'C');
        $pdf->Cell(32,5,utf8_decode($venta->monto_cliente." bs"),0,0,'C');
        $pdf->Ln(3);
        $pdf->Cell(18,5,utf8_decode(""),0,0,'C');
        $pdf->Cell(22,5,utf8_decode("CAMBIO"),0,0,'C');
        $pdf->Cell(32,5,utf8_decode($venta->monto_cliente-($total-$venta->descuento+$venta->interes)." bs"),0,0,'C'); 
    }
    
   /* if($venta->tipo_pago==0){
        $pdf->Ln(4);
        $pdf->Cell(18,5,utf8_decode(""),0,0,'C');
        $pdf->Cell(22,5,utf8_decode("INTERES"),0,0,'C');
        $pdf->Cell(32,5,utf8_decode($venta->interes." bs"),0,0,'C');
    }*/
    $pdf->Ln(3);
    $pdf->Cell(18,5,utf8_decode(""),0,0,'C');
    $pdf->Cell(22,5,utf8_decode("TOTAL"),0,0,'C');
    $pdf->Cell(32,5,utf8_decode($total." bs"),0,0,'C');
    $pdf->Ln(3);
    $pdf->SetFont('Arial','B',7);
    $pdf->Cell(18,5,utf8_decode(""),0,0,'C');
    $pdf->Cell(22,5,utf8_decode($tipo_pago),0,0,'C');
    $pdf->Cell(32,5,utf8_decode($estado),0,0,'C');
    $pdf->SetFont('Arial','',7);

    $pdf->Ln(5);
    $pdf->SetFont('Arial','',8);

    $pdf->MultiCell(0,5,utf8_decode("*** Se le agradece su preferencia. Para poder realizar un reclamo o devolución debe de presentar este ticket con una razon justificable ***"),0,'C',false);

    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(0,7,utf8_decode($configuracion->leyenda),'',0,'C');
    $pdf->Ln(9);
    $pdf->Output('I','Ticket'.$fecha.'_'.$hora.'.pdf',true);
    }

    public function imprimir($id_venta){
        return view('ventas/ver_pdf',compact('id_venta'));
    }

}
