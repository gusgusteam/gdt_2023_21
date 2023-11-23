@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark">Usuarios</h1>
@stop

@section('content')
    <div class="row mb-2">
        <div class="col-6 text-left">
          @can('usuario.agregar')
            <a class="btn btn-primary" onclick="agregar()">agregar</a>
          @endcan
        </div>
        <div class="col-6 text-right">
          @can('usuario.eliminados')
            <a class="btn btn-danger" href="{{route('usuario.index',0)}}">eliminados</a>
          @endcan
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card {{ config('adminlte.classes_index', '') }}">
                <div class="card-body ">
                    <h1 class="card-title {{ config('adminlte.classes_index_header', '') }} ">LISTA DE USUARIOS</h1> 
                </div>
                <div class="card-header">
                    <table id="example1" class="table table-responsive-xl table-bordered table-sm table-hover table-striped"  >
                      <thead>
                          <tr>  
                            <th width="5%"> # </th>
                            <th width="20%">Usuario</th>
                            <th width="40%">Correo</th>
                            <th width="15%">Rol</th>
                            <th width="10%"><span class="badge bg-primary">Estado</span></th>
                            <th width="10%">Acción</th>
                          </tr>
                      </thead>  
                      <tfoot>
                      </tfoot>
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
              <h5 class="modal-title">Registrar Usuario</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true" class="white-text">×</span>
              </button>
            </div>
            <div id="frm" class="modal-body">
                <form id="frm_agregar" name="frm_agregar"  method="POST" novalidate="novalidate">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="nombre">Nombre</label> 
                                <input class="form-control" id="nombre" name="nombre" type="text" placeholder="INGRESE SU NOMBRE" aria-describedby="nombre-error" aria-invalid="true"/>
                                <span id="nombre-error" class="error invalid-feedback" style="display: none;"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="correo">Correo</label> 
                                <input class="form-control" id="correo" name="correo" type="text" placeholder="INGRESE SU EMAIL" aria-describedby="correo-error" aria-invalid="true"/>
                                <span id="correo-error" class="error invalid-feedback" style="display: none;"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="password">Contraseña</label> 
                            <input class="form-control" id="password" name="password" type="password" placeholder="INGRESE SU CONTRASEÑA" aria-describedby="password-error" aria-invalid="true"/>
                            <span id="password-error" class="error invalid-feedback" style="display: none;"></span>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="password_confirmation">Confirmar Contraseña</label> 
                            <input class="form-control" id="password_confirmation"  name="password_confirmation" type="password"  placeholder="CONFIRME SU CONTRASEÑA" aria-describedby="password_confirmation-error" aria-invalid="true" /> 
                            <span id="password_confirmation-error" class="error invalid-feedback" style="display: none;"></span>
                          </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="select2-primary">
                              <label for="id_rol">Seleccione su rol </label>
                              <select  class="select2 select2-hidden-accessible" id="id_rol" name="id_rol[]" multiple="multiple" data-placeholder="Seleccione los roles" aria-describedby="id_rol-error" aria-invalid="true" style="width: 100%;" data-select2-id="8" tabindex="-1" aria-hidden="true"> 
                              </select> 
                              <span id="id_rol-error" class="error invalid-feedback" style="display: none;"></span>
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
            <h5 class="modal-title">Editar Usuario</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" class="white-text">×</span>
            </button>
          </div>
          <div id="frm2" class="modal-body">
              <form id="frm_editar" name="frm_editar"  method="POST" novalidate="novalidate">
                  @csrf
                  <input type="hidden" id="id_registro" name="id_registro"  >
                  <div class="row">
                      <div class="col-sm-6">
                          <div class="form-group">
                              <label for="Mnombre">Nombre</label> 
                              <input class="form-control" disabled id="Mnombre" name="Mnombre" type="text" placeholder="INGRESE SU NOMBRE" aria-describedby="Mnombre-error" aria-invalid="true"/>
                              <span id="Mnombre-error" class="error invalid-feedback" style="display: none;"></span>
                          </div>
                      </div>
                      <div class="col-sm-6">
                          <div class="form-group">
                              <label for="Mcorreo">Correo</label> 
                              <input class="form-control" disabled id="Mcorreo" name="Mcorreo" type="text" placeholder="INGRESE SU EMAIL" aria-describedby="Mcorreo-error" aria-invalid="true"/>
                              <span id="Mcorreo-error" class="error invalid-feedback" style="display: none;"></span>
                          </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-12">
                          <div class="select2-primary">
                            <label for="Mid_rol">Seleccione su rol</label>
                            <select  class="select2 select2-hidden-accessible" id="Mid_rol" name="Mid_rol[]" multiple="multiple" data-placeholder="Seleccione los roles" aria-describedby="Mid_rol-error" aria-invalid="true" style="width: 100%;" data-select2-id="8" tabindex="-1" aria-hidden="true"> 
                            </select> 
                            <span id="Mid_rol-error" class="error invalid-feedback" style="display: none;"></span>
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


    <div class="modal fade" id="modal_empleado" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;" data-gtm-vis-first-on-screen-2340190_1302="5621" data-gtm-vis-total-visible-time-2340190_1302="100" data-gtm-vis-has-fired-2340190_1302="1" aria-hidden="true">
      <div class="modal-dialog modal-xl" role="document">
        <!--Content-->
        <div class="modal-content">
          <!--Header-->
          <div class="modal-header {{ config('adminlte.classes_index_modal_agregar','') }}">
            <h5 class="modal-title">COMPLETAR REGISTRO DE EMPLEADO</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" class="white-text">×</span>
            </button>
          </div>
          <div id="frm" class="modal-body">
            <form  id="frm_empleado" name="frm_empleado" method="POST" novalidate="novalidate">
              @csrf
              <input type="hidden" id="id_user" name="id_user">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="nombre2" >Nombre</label>
                            <input type="text" name="nombre" class="form-control" id="nombre2" placeholder="ingrese su nombre" aria-describedby="nombre2-error" aria-invalid="true" >
                            <span id="nombre2-error" class="error invalid-feedback" style="display: none;"></span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="apellidos">Apellidos</label>
                            <input type="text" name="apellidos" class="form-control" id="apellidos" placeholder="ingrese sus apellidos" aria-describedby="apellidos-error" aria-invalid="true"/>
                            <span id="apellidos-error" class="error invalid-feedback" style="display: none;"></span>
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
                </div>
                <div class="row">
                    <div class="col-sm-4">
                      <div class="form-group">
                        <label for="direccion">Direccion</label>
                        <textarea type="text" name="direccion" class="form-control" id="direccion" placeholder="ingrese la direccion" aria-describedby="direccion-error" aria-invalid="true"></textarea>
                        <span id="direccion-error" class="error invalid-feedback" style="display: none;"></span>
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <div class="form-group">
                          <label for="telefono">Telefono</label>
                          <input type="number" name="telefono" class="form-control" id="telefono"  placeholder="ingrese el nro: telefono" aria-describedby="telefono-error" aria-invalid="true">
                          <span id="telefono-error" class="error invalid-feedback" style="display: none;"></span>
                      </div>
                    </div>
                    <div class="col-sm-2">
                      <div class="form-group">
                          <label for="sueldo">Sueldo</label>
                          <input type="number" step="any" name="sueldo" class="form-control" id="sueldo" placeholder="ingrese su sueldo" aria-describedby="sueldo-error" aria-invalid="true">
                          <span id="sueldo-error" class="error invalid-feedback" style="display: none;"></span>
                      </div>
                    </div>
                    
                </div> 
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                        <label for="edad">Edad</label>
                        <input type="number" name="edad" class="form-control" id="edad" placeholder="ingrese su edad" aria-describedby="edad-error" aria-invalid="true">
                        <span id="edad-error" class="error invalid-feedback" style="display: none;"></span>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                        <label for="nro_carnet">N: carnet</label>
                        <input type="number"  name="nro_carnet" class="form-control" id="nro_carnet" placeholder="ingrese su nro: carnet" aria-describedby="nro_carnet-error" aria-invalid="true">
                        <span id="nro_carnet-error" class="error invalid-feedback" style="display: none;"></span>
                    </div>
                  </div>
                </div>                 
            </form>
          </div>
          <div class="modal-footer">
              <a type="button" id="next3" name="next3"  class="btn btn-success">Guardar</a>
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
      ajax: "{{route('usuario.datos',1)}}",
      columns: [
          {data: 'id',searchable: false,orderable: false},
          {data: 'name',searchable: true},
          {data: 'email',searchable: true},
          {data: 'rol_uso',searchable: false},
          {data: 'estado',searchable: false},
          {data: 'actions',searchable: false,orderable: false}
      ],
    })
</script>
<script>
    
    function DesignarEmpleado(id_usuario){
      $("#id_user").val(id_usuario);
      $('#modal_empleado').modal('show');
    }
  
    function agregar(){
      clear_frm_agregar();
      $('#modal_agregar').modal('show');
      $('#id_rol').select2();
      cargar_roles();
    }

    function asignar_empleado(){
      var link="{{route('empleado.store')}}";
      $.ajax({
          url: link,
          type: "POST",
          processData: false,
          contentType: false,
          data: new FormData($('#frm_empleado')[0]),    
          success:function(response){
            if (response.error==1){
                  toastr.error(response.mensaje, 'Guardar Registro', {timeOut:5000});
               }else{
                  toastr.success('El registro fue guardado correctamente.', 'Guardar Registro', {timeOut:3000}); 
                  $('#modal_empleado').modal('hide');
                  $("#example1").DataTable().ajax.reload();
               }
          }
      })
    }

    function almacenar(){
      var link="{{route('usuario.store')}}";
      $.ajax({
          url: link,
          type: "POST",
          processData: false,
          contentType: false,
          data: new FormData($('#frm_agregar')[0]),    
          success:function(response){
            if (response.error==1){
                  toastr.error(response.mensaje, 'Guardar Registro', {timeOut:5000});
               }else{
                  toastr.success('El registro fue guardado correctamente.', 'Guardar Registro', {timeOut:3000}); 
                  $('#modal_agregar').modal('hide');
                  $("#example1").DataTable().ajax.reload();
               }
          }
      })
    }

    function Modificar(id){
      clear_frm_editar();
      $.ajax({
        url:"{{asset('')}}"+"usuario/show/"+id, dataType:'json',
        success: function(resultado){
          $("#id_registro").val(resultado.user.id);
          $("#Mnombre").val(resultado.user.name); 
          $("#Mcorreo").val(resultado.user.email);
          $('#Mid_rol').select2();
          $('#Mid_rol').empty();
          resultado.roles.forEach(function(elemento, indice, array) {
            var sw=0;
            for (let i = 0; i < resultado.user.roles.length; i++) {
              if (elemento.id === resultado.user.roles[i].id){
                  sw=1;
              }
            }

            if(sw==1) {
                $('#Mid_rol').append($('<option  />', {
                  text: elemento.name,
                  value: elemento.id,
                  selected: true,
                }));
            }else{
              $('#Mid_rol').append($('<option  />', {
                  text: elemento.name,
                  value: elemento.id,  
              }));
            }
                
          });
          $('#modal_editar').modal('show');
        }
      });
    }

    function actualizar(){
      var id = $("#id_registro").val();
      var link="{{asset('')}}"+"usuario/update/"+id;
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
  

    function cargar_roles(){
        $.ajax({ 
          url:"{{asset('')}}"+"rol/datos" , dataType:'json',
            success: function(resultado){       
              $('#id_rol').empty(); // limpiar antes de sobreescribir
              resultado.roles.forEach(function(elemento, indice, array) {
                $('#id_rol').append($('<option  />', {
                  text: elemento.name,
                  value: elemento.id,  
                }));
              });
            }
        });      
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
          var link="{{asset('')}}"+"usuario/destroy/"+id;
            $.ajax({
                url: link,
                type: "GET",
                cache: false,
                async: false,
                success:function(response){
                }
            })

          Swal.fire(
            '¡Eliminado!',
            'Su registro ha sido inhabilitado.',
            'De acuerdo'
          )
          $("#example1").DataTable().ajax.reload();
        }
      })
    }


    $('#frm_agregar').validate({
        rules: {
          nombre: {
            required: true,
            minlength: 3
          },
          correo: {
            required: true,
            email: true
          },
          password: {
            required: true,
            minlength: 5
          },
          password_confirmation: {
            required: true,
            equalTo: "#password"
          },

        },
        messages: {
          nombre: {
            required: "Por favor, introduzca su nombre",
            minlength: "Minimo 3 caracteres"
          },
          correo: {
            required: "Por favor, dato requerido",
            email: "Correo email no valido"
          },
          password: {
            required: "Por favor, introduzca su contraseña",  
            minlength: "Minimo 5 caracteres"
          },
          password_confirmation: {
            required: "Por favor, es necesario su confirmacion",
            equalTo: "No coinsiden las contraseñas"
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
          Mid_rol: {
            required: true,
          }
        },
        messages: {
          Mid_rol: {
            required: "Por favor, es necesario un rol minimo",
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

    $('#frm_empleado').validate({
        rules: {
          nombre: {
            required: true
          },
          apellidos: {
            required: true
          },
          telefono: {
            required: true,
            minlength: 5
          },
          direccion: {
            required: true
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
          sueldo: {
            required: false
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
          direccion: {
            required: "Por favor, introduzca su direccion donde vive",  
          },
          nro_carnet: {
            required: "Por favor, introduzca su numero de carnet",  
          },
          edad: {
            required: "Por favor, la edad seria util",  
          },
          sueldo: {
            required: "Por favor, el dato es reqerido",  
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
          //actualizar(1);
        }
    });  

    $("#next3").click(function(){  // capture the click
        if($("#frm_empleado").valid())  // test for validity
        {
          asignar_empleado();
        }
    }); 

    function perfil_datos(id_empleado){  
      $.ajax({
        url:"{{asset('')}}"+"empleado/show/"+id_empleado, dataType:'json',
        success: function(resultado){
          Swal.fire({
            title: '<strong>Usuario Empleado</strong>',
            icon: false,
            html:
              '<div class="text-left">'+
                    '<b>nombre : </b>'+resultado.nombre+' <br>' +
                    '<b>apellidos : </b>'+resultado.apellidos+'<br>' +
                    '<b>direccion : </b> '+resultado.direccion+' <br>' +
                    '<b>sexo : </b> '+resultado.sexo+' <br>' +
                    '<b>edad : </b>'+resultado.edad+'<br>' +
                    '<b>CI : </b>'+resultado.ci+' <br>' +
                    '<b>sueldo : </b> '+resultado.sueldo+' bs ' +
              '</div>',
            width: '35em',
              
            //padding: '1em',
            showCloseButton: false,
            showCancelButton: false,
            focusConfirm: false,
            confirmButtonText:
              'Okey'
          })
           

        }
      });
    }
   
</script>




        
@stop