<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;


class EmpleadoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('empleados/index');
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
            'apellidos' => 'required',
            'sexo' => 'required',
            'direccion' => 'required',
            'nro_carnet' => 'required',
            'sueldo' => 'required',
            'telefono' => 'required',
            'id_user' => 'required',
        ]);

        if($validator->fails())
        {
            $data=['error'=>'1','mensaje'=>$validator->errors()->all()]; 
            return json_encode($data);
        }
        $empleado=Empleado::all()->where('id_usuario','=',$request->id_user)->first();
        if($empleado){
            $data['error']=1;
            $data['mensaje']="ya tiene registro como empleado";   
            return json_encode($data);
        }
        $empleado=Empleado::all()->where('ci','=',$request->nro_carnet)->first();
        if(!$empleado){
            $registro= new Empleado();
            $registro->nombre=$request->nombre;
            $registro->apellidos=$request->apellidos;
            $registro->sexo=$request->sexo;
            $registro->ci=$request->nro_carnet;
            $registro->sueldo=$request->sueldo;
            $registro->edad=$request->edad;
            $registro->direccion=$request->direccion;
            $registro->telefono=$request->telefono;
            if($request->id_user==-1){
                $registro->id_usuario=null;
            }else{
                $registro->id_usuario=$request->id_user;
            }
            $registro->save();
        }else{
            $data['error']=1;
            $data['mensaje']="el numero de carnet tiene q ser unico";
        }
        
        return json_encode($data);
    }

    public function show(Empleado $empleado)
    {
        return json_encode($empleado);
    }

    public function edit(Empleado $empleado)
    {
        //
    }
    public function update(Request $request, Empleado $empleado)
    {
        $data['error']=0;
        $validator = Validator::make($request->all(), [
            'Mnombre' => 'required',
            'Mapellidos' => 'required',
            'Msexo' => 'required',
            'Medad' => 'required',
            //'Msueldo' => '',
            //'Mdireccion' => 'required',
            'Mnro_carnet' => 'required',
            //'telefono' => 'required'
        ]);

        if($validator->fails())
        {
            $data=['error'=>'1','mensaje'=>$validator->errors()->all()]; 
            return $data;
        }
        if($request->Mnro_carnet == $empleado->ci){
            $registro= $empleado;
            $registro->nombre=$request->Mnombre;
            $registro->apellidos=$request->Mapellidos;
            $registro->sexo=$request->Msexo;
            $registro->ci=$request->Mnro_carnet;
            $registro->edad=$request->Medad;
            $registro->direccion=$request->Mdireccion;
            $registro->sueldo=$request->Msueldo;
            $registro->telefono=$request->Mtelefono;
            $registro->update();
        }else{
            $empleado2=Empleado::all()->where('ci','=',$request->Mnro_carnet)->first();
            if(!$empleado2){
                $registro= $empleado;
                $registro->nombre=$request->Mnombre;
                $registro->apellidos=$request->Mapellidos;
                $registro->sexo=$request->Msexo;
                $registro->ci=$request->Mnro_carnet;
                $registro->edad=$request->Medad;
                $registro->direccion=$request->Mdireccion;
                $registro->sueldo=$request->Msueldo;
                $registro->telefono=$request->Mtelefono;
                $registro->update();
            }else{
                $data['error']=1;
                $data['mensaje']="empleado el numero de carnet ya esta en uso";
            }
        } 
        return $data;
    }

    public function destroy(Empleado $empleado)
    {
        $empleado->estado=0;
        $empleado->update();
    }
    public function restore(Empleado $empleado)
    {
        $empleado->estado=1;
        $empleado->update();
    }

    public function datos()
    {  
        $empleado=Empleado::select(
            'empleados.*'
        );

        return DataTables::of($empleado)
            // anadir nueva columna botones
           ->addColumn('actions', function($empleado){
            $css_btn_edit= config('adminlte.classes_btn_editar') ;
            $css_btn_delete= config('adminlte.classes_btn_eliminar') ;
            $css_btn_restaurar= config('adminlte.classes_btn_restaurar') ;
             $btn_editar='<a class="btn '.$css_btn_edit.'  " rel="tooltip" data-placement="top" title="Editar" onclick="Modificar('.$empleado->id.')" ><i class="far fa-edit"></i></a>';
             $btn_eliminar='<a class="btn '.$css_btn_delete.'" rel="tooltip" data-placement="top" title="Eliminar" onclick="Eliminar('.$empleado->id.')"><i class="far fa-trash-alt"></i></a>';
             $btn_restaurar='<a class="btn '.$css_btn_restaurar.'" rel="tooltip" data-placement="top" title="Eliminar" onclick="Restaurar('.$empleado->id.')"><i class="far fa-trash-alt"></i></a>';
                $btn= '<div class="text-right">  <div class="btn-group btn-group-sm ">';
                if($empleado->estado==1){
                   $btn= $btn.$btn_editar.$btn_eliminar;
                }else{
                   $btn= $btn.$btn_restaurar;
                }
                $btn=$btn.'</div> </div> ';
             return  $btn;
           })
           ->addColumn('foto' , function($empleado){
            $user=User::all()->where('id','=',$empleado->id_user)->first();
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
           ->addColumn('estado', function($empleado){
            if($empleado->estado==0){
                $span= '<span class="badge bg-warning">inactivo</span>';
            }
            if($empleado->estado==1){
                $span= '<span class="badge bg-success">activo</span>';
            }
            return  $span;
            })
            ->addColumn('nombre_completo', function($empleado){
                
                return  $empleado->nombre .' '. $empleado->apellidos;
            })
           ->rawColumns(['actions','nombre_completo','foto','estado']) // incorporar columnas
           ->make(true); // convertir a codigo
    }
}
