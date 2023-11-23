
@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark">Deudas de credito</h1>
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
              <h5 class="text-center">CREDITOS POR MESES</h5>
          </div>
              <div class="card-body">
              <table id="tablaProductos" class="table table-responsive-xl table-bordered table-sm table-hover table-striped">
                <thead class="text-center">
                    <tr>
                      <th width="5%"> Nro </th>
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
                  @php
                      $c=1
                  @endphp
                  @foreach ($datos as $row)
                  <tr>
                    <td>{{$c}}</td>
                    <td>{{$row->anio_tiempo}}</td>
                    <td>{{$row->mes_tiempo}}</td>
                    <td>{{$row->monto_general}} bs</td>
                    <td>{{$row->monto_general-$row->saldo_pagado}} bs</td>
                    <td>{{$row->saldo_pagado}} bs</td>
                    @if ($row->monto_general-$row->saldo_pagado==0)
                      <td><span class="badge bg-success">Saldo Completado</span></td>  
                    @else
                      <td><span class="badge bg-danger">Saldo Iompletado</span></td>  
                    @endif
                   
                    <td class="text-right">
                      <div class="btn-group btn-group-sm">  
                        <a class="btn btn-sm btn-success" onclick="Mostrar_notas_ventas({{$id_cliente}},{{$row->anio_tiempo}},{{$row->mes_tiempo}})" title="Detalles"><i class="far fa-edit"></i></a>
                      </div>
                    </td>
                  </tr>
                  @php($c+=1)
                  @endforeach
                </tbody>
               </table>
              </div>
              <!-- /.card-body -->
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
          <table  id="tablaVentas" class="table table-responsive table-bordered table-sm table-hover table-striped ">
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
          <h5 class="modal-title">Detalle venta</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" class="white-text">×</span>
          </button>
        </div>
        <div id="frm" class="modal-body">
          <div class="embed-responsive embed-responsive-16by9">
            <iframe id="detalle_pdf_venta" class="embed-responsive-item" src="" allowfullscreen></iframe>
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
              <label for="descuento">Descuento</label>
              <input class="form-control" type="number" step="any" name="descuento" id="descuento" value="0">
            </div>
            <div class="col-sm-6">
              <label for="interes">Interes</label>
              <input class="form-control" type="number" step="any" name="interes" id="interes" value="0">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <a class="btn btn-sm btn-success" onclick="actualizar_nota_venta()" >Completar credito</a>
          <button type="button" class="btn btn-info btn-sm" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  @stop

  @section('js')


  <script >

  function completar_credito(id_venta,fecha,monto){
    $('#id_registro').val(id_venta);
    $('#monto_completar').val(monto+' bs');
    $('#fecha_completar').text(fecha);
    $('#completar_credito').modal('show');
  }
  function actualizar_nota_venta(){
    if($('#interes').val()!=null && $('#interes').val()!='' && $('#descuento').val()!=null && $('#descuento').val()!='' && $('#descuento').val()>=0 && $('#interes').val()>=0){
    var url='{{asset('')}}venta/cancelar_credito';
      $.ajax({
        url:url,dataType: 'json', 
        method: "post",
        data: {
            "interes"         : $('#interes').val(),
            "descuento"       : $('#descuento').val(),
            "id_venta"        : $('#id_registro').val(),
            "_token"          :"{{ csrf_token() }}",
        },
        success: function(resultado){
          if(resultado.error==0){
            toastr.success('El credito fue concluido.', 'Credito', {timeOut:1000}); 
            $('#completar_credito').modal('hide');
          //  $("#tablaVentas tbody").re();
          //  $("#tablaVentas tbody").append(resultado.fila);
           // $("#tablaVentas").DataTable().ajax.reload();
          }
        }
      });
    }else{
      mostrarerror('error!','error','por favor verifique los datos del descuento y interes');
    }
  }
  function Mostrar_notas_ventas(id_cliente,anio,mes) { 
    if(id_cliente !=''){
      if (anio != null && anio != 0 && mes != null && mes !=0 && mes<=12) {
            var url='{{asset('')}}venta/deuda_dias/'+id_cliente+'/'+anio+'/'+mes;
              $.ajax({
                  url:url,dataType: 'json', 
                  success: function(resultado){
                    
                      $("#tablaVentas tbody").empty();
                      $("#tablaVentas tbody").append(resultado.fila);
                      $('#modal_deudas').modal('show');
                     // $("#total").val(resultado.total);
                     //$("#id_producto").val('');
                      //$("#id_almacen").val('');
             
                  }
            });
      }else { mostrarerror('error!','error','por favor verifique los datos');}
    }else{mostrarerror('error!','error','Problemas de la pagina de espera recarge de nuevo');}
  }

  function mostrar_detalle(id_venta){
    var url='{{asset('')}}venta/ticket/'+id_venta ;
   // var url='{{asset('')}}informe_dia/'+fecha_inicio;
    $('#pdf_modal').modal('show');
    $('#detalle_pdf_venta').attr('src', url);
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