<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
    public function __construct()
    {   
        $this->middleware('auth');
    }

    public function index($option)
    {
       
        if (Auth::user()->can('usuario')==false) {
            return view('error_permiso');
        }

        if($option==1){
            return view('admin/usuarios/index');
        }else{
            return view('admin/usuarios/eliminados');
        }
        
        
    }
    
    public function store(Request $request)
    {
        if (Auth::user()->can('usuario.agregar')==false) {
            return view('error_permiso');
        }

        $data['error']=0;
        $user=User::all()->where('email','=',$request->correo)->first();
        if($user){
            $data=['error'=>'1','mensaje'=>"EL USUARIO YA EXISTE"];
            return $data;
        }
        if(count(collect($request->id_rol))==0){
            $data=['error'=>'1','mensaje'=>"SELECCIONE UN ROL MINIMO"];
            return $data;
        }
        $roles=collect($request->id_rol);
        User::create([
            'name'=> $request->nombre,
            'email' => $request->correo,
            'password' => Hash::make($request->password),
        ])->assignRole($roles);
        
        return $data;
    }

    public function update(Request $request, User $user)
    {
        if (Auth::user()->can('usuario.editar')==false) {
            return view('error_permiso');
        }

        $data['error']=0;
        if(count(collect($request->Mid_rol))==0){
            $data=['error'=>'1','mensaje'=>"SELECCIONE UN ROL MINIMO"];
            return $data;
        }
        $roles=collect($request->Mid_rol);
        $user->syncRoles($roles);
       // $user->assignRole($roles);
        $user->update();
        
        return $data;
    }

    public function show(User $user)
    {
        if (Auth::user()->can('usuario.editar')==false) {
            return view('error_permiso');
        }

        $roles=Role::select(
            'roles.*'
        )
        ->where('roles.estado','=',1)
        ->get(); 
        $user=$user->load('roles');
        $data=['user'=>$user,'roles'=> $roles];
        return json_encode($data);
    }

    public function destroy(User $user)
    {
        if (Auth::user()->can('usuario.eliminar')==false) {
            return view('error_permiso');
        }

        $user->estado=0;
        $user->update();
        return $user;
    }

    public function restore(User $user)
    {
        if (Auth::user()->can('usuario.restore')==false) {
            return view('error_permiso');
        }
        $user->estado=1;
        $user->update();
        return $user;
    }
     public function datos($sw)
    {  
            // $caja=Caja::select('cajas.*')->whereIn('cajas.activo',[1,-1]);
        $user=User::select(
            'users.*'
        )->with('roles')
        ->where('users.estado','=',$sw);
       // $user= User::all();
       // ->get();
        return DataTables::of($user)
            // anadir nueva columna botones
           ->addColumn('actions', function($user){
            $css_btn_edit= config('adminlte.classes_btn_editar') ;
            $css_btn_delete= config('adminlte.classes_btn_eliminar') ;
            $css_btn_restaurar= config('adminlte.classes_btn_restaurar') ;
            $empleado=Empleado::all()->where('id_usuario','=',$user->id)->first();
            // $url_propio=route('usuario.edit',[$caja->id,'1']);
            if($empleado){
                $btn_asignar='<a class="btn btn-secondary" rel="tooltip" data-placement="top" title="Ver Datos" onclick="perfil_datos('.$empleado->id.')" ><i class="far fa-user"></i></a>';
            }else{
                $btn_asignar='<a class="btn btn-success" rel="tooltip" data-placement="top" title="Asignar" onclick="DesignarEmpleado('.$user->id.')" ><i class="far fa-user"></i></a>';
            }
             $btn_editar='<a class="btn '.$css_btn_edit.'  " rel="tooltip" data-placement="top" title="Editar" onclick="Modificar('.$user->id.')" ><i class="far fa-edit"></i></a>';
             $btn_eliminar='<a class="btn '.$css_btn_delete.'" rel="tooltip" data-placement="top" title="Eliminar" onclick="Eliminar('.$user->id.')"><i class="far fa-trash-alt"></i></a>';
             $btn_restaurar='<a class="btn '.$css_btn_restaurar.'" rel="tooltip" data-placement="top" title="Restaurar" onclick="Restaurar('.$user->id.')"><i class="far fa-trash-alt"></i></a>';
             //$button_grupo4='<a class="btn btn-success" rel="tooltip" data-placement="top" title="Ingresos y egresos" href="'.$url_propio.'" ><i class="fas fa-balance-scale-right"></i></a>';                   $btn= '<div class="text-right">  <div class="btn-group btn-group-sm">'
                $btn= '<div class="text-right">  <div class="btn-group btn-group-sm ">';
             if(Auth::user()->can('usuario.restore')==false){
                $btn_restaurar='';
             }
             if(Auth::user()->can('usuario.editar')==false){
                $btn_editar='';
                $btn_asignar='';
             }
             if(Auth::user()->can('usuario.eliminar')==false){
                $btn_eliminar='';
             }
                if($user->estado==1){
                   $btn= $btn.$btn_asignar.$btn_editar.$btn_eliminar;
                }else{
                   $btn= $btn.$btn_restaurar;
                }
                $btn=$btn.'</div> </div> ';
             return  $btn;
           })
           ->addColumn('rol_uso' , function($user){
            $n=count($user->roles);
            $i=0;
            $lista="";
            while ($i<$n){
                $lista= $lista."[". $user->roles[$i]->name."]" ;
                $i=$i+1;
            }
            if ($n=0){
               return 'no tiene rol';
            }
            return $lista; 
           })
           ->addColumn('estado', function($user){
            if($user->estado==0){
                $span= '<span class="badge bg-danger">inactivo</span>';
            }
            if($user->estado==1){
                $span= '<span class="badge bg-success">activo</span>';
            }
            return  $span;
            })
           ->rawColumns(['actions','rol_uso','estado']) // incorporar columnas
           ->make(true); // convertir a codigo
    }

}
