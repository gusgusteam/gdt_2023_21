@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark">CAJAS</h1>
@stop

@section('content')

<div class="row">
  <div class="col-12">
      <div class="card {{ config('adminlte.classes_index', '') }}">
          <div class="card-body ">
              <h1 class="card-title {{ config('adminlte.classes_index_header', '') }} ">LISTA DE CAJAS</h1> 
          </div>
          <div class="card-header">
              <table id="example1" class="table table-responsive-xl table-bordered table-sm table-hover table-striped"  >
                <thead>
                    <tr>  
                      <th width="5%"> # </th>
                      <th width="10%">Nombre</th>
                      <th width="22%">Descripcion</th>
                      <th width="5%">Capital</th>
                      <th width="7.5%">Ingreso</th>
                      <th width="7.5%">Egreso</th>
                      <th width="15%">Fecha inicio</th>
                      <th width="15%">Fecha final</th>
                      <th width="8%"><span class="badge bg-primary">Estado</span></th>
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
      ajax: "{{route('caja.datos')}}",
      columns: [
          {data: 'nro_caja',searchable: false,orderable: false},
          {data: 'nombre',searchable: false},
          {data: 'descripcion',searchable: false},
          {data: 'capital',searchable: false},
          {data: 'ingreso',searchable: false},
          {data: 'egreso',searchable: false},
          {data: 'fecha_inicio',searchable: false},
          {data: 'fecha_final',searchable: false},
          {data: 'estado',searchable: false},
          {data: 'actions',searchable: false,orderable: false}
      ],
    })
</script>
<script>

  function cerrar_caja(id_caja){
      Swal.fire({
        title: '¿Está seguro cerrar la caja?',
        text: "¡La caja se cerrara y no podra realizar operaciones de venta y compra!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Si, Estoy seguro!'
      }).then((result) => {
        if (result.isConfirmed) {
          var link="{{asset('')}}"+"caja/cerrar/"+id_caja;
            $.ajax({
                url: link, dataType:'json',
                type: "GET",
                success:function(resultado){
                  if(resultado.error==0){
                    Swal.fire(
                    '¡Caja Cerrada!',
                    'Puede ver el informe hasta el cierre de caja.',
                    'De acuerdo'
                    ).then((result)=>{
                      $("#example1").DataTable().ajax.reload();
                    })
                  }
                  
                }
            })
        }
      })
    }

    function iniciar_caja(id_caja){
      Swal.fire({
        title: '¿Está seguro poner en curso la caja?',
        text: "¡Comensara de nuevo la caja y realizar operaciones de venta y compra!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Si, Estoy seguro!'
      }).then((result) => {
        if (result.isConfirmed) {
          var link="{{asset('')}}"+"caja/iniciar/"+id_caja;
            $.ajax({
                url: link, dataType:'json',
                type: "GET",
                success:function(resultado){
                  if(resultado.error==0){
                    Swal.fire(
                    '¡Caja Corriendo!',
                    'Puede realizar todas las operaciones de venta y compra.',
                    'De acuerdo'
                    ).then((result)=>{
                      $("#example1").DataTable().ajax.reload();
                      toastr.success(resultado.mensaje, 'Inicio Caja', {timeOut:4000}); 
                    })
                  }
                  
                }
            })
        }
      })
    }

   

    function informe(id_caja,tipo){
      var url='{{asset('')}}caja/informe_general/'+id_caja+'/'+tipo;
      $('#pdf_modal').modal('show');
      $('#informe_caja').attr('src', url);
    }
</script>

@stop