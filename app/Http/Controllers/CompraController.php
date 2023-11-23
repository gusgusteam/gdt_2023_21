<?php

namespace App\Http\Controllers;
use App\Models\Compra;
use App\Models\Almacen;
use App\Models\Caja;
use App\Models\Configuracion;
use App\Models\DetalleCompra;
use App\Models\Empleado;
use App\Models\Producto;
use App\Models\ProductoAlmacen;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Http\Request;

class CompraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function nueva_compra(){
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
       // $productos=Producto::all()->where('estado','=',1)->where('inventariable','=',1);
        $almacenes=Almacen::all()->where('estado','=',1);

        if(session()->has('id_compra')){
            $key=session('id_compra');
           
        }else{
          session(['id_compra'=>uniqid()]);
          $key=session('id_compra');
        }
        return view('compras/create',compact('almacenes','key'));
    }

    public function index()
    {

      return view('compras/index') ;
    }

    public function datos()
    {  

        $compras=Compra::select(
            'compras.*'
        )
        ->orderBy('compras.created_at','desc');
       // ->orderBy('c.fecha','desc')
       // ->get(); 
        return DataTables::of($compras)
            // anadir nueva columna botones
           ->addColumn('actions', function($compras){
           
            $url_detalle=route('compra.pdf',$compras->id);
             $btn_detalle='<a class="btn btn-primary" rel="tooltip" data-placement="top" title="Ver Detalles" onclick="mostrar_detalle('.$compras->id.')" ><i class="fas fa-stream"></i></a>';
             $btn_eliminar='<a class="btn btn-warning" rel="tooltip" data-placement="top" title="Eliminar" onclick="Eliminar('.$compras->id.')"><i class="far fa-trash-alt"></i></a>';
             $btn_pdf='<a class="btn btn-danger" rel="tooltip" data-placement="top" title="PDF" href="'.$url_detalle.'"><i class="fas fa-file-pdf"></i></a>';
                $btn= '<div class="text-right">  <div class="btn-group btn-group-sm ">';
                if($compras->estado==1 && auth()->user()->can('compra.cancelar')){
                   $btn= $btn.$btn_eliminar.$btn_detalle.$btn_pdf;
                }else{
                   $btn= $btn.$btn_detalle.$btn_pdf;
                }
                $btn=$btn.'</div> </div> ';
             return  $btn;
           })
           ->addColumn('estado', function($compras){
            if($compras->estado==0){
                $span= '<span class="badge bg-danger">Cancelado</span>';
            }
            if($compras->estado==1){
                $span= '<span class="badge bg-success">Completdo</span>';
            }
            return  $span;
            })
            ->addColumn('fecha_hora', function($compras){
            
                return  $compras->fecha . "  ".$compras->hora;
            })
            ->addColumn('monto_total2', function($compras){
            
                return  $compras->monto_total . " bs";
            })
            ->addColumn('usuario', function($compras){
                $empleado=Empleado::all()->where('id','=',$compras->id_empleado)->first();
                return  $empleado->nombre .' '.$empleado->apellidos;
            })
            
           ->rawColumns(['actions','usuario','estado']) // incorporar columnas
           ->make(true); // convertir a codigo
    }

    public function detalle_compra(Compra $compra){
        
        $sql = "SELECT p.nombre as nombre_producto , a.nombre as nombre_almacen, dc.cantidad ,dc.precio_unidad,dc.subtotal
        FROM detalle_compras dc ,compras c, producto_almacens pa, productos p, almacens a
        WHERE 
        dc.id_compra=c.id AND
        dc.id_producto=pa.id_producto AND
        dc.id_almacen=pa.id_almacen AND
        pa.id_producto=p.id AND
        pa.id_almacen=a.id AND
        c.id=$compra->id
        ";

        $detalle = DB::select($sql);

        $fila='';
        $c=1;
        $total=0;
        foreach($detalle as $row){
            $fila .="<tr>";
            $fila .="<td>".$c."</td>";
            $fila .="<td>".$row->nombre_producto."</td>";
            $fila .="<td>".$row->nombre_almacen."</td>";
            $fila .="<td>".$row->cantidad."</td>";
            $fila .="<td>".$row->precio_unidad."</td>";
            $fila .="<td>".number_format($row->subtotal,2,'.','')."</td>";
            $fila .="</tr>";
            $c+=1;
            $total+=$row->subtotal;
        }

        $data['datos']=$fila;
        $data['total']=$total;
        return json_encode($data);

       // return view('compras/detalle',compact('detalle'));
        
    }

    public function calcular_subtotal_producto(Producto $producto,$cantidad)
    {
        return json_encode($producto->precio_compra * $cantidad);
    }
    

    public function destroy(Compra $compra)
    {
        $data['error']=0;
        $data['mensaje']='';
        if( $compra->estado==0){
            $data['error']=1;
            $data['mensaje']='no se puede cancelar 2 veces';
            return json_encode($data);
        }
        $verificar_caja=Caja::findOrFail(1);

        if($verificar_caja->estado==0){
            $data['error']=1;
            $data['mensaje']='la caja de inventario a sido cerrado preguntar al encargado';
            return json_encode($data);
        }
        
        $sw=0;
        $detalle=DetalleCompra::all()->where('id_compra', '=',$compra->id);
        foreach ($detalle as $row){
            $producto_almacen=ProductoAlmacen::all()
            ->where('id_producto','=',$row->id_producto)
            ->where('id_almacen','=',$row->id_almacen)
            ->first();
            $nuevo_stock=$producto_almacen->stock - $row->cantidad;
            if($nuevo_stock<0){
                $sw=1;
            }
        }

        if($sw==0){
            foreach ($detalle as $row){
                $producto_almacen=ProductoAlmacen::all()
                ->where('id_producto','=',$row->id_producto)
                ->where('id_almacen','=',$row->id_almacen)
                ->first();
                $nuevo_stock=$producto_almacen->stock - $row->cantidad;
                $producto_almacen->stock=$nuevo_stock;
                $producto_almacen->update(); 
            }
            $compra->estado=0;
            $compra->update(); 
           
        }else{
            $data['error']=1;
            $data['mensaje']="el stock es insuficiente para la cancelacion";
        }
        return json_encode($data);

    }

    public function pdf_detalle(Compra $compra){
        $sql = "SELECT p.nombre as nombre_producto , a.sigla as nombre_almacen, dc.cantidad ,dc.precio_unidad,dc.subtotal
        FROM detalle_compras dc ,compras c, producto_almacens pa, productos p, almacens a
        WHERE 
        dc.id_compra=c.id AND
        dc.id_producto=pa.id_producto AND
        dc.id_almacen=pa.id_almacen AND
        pa.id_producto=p.id AND
        pa.id_almacen=a.id AND
        c.id=$compra->id
        ";
        $empleado=Empleado::all()->where('id_usuario','=',$compra->id_empleado)->first();
        if(!isset($empleado)){
            return "error de registro de empleado llamar al encargado del sistema";
        }
        $configuracion=Configuracion::all()->first();
        $detalle = DB::select($sql);
        if($compra->estado==1){
            $estado="Completado";
        }else{
            $estado="Cancelado";  
        }
        $fecha=$compra->fecha;
        $hora=$compra->hora;
        $total=$compra->monto_total;
        $pdf = new Fpdf('P','mm',array(200,200));
            $sw=1;
            $contador = 1;
            $color=0;
            foreach ($detalle as $row){
                if ($sw==1){
                    $pdf->AddPage();
                    $pdf->SetMargins(5,5,5);
                    $pdf->SetTitle("Detalle Compra");
                    $pdf->SetFont('Arial','B',16);
                    $pdf->image(asset('vendor/adminlte/dist/img/AdminLTELogo.png'),10,5,10,10,'PNG');
                    $pdf->Cell(190,4,'',0,1,'C');
                    $pdf->Cell(190,4,'NOTA DE COMPRA',0,1,'C');
                    $pdf->Ln(6);
                    $pdf->SetFont('Arial','B',10);
                    $pdf->SetFont('Arial','',9);
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(17,5,utf8_decode('Dirección: '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,$configuracion->direccion,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(22,5,utf8_decode('Fecha y hora: '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,$fecha.' '.$hora,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(22,5,utf8_decode('Remitente: '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,''.$empleado->nombre.''.' '.''.$empleado->apellidos.'',0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(15,5,'Codigo: ',0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(30,5,$compra->codigo,0,1,'L');
                   // $pdf->SetFont('Arial','',9); 
                    $pdf->SetFont('Arial','B',10);
                    $pdf->Ln();
                    $pdf->SetFillColor(2,100,200);//Fondo verde de celda
                    $pdf->SetTextColor(240, 255, 240); //Letra color blanco
                    $pdf->Cell(10,5,utf8_decode('Nº'),1,0,'L',true);
                    $pdf->Cell(60,5,'PRODUCTO',1,0,'L',true);
                    $pdf->Cell(20,5,'ALMACEN',1,0,'L',true);
                    $pdf->Cell(40,5,'PRECIO COMPRA',1,0,'L',true);
                    $pdf->Cell(20,5,'CANTIDAD',1,0,'L',true);
                    $pdf->MultiCell(40,5,'SUB TOTAL',1,1,'L',true);
                    $pdf->SetFont('Arial','',11);
                   // $pdf->Ln(5);
                    $sw=0;
                }

                if($color==1){
                $pdf->SetFillColor(229, 232, 232 ); //gris tenue de cada fila
                $pdf->SetTextColor(3, 3, 3); //Color del texto: Negro
                $color=0;
                }else{
                $pdf->SetFillColor(255, 255, 255 ); //blanco tenue de cada fila
                $pdf->SetTextColor(3, 3, 3); //Color del texto: Negro
                $color=1;
                }

                $pdf->Cell(10,5,$contador,'LR',0,'L',true);
                $pdf->Cell(60,5,$row->nombre_producto,'LR',0,'L',true);
                $pdf->Cell(20,5,$row->nombre_almacen,'LR',0,'L',true);
                $pdf->Cell(40,5,$row->precio_unidad.' bs','LR',0,'L',true);
                $pdf->Cell(20,5,$row->cantidad,'LR',0,'L',true);
                $pdf->Cell(40,5,number_format($row->subtotal,2,'.','').' bs','LR',1,'L',true); // L= IZQUIERDA R= DERECHA
              
                if ($contador%24==0){$sw=1;}
                $contador++;
            }
           
            $pdf->ln();
            $pdf->SetFont('Arial','',12);
            $pdf->Cell(30,5,utf8_decode('Total a Pagar: '),0,0,'L');
            $pdf->SetFont('Arial','',14);
            $pdf->Cell(20,5,number_format($total,2,'.','').' bs',0,1,'L');
            $pdf->ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(20,5,$estado,0,1,'L');
            $pdf->Output('I','detalle_venta'.$fecha.'_'.$hora.'.pdf');

    }

}
