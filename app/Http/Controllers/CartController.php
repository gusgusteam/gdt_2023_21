<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\ProductoAlmacen;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Cart;


class CartController extends Controller
{
    public function shop()
    {
        

        $products = Producto::all();
        dd($products);
        return view('shop')->withTitle('E-COMMERCE STORE | SHOP')->with(['products' => $products]);
    }

    public function cart()  {
        $cartCollection = \Cart::getContent();
        //dd($cartCollection);
        return view('cart')->withTitle('E-COMMERCE STORE | CART')->with(['cartCollection' => $cartCollection]);;
    }
    public function remove(Request $request){
        \Cart::remove($request->id);
        return redirect()->route('cart.index')->with('success_msg', 'Item is removed!');
    }

    public function add($id_producto,$id_almacen){
        $data['error']=0;
        $data['mensaje']=" n ";
        $producto_almacen=ProductoAlmacen::select(
        'producto_almacens.*',
        'productos.nombre as nombre_producto',
        'almacens.nombre as nombre_almacen',
        'productos.precio_venta'
           // 'almacens.id as id_almacen'
        )
       // ->top(1)
        ->join('almacens','almacens.id','=','producto_almacens.id_almacen')
        ->join('productos','productos.id','=','producto_almacens.id_producto')
        ->where('producto_almacens.id_producto','=',$id_producto)
        ->where('producto_almacens.id_almacen','=',$id_almacen)
        ->get();
        //$producto_almacen=$producto_almacen->first();
        //dd($producto_almacen);
       // dd($producto_almacen);
        $existe=$this->verificar_stock_disponible($producto_almacen->first()->id,1);
       // dd($existe);
       // return $data;
        //if ($this->verificar_stock_disponible($producto_almacen->first()->id,1)){
        if($existe['cart_existe']==false){
            $prediccion_stock=($producto_almacen->first()->stock)-1;
            if(($prediccion_stock>=0) && ((\Cart::count()+1)<=70) ){
                \Cart::add(array(
                    'id' => $producto_almacen->first()->id,
                    'name' => $producto_almacen->first()->nombre_producto .' :: '. $producto_almacen->first()->nombre_almacen,
                    'price' => $producto_almacen->first()->precio_venta,
                    'qty' => 1
                   // 'quantity' => 1,
                   // 'attributes' => array(
                        //'image' => $request->img,
                        //'almacen' => $producto_almacen->first()->nombre_almacen
                   // )
                ));
            }else{
                $data['error']=1;
                $data['mensaje']="stock de productos insuficientes";
            }
        }else{
            if($existe['stock_disponible']==true){
              $comprobante=$this->Modificar_cantidad($existe['id_cart'],1);
              return $comprobante;
              //$data['error']=$comprobante['error'];
              //$data['mensaje']=$comprobante['mensaje'];
            }else{
                $data['error']=1;
                $data['mensaje']="stock de productos insuficientes";  
            }
        }
       /* }else{
            $data['error']=1;
            $data['mensaje']="stock del producto del almacen son insuficientes";
        }*/
        
       // $data['datos']=$this->datos_directo();

        return json_encode($data);
    }

    public function verificar_stock_disponible($id_producto_almacen,$canti){
        $sw=true;
        $cart_sw=false;
        $id_cart_carrrito='';
        $cart= \Cart::Content();
        $producto_almacen=ProductoAlmacen::all()->where('id','=',$id_producto_almacen)->first();
        foreach ($cart as $row){
            if($row->id==$id_producto_almacen){ 
                $sum_qty=$row->qty+$canti;
                $calculo=$producto_almacen->stock - $sum_qty;
                if($calculo<0){
                    $sw=false; // no hay mas productos en ese almacen
                }
                $cart_sw=true; // existe el cart 
                $id_cart_carrrito=$row->rowId;
            }
        }
        $data['cart_existe']=$cart_sw;
        $data['stock_disponible']=$sw;
        $data['id_cart']=$id_cart_carrrito;

        return $data;
    }

    public function update(Request $request){
        \Cart::update($request->id,
            array(
                'quantity' => array(
                    'relative' => false,
                    'value' => $request->quantity
                ),
        ));
        return redirect()->route('cart.index')->with('success_msg', 'Cart is Updated!');
    }

    public function clear(){
        $data['error']=0;
        \Cart::destroy();
        return json_encode($data);
    }

    public function datos()
    {  

        $cart= \Cart::Content();
        $c=1;
        return DataTables::of($cart)
            // anadir nueva columna botones
           ->addColumn('action', function($cart){
            $btn='<div class="text-right">  <div class="btn-group btn-group-sm">'; 
            $r="'";
            //'<form id="form-del" action="{{route('.'cart.removeitem'.')}}"method="POST">'. 
            $btn1='<button class="btn btn-success btn-sm btn-flat" type ="button" onclick="AumentarCant('.$r.$cart->rowId.$r.',-1)" title="Menos"><i class="fas fa-minus-circle"></i></button>';
            $btn2='<button class="btn btn-danger btn-sm" type ="button" onclick="'.$cart->id.'" title="Eliminar"><i class="fas fa-trash"></i></button>';
            $btn3='<button class="btn btn-success btn-sm btn-flat" type ="button" onclick="AumentarCant('.$r.$cart->rowId.$r.',1)" title="Menos"><i class="fas fa-plus-circle"></i></button>';
            $btnf='</div></div>';
            return $btn.$btn2.$btnf;
            })
            ->addColumn('sub_total', function($cart){
            
                return $cart->subtotal();
            })
            ->addColumn('num', function(){
                return '';
            
            })
           
           ->rawColumns(['action','sub_total','num']) // incorporar columnas
           ->make(true); // convertir a codigo
    }

    public function datos_directo()
    {  
       // $data['error']=0;
        $cart= \Cart::Content();
        $numFila=0;
        $fila='';
        foreach ($cart as $row){
    
            $numFila++;
            $btn='<div class="text-right">  <div class="btn-group btn-group-sm">'; 
            $r="'";
            $btn_editar_cant='';
            //'<form id="form-del" action="{{route('.'cart.removeitem'.')}}"method="POST">'. 
            $btn1='<button class="btn btn-success btn-sm btn-flat" type ="button" onclick="AumentarCant('.$r.$row->rowId.$r.',-1)" title="Menos"><i class="fas fa-minus-circle"></i></button>';
            $btn2='<button class="btn btn-danger btn-sm" type ="button" onclick="delete_cart('.$r.$row->rowId.$r.')" title="Eliminar"><i class="fas fa-trash"></i></button>';
            $btn3='<button class="btn btn-success btn-sm btn-flat" type ="button" onclick="AumentarCant('.$r.$row->rowId.$r.',1)" title="Menos"><i class="fas fa-plus-circle"></i></button>';
            $btnf='</div></div>';
            $btn_editar_cant.='<div class="input-group input-group-sm mb-0">';
            $btn_editar_cant.='<input type="number" class="form-control" style="width:25px;" id="iden'.$row->rowId.'" name="iden'.$row->rowId.'" title="cantidad" value="'.$row->qty.'" min="1" > ';
            $btn_editar_cant.='<button class="btn btn-success btn-sm btn-flat" type ="button" onclick="modificarQTY('.$r.$row->rowId.$r.')" title="aumentar"><i class="fas fa-plus-circle"></i></button>';
            $btn_editar_cant.='</div>';
            
            $fila .= "<tr id='fila".$numFila."'>";
            $fila .= "<td>".$numFila."</td>";
            $fila .= "<td>".$row->name."</td>";
            $fila .= "<td class=\"text-center\" >".$row->price."</td>";
            //$fila .= "<td class=\"text-center\">".$row->qty."</td>";
            $fila .= "<td class=\"text-center\">".$btn_editar_cant."</td>";
            $fila .= "<td class=\"text-center\">".$row->subtotal()."</td>";
           // $fila .= "<td></td>";
            $fila .= "<td>".$btn.$btn1.$btn2.$btn3.$btnf."</td>";
            $fila .= "</tr>";
            //$total+=$row['sub_total'];
           
        }
        //$data['datos']=$fila;

        return $fila;

    }

    public function Modificar_cantidad($id_cart,$cantidad){
       $data['error']=0;
       $data['mensaje']=" ";
       $carrito= \Cart::get($id_cart);
       if(\Cart::count()+$cantidad<=70){
            $existe=$this->verificar_stock_disponible($carrito->id,$cantidad);
            if($existe['stock_disponible']==true){
                \Cart::update($id_cart, ($carrito->qty+$cantidad));
            }else{
                $data['error']=1;
                $data['mensaje']="stock de productos insuficientes";
            }
       }else{
        $data['error']=1;
        $data['mensaje']="lo siento no puede superar mas de 70 articulos de venta";
       }
       return json_encode($data);
    }

    public function Modificar_qty($id_cart,$cantidad){
        $data['error']=0;
        $carrito= \Cart::get($id_cart);
        $predecido_stock=(\Cart::count()-$carrito->qty)+$cantidad;
       // dd($predecido_stock);
        if($predecido_stock<=70){
            $producto_almacen=ProductoAlmacen::all()->where('id','=',$carrito->id)->first();
            //dd($producto_almacen);
            if($producto_almacen->stock - $cantidad >=0){
               // dd($id_cart);
                \Cart::update($id_cart, '150');
            }else{
                $data['error']=1;
                $data['mensaje']="stock de productos insuficientes";
            }   
        }else{
            $data['error']=1;
            $data['mensaje']="lo siento no puede superar mas de 70 articulos de venta";  
        }
        return json_encode($data);
     }
 

    public function elementos(){
        //$data="hola mundo";
        //dd($data);
       // $data="";
        //$data['cantidad_producto']=\Cart::Content()->count();
        $data['cantidad_producto']=\Cart::count();
        //$data['cantidad_articulos']=\Cart::count();
        $data['monto_total']=number_format(\Cart::total()-\Cart::tax(),2);
        $data['datos']=$this->datos_directo();
        //dd($data);
        return json_encode($data);
    }

    public function DeleteCart($id_cart){
       $data['error']=0;
        \Cart::remove($id_cart);
       return $data; 
    }
 

}
