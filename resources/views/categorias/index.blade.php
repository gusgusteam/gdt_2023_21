@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark">Categorias</h1>
@stop

@section('content')
    <div class="row mb-2">
        <div class="col-6 text-left">
            <a class="btn btn-primary" onclick="agregar()">agregar</a>
        </div>
        <div class="col-6 text-right">
            <a class="btn btn-danger" href="{{route('categoria.index',0)}}">eliminados</a>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card {{ config('adminlte.classes_index', '') }}">
                <div class="card-body ">
                    <h1 class="card-title {{ config('adminlte.classes_index_header', '') }} ">LISTA DE CATEGORIAS</h1> 
                </div>
                <div class="card-header">
                    <table id="example1" class="table table-responsive-xl table-bordered table-sm table-hover table-striped"  >
                      <thead>
                          <tr>  
                            <th width="5%"> # </th>
                            <th width="75%">Nombre</th>
                            <th width="10%"><span class="badge bg-primary">Estado</span></th>
                            <th width="10%">Acción</th>
                          </tr>
                      </thead>  
                      <tbody>
                        @php
                            $c=1;
                            $css_btn_edit= config('adminlte.classes_btn_editar') ;
                            $css_btn_delete= config('adminlte.classes_btn_eliminar') ;
                        @endphp
                        @foreach ($categorias as $categoria)
                        <tr>
                          <td>{{$c}}</td>
                          <td>{{$categoria->nombre}}</td>
                          <td><span class="badge bg-success">activo</span></td>
                          <td class="text-right">
                            <div class="btn-group btn-group-sm">  
                              <a class="btn {{$css_btn_edit}}" rel="tooltip" data-placement="top" title="Editar" onclick="Modificar({{$categoria->id}})" ><i class="far fa-edit"></i></a>
                              <a class="btn {{$css_btn_delete}}" rel="tooltip" data-placement="top" title="Eliminar" onclick="Eliminar({{$categoria->id}})"><i class="far fa-trash-alt"></i></a>
                            </div>
                          </td>
                        </tr>
                        @php $c = $c + 1; @endphp
                        @endforeach
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
              <h5 class="modal-title">Registrar Categoria</h5>
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
                                <input class="form-control" id="nombre" name="nombre" type="text" placeholder="ingrese el nombre" aria-describedby="nombre-error" aria-invalid="true"/>
                                <span id="nombre-error" class="error invalid-feedback" style="display: none;"></span>
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
            <h5 class="modal-title">Editar Categoria</h5>
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
                              <input class="form-control"  id="Mnombre" name="Mnombre" type="text" placeholder="ingrese el nombre" aria-describedby="Mnombre-error" aria-invalid="true"/>
                              <span id="Mnombre-error" class="error invalid-feedback" style="display: none;"></span>
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
      autoWidth: false,
      responsive: r,
    })
</script>
<script>
    
  
    function agregar(){
      clear_frm_agregar();
      $('#modal_agregar').modal('show');
    }

    function almacenar(){
      var link="{{route('categoria.store')}}";
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
                  setTimeout(redirigir, '3000');
               }
          }
      })
    }

    function Modificar(id){
      clear_frm_editar();
      $.ajax({
        url:"{{asset('')}}"+"categoria/show/"+id, dataType:'json',
        success: function(resultado){
          $("#id_registro").val(resultado.categoria.id);
          $("#Mnombre").val(resultado.categoria.nombre); 
          $('#modal_editar').modal('show');
        }
      });
    }

    function actualizar(){
      var id = $("#id_registro").val();
      var link="{{asset('')}}"+"categoria/update/"+id;
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
                  setTimeout(redirigir, '0000');
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
          var link="{{asset('')}}"+"categoria/destroy/"+id;
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
          ).then((result)=>{
            setTimeout(redirigir, '0000');
          })
          
        }
      })
    }


    $('#frm_agregar').validate({
        rules: {
          nombre: {
            required: true,
          },
        },
        messages: {
          nombre: {
            required: "Por favor, introduzca su nombre",
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
            required: true,
          },
        },
        messages: {
          Mnombre: {
            required: "Por favor, es necesario que tenga un nombre",
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