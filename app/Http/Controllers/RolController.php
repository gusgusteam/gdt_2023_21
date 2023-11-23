<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\role_has_permissions;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
class RolController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($sw = 1) {
        $roles=Role::select(
            'roles.*'
        )
        ->where('roles.estado','=',$sw)
        ->get();   

        if ($sw == 1){
            return view('admin/roles/index',compact('roles')); 
        }else{
            return view('admin/roles/eliminados',compact('roles'));
        }
    }
    
    public function store(Request $request)
    {
        $data['error']=0;
        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'descripcion' => 'required'
        ]);

        if($validator->fails())
        {
            $data=['error'=>'1','mensaje'=>$validator->errors()->all()]; 
            return $data;
        }
        Role::create([
            'name'=> $request->nombre,
            'descripcion' => $request->descripcion,   
        ]);
        
        return $data;
    }

    public function update(Request $request, Role $rol)
    {
        $data['error']=0;
        $validator = Validator::make($request->all(), [
            'Mnombre' => 'required',
        ]);

        if($validator->fails())
        {
            $data=['error'=>'1','mensaje'=>$validator->errors()->all()]; 
            return $data;
        }
        
        $rol->name=$request->Mnombre;
        $rol->descripcion=$request->Mdescripcion;
        $rol->update();
        
        return $data;
    }

    public function show(Role $rol)
    {
        $data=['rol'=>$rol];
        return json_encode($data);
    }

    
    public function destroy(Role $rol)
    {
        $rol->estado=0;
        $rol->update();
        return $rol;
    }

    public function restore(Role $rol)
    {
        $rol->estado=1;
        $rol->update();
        return $rol;
    }

    public function datos()
    {   $roles=Role::select(
            'roles.*'
        )
        ->where('roles.estado','=',1)
        ->get();   
      //  $roles=Role::all()->where('estado','=',1);
        $data=['roles'=> $roles];
        return json_encode($data);   
    }

    public function show_permisos(Role $rol){
        
        $roles=$rol->permissions;
        $permisos_sistema=Permission::all();
        $nombre_rol=$rol->name;
        $id_rol=$rol->id;
        //dd($rol->permissions);
        //dd($permisos_sistema);

       /* $rol_permiso=role::all();
        $rol_permiso=$rol_permiso->where('role_id',$rol->id);

        $permisos = Permission::all();
        $id_aux=$rol->id;
        
        $datos=['permisos' => $permisos , 'rol_permiso'=>$rol_permiso,'id_aux'=>$id_aux];
        echo view('administracion/permisos/index',$datos);*/
        return view('admin/permisos/index',compact('roles','permisos_sistema','nombre_rol','id_rol'));
    }
    public function actualizar_permisos(Request $request, $id)
    { 
        $num=collect($request->permisos);
        $n=count($num);

        if($n != 0){
        $roles=Role::all(); // se llama todos los roles
        $roles=$roles->where('id',$id)->first(); // invocamos al rol a cambiar
        $permiso=Permission::whereIn('id', $request->permisos)->get(); // traemos todos los registros con un array de $reques q contiene id de permisos
        $roles->syncPermissions($permiso);// metodo para asiganr array de permisos a un rol

        return redirect()->to(asset('rol/1'));
        }else{
            return "no selecciono ningun permiso";
        }

    }
}
