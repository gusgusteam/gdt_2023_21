@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark">Ventas</h1>
@stop

@section('content')
    
    <div class="row mb-2">
        <div class="col-12">
            <div class="card {{ config('adminlte.classes_index', '') }}">
                <div class="card-body ">
                    <h1 class="card-title {{ config('adminlte.classes_index_header', '') }} ">LISTA DE VENTAS REALIZADAS</h1> 
                </div>
                <div class="card-header">
                    <table id="example1" class="table table-responsive-xl table-bordered table-sm table-hover table-striped"  >
                      <thead>
                          <tr>  
                            <th width="10%"> # Codigo </th>
                            <th width="10%">Monto total</th>
                            <th width="5%">Descuento</th>
                            <th width="5%">Interes</th>
                            <th width="15%">Fecha y hora</th>
                            <th width="20%">Cliente</th>
                            <th width="20%">Empleado</th>
                            <th width="5%"><span class="badge bg-primary">Estado</span></th>
                            <th width="5%"><span class="badge bg-dark">Tipo</span></th>
                            <th width="5%">Acción</th>
                          </tr>
                      </thead>  
                      <tbody>
                       
                      </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tabla_detalle" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;" data-gtm-vis-first-on-screen-2340190_1302="5621" data-gtm-vis-total-visible-time-2340190_1302="100" data-gtm-vis-has-fired-2340190_1302="1" aria-hidden="true">
      <div class="modal-dialog modal-xl" role="document">
        <!--Content-->
        <div class="modal-content">
          <!--Header-->
          <div class="modal-header {{ config('adminlte.classes_index_modal_agregar','') }}">
            <h5 class="modal-title">Detalle de venta</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" class="white-text">×</span>
            </button>
          </div>
          <div id="frm" class="modal-body">
            <table  id="tabla_detalle" class="table table-responsive-sm table-bordered table-sm table-hover table-striped ">
              <thead>
                <tr>  
                  <th width="5%"> # </th>
                  <th width="20%">Producto</th>
                  <th width="20%">Almacen</th>
                  <th width="15%">Cantidad</th>
                  <th width="15%">Precio venta</th>
                  <th width="25%">Sub Total</th>
                </tr>
              </thead>
              <tbody>
                
              </tbody>
          </table>
          </div>
          <div class="modal-footer"> 
            <label style="font-weight: bold; font-size: 30px; text-align: center;">Total Bs.</label>
            <input type="text" id="total" name="total" size="6" readonly="true" value="0.00" style="font-weight: bold; font-size: 30px; text-align: center;"/>
            <button type="button" class="btn btn-info btn-sm" data-dismiss="modal">Cerrar detalle</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="pdf_modal_venta" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;" data-gtm-vis-first-on-screen-2340190_1302="5621" data-gtm-vis-total-visible-time-2340190_1302="100" data-gtm-vis-has-fired-2340190_1302="1" aria-hidden="true">
      <div class="modal-dialog modal-xl" role="document">
        <!--Content-->
        <div class="modal-content">
          <!--Header-->
          <div class="modal-header {{ config('adminlte.classes_index_modal_agregar','') }}">
            <h5 class="modal-title">PDF VENTA</h5>
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

@stop

@section('js')

<script>
  var r=false;
  if({{session()->has('responsivo')}}){
    if({{session('responsivo')}}==1){
      r=true;
    }
  }
  var languages = {'es': '{{asset('vendor/adminlte/Spanish.json')}}'};
    $('#example1').DataTable({ 
      language: {url: languages['es']},
      destroy: true,
      retrieve: true,
      serverSide: true,
      autoWidth: false,
      responsive: r,
      ajax: "{{route('venta.datos')}}",
      columns: [
          {data: 'codigo',searchable: true,orderable: false},
          {data: 'monto',searchable: true},
          {data: 'descuento_monto',searchable: false},
          {data: 'interes_monto',searchable: false},
          {data: 'fecha_hora',searchable: true,orderable: false},
          {data: 'usuario',searchable: false},
          {data: 'empleado',searchable: false},
          {data: 'estado',searchable: false,orderable: false},
          {data: 'tipo',searchable: false,orderable: false},
          {data: 'actions',searchable: false,orderable: false}
      ],
    })
</script>
<script>

    function mostrar_detalle(id_venta){
      var url='{{asset('')}}venta/detalle/'+id_venta;
        $.ajax({
             url: url,dataType: 'json',
            success: function(resultado){
              $("#tabla_detalle tbody").empty();
              $("#tabla_detalle tbody").append(resultado.datos);
              $("#total").val((resultado.total).toFixed(2));
              $('#tabla_detalle').modal('show');
              //  document.getElementById('cantidad').disabled=false;
            },
        });
    }
    
    function mostrar_detalle_venta(id_venta){
      var url='{{asset('')}}venta/ticket/'+id_venta ;
      $('#pdf_modal_venta').modal('show');
      $('#detalle_pdf_venta').attr('src', url);
    }
  
    function Eliminar(id){
      Swal.fire({
        title: '¿Está seguro?',
        text: "¡La compra se canselara y tendra menos en el inventario!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Si, Estoy seguro!'
      }).then((result) => {
        if (result.isConfirmed) {
          var link="{{asset('')}}"+"venta/destroy/"+id;
            $.ajax(     {
                url: link,dataType: 'json',
                type: "GET",
                cache: false,
                async: false,
                success:function(resultado){
                  if(resultado.error==0){
                    Swal.fire(
                      '¡Cancelando!',
                      'Su venta ha sido cancelado.',
                      'De acuerdo'
                    ).then((result)=>{
                      $("#example1").DataTable().ajax.reload();
                    })
                  }else{
                    mostrarerror('Error de cancelacion','error',resultado.mensaje);
                  }
                }
            })  
        }
      })
    }

    function mostrarerror(title,icono,error){
      Swal.fire({
        title: title,
        text: error,
        icon: icono,
        confirmButtonText: 'okey'
      })
    // Toast.fire({icon: 'error',title: error});
    }





   
   
</script>
    
@stop