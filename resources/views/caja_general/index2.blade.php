@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark text-center">CAJA GENERAL</h1>
@stop

@section('content')
  <div class="container-fluid">
    <br>
    <div class="row">
      <div class="col-sm-10">
         <a class="btn btn-sm btn-dark" onclick="tipo_servicio(1)">Servicio Taller</a>
         <a class="btn btn-sm btn-dark" onclick="tipo_servicio(2)">Servicio Grua</a>
         <a class="btn btn-sm btn-dark" onclick="tipo_servicio(3)">Servicio Maquinaria</a>
         <a class="btn btn-sm btn-dark" onclick="tipo_servicio(4)">Servicio Labadero</a>
         <a class="btn btn-sm btn-dark" onclick="tipo_servicio(5)">Servicio Ganaderia</a>  
      </div>
    </div>
  </div>  
  <br>
  <div  class="row"> 
      <div id="panel" style="display:none" class="col-sm-6">
        <div class="card">
          <div class="card-header">
            <h4 class="title text-center"><b id="titulo">Elegir un servicio</b></h4>
          </div>
          <div class="card-body">
            <form id="frm_caja">
              <input type="hidden" id="tipo_general" name="tipo_general">
              <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="precio">Precio</label> 
                        <input step="any" class="form-control" id="precio" name="precio" type="number" placeholder="ingrese el precio"/>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="descripcion">Descripcion</label> 
                        <textarea class="form-control" id="descripcion" name="descripcion" type="text" placeholder="ingrese una descripcion" aria-describedby="descripcion-error" aria-invalid="true"></textarea>
                        <span id="descripcion-error" class="error invalid-feedback" style="display: none;"></span>
                    </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <div class="custom-control custom-switch">
                    <input class="custom-control-input" type="checkbox" id="modo_cliente" checked value ="0" name= "modo_cliente" onclick="ModoCliente(this.value)"/>
                    <label class="custom-control-label font-weight-normal" for="modo_cliente">publico general</label>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <div class="select2-primary">
                    <label for="id_cliente">Cliente</label> 
                    <select class="select2 select2-hidden-accessible" multiple="multiple" name="id_cliente" id="id_cliente" style="width: 100%;">
                      <option selected disabled value="">Elija un Cliente</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="id_empleado">Empleado</label> 
                        <select class="form-control" name="id_empleado" id="id_empleado">
                          <option selected disabled value="">Elija un Empleado</option>
                        </select>
                    </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="tipo_pago">Tipo pago</label> 
                        <select class="form-control" name="tipo_pago" id="tipo_pago">
                          <option selected value="1">Contado</option>
                          <option  value="0">Credito</option>
                        </select>
                    </div>
                </div>
                <div id="tipo_lab" style="display:none" class="col-sm-6">
                  <div class="form-group">
                      <label for="tipo_lab1">Tipo LAB</label> 
                      <select class="form-control" name="tipo_lab1" id="tipo_lab1">
                        <option selected value="1">Labadero</option>
                        <option  value="2">Carga agua</option>
                        <option  value="3">Balanza</option>
                      </select>
                  </div>
                </div>
              </div>
             
            </form>
          </div>
          <div class="card-footer">
            <a class="btn btn-sm btn-primary" onclick="guardar_servicio()">Guardar</a>
          </div>
            
        </div>
      </div>
      <div id="panel2" style="display:none" class="col-sm-6">
        <div class="card text-center">
          <div class="row">      
            <div class="col-sm-12">
              <div class="btn-group" role="group" aria-label="Basic example">
              <a class="btn btn-sm btn-success" onclick="agregar_ingreso()">Nuevo Ingreso</a>
              <a class="btn btn-sm btn-info" href="#">Nuevo Ingreso Caja</a>
              </div>
            </div>
          </div>
          <div class="card-header">
            <h4 class="title text-center"><b id="titulo2"></b></h4>
          </div>
          <div class="card-body">
            <table id="gasto" class="table table-responsive-xl table-bordered table-sm table-hover table-striped">
              <thead>
                <tr>  
                  <th width="5%"> codigo </th>
                  <th width="40%">Descripcion</th>
                  <th width="10%">Monto</th>
                  <th width="20%">Fecha</th>
                  <th width="10%">Caja</th>
                  <th width="10%"><span class="badge bg-primary">Estado</span></th>
                  <th width="5%">Acción</th>
                </tr>
              </thead> 
            </table>
          </div>
          <div class="card-footer">
            <a class="btn btn-sm btn-primary" href="#">Guardar</a>
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
          <h5 class="modal-title">Agregar Ingreso</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" class="white-text">×</span>
          </button>
        </div>
        <div id="frm_gasto" class="modal-body">          
          <div class="row">
              <div class="col-sm-6">
                  <div class="form-group">
                      <label for="monto">Monto</label> 
                      <input class="form-control" id="monto" name="monto" type="number" placeholder="ingrese el monto"/>
                  </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                    <label for="descripcion_ingreso">Descripcion</label> 
                    <textarea class="form-control" id="descripcion_ingreso" name="descripcion_ingreso" type="text" ></textarea>
                </div>
              </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="id_caja1">Caja primaria</label> 
                    <select class="form-control" name="id_caja1" id="id_caja1">
                      
                    </select>
                </div>
            </div>
            <div  class="col-sm-6">
              <div class="form-group">
                  <label for="id_caja2">Caja secundaria</label> 
                  <select class="form-control" name="id_caja2" id="id_caja2">
                    
                  </select>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
            <a type="button" onclick="guardar_ingreso()" name="next"  class="btn btn-success">Agregar</a>
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
    $('#gasto').DataTable({ 
      language: {url: languages['es']},
      destroy: true,
      retrieve: true,
      serverSide: true,
      autoWidth: false,
      responsive: r,
      ajax: "{{route('ingreso.datos',7)}}",
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
</script>
<script>

  function ModoCliente(value){
      if (value==1) {
        $("#modo_cliente").val("0"); 
      }else{
        $("#modo_cliente").val("1");  
      }
  }

  function agregar_ingreso(){
   
    $('#ingreso_agregar').modal("show");
    cargar_datos_caja();
  }

  function cargar_datos_caja(){
    $.ajax({
        url:"{{asset('')}}"+"caja/cajas_generales", dataType:'json',
        success: function(resultado){  
          //$('#id_cliente').select2();
          $('#id_caja1').empty();
          $('#id_caja2').empty();
          $('#id_caja1').append($('<option  />', {
              text: "seleccione una caja",
              value: -1,
              selected: true,
          }));  
          resultado.cajas.forEach(function(elemento, indice, array) {    
            $('#id_caja1').append($('<option  />', {
              text: elemento.nombre,
              value: elemento.id,
              selected: false,
            }));          
          });
          resultado.cajas.forEach(function(elemento, indice, array) {    
            $('#id_caja2').append($('<option  />', {
              text: elemento.nombre,
              value: elemento.id,
              selected: false,
            }));          
          });
          $('#id_caja1').val(7);
          $('#id_caja1').prop('disabled', true);

        }
      });
    
  }

  function guardar_ingreso() { 
     // var url='{{asset('')}}servicio_general/guardar';
     if($("#descripcion_ingreso").val().trim().length > 0
     && $("#monto").val() != '' && $("#descripcion_ingreso").val() != ''
     && $("#monto").val().trim().length > 0
    
     ){
          if($("#id_caja2").val() != '' && $("#id_caja2").val() != -1){
              
                let url='{{asset('')}}ingreso/guardar';

                $.ajax({
                  url: url,dataType: 'json',
                  type: "POST",
                  data: {
                  
                    "monto"             : $('#monto').val(),
                    "descripcion"        : $('#descripcion_ingreso').val(),
                    "id_caja_secundaria" : $('#id_caja2').val(),
                    "_token"             :"{{ csrf_token() }}",
                  },
                  success: function(resultado){
                    //var resultado= JSON.parse(resultado);
                    if(resultado.error==0){
                      toastr.success('Venta realizada correctamente','Completar servicio',{timeOut:0500});
                      limpiar_frm_gasto();
                      cargar_datos_caja();
                      $('#ingreso_agregar').modal("hide");
                    }else{
                      mostrarerror('error!','error',resultado.mensaje);
                    } 
                  },
                });
              
            
          }else{
            mostrarerror('error!','error',"se requiere la caja secundaria");
          }
      }else{
        mostrarerror('error!','error',"escribir sin espacios");
      }
    }


  function limpiar_frm(){
    document.getElementById("frm_caja").reset(); 
    $('#modo_cliente').val(0);
    $('#tipo_pago').val(1);
  }
  function limpiar_frm_gasto(){
    document.getElementById("frm_gasto").reset(); 
  }
  function tipo_servicio(opcion){
    limpiar_frm();
    $('#modo_cliente').val(0);
    $('#modo_cliente').prop('disabled', false);
    $('#modo_cliente').prop('disabled', false);
    if(opcion==1){
      //$('#panel').hide();
      //$('#precio_servicio').prop('disabled', true);
      $('#tipo_general').val(1);
      $('#titulo').text('Servicio de Taller');
      $('#id_empleado').prop('disabled', false);
      $('#id_cliente').prop('disabled', false);
      $('#tipo_lab').hide();
    }
    if(opcion==2){
      $('#tipo_general').val(2);
      $('#titulo').text('Servicio de Grua');
      $('#id_empleado').prop('disabled', false);
      $('#id_cliente').prop('disabled', false);
      $('#tipo_lab').hide();
    }
    if(opcion==3){
      $('#tipo_general').val(3);
      $('#titulo').text('Servicio de Maquinaria');
      $('#id_empleado').prop('disabled', false);
      $('#id_cliente').prop('disabled', false);
      $('#tipo_lab').hide();
    }
    if(opcion==4){
      $('#tipo_general').val(4);
      $('#titulo').text('Servicio de Labadero');
      $('#tipo_pago').prop('disabled', true);
      $('#modo_cliente').prop('disabled', true);
      $('#id_empleado').prop('disabled', true);
      $('#id_cliente').prop('disabled', true);
      $('#tipo_lab').show();
    }
    if(opcion==5){
      $('#tipo_general').val(5);
      $('#titulo').text('Servicio de Ganaderia');
      $('#id_empleado').prop('disabled', false);
      $('#id_cliente').prop('disabled', false);
      $('#tipo_lab').hide();
    }
    $('#panel').show();
    $('#id_cliente').select2();
    //$('#panel2').hide();
  }

  function tipo_gasto(opcion){
    if(opcion==1){
      $('#titulo2').text('INGRESOS');  
    }
    if(opcion==0){
      $('#titulo2').text('EGRESOS');
      
    }
    $('#panel2').show();
   // $('#panel').hide();
  }

  cargar_clientes_empleados();
  function cargar_clientes_empleados(){
    $.ajax({
        url:"{{asset('')}}"+"general/datos", dataType:'json',
        success: function(resultado){  
          //$('#id_cliente').select2();
          $('#id_cliente').empty();
          $('#id_empleado').empty();
          $('#id_empleado').append($('<option  />', {
              text: "seleccione un empleado",
              value: -1,
              selected: true,
          }));  
          resultado.clientes.forEach(function(elemento, indice, array) {    
            $('#id_cliente').append($('<option  />', {
              text: elemento.nombre + ' '+elemento.apellidos,
              value: elemento.id,
              selected: false,
            }));          
          });
          resultado.empleados.forEach(function(elemento, indice, array) {    
            $('#id_empleado').append($('<option  />', {
              text: elemento.nombre + ' '+elemento.apellidos,
              value: elemento.id,
              selected: false,
            }));          
          });
        }
      });
    
  }

  
    function guardar_servicio() { 
     // var url='{{asset('')}}servicio_general/guardar';
     if($("#precio").val().trim().length > 0 
     && $("#descripcion").val().trim().length > 0
     && $("#tipo_pago").val() != ''
     && $("#tipo_general").val().trim().length > 0
    
     ){
        if($("#modo_cliente").val() != '' && ($("#modo_cliente").val()==0 || ($("#modo_cliente").val()==1 && $("#id_cliente").val() != '' && $("#id_cliente").val() != -1  )) ){
          if(($("#id_empleado").val() != '' && $("#id_empleado").val() != -1 ) 
             || ($("#tipo_general").val()==4 &&  $("#tipo_lab1").val() != '' && $("#tipo_lab1").val() != -1 )){
              
                let url='{{asset('')}}general/guardar';

                $.ajax({
                  url: url,dataType: 'json',
                  type: "POST",
                  data: {
                    "tipo_pago"          : $('#tipo_pago').val(),
                    "modo_cliente"       : $('#modo_cliente').val(),
                    "tipo_general"       : $('#tipo_general').val(),
                    "precio"             : $('#precio').val(),
                    "id_cliente"         : $('#id_cliente').val(),
                    "id_empleado"        : $('#id_empleado').val(),
                    "descripcion"        : $('#descripcion').val(),
                    "tipo_lab"          : $('#tipo_lab1').val(),
                    "_token"             :"{{ csrf_token() }}",
                  },
                  success: function(resultado){
                    //var resultado= JSON.parse(resultado);
                    if(resultado.error==0){
                      toastr.success('Servicio realizado correctamente','Completar servicio',{timeOut:0500});
                      limpiar_frm();
                      cargar_clientes_empleados();
                    }else{
                      mostrarerror('error!','error',resultado.mensaje);
                    } 
                  },
                });
              
            
          }else{
            if($("#tipo_general").val()!=4){
              mostrarerror('error!','error',"se requiere datos de empleado y cliente obligatoriamente");
            }else{
              if($("#tipo_lab1").val() == '' || $("#tipo_lab1").val() == -1){
                mostrarerror('error!','error',"se requiere el tipo de LAB");
              }
            }
          }
        }else{
          if($("#modo_cliente").val()==1 && ($("#id_cliente").val() == '' || $("#id_cliente").val() == -1)){
            mostrarerror('error!','error',"si deshabilita el publico en general tiene que seleccionar un cliente obligatoriamente");
          }
        }
      }else{
        mostrarerror('error!','error',"escribir sin espacios");
      }
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
