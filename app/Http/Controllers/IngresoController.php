<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\ingreso;
use App\Models\planpago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class IngresoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        return view('ingresos/index');
    }

    public function datos_caja($id_caja){
        $caja=Caja::findOrFail($id_caja);
        $fecha_inicial=$caja->fecha_inicio." ".$caja->hora_inicio;
       // $fecha_final=$caja->fecha_final." ".$caja->hora_final;
        $ingresos=ingreso::select(
            'ingresos.*'
        )
        ->where('ingresos.created_at','>=',$fecha_inicial)
        ->where('ingresos.id_caja_secundaria','=',$id_caja)
        ->orderBy('ingresos.created_at','desc');

        return DataTables::of($ingresos)
            // anadir nueva columna botones
           ->addColumn('actions', function($ingresos){
            //$url_detalle=route('venta.pdf',$ingresos->id);
            //$url_ticket=route('venta.ticket',$ventas->id);
           // $url_ticket=route('venta.imprimir',$ingresos->id);
             $btn_eliminar='<a class="btn btn-warning" rel="tooltip" data-placement="top" title="Eliminar" onclick="Eliminar_ingreso('.$ingresos->id.')"><i class="far fa-trash-alt"></i></a>';
                $btn= '<div class="text-right">  <div class="btn-group btn-group-sm ">';
                if(($ingresos->estado==1 || $ingresos->estado==-1 )  && $ingresos->tipo_ingreso!='INICIO INGRESO' && auth()->user()->can('egreso.eliminar') ){
                   $btn= $btn.$btn_eliminar;
                }else{
                   $btn= $btn;
                }
                $btn=$btn.'</div> </div> ';
             return  $btn;
           })
           ->addColumn('estado', function($ingresos){
            if($ingresos->estado==0){
                $span= '<span class="badge bg-danger">Cancelado</span>';
            }
            if($ingresos->estado==1){
                $span= '<span class="badge bg-success">Completado</span>';
            }
            return  $span;
            })
            
            ->addColumn('fecha_hora', function($ingresos){
            
                return  $ingresos->created_at;
            })
            ->addColumn('monto', function($ingresos){
                return  $ingresos->monto_total . " bs";
            })
         
           
            ->addColumn('caja', function($ingresos){
                if($ingresos->id_caja_primaria==null){
                    return $ingresos->tipo_ingreso;
                }else{
                    $caja=Caja::findOrFail($ingresos->id_caja_primaria);
                    return $caja->nombre;
                }
              
            })
            
            
           ->rawColumns(['actions','caja','monto','fecha_hora','estado']) // incorporar columnas
           ->make(true); // convertir a codigo
    }

    public function datos($id_caja){
        $caja=Caja::findOrFail($id_caja);
        $fecha_inicial=$caja->fecha_inicio." ".$caja->hora_inicio;
       // $fecha_final=$caja->fecha_final." ".$caja->hora_final;
        $ingresos=ingreso::select(
            'ingresos.*'
        )
        ->where('ingresos.created_at','>=',$fecha_inicial)
        ->where('ingresos.id_caja_primaria','=',$id_caja)
        ->orderBy('ingresos.created_at','desc');

        return DataTables::of($ingresos)
            // anadir nueva columna botones
           ->addColumn('actions', function($ingresos){
             $btn_eliminar='<a class="btn btn-warning" rel="tooltip" data-placement="top" title="Eliminar" onclick="Eliminar_ingreso_propio('.$ingresos->id.')"><i class="far fa-trash-alt"></i></a>';
                $btn= '<div class="text-right">  <div class="btn-group btn-group-sm ">';
                if(($ingresos->estado==1 || $ingresos->estado==-1) && $ingresos->tipo_ingreso!='INICIO INGRESO' && auth()->user()->can('ingreso.eliminar')){
                   $btn= $btn.$btn_eliminar;
                }else{
                   $btn= $btn;
                }
                $btn=$btn.'</div> </div> ';
             return  $btn;
           })
           ->addColumn('estado', function($ingresos){
            if($ingresos->estado==0){
                $span= '<span class="badge bg-danger">Cancelado</span>';
            }
            if($ingresos->estado==1){
                $span= '<span class="badge bg-success">Completado</span>';
            }
            return  $span;
            })
            
            ->addColumn('fecha_hora', function($ingresos){
            
                return  $ingresos->created_at;
            })
            ->addColumn('monto', function($ingresos){
                return  $ingresos->monto_total . " bs";
            })
         
           
            ->addColumn('caja', function($ingresos){
                if($ingresos->id_caja_secundaria==null){
                    return $ingresos->tipo_ingreso;
                }else{
                    $caja=Caja::findOrFail($ingresos->id_caja_secundaria);
                    return $caja->nombre;
                }
              
            })
            
            
           ->rawColumns(['actions','caja','monto','fecha_hora','estado']) // incorporar columnas
           ->make(true); // convertir a codigo
    }

  

    public function guardar(Request $request,$tipo)
    {   $data['error']=0;
        $data['mensaje']='';
        if(!isset($tipo)){
            $data['error']=1;
            $data['mensaje']='no selecciono ningun tipo por error';
            return json_encode($data);
        }
        $ingreso= new ingreso();
        $sw=0;
        $codigo_generado='';
        $caja_primaria=7;
        while($sw==0){
            $codigo_generado = uniqid();
            $existe=ingreso::all()->where('codigo','=',$codigo_generado)->first();
            if(!$existe){ $sw=1;}
        }
        
        $ingreso->codigo=$codigo_generado;
        $ingreso->descripcion=$request->descripcion;
        $ingreso->monto_total=$request->monto;
        $ingreso->id_caja_primaria=$caja_primaria;
        //$caja_primaria=Caja::findOrFail($caja_primaria);
        $this->recalcular_datos($request->id_caja_secundaria);
        if($tipo==1){
            $ingreso->id_caja_secundaria=$request->id_caja_secundaria;
            $caja_secundaria=Caja::findOrFail($request->id_caja_secundaria);
            if(($caja_secundaria->monto_ingreso)>= $request->monto ){  
                $ingreso->save();
            }else{
                $data['error']=1;
                $data['mensaje']='los ingresos de la '.$caja_secundaria->nombre.' es solo de '.$caja_secundaria->monto_ingreso.' bs no cubre la cantidad de '.$request->monto.' bs';
            }
        }else{
            if($tipo==0){
                $ingreso->id_caja_primaria=$request->id_caja_secundaria;
                $ingreso->tipo_ingreso=$request->tipo_ingreso;  
                $ingreso->save();
            }else{
                $data['error']=1;
                $data['mensaje']='tipo 1 es para la caja general y tipo 0 es sin justificar de que caja vino el dinero'; 
            }
        }
        $this->recalcular_datos($request->id_caja_secundaria);

       
        return json_encode($data);
    }

    public function recalcular_datos($id_caja){
        $caja=Caja::findOrFail($id_caja);
        $fecha_inicial=$caja->fecha_inicio.' '.$caja->hora_inicio;
        $intereses_inventario=planpago::where('created_at','>=',$fecha_inicial)->where('estado','=',1)->sum('interes');

        $sql="SELECT SUM(sg.monto_total) as monto_total,SUM(sg.interes) as interes,
        (
        SELECT SUM(i.monto_total)
        FROM ingresos i
        WHERE i.id_caja_primaria=$caja->id AND
            i.created_at>= '$fecha_inicial' AND
            i.estado=1
        ) as monto_inicio_caja,
        (
         SELECT SUM(v.monto_total)
         FROM compras v
         WHERE v.id_caja = $caja->id AND
         v.estado=1 AND
         concat(v.fecha,' ', v.hora) >= '$fecha_inicial'
        ) as monto_compra,
        (
         SELECT SUM(v.monto_total + v.interes - v.descuento)
         FROM ventas v
         WHERE v.id_caja= $caja->id AND
         v.estado=1 AND
         (concat(v.fecha,' ', v.hora) >= '$fecha_inicial' OR concat(v.fecha_deuda,' ', v.hora_deuda)>= '$fecha_inicial')
        ) as monto_venta,
        (
        SELECT SUM(i.monto_total)
        FROM ingresos i
        WHERE i.id_caja_secundaria=$caja->id AND
            i.created_at>= '$fecha_inicial' AND
            i.estado=1
        ) as monto_gasto,
        (
        SELECT SUM(e.monto_total)
        FROM egresos e
        WHERE e.id_caja_primaria=$caja->id AND
            e.created_at>= '$fecha_inicial' AND
            e.estado=1
        ) as monto_egreso
        FROM servicio_generals sg
        WHERE sg.id_caja=$caja->id AND
        sg.created_at >= '$fecha_inicial' AND
        sg.estado=1";

        //$datos=  DB::select($sql);
        
        $monto_compra=collect(DB::select($sql))->first()->monto_compra;
        $monto_venta=collect(DB::select($sql))->first()->monto_venta;
        $monto_egreso=collect(DB::select($sql))->first()->monto_egreso;
        $monto_servicios=collect(DB::select($sql))->first()->monto_total;
        $monto_interes=collect(DB::select($sql))->first()->interes;
        $monto_gasto_caja_gdt=collect(DB::select($sql))->first()->monto_gasto;
        $monto_inicio_caja=collect(DB::select($sql))->first()->monto_inicio_caja;
        if($monto_compra==null){
            $monto_compra=0;
        }
        if($monto_venta==null){
            $monto_venta=0;
        }
        if($monto_egreso==null){
            $monto_egreso=0;
        }
        if($monto_inicio_caja==null){
            $monto_inicio_caja=0;
        }
        if($monto_gasto_caja_gdt==null){
            $monto_gasto_caja_gdt=0;
        }
        if($monto_servicios==null){
            $monto_servicios=0;
        }
        if($monto_interes==null){
            $monto_interes=0;
        }
        $caja->monto_ingreso=$monto_servicios+$monto_interes-$monto_gasto_caja_gdt+$monto_inicio_caja-$monto_egreso+$monto_venta-$monto_compra;
        $caja->monto_ingreso_caja=$monto_inicio_caja;
        $caja->monto_total_generado=$monto_servicios+$monto_interes+$monto_venta;
        $caja->monto_egreso=$monto_gasto_caja_gdt+$monto_egreso+$monto_compra;
        if($caja->id==1){
            // interes de los plan de pagos
            $caja->monto_total_generado+=$intereses_inventario;
            $caja->monto_ingreso+=$intereses_inventario;
        }
        $caja->update();

    }


    public function guardar22(Request $request,$tipo)
    {   $data['error']=0;
        $data['mensaje']='';
        $ingreso= new ingreso();
        $sw=0;
        $codigo_generado='';
        $caja_primaria=7;
        while($sw==0){
            $codigo_generado = uniqid();
            $existe=ingreso::all()->where('codigo','=',$codigo_generado)->first();
            if(!$existe){ $sw=1;}
        }
        
        $ingreso->codigo=$codigo_generado;
        $ingreso->descripcion=$request->descripcion;
        $ingreso->monto_total=$request->monto;
        $ingreso->id_caja_primaria=$caja_primaria;
        $caja_primaria=Caja::findOrFail($caja_primaria);
        if($tipo==1){
            $ingreso->id_caja_secundaria=$request->id_caja_secundaria;
            
            $caja_secundaria=Caja::findOrFail($request->id_caja_secundaria);
            if(($caja_secundaria->monto_ingreso-$caja_secundaria->monto_egreso)>= $request->monto ){  
                $caja_primaria->monto_ingreso+=$request->monto;
                $caja_primaria->update();
                $caja_secundaria->monto_egreso+=$request->monto;
                $caja_secundaria->update();
                $ingreso->save();
            }else{
                $data['error']=1;
                $data['mensaje']='los fondos no son suficientes '.$caja_secundaria->nombre.' '.$caja_secundaria->monto_ingreso-$caja_secundaria->monto_egreso.' bs';
            }
        }else{
            $caja_primaria->monto_ingreso+=$request->monto;
            $ingreso->tipo_ingreso=$request->tipo_ingreso;  
            $caja_primaria->update(); 
            $ingreso->save();
        }
       
        return json_encode($data);
    }

  
    public function eliminar(ingreso $ingreso)
    {   $data['error']=0;
        $data['mensaje']='';
        app(IngresoController::class)->recalcular_datos($ingreso->id_caja_primaria);
        $caja_primaria=Caja::findOrFail($ingreso->id_caja_primaria);
       
       // $caja_primaria=Caja::findOrFail($servicioGeneral->id_caja);
        if($caja_primaria->estado==0){
            $data['error']=1;
            $data['mensaje']='la '.$caja_primaria->nombre .' a sido cerrado preguntar al encargado';
            return json_encode($data);
        }
        if(($caja_primaria->monto_ingreso)>=$ingreso->monto_total){
            $ingreso->estado=0;
            $ingreso->update();
        }else{
            $data['error']=1;
            $data['mensaje']='La '.$caja_primaria->nombre.' solo dispone de '. ($caja_primaria->monto_ingreso) .' bs no puede cancelar sino cubre el gasto de '.$ingreso->monto_total ;
        }
        
        return json_encode($data);
    }

    
    public function edit(ingreso $ingreso)
    {
        //
    }

  
    public function update(Request $request, ingreso $ingreso)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ingreso  $ingreso
     * @return \Illuminate\Http\Response
     */
    public function destroy(ingreso $ingreso)
    {
        //
    }
}
