<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($sw=1)
    {
        $categorias=Categoria::select(
            'categorias.*'
        )
        ->where('categorias.estado','=',$sw)
        ->get();  

        if ($sw == 1){
            return view('categorias/index',compact('categorias')); 
        }else{
            return view('categorias/eliminados',compact('categorias'));
        }
    }

    public function store(Request $request)
    {
        $data['error']=0;
        $validator = Validator::make($request->all(), [
            'nombre' => 'required'
        ]);

        if($validator->fails())
        {
            $data=['error'=>'1','mensaje'=>$validator->errors()->all()]; 
            return $data;
        }
        $categoria= new Categoria();    
        $categoria->nombre=$request->nombre;
        $categoria->save();
        
        return $data;
    }
   
 
    public function show(Categoria $categoria)
    {
        $data['categoria']=$categoria;
        return json_encode($data);
    }


    public function update(Request $request, Categoria $categoria)
    {
        $data['error']=0;
        $validator = Validator::make($request->all(), [
            'Mnombre' => 'required'
        ]);

        if($validator->fails())
        {
            $data=['error'=>'1','mensaje'=>$validator->errors()->all()]; 
            return $data;
        }
        $categoria->nombre =$request->Mnombre;
        $categoria->update();
        return $data;

    }

    public function destroy(Categoria $categoria)
    {
        $categoria->estado=0;
        $categoria->update();
        return $categoria;
    }

    public function restore(Categoria $categoria)
    {
        $categoria->estado=1;
        $categoria->update();
        return $categoria;
    }

    public function datos()
    {   
        $categorias=Categoria::all();
        return json_encode($categorias);   
    }
}
