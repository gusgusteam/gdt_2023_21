<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Configuracion;
use App\Models\detalle_plan;
use App\Models\Empleado;
use App\Models\planpago;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Codedge\Fpdf\Fpdf\Fpdf;

class PlanpagoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        return view('plan_pago/index');
    }

    public function datos(){
        $caja=Caja::findOrFail(1);
        $fecha_inicial=$caja->fecha_inicio.' '.$caja->hora_inicio;
        $planpago=planpago::select(
            'planpagos.*'
        )
        ->where('planpagos.created_at','>=',$fecha_inicial)
        ->orderBy('planpagos.created_at','desc');

        return DataTables::of($planpago)
            // anadir nueva columna botones
           ->addColumn('actions', function($planpago){
        
             $btn_pdf='<a class="btn btn-sm btn-danger " rel="tooltip" data-placement="top" title="mostrar pdf" onclick="Mostrar_pdf('.$planpago->id.')" ><i class="fas fa-file-pdf"></i></a>';
             $btn_cancelar='<a class="btn btn-sm btn-warning" rel="tooltip" data-placement="top" title="Cancelar" onclick="Cancelar('.$planpago->id.')"><i class="far fa-trash-alt"></i></a>';
                $btn= '<div class="text-right">  <div class="btn-group btn-group-sm ">';
                if($planpago->estado==1 && auth()->user()->can('plan.eliminar') ){
                   $btn= $btn.$btn_cancelar.$btn_pdf;
                }else{
                   $btn= $btn.$btn_pdf;
                }
                $btn=$btn.'</div> </div> ';
             return  $btn;
           })
           ->addColumn('estado', function($planpago){
            if($planpago->estado==0){
                $span= '<span class="badge bg-danger">Cancelado</span>';
            }
            if($planpago->estado==1){
                $span= '<span class="badge bg-success">Completado</span>';
            }
            return  $span;
            })
            ->addColumn('cliente', function($planpago){
                $cliente=Cliente::findOrFail($planpago->id_cliente);
                $nombre_cliente='---';
                if(isset($cliente)){
                    $nombre_cliente=$cliente->nombre.' '.$cliente->apellidos;
                }
                return  $nombre_cliente;
            })
            ->addColumn('fecha', function($planpago){
               $fecha=$planpago->created_at;
                return $fecha ;
            })
            ->addColumn('monto_total2', function($planpago){ 
                 return $planpago->monto_total.' bs' ;
             })
             ->addColumn('interes2', function($planpago){ 
                return $planpago->interes.' bs' ;
            })
            ->addColumn('empleado', function($planpago){
                $empleado=Empleado::where('id_usuario','=',$planpago->id_usuario)->first();
                $nombre_empleado='sin registro de empleado';
                if(isset($empleado)){
                    $nombre_empleado=$empleado->nombre.' '.$empleado->apellidos;
                }
                return  $nombre_empleado;
            })
           ->rawColumns(['actions','estado','cliente','empleado']) // incorporar columnas
           ->make(true); // convertir a codigo
    }


   
    public function cancelar(planpago $planpago)
    {   $data['error']=0;
        $data['mensaje']='';
        if( $planpago->estado==0){
            $data['error']=1;
            $data['mensaje']='no se puede cancelar 2 veces';
            return json_encode($data);
        }

        $detalles=detalle_plan::all()->where('id_plan','=',$planpago->id)->where('estado','=',1);

        foreach ($detalles as $row){
            $row->estado=0;
            $row->update();
            $venta=Venta::findOrFail($row->id_venta);
            if($venta->estado!=0){
                $venta->estado=-1;
                $venta->update();
            }   
        }
        
        $data['id_cliente']=$planpago->id_cliente;
        $planpago->estado=0;
        $planpago->update();
        return json_encode($data);
    }



    public function pdf(planpago $planpago){
        $sql = "SELECT v.fecha as fecha_venta, v.codigo as codigo_venta, v.monto_total as monto_venta,v.descuento as descuento_venta,dp.estado as estado_plan
        FROM planpagos p,detalle_plans dp,ventas v
        WHERE p.id=$planpago->id AND
        dp.id_plan=p.id AND
        dp.id_venta=v.id 
        ";

        $empleado=Empleado::all()->where('id_usuario','=',$planpago->id_usuario)->first();
        $cliente=Cliente::all()->where('id','=',$planpago->id_cliente)->first();
        if(isset($cliente)){
            $nombre_cliente=$cliente->nombre.' '.$cliente->apellidos;
            $carnet=$cliente->ci;
            $telefono=$cliente->telefono;
        }else{
            $nombre_cliente='publico general';
            $carnet='';
            $telefono='';
        }
        if(isset($empleado)){
            $nombre_empleado=$empleado->nombre.' '.$empleado->apellidos;
          
        }else{
            $nombre_empleado='no tiene registro de empleado';
           
        }
        $configuracion=Configuracion::all()->first();
        $detalle = DB::select($sql);
        if(count($detalle) <=0){
            return 'no existe este plan de pago ';
        }
        if($planpago->estado==1){
            $estado="Completado";
        }else{
            if($planpago->estado==0){
                $estado="Cancelado";
            }else{
                $estado=" ";  
            }
        }
        $fecha=$planpago->created_at;
        $pdf = new Fpdf('P','mm',array(200,200));
        
            $sw=1;
            $contador = 1;
            $color=0;
            foreach ($detalle as $row){
                if ($sw==1){
                    $pdf->AddPage();
                    $pdf->SetMargins(5,5,5);
                    $pdf->SetTitle("Detalle Plan");
                    $pdf->SetFont('Arial','B',16);
                    $pdf->image(asset('vendor/adminlte/dist/img/AdminLTELogo.png'),10,5,10,10,'PNG');
                    $pdf->Cell(190,4,'',0,1,'C');
                    $pdf->Cell(190,4,'NOTA DE CREDITO',0,1,'C');
                    $pdf->Ln(2);
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(17,5,utf8_decode('Dirección: '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,$configuracion->direccion,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(22,5,utf8_decode('Fecha y hora: '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,$fecha,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(22,5,utf8_decode('Remitente: '),0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(50,5,''.$nombre_empleado,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(15,5,'Codigo: ',0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(30,5,$planpago->codigo,0,1,'L');
                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(15,5,'Cliente: ',0,0,'L');
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(30,5,utf8_decode($nombre_cliente).'  ci: '.$carnet.' cel: '.$telefono,0,1,'L');
                    $pdf->SetFont('Arial','B',10);
                    $pdf->Ln();
                    $pdf->SetFillColor(2,100,200);//Fondo verde de celda
                    $pdf->SetTextColor(240, 255, 240); //Letra color blanco
                    $pdf->Cell(10,5,utf8_decode('Nº'),1,0,'L',true);
                    $pdf->Cell(25,5,'ESTADO',1,0,'L',true);
                    $pdf->Cell(40,5,'CODIGO DE VENTA',1,0,'L',true);
                    $pdf->Cell(25,5,'MONTO',1,0,'L',true);
                    $pdf->Cell(27,5,'DESCUENTO',1,0,'L',true);
                    $pdf->Cell(30,5,'FECHA',1,0,'L',true);
                    $pdf->MultiCell(25,5,'SUB TOTAL',1,1,'L',true);
                    $pdf->SetFont('Arial','',9);
                   
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
                if($row->estado_plan==1){
                    $pdf->Cell(25,5,'COMPLETADO','LR',0,'L',true); 
                }
                if($row->estado_plan==0){
                    $pdf->Cell(25,5,'CANCELADO','LR',0,'L',true); 
                }
               
                $pdf->Cell(40,5,$row->codigo_venta,'LR',0,'L',true);
                $pdf->Cell(25,5,$row->monto_venta.' bs','LR',0,'L',true);
                $pdf->Cell(27,5,$row->descuento_venta.' bs','LR',0,'L',true);
                $pdf->Cell(30,5,$row->fecha_venta,'LR',0,'L',true);
                $pdf->Cell(25,5,number_format(($row->monto_venta-$row->descuento_venta),2,'.','').' bs','LR',1,'L',true); // L= IZQUIERDA R= DERECHA
              
                if ($contador%19==0){$sw=1;}
                $contador++;
            }
           
            $pdf->ln();
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(17,5,$configuracion->leyenda,0,1,'L');
            $pdf->Cell(30,5,utf8_decode('Monto total: '),0,0,'L');
            $pdf->Cell(20,5,number_format($planpago->monto_total,2,'.','').' bs',0,1,'L');
            $pdf->Cell(30,5,utf8_decode('Interes: '),0,0,'L');
            $pdf->Cell(20,5,number_format($planpago->interes,2,'.','').' bs',0,1,'L');
            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(20,5,number_format($planpago->monto_total+$planpago->interes,2,'.','').' bs',0,1,'L');
            $pdf->ln();
            $pdf->Cell(20,5,$estado,0,1,'L');
            $pdf->SetFont('Arial','',10);
            
            $pdf->Output('I','detalle_plan'.$fecha.'.pdf');

    }

    function comprobar_eliminar_venta($id_venta){
        $codigo='';
        $datos=detalle_plan::all()->where('id_venta','=',$id_venta)->where('estado','=',1)->first();
        
        if(isset($datos)){
            $plan=planpago::findOrFail($datos->id_plan);
            $codigo=$plan->codigo;
            return $codigo; 
        }

      return $codigo;
    }


}
