@extends('adminlte::page')

@section('title')

@section('content_header')
  {{--<h1 class="m-0 text-dark">INICIO2</h1>--}}
@stop

@section('content')
    @if (Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Socio'))
        <div class="row">
            <div class="col-xl-3 col-sm-6 mb-2">
                <div class="small-box bg-info">
                    <div class="inner">
                      <h3>{{$total}}</h3>
                      <p>Total de productos&nbsp;&nbsp;</p>
                    </div>
                    <div class="icon">
                      <i class="fab fa-product-hunt"></i>
                    </div>
                    <a href="#" class="small-box-footer">&nbsp;&nbsp;Ver detalles <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-2">
                <div class="small-box bg-success">
                    <div class="inner">
                      <h3>@if($totalVentas==null) {{0}} @else{{ $totalVentas}} @endif <sup style="font-size: 20px">bs.</sup></h3>
                      <p>Ventas del día &nbsp;&nbsp;{{ date('d-m-Y')}}</p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-cash-register"></i>
                    </div>
                    <a href="{{asset('ver_informe')}}/{{date('Y-m-d')}}" class="small-box-footer">&nbsp;&nbsp;Ver detalles  <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-2">
                <div class="small-box bg-warning">
                    <div class="inner">
                      <h3>
                        @php
                            $m_actual=0;
                            $i_actual=0;
                        @endphp
                        @foreach ($totalxmeses as  $totalxmes)
                            @if ($totalxmes->mes == date("F")) 
                                @php
                                    $m_actual= $totalxmes->total_venta
                                @endphp 
                            @endif
                        @endforeach
                        @foreach ($total_interesxmeses as  $total_interesxmes)
                            @if ($total_interesxmes->mes == date("F")) 
                                @php
                                    $i_actual= $total_interesxmes->total_interes
                                @endphp 
                            @endif
                        @endforeach
                        @if(isset($m_actual) && isset($i_actual)){{$m_actual + $i_actual}}@else {{0}} @endif 
                        <sup style="font-size: 20px">bs.</sup>
                      </h3>
                      <p>Monto del mes {{date("F")}}</p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-cash-register"></i>
                    </div>
                    <a href="{{asset('generar_reporte')}}" class="small-box-footer">&nbsp;&nbsp;Ver detalles <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 mb-2">
                <div class="small-box bg-danger">
                    <div class="inner">
                      <h3>{{$minimos}}</h3>
                      <p>Stock mínimos</p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-list"></i>
                    </div>
                    <a onclick="reporte_minimo()" class="small-box-footer"> &nbsp;&nbsp;Ver detalles <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
        <div class="row">
         
          <div class="col-xl-3 col-sm-6 mb-2">
            <div class="small-box bg-primary">
                <div class="inner">
                  <div class="card text-dark">
                    <div class="icon">
                      <i class="fab fa-product-hunt"></i>
                    </div>
                    <div class="card-header">
                      <h4><b>Caja inventario @if ($caja_inventario->estado==1)
                        <span class="badge bg-success">En Curso</span>   
                      @else
                        <span class="badge bg-danger">Cerrado</span> 
                      @endif</b></h4>
                    </div>
                    <div class="card-body">
                      <b> Ingreso caja = {{$caja_inventario->monto_ingreso_caja}} bs</b>
                      <br>
                      <b> Ingreso Venta = {{$caja_inventario->monto_total_generado}} bs</b>
                      <br>
                      <b> Egresos Caja y Compra = {{$caja_inventario->monto_egreso}} bs</b>
                      
                    </div>
                    <div class="card-footer">
                      <b>Margen de capital</b><h3>@if (($caja_inventario->monto_ingreso)>=0)
                          {{$caja_inventario->monto_ingreso}} bs
                      @else
                          N bs
                      @endif</h3>
                      <div class="row align-items-center">
                        <div class="col-4 align-self-start">
                          <a href="" class="btn btn-primary btn-sm small-box-footer">&nbsp;Detalles <i class="fas fa-arrow-circle-right"></i></a>          
                        </div>
                        <div class="col-4 align-self-center">
                          <a href="{{asset('caja/informe_general').'/'.$caja_inventario->id.'/0'}}" class="btn btn-success btn-sm small-box-footer">&nbsp;&nbsp;informe <i class="fas fa-arrow-circle-right"></i></a>          
                        </div>
                        <div class="col-4 align-self-end">
                          <a href="{{route('caja.caja_panel',$caja_inventario->id)}}" class="btn btn-dark btn-sm small-box-footer">&nbsp;&nbsp;panel <i class="fas fa-arrow-circle-right"></i></a>     
                        </div> 
                      </div>
                    </div>
                  </div>
                </div>
            </div>
          </div>
          <div class="col-xl-3 col-sm-6 mb-2">
            <div class="small-box bg-danger">
                <div class="inner">
                  <div class="card text-dark">
                    <div class="icon">
                      <i class="fab fa-product-hunt"></i>
                    </div>
                    <div class="card-header">
                      <h4><b>Caja taller @if ($caja_taller->estado==1)
                        <span class="badge bg-success">En Curso</span>   
                      @else
                        <span class="badge bg-danger">Cerrado</span> 
                      @endif</b></h4>
                    </div>
                    <div class="card-body">
                      <b> Ingreso caja = {{$caja_taller->monto_ingreso_caja}} bs</b>
                      <br>
                      <b> Ingreso trabajo = {{$caja_taller->monto_total_generado}} bs</b>
                      <br>
                      <b> Egresos = {{$caja_taller->monto_egreso}} bs</b>
                      
                    </div>
                    <div class="card-footer">
                      <b>Margen de capital</b><h3>{{$caja_taller->monto_ingreso}} bs</h3>
                      <div class="row align-items-center">
                        <div class="col-4 align-self-start">
                          <a href="{{asset('general/detalles')}}/{{$caja_taller->id}}" class="btn btn-primary btn-sm small-box-footer">&nbsp;Detalles <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                        <div class="col-4 align-self-center">
                          <a href="{{asset('caja/informe_general').'/'.$caja_taller->id.'/0'}}" class="btn btn-success btn-sm small-box-footer">&nbsp;&nbsp;informe <i class="fas fa-arrow-circle-right"></i></a>          
                        </div>
                        <div class="col-4 align-self-end">
                          <a href="{{route('caja.caja_panel',$caja_taller->id)}}" class="btn btn-dark btn-sm small-box-footer">&nbsp;&nbsp;panel <i class="fas fa-arrow-circle-right"></i></a>     
                        </div> 
                      </div>
                    </div>
                  </div>
                </div>
            </div>
          </div>
          <div class="col-xl-3 col-sm-6 mb-2">
            <div class="small-box bg-warning">
                <div class="inner">
                  <div class="card text-dark">
                    <div class="icon">
                      <i class="fab fa-product-hunt"></i>
                    </div>
                    <div class="card-header">
                      <h4><b>Caja Grua @if ($caja_grua->estado==1)
                        <span class="badge bg-success">En Curso</span>   
                      @else
                        <span class="badge bg-danger">Cerrado</span> 
                      @endif</b></h4>
                    </div>
                    <div class="card-body">
                      <b> Ingreso caja = {{$caja_grua->monto_ingreso_caja}} bs</b>
                      <br>
                      <b> Ingreso trabajo = {{$caja_grua->monto_total_generado}} bs</b>
                      <br>
                      <b> Egresos = {{$caja_grua->monto_egreso}} bs</b>
                    </div>
                    <div class="card-footer">
                      <b>Margen de capital</b><h3>{{$caja_grua->monto_ingreso}} bs</h3>
                      <div class="row align-items-center">
                        <div class="col-4 align-self-start">
                          <a href="{{asset('general/detalles')}}/{{$caja_grua->id}}" class="btn btn-primary btn-sm small-box-footer">&nbsp;Detalles <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                        <div class="col-4 align-self-center">
                          <a href="{{asset('caja/informe_general').'/'.$caja_grua->id.'/0'}}" class="btn btn-success btn-sm small-box-footer">&nbsp;&nbsp;informe <i class="fas fa-arrow-circle-right"></i></a>          
                        </div>
                        <div class="col-4 align-self-end">
                          <a  href="{{route('caja.caja_panel',$caja_grua->id)}}"  class="btn btn-dark btn-sm small-box-footer">&nbsp;&nbsp;panel <i class="fas fa-arrow-circle-right"></i></a>     
                        </div> 
                      </div>
                    </div>
                  </div>
                </div>
            </div>
          </div>
          <div class="col-xl-3 col-sm-6 mb-2">
            <div class="small-box bg-success">
                <div class="inner">
                  <div class="card text-dark">
                    <div class="icon">
                      <i class="fab fa-product-hunt"></i>
                    </div>
                    <div class="card-header">
                      <h4><b>Caja Maquinaria @if ($caja_maquinaria->estado==1)
                        <span class="badge bg-success">En Curso</span>   
                      @else
                        <span class="badge bg-danger">Cerrado</span> 
                      @endif</b></h4>
                    </div>
                    <div class="card-body">
                      <b> Ingreso caja = {{$caja_maquinaria->monto_ingreso_caja}} bs</b>
                      <br>
                      <b> Ingreso trabajo = {{$caja_maquinaria->monto_total_generado}} bs</b>
                      <br>
                      <b> Egresos = {{$caja_maquinaria->monto_egreso}} bs</b>
                    </div>
                    <div class="card-footer">
                      <b>Margen de capital</b><h3>{{$caja_maquinaria->monto_ingreso}} bs</h3>
                      <div class="row align-items-center">
                        <div class="col-4 align-self-start">
                          <a  href="{{asset('general/detalles')}}/{{$caja_maquinaria->id}}" class="btn btn-primary btn-sm small-box-footer">&nbsp;Detalles <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                        <div class="col-4 align-self-center">
                          <a href="{{asset('caja/informe_general').'/'.$caja_maquinaria->id.'/0'}}" class="btn btn-success btn-sm small-box-footer">&nbsp;&nbsp;informe <i class="fas fa-arrow-circle-right"></i></a>          
                        </div>
                        <div class="col-4 align-self-end">
                          <a  href="{{route('caja.caja_panel',$caja_maquinaria->id)}}" class="btn btn-dark btn-sm small-box-footer">&nbsp;&nbsp;panel <i class="fas fa-arrow-circle-right"></i></a>     
                        </div> 
                      </div>
                    </div>
                  </div>
                </div>
            </div>
          </div>
          <div class="col-xl-3 col-sm-6 mb-2">
            <div class="small-box bg-dark">
                <div class="inner">
                  <div class="card text-dark">
                    <div class="icon">
                      <i class="fab fa-product-hunt"></i>
                    </div>
                    <div class="card-header">
                      <h4><b>Caja LAB @if ($caja_labadero->estado==1)
                        <span class="badge bg-success">En Curso</span>   
                      @else
                        <span class="badge bg-danger">Cerrado</span> 
                      @endif</b></h4>
                    </div>
                    <div class="card-body">
                      <b> Ingreso caja = {{$caja_labadero->monto_ingreso_caja}} bs</b>
                      <br>
                      <b> Ingreso trabajo = {{$caja_labadero->monto_total_generado}} bs</b>
                      <br>
                      <b> Egresos = {{$caja_labadero->monto_egreso}} bs</b>
                    </div>
                    <div class="card-footer">
                      <b>Margen de capital</b><h3>{{$caja_labadero->monto_ingreso}} bs</h3>
                      <div class="row align-items-center">
                        <div class="col-4 align-self-start">
                          <a href="{{asset('general/detalles')}}/{{$caja_labadero->id}}" class="btn btn-primary btn-sm small-box-footer">&nbsp;Detalles <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                        <div class="col-4 align-self-center">
                          <a href="{{asset('caja/informe_general').'/'.$caja_labadero->id.'/0'}}" class="btn btn-success btn-sm small-box-footer">&nbsp;&nbsp;informe <i class="fas fa-arrow-circle-right"></i></a>          
                        </div>
                        <div class="col-4 align-self-end">
                          <a href="{{route('caja.caja_panel',$caja_labadero->id)}}" class="btn btn-dark btn-sm small-box-footer">&nbsp;&nbsp;panel <i class="fas fa-arrow-circle-right"></i></a>     
                        </div> 
                      </div>
                    </div>
                  </div>
                </div>
            </div>
          </div>
          <div class="col-xl-3 col-sm-6 mb-2">
            <div class="small-box bg-info">
                <div class="inner">
                  <div class="card text-dark">
                    <div class="icon">
                      <i class="fab fa-product-hunt"></i>
                    </div>
                    <div class="card-header">
                      <h4><b>Caja Ganaderia @if ($caja_ganaderia->estado==1)
                        <span class="badge bg-success">En Curso</span>   
                      @else
                        <span class="badge bg-danger">Cerrado</span> 
                      @endif</b></h4>
                    </div>
                    <div class="card-body">
                      <b> Ingreso caja = {{$caja_ganaderia->monto_ingreso_caja}} bs</b>
                      <br>
                      <b> Ingreso trabajo = {{$caja_ganaderia->monto_total_generado}} bs</b>
                      <br>
                      <b> Egresos = {{$caja_ganaderia->monto_egreso}} bs</b>
                    </div>
                    <div class="card-footer">
                      <b>Margen de ganancia</b><h3>{{$caja_ganaderia->monto_ingreso}} bs</h3>
                      <div class="row align-items-center">
                        <div class="col-4 align-self-start">
                          <a href="{{asset('general/detalles')}}/{{$caja_ganaderia->id}}" class="btn btn-primary btn-sm small-box-footer">&nbsp;Detalles <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                        <div class="col-4 align-self-center">
                          <a href="{{asset('caja/informe_general').'/'.$caja_ganaderia->id.'/0'}}" class="btn btn-success btn-sm small-box-footer">&nbsp;&nbsp;informe <i class="fas fa-arrow-circle-right"></i></a>          
                        </div>
                        <div class="col-4 align-self-end">
                          <a href="{{route('caja.caja_panel',$caja_ganaderia->id)}}" class="btn btn-dark btn-sm small-box-footer">&nbsp;&nbsp;panel <i class="fas fa-arrow-circle-right"></i></a>     
                        </div> 
                      </div>
                    </div>
                  </div>
                </div>
            </div>
          </div>
          <div class="col-xl-6 col-sm-6 mb-2">
            <div class="small-box bg-pink">
                <div class="inner">
                  <div class="card text-dark">
                    <div class="icon">
                      <i class="fab fa-product-hunt"></i>
                    </div>
                    <div class="card-header">
                      <h4><b>CAJA GENERAL GDT @if ($caja_general->estado==1)
                        <span class="badge bg-success">En Curso</span>   
                      @else
                        <span class="badge bg-danger">Cerrado</span> 
                      @endif</b></h4>
                    </div>
                    <div class="card-body">
                      <b> Ingreso caja = {{$caja_general->monto_ingreso_caja}} bs</b>
                      <br>
                      <b> Ingreso trabajo = {{$caja_general->monto_total_generado}} bs</b>
                      <br>
                      <b> Egresos = {{$caja_general->monto_egreso}} bs</b>
                      
                    </div>
                    <div class="card-footer">
                      <b>Margen de capital</b><h3>{{$caja_general->monto_ingreso}} bs</h3>
                      
                      <div class="row align-items-center">
                        <div class="col-4 align-self-start">
                          <a href="#" class="btn btn-primary btn-sm small-box-footer">&nbsp;Estadistica<i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                        <div class="col-4 align-self-center">
                          <a href="{{asset('caja/informe_general').'/'.$caja_general->id.'/0'}}" class="btn btn-success btn-sm small-box-footer">&nbsp;&nbsp;informe <i class="fas fa-arrow-circle-right"></i></a>          
                        </div>
                        <div class="col-4 align-self-end">
                          <a href="{{route('caja.caja_panel',$caja_general->id)}}" class="btn btn-dark btn-sm small-box-footer">&nbsp;&nbsp;panel <i class="fas fa-arrow-circle-right"></i></a>     
                        </div> 
                      </div>
                    </div>
                  </div>
                </div>
            </div>
          </div>
        </div>







        <div class="modal fade" id="pdf_modal" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;" data-gtm-vis-first-on-screen-2340190_1302="5621" data-gtm-vis-total-visible-time-2340190_1302="100" data-gtm-vis-has-fired-2340190_1302="1" aria-hidden="true">
          <div class="modal-dialog modal-xl" role="document">
            <!--Content-->
            <div class="modal-content">
              <!--Header-->
              <div class="modal-header {{ config('adminlte.classes_index_modal_agregar','') }}">
                <h5 class="modal-title">INFORME DE CAJA</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true" class="white-text">×</span>
                </button>
              </div>
              <div id="frm" class="modal-body">
                <div class="embed-responsive embed-responsive-16by9">
                  <iframe id="informe_caja" class="embed-responsive-item" src="" allowfullscreen></iframe>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-info btn-sm" data-dismiss="modal">Cerrar PDF</button>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="pdf_modal_informe" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;" data-gtm-vis-first-on-screen-2340190_1302="5621" data-gtm-vis-total-visible-time-2340190_1302="100" data-gtm-vis-has-fired-2340190_1302="1" aria-hidden="true">
          <div class="modal-dialog modal-xl" role="document">
            <!--Content-->
            <div class="modal-content">
              <!--Header-->
              <div class="modal-header {{ config('adminlte.classes_index_modal_agregar','') }}">
                <h5 class="modal-title">INFORME</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true" class="white-text">×</span>
                </button>
              </div>
              <div id="frm" class="modal-body">
                <div class="embed-responsive embed-responsive-16by9">
                  <iframe id="informe_todo" class="embed-responsive-item" src="" allowfullscreen></iframe>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-info btn-sm" data-dismiss="modal">Cerrar PDF</button>
              </div>
            </div>
          </div>
        </div>

    @endif
    @if(Auth::user()->hasRole('Caja'))
       <div class="row">
        <div class="col-sm-12">
            <img src="{{asset('img/fondo_principal.jpg')}}" class="img img-fluid" alt="no existe la foto">
        </div>
       </div> 
    @endif
@stop


@section('js')

<script>
function informe(id_caja,fecha_inicial,fecha_final){
  var url='{{asset('')}}caja/informe/'+id_caja+'/'+ fecha_inicial+'/'+fecha_final;
  $('#pdf_modal').modal('show');
  $('#informe_caja').attr('src', url);
}

function reporte_minimo(){
  var url='{{asset('')}}informe_productos';
  $('#pdf_modal_informe').modal('show');
  $('#informe_todo').attr('src', url);
}
</script>

@stop