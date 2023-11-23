<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\egresos;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EgresosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view('egresos/index');
    }

    public function datos($id_caja){
        $caja=Caja::findOrFail($id_caja);
        $fecha_inicial=$caja->fecha_inicio." ".$caja->hora_inicio;
       // $fecha_final=$caja->fecha_final." ".$caja->hora_final;
        $egresos=egresos::select(
            'egresos.*'
        )
        ->where('egresos.created_at','>=',$fecha_inicial)
        ->where('egresos.id_caja_primaria','=',$id_caja)
        ->orderBy('egresos.created_at','desc');

        return DataTables::of($egresos)
            // anadir nueva columna botones
           ->addColumn('actions', function($egresos){
            //$url_detalle=route('venta.pdf',$ingresos->id);
            //$url_ticket=route('venta.ticket',$ventas->id);
           // $url_ticket=route('venta.imprimir',$ingresos->id);
             $btn_eliminar='<a class="btn btn-warning" rel="tooltip" data-placement="top" title="Eliminar" onclick="Eliminar_egreso('.$egresos->id.')"><i class="far fa-trash-alt"></i></a>';
                $btn= '<div class="text-right">  <div class="btn-group btn-group-sm ">';
                if(($egresos->estado==1 || $egresos->estado==-1) && auth()->user()->can('egreso.eliminar') ){
                   $btn= $btn.$btn_eliminar;
                }else{
                   $btn= $btn;
                }
                $btn=$btn.'</div> </div> ';
             return  $btn;
           })
           ->addColumn('estado', function($egresos){
            if($egresos->estado==0){
                $span= '<span class="badge bg-danger">Cancelado</span>';
            }
            if($egresos->estado==1){
                $span= '<span class="badge bg-success">Completado</span>';
            }
            return  $span;
            })
            
            ->addColumn('fecha_hora', function($egresos){
            
                return  $egresos->created_at;
            })
            ->addColumn('monto', function($egresos){
                return  $egresos->monto_total . " bs";
            })
         
           
            ->addColumn('caja', function($egresos){
                if($egresos->tipo_egreso!=null){
                    return $egresos->tipo_egreso;
                }else{
                    return 'ninguno';
                }
              
            })
            
            
           ->rawColumns(['actions','caja','monto','fecha_hora','estado']) // incorporar columnas
           ->make(true); // convertir a codigo
    }

    
    public function guardar(Request $request)
    {   $data['error']=0;
        $data['mensaje']='';
        $egreso= new egresos();
        $sw=0;
        $codigo_generado='';
        $caja_primaria=$request->id_caja_secundaria;
        while($sw==0){
            $codigo_generado = uniqid();
            $existe=egresos::all()->where('codigo','=',$codigo_generado)->first();
            if(!$existe){ $sw=1;}
        }
        $egreso->codigo=$codigo_generado;
        $egreso->descripcion=$request->descripcion;
        $egreso->monto_total=$request->monto;
        $egreso->tipo_egreso=$request->tipo_egreso;
        $egreso->id_caja_primaria=$caja_primaria;
        app(IngresoController::class)->recalcular_datos($caja_primaria);
        $caja=Caja::findOrFail($caja_primaria);
        if(($caja->monto_ingreso)>=$request->monto){
            $egreso->save();
        }else{
            $data['error']=1;
            $data['mensaje']='los fondos no son suficientes para cubrir ese gasto SOLO DISPONE DE '.$caja->monto_ingreso.' bs';
        }
        app(IngresoController::class)->recalcular_datos($caja_primaria);
        return json_encode($data);
    }

    public function guardar22(Request $request)
    {   $data['error']=0;
        $data['mensaje']='';
        $egreso= new egresos();
        $sw=0;
        $codigo_generado='';
        $caja_primaria=7;
        while($sw==0){
            $codigo_generado = uniqid();
            $existe=egresos::all()->where('codigo','=',$codigo_generado)->first();
            if(!$existe){ $sw=1;}
        }
        $egreso->codigo=$codigo_generado;
        $egreso->descripcion=$request->descripcion;
        $egreso->monto_total=$request->monto;
        $egreso->tipo_egreso=$request->tipo_egreso;
        $egreso->id_caja_primaria=$caja_primaria;
        $caja_primaria=Caja::findOrFail($caja_primaria);
        if(($caja_primaria->monto_ingreso-$caja_primaria->monto_egreso)>=$request->monto){
           // $caja_primaria->monto_ingreso=$request->monto;
            $caja_primaria->monto_egreso+=$request->monto;
            $caja_primaria->update();
            $egreso->save();
        }else{
            $data['error']=1;
            $data['mensaje']='los fondos no son suficientes para cubrir ese gasto SOLO DISPONE DE '.$caja_primaria->monto_ingreso-$caja_primaria->monto_egreso.' bs';
        }
       
        return json_encode($data);
    }
    
    public function eliminar(egresos $egresos)
    {
        $data['error']=0;
        $data['mensaje']='';
        app(IngresoController::class)->recalcular_datos($egresos->id_caja_primaria);
        $egresos->estado=0;
        $egresos->update();
        return json_encode($data);

    }

    public function edit(egresos $egresos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\egresos  $egresos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, egresos $egresos)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\egresos  $egresos
     * @return \Illuminate\Http\Response
     */
    public function destroy(egresos $egresos)
    {
        //
    }
}
