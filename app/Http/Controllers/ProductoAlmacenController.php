<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Producto;
use App\Models\ProductoAlmacen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProductoAlmacenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {   //$registros=ProductoAlmacen::all();
        $sql="UPDATE producto_almacens
        SET estado=if( (producto_almacens.stock >0) AND (producto_almacens.stock <=
        (
        SELECT p.stock_minimo
        FROM productos p
        WHERE p.id=producto_almacens.id_producto
        ))  
            ,-1,
        (
          if(producto_almacens.stock>0,1,0)    
        )
         )";
        DB::select($sql);
        //foreach ($registros as $row){
        //    
        //    $producto=Producto::findOrFail($row->id_producto);
        //    if($row->stock>0 && $row->stock<=$producto->stock_minimo){
        //        $row->estado=-1;
        //    }else{
        //        if($row->stock>0){
        //            $row->estado=1;
        //        }else{
        //        $row->estado=0;
        //        }
        //    }
        //    $row->update();
        //}
        //$sql="SELECT pa.estado, p.inventariable as producto_inventariable , pa.id,pa.id_producto,pa.id_almacen, p.nombre as nombre_producto,a.nombre as nombre_almacen,pa.stock , p.precio_venta
        //FROM producto_almacens pa,productos p,almacens a
        //WHERE pa.id_producto=p.id AND
        //pa.id_almacen=a.id AND
        //p.estado=1
        //";
        //$producto_almacen= DB::select($sql);
      
       // $almacenes=Almacen::all()->where('estado','=',1);
        return view('inventario/index'); 
    }

   
    public function datos()
    {
        $datos=ProductoAlmacen::select(
            'producto_almacens.*',
            'productos.nombre as nombre_producto',
            'productos.stock_minimo as stock_minimo',
            'productos.inventariable as inventariable',
            'almacens.nombre as nombre_almacen'
        )
        ->join('productos','productos.id','=','producto_almacens.id_producto')
        ->join('almacens','almacens.id','=','producto_almacens.id_almacen')
        //->join('producto_almacens','producto_almacens.id_producto','=','productos.id')
        //->join('producto_almacens','producto_almacens.id_almacen','=','almacens.id')

        ->where('productos.estado','=',1);

        //->get();

        return DataTables::of($datos)
       
       
       ->addColumn('estado', function($datos){
        if($datos->estado ==-1){
            $span= '<span class="badge bg-secondary">agotandose</span>';
        }
        if($datos->estado==1){
            $span= '<span class="badge bg-success">activo</span>';
        }
        if($datos->estado==0){
            $span= '<span class="badge bg-warning">agotado</span>';
        }
        return  $span;
        })
        
       ->rawColumns(['estado']) // incorporar columnas
       ->make(true); // convertir a codigo 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductoAlmacen  $productoAlmacen
     * @return \Illuminate\Http\Response
     */
    public function show(ProductoAlmacen $productoAlmacen)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductoAlmacen  $productoAlmacen
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductoAlmacen $productoAlmacen)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductoAlmacen  $productoAlmacen
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductoAlmacen $productoAlmacen)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductoAlmacen  $productoAlmacen
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductoAlmacen $productoAlmacen)
    {
        //
    }
}
