@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark text-center">SERVICIOS DE CAJA</h1>
@stop

@section('content')
<div class="row mb-2">
  <div class="col-sm-2">
    <div class="btn-group-vertical">
      <button type="button" onclick="caja_servicio(1)" class="btn btn-default">Servicio Taller</button>
      <button type="button" onclick="caja_servicio(2)" class="btn btn-default">Servicio Grua</button>
      <button type="button" onclick="caja_servicio(3)" class="btn btn-default">Servicio Maquinaria</button>
      <button type="button" onclick="caja_servicio(4)" class="btn btn-default">Servicio Labadero</button>
      <button type="button" onclick="caja_servicio(5)" class="btn btn-default">Servicio Ganaderia</button>
    </div>
  </div>
  <div class="col-sm-10">
    <div id="card_servicio" class="card card-light">
      <div class="card-header">
        <h3 id="titulo" class="card-title text-light">SERVICIO DE CAJA</h3>
      </div>
      <form id="frm_caja">
        <div class="card-body">
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
                        <textarea rows="4" class="form-control" id="descripcion" name="descripcion" type="text" placeholder="ingrese una descripcion" aria-describedby="descripcion-error" aria-invalid="true"></textarea>
                    </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <div class="custom-control custom-switch">
                    <input class="custom-control-input" type="checkbox" id="modo_cliente" checked value ="0" name= "modo_cliente" onchange="ModoCliente(this.value)"/>
                    <label class="custom-control-label font-weight-normal" for="modo_cliente">publico general</label>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <div class="select2-primary">
                    <label for="id_cliente">Cliente</label> 
                    <select disabled class="select2 select2-hidden-accessible" multiple="multiple" name="id_cliente" id="id_cliente" style="width: 100%;">
                    </select>
                  </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="id_empleado">Empleado</label> 
                        <select class="form-control" name="id_empleado" id="id_empleado">
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
        </div>
        <div class="card-footer">
          <button type="button" onclick="completar_trabajo()" class="btn btn-primary">Completar</button>
        </div>
      </form>
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

  $("#tipo_pago").on("click",function(){
     // var otro=$("#otro").val();   
     // 0 publico general 1 cliente
     // 1 contado 0 credito
     $("#id_cliente").trigger("change");
      var valor=$(this).val()//obtenemos el valor seleccionado en una variable
      if(valor==0){
        $('#id_cliente').prop('disabled', false);  
        $("#modo_cliente").val("1");
        $("#modo_cliente").prop("checked", false); 
        $('#modo_cliente').prop('disabled', true);
      }
      if(valor==1){
        $("#modo_cliente").val("0");
        $('#id_cliente').prop('disabled', true);
        $('#modo_cliente').prop('disabled', false);
       
        $("#modo_cliente").prop("checked", true); 
      }
    //  console.log( valorSelect+otro)
  })




  function ModoCliente(value){
      if (value==1) {
        $("#modo_cliente").val("0");
        $('#id_cliente').prop('disabled', true);
       // $("#modo_cliente").val("1");
       // $("#modo_cliente").prop("checked", false); 
       // $('#modo_cliente').prop('disabled', true); 
      }else{
        $("#modo_cliente").val("1");  
        $('#id_cliente').prop('disabled', false);
      }
  }
  
    function caja_servicio(opcion){
    limpiar_frm();
    $("#modo_cliente").val("0");
    $('#id_cliente').prop('disabled', true);
    $("#modo_cliente").prop("checked", true); 
    $('#modo_cliente').prop('disabled', false);
    $('#id_empleado').prop('disabled', false);
    //$('#id_cliente').prop('disabled', false);
    $('#tipo_pago').prop('disabled', false);
    $("#tipo_pago").val("1");
    $("#id_cliente").trigger("change");
    $('#tipo_lab').hide();
    
    if(opcion==1){
      $('#tipo_general').val(2);
      $('#titulo').text('SERVICIO DE TALLER');
      $("#card_servicio").removeClass();
      $("#card_servicio").addClass("card card-danger");
    }
    if(opcion==2){
      $('#tipo_general').val(3);
      $('#titulo').text('SERVICIO DE GRUA');
      $("#card_servicio").removeClass();
      $("#card_servicio").addClass("card card-warning");
    }
    if(opcion==3){
      $('#tipo_general').val(4);
      $('#titulo').text('SERVICIO DE MAQUINARIA');
      $("#card_servicio").removeClass();
      $("#card_servicio").addClass("card card-success");
    }
    if(opcion==4){
      $('#tipo_general').val(5);
      $('#titulo').text('SERVICIO DE LABADERO');
      $("#card_servicio").removeClass();
      $("#card_servicio").addClass("card card-dark");
      $("#tipo_pago").val("1");
      $('#tipo_pago').prop('disabled', true);
      //$('#modo_cliente').prop('disabled', true);
      $('#id_empleado').prop('disabled', true);
      $('#id_cliente').prop('disabled', true);
      $("#modo_cliente").val("0");
      $("#modo_cliente").prop("checked", true); 
      $('#modo_cliente').prop('disabled', true);
      $('#tipo_lab').show();
      
    }
    if(opcion==5){
      $('#tipo_general').val(6);
      $('#titulo').text('SERVICIO DE GANADERIA');
      $("#card_servicio").removeClass();
      $("#card_servicio").addClass("card card-info");
    }
  }




  function limpiar_frm(){
    document.getElementById("frm_caja").reset(); 
    $('#modo_cliente').val(0);
    $('#tipo_pago').val(1);
  }
 

  cargar_clientes_empleados();
  function cargar_clientes_empleados(){
    $('#id_cliente').select2();
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
        let url='{{asset('')}}general/guardar';
        //let url = '{{url('')}}/venta/store';
        Swal.fire({
            title: '¿Desea Concluir el servicio?',
            text: "¡No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#58D68D',
            cancelButtonColor: '#d33',
            confirmButtonText: '!completar!',
            denyButtonColor: '#3498DB',
            showDenyButton: true,
          //  showCancelButton: true,
           // confirmButtonText: 'Save',
            denyButtonText: 'completar y imprimir',

        }).then((result) => {
              if (result.isConfirmed) {
                //
                $.ajax({
                url: url,dataType: 'json',
                method: "POST",
                data: {
                  "tipo_pago"          : $('#tipo_pago').val(),
                  "modo_cliente"       : $('#modo_cliente').val(),
                  "tipo_general"       : $('#tipo_general').val(),
                  "precio"             : $('#precio').val(),
                  "id_cliente"         : $('#id_cliente').val(),
                  "id_empleado"        : $('#id_empleado').val(),
                  "descripcion"        : $('#descripcion').val(),
                  "tipo_lab"           : $('#tipo_lab1').val(),
                  "_token"             :"{{ csrf_token() }}",
                },
                success: function(resultado){
                  //var resultado= JSON.parse(resultado);
                  if(resultado.error==0){
                    toastr.success('Servicio realizada correctamente','Completar servicio',{timeOut:2000});
                    caja_servicio($('#tipo_general').val()-1);
                  }else{
                    mostrarerror('error!','error',resultado.mensaje);
                  } 
                },
                });
              //

              } else if (result.isDenied) {
                //
                $.ajax({
                url: url,dataType: 'json',
                method: "post",
                data: {
                  "tipo_pago"          : $('#tipo_pago').val(),
                  "modo_cliente"       : $('#modo_cliente').val(),
                  "tipo_general"       : $('#tipo_general').val(),
                  "precio"             : $('#precio').val(),
                  "id_cliente"         : $('#id_cliente').val(),
                  "id_empleado"        : $('#id_empleado').val(),
                  "descripcion"        : $('#descripcion').val(),
                  "tipo_lab"           : $('#tipo_lab1').val(),
                  "_token"             :"{{ csrf_token() }}",
                },
                success: function(resultado){
                  //var resultado= JSON.parse(resultado);
                  if(resultado.error==0){
                    
                    toastr.success('correctamente','Completar venta',{timeOut:2000});
                    caja_servicio($('#tipo_general').val()-1);
                    mostrar_detalle(resultado.servicio);
                  }else{
                    mostrarerror('error!','error',resultado.mensaje);
                  } 
                },
              });
              //
            }
         });
  }

  function completar_trabajo(){
    // 0 publico general 1 cliente
     // 1 contado 0 credito
    if($("#precio").val().trim().length > 0 && $("#precio").val()>=0){
      if($("#descripcion").val().trim().length > 0){
        if($("#tipo_pago").val() == 1 || $("#tipo_pago").val() == 0){
          if($("#tipo_general").val().trim().length > 0 && $("#tipo_general").val()>=2 && $("#tipo_general").val()<=6){
            if(($("#modo_cliente").val()==0) || ($("#modo_cliente").val()==1 && $("#id_cliente").val() != '' && $("#id_cliente").val() >=1 )){
              if(($("#id_empleado").val() != -1 && $("#id_empleado").val() >= 1) || ($('#tipo_general').val() == 5)){
                if(($("#tipo_lab1").val()>=1 && $('#tipo_general').val() == 5) || ($('#tipo_general').val() != 5)){
                  guardar_servicio();
                  
                  //mostrarerror('error!','success',"BIEN"+$("#tipo_general").val());
                }else{
                  mostrarerror('error!','error',"requiere seleccionar un tipo de LAB");
                }
              }else{
                mostrarerror('error!','error',"todos los servicios necesitan un empleado dirigido a ecepcion del labadero");
              }  
            }else{
              mostrarerror('error!','error',"no puede hacer un trabajo dirigido sin especificar el cliente");
            }
          }else{
            mostrarerror('error!','error',"seleccione un tipo de trabajo");
          }
        }else{
          mostrarerror('error!','error',"el tipo de pago es necesario 'recarge la pagina por seguridad'");
        }
      }else{
        mostrarerror('error!','error',"la descripcion es necesaria");
      }
    }else{
      mostrarerror('error!','error',"el precio debe ser positivo no negativo");
    }
  }
  
  
  function mostrar_detalle(id_servicio){
    var url='{{asset('')}}general/pdf_tiket/'+id_servicio ;
   // var url='{{asset('')}}informe_dia/'+fecha_inicio;
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
