<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuracion;
use App\Models\Empleado;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; //Extencion para importar imagen
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Codedge\Fpdf\Fpdf\Fpdf;

use App\Exports\UsersExport;
use App\Models\Caja;
use App\Models\Compra;
use App\Models\planpago;
use Maatwebsite\Excel\Facades\Excel;

//use Maatwebsite\Excel\Facades\Excel;
//use Maatwebsite\Excel\Facades\Excel;
//use Maatwebsite\Excel\Excel as Excel;


class HomeController  extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
        $this->middleware('auth');
       
    }


    public function inicio()
    {
        app(IngresoController::class)->recalcular_datos(1);
        app(IngresoController::class)->recalcular_datos(2);
        app(IngresoController::class)->recalcular_datos(3);
        app(IngresoController::class)->recalcular_datos(4);
        app(IngresoController::class)->recalcular_datos(5);
        app(IngresoController::class)->recalcular_datos(6);
        app(IngresoController::class)->recalcular_datos(7);
        
		$hoy= date('Y-m-d');
		$anio_actual= date('Y');
		$mes_actual= date('m');
        $sumatoria_ventas=Venta::where('fecha','=',$hoy)->where('estado','=',1)->where('id_caja','=',1)->sum('monto_total');
        $sumatoria_pagos_interes=planpago::whereDate('created_at','=',$hoy)->where('estado','=',1)->sum('interes');
        $sumatoria_ventas_descuento=Venta::where('fecha','=',$hoy)->where('estado','=',1)->where('id_caja','=',1)->sum('descuento');

        $sumatoria_ventas+=$sumatoria_pagos_interes;
        $sumatoria_ventas-=$sumatoria_ventas_descuento;
        $cantidad_producto=Producto::where('estado','=',1)->count();
      
       
        $sql="SELECT MONTHNAME(ventas.fecha) AS mes, SUM(ventas.monto_total - ventas.descuento + ventas.interes) AS total_venta
        FROM ventas
        WHERE ventas.estado=1 AND
        YEAR(ventas.fecha)=$anio_actual
        GROUP BY mes";

        $sql_interes="SELECT MONTHNAME(p.created_at) AS mes, SUM(p.interes) AS total_interes
        FROM planpagos p
        WHERE p.estado=1 AND
        YEAR(p.created_at)=$anio_actual
        GROUP BY mes";


        $sql2="SELECT count(pa.id) as cantidad_minimos
        FROM productos p,almacens a,producto_almacens pa
        WHERE pa.id_producto = p.id AND
        pa.id_almacen = a.id AND
        p.inventariable != 0 AND
        pa.stock <= p.stock_minimo";

        $sql3="SELECT p.nombre as nombre_producto, a.sigla as nombre_almacen, SUM(dv.cantidad) as cantidad_producto, dv.precio_unidad as precio_venta_detalle ,dv.precio_unidad_compra as precio_compra_detalle
        FROM ventas v,producto_almacens pa,detalle_ventas dv,productos p,almacens a
        WHERE dv.id_venta = v.id AND
        dv.id_producto=pa.id_producto AND
        dv.id_almacen=pa.id_almacen AND
        v.estado=1 AND
        v.id_caja=1 AND
        pa.id_producto=p.id AND
        pa.id_almacen=a.id
        GROUP BY precio_compra_detalle,precio_venta_detalle,nombre_almacen,nombre_producto,nombre_almacen";
        $datos_caja1=(array) DB::select($sql3); 
        $sum_compra=0;
        $sum_venta=0;
        $ganancia_capital=0;
        foreach ($datos_caja1 as $row){
            $sum_compra+=($row->cantidad_producto*$row->precio_compra_detalle);
            $sum_venta+=($row->cantidad_producto*$row->precio_venta_detalle);
        }
        $ganancia_capital=$sum_venta-$sum_compra;
        
		$total = $cantidad_producto;
        $totalVentas = $sumatoria_ventas;
       
		$totalxmeses= (array) DB::select($sql); 
        $total_interesxmeses= (array) DB::select($sql_interes); 
        
		$minimos = collect(DB::select($sql2))->first()->cantidad_minimos;
       
        $caja_inventario=Caja::findOrFail(1);
        $caja_taller=Caja::findOrFail(2);
        $caja_grua=Caja::findOrFail(3);
        $caja_maquinaria=Caja::findOrFail(4);
        $caja_labadero=Caja::findOrFail(5);
        $caja_ganaderia=Caja::findOrFail(6);
        $caja_general=Caja::findOrFail(7);
        return view('home',compact('caja_general','caja_ganaderia','caja_labadero','caja_maquinaria','caja_grua','caja_taller','caja_inventario','total','totalVentas','totalxmeses','total_interesxmeses','minimos'));
    }

    public function ver_informe($fecha){
        return view('ventas/ver_ventas_dia',compact('fecha'));
    }

    public function generar_reporte(){
        return view('ventas/ver_ventas_por_rango');
    }

    public function reporte_rango($fecha_inicial,$fecha_final){
        $sumatoria_pagos_interes=planpago::whereDate('created_at','>=',$fecha_inicial)->whereDate('created_at','<=',$fecha_final)->where('estado','=',1)->sum('interes');
        $sumatoria_ventas_descuento=Venta::where('fecha','>=',$fecha_inicial)->where('fecha','<=',$fecha_final)->where('estado','=',1)->where('id_caja','=',1)->sum('descuento');

        $sql="SELECT SUM(detalle_ventas.cantidad) as cant_productos, detalle_ventas.precio_unidad as precio_unidad , productos.nombre as nombre_producto, almacens.sigla as nombre_almacen
        FROM ventas,detalle_ventas,producto_almacens,productos , almacens
        WHERE detalle_ventas.id_venta=ventas.id AND
        detalle_ventas.id_producto=producto_almacens.id_producto AND
        detalle_ventas.id_almacen=producto_almacens.id_almacen AND
        producto_almacens.id_producto=productos.id AND
        producto_almacens.id_almacen=almacens.id AND
        ventas.estado=1 AND
        ventas.fecha >='$fecha_inicial' AND
        ventas.fecha <='$fecha_final' AND
        ventas.id_caja =1
        GROUP BY  precio_unidad , nombre_producto, nombre_almacen
        ORDER BY cant_productos DESC";
       // dd($sql);

        $datos_ventas= (array) DB::select($sql); 
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
            $total=0;
            foreach ($datos_ventas as $row){
                if ($sw==1){
                    $pdf->AddPage();
                    $pdf->SetMargins(5,5,5);
                    $pdf->SetTitle("INFORME");
                    $pdf->SetFont('Arial','B',16);
                    $pdf->image(asset('vendor/adminlte/dist/img/AdminLTELogo.png'),10,5,10,10,'PNG');
                    $pdf->Cell(190,4,'',0,1,'C');
                    $pdf->Cell(190,4,'INFORME DE VENTAS ('.$fecha_inicial.' - '.$fecha_final.')',0,1,'C');
                    $pdf->Ln(6);
                    $pdf->SetFont('Arial','B',10);
                    $pdf->SetFont('Arial','',9);
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(17,5,utf8_decode('Dirección: '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,$configuracion->direccion,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(17,5,utf8_decode('Fecha : '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,$fecha_inicial.'  -  '.$fecha_final,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Ln(10);
                    $pdf->SetFillColor(2,100,200);//Fondo verde de celda
                    $pdf->SetTextColor(240, 255, 240); //Letra color blanco
                    $pdf->Cell(10,5,utf8_decode('Nº'),1,0,'L',true);
                    $pdf->Cell(65,5,'PRODUCTO',1,0,'L',true);
                    $pdf->Cell(18,5,'ALMACEN',1,0,'L',true);
                    $pdf->Cell(37,5,'PRECIO VENTA',1,0,'L',true);
                    $pdf->Cell(20,5,'CANTIDAD',1,0,'L',true);
                    $pdf->Cell(40,5,'SUB TOTAL',1,1,'L',true);
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
                $pdf->Cell(18,5,$row->nombre_almacen,'LR',0,'L',true);
                $pdf->Cell(37,5,number_format($row->precio_unidad,2,'.','').' bs','LR',0,'L',true);
                $pdf->Cell(20,5,$row->cant_productos,'LR',0,'L',true);
                $pdf->Cell(40,5,number_format($row->precio_unidad*$row->cant_productos,2,'.','').' bs','LR',1,'L',true); // L= IZQUIERDA R= DERECHA
                $total+=$row->precio_unidad*$row->cant_productos;
                if ($contador%19==0){$sw=1;}
                $contador++;
            }
           
            $pdf->ln();
            $pdf->SetFont('Arial','',11);
            $pdf->ln();
            $pdf->Cell(50,5,'Monto Total: ',0,0,'L');
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(20,5,number_format($total,2,'.','').' bs',0,1,'L');
            $pdf->Cell(50,5,'Interes Total: ',0,0,'L');
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(20,5,number_format($sumatoria_pagos_interes,2,'.','').' bs',0,1,'L');
            $pdf->Cell(50,5,'Descuento Total: ',0,0,'L');
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(20,5,number_format($sumatoria_ventas_descuento,2,'.','').' bs',0,1,'L');
            $pdf->Cell(50,5,'Total generado: ',0,0,'L');
            $pdf->SetFont('Arial','B',11);
            $pdf->SetTextColor(16, 158, 240 );
            $pdf->Cell(20,5,number_format($total+$sumatoria_pagos_interes-$sumatoria_ventas_descuento,2,'.','').' bs',0,1,'L');
            $pdf->SetTextColor(3, 3, 3);

        $sql3="SELECT YEAR(va.fecha) as anio_tiempo, MONTH(va.fecha) as mes_tiempo, 
        (SELECT SUM(v.monto_total + v.interes) 
         FROM ventas v 
         WHERE 
         v.fecha >='$fecha_inicial' AND 
         v.fecha <='$fecha_final' AND 
         MONTH(v.fecha)=MONTH(va.fecha) AND 
         YEAR(v.fecha)=YEAR(va.fecha) AND 
         v.estado=va.estado ) as monto_mes
                FROM ventas va
                WHERE 
                va.estado=1 AND
                va.fecha >='$fecha_inicial' AND
                va.fecha <='$fecha_final'  
                GROUP BY anio_tiempo,mes_tiempo,monto_mes
                ORDER BY anio_tiempo DESC";

        $datos_mes= (array) DB::select($sql3); 
       // $pdf = new Fpdf('P','mm',array(200,200));
        //meses 

            $sw=1;
            $contador = 1;
            $color=0;
            $total=0;
            $total_interes=0;
            $total_descuento=0;
            foreach ($datos_mes as $row){
                if ($sw==1){
                    $pdf->AddPage();
                    $pdf->SetMargins(5,5,5);
                    $pdf->SetTitle("INFORME");
                    $pdf->SetFont('Arial','B',16);
                    $pdf->image(asset('vendor/adminlte/dist/img/AdminLTELogo.png'),10,5,10,10,'PNG');
                    $pdf->Cell(190,4,'',0,1,'C');
                    $pdf->Cell(190,4,'INFORME DE VENTAS ('.$fecha_inicial.' - '.$fecha_final.')',0,1,'C');
                    $pdf->Ln(6);
                    $pdf->SetFont('Arial','B',10);
                    $pdf->SetFont('Arial','',9);
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(17,5,utf8_decode('Dirección: '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,$configuracion->direccion,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(17,5,utf8_decode('Fecha : '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,$fecha_inicial.'  -  '.$fecha_final,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Ln(5);
                    $pdf->SetFillColor(2,100,200);//Fondo verde de celda
                    $pdf->SetTextColor(240, 255, 240); //Letra color blanco
                    $pdf->Cell(10,5,utf8_decode('Nº'),1,0,'L',true);
                    $pdf->Cell(25,5,utf8_decode('AÑO'),1,0,'L',true);
                    $pdf->Cell(25,5,'MES',1,0,'L',true);
                    $pdf->Cell(40,5,'MONTO GENERADO',1,0,'L',true);
                    $pdf->Cell(45,5,'DESCUENTO GENERADO',1,0,'L',true);
                    $pdf->Cell(40,5,'INTERES GENERADO',1,1,'L',true);
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
                $mes="";
               
                    switch ($row->mes_tiempo) {
                        case 1: $mes="enero"; break;
                        case 2: $mes="febrero"; break;
                        case 3: $mes="marzo"; break;
                        case 4: $mes="abril"; break;
                        case 5: $mes="mayo"; break;
                        case 6: $mes="junio"; break;
                        case 7: $mes="julio"; break;
                        case 8: $mes="agosto"; break;
                        case 9: $mes="septiembre"; break;
                        case 10: $mes="octubre"; break;
                        case 11: $mes="noviembre"; break;
                        case 12: $mes="dicienbre"; break;
                    }
                
                $pdf->Cell(10,5,$contador,'LR',0,'L',true);


                $pdf->Cell(25,5,$row->anio_tiempo,'LR',0,'L',true);
                $pdf->Cell(25,5,$mes,'LR',0,'L',true);
              
                $pdf->Cell(40,5,$row->monto_mes.' bs','LR',0,'L',true);
               
               $sql_aux="SELECT SUM(ve.descuento) AS descuento
                FROM ventas ve 
                WHERE 
                ve.fecha >='$fecha_inicial' AND 
                ve.fecha <='$fecha_final' AND 
                MONTH(ve.fecha)=$row->mes_tiempo AND 
                YEAR(ve.fecha)=$row->anio_tiempo AND 
                ve.estado=1";
                $descuento_aux=collect(DB::select($sql_aux))->first()->descuento;;

                $interes_aux=planpago::whereDate('created_at','>=',$fecha_inicial)->whereDate('created_at','<=',$fecha_final)->whereMonth('created_at','=',$row->mes_tiempo)->where('estado','=',1)->sum('interes');
                $pdf->Cell(45,5,$descuento_aux.' bs','LR',0,'L',true);
                $pdf->Cell(40,5,$interes_aux.' bs','LR',1,'L',true);
                $total+=$row->monto_mes;
                $total_interes+=$interes_aux;
                $total_descuento+=$descuento_aux;
                if ($contador%19==0){$sw=1;}
                $contador++;
            }
           
            $pdf->ln();
            $pdf->SetFont('Arial','',11);
            
            $pdf->ln();
            
            $pdf->Cell(40,5,utf8_decode('Monto Total : '),0,0,'L');
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(20,5,number_format($total,2,'.','').' bs',0,1,'L');
            $pdf->Cell(40,5,utf8_decode('Descuento : '),0,0,'L');
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(20,5,number_format($total_descuento,2,'.','').' bs',0,1,'L');
            $pdf->Cell(40,5,utf8_decode('Monto Interes : '),0,0,'L');
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(20,5,number_format($total_interes,2,'.','').' bs',0,1,'L');
            $pdf->Cell(40,5,'Total generado: ',0,0,'L');
            $pdf->SetFont('Arial','B',11);
            $pdf->SetTextColor(16, 158, 240 );
            $pdf->Cell(20,5,number_format($total+$total_interes-$total_descuento,2,'.','').' bs',0,1,'L');
            $pdf->SetTextColor(3, 3, 3);
            $pdf->Output('I','informe_ventas'.$fecha_inicial.' - '.$fecha_final.'pdf');
    }


    public function informe_dia_ventas($fecha){
       
        $sumatoria_pagos_interes=planpago::whereDate('created_at','=',$fecha)->where('estado','=',1)->sum('interes');
        $sumatoria_ventas_descuento=Venta::where('fecha','=',$fecha)->where('estado','=',1)->where('id_caja','=',1)->sum('descuento');

        $sql="SELECT SUM(detalle_ventas.cantidad) as cant_productos, detalle_ventas.precio_unidad as precio_unidad , productos.nombre as nombre_producto, almacens.sigla as nombre_almacen
        FROM ventas,detalle_ventas,producto_almacens,productos , almacens
        WHERE detalle_ventas.id_venta=ventas.id AND
        detalle_ventas.id_producto=producto_almacens.id_producto AND
        detalle_ventas.id_almacen=producto_almacens.id_almacen AND
        producto_almacens.id_producto=productos.id AND
        producto_almacens.id_almacen=almacens.id AND
        ventas.estado=1 AND
        ventas.fecha='$fecha'
        GROUP BY  precio_unidad , nombre_producto, nombre_almacen
        ORDER BY cant_productos DESC";
       // dd($sql);

        $datos_ventas= (array) DB::select($sql); 
       // dd($datos_ventas);
       // $datos_ventas= collect(DB::select($sql));
        $configuracion=Configuracion::all()->first();

        if(count($datos_ventas)<=0){
            return "no se realizo ninguna venta hor dia";
        }
        $pdf = new Fpdf('P','mm',array(200,200));
        
            $sw=1;
            $contador = 1;
            $color=0;
            $total=0;
            foreach ($datos_ventas as $row){
                if ($sw==1){
                    $pdf->AddPage();
                    $pdf->SetMargins(5,5,5);
                    $pdf->SetTitle("INFORME");
                    $pdf->SetFont('Arial','B',16);
                    $pdf->image(asset('vendor/adminlte/dist/img/AdminLTELogo.png'),10,5,10,10,'PNG');
                    $pdf->Cell(190,4,'',0,1,'C');
                    $pdf->Cell(190,4,'INFORME DE VENTAS DEL DIA',0,1,'C');
                    $pdf->Ln();
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(17,5,utf8_decode('Dirección: '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,$configuracion->direccion,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(17,5,utf8_decode('Fecha : '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,$fecha,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Ln();
                    $pdf->SetFillColor(2,100,200);//Fondo verde de celda
                    $pdf->SetTextColor(240, 255, 240); //Letra color blanco
                    $pdf->Cell(10,5,utf8_decode('Nº'),1,0,'L',true);
                    $pdf->Cell(70,5,'PRODUCTO',1,0,'L',true);
                    $pdf->Cell(18,5,'ALMACEN',1,0,'L',true);
                    $pdf->Cell(32,5,'PRECIO VENTA',1,0,'L',true);
                    $pdf->Cell(20,5,'CANTIDAD',1,0,'L',true);
                    $pdf->Cell(40,5,'SUB TOTAL',1,1,'L',true);
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
                $pdf->Cell(70,5,$row->nombre_producto,'LR',0,'L',true);
                $pdf->Cell(18,5,$row->nombre_almacen,'LR',0,'L',true);
                $pdf->Cell(32,5,number_format($row->precio_unidad,2,'.','').' bs','LR',0,'L',true);
                $pdf->Cell(20,5,$row->cant_productos,'LR',0,'L',true);
                $pdf->Cell(40,5,number_format($row->precio_unidad*$row->cant_productos,2,'.','').' bs','LR',1,'L',true); // L= IZQUIERDA R= DERECHA
                $total+=$row->precio_unidad*$row->cant_productos;
                if ($contador%19==0){$sw=1;}
                $contador++;
            }
           
            $pdf->ln();
            $pdf->SetFont('Arial','',11);
            
            $pdf->ln();
            
            $pdf->Cell(55,5,utf8_decode('Monto Total del dia: '),0,0,'L');
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(20,5,number_format($total,2,'.','').' bs',0,1,'L');
            $pdf->Cell(55,5,utf8_decode('Interes Total del dia: '),0,0,'L');
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(20,5,number_format($sumatoria_pagos_interes,2,'.','').' bs',0,1,'L');
            $pdf->Cell(55,5,utf8_decode('Descuento Total del dia: '),0,0,'L');
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(20,5,number_format($sumatoria_ventas_descuento,2,'.','').' bs',0,1,'L');
            $pdf->Cell(55,5,utf8_decode('Total del dia: '),0,0,'L');
            $pdf->SetFont('Arial','B',11);
            $pdf->SetTextColor(16, 158, 240 );
            $pdf->Cell(20,5,number_format($total+$sumatoria_pagos_interes-$sumatoria_ventas_descuento,2,'.','').' bs',0,1,'L');
            $pdf->Output('I','informe_ventas_dia'.$fecha.'pdf');
    }
    
    public function index()
    {
        
        return view('home');
    }

    public function configuracion(){
        $datos=Configuracion::all()->where('id','=',1)->first();
        return view('admin/configuracion/index',compact('datos'));
    }

    public function show(){
        $datos=Configuracion::all()->where('id','=',1)->first();
       // return $datos;
       return 'hola';
    }

    public function logo(Request $request){
        $data['error']=0;
        $validator = Validator::make($request->all(), [
            'img_logo' => 'image|mimes:png'
        ]);

        if($validator->fails())
        {
            $data=['error'=>'1','mensaje'=>$validator->errors()->all()];  // all() 
            return $data;
        }
        
        //script para subir editar una imagen
        if ($request->hasFile("img_logo")) {
            $image_path = public_path("vendor/adminlte/dist/img/AdminLTELogo.png");
            if (File::exists($image_path)) {
                File::delete($image_path);  //eliminar imagen existente
            }
            
            $imagen = $request->file("img_logo");
            $nombreimagen =  "AdminLTELogo.png";
            $ruta = public_path("vendor/adminlte/dist/img/");
            $imagen->move($ruta,$nombreimagen);
        }
        return $data;
      //  $datos=Configuracion::all()->where('id','=',1)->first();
       // return $datos;
      // return 'hola';
    }

    public function fondo(Request $request){
        $data['error']=0;
        $validator = Validator::make($request->all(), [
            'img_fondo' => 'image|mimes:jpg'
        ]);

        if($validator->fails())
        {
            $data=['error'=>'1','mensaje'=>$validator->errors()->all()];  // all() 
            return $data;
        }
        
        //script para subir editar una imagen
        if ($request->hasFile("img_fondo")) {
            $image_path = public_path("img/fondo_principal.jpg");
            if (File::exists($image_path)) {
                File::delete($image_path);  //eliminar imagen existente
            }
            
            $imagen = $request->file("img_fondo");
            $nombreimagen =  "fondo_principal.jpg";
            $ruta = public_path("img/");
            $imagen->move($ruta,$nombreimagen);
        }
        return $data;
    }

    public function datos(Request $request){

        $configuracion=Configuracion::all()->where('id','=',1)->first();
        $data['error']=0;
        $validator = Validator::make($request->all(), [
            'nombre_empresa' => 'required',
            'nombre_empresa2' => 'required|min:1',
            'direccion' => 'required',
            'telefono' => 'required|integer|min:5',
            'correo' => 'required',
            'frase' => 'required',
        ]);

        if($validator->fails())
        {
            $data=['error'=>'1','mensaje'=>$validator->errors()->all()];  // all() 
            return $data;
        }

        $configuracion->nombre=$request->nombre_empresa;
        $configuracion->nombre2=$request->nombre_empresa2;
        $configuracion->telefono=$request->telefono;
        $configuracion->direccion=$request->direccion;
        $configuracion->correo=$request->correo;
        $configuracion->nic=$request->nic;
        $configuracion->leyenda=$request->frase;
        $configuracion->update();
        
       return $data;
    }

    public function config(Request $request){

        $configuracion=Configuracion::all()->where('id','=',1)->first();
        $data['error']=0;
        $validator = Validator::make($request->all(), [
            'tabla' => 'required',
        ]);

        if($validator->fails())
        {
            $data=['error'=>'1','mensaje'=>$validator->errors()->all()];  // all() 
            return $data;
        }

        $configuracion->tabla=$request->tabla;
        $configuracion->update();
        session(['responsivo'=>$request->tabla]);
        
       return $data;
    }

    public function perfil_datos(){
        $datos=Empleado::where('id_usuario','=',Auth::user()->id)->first();
        if(isset($datos)){
            $nombre_completo=$datos->nombre.' '.$datos->apellidos;
            $sexo=$datos->sexo;
            $edad=$datos->edad;
            $telefono=$datos->telefono;
            $carnet=$datos->ci;
            $direccion=$datos->direccion;
        }else{
            $nombre_completo="no tiene registro de empleado";
            $sexo="";
            $edad="";
            $telefono="";
            $carnet="";
            $direccion=""; 
        }
        return view('perfil',compact('nombre_completo','sexo','edad','telefono','carnet','direccion'));
    }

    public function password(){
        return view('password');
    }

    public function update_password(Request $request){   
        $user=User::where('id','=',Auth::user()->id)->first() ;
        if (Hash::check($request->contraseña,$user->password)){
            if($request->nueva_contraseña == $request->confirmar_nueva_contraseña){
                if (Hash::check($request->nueva_contraseña,$user->password)){
                    return 'no puede ingresar lo mismo';   
                }else{
                    $user->password=Hash::make($request->nueva_contraseña);
                    $user->update();
                    return  redirect()->to(asset('/'));
                }
            }else{
                return 'son diferentes contraseñas ingresadas';
            }
         }else{
            return 'disculpe su contraseña actual no esta correcta intente de nuevo';
         }  
    }


    public function productos_en_stock_minimo(){
       
        $sql2="UPDATE productos
        SET stock=IFNULL(
        (
            SELECT SUM(pa.stock)
            FROM producto_almacens pa
            WHERE pa.id_producto = productos.id
        ),0)";
         DB::select($sql2);

        $sql="SELECT p.nombre,p.stock,p.stock_minimo,p.precio_venta,p.precio_compra
        FROM productos p,almacens a,producto_almacens pa
        WHERE pa.id_producto = p.id AND
        pa.id_almacen = a.id AND
        p.inventariable != 0 AND
        pa.stock <= p.stock_minimo";
       // dd($sql);

        $datos= (array) DB::select($sql); 
       // dd($datos_ventas);
       // $datos_ventas= collect(DB::select($sql));
        $configuracion=Configuracion::all()->first();

        if(count($datos)<=0){
            return "no hay productos en stock minimo";
        }
        $pdf = new Fpdf('P','mm',array(200,200));
        
            $sw=1;
            $contador = 1;
            $color=0;
            foreach ($datos as $row){
                if ($sw==1){
                    $pdf->AddPage();
                    $pdf->SetMargins(5,5,5);
                    $pdf->SetTitle("INFORME");
                    $pdf->SetFont('Arial','B',16);
                    $pdf->image(asset('vendor/adminlte/dist/img/AdminLTELogo.png'),10,5,10,10,'PNG');
                    $pdf->Cell(190,4,'',0,1,'C');
                    $pdf->Cell(190,4,'PRODUCTOS EN STOCK MINIMO',0,1,'C');
                    $pdf->Ln();
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(17,5,utf8_decode('Dirección: '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,$configuracion->direccion,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(17,5,utf8_decode('Fecha : '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,date('Y:m:d'),0,1,'L');
                    $pdf->SetFont('Arial','B',8);
                    $pdf->Ln();
                    $pdf->SetFillColor(2,100,200);//Fondo verde de celda
                    $pdf->SetTextColor(240, 255, 240); //Letra color blanco
                    $pdf->Cell(10,5,utf8_decode('Nº'),1,0,'L',true);
                    $pdf->Cell(70,5,'PRODUCTO',1,0,'L',true);
                    $pdf->Cell(30,5,'PRECIO COMPRA',1,0,'L',true);
                    $pdf->Cell(30,5,'PRECIO VENTA',1,0,'L',true);
                    $pdf->Cell(20,5,'STOCK',1,0,'L',true);
                    $pdf->Cell(30,5,'STOCK MINIMO',1,1,'L',true);
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
                $pdf->Cell(70,5,$row->nombre,'LR',0,'L',true);
                $pdf->Cell(30,5,number_format($row->precio_compra,2,'.','').' bs','LR',0,'L',true);
                $pdf->Cell(30,5,number_format($row->precio_venta,2,'.','').' bs','LR',0,'L',true);
                $pdf->Cell(20,5,$row->stock,'LR',0,'L',true);
                $pdf->Cell(30,5,$row->stock_minimo,'LR',1,'L',true); // L= IZQUIERDA R= DERECHA
               
                if ($contador%19==0){$sw=1;}
                $contador++;
            }
           
            $pdf->ln();
            $pdf->SetFont('Arial','',11);
            
            $pdf->Output('I','informe_productos'.date('Y:m:d').'pdf');
    }
}
