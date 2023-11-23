<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class ProveedorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('provedores/index');
    }

    public function datos(){
        $proveedor=Proveedor::select(
            'proveedors.*'
        );

        return DataTables::of($proveedor)
            // anadir nueva columna botones
           ->addColumn('actions', function($proveedor){
            $css_btn_edit= config('adminlte.classes_btn_editar') ;
            $css_btn_delete= config('adminlte.classes_btn_eliminar') ;
            $css_btn_restaurar= config('adminlte.classes_btn_restaurar') ;
             $btn_editar='<a class="btn '.$css_btn_edit.'  " rel="tooltip" data-placement="top" title="Editar" onclick="Modificar('.$proveedor->id.')" ><i class="far fa-edit"></i></a>';
             $btn_eliminar='<a class="btn '.$css_btn_delete.'" rel="tooltip" data-placement="top" title="Eliminar" onclick="Eliminar('.$proveedor->id.')"><i class="far fa-trash-alt"></i></a>';
             $btn_restaurar='<a class="btn '.$css_btn_restaurar.'" rel="tooltip" data-placement="top" title="Eliminar" onclick="Restaurar('.$proveedor->id.')"><i class="far fa-trash-alt"></i></a>';
                $btn= '<div class="text-right">  <div class="btn-group btn-group-sm ">';
                if($proveedor->estado==1){
                   $btn= $btn.$btn_editar.$btn_eliminar;
                }else{
                   $btn= $btn.$btn_restaurar;
                }
                $btn=$btn.'</div> </div> ';
             return  $btn;
           })
           ->addColumn('estado', function($proveedor){
            if($proveedor->estado==0){
                $span= '<span class="badge bg-warning">inactivo</span>';
            }
            if($proveedor->estado==1){
                $span= '<span class="badge bg-success">activo</span>';
            }
            return  $span;
            })
            ->addColumn('tipo2', function($proveedor){
                if($proveedor->tipo==0){
                    $span= '<span class="badge bg-info">persona</span>';
                }
                if($proveedor->tipo==1){
                    $span= '<span class="badge bg-secondary">empresa</span>';
                }
                return  $span;
                })
           ->rawColumns(['actions','estado','tipo2']) // incorporar columnas
           ->make(true); // convertir a codigo
    }
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data['error']=0;
        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'descripcion' => 'required',
            'direccion' => 'required',
            'telefono' => 'required',
            'tipo' => 'required',
            'correo' => 'required',
            
        ]);

        if($validator->fails())
        {
            $data=['error'=>'1','mensaje'=>$validator->errors()->all()]; 
            return json_encode($data);
        }
        
        $registro= new Proveedor();
        $registro->nombre=$request->nombre;
        $registro->direccion=$request->direccion;
        $registro->descripcion=$request->descripcion;
        $registro->correo=$request->correo;
        $registro->tipo=$request->tipo;
        $registro->nic=$request->nic;
        $registro->telefono=$request->telefono;
        $registro->save();
        
        return json_encode($data);
    }

    public function show(Proveedor $proveedor)
    {
        $data=$proveedor;
        return json_encode($data);
    }

    public function edit(Proveedor $proveedor)
    {
        //
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $data['error']=0;
        $validator = Validator::make($request->all(), [
            'Mnombre' => 'required',
            'Mdescripcion' => 'required',
            'Mdireccion' => 'required',
            'Mtelefono' => 'required',
            'Mtipo' => 'required',
            'Mcorreo' => 'required',
            
        ]);

        if($validator->fails())
        {
            $data=['error'=>'1','mensaje'=>$validator->errors()->all()]; 
            return json_encode($data);
        }
        
        $registro=$proveedor;
        $registro->nombre=$request->Mnombre;
        $registro->direccion=$request->Mdireccion;
        $registro->descripcion=$request->Mdescripcion;
        $registro->correo=$request->Mcorreo;
        $registro->tipo=$request->Mtipo;
        $registro->nic=$request->Mnic;
        $registro->telefono=$request->Mtelefono;
        $registro->update();
        
        return json_encode($data);
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->estado=0;
        $proveedor->update();
        return json_encode($proveedor);
    }

    public function restore(Proveedor $proveedor)
    {
        $proveedor->estado=1;
        $proveedor->update();
        return json_encode($proveedor);
    }

    public function autocompleteData($nombre){
        $proveedor = Proveedor::where('nombre','LIKE','%'.$nombre.'%')->where('estado','=',1)->get(); 
        return json_encode($proveedor);
    }
}
