<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Configuracion;
use App\Models\ingreso;
use App\Models\planpago;
use App\Models\Venta;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Codedge\Fpdf\Fpdf\Fpdf;

class CajaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('caja_general/index');
    }

    public function caja_general()
    {
        return view('caja_general/cajas');
    }
    public function caja_panel(Caja $caja)
    {
        app(IngresoController::class)->recalcular_datos($caja->id);
        return view('caja_general/panel',compact('caja'));
    }



    public function cajas_generales(){
        $cajas=Caja::all();
        $data['cajas']=$cajas;
        return json_encode($data);
    }

    public function datos()
    {
        $cajas=Caja::select(
            'cajas.*'
        )
        ->orderBy('cajas.created_at','desc');
     
        return DataTables::of($cajas)
            // anadir nueva columna botones
           ->addColumn('actions', function($cajas){
            //$url_detalle=route('venta.pdf',$ventas->id);
            //$url_ticket=route('venta.ticket',$ventas->id);
            //$url_ticket=route('venta.imprimir',$ventas->id);
            $aux_fecha_inicio="'".$cajas->fecha_inicio." ".$cajas->hora_inicio."'";
            $aux_fecha_final="'".$cajas->fecha_final." ".$cajas->hora_final."'";
             $btn_curso_caja='<a class="btn btn-success" rel="tooltip" data-placement="top" title="Iniciar Caja" onclick="iniciar_caja('.$cajas->id.')" ><i class="fas fa-stream"></i></a>';
             $btn_cierre_caja='<a class="btn btn-primary" rel="tooltip" data-placement="top" title="Cerrar Caja" onclick="cerrar_caja('.$cajas->id.')" ><i class="fas fa-stream"></i></a>';
             $btn_informe_caja='<a class="btn btn-warning" title="Informe" onclick="informe('.$cajas->id.',1)" ><i class="fas fa-sitemap"></i></a>';
  
             $btn= '<div class="text-right">  <div class="btn-group btn-group-sm ">';
               
                    if($cajas->estado==1){
                        $btn= $btn.$btn_cierre_caja;
                    }else{
                        $btn= $btn.$btn_curso_caja.$btn_informe_caja;
                    }
                
                
                $btn=$btn.'</div> </div> ';
             return  $btn;
           })
            ->addColumn('fecha_inicio', function($cajas){
                return  $cajas->fecha_inicio." ".$cajas->hora_inicio;
            })
            ->addColumn('fecha_final', function($cajas){
                return  $cajas->fecha_final." ".$cajas->hora_final;
            })
            ->addColumn('capital', function($cajas){
                $calculo=0;
                if($cajas->id==7){
                   $calculo= $cajas->monto_ingreso - $cajas->monto_egreso;
                }else{
                   $calculo=$cajas->monto_ingreso;
                }
                return  $calculo." bs";
            })
            ->addColumn('ingreso', function($cajas){
                return  $cajas->monto_total_generado+$cajas->monto_ingreso_caja." bs";
            })
            ->addColumn('egreso', function($cajas){
                return  $cajas->monto_egreso." bs";
            })
            ->addColumn('estado', function($cajas){
                if($cajas->estado==0){
                    $span= '<span class="badge bg-danger">Cerrado</span>';
                }
                if($cajas->estado==1){
                    $span= '<span class="badge bg-success">En Curso</span>';
                }
                return  $span;
            })
          
           ->rawColumns(['actions','fecha_inicio','fecha_final','egreso','ingreso','estado']) // incorporar columnas
           ->make(true); // convertir a codigo
    }

    
    public function cerrar_caja(Caja $caja)
    {
        $data['error']=0;
        $caja->estado=0;
        //$caja->monto_ingreso=0;
        //$caja->monto_egreso=0;
        $caja->fecha_final=date("y-m-d");
        $caja->hora_final=date("H:i:s");
        $caja->update();
        return json_encode($data);
    }
    public function iniciar_caja(Caja $caja)
    {
        $data['error']=0;
        $data['mensaje']='';
        $caja->estado=1;
        $valor_nuevo=$caja->monto_ingreso;
        if($valor_nuevo>=0){
            $ingreso=new ingreso();
            $sw=0;
            $codigo_generado='';
            $caja_primaria=$caja->id;
            while($sw==0){
                $codigo_generado = uniqid();
                $existe=ingreso::all()->where('codigo','=',$codigo_generado)->first();
                if(!$existe){ $sw=1;}
            }
            $ingreso->codigo=$codigo_generado;
            $ingreso->descripcion='inicio de la '.$caja->nombre.' con '.$valor_nuevo .' bs';
            $ingreso->monto_total=$valor_nuevo;
            $ingreso->id_caja_primaria=$caja_primaria;
            $ingreso->tipo_ingreso='INICIO INGRESO';
            $ingreso->save();

            
            $caja->fecha_inicio=$caja->fecha_final;
            $caja->hora_inicio=$caja->hora_final;
            $caja->fecha_final=null;
            $caja->hora_final=null;

            $caja->update();
            
            $data['mensaje']='cierre de caja completa con un ingreso de inicio '.$valor_nuevo .' bs';
        }else{
            $caja->fecha_final=null;
            $caja->hora_final=null;
            $caja->update(); 
            $data['mensaje']='todavia no se puede hacer un cierre de caja completo';
        }
        app(IngresoController::class)->recalcular_datos($caja->id);
        return json_encode($data);
    }


    public function reporte_informe($id_caja,$fecha_inicial,$fecha_final){
       
        $sql="SELECT SUM(detalle_ventas.cantidad) as cant_productos, detalle_ventas.precio_unidad as precio_unidad , productos.nombre as nombre_producto, almacens.sigla as nombre_almacen,ventas.tipo_pago as tipo_pago
        FROM ventas,detalle_ventas,producto_almacens,productos , almacens
        WHERE detalle_ventas.id_venta=ventas.id AND
        detalle_ventas.id_producto=producto_almacens.id_producto AND
        detalle_ventas.id_almacen=producto_almacens.id_almacen AND
        producto_almacens.id_producto=productos.id AND
        producto_almacens.id_almacen=almacens.id AND
        (ventas.estado=1) AND
        concat( ventas.fecha, ' ', ventas.hora) >='$fecha_inicial' AND
        concat( ventas.fecha, ' ', ventas.hora) <='$fecha_final' AND
        ventas.id_caja=$id_caja
        GROUP BY  precio_unidad , nombre_producto, nombre_almacen,tipo_pago";
        //dd($sql);
        $sql2="SELECT SUM(ventas.interes) total_interes
        FROM ventas
        WHERE 
        (ventas.estado=1 OR ventas.estado=-1) AND
        concat( ventas.fecha, ' ', ventas.hora) >='$fecha_inicial' AND
        concat( ventas.fecha, ' ', ventas.hora) <='$fecha_final' AND
        ventas.id_caja=$id_caja";

        $sql3="SELECT SUM(ventas.monto_total) total_credito
        FROM ventas
        WHERE 
        ventas.estado=-1 AND
        concat( ventas.fecha, ' ', ventas.hora) >='$fecha_inicial' AND
        concat( ventas.fecha, ' ', ventas.hora) <='$fecha_final' AND
        ventas.id_caja=$id_caja";
        $sql4="SELECT SUM(ventas.monto_total) total_monto
        FROM ventas
        WHERE 
        ventas.estado=1 AND
        concat( ventas.fecha, ' ', ventas.hora) >='$fecha_inicial' AND
        concat( ventas.fecha, ' ', ventas.hora) <='$fecha_final' AND
        ventas.id_caja=$id_caja";
        $sql5="SELECT SUM(ventas.descuento) total_descuento
        FROM ventas
        WHERE 
        (ventas.estado=1) AND
        concat( ventas.fecha, ' ', ventas.hora) >='$fecha_inicial' AND
        concat( ventas.fecha, ' ', ventas.hora) <='$fecha_final' AND
        ventas.id_caja=$id_caja";
        $sqldescuento="SELECT SUM(ventas.descuento) total_descuento_publico
        FROM ventas
        WHERE 
        (ventas.estado=1) AND
        concat( ventas.fecha, ' ', ventas.hora) >='$fecha_inicial' AND
        concat( ventas.fecha, ' ', ventas.hora) <='$fecha_final' AND
        ventas.id_caja=$id_caja AND
        ventas.id_cliente is NULL";

        $sqlx="SELECT i.descripcion, i.monto_total,i.tipo_ingreso
        FROM ingresos i
        WHERE i.id_caja_primaria=$id_caja AND
        i.estado=1 AND
        i.created_at >= '$fecha_inicial' AND
        i.created_at <= '$fecha_final'";
         $primer_ingreso = DB::select($sqlx);
         $sw_ingreso=0;


        $datos_ventas= (array) DB::select($sql); 
        $intereses = collect(DB::select($sql2))->first()->total_interes;
        $creditos = collect(DB::select($sql3))->first()->total_credito;
        $descuento_publico = collect(DB::select($sqldescuento))->first()->total_descuento_publico;
        $descuento = collect(DB::select($sql5))->first()->total_descuento;
       // dd($datos_ventas);
       // $datos_ventas= collect(DB::select($sql));
        $configuracion=Configuracion::all()->first();

        if(count($datos_ventas)<=0){
            return "no se realizo ninguna venta ";
        }
        $pdf = new Fpdf('P','mm',array(200,200));
        
            $sw=1;
            $contador = 1;
            $color=0;
            $total_completo=0;
            $sumx=0;
            foreach ($datos_ventas as $row){
                if ($sw==1){
                    $pdf->AddPage();
                    $pdf->SetMargins(5,5,5);
                    $pdf->SetTitle("INFORME");
                    $pdf->SetFont('Arial','B',15);
                    $pdf->image(asset('vendor/adminlte/dist/img/AdminLTELogo.png'),10,5,10,10,'PNG');
                    $pdf->Cell(190,4,'',0,1,'C');
                    $pdf->Cell(190,4,'INFORME DE CAJA "COMPLETADOS CREDITO Y CONTADO"',0,1,'C');
                    $pdf->Ln(6);
                    $pdf->SetFont('Arial','B',10);
                    $pdf->SetFont('Arial','',9);
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(17,5,utf8_decode('Dirección: '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,$configuracion->direccion,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(15,5,utf8_decode('Fecha : '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(30,5,$fecha_inicial.'  -  '.$fecha_final,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Ln();
                    if(count($primer_ingreso)>0){
                        if($sw_ingreso==0){
                            foreach ($primer_ingreso as $item){
                                $pdf->SetFont('Arial','B',11);
                                $pdf->SetFillColor(2,180,100);//Fondo verde de celda
                                $pdf->SetTextColor(240, 255, 240); //Letra color blanco         
                                $pdf->Cell(80,5,'Descripcion',1,0,'L',true);
                                $pdf->Cell(20,5,'monto',1,0,'L',true);
                                $pdf->Cell(30,5,'tipo',1,1,'L',true);
                                $pdf->SetFont('Arial','',9);
                                $pdf->SetFillColor(255, 255, 255 ); //gris tenue de cada fila
                                $pdf->SetTextColor(3, 3, 3); //Color del texto: Negro
                                $pdf->Cell(80,5,$item->descripcion,1,0,'L',true);
                                $pdf->Cell(20,5,$item->monto_total.' bs',1,0,'L',true);
                                $pdf->Cell(30,5,$item->tipo_ingreso,1,1,'L',true);
                                $sumx+=$item->monto_total;
                            }
                            $sw_ingreso=1;
                        }
                    }
                    $pdf->Ln();
                    $pdf->SetFillColor(52, 152, 219);//Fondo verde de celda
                    $pdf->SetTextColor(240, 255, 240); //Letra color blanco
                    $pdf->Cell(10,5,'Nº',1,0,'L',true);
                    $pdf->Cell(65,5,'Producto',1,0,'L',true);
                    $pdf->Cell(15,5,'Almacen',1,0,'L',true);
                    $pdf->Cell(30,5,'precio de venta',1,0,'L',true);
                    $pdf->Cell(20,5,'cantidad',1,0,'L',true);
                    $pdf->Cell(20,5,'tipo venta',1,0,'L',true);
                    $pdf->MultiCell(30,5,utf8_decode('sub total'),1,1,'L',true);
                    $pdf->SetFont('Arial','',8);
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
                $pdf->Cell(65,5,$row->nombre_producto,'LR',0,'L',true);
                $pdf->Cell(15,5,$row->nombre_almacen,'LR',0,'L',true);
                $pdf->Cell(30,5,$row->precio_unidad.' bs','LR',0,'L',true);
                $pdf->Cell(20,5,$row->cant_productos,'LR',0,'L',true);

                $pdf->SetFont('Arial','B',8);
                if($row->tipo_pago==0){
                    $pdf->Cell(20,5,'CREDITO','LR',0,'L',true);
                }else{
                    $pdf->Cell(20,5,'CONTADO','LR',0,'L',true);
                    
                }
                $pdf->SetFont('Arial','',8);
               // $total+=($row->precio_unidad*$row->cant_productos);
                $pdf->Cell(30,5,number_format($row->precio_unidad*$row->cant_productos,2,'.','').' bs','LR',1,'L',true); // L= IZQUIERDA R= DERECHA
                $total_completo+=$row->precio_unidad*$row->cant_productos;
                if ($contador%19==0){$sw=1;}
                $contador++;
            }
           
            $pdf->ln();
            $pdf->SetFont('Arial','',12);
           
            $pdf->ln();
            $pdf->Cell(45,5,utf8_decode('Monto total al contado: '),0,0,'L');
            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(20,5,number_format($total_completo,2,'.','').' bs',0,1,'L');
            
            $pdf->SetFont('Arial','',12);
            $pdf->Cell(50,5,utf8_decode('Monto total de intereses: '),0,0,'L');
            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(20,5,number_format($intereses,2,'.','').' bs',0,1,'L');
            $pdf->SetFont('Arial','',12);
            $pdf->Cell(50,5,utf8_decode('Monto total de descuento: '),0,0,'L');
            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(20,5,number_format($descuento,2,'.','').' bs',0,1,'L');
            $pdf->SetFont('Arial','',12);
            $pdf->Cell(30,5,utf8_decode('Monto en caja:'),0,0,'L');
            $pdf->SetTextColor(52, 152, 219);
            $pdf->SetFont('Arial','B',15);
            $pdf->Cell(20,5,number_format($total_completo+$intereses-$descuento+$sumx,2,'.','').' bs',0,1,'L');
             $pdf->SetTextColor(3, 3, 3);
            //$pdf->ln();
        
            $sqlincompleto="SELECT SUM(detalle_ventas.cantidad) as cant_productos, detalle_ventas.precio_unidad as precio_unidad , productos.nombre as nombre_producto, almacens.sigla as nombre_almacen,ventas.tipo_pago as tipo_pago
            FROM ventas,detalle_ventas,producto_almacens,productos , almacens
            WHERE detalle_ventas.id_venta=ventas.id AND
            detalle_ventas.id_producto=producto_almacens.id_producto AND
            detalle_ventas.id_almacen=producto_almacens.id_almacen AND
            producto_almacens.id_producto=productos.id AND
            producto_almacens.id_almacen=almacens.id AND
            ventas.estado=-1 AND
            ventas.tipo_pago=0 AND
            concat( ventas.fecha, ' ', ventas.hora) >='$fecha_inicial' AND
            concat( ventas.fecha, ' ', ventas.hora) <='$fecha_final' AND
            ventas.id_caja=$id_caja
            GROUP BY  precio_unidad , nombre_producto, nombre_almacen,tipo_pago";
        
        $datos_ventas_imcompleto= (array) DB::select($sqlincompleto); 

        $sw=1;
        $contador = 1;
        $color=0;
        $total_imcompleto=0;
        
        foreach ($datos_ventas_imcompleto as $row){
            if ($sw==1){
                $pdf->AddPage();
                $pdf->SetMargins(5,5,5);
                $pdf->SetTitle("INFORME");
                $pdf->SetFont('Arial','B',15);
                $pdf->image(asset('vendor/adminlte/dist/img/AdminLTELogo.png'),10,5,10,10,'PNG');
                $pdf->Cell(190,4,'',0,1,'C');
                $pdf->Cell(190,4,'INFORME DE CAJA "IMCOMPLETOS CREDITO"',0,1,'C');
                $pdf->Ln(6);
                $pdf->SetFont('Arial','B',10);
                $pdf->SetFont('Arial','',9);
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(17,5,utf8_decode('Dirección: '),0,0,'L');
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(50,5,$configuracion->direccion,0,1,'L');
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(15,5,utf8_decode('Fecha : '),0,0,'L');
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(30,5,$fecha_inicial.'  -  '.$fecha_final,0,1,'L');
                $pdf->SetFont('Arial','B',9);
                $pdf->Ln(10);
                $pdf->SetFillColor(52, 152, 219);//Fondo verde de celda
                $pdf->SetTextColor(240, 255, 240); //Letra color blanco
                $pdf->Cell(10,5,'Nº',1,0,'L',true);
                $pdf->Cell(65,5,'Producto',1,0,'L',true);
                $pdf->Cell(15,5,'Almacen',1,0,'L',true);
                $pdf->Cell(30,5,'precio de venta',1,0,'L',true);
                $pdf->Cell(20,5,'cantidad',1,0,'L',true);
                $pdf->Cell(20,5,'tipo venta',1,0,'L',true);
                $pdf->MultiCell(30,5,utf8_decode('sub total'),1,1,'L',true);
                $pdf->SetFont('Arial','',8);
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
            $pdf->Cell(65,5,$row->nombre_producto,'LR',0,'L',true);
            $pdf->Cell(15,5,$row->nombre_almacen,'LR',0,'L',true);
            $pdf->Cell(30,5,$row->precio_unidad.' bs','LR',0,'L',true);
            $pdf->Cell(20,5,$row->cant_productos,'LR',0,'L',true);

            $pdf->SetFont('Arial','B',8);
            if($row->tipo_pago==0){
                $pdf->Cell(20,5,'CREDITO','LR',0,'L',true);
            }else{
                $pdf->Cell(20,5,'CONTADO','LR',0,'L',true);
                
            }
            $pdf->SetFont('Arial','',8);
           // $total+=($row->precio_unidad*$row->cant_productos);
            $pdf->Cell(30,5,number_format($row->precio_unidad*$row->cant_productos,2,'.','').' bs','LR',1,'L',true); // L= IZQUIERDA R= DERECHA
            $total_imcompleto+=$row->precio_unidad*$row->cant_productos;
            if ($contador%19==0){$sw=1;}
            $contador++;
        }
       
        $pdf->ln();
        $pdf->SetFont('Arial','',12);
       
        $pdf->ln();
        $pdf->Cell(45,5,utf8_decode('Monto total credito: '),0,0,'L');
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(20,5,number_format($total_imcompleto,2,'.','').' bs',0,1,'L');
       


        // monto de ventas completas al contado
        $sql6="SELECT  SUM(detalle_ventas.subtotal) as total_productos , SUM(detalle_ventas.cantidad) as cant_productos, productos.nombre as nombre_producto
        FROM ventas,detalle_ventas,producto_almacens,productos , almacens
        WHERE detalle_ventas.id_venta=ventas.id AND
        detalle_ventas.id_producto=producto_almacens.id_producto AND
        detalle_ventas.id_almacen=producto_almacens.id_almacen AND
        producto_almacens.id_producto=productos.id AND
        producto_almacens.id_almacen=almacens.id AND
        ventas.estado=1 AND
        concat( ventas.fecha, ' ', ventas.hora) >='$fecha_inicial' AND
        concat( ventas.fecha, ' ', ventas.hora) <='$fecha_final' AND
        ventas.id_caja=$id_caja
        GROUP BY  nombre_producto";

        $datos_completados= (array) DB::select($sql6); 
    
            $sw=1;
            $contador = 1;
            $color=0;
            $total=0;
            foreach ($datos_completados as $row){
                if ($sw==1){
                    $pdf->AddPage();
                    $pdf->SetMargins(5,5,5);
                    $pdf->SetTitle("INFORME");
                    $pdf->SetFont('Arial','B',16);
                    $pdf->image(asset('vendor/adminlte/dist/img/AdminLTELogo.png'),10,5,10,10,'PNG');
                    $pdf->Cell(190,4,'',0,1,'C');
                    $pdf->Cell(190,4,'INFORME DE VENTAS "COMPLETADOS GENERAL"',0,1,'C');
                    $pdf->Ln(6);
                    $pdf->SetFont('Arial','B',10);
                    $pdf->SetFont('Arial','',9);
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(17,5,utf8_decode('Dirección: '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,$configuracion->direccion,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(18,5,utf8_decode('Fecha : '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,$fecha_inicial.'  -  '.$fecha_final,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Ln(10);
                    $pdf->SetFillColor(46, 204, 113 );//Fondo verde de celda
                    $pdf->SetTextColor(240, 255, 240); //Letra color blanco
                    $pdf->Cell(10,5,'Nº',1,0,'L',true);
                    $pdf->Cell(50,5,'nombre producto',1,0,'L',true);
                    $pdf->Cell(30,5,'cantidad',1,0,'L',true);
                    $pdf->Cell(40,5,'total generado',1,1,'L',true);
                    $pdf->SetFont('Arial','',8);
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
                $pdf->Cell(50,5,$row->nombre_producto,'LR',0,'L',true);
                $pdf->Cell(30,5,$row->cant_productos,'LR',0,'L',true);
                $pdf->Cell(40,5,$row->total_productos.' bs','LR',1,'L',true);
                $total+=$row->total_productos;
                if ($contador%19==0){$sw=1;}
                $contador++;
            }
           
            $pdf->ln();
            $pdf->SetFont('Arial','',12);
            
            $pdf->ln();
            $pdf->Cell(30,5,utf8_decode('Monto Total: '),0,0,'L');
            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(20,5,number_format($total,2,'.','').' bs',0,1,'L');
            
        // monto de ventas intereses y descuento publico y clientes
        $sql7="SELECT 
        (SELECT SUM(va.monto_total)
        FROM ventas va 
        WHERE 
            concat(va.fecha, ' ',va.hora) >='$fecha_inicial' AND
            concat(va.fecha, ' ',va.hora) <='$fecha_final' AND
            va.estado=-1 AND
            va.tipo_pago=0 AND
            va.id_caja=v.id_caja AND
            va.id_cliente=clientes.id) as total_deuda, 
            SUM(v.descuento) as total_descuento , 
            SUM(v.interes) as total_interes , 
            clientes.nombre as nombre_cliente,
            clientes.apellidos as apellido_cliente
        FROM ventas v , clientes
        WHERE
        v.id_cliente=clientes.id AND
        (v.estado=1 OR v.estado=-1) AND
        concat( v.fecha, ' ', v.hora) >='$fecha_inicial' AND
        concat( v.fecha, ' ', v.hora) <='$fecha_final' AND
        v.id_caja=$id_caja
        GROUP BY  nombre_cliente,apellido_cliente,total_deuda";

        $datos_completados= (array) DB::select($sql7); 
    
            $sw=1;
            $contador = 1;
            $color=0;
            $interes=0;
            $descuento=0;
            $deuda=0;
            foreach ($datos_completados as $row){
                if ($sw==1){
                    $pdf->AddPage();
                    $pdf->SetMargins(5,5,5);
                    $pdf->SetTitle("INFORME");
                    $pdf->SetFont('Arial','B',16);
                    $pdf->image(asset('vendor/adminlte/dist/img/AdminLTELogo.png'),10,5,10,10,'PNG');
                    $pdf->Cell(190,4,'',0,1,'C');
                    $pdf->Cell(190,4,'INFORME DE VENTAS "iNTERES,DESCUENTOS Y DEUDA"',0,1,'C');
                    $pdf->Ln(6);
                    $pdf->SetFont('Arial','B',10);
                    $pdf->SetFont('Arial','',9);
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(17,5,utf8_decode('Dirección: '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,$configuracion->direccion,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(18,5,utf8_decode('Fecha : '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,$fecha_inicial.'  -  '.$fecha_final,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Ln(10);
                    $pdf->SetFillColor(46, 204, 113 );//Fondo verde de celda
                    $pdf->SetTextColor(240, 255, 240); //Letra color blanco
                    $pdf->Cell(10,5,'Nº',1,0,'L',true);
                    $pdf->Cell(50,5,'nombre cliente',1,0,'L',true);
                    $pdf->Cell(30,5,'interes',1,0,'L',true);
                    $pdf->Cell(30,5,'descuento',1,0,'L',true);
                    $pdf->Cell(40,5,'deuda sin pagar',1,1,'L',true);
                    $pdf->SetFont('Arial','',8);
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
               
                if($descuento_publico>0){
                    $pdf->Cell(10,5,$contador,'LR',0,'L',true);
                    $pdf->Cell(50,5,'publico general','LR',0,'L',true);
                    $pdf->Cell(30,5,'0 bs','LR',0,'L',true);
                    $pdf->Cell(30,5,$descuento_publico.' bs','LR',0,'L',true);
                    $pdf->Cell(40,5,'0 bs','LR',1,'L',true);
                    $descuento+=$descuento_publico;
                    $descuento_publico=-1;
                }
                
                $pdf->Cell(10,5,$contador,'LR',0,'L',true);
                $pdf->Cell(50,5,$row->nombre_cliente.' '.$row->apellido_cliente,'LR',0,'L',true);
                $pdf->Cell(30,5,$row->total_interes.' bs','LR',0,'L',true);
                $pdf->Cell(30,5,$row->total_descuento.' bs','LR',0,'L',true);
                $pdf->Cell(40,5,$row->total_deuda.' bs','LR',1,'L',true);
                $interes+=$row->total_interes;
                $descuento+=$row->total_descuento;
                $deuda+=$row->total_deuda;
                if ($contador%19==0){$sw=1;}
                $contador++;
            }
           
            $pdf->ln();
            $pdf->SetFont('Arial','',12);
            $pdf->Cell(45,5,utf8_decode('Monto Total Interes : '),0,0,'L');
            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(20,5,number_format($interes,2,'.','').' bs',0,1,'L');
            $pdf->SetFont('Arial','',12);
            $pdf->Cell(50,5,utf8_decode('Monto Total Descuento : '),0,0,'L');
            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(20,5,number_format($descuento,2,'.','').' bs',0,1,'L');
            $pdf->SetFont('Arial','',12);
            $pdf->Cell(50,5,utf8_decode('Monto Total Deuda : '),0,0,'L');
            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(20,5,number_format($deuda,2,'.','').' bs',0,1,'L');
            


    
            $pdf->Output('I','informe_caja_'.$fecha_inicial.' - '.$fecha_final.'pdf');
    }

    public function informe_cajas_servicios(Caja $caja){
        $fecha_inicial=$caja->fecha_inicio.' '.$caja->hora_inicio;
        $fecha_final=$caja->fecha_final.' '.$caja->hora_final;
        $sql="SELECT sg.descripcion,sg.created_at, sg.monto_total,interes,sg.id_cliente,sg.tipo_pago
        FROM servicio_generals sg
        WHERE sg.id_caja=$caja->id AND
        sg.estado=1 AND
        sg.created_at >= '$fecha_inicial' AND
        sg.created_at <= '$fecha_final' ";

        $sql2="SELECT i.descripcion, i.monto_total,i.tipo_ingreso
        FROM ingresos i
        WHERE i.id_caja_primaria=$caja->id AND
        i.estado=1 AND
        i.created_at >= '$fecha_inicial' AND
        i.created_at <= '$fecha_final'";
        
       
        
        $configuracion=Configuracion::all()->first();
        $datos = DB::select($sql);
        if(count($datos)<=0){
            return "no hay registros";
        }
        $primer_ingreso = DB::select($sql2);
        $pdf = new Fpdf('P','mm',array(200,200));
        
            $sw=1;
            $contador = 1;
            $color=0;
            $sum=0;
            $sw_ingreso=0;

            foreach ($datos as $row){
                if ($sw==1){
                    $pdf->AddPage();
                    $pdf->SetMargins(5,5,5);
                    $pdf->SetTitle("Informe");
                    $pdf->SetFont('Arial','B',16);
                    $pdf->image(asset('vendor/adminlte/dist/img/AdminLTELogo.png'),10,5,10,10,'PNG');
                    $pdf->Cell(190,4,'',0,1,'C');
                    $pdf->Cell(190,4,'INFORME DE '. strtoupper($caja->nombre),0,1,'C');
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
                    $pdf->Cell(50,5,date("Y-m-d H:i:s"),0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->SetFont('Arial','B',11);
                    $pdf->Ln();
                    if(count($primer_ingreso)>0){
                    if($sw_ingreso==0){
                        foreach ($primer_ingreso as $item){
                            $pdf->SetFont('Arial','B',11);
                            $pdf->SetFillColor(2,180,100);//Fondo verde de celda
                            $pdf->SetTextColor(240, 255, 240); //Letra color blanco         
                            $pdf->Cell(80,5,'Descripcion',1,0,'L',true);
                            $pdf->Cell(20,5,'monto',1,0,'L',true);
                            $pdf->Cell(30,5,'tipo',1,1,'L',true);
                            $pdf->SetFont('Arial','',9);
                            $pdf->SetFillColor(255, 255, 255 ); //gris tenue de cada fila
                            $pdf->SetTextColor(3, 3, 3); //Color del texto: Negro
                            $pdf->Cell(80,5,$item->descripcion,1,0,'L',true);
                            $pdf->Cell(20,5,$item->monto_total.' bs',1,0,'L',true);
                            $pdf->Cell(30,5,$item->tipo_ingreso,1,1,'L',true);
                            $sum+=$item->monto_total;
                        }
                        $sw_ingreso=1;
                    }
                    }
                    $pdf->Ln();
                    $pdf->SetFont('Arial','B',11);
                    $pdf->SetFillColor(2,157,130);//Fondo verde de celda
                    $pdf->SetTextColor(240, 255, 240); //Letra color blanco
                    $pdf->Cell(10,5,'Nº',1,0,'L',true);
                    $pdf->Cell(65,5,'Descripcion',1,0,'L',true);
                    $pdf->Cell(20,5,'monto',1,0,'L',true);
                    $pdf->Cell(20,5,'interes',1,0,'L',true);
                    $pdf->Cell(30,5,'fecha',1,0,'L',true);
                    $pdf->Cell(40,5,'cliente',1,1,'L',true);
                    $pdf->SetFont('Arial','',8);
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
                $pdf->Cell(65,5,$row->descripcion,'LR',0,'L',true);
                $pdf->Cell(20,5,$row->monto_total.' bs','LR',0,'L',true);
                $pdf->Cell(20,5,$row->interes.' bs','LR',0,'L',true);
                $pdf->Cell(30,5,$row->created_at,'LR',0,'L',true);
                if($row->id_cliente!=null){
                    $cliente=Cliente::findOrFail($row->id_cliente);
                    $pdf->Cell(40,5,$cliente->nombre.' '.$cliente->apellidos,'LR',1,'L',true);
                }else{
                    $pdf->Cell(40,5,'publico general','LR',1,'L',true); 
                }
              
                if ($contador%19==0){$sw=1;}
                $contador++;
                $sum+=$row->monto_total+$row->interes;
            }
           
            $pdf->ln();
            $pdf->SetFont('Arial','',12);
            $pdf->Cell(30,5,utf8_decode('Total generado: '),0,0,'L');
            $pdf->SetFont('Arial','',14);
            $pdf->Cell(20,5,number_format($sum,2,'.','').' bs',0,1,'L');



            ///////////////////////
            $sqly="SELECT sg.descripcion,sg.monto_total,sg.interes,sg.tipo_pago,sg.fecha_deuda,sg.created_at
            FROM servicio_generals sg
            WHERE sg.fecha_deuda>='$fecha_inicial' AND
            sg.id_caja=$caja->id";
            $datos_deuda_pagada = DB::select($sqly);

            $sw=1;
            $contador = 1;
            $color=0;
            $sum=0;
        if(count($datos_deuda_pagada)>0){
            foreach ($datos_deuda_pagada as $row){
                if ($sw==1){
                    $pdf->AddPage();
                    $pdf->SetMargins(5,5,5);
                    $pdf->SetTitle("Informe");
                    $pdf->SetFont('Arial','B',16);
                    $pdf->image(asset('vendor/adminlte/dist/img/AdminLTELogo.png'),10,5,10,10,'PNG');
                    $pdf->Cell(190,4,'',0,1,'C');
                    $pdf->Cell(190,4,'INFORME DE DEUDAS PAGADAS',0,1,'C');
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
                    $pdf->Cell(50,5,date("Y-m-d H:i:s"),0,1,'L');
                    
                    $pdf->Ln();
                    $pdf->SetFont('Arial','B',11);
                    $pdf->SetFillColor(2,157,130);//Fondo verde de celda
                    $pdf->SetTextColor(240, 255, 240); //Letra color blanco
                    $pdf->Cell(10,5,'Nº',1,0,'L',true);
                    $pdf->Cell(65,5,'Descripcion',1,0,'L',true);
                    $pdf->Cell(20,5,'monto',1,0,'L',true);
                    $pdf->Cell(20,5,'interes',1,0,'L',true);
                    $pdf->Cell(30,5,'fecha',1,0,'L',true);
                    $pdf->Cell(30,5,'fecha_deuda',1,1,'L',true);
                    $pdf->SetFont('Arial','',8);
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
                $pdf->Cell(65,5,$row->descripcion,'LR',0,'L',true);
                $pdf->Cell(20,5,$row->monto_total.' bs','LR',0,'L',true);
                $pdf->Cell(20,5,$row->interes.' bs','LR',0,'L',true);
                $pdf->Cell(30,5,$row->created_at,'LR',0,'L',true);
                $pdf->Cell(30,5,$row->fecha_deuda,'LR',1,'L',true);
                /*if($row->id_cliente!=null){
                    $cliente=Cliente::findOrFail($row->id_cliente);
                    $pdf->Cell(40,5,$cliente->nombre.' '.$cliente->apellidos,'LR',1,'L',true);
                }else{
                    $pdf->Cell(40,5,'publico general','LR',1,'L',true); 
                }*/
                
              
                if ($contador%19==0){$sw=1;}
                $contador++;
                $sum+=$row->monto_total+$row->interes;
            }
           
            $pdf->ln();
            $pdf->SetFont('Arial','',12);
            $pdf->Cell(40,5,utf8_decode('Total deuda pagado: '),0,0,'L');
            $pdf->SetFont('Arial','',14);
            $pdf->Cell(20,5,number_format($sum,2,'.','').' bs',0,1,'L');
        }
            
            $pdf->Output('I','informe_'.$caja->nombre.'_'.date("Y-m-d H:i:s").'.pdf');

    }


    public function reporte_informe2($id_caja,$tipo){
        $sum_servicios=0;
        $sumx=0;
        $sumy=0;
        $sumz=0;
        $total_completo=0;
        $caja=Caja::findOrFail($id_caja);
        $nombre_caja=$caja->nombre;
        $fecha_inicial=$caja->fecha_inicio . " ".$caja->hora_inicio;
        if($tipo==1){
            $fecha_final=$caja->fecha_final . " ".$caja->hora_final;
        }else{
            if($caja->estado==1){
                $fecha_final=date("Y-m-d")." ".date("H:i:s");
            }else{
                return "caja cerrada por superiores";
            }
        }
        $sumatoria_pagos_interes=planpago::where('created_at','>=',$fecha_inicial)->where('created_at','<=',$fecha_final)->where('estado','=',1)->sum('interes');
        //$sumatoria_ventas_descuento=Venta::where('fecha','>=',$fecha_inicial)->where('fecha','<=',$fecha_final)->where('estado','=',1)->where('id_caja','=',$caja->id)->sum('descuento');
        $descuento_sql="SELECT IFNULL(SUM(ventas.descuento),0) as descuento
        FROM ventas 
        WHERE 
        concat( ventas.fecha, ' ', ventas.hora) >='$fecha_inicial' AND
        concat( ventas.fecha, ' ', ventas.hora) <='$fecha_final' AND
        ventas.estado=1 AND
        ventas.id_caja=$caja->id";
        $sumatoria_ventas_descuento = collect(DB::select($descuento_sql))->first()->descuento;

        $compras_sql="SELECT IFNULL(SUM(compras.monto_total),0) as monto_compras
        FROM compras 
        WHERE 
        concat( compras.fecha, ' ', compras.hora) >='$fecha_inicial' AND
        concat( compras.fecha, ' ', compras.hora) <='$fecha_final' AND
        compras.estado=1 AND
        compras.id_caja=$caja->id AND
        compras.estado=1";
        
        $sumatoria_compras = collect(DB::select($compras_sql))->first()->monto_compras;

        $sql="SELECT SUM(detalle_ventas.cantidad) as cant_productos, detalle_ventas.precio_unidad as precio_unidad , productos.nombre as nombre_producto, almacens.sigla as nombre_almacen,ventas.tipo_pago as tipo_pago
        FROM ventas,detalle_ventas,producto_almacens,productos , almacens
        WHERE detalle_ventas.id_venta=ventas.id AND
        detalle_ventas.id_producto=producto_almacens.id_producto AND
        detalle_ventas.id_almacen=producto_almacens.id_almacen AND
        producto_almacens.id_producto=productos.id AND
        producto_almacens.id_almacen=almacens.id AND
        ventas.estado=1 AND
        concat( ventas.fecha, ' ', ventas.hora) >='$fecha_inicial' AND
        concat( ventas.fecha, ' ', ventas.hora) <='$fecha_final' AND
        ventas.id_caja ='$caja->id'
        GROUP BY  precio_unidad , nombre_producto, nombre_almacen,tipo_pago
        ORDER BY cant_productos DESC";
        $datos_ventas_general= DB::select($sql);
       
    

    $configuracion=Configuracion::all()->first();
    $pdf = new Fpdf('P','mm',array(200,200));
    if(count($datos_ventas_general)>0){
            $sw=1;
            $contador = 1;
            $color=0;
            $total_completo=0;
            foreach ($datos_ventas_general as $row){
                if ($sw==1){
                    $pdf->AddPage();
                    $pdf->SetMargins(5,5,5);
                    $pdf->SetTitle("INFORME");
                    $pdf->SetFont('Arial','B',15);
                    $pdf->image(asset('vendor/adminlte/dist/img/AdminLTELogo.png'),10,5,10,10,'PNG');
                    $pdf->Cell(190,4,'',0,1,'C');
                    $pdf->Cell(190,4,'INFORME DE CAJA "VENTAS"',0,1,'C');
                    $pdf->Ln();
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(17,5,utf8_decode('Dirección: '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,$configuracion->direccion,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(15,5,utf8_decode('Fecha : '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(30,5,$fecha_inicial.'  -  '.$fecha_final,0,1,'L');
                    $pdf->SetFont('Arial','B',8);
                    $pdf->Ln();
                    
                
                    $pdf->SetFillColor(52, 100, 219);//Fondo verde de celda
                    $pdf->SetTextColor(240, 255, 240); //Letra color blanco
                    $pdf->Cell(10,5,utf8_decode('Nº'),1,0,'L',true);
                    $pdf->Cell(65,5,'PRODUCTO',1,0,'L',true);
                    $pdf->Cell(20,5,'ALMACEN',1,0,'L',true);
                    $pdf->Cell(25,5,'PRECIO VENTA',1,0,'L',true);
                    $pdf->Cell(20,5,'CANTIDAD',1,0,'L',true);
                    $pdf->Cell(20,5,'TIPO VENTA',1,0,'L',true);
                    $pdf->MultiCell(30,5,utf8_decode('SUB TOTAL'),1,1,'L',true);
                    $pdf->SetFont('Arial','',8);
                 
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

                $pdf->Cell(10,5,$contador,1,0,'L',true);
                $pdf->Cell(65,5,$row->nombre_producto,1,0,'L',true);
                $pdf->Cell(20,5,$row->nombre_almacen,1,0,'L',true);
                $pdf->Cell(25,5,$row->precio_unidad.' bs',1,0,'L',true);
                $pdf->Cell(20,5,$row->cant_productos,1,0,'L',true);

                $pdf->SetFont('Arial','B',8);
                if($row->tipo_pago==0){
                    $pdf->Cell(20,5,'CREDITO',1,0,'L',true);
                }else{
                    $pdf->Cell(20,5,'CONTADO',1,0,'L',true);
                    
                }
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(30,5,number_format($row->precio_unidad*$row->cant_productos,2,'.','').' bs',1,1,'L',true); // L= IZQUIERDA R= DERECHA
                $total_completo+=($row->precio_unidad*$row->cant_productos);
                if ($contador%19==0){$sw=1;}
                $contador++;
            }
           
            $pdf->ln();
            $pdf->SetFont('Arial','',11);
           
            $pdf->ln();
            $pdf->Cell(50,5,utf8_decode('Monto total ventas: '),0,0,'L');
            $pdf->SetFont('Arial','B',11);
            $pdf->Cell(20,5,number_format($total_completo,2,'.','').' bs',0,1,'L');
            
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(50,5,utf8_decode('Monto total de intereses: '),0,0,'L');
            $pdf->SetFont('Arial','B',11);
            $pdf->Cell(20,5,number_format($sumatoria_pagos_interes,2,'.','').' bs',0,1,'L');
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(50,5,utf8_decode('Monto total de descuento: '),0,0,'L');
            $pdf->SetFont('Arial','B',11);
            $pdf->Cell(20,5,number_format($sumatoria_ventas_descuento,2,'.','').' bs',0,1,'L');
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(50,5,utf8_decode('Total:'),0,0,'L');
            $pdf->SetTextColor(52, 152, 219);
            $pdf->SetFont('Arial','B',11);
            $pdf->Cell(20,5,number_format($total_completo+$sumatoria_pagos_interes-$sumatoria_ventas_descuento,2,'.','').' bs',0,1,'L');
            $pdf->SetTextColor(3, 3, 3);
    } 

    $sql_compras="SELECT a.sigla as nombre_almacen,p.nombre as nombre_producto,dc.precio_unidad as precio_compra,SUM(dc.cantidad) as cantidad_producto
        FROM compras c,detalle_compras dc,producto_almacens pa,productos p,almacens a
        WHERE dc.id_compra=c.id AND
        dc.id_producto=pa.id_producto AND
        dc.id_almacen=pa.id_almacen AND
        pa.id_producto=p.id AND
        pa.id_almacen=a.id AND
        c.id_caja=$caja->id AND
        concat( c.fecha, ' ', c.hora) >='$fecha_inicial' AND
        concat( c.fecha, ' ', c.hora) <='$fecha_final' AND
        c.estado=1
    GROUP BY a.sigla,p.nombre,dc.precio_unidad";
    $datos_compras_general= DB::select($sql_compras);
    if(count($datos_compras_general)>0){
        $sw=1;
        $contador = 1;
        $color=0;
        $total_compras=0;
        foreach ($datos_compras_general as $row){
            if ($sw==1){
                $pdf->AddPage();
                $pdf->SetMargins(5,5,5);
                $pdf->SetTitle("INFORME");
                $pdf->SetFont('Arial','B',15);
                $pdf->image(asset('vendor/adminlte/dist/img/AdminLTELogo.png'),10,5,10,10,'PNG');
                $pdf->Cell(190,4,'',0,1,'C');
                $pdf->Cell(190,4,'INFORME DE CAJA "COMPRAS"',0,1,'C');
                $pdf->Ln();
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(17,5,utf8_decode('Dirección: '),0,0,'L');
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(50,5,$configuracion->direccion,0,1,'L');
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(15,5,utf8_decode('Fecha : '),0,0,'L');
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(30,5,$fecha_inicial.'  -  '.$fecha_final,0,1,'L');
                $pdf->SetFont('Arial','B',8);
                $pdf->Ln();
                
            
                $pdf->SetFillColor(52, 100, 219);//Fondo verde de celda
                $pdf->SetTextColor(240, 255, 240); //Letra color blanco
                $pdf->Cell(10,5,utf8_decode('Nº'),1,0,'L',true);
                $pdf->Cell(80,5,'PRODUCTO',1,0,'L',true);
                $pdf->Cell(20,5,'ALMACEN',1,0,'L',true);
                $pdf->Cell(30,5,'PRECIO COMPRA',1,0,'L',true);
                $pdf->Cell(20,5,'CANTIDAD',1,0,'L',true);
                
                $pdf->MultiCell(30,5,utf8_decode('SUB TOTAL'),1,1,'L',true);
                $pdf->SetFont('Arial','',8);
             
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

            $pdf->Cell(10,5,$contador,1,0,'L',true);
            $pdf->Cell(80,5,$row->nombre_producto,1,0,'L',true);
            $pdf->Cell(20,5,$row->nombre_almacen,1,0,'L',true);
            $pdf->Cell(30,5,$row->precio_compra.' bs',1,0,'L',true);
            $pdf->Cell(20,5,$row->cantidad_producto,1,0,'L',true);
            $pdf->Cell(30,5,number_format($row->precio_compra*$row->cantidad_producto,2,'.','').' bs',1,1,'L',true); // L= IZQUIERDA R= DERECHA
            $total_compras+=($row->precio_compra*$row->cantidad_producto);
            if ($contador%19==0){$sw=1;}
            $contador++;
        }
       
        $pdf->ln();
        $pdf->SetFont('Arial','',11);
       
        $pdf->ln();
        $pdf->Cell(50,5,utf8_decode('Monto total compras: '),0,0,'L');
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(20,5,number_format($total_compras,2,'.','').' bs',0,1,'L');        
        $pdf->SetTextColor(3, 3, 3);
    } 


    $sqlx="SELECT i.descripcion, i.monto_total,i.tipo_ingreso
    FROM ingresos i
    WHERE i.id_caja_primaria=$id_caja AND
    i.estado=1 AND
    i.created_at >= '$fecha_inicial' AND
    i.created_at <= '$fecha_final'";
    $ingresos = DB::select($sqlx); 

    if(count($ingresos)>0){
            
            $sw=1;
            $contador = 1;
            $color=0;
            $sumx=0;
        
        foreach ($ingresos as $row){
            if ($sw==1){
                $pdf->AddPage();
                $pdf->SetMargins(5,5,5);
                $pdf->SetTitle("INFORME");
                $pdf->SetFont('Arial','B',15);
                $pdf->image(asset('vendor/adminlte/dist/img/AdminLTELogo.png'),10,5,10,10,'PNG');
                $pdf->Cell(190,4,'',0,1,'C');
                $pdf->Cell(190,4,'INFORME DE CAJA "INGRESOS"',0,1,'C');
                $pdf->Ln();
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(17,5,utf8_decode('Dirección: '),0,0,'L');
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(50,5,$configuracion->direccion,0,1,'L');
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(15,5,utf8_decode('Fecha : '),0,0,'L');
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(30,5,$fecha_inicial.'  -  '.$fecha_final,0,1,'L');
                $pdf->SetFont('Arial','B',9);
                $pdf->Ln();
                $pdf->SetFillColor(52, 100, 219);//Fondo verde de celda
                $pdf->SetTextColor(240, 255, 240); //Letra color blanco
                $pdf->Cell(10,5,utf8_decode('Nº'),1,0,'L',true);
                $pdf->Cell(130,5,'DESCRIPCION',1,0,'L',true);
                $pdf->Cell(30,5,'MONTO',1,0,'L',true);
                $pdf->Cell(20,5,'TIPO',1,1,'L',true);
                
                $pdf->SetFont('Arial','',10);
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

            $pdf->Cell(10,5,$contador,1,0,'L',true);
            $pdf->Cell(130,5,$row->descripcion,1,0,'L',true);
            $pdf->Cell(30,5,$row->monto_total.' bs',1,0,'L',true);
            $pdf->Cell(20,5,$row->tipo_ingreso,1,1,'L',true);
            $sumx+=$row->monto_total;
            if ($contador%19==0){$sw=1;}
            $contador++;
        }
       
        $pdf->ln();
        $pdf->SetFont('Arial','',11);
       
        $pdf->ln();
        $pdf->Cell(30,5,utf8_decode('Total Ingreso: '),0,0,'L');
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(20,5,number_format($sumx,2,'.','').' bs',0,1,'L');
    }



    $sqly="SELECT i.descripcion, i.monto_total,i.tipo_egreso
    FROM egresos i
    WHERE i.id_caja_primaria=$id_caja AND
    i.estado=1 AND
    i.created_at >= '$fecha_inicial' AND
    i.created_at <= '$fecha_final'";
    $egresos = DB::select($sqly);



    if(count($egresos)>0){
        
        $sw=1;
        $contador = 1;
        $color=0;
        $sumy=0;
    
        foreach ($egresos as $row){
            if ($sw==1){
                $pdf->AddPage();
                $pdf->SetMargins(5,5,5);
                $pdf->SetTitle("INFORME");
                $pdf->SetFont('Arial','B',15);
                $pdf->image(asset('vendor/adminlte/dist/img/AdminLTELogo.png'),10,5,10,10,'PNG');
                $pdf->Cell(190,4,'',0,1,'C');
                $pdf->Cell(190,4,'INFORME DE CAJA "EGRESOS PROPIOS"',0,1,'C');
                $pdf->Ln();
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(17,5,utf8_decode('Dirección: '),0,0,'L');
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(50,5,$configuracion->direccion,0,1,'L');
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(15,5,utf8_decode('Fecha : '),0,0,'L');
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(30,5,$fecha_inicial.'  -  '.$fecha_final,0,1,'L');
                $pdf->SetFont('Arial','B',9);
                $pdf->Ln();
                $pdf->SetFillColor(52, 100, 219);//Fondo verde de celda
                $pdf->SetTextColor(240, 255, 240); //Letra color blanco
                $pdf->Cell(10,5,utf8_decode('Nº'),1,0,'L',true);
                $pdf->Cell(130,5,'DESCRIPCION',1,0,'L',true);
                $pdf->Cell(30,5,'MONTO',1,0,'L',true);
                $pdf->Cell(20,5,'TIPO',1,1,'L',true);
                
                $pdf->SetFont('Arial','',10);
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
    
            $pdf->Cell(10,5,$contador,1,0,'L',true);
            $pdf->Cell(130,5,$row->descripcion,1,0,'L',true);
            $pdf->Cell(30,5,$row->monto_total.' bs',1,0,'L',true);
            $pdf->Cell(20,5,$row->tipo_egreso,1,1,'L',true);
            $sumy+=$row->monto_total;
            if ($contador%19==0){$sw=1;}
            $contador++;
        }
   
        $pdf->ln();
        $pdf->SetFont('Arial','',11);
    
        $pdf->ln();
        $pdf->Cell(30,5,utf8_decode('Total Egreso: '),0,0,'L');
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(20,5,number_format($sumy,2,'.','').' bs',0,1,'L');
    }

    $sqlz="SELECT i.descripcion, i.monto_total,i.tipo_ingreso
    FROM ingresos i
    WHERE i.id_caja_primaria=7 AND
    i.id_caja_secundaria=$id_caja AND
    i.estado=1 AND
    i.created_at >= '$fecha_inicial' AND
    i.created_at <= '$fecha_final'";
    $egresos_general = DB::select($sqlz);

    if(count($egresos_general)>0){
        
        $sw=1;
        $contador = 1;
        $color=0;
        $sumz=0;
    
        foreach ($egresos_general as $row){
            if ($sw==1){
                $pdf->AddPage();
                $pdf->SetMargins(5,5,5);
                $pdf->SetTitle("INFORME");
                $pdf->SetFont('Arial','B',15);
                $pdf->image(asset('vendor/adminlte/dist/img/AdminLTELogo.png'),10,5,10,10,'PNG');
                $pdf->Cell(190,4,'',0,1,'C');
                $pdf->Cell(190,4,'INFORME DE CAJA "EGRESOS CAJA GENERAL"',0,1,'C');
                $pdf->Ln();
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(17,5,utf8_decode('Dirección: '),0,0,'L');
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(50,5,$configuracion->direccion,0,1,'L');
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(15,5,utf8_decode('Fecha : '),0,0,'L');
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(30,5,$fecha_inicial.'  -  '.$fecha_final,0,1,'L');
                $pdf->SetFont('Arial','B',9);
                $pdf->Ln();
                $pdf->SetFillColor(52, 100, 219);//Fondo verde de celda
                $pdf->SetTextColor(240, 255, 240); //Letra color blanco
                $pdf->Cell(10,5,utf8_decode('Nº'),1,0,'L',true);
                $pdf->Cell(120,5,'DESCRIPCION',1,0,'L',true);
                $pdf->Cell(30,5,'MONTO',1,0,'L',true);
                $pdf->Cell(30,5,'TIPO',1,1,'L',true);
                
                $pdf->SetFont('Arial','',10);
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
    
            $pdf->Cell(10,5,$contador,1,0,'L',true);
            $pdf->Cell(120,5,$row->descripcion,1,0,'L',true);
            $pdf->Cell(30,5,$row->monto_total.' bs',1,0,'L',true);
            if($row->tipo_ingreso==null){
                $pdf->Cell(30,5,'CAJA GENERAL',1,1,'L',true);
            }else{
                $pdf->Cell(30,5,$row->tipo_ingreso,1,1,'L',true);
            }
            
            $sumz+=$row->monto_total;
            if ($contador%19==0){$sw=1;}
            $contador++;
        }
   
        $pdf->ln();
        $pdf->SetFont('Arial','',11);
    
        $pdf->ln();
        $pdf->Cell(30,5,utf8_decode('Total Egreso: '),0,0,'L');
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(20,5,number_format($sumz,2,'.','').' bs',0,1,'L');
    }


    $sql_servicios="SELECT *
    FROM servicio_generals s
    WHERE 
    s.id_caja=$id_caja AND
    s.estado=1 AND
    s.created_at >= '$fecha_inicial' AND
    s.created_at <= '$fecha_final'
    ORDER BY tipo_pago DESC";

    $servicios = DB::select($sql_servicios);

    if(count($servicios)>0){
        
        $sw=1;
        $contador = 1;
        $color=0;
        $sum_servicios=0;
    
        foreach ($servicios as $row){
            if ($sw==1){
                $pdf->AddPage();
                $pdf->SetMargins(5,5,5);
                $pdf->SetTitle("INFORME");
                $pdf->SetFont('Arial','B',15);
                $pdf->image(asset('vendor/adminlte/dist/img/AdminLTELogo.png'),10,5,10,10,'PNG');
                $pdf->Cell(190,4,'',0,1,'C');
                $pdf->Cell(190,4,'INFORME DE CAJA "SERVICIOS"',0,1,'C');
                $pdf->Ln();
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(17,5,utf8_decode('Dirección: '),0,0,'L');
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(50,5,$configuracion->direccion,0,1,'L');
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(15,5,utf8_decode('Fecha : '),0,0,'L');
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(30,5,$fecha_inicial.'  -  '.$fecha_final,0,1,'L');
                $pdf->SetFont('Arial','B',9);
                $pdf->Ln();
                $pdf->SetFillColor(52, 100, 219);//Fondo verde de celda
                $pdf->SetTextColor(240, 255, 240); //Letra color blanco
                $pdf->Cell(10,5,utf8_decode('Nº'),1,0,'L',true);
                $pdf->Cell(30,5,'CODIGO',1,0,'L',true);
                $pdf->Cell(30,5,'MONTO',1,0,'L',true);
                $pdf->Cell(30,5,'INTERES',1,0,'L',true);
                $pdf->Cell(70,5,'CLIENTE',1,0,'L',true);
                $pdf->Cell(20,5,'TIPO',1,1,'L',true);
                
                $pdf->SetFont('Arial','',10);
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
    
            $pdf->Cell(10,5,$contador,1,0,'L',true);
            $pdf->Cell(30,5,$row->codigo,1,0,'L',true);
            $pdf->Cell(30,5,$row->monto_total.' bs',1,0,'L',true);
            $pdf->Cell(30,5,$row->interes.' bs',1,0,'L',true);
            if($row->id_cliente!= null){
            $cliente=Cliente::findOrFail($row->id_cliente);
            $nombre=$cliente->nombre." ".$cliente->apellidos;
            }else{
                $nombre="PUBLICO GENERAL";
            }

            $pdf->Cell(70,5,$nombre,1,0,'L',true);
            if($row->tipo_pago==0){
                $pdf->Cell(20,5,'CREDITO',1,1,'L',true);
            }else{
                $pdf->Cell(20,5,'CONTADO',1,1,'L',true);
            }
            
            $sum_servicios+=$row->monto_total+$row->interes;
            if ($contador%19==0){$sw=1;}
            $contador++;
        }
   
        $pdf->ln();
        $pdf->SetFont('Arial','',11);
    
        $pdf->ln();
        $pdf->Cell(30,5,utf8_decode('Total Servicios: '),0,0,'L');
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(20,5,number_format($sum_servicios,2,'.','').' bs',0,1,'L');
    }
    
    if(true){
        
        $sw=1;
        $contador = 1;
        $color=0;
        
                $pdf->AddPage();
                $pdf->SetMargins(5,5,5);
                $pdf->SetTitle("INFORME");
                $pdf->SetFont('Arial','B',15);
                $pdf->image(asset('vendor/adminlte/dist/img/AdminLTELogo.png'),10,5,10,10,'PNG');
                $pdf->Cell(190,4,'',0,1,'C');
                $pdf->Cell(190,4,strtoupper('RESUMEN '.$nombre_caja),0,1,'C');
                $pdf->Ln();
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(17,5,utf8_decode('Dirección: '),0,0,'L');
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(50,5,$configuracion->direccion,0,1,'L');
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(15,5,utf8_decode('Fecha : '),0,0,'L');
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(30,5,$fecha_inicial.'  -  '.$fecha_final,0,1,'L');
                $pdf->SetFont('Arial','B',12);
                $pdf->Ln();
                $pdf->SetFillColor(255, 255, 255);//Fondo verde de celda
                $pdf->SetTextColor(0, 0, 0); //Letra color blanco
                $pdf->Cell(47,8,'INGRESOS',0,0,'C',true);
                $pdf->Cell(47,8,'EGRESOS CAJA',0,0,'C',true);
                $pdf->Cell(47,8,'EGRESOS GENERAL',0,0,'C',true);
                $pdf->Cell(47,8,'SERVICIOS',0,1,'C',true);

                
                
                $pdf->SetFont('Arial','',12);
              
    
            if($color==1){
            $pdf->SetFillColor(229, 232, 232 ); //gris tenue de cada fila
            $pdf->SetTextColor(3, 3, 3); //Color del texto: Negro
            $color=0;
            }else{
            $pdf->SetFillColor(255, 255, 255 ); //blanco tenue de cada fila
            $pdf->SetTextColor(3, 3, 3); //Color del texto: Negro
            $color=1;
            }
    
            $pdf->Cell(47,8,$sumx.' bs','LR',0,'C',true);
            $pdf->Cell(47,8,$sumy.' bs','LR',0,'C',true);
            $pdf->Cell(47,8,$sumz.' bs','LR',0,'C',true);
            $pdf->Cell(47,8,$sum_servicios.' bs','LR',1,'C',true);

            $pdf->ln();
            $pdf->SetTextColor(46, 204, 113);
            $pdf->SetFont('Arial','B',16);
            $pdf->Cell(190,8,'RECAUDADO EN VENTA',0,1,'C',true); 
            $pdf->SetFillColor(255, 255, 255);//Fondo verde de celda
            $pdf->SetTextColor(0, 0, 0); //Letra color blanco
            $pdf->SetFont('Arial','B',12);
            $pdf->Cell(47,8,'VENTAS',0,0,'C',true);
            $pdf->Cell(47,8,'INTERESES',0,0,'C',true);
            $pdf->Cell(47,8,'DESCUENTOS',0,0,'C',true);
            $pdf->Cell(47,8,'EGRESOS',0,1,'C',true); 
            $pdf->SetFillColor(255, 255, 255 ); //blanco tenue de cada fila
            $pdf->SetTextColor(3, 3, 3); //Color del texto: Negro
            $pdf->SetFont('Arial','',12);
            $pdf->Cell(47,8,$total_completo.' bs','LR',0,'C',true);
            if($caja->id!=1){
                $sumatoria_pagos_interes=0;
            }
            $pdf->Cell(47,8,$sumatoria_pagos_interes.' bs','LR',0,'C',true);
            $pdf->Cell(47,8,$sumatoria_ventas_descuento.' bs','LR',0,'C',true);
            $pdf->Cell(47,8,$sumatoria_compras.' bs','LR',1,'C',true);
            $pdf->ln();
            $pdf->SetTextColor(3, 3, 3);
            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(190,8,number_format($total_completo+$sumatoria_pagos_interes-$sumatoria_ventas_descuento,2,'.','').' bs',0,1,'C',true);
            
            $sum_general=($total_completo+$sumatoria_pagos_interes-$sumatoria_ventas_descuento);
            
            $pdf->SetTextColor(3, 161, 252);
            $pdf->SetFont('Arial','B',18);
            $pdf->Cell(190,8,'RECAUDADO EN CAJA',0,1,'C',true);  
            $pdf->SetTextColor(3, 3, 3);
            $pdf->SetFont('Arial','B',16);
            $calculo=$sum_general+$sum_servicios-$sumy-$sumz+$sumx-$sumatoria_compras;
            if($calculo>=0){
                $pdf->Cell(190,8,number_format( $calculo,2,'.','').' bs',0,1,'C',true);
            }else{
                $pdf->Cell(190,8,'N bs',0,1,'C',true);
            }
       
    }



    $pdf->Output('I','informe_caja_'.$fecha_inicial.' - '.$fecha_final.'pdf');
    }

    
}
