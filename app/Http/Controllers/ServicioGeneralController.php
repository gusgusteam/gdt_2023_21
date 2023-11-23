<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Configuracion;
use App\Models\Empleado;
use App\Models\ServicioGeneral;
use Illuminate\Contracts\Validation\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Codedge\Fpdf\Fpdf\Fpdf;
use PhpParser\Node\Stmt\Return_;

class ServicioGeneralController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function detalles($id_caja)
    {
        $caja=Caja::findOrFail($id_caja);
        $nombre=$caja->nombre;
        return view('caja_general/detalles',compact('id_caja','nombre'));
    }

    public function datos()
    {
       // $clientes=Cliente::all()->where('estado','=',1);
        $sql="SELECT * FROM clientes WHERE clientes.estado = 1 ";
        $clientes=DB::select($sql);

        $sql="SELECT * FROM empleados WHERE empleados.id_usuario is null AND empleados.estado=1";

        $empleados=DB::select($sql);

        $data['clientes']=$clientes;
        $data['empleados']=$empleados;
        return json_encode($data);
    }

    public function servicios($id_caja){
        $caja=Caja::findOrFail($id_caja);
        $fecha_inicial=$caja->fecha_inicio." ".$caja->hora_inicio;
       // $fecha_final=$caja->fecha_final." ".$caja->hora_final;
        $servicio=ServicioGeneral::select(
            'servicio_generals.*'
        )
        ->where('servicio_generals.created_at','>=',$fecha_inicial)
        //->orWhere('servicio_generals.fecha_deuda','>=',$fecha_inicial)
        ->where('servicio_generals.id_caja','=',$id_caja)
        ->orderBy('servicio_generals.created_at','desc');

        return DataTables::of($servicio)
            // anadir nueva columna botones
           ->addColumn('actions', function($servicio){
            $url_detalle=route('general.pdf_tiket',$servicio->id);
             $btn_eliminar='<a class="btn btn-warning" rel="tooltip" data-placement="top" title="Eliminar" onclick="Eliminar('.$servicio->id.')"><i class="far fa-trash-alt"></i></a>';
             $btn_pdf='<a class="btn btn-danger" rel="tooltip" data-placement="top" title="PDF" href="'.$url_detalle.'"><i class="fas fa-file-pdf"></i></a>';
                $btn= '<div class="text-right">  <div class="btn-group btn-group-sm ">';
                if($servicio->estado==1 || $servicio->estado==-1 ){
                    if(auth()->user()->can('servicio.eliminar')){
                        $btn= $btn.$btn_eliminar.$btn_pdf;
                    }else{
                        $btn= $btn.$btn_pdf;
                    }
                   
                }else{
                   $btn= $btn.$btn_pdf;
                }
                $btn=$btn.'</div> </div> ';
             return  $btn;
           })
           ->addColumn('estado', function($servicio){
            if($servicio->estado==0){
                $span= '<span class="badge bg-danger">Cancelado</span>';
            }
            if($servicio->estado==1){
                $span= '<span class="badge bg-success">Completdo</span>';
            }
            if($servicio->estado==-1){
                $span= '<span class="badge bg-warning">Incompleto</span>';
            }
            return  $span;
            })
            ->addColumn('tipo', function($servicio){
                if($servicio->tipo_pago==0){
                    $span= '<span class="badge bg-dark">Credito</span>';
                }
                if($servicio->tipo_pago==1){
                    $span= '<span class="badge bg-white">Contado</span>';
                }
                return  $span;
            })
            ->addColumn('fecha_hora', function($servicio){
            
                return  $servicio->created_at;
            })
            ->addColumn('monto', function($servicio){
                return  $servicio->monto_total . " bs";
            })
            ->addColumn('interes', function($servicio){
                return  $servicio->interes . " bs";
            })
           
            ->addColumn('cliente', function($servicio){
                if($servicio->id_cliente==null){
                    return "publico general";
                }else{
                    $cliente=Cliente::findOrFail($servicio->id_cliente);
                    return $cliente->nombre.' '.$cliente->apellidos;
                }
              
            })
            ->addColumn('empleado', function($servicio){
                if($servicio->id_empleado==null){
                    return "sin asignar";
                }else{
                    $empleado=Empleado::findOrFail($servicio->id_empleado);
                    return $empleado->nombre.' '.$empleado->apellidos;
                }
              
            })
            
           ->rawColumns(['actions','cliente','tipo','monto','descuento_monto','interes','empleado','fecha_hora','estado']) // incorporar columnas
           ->make(true); // convertir a codigo
    


    }

    public function guardar(Request $request)
    {
       $data['error']=0;
       $data['mensaje']='';
       $data['servicio']=-1;

       $verificar_caja=Caja::findOrFail($request->tipo_general);
        if($verificar_caja->estado==0){
            $data['error']=1;
            $data['mensaje']='la '.$verificar_caja->nombre .' a sido cerrado preguntar al encargado';
            return json_encode($data);
        }
       // 0 publico general 1 cliente
        // 1 contado 0 credito

       $servicio= new ServicioGeneral();
       $sw=0;
       $codigo_generado='';
        while($sw==0){
            $codigo_generado = uniqid();
            $existe=ServicioGeneral::all()->where('codigo','=',$codigo_generado)->first();
            if(!$existe){ $sw=1;}
        }
        $servicio->codigo=$codigo_generado;
        $servicio->descripcion=$request->descripcion;
        $servicio->monto_total=$request->precio;
        $servicio->tipo_pago=$request->tipo_pago;
        if($request->tipo_pago==0){
            $servicio->estado=-1;
        }
        
        if($request->tipo_general!=5){
         if($request->modo_cliente==1){
            $id_cliente_aux=collect($request->id_cliente)[0];
         }else{
            $id_cliente_aux=null; 
         }
         $servicio->id_cliente=$id_cliente_aux;
         $servicio->id_empleado=$request->id_empleado;
        }else{
            $servicio->tipo_lab=$request->tipo_lab;
        }
        $servicio->id_caja=$verificar_caja->id;
        $servicio->save();

       if($servicio){
        $data['servicio']=$servicio->id;
       }

       return json_encode($data); 
    }

    public function guardar22(Request $request)
    {
       $data['error']=0;
       $data['mensaje']='';

       $verificar_caja=Caja::findOrFail($request->tipo_general+1);
        if($verificar_caja->estado==0){
            $data['error']=1;
            $data['mensaje']='la '.$verificar_caja->nombre .' a sido cerrado preguntar al encargado';
            return json_encode($data);
        }

       if($request->modo_cliente==0 && $request->tipo_pago==1){
            $id_cliente_aux=null;
       }else{
        if($request->tipo_pago==0 && $request->modo_cliente==0){
            $data['error']=1;
            $data['mensaje']='no puede realizar esto con un cliente PUBLICO GENERAL el tipo de pago tiene que ser al CONTADO';
            return json_encode($data);
        }
         $id_cliente_aux=collect($request->id_cliente)[0];
       }
      
       $servicio= new ServicioGeneral();
       $sw=0;
       $codigo_generado='';
        while($sw==0){
            $codigo_generado = uniqid();
            $existe=ServicioGeneral::all()->where('codigo','=',$codigo_generado)->first();
            if(!$existe){ $sw=1;}
        }
        $servicio->codigo=$codigo_generado;
        $servicio->descripcion=$request->descripcion;
        $servicio->monto_total=$request->precio;
        $servicio->tipo_pago=$request->tipo_pago;
        if($request->tipo_pago==0){
            $servicio->estado=-1;
        }
        switch ($request->tipo_general) {
            case 1: $caja=Caja::findOrFail(2);; break;
            case 2: $caja=Caja::findOrFail(3);; break;
            case 3: $caja=Caja::findOrFail(4);; break;
            case 4: $caja=Caja::findOrFail(5);; break;
            case 5: $caja=Caja::findOrFail(6);; break;
        }
        if($request->tipo_general!=4){
        // $servicio->id_cliente=collect($request->id_cliente)[0];
         $servicio->id_cliente=$id_cliente_aux;
         $servicio->id_empleado=$request->id_empleado;
        }else{
            $servicio->tipo_lab=$request->tipo_lab;
        }
        $servicio->id_caja=$caja->id;
        $servicio->save();
        if($request->tipo_pago==1){
         $caja->monto_ingreso+=$request->precio;
         $caja->update();
        }

       return json_encode($data); 
    }


    public function deuda($id_caja,$id_cliente){
        $caja=Caja::findOrFail($id_caja);
        $cliente=Cliente::findOrFail($id_cliente);
        $nombre_cliente=$cliente->nombre.' '.$cliente->apellidos;
        $nombre_caja=$caja->nombre;
        $id_cliente=$cliente->id;
       
        $sql="SELECT YEAR(sg.created_at) as anio_tiempo, MONTH(sg.created_at) as mes_tiempo, 
        (SELECT SUM(sgg.monto_total) 
         FROM servicio_generals sgg 
         WHERE MONTH(sgg.created_at)=MONTH(sg.created_at) AND 
         YEAR(sgg.created_at)=YEAR(sg.created_at) AND 
         sgg.id_cliente= $id_cliente AND 
         sgg.tipo_pago=0  AND 
         sgg.id_caja=$id_caja AND
         (sgg.estado=-1 OR sgg.estado=1) ) as monto_general ,
         
         (SELECT SUM(sggg.monto_total) 
          FROM servicio_generals sggg
          WHERE sggg.id_cliente=$id_cliente AND  
          MONTH(sggg.created_at)=MONTH(sg.created_at) AND 
          YEAR(sggg.created_at)=YEAR(sg.created_at) AND 
          sggg.id_caja=$id_caja AND
          sggg.estado=1 AND sggg.tipo_pago=0 ) as saldo_pagado
          
                FROM servicio_generals sg
                WHERE 
                (sg.estado=-1  OR sg.estado=1) AND
                sg.tipo_pago=0 AND
                sg.id_cliente=$id_cliente AND
                sg.id_caja=$id_caja
                GROUP BY anio_tiempo,mes_tiempo,monto_general,saldo_pagado";
        $datos=DB::select($sql);
        return view('caja_general/deuda_mes',compact('datos','nombre_cliente','id_caja','id_cliente','nombre_caja'));
    }

    public function servicios_deuda($id_caja,$id_cliente){
        //$caja=Caja::findOrFail($id_caja);
        $sql="SELECT sg.id_cliente as id_cliente, YEAR(sg.created_at) as anio_tiempo, MONTH(sg.created_at) as mes_tiempo, 
        (SELECT SUM(sgg.monto_total) 
         FROM servicio_generals sgg 
         WHERE MONTH(sgg.created_at)=MONTH(sg.created_at) AND 
         YEAR(sgg.created_at)=YEAR(sg.created_at) AND 
         sgg.id_cliente= $id_cliente AND 
         sgg.tipo_pago=0  AND 
         sgg.id_caja=$id_caja AND
         (sgg.estado=-1 OR sgg.estado=1) ) as monto_general ,
         
         (SELECT SUM(sggg.monto_total) 
          FROM servicio_generals sggg
          WHERE sggg.id_cliente=$id_cliente AND  
          MONTH(sggg.created_at)=MONTH(sg.created_at) AND 
          YEAR(sggg.created_at)=YEAR(sg.created_at) AND 
          sggg.id_caja=$id_caja AND
          sggg.estado=1 AND sggg.tipo_pago=0 ) as saldo_pagado
          
                FROM servicio_generals sg
                WHERE 
                (sg.estado=-1  OR sg.estado=1) AND
                sg.tipo_pago=0 AND
                sg.id_cliente=$id_cliente AND
                sg.id_caja=$id_caja
                GROUP BY anio_tiempo,mes_tiempo,monto_general,saldo_pagado,id_cliente";
        $datos=DB::select($sql);
        

        return DataTables::of($datos)
            // anadir nueva columna botones
           ->addColumn('actions', function($datos){
            
             $btn_detalle='<a class="btn btn-sm btn-success" onclick="Mostrar_notas_servicios('.$datos->id_cliente.','.$datos->anio_tiempo.','.$datos->mes_tiempo.')" title="Detalles"><i class="far fa-edit"></i></a>';
                $btn= '<div class="text-right">  <div class="btn-group btn-group-sm ">';
                if($datos->monto_general-$datos->saldo_pagado>0){
                   $btn= $btn.$btn_detalle;
                }else{
                   $btn= $btn;
                }
                $btn=$btn.'</div> </div> ';
             return  $btn;
           })
           ->addColumn('estado', function($datos){
            if($datos->monto_general-$datos->saldo_pagado==0){
                $span= '<span class="badge bg-success">saldo completdo</span>';
            }else{
                $span= '<span class="badge bg-danger">saldo incompleto</span>';
            }
            return  $span;
            })
            ->addColumn('monto_general2', function($datos){               
                return  $datos->monto_general.' bs';
            })
            ->addColumn('saldo_faltante', function($datos){               
                return  $datos->monto_general-$datos->saldo_pagado.' bs';
            })
            ->addColumn('saldo_pagado2', function($datos){
                if($datos->saldo_pagado==null){
                    return '0 bs';
                }else{
                return  $datos->saldo_pagado.' bs';
                }
            })
            
               
           ->rawColumns(['actions','estado','saldo_faltante']) // incorporar columnas
           ->make(true); // convertir a codigo
    }

    public function deuda_cliente_dias($id_cliente){
        $registro=Cliente::findOrFail($id_cliente);
        $nombre_cliente=$registro->nombre. ' '.$registro->apellidos;
        $sql2="SELECT sg.created_at,sg.fecha_deuda, sg.id ,sg.codigo, sg.monto_total,sg.id_empleado,sg.interes,sg.estado
        FROM servicio_generals sg
        WHERE 
        (sg.estado=-1 OR sg.estado=1) AND
        sg.tipo_pago=0 AND
        sg.id_cliente=$id_cliente
        ORDER BY estado ASC";

        $datos=DB::select($sql2);

        $fila = '';
        $numFila = 0;
        $aux_fecha="";
        $total=0;
        foreach ($datos as $row){
            $numFila++;
            
            $r=Empleado::findOrFail($row->id_empleado);
            $fila .= "<tr id='fila".$numFila."'>";
            $fila .= "<td>".$numFila."</td>";
            $fila .= "<td>".$row->codigo."</td>";
            $fila .= "<td>".$row->created_at."</td>";
            $fila .= "<td>".$row->fecha_deuda."</td>";
            $fila .= "<td>".$r->nombre.' '.$r->apellidos."</td>";
            $fila .= "<td>".$row->monto_total."</td>";
            $fila .= "<td>".$row->interes."</td>";
            if($row->estado==1){
              $fila .= "<td> <span class=\"badge bg-success\">Completado</span> </td>";  
            }else{
                $total+=$row->monto_total;
              $fila .= "<td> <span class=\"badge bg-warning\">Incompleto</span> </td>";  
            }
            $aux_fecha="'".$row->created_at."'";
            if(auth()->user()->can('servicio.credito')){
                $btn="<a class=\"btn btn-sm btn-dark\" onclick=\"completar_credito_servicio(".$row->id.','.$aux_fecha.','.$row->monto_total.")\" title=\"cancelar deuda\" ><i class=\"far fa-check-circle\"></i></a>";
            }else{
                $btn="";
            }
 
            $fila .= "<td><div class=\"text-right\"><div class=\"btn-group btn-group-sm \">";
            if($row->estado==-1){
                $fila .= $btn;
            } 
           
                
            $fila.="<a class=\"btn btn-sm btn-danger\" title=\"ver pdf servicio \"  onclick=\"mostrar_detalle_servicio(".$row->id.")\" ><i class=\"far fa-file-pdf\"></i></a>

            </div></div></td>";
            $fila .= "</tr>";
            
        }

        $data['fila']=$fila;
        $data['nombre_cliente']=$nombre_cliente;
        $data['total']=number_format($total,2,'.','');
        return json_encode($data);
       // return view('ventas/deuda_mes',compact('datos','nombre_cliente'));
    }

    public function cancelar_credito(Request $request){
        $data['error']=0;
        $data['mensaje']="";
        
        $servicio=ServicioGeneral::findOrFail($request->id_servicio);
        $verificar_caja=Caja::findOrFail($servicio->id_caja);

        if($verificar_caja->estado==0){
            $data['error']=1;
            $data['mensaje']=$verificar_caja->nombre.' a sido cerrado preguntar al encargado';
            return json_encode($data);
        }

      //  $venta=Venta::findOrFail($request->id_venta);
        if(isset($servicio)){
            if($servicio->estado==-1){
                $servicio->estado=1;
                $servicio->interes=$request->interes;
                $servicio->fecha_deuda=date("Y-m-d H:i:s");
                $servicio->update();
                $data['id_cliente']=$servicio->id_cliente;
            }else{
                $data['error']=1;
                $data['mensaje']="no se puede cancelar el credito 2 veces";
            }
            
        }
        return json_encode($data);

    }

    public function destroy(ServicioGeneral $servicioGeneral)
    {
        $data['error']=0;
        $data['mensaje']='';
        app(IngresoController::class)->recalcular_datos($servicioGeneral->id_caja);
        $verificar_caja=Caja::findOrFail($servicioGeneral->id_caja);
        if($verificar_caja->estado==0){
            $data['error']=1;
            $data['mensaje']='la '.$verificar_caja->nombre .' a sido cerrado preguntar al encargado';
            return json_encode($data);
        }
            $aux_estado=$servicioGeneral->estado;
            if($aux_estado==1 && $servicioGeneral->tipo_pago==0){
                $data['error']=1;
                $data['mensaje']='no puede eliminar un servicio ya pagado su credito';
                return json_encode($data);
            }
            $servicioGeneral->estado=0;
            if($aux_estado==1){
                if(($verificar_caja->monto_ingreso)>=($servicioGeneral->monto_total+$servicioGeneral->interes-$servicioGeneral->descuento)){
                    $servicioGeneral->update();  
                }else{
                    $data['error']=1;
                    $data['mensaje']='Cuenta con '.($verificar_caja->monto_ingreso).' bs no cubre la canselacion '.$servicioGeneral->monto_total.' y su interes de '.$servicioGeneral->interes.' bs ,descuento '.$servicioGeneral->descuento;
                }
            }else{
                $servicioGeneral->update();  
            }
        
        return json_encode($data);

    }



    public function pdf_servicio(ServicioGeneral $servicioGeneral){
        
        $empleado=Empleado::all()->where('id','=',$servicioGeneral->id_empleado)->first();
        $cliente=Cliente::all()->where('id','=',$servicioGeneral->id_cliente)->first();
        
        switch ($servicioGeneral->id_caja) {
            
            case 2:  $nombre_caja="TALLER"; break;
            case 3:  $nombre_caja="GRUA"; break;
            case 4:  $nombre_caja="MAQUINARIA"; break;
            case 5:  $nombre_caja="LABADERO,AGUA,BALANZA"; break;
            case 6:  $nombre_caja="GANADERIA"; break;
    
           
        }
    
        if(isset($empleado)){
            $nombre_empleado=$empleado->nombre;
            $apellidos_empleado=$empleado->apellidos;
        }else{
            $nombre_empleado='--';
            $apellidos_empleado='--';
        }
        if(isset($cliente)){
            $nombre_cliente=$cliente->nombre.' '.$cliente->apellidos;
            $carnet=$cliente->ci;
            $telefono=$cliente->telefono;
        }else{
            $nombre_cliente='publico general';
            $carnet='';
            $telefono='';
        }
        $configuracion=Configuracion::all()->first();
        
        if($servicioGeneral->estado==1){
            $estado="Completado";
        }else{
            if($servicioGeneral->estado==-1){
                $estado="Incompleto";  
            }else{
                $estado="Cancelado"; 
            }
            
        }
        $fecha=$servicioGeneral->created_at;
        $total=$servicioGeneral->monto_total+$servicioGeneral->interes;
        $pdf = new Fpdf('P','mm',array(200,200));
        
            $sw=1;
            $contador = 1;
            $color=0;
           
                if ($sw==1){
                    $pdf->AddPage();
                    $pdf->SetMargins(5,5,5);
                    $pdf->SetTitle("Detalle");
                    $pdf->SetFont('Arial','B',16);
                    $pdf->image(asset('vendor/adminlte/dist/img/AdminLTELogo.png'),10,5,10,10,'PNG');
                    $pdf->Cell(190,4,'',0,1,'C');
                    $pdf->Cell(190,4,'NOTA DE SERVICIO '.$nombre_caja,0,1,'C');
                    $pdf->Ln();
                  
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(17,5,utf8_decode('Dirección: '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,$configuracion->direccion,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(22,5,utf8_decode('Fecha y hora: '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,$fecha,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(22,5,utf8_decode('Empleado: '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,''.$nombre_empleado.''.' '.''.$apellidos_empleado.'',0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(15,5,'Codigo: ',0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(30,5,$servicioGeneral->codigo,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(15,5,'Cliente: ',0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(30,5,utf8_decode($nombre_cliente.'  ci : '.$carnet.'  cel : '.$telefono),0,1,'L');
                    $pdf->SetFont('Arial','B',11);
                    $pdf->Ln();
                    $pdf->SetFont('Arial','',11);
                    $sw=0;
                }

                if($color==1){
                
                $color=0;
                }else{
                $pdf->SetFillColor(255, 255, 255 ); //blanco tenue de cada fila
                $pdf->SetTextColor(3, 3, 3); //Color del texto: Negro
                $color=1;
                }
                $pdf->SetFillColor(255,255,255);//Fondo verde de celda
                $pdf->SetTextColor(0, 0, 0); //Letra color blanco
                $pdf->SetFont('Arial','B',11);
                $pdf->Cell(190,5,'DESCRIPCION',0,1,'C',true);
                $pdf->SetFillColor(255, 255, 255 ); //gris tenue de cada fila
                $pdf->SetTextColor(3, 3, 3); //Color del texto: Negro
                $pdf->SetFont('Arial','',11);
                $arr = explode("\n", $servicioGeneral->descripcion);

                for ($i = 0; $i < count($arr); $i++) {
                    $linea = $arr[$i];
                    $pdf->MultiCell(190,6,utf8_decode($linea),'B','L',0);
                 }
                
                //$pdf->MultiCell(190,5,utf8_decode($servicioGeneral->descripcion),'B','L',0);
                $pdf->Ln();

                $pdf->SetFont('Arial','B',11);
                $pdf->SetFillColor(255,255,255);//Fondo verde de celda
                $pdf->SetTextColor(0, 0, 0); //Letra color blanco
                $pdf->Cell(63,5,'INTERES',0,0,'C',true);
                $pdf->Cell(63,5,'PRECIO',0,0,'C',true);
                $pdf->Cell(63,5,'TIPO PAGO',0,1,'C',true);
                $pdf->SetFont('Arial','',11);
                $pdf->SetFillColor(255, 255, 255 ); //gris tenue de cada fila
                $pdf->SetTextColor(3, 3, 3); //Color del texto: Negro
                $pdf->Cell(63,5,$servicioGeneral->interes.' bs','LR',0,'C',true);
                $pdf->Cell(63,5,$servicioGeneral->monto_total.' bs','LR',0,'C',true);
                if($servicioGeneral->tipo_pago==1){
                    $pdf->Cell(63,5,'CONTADO','LR',1,'C',true);
                }else{
                    $pdf->Cell(63,5,'CREDITO','LR',1,'C',true);
                }
        
            $pdf->ln();
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(17,5,$configuracion->leyenda,0,1,'L');
          
            $pdf->Cell(55,5,utf8_decode('Total a pagar con interes: '),0,0,'L');
            $pdf->SetFont('Arial','B',11);
            $pdf->Cell(20,5,number_format($total,2,'.','').' bs',0,1,'L');
            $pdf->ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(20,5,$estado,0,1,'L');
            
            $pdf->Output('I','detalle_servicio'.$fecha.'.pdf');

    }

  

    public function clientes_deudores_servicio()
    {  
       
        $sql="SELECT c.id as id_cliente,(
            SELECT IFNULL(SUM(sgg.monto_total),0)
            FROM servicio_generals sgg
            WHERE sgg.id_cliente=sg.id_cliente AND
                sgg.estado=-1 AND
                sgg.tipo_pago=sg.tipo_pago
            ) as total_deuda,
            (
                SELECT IFNULL(SUM(sggg.monto_total),0)
                FROM servicio_generals sggg
                WHERE sggg.id_cliente=sg.id_cliente AND
                sggg.estado=1 AND
                sggg.tipo_pago=sg.tipo_pago
            ) as total_pagado
            FROM servicio_generals sg,clientes c
            WHERE 
            sg.tipo_pago=0 AND
            sg.estado=-1 AND
            sg.id_cliente=c.id
            GROUP BY c.id,total_deuda,total_pagado
            ORDER BY (total_deuda) DESC
        ";
        $datos=DB::select($sql);
        return DataTables::of($datos)
            // anadir nueva columna botones
           ->addColumn('actions', function($datos){
            //$btn_plan='<a class="btn btn-sm btn-dark" rel="tooltip" data-placement="top" title="ver plan de pagos realizados" onclick="mostrar_plan_pagos('.$cliente->id.')" ><i class="far fa-clipboard"></i></a>';
            $btn_credito='<a class="btn btn-sm btn-success" rel="tooltip" data-placement="top" title="Pagar todo" onclick="pagar_todo_venta('.$datos->id_cliente.','.$datos->total_deuda-$datos->total_pagado.',-1,-1,-1,1)" ><i class="far fa-clipboard"></i></a>';
            $btn_mostrar_detalles='<a class="btn btn-sm btn-danger" rel="tooltip" data-placement="top" title="Detalles de servicio" onclick="Mostrar_notas_servicios('.$datos->id_cliente.')" ><i class="far fa-clipboard"></i></a>';
            $btn= '<div class="text-right">  <div class="btn-group btn-group-sm ">';
                //if(auth()->user()->can('servicio.credito')){
                //    if($datos->total_deuda-$datos->total_pagado>0){
                //        $btn= $btn.$btn_credito;
                //    }   
                //}
                $btn= $btn.$btn_mostrar_detalles;
            
                $btn=$btn.'</div> </div> ';
             return  $btn;
           })

           ->addColumn('nombre_completo', function($datos){
           
            $cliente=Cliente::findOrFail($datos->id_cliente);
            return  $cliente->nombre.' '.$cliente->apellidos;
           })
           ->addColumn('ci', function($datos){
           
            $cliente=Cliente::findOrFail($datos->id_cliente);
            return  $cliente->ci;
           })
           ->addColumn('telefono', function($datos){
            
            $cliente=Cliente::findOrFail($datos->id_cliente);
            return  $cliente->telefono;
           })
           ->addColumn('edad', function($datos){
         
            $cliente=Cliente::findOrFail($datos->id_cliente);
            return  $cliente->edad;
           })
           ->addColumn('sexo', function($datos){
            
            $cliente=Cliente::findOrFail($datos->id_cliente);
            return  $cliente->sexo;
           })
           ->addColumn('total_pagado2', function($datos){
            if($datos->total_pagado==null){
                $monto=0;
            }else{
                $monto=$datos->total_pagado;
            }
            return  $monto.' bs';
            })
            ->addColumn('total_deuda2', function($datos){
                
                if($datos->total_deuda==null){
                    $monto=0;
                }else{
                    $monto=$datos->total_deuda;
                }
                return  $monto.' bs';
            })
           
           ->addColumn('estado', function($datos){
            if(($datos->total_deuda)<=0){
                $span= '<span class="badge bg-success">Completado</span>';
               
            }else{
                $span= '<span class="badge bg-danger">Incompleto</span>';
            }
            return  $span;
            })
           ->rawColumns(['actions','estado']) // incorporar columnas
           ->make(true); // convertir a codigo
    }

    public function comprobantes(){
        return view('caja_general/servicios');
    }

    public function servicios_datos(){
        
        $servicio=ServicioGeneral::select(
            'servicio_generals.*'
        )
        //->where('servicio_generals.created_at','>=',$fecha_inicial)
        //->orWhere('servicio_generals.fecha_deuda','>=',$fecha_inicial)
        //->where('servicio_generals.id_caja','=',$id_caja)
        ->orderBy('servicio_generals.created_at','desc');

        return DataTables::of($servicio)
            // anadir nueva columna botones
           ->addColumn('actions', function($servicio){
            $url_detalle=route('general.pdf_tiket',$servicio->id);
             $btn_eliminar='<a class="btn btn-warning" rel="tooltip" data-placement="top" title="Eliminar" onclick="Eliminar('.$servicio->id.')"><i class="far fa-trash-alt"></i></a>';
             $btn_pdf='<a class="btn btn-danger" rel="tooltip" data-placement="top" title="PDF"  onclick="mostrar_detalle_servicio('.$servicio->id.')" ><i class="fas fa-file-pdf"></i></a>';
                $btn= '<div class="text-right">  <div class="btn-group btn-group-sm ">';
                if($servicio->estado==1 || $servicio->estado==-1 ){
                    if(auth()->user()->can('servicio.eliminar')){
                        $btn= $btn.$btn_eliminar.$btn_pdf;
                    }else{
                        $btn= $btn.$btn_pdf;
                    }
                   
                }else{
                   $btn= $btn.$btn_pdf;
                }
                $btn=$btn.'</div> </div> ';
             return  $btn;
           })
           ->addColumn('estado', function($servicio){
            if($servicio->estado==0){
                $span= '<span class="badge bg-danger">Cancelado</span>';
            }
            if($servicio->estado==1){
                $span= '<span class="badge bg-success">Completdo</span>';
            }
            if($servicio->estado==-1){
                $span= '<span class="badge bg-warning">Incompleto</span>';
            }
            return  $span;
            })
            ->addColumn('tipo', function($servicio){
                if($servicio->tipo_pago==0){
                    $span= '<span class="badge bg-dark">Credito</span>';
                }
                if($servicio->tipo_pago==1){
                    $span= '<span class="badge bg-white">Contado</span>';
                }
                return  $span;
            })
            ->addColumn('fecha_hora', function($servicio){
            
                return  $servicio->created_at;
            })
            ->addColumn('monto', function($servicio){
                return  $servicio->monto_total . " bs";
            })
            ->addColumn('interes', function($servicio){
                return  $servicio->interes . " bs";
            })
           
            ->addColumn('cliente', function($servicio){
                if($servicio->id_cliente==null){
                    return "publico general";
                }else{
                    $cliente=Cliente::findOrFail($servicio->id_cliente);
                    return $cliente->nombre.' '.$cliente->apellidos;
                }
              
            })
            ->addColumn('empleado', function($servicio){
                if($servicio->id_empleado==null){
                    return "sin asignar";
                }else{
                    $empleado=Empleado::findOrFail($servicio->id_empleado);
                    return $empleado->nombre.' '.$empleado->apellidos;
                }
              
            })
            
           ->rawColumns(['actions','cliente','tipo','monto','descuento_monto','interes','empleado','fecha_hora','estado']) // incorporar columnas
           ->make(true); // convertir a codigo
    


    }
    
}
