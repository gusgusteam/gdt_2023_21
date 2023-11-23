@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark text-uppercase">{{$caja->nombre}}</h1>
@stop

@section('content')

<div class="row mb-2">

  <div class="col-4">
    
      <div class="text-left">
        @if ( Auth::user()->hasRole('Administrador'))
          <a onclick="agregar_ingreso_varios()" class="btn btn-primary btn-sm small-box-footer">&nbsp;Ingreso varios <i class="fas fa-arrow-circle-right"></i></a>
        @endif
        <a onclick="ingresos_cargar()" class="btn btn-dark btn-sm small-box-footer">&nbsp;INGRESOS</a>
      </div>
   
  </div>
  <div class="col-4 ">
    @if ( Auth::user()->hasRole('Administrador'))
      <div class="text-center">
        @if ($caja->id!=7)
          <a onclick="agregar_ingreso()" class="btn btn-danger btn-sm small-box-footer">&nbsp;Pasar a caja general <i class="fas fa-arrow-circle-right"></i></a>          
        @endif
      </div>
    @endif
  </div>
  <div class="col-4 ">
    @if ( Auth::user()->hasRole('Administrador'))
      <div class="text-right">
        <a onclick="agregar_egreso_caja()" class="btn btn-primary btn-sm small-box-footer">&nbsp;Egreso de caja<i class="fas fa-arrow-circle-right"></i></a>     
      </div>
    @endif
  </div> 
</div>
<div class="row mb-2">
  <div class="col-sm-3">
    <div class="text-left">
      <div class="info-box mb-3">
        <span class="info-box-icon bg-info elevation-1"><i class="fas fa-thumbs-up"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Monto trabajo generado</span>
          <span class="info-box-number">{{$caja->monto_total_generado}} bs</span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-2">
    <div class="text-left">
      <div class="info-box mb-3">
        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-thumbs-up"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Monto capital</span>
          <span class="info-box-number">{{$caja->monto_ingreso}} bs</span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-2">
    <div class="text-left">
      <div class="info-box mb-3">
        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-thumbs-up"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Monto egresos</span>
          <span class="info-box-number">{{$caja->monto_egreso}} bs</span>
        </div>
      </div>
    </div>
  </div> 
</div>

<div class="row">
  <div class="col-12">
      <div class="card {{ config('adminlte.classes_index', '') }}">
          <div class="card-body ">
              <h1 class="card-title {{ config('adminlte.classes_index_header', '') }} ">LISTA DE EGRESOS CAJA GENERAL</h1> 
          </div>
          <div class="card-header">
              <table id="example1" class="table table-responsive-xl table-bordered table-sm table-hover table-striped"  >
                <thead>
                    <tr>  
                      <th width="5%"> # </th>
                      <th width="42%">Descripcion</th>
                      <th width="10%">Monto total</th>
                      <th width="15%">Fecha</th>
                      <th width="15%">Caja destino</th>
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
<div class="row">
  <div class="col-12">
      <div class="card {{ config('adminlte.classes_index', '') }}">
          <div class="card-body ">
              <h1 class="card-title {{ config('adminlte.classes_index_header', '') }} text-uppercase ">LISTA DE EGRESOS {{$caja->nombre}}</h1> 
          </div>
          <div class="card-header">
              <table id="example2" class="table table-responsive-xl table-bordered table-sm table-hover table-striped"  >
                <thead>
                    <tr>  
                      <th width="5%"> # </th>
                      <th width="42%">Descripcion</th>
                      <th width="10%">Monto total</th>
                      <th width="15%">Fecha</th>
                      <th width="15%">Tipo</th>
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

<div class="modal fade" id="ingreso_agregar" tabindex="-1" aria-labelledby="myModalLabel"  data-gtm-vis-first-on-screen-2340190_1302="5621" data-gtm-vis-total-visible-time-2340190_1302="100" data-gtm-vis-has-fired-2340190_1302="1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <!--Content-->
    <div class="modal-content">
      <!--Header-->
      <div class="modal-header {{ config('adminlte.classes_index_modal_agregar','') }}">
        <h5 id="modal_titulo" class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="white-text">×</span>
        </button>
      </div>
      <div  class="modal-body"> 
        <input id="id_caja_secundaria" type="hidden"  value="{{$caja->id}}"> 
        <form id="frm_gasto">         
          <div class="row">
              <div class="col-sm-6">
                  <div class="form-group">
                      <label for="monto">Monto</label> 
                      <input step="any" class="form-control" id="monto" name="monto" type="number" placeholder="ingrese el monto"/>
                  </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                    <label for="descripcion_ingreso">Descripcion</label> 
                    <textarea class="form-control" id="descripcion_ingreso" name="descripcion_ingreso" type="text" ></textarea>
                </div>
              </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
          <a type="button" onclick="guardar_ingreso()" name="next"  class="btn btn-success">Agregar</a>
          <a type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</a>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="ingreso_agregar2" tabindex="-1" aria-labelledby="myModalLabel"  data-gtm-vis-first-on-screen-2340190_1302="5621" data-gtm-vis-total-visible-time-2340190_1302="100" data-gtm-vis-has-fired-2340190_1302="1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <!--Content-->
    <div class="modal-content">
      <!--Header-->
      <div class="modal-header {{ config('adminlte.classes_index_modal_agregar','') }}">
        <h5 id="modal_titulo2" class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="white-text">×</span>
        </button>
      </div>
      <div  class="modal-body"> 
        <input id="id_caja_secundaria2" type="hidden"  value="{{$caja->id}}"> 
        <form id="frm_gasto2">         
          <div class="row">
              <div class="col-sm-6">
                  <div class="form-group">
                      <label for="monto2">Monto</label> 
                      <input step="any" class="form-control" id="monto2" name="monto2" type="number" placeholder="ingrese el monto"/>
                  </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                    <label for="descripcion_ingreso2">Descripcion</label> 
                    <textarea class="form-control" id="descripcion_ingreso2" name="descripcion_ingreso2" type="text" ></textarea>
                </div>
              </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="tipo_ingreso">Tipo ingreso</label> 
                    <select class="form-control" name="tipo_ingreso" id="tipo_ingreso">
                        <option selected value="VARIOS">VARIOS</option>
                        <option value="VENTA">VENTA</option> 
                        <option value="COMPRA">COMPRA</option>
                    </select>
                </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
          <a type="button" onclick="guardar_ingreso2()"  class="btn btn-success">Agregar</a>
          <a type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="egreso_agregar" tabindex="-1" aria-labelledby="myModalLabel"  data-gtm-vis-first-on-screen-2340190_1302="5621" data-gtm-vis-total-visible-time-2340190_1302="100" data-gtm-vis-has-fired-2340190_1302="1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <!--Content-->
    <div class="modal-content">
      <!--Header-->
      <div class="modal-header {{ config('adminlte.classes_index_modal_agregar','') }}">
        <h5 id="modal_titulo3" class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="white-text">×</span>
        </button>
      </div>
      <div  class="modal-body"> 
        <input id="id_caja_secundaria3" type="hidden"  value="{{$caja->id}}"> 
        <form id="frm_gasto2">         
          <div class="row">
              <div class="col-sm-6">
                  <div class="form-group">
                      <label for="monto3">Monto</label> 
                      <input step="any" class="form-control" id="monto3" name="monto3" type="number" placeholder="ingrese el monto"/>
                  </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                    <label for="descripcion_egreso">Descripcion</label> 
                    <textarea class="form-control" id="descripcion_egreso" name="descripcion_egreso" type="text" ></textarea>
                </div>
              </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="tipo_egreso">Tipo egreso</label> 
                    <select class="form-control" name="tipo_egreso" id="tipo_egreso">
                        <option selected value="VARIOS">VARIOS</option>
                        <option value="MATERIALES">MATERIALES</option> 
                        <option value="REPUESTOS">REPUESTOS</option>
                        <option value="SUELDO">SUELDO</option>
                        <option value="REFRIGERIO">REFRIGERIO</option>
                        <option value="OTRO">OTRO..</option>
                    </select>
                </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
          <a type="button" onclick="guardar_egreso()"   class="btn btn-success">Agregar</a>
          <a type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="ingresos_detalle" tabindex="-1" aria-labelledby="myModalLabel"  data-gtm-vis-first-on-screen-2340190_1302="5621" data-gtm-vis-total-visible-time-2340190_1302="100" data-gtm-vis-has-fired-2340190_1302="1" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <!--Content-->
    <div class="modal-content">
      <!--Header-->
      <div class="modal-header {{ config('adminlte.classes_index_modal_agregar','') }}">
        <h5 id="modal_titulo3" class="modal-title">INGRESOS DETALLE</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="white-text">×</span>
        </button>
      </div>
      <div  class="modal-body"> 
        <table id="example3" class="table table-responsive-xl table-bordered table-sm table-hover table-striped"  >
          <thead>
              <tr>  
                <th width="5%"> # </th>
                <th width="42%">Descripcion</th>
                <th width="10%">Monto </th>
                <th width="15%">Fecha</th>
                <th width="15%">Origen</th>
                <th width="8%"><span class="badge bg-primary">Estado</span></th>
                <th width="5%">Acción</th>
              </tr>
          </thead>  
          <tbody>
            
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
          
          <a type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</a>
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
      ajax: "{{route('ingreso.datos_caja',$caja->id)}}",
      columns: [
        {data: 'codigo',searchable: true,orderable: false},
        {data: 'descripcion',searchable: true},
        {data: 'monto',searchable: true},
        {data: 'fecha_hora',searchable: false},
        {data: 'caja',searchable: false},
        {data: 'estado',searchable: false},
        
        {data: 'actions',searchable: false,orderable: false}
      ],
    })

    $('#example2').DataTable({ 
      language: {url: languages['es']},
      destroy: true,
      retrieve: true,
      serverSide: true,
      autoWidth: false,
      responsive: r,
      ajax: "{{route('egreso.datos',$caja->id)}}",
      columns: [
        {data: 'codigo',searchable: true,orderable: false},
        {data: 'descripcion',searchable: true},
        {data: 'monto',searchable: true},
        {data: 'fecha_hora',searchable: false},
        {data: 'caja',searchable: false},
        {data: 'estado',searchable: false},
        
        {data: 'actions',searchable: false,orderable: false}
      ],
    })

    function ingresos_cargar(){
      
      
    $('#ingresos_detalle').modal("show");

    $('#example3').DataTable({ 
      language: {url: languages['es']},
      destroy: true,
      retrieve: true,
      serverSide: true,
      autoWidth: false,
      responsive: r,
      ajax: "{{route('ingreso.datos',$caja->id)}}",
      columns: [
        {data: 'codigo',searchable: false,orderable: false},
        {data: 'descripcion',searchable: false},
        {data: 'monto',searchable: true},
        {data: 'fecha_hora',searchable: false},
        {data: 'caja',searchable: false},
        {data: 'estado',searchable: false},
        {data: 'actions',searchable: false,orderable: false}
      ],
    })
    $("#example3").DataTable().ajax.reload();
    }
</script>
<script>

function agregar_ingreso(){
   $('#modal_titulo').text('AGREGAR INGRESO CAJA GENERAL');
   $('#ingreso_agregar').modal("show");
  // cargar_datos_caja();
}
function agregar_ingreso_varios(){
   $('#modal_titulo2').text('AGREGAR INGRESO VARIOS');
   $('#ingreso_agregar2').modal("show");
  // cargar_datos_caja();
}
function agregar_egreso_caja(){
   $('#modal_titulo3').text('AGREGAR EGRESO');
   $('#egreso_agregar').modal("show");
  // cargar_datos_caja();
}

function guardar_ingreso() { 
     // var url='{{asset('')}}servicio_general/guardar';
     if($("#descripcion_ingreso").val().trim().length > 0
     && $("#monto").val() != '' && $("#descripcion_ingreso").val() != ''
     && $("#monto").val().trim().length > 0 && $("#monto").val()>=0
     ){       
        let url='{{asset('')}}ingreso/guardar/1';
        $.ajax({
          url: url,dataType: 'json',
          type: "POST",
          data: {
            "monto"              : $('#monto').val(),
            "descripcion"        : $('#descripcion_ingreso').val(),
            "id_caja_secundaria" : $('#id_caja_secundaria').val(),
            "_token"             :"{{ csrf_token() }}",
          },
          success: function(resultado){
            //var resultado= JSON.parse(resultado);
            if(resultado.error==0){
              toastr.success('Ingreso realizada correctamente','Completar ingreso',{timeOut:0500});
              limpiar_frm_gasto();
             // cargar_datos_caja();
              $('#ingreso_agregar').modal('hide');
              $("#example1").DataTable().ajax.reload();
            }else{
              toastr.error(resultado.mensaje,'Completar ingreso',{timeOut:3000});
             // mostrarerror('error!','error',);
            } 
          },
        });        
      }else{
        if($("#descripcion_ingreso").val().trim().length <= 0){
          mostrarerror('error!','error',"la descripcion es obligatoria");
        }else{
          if($("#descripcion_ingreso").val() == '' || $("#monto").val() == ''){
            if($("#descripcion_ingreso").val() == ''){
              mostrarerror('error!','error',"corrija el campo de descripcion");
            }
            if($("#monto").val() == ''){
              mostrarerror('error!','error',"corrija el campo de monto");
            }    
          }else{
            if($("#monto").val().trim().length <= 0){
              mostrarerror('error!','error',"el monto es obligatoria");
            }else{
              if($("#monto").val() < 0){
                mostrarerror('error!','error',"el monto NO PUEDE SER NEGATIVO");
              }
            }
          }
        }
      }
}

function guardar_ingreso2() { 
     // var url='{{asset('')}}servicio_general/guardar';
     if($("#descripcion_ingreso2").val().trim().length > 0
     && $("#monto2").val() != '' && $("#descripcion_ingreso2").val() != ''
     && $("#monto2").val().trim().length > 0 && $("#monto2").val()>=0 
     && $("#tipo_ingreso").val() != ''
     && $("#tipo_ingreso").val().trim().length > 0

     ){       
        let url='{{asset('')}}ingreso/guardar/0';
        $.ajax({
          url: url,dataType: 'json',
          type: "POST",
          data: {
            "monto"              : $('#monto2').val(),
            "descripcion"        : $('#descripcion_ingreso2').val(),
            "id_caja_secundaria" : $('#id_caja_secundaria2').val(),
            "tipo_ingreso"       : $('#tipo_ingreso').val(),
            "_token"             :"{{ csrf_token() }}",
          },
          success: function(resultado){
            //var resultado= JSON.parse(resultado);
            if(resultado.error==0){
              toastr.success('Ingreso realizada correctamente','Completar ingreso',{timeOut:0500});
              limpiar_frm_gasto2();
             // cargar_datos_caja();
              $('#ingreso_agregar2').modal('hide');
              $("#example1").DataTable().ajax.reload();
            }else{
              toastr.error(resultado.mensaje,'Completar ingreso',{timeOut:3000});
             // mostrarerror('error!','error',);
            } 
          },
        });        
      }else{
        if($("#tipo_ingreso").val() == ''){
          mostrarerror('error!','error',"el tipo es requerido y necesario");
        }
        if($("#descripcion_ingreso2").val().trim().length <= 0){
          mostrarerror('error!','error',"la descripcion es obligatoria");
        }else{
          if($("#descripcion_ingreso2").val() == '' || $("#monto2").val() == ''){
            if($("#descripcion_ingreso2").val() == ''){
              mostrarerror('error!','error',"corrija el campo de descripcion");
            }
            if($("#monto2").val() == ''){
              mostrarerror('error!','error',"corrija el campo de monto");
            }    
          }else{
            if($("#monto2").val().trim().length <= 0){
              mostrarerror('error!','error',"el monto es obligatoria");
            }else{
              if($("#monto2").val() < 0){
                mostrarerror('error!','error',"el monto NO PUEDE SER NEGATIVO");
              }
            }
          }
        }
      }
}



function guardar_egreso() { 
     // var url='{{asset('')}}servicio_general/guardar';
     if($("#descripcion_egreso").val().trim().length > 0
     && $("#monto3").val() != '' && $("#descripcion_egreso").val() != ''
     && $("#monto3").val().trim().length > 0 && $("#monto3").val()>=0 
     && $("#tipo_egreso").val() != ''
     && $("#tipo_egreso").val().trim().length > 0

     ){       
        let url='{{asset('')}}egreso/guardar';
        $.ajax({
          url: url,dataType: 'json',
          type: "POST",
          data: {
            "monto"              : $('#monto3').val(),
            "descripcion"        : $('#descripcion_egreso').val(),
            "id_caja_secundaria" : $('#id_caja_secundaria3').val(),
            "tipo_egreso"       : $('#tipo_egreso').val(),
            "_token"             :"{{ csrf_token() }}",
          },
          success: function(resultado){
            //var resultado= JSON.parse(resultado);
            if(resultado.error==0){
              toastr.success('Ingreso realizada correctamente','Completar egreso',{timeOut:0500});
              limpiar_frm_gasto2();
             // cargar_datos_caja();
              $('#egreso_agregar').modal('hide');
              $("#example2").DataTable().ajax.reload();
            }else{
              toastr.error(resultado.mensaje,'Completar egreso',{timeOut:3000});
             // mostrarerror('error!','error',);
            } 
          },
        });        
      }else{
        if($("#tipo_egreso").val() == ''){
          mostrarerror('error!','error',"el tipo es requerido y necesario");
        }
        if($("#descripcion_egreso").val().trim().length <= 0){
          mostrarerror('error!','error',"la descripcion es obligatoria");
        }else{
          if($("#descripcion_egreso").val() == '' || $("#monto3").val() == ''){
            if($("#descripcion_egreso").val() == ''){
              mostrarerror('error!','error',"corrija el campo de descripcion");
            }
            if($("#monto3").val() == ''){
              mostrarerror('error!','error',"corrija el campo de monto");
            }    
          }else{
            if($("#monto3").val().trim().length <= 0){
              mostrarerror('error!','error',"el monto es obligatoria");
            }else{
              if($("#monto3").val() < 0){
                mostrarerror('error!','error',"el monto NO PUEDE SER NEGATIVO");
              }
            }
          }
        }
      }
}


function limpiar_frm_gasto(){
    document.getElementById("frm_gasto").reset(); 
}
function limpiar_frm_gasto2(){
    document.getElementById("frm_gasto2").reset(); 
}

function mostrarerror(title,icono,error){
    Swal.fire({
      title: title,
      text: error,
      icon: icono,
      confirmButtonText: 'okey'
    })
}

function Eliminar_ingreso_propio(id){
      Swal.fire({
        title: '¿Está seguro?',
        text: "¡El ingreso cambiara a estado cancelado!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Si, Estoy seguro!'
      }).then((result) => {
        if (result.isConfirmed) {
          var link="{{asset('')}}"+"ingreso/eliminar/"+id;
            $.ajax({
                url: link, dataType: 'json',
                type: "GET",
                success:function(resultado){
                  if(resultado.error==0){
                    $("#example3").DataTable().ajax.reload();
                    toastr.success('cancelado exitosamente','Cancelar ingreso',{timeOut:2000});
                  }else{
                    mostrarerror('error!','error',resultado.mensaje);
                  }
                }
            })
          
        }
      })
  }

function Eliminar_ingreso(id){
      Swal.fire({
        title: '¿Está seguro?',
        text: "¡El ingreso cambiara a estado cancelado!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Si, Estoy seguro!'
      }).then((result) => {
        if (result.isConfirmed) {
          var link="{{asset('')}}"+"ingreso/eliminar/"+id;
            $.ajax({
                url: link, dataType: 'json',
                type: "GET",
                success:function(resultado){
                  if(resultado.error==0){
                    $("#example1").DataTable().ajax.reload();
                    toastr.success('cancelado exitosamente','Cancelar ingreso',{timeOut:2000});
                  }else{
                    mostrarerror('error!','error',resultado.mensaje);
                  }
                }
            })
          
        }
      })
  }

  function Eliminar_egreso(id){
      Swal.fire({
        title: '¿Está seguro?',
        text: "¡El egreso cambiara a estado cancelado!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Si, Estoy seguro!'
      }).then((result) => {
        if (result.isConfirmed) {
          var link="{{asset('')}}"+"egreso/eliminar/"+id;
            $.ajax({
                url: link, dataType: 'json',
                type: "GET",
                success:function(resultado){
                  if(resultado.error==0){
                    $("#example2").DataTable().ajax.reload();
                    toastr.success('cancelado exitosamente','Cancelar egreso',{timeOut:2000});
                  }else{
                    mostrarerror('error!','error',resultado.mensaje);
                  }
                }
            })
          
        }
      })
  }

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

    function informe(id_caja,fecha_inicial,fecha_final){
      var url='{{asset('')}}caja/informe/'+id_caja+'/'+ fecha_inicial+'/'+fecha_final;
      $('#pdf_modal').modal('show');
      $('#informe_caja').attr('src', url);
    }

    function informe_servicios(id_caja){
      var url='{{asset('')}}caja/informe_servicios/'+id_caja;
      $('#pdf_modal').modal('show');
      $('#informe_caja').attr('src', url);
    }
</script>

@stop