<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\detalle_plan;
use App\Models\Empleado;
use App\Models\planpago;
use App\Models\User;
use App\Models\Venta;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('clientes/index');
    }

    public function store(Request $request)
    {
        $data['error']=0;
        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'apellidos' => 'required',
            'sexo' => 'required',
            'edad' => 'required',
            'nro_carnet' => 'required',
            //'telefono' => 'required'
        ]);

        if($validator->fails())
        {
            $data=['error'=>'1','mensaje'=>$validator->errors()->all()]; 
            return $data;
        }
        $cliente=Cliente::all()->where('ci','=',$request->nro_carnet)->first();
        if(!$cliente){
            $registro= new Cliente();
            $registro->nombre=$request->nombre;
            $registro->apellidos=$request->apellidos;
            $registro->sexo=$request->sexo;
            $registro->ci=$request->nro_carnet;
            $registro->edad=$request->edad;
            $registro->telefono=$request->telefono;
            $registro->save();
        }else{
            $data['error']=1;
            $data['mensaje']="el numero de carnet tiene q ser unico";
        } 
        return $data;
    }

    public function show(Cliente $cliente)
    {
        $data['cliente']=$cliente;
        return json_encode($data);
    }

    public function buscar_por_ci( $ci)
    {
        $data['error']=0;
        
        $data['cliente']=Cliente::all()->where('ci','=',$ci)->first();;
        if(!isset($data['cliente'])){
           $data['error']=1;
        }
        
        return json_encode($data);
    }

   public function autocompleteData(Request $request){

    $returnData = array();
    $valor=$request->term;

   $clientes = Cliente::where('nombre','LIKE','%'.$valor.'%')->where('estado','=',1)->get();
   // $clientes = Cliente::all();
    if(!empty($clientes)){
        foreach ($clientes as $row) {
            $data['id'] = $row['id'];
            $data['value'] = $row['nombre'].' '.$row['apellidos'];
            array_push($returnData,$data);
        }
    }
    return response()->json($returnData);
  
   }

    public function update(Request $request, Cliente $cliente)
    {
        $data['error']=0;
        $validator = Validator::make($request->all(), [
            'Mnombre' => 'required',
            'Mapellidos' => 'required',
            'Msexo' => 'required',
            'Medad' => 'required',
            'Mnro_carnet' => 'required',
            //'telefono' => 'required'
        ]);

        if($validator->fails())
        {
            $data=['error'=>'1','mensaje'=>$validator->errors()->all()]; 
            return $data;
        }
        if($request->Mnro_carnet == $cliente->ci){
            $registro= $cliente;
            $registro->nombre=$request->Mnombre;
            $registro->apellidos=$request->Mapellidos;
            $registro->sexo=$request->Msexo;
            $registro->ci=$request->Mnro_carnet;
            $registro->edad=$request->Medad;
            $registro->telefono=$request->Mtelefono;
            $registro->update();
        }else{
            $cliente2=Cliente::all()->where('ci','=',$request->Mnro_carnet)->first();
            if(!$cliente2){
                $registro= $cliente;
                $registro->nombre=$request->Mnombre;
                $registro->apellidos=$request->Mapellidos;
                $registro->sexo=$request->Msexo;
                $registro->ci=$request->Mnro_carnet;
                $registro->edad=$request->Medad;
                $registro->telefono=$request->Mtelefono;
                $registro->update();
            }else{
                $data['error']=1;
                $data['mensaje']="el numero de carnet ya esta en uso";
            }
        } 
        return $data;
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->estado=0;
        $cliente->update();
    }

    public function restore(Cliente $cliente)
    {
        $cliente->estado=1;
        $cliente->update();
    }

    public function datos()
    {  
        $cliente=Cliente::select(
            'clientes.*'
        );

        return DataTables::of($cliente)
            // anadir nueva columna botones
           ->addColumn('actions', function($cliente){
   
            $css_btn_edit= config('adminlte.classes_btn_editar') ;
            $css_btn_delete= config('adminlte.classes_btn_eliminar') ;
            $css_btn_restaurar= config('adminlte.classes_btn_restaurar') ;
             $btn_group='<div class="btn-group" role="group">
             <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <i class="far fa-clipboard"></i>
             </button>
             <div class="dropdown-menu">';
             $btn_credito_servicios_taller='<a class="dropdown-item"  title="Creditos caja taller" href="'.asset('general/deuda/'.'2'.'/'.$cliente->id).'" >servicio taller</a>';
             $btn_credito_servicios_grua='<a class="dropdown-item"  title="Creditos caja grua" href="'.asset('general/deuda/'.'3'.'/'.$cliente->id).'" >servicio Grua</a>';
             $btn_credito_servicios_maquinaria='<a class="dropdown-item"  title="Creditos caja grua" href="'.asset('general/deuda/'.'4'.'/'.$cliente->id).'" >servicio Maquinaria</a>';
             $btn_credito_servicios_lab='<a class="dropdown-item"  title="Creditos caja grua" href="'.asset('general/deuda/'.'5'.'/'.$cliente->id).'" >servicio LAB</a>';
             $btn_credito_servicios_ganaderia='<a class="dropdown-item"  title="Creditos caja grua" href="'.asset('general/deuda/'.'6'.'/'.$cliente->id).'" >servicio Ganaderia</a>';


            $btn_group.=$btn_credito_servicios_taller.$btn_credito_servicios_grua.$btn_credito_servicios_maquinaria.$btn_credito_servicios_lab.$btn_credito_servicios_ganaderia;

              
            $btn_group.='</div>
             </div>';
             $btn_credito='<a class="btn btn-sm btn-dark" rel="tooltip" data-placement="top" title="Creditos" href="'.route('venta.deuda',$cliente->id).'" ><i class="far fa-clipboard"></i></a>';
             $btn_editar='<a class="btn '.$css_btn_edit.'  " rel="tooltip" data-placement="top" title="Editar" onclick="Modificar('.$cliente->id.')" ><i class="far fa-edit"></i></a>';
             $btn_eliminar='<a class="btn '.$css_btn_delete.'" rel="tooltip" data-placement="top" title="Eliminar" onclick="Eliminar('.$cliente->id.')"><i class="far fa-trash-alt"></i></a>';
             $btn_restaurar='<a class="btn '.$css_btn_restaurar.'" rel="tooltip" data-placement="top" title="Eliminar" onclick="Restaurar('.$cliente->id.')"><i class="far fa-trash-alt"></i></a>';
             $btn_plan='<a class="btn btn-sm btn-warning" rel="tooltip" data-placement="top" title="ver plan de pagos realizados" onclick="mostrar_plan_pagos('.$cliente->id.')" ><i class="far fa-clipboard"></i></a>';
 
             $btn= '<div class="text-right">  <div class="btn-group btn-group-sm ">';
                if($cliente->estado==1){
                   $btn= $btn.$btn_plan.$btn_editar.$btn_eliminar;
                }else{
                   $btn= $btn.$btn_plan.$btn_credito.$btn_restaurar;
                }
                $btn=$btn.'</div> </div> ';
             return  $btn;
           })
           ->addColumn('foto' , function($cliente){
            $user=User::all()->where('id','=',$cliente->id_user)->first();
            if($user){
                $imagen='img/usuarios/'.$user->id.'.png';
                if (!file_exists($imagen)) {
                 $imagen = "img/usuarios/user.png";
                }
                $url=asset($imagen.'?'.time());
            }else{
                $imagen = "img/usuarios/user.png";
                $url=asset($imagen.'?'.time());
            }
            $r="'";
            return '<a class="btn btn-sm" rel="tooltip" data-placement="top" title="Ver imagen" onclick="Imagen('.$r.$imagen.$r.')">  <div class="text-center" > <img width="30" height="30" src="'.$url.'"/> </div> </a>';
         
           })
           ->addColumn('estado', function($cliente){
            if($cliente->estado==0){
                $span= '<span class="badge bg-warning">inactivo</span>';
            }
            if($cliente->estado==1){
                $span= '<span class="badge bg-success">activo</span>';
            }
            return  $span;
            })
           ->rawColumns(['actions','foto','estado']) // incorporar columnas
           ->make(true); // convertir a codigo
    }


    public function cliente_pagos_venta($id_cliente){
        $registro=Cliente::findOrFail($id_cliente);
        $nombre_cliente=$registro->nombre. ' '.$registro->apellidos.'  ci :'.$registro->ci;

        $sql="SELECT  * 
        FROM planpagos p
        WHERE p.id_cliente=$id_cliente
        ORDER BY p.created_at desc
        LIMIT 5";
        $datos=DB::select($sql);

        $fila = '';
      
        
        foreach ($datos as $row){
            
            $fila .= "<tr>";
            $fila .= "<td>".$row->codigo."</td>";
            $fila .= "<td>".$row->descripcion."</td>";
            $fila .= "<td>".$row->created_at."</td>";
            $fila .= "<td>".$row->monto_total.' bs'."</td>";
            $fila .= "<td>".$row->interes.' bs'."</td>";
            $empleado=Empleado::where('id_usuario','=',$row->id_usuario)->first();
            if(isset($empleado)){
                $nombre_completo=$empleado->nombre.' '.$empleado->apellidos;
            }else{
                $nombre_completo='---';
            }
            $fila .= "<td>".$nombre_completo."</td>";
            if($row->estado==1){
                $fila .= "<td> <span class=\"badge bg-success\">Completado</span> </td>";  
              }else{
                $fila .= "<td> <span class=\"badge bg-danger\">Cancelado</span> </td>";  
              }
            $btn_cancelar="<a class=\"btn btn-sm btn-warning\" onclick=\"Cancelar('$row->id')\"  title=\"CANCELAR PLAN DE PAGO\" ><i class=\"far fa-check-circle\"></i></a>";
            
            $btn_detalle_pdf="<a class=\"btn btn-sm btn-danger\" onclick=\"Mostrar_pdf_plan(".$row->id.")\" title=\"PDF DE PLAN DE PAGO\" ><i class=\"far fa-file-pdf\"></i></a>";

 
            $fila .= "<td><div class=\"text-right\"><div class=\"btn-group btn-group-sm \">";
            if(auth()->user()->can('plan.eliminar')){
                $fila .= $btn_cancelar;
            }
            $fila.=$btn_detalle_pdf;
            $fila.="</div></div></td>";
            $fila .= "</tr>";
        }

        $data['fila']=$fila;
        $data['nombre_cliente']='Plan de pagos '.$nombre_cliente;
        $data['id_cliente']=$id_cliente;
      
        return json_encode($data);
        //return view('ventas/deuda_mes',compact('datos','nombre_cliente','id_cliente'));
    }
    
    public function cancelar_deuda_venta(Request $request){
        $data['error']=0;
        $data['mensaje']='';
        $descripcion_general='';
        $plan_pago=new planpago();
        $sw=0;
        while($sw==0){
            $codigo_generado = uniqid();
            $existe=planpago::all()->where('codigo','=',$codigo_generado)->first();
            if(!$existe){ $sw=1;}
        }
        $plan_pago->codigo=$codigo_generado;
        $plan_pago->monto_total=0;
        $plan_pago->id_usuario=Auth::user()->id;
        $plan_pago->id_cliente=$request->id_cliente;
        $plan_pago->interes=$request->interes;
        $total=0;
        if($request->opcion_pago==1){
            //pagamos todo
            
            $sql="SELECT * 
            FROM ventas v  
            WHERE v.estado=-1 AND
            v.tipo_pago=0 AND
            v.id_cliente=$request->id_cliente";
            $descripcion_general='SE PAGO TODO LA DEUDA EN GENERAL'; 
        }
        if($request->opcion_pago==2){
            //rango de fecha
            $sql="SELECT * 
            FROM ventas v  
            WHERE v.estado=-1 AND
            v.tipo_pago=0 AND
            v.id_cliente=$request->id_cliente AND
            MONTH(v.fecha)=$request->mes AND 
            YEAR(v.fecha)=$request->anio"; 
            $descripcion_general='SE PAGO LA DEUDA DEL ANIO :'.$request->anio.' MES :'.$request->mes;
        }
        if($request->opcion_pago==3){
            //solo una venta
            $sql="SELECT * 
            FROM ventas v  
            WHERE v.estado=-1 AND
            v.tipo_pago=0 AND
            v.id_cliente=$request->id_cliente AND
            v.id=$request->id_venta";  
            $venta=Venta::findOrFail($request->id_venta);
            $descripcion_general='SE PAGO SOLO DE LA NOTA DE VENTA CON CODIGO = '.$venta->codigo;
        }


        $datos=DB::select($sql);
        if(count($datos)>0){
            $plan_pago->save();     
        }else{
            $data['error']=1;
            $data['mensaje']='ya se encuentra pagado';
        }

        foreach ($datos as $row){
            $detalle= new detalle_plan();
            $detalle->id_venta=$row->id;
            $detalle->id_plan=$plan_pago->id;
            $detalle->save();
            $venta=Venta::findOrFail($row->id);
            $venta->estado=1;
            $venta->fecha_deuda=date("y-m-d");
            $venta->hora_deuda=date("H:i:s");
            $venta->update();
            $total+=($row->monto_total-$row->descuento);
        }
        $plan_pago->descripcion=$descripcion_general;
        $plan_pago->monto_total=$total;
        $plan_pago->update();
        if(isset($plan_pago)){
            $data['id_plan']=$plan_pago->id;
        }else{
            $data['error']=1;
            $data['mensaje']='fallo la operacion';
        }

        return json_encode($data);
    }

    public function cliente_deuda($id_cliente){
        $registro=Cliente::findOrFail($id_cliente);
        $nombre_cliente=$registro->nombre. ' '.$registro->apellidos.'  ci :'.$registro->ci;

        $sql="SELECT va.id_cliente as id_cliente  ,YEAR(va.fecha) as anio_tiempo, MONTH(va.fecha) as mes_tiempo, 
        (
         SELECT SUM(v.monto_total-v.descuento) 
         FROM ventas v 
         WHERE 
         MONTH(v.fecha)=MONTH(va.fecha) AND 
         YEAR(v.fecha)=YEAR(va.fecha) AND
         v.id_cliente= va.id_cliente AND 
         v.tipo_pago=0  AND 
         (v.estado=-1 OR v.estado=1) 
         ) as total_deuda, 
         (
          SELECT SUM(vv.monto_total-vv.descuento) 
          FROM ventas vv 
          WHERE vv.id_cliente=va.id_cliente AND 
          MONTH(vv.fecha)=MONTH(va.fecha) AND 
          YEAR(vv.fecha)=YEAR(va.fecha) AND 
          vv.estado=1 AND 
          vv.tipo_pago=0 
         ) as total_pagado
                FROM ventas va
                WHERE 
                (va.estado=-1  OR va.estado=1) AND
                va.tipo_pago=0 AND
                va.id_cliente=$id_cliente
                GROUP BY anio_tiempo,mes_tiempo,total_pagado,total_deuda,id_cliente
                ORDER BY (anio_tiempo) DESC,(mes_tiempo) DESC,(total_deuda-total_pagado) ASC
                LIMIT 12";
        $datos=DB::select($sql);

        $fila = '';
    
        $total=0;
        foreach ($datos as $row){
            
            $fila .= "<tr>";
            $fila .= "<td>".$row->anio_tiempo."</td>";
            $fila .= "<td>".$row->mes_tiempo."</td>";
            $fila .= "<td>".$row->total_deuda.' bs'."</td>";
            $fila .= "<td>".$row->total_deuda-$row->total_pagado.' bs'."</td>";
            if($row->total_pagado==null){
                $monto=0;
            }else{
                $monto=$row->total_pagado;
            }
            $fila .= "<td>".$monto.' bs'."</td>";
            if(($row->total_deuda-$row->total_pagado)<=0){
              $fila .= "<td> <span class=\"badge bg-success\">Completado</span> </td>";  
            }else{
              $fila .= "<td> <span class=\"badge bg-warning\">Incompleto</span> </td>";  
            }
           // $btn_completar="<a class=\"btn btn-sm btn-success\" onclick=\"completar_credito(".$row->id.','.$aux_fecha.','.$row->monto_total.")\" title=\"cancelar deuda\" ><i class=\"far fa-check-circle\"></i></a>";
            $btn_completar="<a class=\"btn btn-sm btn-success\"  title=\"cancelar deuda de esta fecha\" onclick=\"pagar_todo_venta(".$id_cliente.",".$row->total_deuda-$row->total_pagado.",".$row->anio_tiempo.",".$row->mes_tiempo.",-1,2)\" ><i class=\"far fa-check-circle\"></i></a>";
            
            $btn_detalle="<a class=\"btn btn-sm btn-danger\" onclick=\"Mostrar_notas_ventas(".$id_cliente.",".$row->anio_tiempo.",".$row->mes_tiempo.")\" title=\"mostrar notas de ventas\" ><i class=\"far fa-file-pdf\"></i></a>";

           // $btn_detalle="<a class=\"btn btn-sm btn-danger\" onclick=\"mostrar_detalle(".$id_cliente.")\" title=\"cancelar deuda\" ><i class=\"far fa-file-pdf\"></i></a>";
 
            $fila .= "<td><div class=\"text-right\"><div class=\"btn-group btn-group-sm \">";
            if(auth()->user()->can('venta.credito')){
                $fila .= $btn_completar;
            }
            $fila.=$btn_detalle;
            $fila.="</div></div></td>";
            $fila .= "</tr>";
            $total+=($row->total_deuda-$row->total_pagado);
        }

        $data['fila']=$fila;
        $data['nombre_cliente']=$nombre_cliente;
        $data['id_cliente']=$id_cliente;
        $data['total']=$total.' bs';
        return json_encode($data);
        //return view('ventas/deuda_mes',compact('datos','nombre_cliente','id_cliente'));
    }


    public function clientes_deudores()
    {  
       
        $sql="SELECT c.id,c.nombre,c.apellidos,c.ci,c.telefono,c.edad,c.sexo,c.estado,
        (
        SELECT IFNULL(SUM(ve.monto_total-ve.descuento),0)
            FROM ventas ve
            WHERE ve.id_cliente=c.id AND
            (ve.estado=-1 OR ve.estado=1) AND
            ve.tipo_pago=0
        ) as total_deuda,
        (
        SELECT IFNULL(SUM(vee.monto_total-vee.descuento),0)
            FROM ventas vee
            WHERE vee.id_cliente=c.id AND
            vee.estado=1 AND vee.tipo_pago=0  
        ) as total_pagado
        
        FROM ventas v, clientes c
        WHERE v.id_cliente=c.id AND
        (v.estado=-1) AND
        v.tipo_pago=0
        GROUP BY c.id,c.nombre,c.apellidos,c.ci,c.telefono,c.edad,c.sexo,c.estado,total_pagado,total_deuda
        ORDER BY (total_deuda) desc";
        $cliente=DB::select($sql);

        return DataTables::of($cliente)
            // anadir nueva columna botones
           ->addColumn('actions', function($cliente){
            //$btn_plan='<a class="btn btn-sm btn-dark" rel="tooltip" data-placement="top" title="ver plan de pagos realizados" onclick="mostrar_plan_pagos('.$cliente->id.')" ><i class="far fa-clipboard"></i></a>';
            $btn_credito='<a class="btn btn-sm btn-success" rel="tooltip" data-placement="top" title="Pagar todo" onclick="pagar_todo_venta('.$cliente->id.','.$cliente->total_deuda-$cliente->total_pagado.',-1,-1,-1,1)" ><i class="far fa-clipboard"></i></a>';
            $btn_mostrar_detalles='<a class="btn btn-sm btn-danger" rel="tooltip" data-placement="top" title="Detalles de deuda" onclick="mostrar_deuda_cliente('.$cliente->id.')" ><i class="far fa-clipboard"></i></a>';
            $btn= '<div class="text-right">  <div class="btn-group btn-group-sm ">';
                if(auth()->user()->can('venta.credito')){
                    if($cliente->total_deuda-$cliente->total_pagado>0){
                        $btn= $btn.$btn_credito;
                    }   
                }
                $btn= $btn.$btn_mostrar_detalles;
            
                $btn=$btn.'</div> </div> ';
             return  $btn;
           })

           ->addColumn('nombre_completo', function($cliente){
            return  $cliente->nombre.' '.$cliente->apellidos;
           })
           ->addColumn('total_pagado2', function($cliente){
            if($cliente->total_pagado==null){
                $monto=0;
            }else{
                $monto=$cliente->total_pagado;
            }
            return  $monto.' bs';
            })
            ->addColumn('total_deuda2', function($cliente){
                
                if($cliente->total_deuda==null){
                    $monto=0;
                }else{
                    $monto=$cliente->total_deuda-$cliente->total_pagado;
                }
                return  $monto.' bs';
            })
           
           ->addColumn('estado', function($cliente){
            if(($cliente->total_deuda-$cliente->total_pagado)<=0){
                $span= '<span class="badge bg-success">Completado</span>';
               
            }else{
                $span= '<span class="badge bg-danger">Incompleto</span>';
            }
            return  $span;
            })
           ->rawColumns(['actions','estado']) // incorporar columnas
           ->make(true); // convertir a codigo
    }

    public function deuda_cliente_dias($id_cliente,$anio,$mes){
        $registro=Cliente::findOrFail($id_cliente);
        $nombre_cliente=$registro->nombre. ' '.$registro->apellidos.'  ci :'.$registro->ci;
        $sql2="SELECT va.descuento,va.fecha_deuda,va.hora_deuda, va.id,va.hora ,va.codigo, va.monto_total,va.id_empleado,va.interes, va.fecha,va.estado
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
        
        foreach ($datos as $row){
            $numFila++;
            
            $r=Empleado::findOrFail($row->id_empleado);
            $fila .= "<tr id='fila".$numFila."'>";
            $fila .= "<td>".$numFila."</td>";
            $fila .= "<td>".$row->codigo."</td>";
            $fila .= "<td>".$row->fecha.' '.$row->hora."</td>";
            $fila .= "<td>".$row->fecha_deuda.' '.$row->hora_deuda."</td>";
            $fila .= "<td>".$r->nombre.' '.$r->apellidos."</td>";
            $fila .= "<td title=\" total = $row->monto_total descuento = $row->descuento  \" >".$row->monto_total-$row->descuento.' bs'."</td>";
            $btn_descuento="";
            if($row->descuento>0){
                $btn_descuento="<span class=\"badge bg-primary\">$row->descuento bs</span>";
            }
            if($row->estado==1){
              $fila .= "<td> <span class=\"badge bg-success\">Completado</span> $btn_descuento </td>"; 
            }else{
              $fila .= "<td> <span class=\"badge bg-warning\">Incompleto</span> $btn_descuento </td>";  
            }
            
            $btn_completar="<a class=\"btn btn-sm btn-success\"  title=\"cancelar deuda de esta fecha\" onclick=\"pagar_todo_venta(".$id_cliente.",".$row->monto_total-$row->descuento.",-1,-1,".$row->id.",3)\" ><i class=\"far fa-check-circle\"></i></a>";
            $fila .= "<td><div class=\"text-right\"><div class=\"btn-group btn-group-sm \">";
            if($row->estado==-1 && auth()->user()->can('venta.credito')){
                $fila .= $btn_completar;
            } 
                
            $fila.="<a class=\"btn btn-sm btn-danger\" onclick=\"mostrar_detalle_venta(".$row->id.")\" title=\"pdf venta\" ><i class=\"far fa-file-pdf\"></i></a>

            </div></div></td>";
            $fila .= "</tr>";
        }

        $data['fila']=$fila;
        $data['nombre_cliente']=$nombre_cliente.' lista de nota de venta';
     
        return json_encode($data);
   
    }
}
