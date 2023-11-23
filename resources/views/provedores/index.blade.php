@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark">Provedor</h1>
@stop

@section('content')
    <div class="row mb-2">
      <div class="col-6 text-left">
          <a class="btn btn-primary" onclick="agregar()">agregar</a>
      </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card {{ config('adminlte.classes_index', '') }}">
                <div class="card-body ">
                    <h1 class="card-title {{ config('adminlte.classes_index_header', '') }} ">LISTA DE PROVEDORES</h1> 
                </div>
                <div class="card-header">
                    <table id="example1" class="table table-responsive-xl table-bordered table-sm table-hover table-striped"  >
                      <thead>
                          <tr>  
                            <th width="5%"> # </th>
                
                            <th width="15%">Nombre o razon social</th>
                            <th width="25%">Descripcion</th>   
                            <th width="25%">Direccion</th>
                            <th width="10%">Telefono</th>
                            <th width="10%">Nic</th>
                            <th width="5%">Tipo</th>
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


    <div class="modal fade" id="modal_agregar" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;" data-gtm-vis-first-on-screen-2340190_1302="5621" data-gtm-vis-total-visible-time-2340190_1302="100" data-gtm-vis-has-fired-2340190_1302="1" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <!--Content-->
        <div class="modal-content">
          <!--Header-->
          <div class="modal-header {{ config('adminlte.classes_index_modal_agregar','') }}">
            <h5 class="modal-title">Registrar Provedor</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" class="white-text">×</span>
            </button>
          </div>
          <div id="frm" class="modal-body">
            <form  id="frm_agregar" name="frm_agregar" method="POST" novalidate="novalidate">
              @csrf
              <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="nombre" >Nombre O Razon social</label>
                        <input type="text" name="nombre" class="form-control" id="nombre" placeholder="ingrese el nombre de la empresa o persona" aria-describedby="nombre-error" aria-invalid="true" >
                        <span id="nombre-error" class="error invalid-feedback" style="display: none;"></span>
                    </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                      <label for="direccion">Direccion</label>
                      <textarea type="text" name="direccion" class="form-control" id="direccion" placeholder="ingrese una direccion" aria-describedby="direccion-error" aria-invalid="true"></textarea>
                      <span id="direccion-error" class="error invalid-feedback" style="display: none;"></span>
                  </div>
                </div>  
            </div>
            <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                      <label for="descripcion">Descripcion</label>
                      <textarea type="text" name="descripcion" class="form-control" id="descripcion" placeholder="ingrese una descripcion " aria-describedby="descripcion-error" aria-invalid="true"></textarea>
                      <span id="descripcion-error" class="error invalid-feedback" style="display: none;"></span>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                      <label for="tipo">Tipo</label>
                      <select class="form-control" name="tipo" id="tipo">
                        <option disabled selected value="">Seleccione tipo de provedor</option>
                        <option value="0">Persona</option>
                        <option value="1">Empresa</option>
                      </select>
                      <span id="tipo-error" class="error invalid-feedback" style="display: none;"></span>
                  </div>
                </div>
            </div> 
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                    <label for="telefono">Telefono</label>
                    <input type="number" name="telefono" class="form-control" id="telefono" placeholder="ingrese su telefono" aria-describedby="telefono-error" aria-invalid="true">
                    <span id="telefono-error" class="error invalid-feedback" style="display: none;"></span>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                    <label for="correo" >Correo</label>
                    <input type="text" name="correo" class="form-control" id="correo" placeholder="ingrese el correo" aria-describedby="correo-error" aria-invalid="true" >
                    <span id="correo-error" class="error invalid-feedback" style="display: none;"></span>
                </div>
              </div>
            </div> 
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                    <label for="nic">Nic</label>
                    <input type="number" name="nic" class="form-control" id="nic" placeholder="ingrese su NIC" aria-describedby="nic-error" aria-invalid="true">
                    <span id="nic-error" class="error invalid-feedback" style="display: none;"></span>
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
            <h5 class="modal-title">Editar Provedor</h5>
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
                              <label for="Mnombre" >Nombre O Razon social</label>
                              <input type="text" name="Mnombre" class="form-control" id="Mnombre" placeholder="ingrese el nombre de la empresa o persona" aria-describedby="Mnombre-error" aria-invalid="true" >
                              <span id="Mnombre-error" class="error invalid-feedback" style="display: none;"></span>
                          </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="form-group">
                            <label for="Mdireccion">Direccion</label>
                            <textarea type="text" name="Mdireccion" class="form-control" id="Mdireccion" placeholder="ingrese una direccion" aria-describedby="Mdireccion-error" aria-invalid="true"></textarea>
                            <span id="Mdireccion-error" class="error invalid-feedback" style="display: none;"></span>
                        </div>
                      </div>  
                  </div>
                  <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group">
                            <label for="Mdescripcion">Descripcion</label>
                            <textarea type="text" name="Mdescripcion" class="form-control" id="Mdescripcion" placeholder="ingrese una descripcion " aria-describedby="Mdescripcion-error" aria-invalid="true"></textarea>
                            <span id="Mdescripcion-error" class="error invalid-feedback" style="display: none;"></span>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="form-group">
                            <label for="Mtipo">Tipo</label>
                            <select class="form-control" name="Mtipo" id="Mtipo">
                              <option disabled selected value="">Seleccione tipo de provedor</option>
                              <option value="0">Persona</option>
                              <option value="1">Empresa</option>
                            </select>
                            <span id="Mtipo-error" class="error invalid-feedback" style="display: none;"></span>
                        </div>
                      </div>
                     
                  </div> 
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                          <label for="Mtelefono">Telefono</label>
                          <input type="number" name="Mtelefono" class="form-control" id="Mtelefono" placeholder="ingrese su telefono" aria-describedby="Mtelefono-error" aria-invalid="true">
                          <span id="Mtelefono-error" class="error invalid-feedback" style="display: none;"></span>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                          <label for="Mcorreo" >Correo</label>
                          <input type="text" name="Mcorreo" class="form-control" id="Mcorreo" placeholder="ingrese el correo" aria-describedby="Mcorreo-error" aria-invalid="true" >
                          <span id="Mcorreo-error" class="error invalid-feedback" style="display: none;"></span>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                          <label for="Mnic">Nic</label>
                          <input type="number" name="Mnic" class="form-control" id="Mnic" placeholder="ingrese su NIC" aria-describedby="Mnic-error" aria-invalid="true">
                          <span id="Mnic-error" class="error invalid-feedback" style="display: none;"></span>
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
      ajax: "{{route('provedor.datos')}}",
      columns: [
          {data: 'id',searchable: false,orderable: false},
          {data: 'nombre',searchable: true},
          {data: 'descripcion',searchable: false},
          {data: 'direccion',searchable: false},
          {data: 'telefono',searchable: false},
          {data: 'nic',searchable: false},
          {data: 'tipo2',searchable: false},
          {data: 'estado',searchable: false},
          {data: 'actions',searchable: false,orderable: false}
      ],
    })
</script>
<script>
    
  
    function agregar(){
      clear_frm_agregar();
      $('#modal_agregar').modal('show');
    }

    function almacenar(){
      var link="{{route('provedor.store')}}";
      $.ajax({
          url: link,dataType: 'json',
          type: "POST",
          processData: false,
          contentType: false,
          data: new FormData($('#frm_agregar')[0]),    
          success:function(result){
            if (result.error==0){
                  toastr.success('El registro fue guardado correctamente.', 'Guardar Registro', {timeOut:2000}); 
                  $('#modal_agregar').modal('hide');
                  $("#example1").DataTable().ajax.reload();
                  //setTimeout(redirigir, '3000');
               }else{
                toastr.error(result.mensaje, 'Guardar Registro', {timeOut:5000});
               }
          }
      })
    }

    

    function Modificar(id){
      clear_frm_editar();
      $.ajax({
        url:"{{asset('')}}"+"provedor/show/"+id, dataType:'json',
        success: function(resultado){
          $("#id_registro").val(resultado.id);
          $("#Mnombre").val(resultado.nombre); 
          $("#Mdescripcion").val(resultado.descripcion); 
          $("#Mtelefono").val(resultado.telefono); 
          $("#Mtipo").val(resultado.tipo); 
          $("#Mnic").val(resultado.nic); 
          $("#Mcorreo").val(resultado.correo); 
          $("#Mdireccion").val(resultado.direccion);
          $('#modal_editar').modal('show');
        }
      });
    }

    function actualizar(){
      var id = $("#id_registro").val();
      var link="{{asset('')}}"+"provedor/update/"+id;
      $.ajax({
          url: link,dataType:'json',
          type: "POST",
          processData: false,
          contentType: false,
          data: new FormData($('#frm_editar')[0]),    
          success:function(response){
            if (response.error==0){
                  toastr.success('El registro fue actualizado correctamente.', 'Guardar Registro', {timeOut:2000}); 
                  $('#modal_editar').modal('hide');
                  $("#example1").DataTable().ajax.reload();
               }else{
                  toastr.error(response.mensaje, 'Guardar Registro', {timeOut:2000});
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
          var link="{{asset('')}}"+"provedor/destroy/"+id;
            $.ajax({
                url: link,dataType:'json',
                type: "GET",
                cache: false,
                async: false,
                success:function(response){
                  $("#example1").DataTable().ajax.reload();
                  Swal.fire(
                    'Inactivo!',
                    'Su registro ha sido inhabilitado.',
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
          var link="{{asset('')}}"+"provedor/restore/"+id;
            $.ajax({
                url: link,dataType:'json',
                type: "GET",
                cache: false,
                async: false,
                success:function(response){
                  $("#example1").DataTable().ajax.reload();
                  Swal.fire({
                    title: '¡Restaurado!',
                    text: 'Su registro ha sido habilitado.',
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
          direccion: {
            required: true
          },
          telefono: {
            required: true,
            minlength: 5
          },
          descripcion: {
            required: true
          },
          nic: {
            required: false
          },
          correo: {
            required: true,
            email: true
          },
          tipo: {
            required: true
          }
        },
        messages: {
          nombre: {
            required: "Por favor, dato requerido",   
          },
          direccion: {
            required: "Por favor, la direccion es requerido",
          },
          telefono: {
            required: "Por favor, introduzca su telefono",  
            minlength: "Minimo 5 numero"
          },
          tipo: {
            required: "Por favor, es necesario que seleccione el tipo de provedor",
          },
          descripcion: {
            required: "Por favor, introduzca su descripcion",  
          },
          correo: {
            required: "Por favor, la edad seria util",
            email: "tiene que ser un correo valido"  
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

    $('#frm_editar').validate({
        rules: {
          Mnombre: {
            required: true
          },
          Mdireccion: {
            required: true
          },
          Mtelefono: {
            required: true,
            minlength: 5
          },
          Mdescripcion: {
            required: true
          },
          Mnic: {
            required: false
          },
          Mcorreo: {
            required: true,
            email: true
          },
          Mtipo: {
            required: true
          }
        },
        messages: {
          Mnombre: {
            required: "Por favor, dato requerido",   
          },
          Mdireccion: {
            required: "Por favor, la direccion es requerido",
          },
          Mtelefono: {
            required: "Por favor, introduzca su telefono",  
            minlength: "Minimo 5 numero"
          },
          Mtipo: {
            required: "Por favor, es necesario que seleccione el tipo de provedor",
          },
          Mdescripcion: {
            required: "Por favor, introduzca su descripcion",  
          },
          Mcorreo: {
            required: "Por favor, la edad seria util",
            email: "tiene que ser un correo valido"  
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