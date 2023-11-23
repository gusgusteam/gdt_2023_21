@extends('adminlte::page')

@section('title')

@section('content_header')
  <h1 class="m-0 text-dark">Configuracion del sistema</h1>
@stop

@section('content')
    <div class="row mb-2">
        <div class="col-sm-4 ">
            <div class="card ">
                <div class="card-header">
                  <h5>Logo del sistema</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <img id="logo" name="logo" src="{{asset('vendor/adminlte/dist/img/AdminLTELogo.png')}}" alt="imagen" class="img-responsive" width= "100px" height="100px"> 
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row justify-content-between">
                        <div class="col-sm-4">
                            <a class="btn btn-primary" onclick="cambiar_logo()">Actualizar</a>
                        </div>
                        <div class="col-sm-6">
                            <div class="custom-file">
                                <form enctype="multipart/form-data" id="form_logo"   role="form" method="post">
                                    @csrf
                                    <input style="cursor: pointer;" type="file" id="img_logo" name="img_logo" class="custom-file-input" accept="image/png">
                                </form>
                                <label class="custom-file-label align-middle" for="img_logo" data-browse="Buscar">Cambiar Logo</label>
                            </div>
                        </div>     
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-8">
            <div class="card ">
                <div class="card-header">
                  <h5>Datos de la empresa</h5>
                </div>
               {{-- <img src="{{asset('vendor/adminlte/dist/img/AdminLTELogo.png')}}" alt="imagen producto"> --}}
                <div class="card-body">
                    <form id="form_datos" name="form_datos" method="POST" novalidate="novalidate">
                        @csrf
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="nombre_empresa">Nombre de la empresa</label> 
                                    <input class="form-control" id="nombre_empresa" name="nombre_empresa" type="text" value="{{$datos->nombre}}" placeholder="INGRESE EL NOMBRE DE LA EMPRESA" aria-describedby="nombre_empresa-error" aria-invalid="true"/>
                                    <span id="nombre_empresa-error" class="error invalid-feedback" style="display: none;"></span>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="nombre_empresa2">&nbsp;&nbsp;</label> 
                                    <input class="form-control" id="nombre_empresa2" name="nombre_empresa2" type="text" value="{{$datos->nombre2}}" placeholder="INGRESE EL NOMBRE RESUMIDO" aria-describedby="nombre_empresa2-error" aria-invalid="true"/>
                                    <span id="nombre_empresa2-error" class="error invalid-feedback" style="display: none;"></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="frase">frase ticket</label> 
                                    <input class="form-control" id="frase" name="frase" type="text" value="{{$datos->leyenda}}" placeholder="INGRESE SU FRASE DE TIKET" aria-describedby="frase-error" aria-invalid="true"/>
                                    <span id="frase-error" class="error invalid-feedback" style="display: none;"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                              <div class="form-group">
                                <label for="direccion">Direccion</label> 
                                <input class="form-control" id="direccion" name="direccion" type="text" value="{{$datos->direccion}}" placeholder="INGRESE LA DIRECCION DE LA EMPRESA" aria-describedby="direccion-error" aria-invalid="true"/>
                                <span id="direccion-error" class="error invalid-feedback" style="display: none;"></span>
                              </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <label for="telefono">Telefono</label> 
                                <input class="form-control" id="telefono"  name="telefono" type="number" value="{{$datos->telefono}}"  placeholder="INGRESE EL NUMERO DE TELEFONO" aria-describedby="telefono-error" aria-invalid="true" /> 
                                <span id="telefono-error" class="error invalid-feedback" style="display: none;"></span>
                              </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                              <div class="form-group">
                                <label for="correo">Correo electronico</label> 
                                <input class="form-control" id="correo" name="correo" type="email" value="{{$datos->correo}}" placeholder="INGRESE EL CORREO ELECTRONICO" aria-describedby="correo-error" aria-invalid="true"/>
                                <span id="correo-error" class="error invalid-feedback" style="display: none;"></span>
                              </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <label for="nic">NIC</label> 
                                <input class="form-control" id="nic"  name="nic" type="text" value="{{$datos->nic}}"  placeholder="INGRESE EL NUMERO DE NIC" aria-describedby="nic-error" aria-invalid="true" /> 
                                <span id="nic-error" class="error invalid-feedback" style="display: none;"></span>
                              </div>
                            </div>
                        </div>
                        
                    </form>
                </div>
                <div class="card-footer">
                    <a class="btn btn-primary" id="mod_datos">Modificar datos</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-2">
      <div class="col-sm-6 ">
        <div class="card ">
            <div class="card-header">
              <h5>Fondo del login</h5>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <img id="fondo" name="fondo" src="{{asset('img/fondo_principal.jpg')}}" alt="imagen" class="img-responsive img-fluid" > 
                </div>
            </div>
            <div class="card-footer">
                <div class="row justify-content-between">
                    <div class="col-sm-4">
                        <a class="btn btn-primary" onclick="cambiar_fondo()">Actualizar</a>
                    </div>
                    
                    <div class="col-sm-4">
                        <div class="custom-file">
                            <form enctype="multipart/form-data" id="form_fondo"   role="form" method="post">
                                @csrf
                                <input style="cursor: pointer;" type="file" id="img_fondo" name="img_fondo" class="custom-file-input" accept="image/jpg">
                            </form>
                            <label class="custom-file-label align-middle" for="img_fondo" data-browse="Buscar">Cambiar Fondo</label>
                        </div>
                    </div>     
                </div>
            </div>
        </div>
      </div>
      <div class="col-sm-6 ">
        <div class="card ">
            <div class="card-header">
              <h5>Configuracion basica del sistema</h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <form enctype="multipart/form-data" id="form_config" name="form_config"method="post">
                      @csrf
                      <label for="tabla">seleccione el tipo de tabla</label> 
                      <select class="form-control" name="tabla" id="tabla">
                        <option @if (session()->has('responsivo')) @if (session('responsivo')==0) selected @endif  @endif value="0">tabla normal</option>
                        <option @if (session()->has('responsivo')) @if (session('responsivo')==1) selected @endif  @endif value="1">tabla responsivo</option>
                      </select>
                      <span id="tabla-error" class="error invalid-feedback" style="display: none;"></span>
                    </form>
                  </div>
                </div>
            </div>
            </div>
            <div class="card-footer">
                <div class="row justify-content-between">
                    <div class="col-sm-4">
                        <a class="btn btn-primary" id="mod_config" >Modificar configuracion</a>
                    </div>   
                </div>
            </div>
        </div>
      </div>
    </div>
    

      

@stop

@section('js')  

<script type="text/javascript">
    function cambiar_logo(){
      var link="{{route('configuracion.logo_update')}}";
      $.ajax({
          url: link,
          type: "POST",
          enctype: 'multipart/form-data', // para capturar file imagenes
          processData: false,
          contentType: false,
         // cache: false,
         // async: false,
          data: new FormData($('#form_logo')[0]),  
          success:function(response){
            if (response.error==1){
                toastr.error(response.mensaje, 'Actualizar Logo', {timeOut:4000})
               }else{
                  toastr.success('El logo fue actualizado correctamente.', 'Actualizar Logo', {timeOut:3000});  
                  setTimeout(redirigir, '1000');
               }
          }
      })
    }

    function cambiar_fondo(){
      var link="{{route('configuracion.fondo_update')}}";
      $.ajax({
          url: link,
          type: "POST",
          enctype: 'multipart/form-data', // para capturar file imagenes
          processData: false,
          contentType: false,
         // cache: false,
         // async: false,
          data: new FormData($('#form_fondo')[0]),  
          success:function(response){
            if (response.error==1){
                toastr.error(response.mensaje, 'Actualizar Fondo', {timeOut:4000})
               }else{
                  toastr.success('El fondo fue actualizado correctamente.', 'Actualizar Fondo', {timeOut:3000});
                  setTimeout(redirigir, '1000');
               }
          }
      })
    }

    function cambiar_datos(){
      var link="{{route('configuracion.datos_update')}}";
      $.ajax({
          url: link,
          type: "POST",
          enctype: 'multipart/form-data', // para capturar file imagenes
          processData: false,
          contentType: false,
         // cache: false,
         // async: false,
          data: new FormData($('#form_datos')[0]),  
          success:function(response){
            if (response.error==1){
                toastr.error(response.mensaje, 'Actualizar Datos', {timeOut:4000})
               }else{
                  toastr.success('Los datos fueron actualizado correctamente.', 'Actualizar Datos', {timeOut:3000});  
                  setTimeout(redirigir, '1000'); 
               }
          }
      })
    }

    function cambiar_config(){
      var link="{{route('configuracion.config_update')}}";
      $.ajax({
          url: link,
          type: "POST",
          enctype: 'multipart/form-data', // para capturar file imagenes
          processData: false,
          contentType: false,
         // cache: false,
         // async: false,
          data: new FormData($('#form_config')[0]),  
          success:function(response){
            if (response.error==1){
                toastr.error(response.mensaje, 'Actualizar Configuracion', {timeOut:4000})
               }else{
                  toastr.success('La configuracion fueron actualizado correctamente.', 'Actualizar Configuracion', {timeOut:3000});   
                  setTimeout(redirigir, '1000');
               }
          }
      })
    }


    
    $('#form_datos').validate({
        rules: {
          nombre_empresa: {
            required: true,
          },
          nombre_empresa2: {
            required: true,
            minlength: 1
          },
          direccion: {
            required: true,
          },
          telefono: {
            required: true,
            minlength: 5
          },
          correo: {
            required: true,
            email: true
          },
          frase: {
            required: true,
          }
        },
        messages: {
          nombre_empresa: {
            required: "Por favor, es necesario el nombre de la empresa",
          },
          nombre_empresa2: {
            required: "Por favor, es necesario la Sigla",
            minlength: "minimo 1 caracter"
          },
          direccion: {
            required: "Por favor, es necesario la direccion de la empresa",
          },
          telefono: {
            required: "Por favor, es necesario un telefono como referencia a la empresa",
            minlength: "minimo 5 numeros para que sea valido"
          },
          correo: {
            required: "Por favor, es necesario el correo electronico de la empresa",
          },
          frase: {
            required: "Por favor, es necesario una frase para los tiket",
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

    $('#form_config').validate({
        rules: {
          tabla: {
            required: true,
          }
        },
        messages: {
          tabla: {
            required: "Por favor, es necesario que seleccione",
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
    
    $("#mod_datos").click(function(){  // capture the click
        if($("#form_datos").valid())  // test for validity
        {
            cambiar_datos();
        }
    });

    $("#mod_config").click(function(){  // capture the click
        if($("#form_config").valid())  // test for validity
        {
            cambiar_config();
        }
    });
  
    
    $("#img_logo").change(function () {
        readImage(this,'#logo');
    });

    $("#img_fondo").change(function () {
        readImage(this,'#fondo');
    });
</script> 

@stop