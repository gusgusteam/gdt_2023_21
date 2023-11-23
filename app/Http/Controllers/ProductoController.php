<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\ProductoAlmacen;
use App\Models\Proveedor;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($sw=1)
    {
        $sql="UPDATE productos
        SET stock=IFNULL(
        (
            SELECT SUM(pa.stock)
            FROM producto_almacens pa
            WHERE pa.id_producto = productos.id
        ),0)";
         DB::select($sql);

       // $productos=Producto::all();
       // foreach ($productos as $row){
       //     $registro=ProductoAlmacen::all()->where('id_producto','=',$row->id);
       //     $row->stock=$registro->sum('stock');
       //     $row->update();
       // }
        // $productos=Producto::all();
       // foreach ($productos as $row){
       //     $registro=ProductoAlmacen::all()->where('id_producto','=',$row->id);
       //     $row->stock=$registro->sum('stock');
       //     $row->update();
       // }


        $categorias=Categoria::select(
            'categorias.*'
        )
        ->where('categorias.estado','=',1)
        ->get();  

        if ($sw == 1){
            return view('productos/index',compact('categorias')); 
        }else{
            return view('productos/eliminados',compact('categorias'));
        }
    }

   
    public function store(Request $request)
    {
        $data['error']=0;
        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'descripcion' => 'required',
            'precio_venta' => 'required',
            'precio_compra' => 'required',
            'stock_minimo' => 'required',
            'inventariable' => 'required',
            'id_categoria' => 'required',
            'img_producto' => 'image|mimes:png'
        ]);

        if($validator->fails())
        {
            $data=['error'=>'1','mensaje'=>$validator->errors()->all()]; 
            return $data;
        }
        $producto= new Producto();    
        $producto->nombre=$request->nombre;
        $producto->descripcion =$request->descripcion;
        $producto->precio_venta=$request->precio_venta;
        $producto->precio_compra =$request->precio_compra;
        $producto->id_provedor =$request->id_provedor;
        $producto->stock=0;
        $producto->stock_minimo =$request->stock_minimo;
        $producto->inventariable =$request->inventariable; 
        $producto->id_categoria =$request->id_categoria;
        $producto->save();

        if($request->inventariable==0){
            $producto_almacen= new ProductoAlmacen();
            $producto_almacen->id_producto=$producto->id;
            $producto_almacen->id_almacen=1;
            $producto_almacen->stock=0;
            $producto_almacen->inventariable=0;
            $producto_almacen->save();   
        }

        if ($request->hasFile("img_producto")) {//existe un campo de tipo file?
            $imagen = $request->file("img_producto"); //almacenar imagen en variable
            $nombreimagen=Str::slug($producto->id).".".$imagen->guessExtension();//insertar parametro del nombre de imagen
            $ruta = public_path("img/productos/");//guardar en esa ruta
            $imagen->move($ruta,$nombreimagen); //mover la imagen es esa ruta y con ese nombre      
        }
        
        return json_encode($data);
    }

    
    public function show(Producto $producto)
    {
        $categorias=Categoria::all()->where('estado','=',1);
        $imagen='img/productos/'.$producto->id.'.png';
        if (!file_exists($imagen)) {
         $imagen = "img/productos/150x150.png";
        }
        $url=asset($imagen.'?'.time());
        $nombre="";
        $id_provedor=-1;
        if($producto->id_provedor!= null){
            $provedor= Proveedor::findOrFail($producto->id_provedor);
            $nombre= $provedor->nombre;
            $id_provedor= $producto->id_provedor;
        }else{
            $nombre="no tiene provedor";
        }
       
        $data['producto']=$producto;
        $data['foto']=$url;
        $data['categorias']=$categorias;
        $data['provedor']=$nombre;
        $data['id_provedor']=$id_provedor;
        return json_encode($data);
    }


   
    public function update(Request $request, Producto $producto)
    {
        $data['error']=0;
        $validator = Validator::make($request->all(), [
            'Mnombre' => 'required',
            'Mdescripcion' => 'required',
            'Mprecio_venta' => 'required',
            'Mprecio_compra' => 'required',
            'Mstock_minimo' => 'required',
            'Minventariable' => 'required',
            'Mid_categoria' => 'required',
            'Mimg_producto' => 'image|mimes:png'
        ]);

        if($validator->fails())
        {
            $data=['error'=>'1','mensaje'=>$validator->errors()->all()]; 
            return $data;
        }
        $producto->nombre =$request->Mnombre;
        $producto->descripcion =$request->Mdescripcion;
        $producto->precio_venta=$request->Mprecio_venta;
        $producto->precio_compra =$request->Mprecio_compra;
        $producto->stock_minimo =$request->Mstock_minimo;
        if($request->Mid_provedor!=-1){
            $producto->id_provedor =$request->Mid_provedor;
        }
        $producto->inventariable =$request->Minventariable; 
        $producto->id_categoria =$request->Mid_categoria;
        $producto->update();

        if ($request->hasFile("Mimg_producto")) {
            $image_path = public_path("img/productos/{$producto->id}.png");
            if (File::exists($image_path)) {
                File::delete($image_path);  //eliminar imagen existente
            }
            
            $imagen = $request->file("Mimg_producto");
            $nombreimagen =  $producto->id.".png";
            $ruta = public_path("img/productos/");
            $imagen->move($ruta,$nombreimagen);
        }
        if($request->Minventariable==0){
            $registro=ProductoAlmacen::all()->where('id_producto','=',$producto->id)->where('id_almacen','=',1)->first();
            if(!isset($registro)){
                $producto_almacen= new ProductoAlmacen();   
                $producto_almacen->id_producto=$producto->id;
                $producto_almacen->id_almacen=1;
                $producto_almacen->stock=0;
                $producto_almacen->inventariable=0;
                $producto_almacen->save();   
            }
        }

        return json_encode($data);

    }


    public function destroy(Producto $producto)
    {
        $producto->estado=0;
        $producto->update();
        return $producto;
    }

    public function restore(Producto $producto)
    {
        $producto->estado=1;
        $producto->update();
        return $producto;
    }

    public function datos(Producto $producto)
    {   
        $data['datos']=$producto;
      
        return json_encode($data);  
    }

    public function datos2($sw){
        $productos=Producto::select(
            'productos.*',
            'categorias.nombre as nombre_categoria'
        )
        ->join('categorias','categorias.id','=','productos.id_categoria')
        ->whereIn('productos.estado',[$sw,-1]);
        //->get();

        return DataTables::of($productos)
        // anadir nueva columna botones
       ->addColumn('actions', function($productos){
        $css_btn_edit= config('adminlte.classes_btn_editar') ;
        $css_btn_delete= config('adminlte.classes_btn_eliminar') ;
        $css_btn_restaurar= config('adminlte.classes_btn_restaurar') ;
         $btn_editar='<a class="btn '.$css_btn_edit.'  " rel="tooltip" data-placement="top" title="Editar" onclick="Modificar('.$productos->id.')" ><i class="far fa-edit"></i></a>';
         $btn_eliminar='<a class="btn '.$css_btn_delete.'" rel="tooltip" data-placement="top" title="Eliminar" onclick="Eliminar('.$productos->id.')"><i class="far fa-trash-alt"></i></a>';
         $btn_restaurar='<a class="btn '.$css_btn_restaurar.'" rel="tooltip" data-placement="top" title="Eliminar" onclick="Restaurar('.$productos->id.')"><i class="far fa-trash-alt"></i></a>';
            $btn= '<div class="text-right">  <div class="btn-group btn-group-sm ">';
            if($productos->estado==1){
               $btn= $btn.$btn_editar.$btn_eliminar;
            }else{
               if($productos->estado==0){
                $btn= $btn.$btn_restaurar;
               }else{
                $btn= '';
               }
               
            }
            $btn=$btn.'</div> </div> ';
         return  $btn;
       })
       ->addColumn('btn_compra' , function($productos){
        $btn='<a class="btn btn-info btn-sm" onclick="datos_producto('.$productos->id.')" rel="tooltip" data-placement="top" title="Seleccionar"> <i class="fas fa-plus"></i></a>';
        
        return $btn;
     
       })
       ->addColumn('foto' , function($productos){
     
        $imagen='img/productos/'.$productos->id.'.png';
        if (!file_exists($imagen)) {
         $imagen = "img/productos/150x150.png";
        }
        $url=asset($imagen.'?'.time());
      
        $r="'";
        return '<a class="btn btn-sm" rel="tooltip" data-placement="top" title="Ver imagen" onclick="Imagen('.$r.$imagen.$r.')">  <div class="text-center" > <img width="30" height="30" src="'.$url.'"/> </div> </a>';
     
       })
       ->addColumn('estado', function($productos){
        if($productos->estado==0){
            $span= '<span class="badge bg-danger">inactivo</span>';
        }
        if($productos->estado==1){
            $span= '<span class="badge bg-success">activo</span>';
        }
        if($productos->estado==-1){
            $span= '<span class="badge bg-warning">agotado</span>';
        }
        return  $span;
        })
        ->addColumn('nombre_provedor', function($productos){
            $nombre="";
            if($productos->id_provedor!=null){
               $provedor= Proveedor::findOrFail($productos->id_provedor);
                $nombre= $provedor->nombre;
            }else{
                $nombre="no tiene provedor";
            }
            
            return $nombre ;
        })
       ->rawColumns(['btn_compra','nombre_provedor','actions','foto','estado']) // incorporar columnas
       ->make(true); // convertir a codigo
    }

 
}
