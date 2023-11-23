@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark">Empleados</h1>
@stop

@section('content')
    <div class="row mb-2">
      <div class="col-sm-2">
        <a class="btn btn-primary" onclick="agregar()">agregar</a>
      </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card {{ config('adminlte.classes_index', '') }}">
                <div class="card-body ">
                    <h1 class="card-title {{ config('adminlte.classes_index_header', '') }} ">LISTA DE EMPLEADOS</h1> 
                </div>
                <div class="card-header">
                    <table id="example1" class="table table-responsive-xl table-bordered table-sm table-hover table-striped"  >
                      <thead>
                          <tr>  
                            <th width="5%"> # </th>
                            <th width="5%"> Foto </th>
                            <th width="15%">Nombre y apellido</th>
                            <th width="25%">Direccion</th>
                            <th width="5%">Edad</th>
                            <th width="10%">Sexo</th>
                            <th width="10%">Telefono</th>
                            <th width="10%">CI</th>
                            <th width="10%"><span class="badge bg-primary">Estado</span></th>
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

    
    <div class="modal fade" id="modal_agregar" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;" data-gtm-vis-first-on-screen-2340190_1302="5621" data-gtm-vis-total-visible-time-2340190_1302="100" data-gtm-vis-has-fired-2340190_1302="1" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <!--Content-->
        <div class="modal-content">
          <!--Header-->
          <div class="modal-header {{ config('adminlte.classes_index_modal_agregar','') }}">
            <h5 class="modal-title">Registrar Empleado</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" class="white-text">×</span>
            </button>
          </div>
          <div id="frm" class="modal-body">
            <form  id="frm_agregar" name="frm_agregar" method="POST" novalidate="novalidate">
              @csrf
              <input type="hidden" id="id_user" name="id_user" value="-1">

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="nombre" >Nombre</label>
                            <input type="text" name="nombre" class="form-control" id="nombre" placeholder="ingrese su nombre" aria-describedby="nombre-error" aria-invalid="true" >
                            <span id="nombre-error" class="error invalid-feedback" style="display: none;"></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="apellidos">Apellidos</label>
                            <input type="text" name="apellidos" class="form-control" id="apellidos" placeholder="ingrese sus apellidos" aria-describedby="apellidos-error" aria-invalid="true"/>
                            <span id="apellidos-error" class="error invalid-feedback" style="display: none;"></span>
                        </div>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="col-sm-4">
                      <div class="form-group">
                          <label for="telefono">Telefono</label>
                          <input type="number" name="telefono" class="form-control" id="telefono"  placeholder="ingrese el nro: telefono" aria-describedby="telefono-error" aria-invalid="true">
                          <span id="telefono-error" class="error invalid-feedback" style="display: none;"></span>
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <div class="form-group">
                          <label for="sexo">Sexo</label>
                          <select class="form-control" name="sexo" id="sexo">
                            <option disabled selected value="">Seleccione su sexo</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Femenino">Femenino</option>
                          </select>
                          <span id="sexo-error" class="error invalid-feedback" style="display: none;"></span>
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <div class="form-group">
                          <label for="edad">Edad</label>
                          <input type="number" name="edad" class="form-control" id="edad" placeholder="ingrese su edad" aria-describedby="edad-error" aria-invalid="true">
                          <span id="edad-error" class="error invalid-feedback" style="display: none;"></span>
                      </div>
                    </div>
                    
                </div> 
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                        <label for="nro_carnet">N: carnet</label>
                        <input type="number" name="nro_carnet" class="form-control" id="nro_carnet" placeholder="ingrese su nro: carnet" aria-describedby="nro_carnet-error" aria-invalid="true">
                        <span id="nro_carnet-error" class="error invalid-feedback" style="display: none;"></span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                        <label for="direccion">Direccion</label>
                        <textarea type="text" name="direccion" class="form-control" id="direccion" placeholder="ingrese su una direccion" aria-describedby="direccion-error" aria-invalid="true"></textarea>
                        <span id="direccion-error" class="error invalid-feedback" style="display: none;"></span>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sueldo">Sueldo</label>
                        <input type="number" name="sueldo" step="any" class="form-control" id="sueldo" placeholder="ingrese su sueldo" aria-describedby="sueldo-error" aria-invalid="true">
                        <span id="sueldo-error" class="error invalid-feedback" style="display: none;"></span>
                    </div>
                  </div>
                </div>                 
            </form>
          </div>
          <div class="modal-footer">
              <a type="button" id="next" name="next"  class="btn btn-success">Guardar</a>
              <a type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</a>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modal_editar" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;" data-gtm-vis-first-on-screen-2340190_1302="5621" data-gtm-vis-total-visible-time-2340190_1302="100" data-gtm-vis-has-fired-2340190_1302="1" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <!--Content-->
        <div class="modal-content">
          <!--Header-->
          <div class="modal-header {{ config('adminlte.classes_index_modal_editar','') }}">
            <h5 class="modal-title">Editar Empleado</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" class="white-text">×</span>
            </button>
          </div>
          <div id="frm2" class="modal-body">
              <form  id="frm_editar" name="frm_editar" method="POST" novalidate="novalidate">
                @csrf
                <input type="hidden" id="id_registro" name="id_registro"  >
                <div class="row">
                      <div class="col-sm-6">
                          <div class="form-group">
                              <label for="Mnombre" >Nombre</label>
                              <input type="text" name="Mnombre" class="form-control" id="Mnombre" placeholder="ingrese su nombre" aria-describedby="Mnombre-error" aria-invalid="true" >
                              <span id="Mnombre-error" class="error invalid-feedback" style="display: none;"></span>
                          </div>
                      </div>
                      <div class="col-sm-6">
                          <div class="form-group">
                              <label for="Mapellidos">Apellidos</label>
                              <input type="text" name="Mapellidos" class="form-control" id="Mapellidos" placeholder="ingrese sus apellidos" aria-describedby="Mapellidos-error" aria-invalid="true"/>
                              <span id="Mapellidos-error" class="error invalid-feedback" style="display: none;"></span>
                          </div>
                      </div>
                      
                  </div>
                  <div class="row">
                      <div class="col-sm-4">
                        <div class="form-group">
                            <label for="Mtelefono">Telefono</label>
                            <input type="number" name="Mtelefono" class="form-control" id="Mtelefono"  placeholder="ingrese el nro: telefono" aria-describedby="Mtelefono-error" aria-invalid="true">
                            <span id="Mtelefono-error" class="error invalid-feedback" style="display: none;"></span>
                        </div>
                      </div>
                      <div class="col-sm-4">
                        <div class="form-group">
                            <label for="Msexo">Sexo</label>
                            <select class="form-control" name="Msexo" id="Msexo">
                              <option disabled selected value="">Seleccione su sexo</option>
                              <option value="Masculino">Masculino</option>
                              <option value="Femenino">Femenino</option>
                            </select>
                            <span id="Msexo-error" class="error invalid-feedback" style="display: none;"></span>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                            <label for="Medad">Edad</label>
                            <input type="number" name="Medad" class="form-control" id="Medad" placeholder="ingrese su edad" aria-describedby="Medad-error" aria-invalid="true">
                            <span id="Medad-error" class="error invalid-feedback" style="display: none;"></span>
                        </div>
                      </div>
                  </div> 
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                          <label for="Mnro_carnet">N: carnet</label>
                          <input type="number" name="Mnro_carnet" class="form-control" id="Mnro_carnet" placeholder="ingrese su nro: carnet" aria-describedby="Mnro_carnet-error" aria-invalid="true">
                          <span id="Mnro_carnet-error" class="error invalid-feedback" style="display: none;"></span>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                          <label for="Msueldo">Sueldo</label>
                          <input type="number" step="any" name="Msueldo" class="form-control" id="Msueldo" placeholder="ingrese el monto que gana" aria-describedby="Msueldo-error" aria-invalid="true">
                          <span id="Msueldo-error" class="error invalid-feedback" style="display: none;"></span>
                      </div>
                    </div>
                  </div> 
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                          <label for="Mdireccion">Direccion</label>
                          <textarea type="text" name="Mdireccion" class="form-control" id="Mdireccion" placeholder="ingrese una direccion de donde vive" aria-describedby="Mdireccion-error" aria-invalid="true"></textarea>
                          <span id="Mdireccion-error" class="error invalid-feedback" style="display: none;"></span>
                      </div>
                    </div>
                  </div>                 
              </form>
          </div>
          <div class="modal-footer">
              <a type="button" id="next2" name="next2"  class="btn btn-success">Actualizar</a>
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
      ajax: "{{route('empleado.datos')}}",
      columns: [
          {data: 'id',searchable: false,orderable: false},
          {data: 'foto',searchable: false},
          {data: 'nombre_completo',searchable: true},
          {data: 'direccion',searchable: false},
          {data: 'edad',searchable: false},
          {data: 'sexo',searchable: false},
          {data: 'telefono',searchable: false},
          {data: 'ci',searchable: true},
          {data: 'estado',searchable: false},
          {data: 'actions',searchable: false,orderable: false}
      ],
    })
</script>
<script>
    
  
    function agregar(){
      reset_frm_agregar();
      clear_frm_agregar();
      $('#modal_agregar').modal('show');
    }


    function almacenar(){
      var link="{{route('empleado.store')}}";
      $.ajax({
          url: link,dataType:'json',
          type: "POST",
          processData: false,
          contentType: false,
          data: new FormData($('#frm_agregar')[0]),    
          success:function(response){
            if (response.error==0){
                  toastr.success('El registro fue guardado correctamente.', 'Guardar Registro', {timeOut:2000}); 
                  $('#modal_agregar').modal('hide');
                  $("#example1").DataTable().ajax.reload();
               }else{
                toastr.error(response.mensaje, 'Guardar Registro', {timeOut:4000});
               }
          }
      })
    }
    

    function Modificar(id){
      clear_frm_editar();
      $.ajax({
        url:"{{asset('')}}"+"empleado/show/"+id, dataType:'json',
        success: function(resultado){
          $("#id_registro").val(resultado.id);
          $("#Mnombre").val(resultado.nombre); 
          $("#Mapellidos").val(resultado.apellidos); 
          $("#Msexo").val(resultado.sexo); 
          $("#Medad").val(resultado.edad); 
          $("#Mtelefono").val(resultado.telefono); 
          $("#Mnro_carnet").val(resultado.ci);
          $("#Mdireccion").val(resultado.direccion);
          $("#Msueldo").val(resultado.sueldo);   
          $('#modal_editar').modal('show');
        }
      });
    }

    function actualizar(){
      var id = $("#id_registro").val();
      var link="{{asset('')}}"+"empleado/update/"+id;
      $.ajax({
          url: link,
          type: "POST",
          processData: false,
          contentType: false,
          data: new FormData($('#frm_editar')[0]),    
          success:function(response){
            if (response.error==1){
                  toastr.error(response.mensaje, 'Guardar Registro', {timeOut:5000});
               }else{
                  toastr.success('El registro fue actualizado correctamente.', 'Guardar Registro', {timeOut:3000}); 
                  $('#modal_editar').modal('hide');
                  $("#example1").DataTable().ajax.reload();
               }
          }
      })
    }
  
    function Eliminar(id){
      Swal.fire({
        title: '¿Está seguro?',
        text: "¡El registro cambiara a estado inactivo!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Si, Estoy seguro!'
      }).then((result) => {
        if (result.isConfirmed) {
          var link="{{asset('')}}"+"empleado/destroy/"+id;
            $.ajax({
                url: link,
                type: "GET",
                cache: false,
                async: false,
                success:function(response){
                  $("#example1").DataTable().ajax.reload();
                  Swal.fire(
                    'Inactivo!',
                    'Su registro ha sido inhabilitado no se realizara compras.',
                    'De acuerdo'
                  ).then((result)=>{
                  })
                }
            })
        }
      })
    }

    function Restaurar(id){
      Swal.fire({
        title: '¿Está seguro?',
        text: "¡El registro cambiara a estado activo!",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Si, Estoy seguro!'
      }).then((result) => {
        if (result.isConfirmed) {
          var link="{{asset('')}}"+"empleado/restore/"+id;
            $.ajax({
                url: link,
                type: "GET",
                cache: false,
                async: false,
                success:function(response){
                  $("#example1").DataTable().ajax.reload();
                  Swal.fire({
                    title: '¡Restaurado!',
                    text: 'El empelado ha sido habilitado podra realizar compras.',
                    confirmButtonText:'De acuerdo',
                    icon: 'success'
                  }).then((result)=>{
                  })
                }
            })
        }
      })
    }



    $('#frm_agregar').validate({
        rules: {
          nombre: {
            required: true
          },
          apellidos: {
            required: true
          },
          telefono: {
            required: false,
            minlength: 5
          },
          sexo: {
            required: true
          },
          nro_carnet: {
            required: true
          },
          edad: {
            required: true
          },
          direccion: {
            required: true
          },
          sueldo: {
            required: true
          }
        },
        messages: {
          nombre: {
            required: "Por favor, dato requerido",   
          },
          apellidos: {
            required: "Por favor, dato requerido",
          },
          telefono: {
            required: "Por favor, introduzca su telefono",  
            minlength: "Minimo 5 numero"
          },
          sexo: {
            required: "Por favor, es necesario que seleccione una opcion",
          },
          nro_carnet: {
            required: "Por favor, introduzca su numero de carnet",  
          },
          edad: {
            required: "Por favor, la edad seria util",  
          },
          direccion: {
            required: "Por favor, la direccion es importante",  
          },
          sueldo: {
            required: "Por favor, el sueldo es muy reqerida",
          },
          
        },
        errorElement: 'span',
        
        errorPlacement: function (error, element) {
           error.addClass('invalid-feedback');
           element.closest('.form-group').append(error);  
        },
        
        highlight: function (element, errorClass, validClass) {
         $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
         $(element).removeClass('is-invalid').addClass( "is-valid" );
        }               
    });

    $('#frm_editar').validate({
        rules: {
          Mnombre: {
            required: true
          },
          Mapellidos: {
            required: true
          },
          Mtelefono: {
            required: false,
            minlength: 5
          },
          Msexo: {
            required: true
          },
          Mnro_carnet: {
            required: true
          },
          Medad: {
            required: true
          }
        },
        messages: {
          Mnombre: {
            required: "Por favor, dato requerido",   
          },
          Mapellidos: {
            required: "Por favor, dato requerido",
          },
          Mtelefono: {
            required: "Por favor, introduzca su telefono",  
            minlength: "Minimo 5 numero"
          },
          Msexo: {
            required: "Por favor, es necesario que seleccione una opcion",
          },
          Mnro_carnet: {
            required: "Por favor, introduzca su numero de carnet",  
          },
          Medad: {
            required: "Por favor, la edad seria util",  
          }
          
        },
        errorElement: 'span',
        
        errorPlacement: function (error, element) {
           error.addClass('invalid-feedback');
           element.closest('.form-group').append(error);  
        },
        
        highlight: function (element, errorClass, validClass) {
         $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
         $(element).removeClass('is-invalid').addClass( "is-valid" );
        }               
    });

  
    $("#next").click(function(){  // capture the click
        if($("#frm_agregar").valid())  // test for validity
        {
          almacenar();
        }
    }); 

    $("#next2").click(function(){  // capture the click
        if($("#frm_editar").valid())  // test for validity
        {
          actualizar();
        }
    });  



   
   
</script>
    
@stop