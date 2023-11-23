@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark">Plan pago</h1>
@stop

@section('content')
    
    <div class="row">
        <div class="col-12">
            <div class="card {{ config('adminlte.classes_index', '') }}">
                <div class="card-body ">
                    <h1 class="card-title {{ config('adminlte.classes_index_header', '') }} ">LISTA DE PAGOS</h1> 
                </div>
                <div class="card-header">
                    <table  id="example1" class="table table-responsive-xl table-bordered table-sm table-hover table-striped"  >
                      <thead>
                          <tr>  
                            <th width="10%"> Codigo </th>
                            <th width="24%">Descripcion</th> 
                            <th width="11%">Fecha</th>  
                            <th width="8%">Monto</th>
                            <th width="7%">Interes</th>
                            <th width="15%">Cliente</th>
                            <th width="15%">Empleado</th>
                            <th width="5%"><span class="badge bg-primary">Estado</span></th>
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

    <div class="modal fade" id="pdf_modal_plan" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;" data-gtm-vis-first-on-screen-2340190_1302="5621" data-gtm-vis-total-visible-time-2340190_1302="100" data-gtm-vis-has-fired-2340190_1302="1" aria-hidden="true">
      <div class="modal-dialog modal-xl" role="document">
        <!--Content-->
        <div class="modal-content">
          <!--Header-->
          <div class="modal-header {{ config('adminlte.classes_index_modal_agregar','') }}">
            <h5 class="modal-title">PDF CREDITO</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" class="white-text">×</span>
            </button>
          </div>
          <div id="frm" class="modal-body">
            <div class="embed-responsive embed-responsive-16by9">
              <iframe id="detalle_pdf_plan" class="embed-responsive-item" src="" allowfullscreen></iframe>
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
      ajax: "{{route('plan_pago.datos')}}",
      columns: [
          {data: 'codigo',searchable: true,orderable: false}, 
          {data: 'descripcion',searchable: false},
          {data: 'fecha',searchable: true},
          {data: 'monto_total2',searchable: false},
          {data: 'interes2',searchable: false},
          {data: 'cliente',searchable: false},
          {data: 'empleado',searchable: false},
          {data: 'estado',searchable: false},
          {data: 'actions',searchable: false,orderable: false}
      ],
    })
</script>
<script>
    

    function Cancelar(id) { 
      var link="{{asset('')}}"+"plan_pago/cancelar/"+id;
        Swal.fire({
            title: '¿Desea Cancelar el plan de pago?',
            text: "¡No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#58D68D',
            cancelButtonColor: '#d33',
            confirmButtonText: '!cancelar!',
            denyButtonColor: '#3498DB',
            showDenyButton: false,
          //  showCancelButton: true,
           // confirmButtonText: 'Save',
            denyButtonText: 'completar y imprimir',

        }).then((result) => {
              if (result.isConfirmed) {
                $.ajax({
                      url:link, dataType: 'json', 
                      success: function(resultado){
                        if(resultado.error==0){
                          $("#example1").DataTable().ajax.reload();
                        }else{
                          mostrarerror('error!','error',resultado.mensaje);
                        }
                         
                      }
                });
              }
      });
    }

    function Mostrar_pdf(id_plan){
      var url='{{asset('')}}plan_pago/pdf/'+id_plan ;
      
      $('#pdf_modal_plan').modal('show');
      $('#detalle_pdf_plan').attr('src', url);
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