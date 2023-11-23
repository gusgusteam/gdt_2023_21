
@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark text-uppercase">Deudas de credito {{$nombre_caja}}</h1>
@stop

@section('content')
  <div class="row">
    <div class="col-sm-6">
      <h3>cliente : {{$nombre_cliente}} </h3>
    </div>
    <div class="col-sm-6 text-right">
      <a class="btn btn-primary" href="{{route('cliente.index',1)}}">Regresar</a>
    </div>
  </div>
  <br>
  <div class="row">
      <div class="col-12">
        <div class="card card-outline card-success">
          <div class="card-header">
              <h5 class="text-center">CREDITOS SERVICIOS POR MESES</h5>
          </div>
              <div class="card-body">
              <table id="tabla_deuda" class="table table-responsive-xl table-bordered table-sm table-hover table-striped">
                <thead class="text-center">
                    <tr>
                      <th width="15%">Anio</th>
                      <th width="10%">mes</th>
                      <th width="20%">Total deuda</th>
                      <th width="20%">Saldo faltante</th>
                      <th width="15%">Saldo pagado</th>
                      <th width="10%">estado</th>
                      <th width="5%"></th>
                    </tr>
                </thead>
                <tbody>
                 
                </tbody>
               </table>
              </div>
        </div>
      </div>
  </div>

  <div class="modal fade" id="modal_deudas" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;" data-gtm-vis-first-on-screen-2340190_1302="5621" data-gtm-vis-total-visible-time-2340190_1302="100" data-gtm-vis-has-fired-2340190_1302="1" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <!--Content-->
      <div class="modal-content">
        <!--Header-->
        <div class="modal-header {{ config('adminlte.classes_index_modal_agregar','') }}">
          <h5 class="modal-title">Lista Deudas</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" class="white-text">×</span>
          </button>
        </div>
        <div id="frm" class="modal-body">
          <input type="hidden" name="id_cliente_aux" id="id_cliente_aux">
          <input type="hidden" name="mes_aux" id="mes_aux">
          <input type="hidden" name="anio_aux" id="anio_aux">
          <table  id="tablaServicios" class="table table-responsive table-bordered table-sm table-hover table-striped ">
            <thead>
                <tr>
                  <th width="5%">Nro</th>
                  <th width="10%">Codigo</th>
                  <th width="14%">Fecha</th>  
                  <th width="15%">Fecha Deuda</th>    
                  <th width="30%">Empleado</th>
                  <th width="6%">Monto</th>  
                  <th width="5%">Interes</th> 
                  <th width="10%">Estado</th>     
                  <th width="5%"></th>    
                </tr>
            </thead>
            <tbody>
             
            </tbody>
        </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-info btn-sm" data-dismiss="modal">Cerrar lista</button>
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
          <h5 class="modal-title">Detalle servicio</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" class="white-text">×</span>
          </button>
        </div>
        <div id="frm" class="modal-body">
          <div class="embed-responsive embed-responsive-16by9">
            <iframe id="detalle_pdf_servicio" class="embed-responsive-item" src="" allowfullscreen></iframe>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-info btn-sm" data-dismiss="modal">Cerrar PDF</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="completar_credito" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;" data-gtm-vis-first-on-screen-2340190_1302="5621" data-gtm-vis-total-visible-time-2340190_1302="100" data-gtm-vis-has-fired-2340190_1302="1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <!--Content-->
      <div class="modal-content">
        <!--Header-->
        <div class="modal-header {{ config('adminlte.classes_index_modal_agregar','') }}">
          <h5 class="modal-title">Completar credito</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" class="white-text">×</span>
          </button>
        </div>
        <div id="frm" class="modal-body">
          <input type="hidden" value="" id="id_registro">
          <div class="row">
            <div class="col-sm-6">
              <label for="monto_completar">Monto total</label>
              <input class="form-control" disabled type="text" id="monto_completar" value="0">
            </div>
            <div class="col-sm-6">
              <label for="fecha_completar">Fecha de credito : <p id="fecha_completar"></p></label>
           
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <label for="interes">Interes</label>
              <input class="form-control" type="number" step="any" name="interes" id="interes" value="0">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <a class="btn btn-sm btn-success" onclick="actualizar_nota_servicio()" >Completar credito</a>
          <button type="button" class="btn btn-info btn-sm" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  @stop

  @section('js')

  <script>
    var r=false;
    if({{session()->has('responsivo')}}){
      if({{session('responsivo')}}==1){
        r=true;
      }
    }
    var url_directo='{{asset('')}}general/server/'+{{$id_caja}}+'/'+{{$id_cliente}};
    var languages = {'es': '{{asset('vendor/adminlte/Spanish.json')}}'};
      $('#tabla_deuda').DataTable({ 
        language: {url: languages['es']},
        destroy: true,
        retrieve: true,
        serverSide: true,
        autoWidth: false,
        responsive: r,
        ajax: url_directo,
        columns: [
            {data: 'anio_tiempo',searchable: false,orderable: false},
            {data: 'mes_tiempo',searchable: false},
            {data: 'monto_general2',searchable: true},
            {data: 'saldo_faltante',searchable: false},
            {data: 'saldo_pagado2',searchable: false},
            {data: 'estado',searchable: false},
            {data: 'actions',searchable: false,orderable: false}
        ],
      })
  </script>


  <script >

  function completar_credito(id_servicio,fecha,monto){
    $('#id_registro').val(id_servicio);
    $('#monto_completar').val(monto+' bs');
    $('#fecha_completar').text(fecha);
    $('#completar_credito').modal('show');
  }
  function actualizar_nota_servicio(){
    if($('#interes').val()!=null && $('#interes').val()!='' && $('#interes').val()>=0){
    var url='{{asset('')}}general/cancelar_credito';
      $.ajax({
        url:url,dataType: 'json', 
        method: "post",
        data: {
            "interes"         : $('#interes').val(),
            "id_servicio"        : $('#id_registro').val(),
            "_token"          :"{{ csrf_token() }}",
        },
        success: function(resultado){
          if(resultado.error==0){
            toastr.success('El credito fue concluido.', 'Credito', {timeOut:1000}); 
            $('#completar_credito').modal('hide');
            datos_de_deuda_cliente($('#id_cliente_aux').val(),$('#anio_aux').val(),$('#mes_aux').val());
            $("#tabla_deuda").DataTable().ajax.reload();
          }else{
            mostrarerror('error!','error',resultado.mensaje);
          }
        }
      });
    }else{
      mostrarerror('error!','error','por favor verifique los datos  interes');
    }
  }
  function Mostrar_notas_servicios(id_cliente,anio,mes){
    $('#modal_deudas').modal('show');
    $('#id_cliente_aux').val(id_cliente);
    $('#anio_aux').val(anio);
    $('#mes_aux').val(mes);
    datos_de_deuda_cliente(id_cliente,anio,mes);
  }
  function datos_de_deuda_cliente(id_cliente,anio,mes) { 
    if(id_cliente !=''){
      if (anio != null && anio != 0 && mes != null && mes !=0 && mes<=12) {
            var url='{{asset('')}}general/deuda_dias/'+id_cliente+'/'+anio+'/'+mes;
              $.ajax({
                  url:url,dataType: 'json', 
                  success: function(resultado){
                    
                      $("#tablaServicios tbody").empty();
                      $("#tablaServicios tbody").append(resultado.fila);
                      
                  }
            });
      }else { mostrarerror('error!','error','por favor verifique los datos');}
    }else{mostrarerror('error!','error','Problemas de la pagina de espera recarge de nuevo');}
  }

  function mostrar_detalle(id_servicio){
    var url='{{asset('')}}general/pdf_tiket/'+id_servicio ;
    
    $('#pdf_modal').modal('show');
    $('#detalle_pdf_servicio').attr('src', url);
  }


    
    function mostrarerror(title,icono,error){
      Swal.fire({
        title: title,
        text: error,
        icon: icono,
        confirmButtonText: 'okey'
      })
    }

    

    
    
    

  </script>
 
    
  @stop