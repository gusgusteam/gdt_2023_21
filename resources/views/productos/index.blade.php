@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark">Productos</h1>
@stop

@section('content')

    <div class="row">
      
    </div>
    <div class="row mb-2">
        <div class="col-6 text-left">
            <a class="btn btn-primary" onclick="agregar()">agregar</a>
        </div>
        <div class="col-6 text-right">
            <a class="btn btn-danger" href="{{route('producto.index',0)}}">eliminados</a>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card {{ config('adminlte.classes_index', '') }}">
                <div class="card-body ">
                    <h1 class="card-title {{ config('adminlte.classes_index_header', '') }} ">LISTA DE PRODUCTOS</h1> 
                </div>
                <div class="card-header">
                    <table id="example1" class="table table-responsive-xl table-bordered table-sm table-hover table-striped"  >
                      <thead>
                          <tr>  
                            <th width="5%"> # </th>
                            <th width="5%"> </th>
                            <th width="15%">Nombre</th>
                            <th width="20%">Descripcion</th>
                            <th width="5%">Stock</th>
                            <th width="10%">Stock minimo</th>
                            <th width="12%">Provedor</th>
                            <th width="9%">Precio venta</th>
                            <th width="9%">Precio compra</th>
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
        <div class="modal-dialog modal-xl" role="document">
          <!--Content-->
          <div class="modal-content">
            <!--Header-->
            <div class="modal-header {{ config('adminlte.classes_index_modal_agregar','') }}">
              <h5 class="modal-title">Registrar Producto</h5>
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
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="descripcion">Descripcion</label> 
                                <textarea class="form-control" id="descripcion" name="descripcion" type="text" placeholder="ingrese una descripcion" aria-describedby="descripcion-error" aria-invalid="true"></textarea>
                                <span id="descripcion-error" class="error invalid-feedback" style="display: none;"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-3">
                          <div class="form-group">
                              <label for="precio_venta">Precio venta</label> 
                              <input class="form-control" id="precio_venta" name="precio_venta" step="any" type="number" placeholder="ingrese el precio de venta" aria-describedby="precio_venta-error" aria-invalid="true"/>
                              <span id="precio_venta-error" class="error invalid-feedback" style="display: none;"></span>
                          </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                            <label for="precio_compra">Precio compra</label> 
                            <input class="form-control" id="precio_compra" name="precio_compra" step="any" type="number" placeholder="ingrese el precio de compra" aria-describedby="precio_compra-error" aria-invalid="true"/>
                            <span id="precio_compra-error" class="error invalid-feedback" style="display: none;"></span>
                        </div>
                      </div>
                      <div class="col-sm-6">
                          <div class="form-group">
                              <label for="id_categoria">Categorias</label>
                              <select class="form-control" name="id_categoria" id="id_categoria" aria-describedby="id_categoria-error" aria-invalid="true">
                                <option selected disabled value="">seleccione una categoria</option>
                                @foreach ($categorias as $categoria)
                                <option value="{{$categoria->id}}">{{$categoria->nombre}}</option>
                                @endforeach
                              </select> 
                              <span id="id_categoria-error" class="error invalid-feedback" style="display: none;"></span>
                          </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-3">
                          <div class="form-group">
                              <label for="stock">Stock</label> 
                              <input class="form-control" id="stock" name="stock" value="0" disabled type="number" placeholder="ingrese el stock" aria-describedby="stock-error" aria-invalid="true"/>
                              <span id="stock-error" class="error invalid-feedback" style="display: none;"></span>
                          </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                            <label for="stock_minimo">Stock minimo</label> 
                            <input class="form-control" id="stock_minimo" name="stock_minimo" type="number" placeholder="ingrese el stock minimo" aria-describedby="stock_minimo-error" aria-invalid="true"/>
                            <span id="stock_minimo-error" class="error invalid-feedback" style="display: none;"></span>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label for="id_provedor">Provedor</label>
                            <select class="form-control" name="id_provedor" id="id_provedor" aria-describedby="id_provedor-error" aria-invalid="true">
                              <option selected disabled value="">seleccione un provedor</option>
                            </select> 
                            <span id="id_provedor-error" class="error invalid-feedback" style="display: none;"></span>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label for="provedor">&nbsp;&nbsp;</label> 
                          <input type="text" class="form-control" id="provedor" name="provedor" placeholder="Buscar provedor por nombre" value="" onchange="buscar_por_nombre(this.value)"  autocomplete="off" />
                          <span id="provedor-error" class="error invalid-feedback" style="display: none;"></span>  
                        </div>
                      </div>
                     
                    </div>
                    <div class="row">
                      <div class="col-sm-6">
                          <div class="form-group">
                          <label for="foto_producto">Previsualizar imagen</label>
                              <div class="row col-sm-6">
                                  <img id="foto_producto" class="img-fluid" src="{{asset('img/productos/150x150.png')}}" alt="Photo" style="max-height: 160px;">
                              </div>
                          </div>
                      </div>
                    </div>
                    <div class="row" >
                      <div class="col-sm-6">
                        <div class="custom-file">
                          <div class="form-group" >
                            <input style="cursor: pointer;" type="file" id="img_producto" name="img_producto" class="custom-file-input" aria-describedby="img_producto-error" aria-invalid="true" accept="image/png" >
                            <span id="img_producto" class="error invalid-feedback" style="display: none;"></span>
                            <label class="custom-file-label align-middle" for="img_producto" data-browse="Buscar">Seleccione una foto</label>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                            <label for="inventariable">Inventariable</label>
                            <select class="form-control" name="inventariable" id="inventariable" aria-describedby="inventariable-error" aria-invalid="true">
                              <option disabled value="">es inventariable</option>
                              <option value="0">no</option>
                              <option selected value="1">si</option>
                            </select> 
                            <span id="inventariable-error" class="error invalid-feedback" style="display: none;"></span>
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
      <div class="modal-dialog modal-xl" role="document">
        <!--Content-->
        <div class="modal-content">
          <!--Header-->
          <div class="modal-header {{ config('adminlte.classes_index_modal_editar','') }}">
            <h5 class="modal-title">Editar Producto</h5>
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
                            <input class="form-control" id="Mnombre" name="Mnombre" type="text" placeholder="ingrese el nombre" aria-describedby="Mnombre-error" aria-invalid="true"/>
                            <span id="Mnombre-error" class="error invalid-feedback" style="display: none;"></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="Mdescripcion">Descripcion</label> 
                            <textarea class="form-control" id="Mdescripcion" name="Mdescripcion" type="text" placeholder="ingrese una descripcion" aria-describedby="Mdescripcion-error" aria-invalid="true"></textarea>
                            <span id="Mdescripcion-error" class="error invalid-feedback" style="display: none;"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                  <div class="col-sm-3">
                      <div class="form-group">
                          <label for="Mprecio_venta">Precio venta</label> 
                          <input class="form-control" id="Mprecio_venta" name="Mprecio_venta" step="any" type="number" placeholder="ingrese el precio de venta" aria-describedby="Mprecio_venta-error" aria-invalid="true"/>
                          <span id="Mprecio_venta-error" class="error invalid-feedback" style="display: none;"></span>
                      </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                        <label for="Mprecio_compra">Precio compra</label> 
                        <input class="form-control" id="Mprecio_compra" name="Mprecio_compra" step="any" type="number" placeholder="ingrese el precio de compra" aria-describedby="Mprecio_compra-error" aria-invalid="true"/>
                        <span id="Mprecio_compra-error" class="error invalid-feedback" style="display: none;"></span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                      <div class="form-group">
                          <label for="Mid_categoria">Categorias</label>
                          <select class="form-control" name="Mid_categoria" id="Mid_categoria" aria-describedby="Mid_categoria-error" aria-invalid="true">
                            <option selected disabled value="">seleccione una categoria</option>
                            @foreach ($categorias as $categoria)
                            <option value="{{$categoria->id}}">{{$categoria->nombre}}</option>
                            @endforeach
                          </select> 
                          <span id="Mid_categoria-error" class="error invalid-feedback" style="display: none;"></span>
                      </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-3">
                      <div class="form-group">
                          <label for="Mstock">Stock</label> 
                          <input class="form-control" id="Mstock" name="Mstock" value="0" disabled type="number" placeholder="ingrese el stock" aria-describedby="Mstock-error" aria-invalid="true"/>
                          <span id="Mstock-error" class="error invalid-feedback" style="display: none;"></span>
                      </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                        <label for="Mstock_minimo">Stock minimo</label> 
                        <input class="form-control" id="Mstock_minimo" name="Mstock_minimo" type="number" placeholder="ingrese el stock minimo" aria-describedby="Mstock_minimo-error" aria-invalid="true"/>
                        <span id="Mstock_minimo-error" class="error invalid-feedback" style="display: none;"></span>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label for="Mid_provedor">Provedor</label>
                        <select class="form-control" name="Mid_provedor" id="Mid_provedor" aria-describedby="Mid_provedor-error" aria-invalid="true">
                          <option selected disabled value="">seleccione un provedor</option>
                        </select> 
                        <span id="Mid_provedor-error" class="error invalid-feedback" style="display: none;"></span>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label for="Mprovedor">&nbsp;&nbsp;</label> 
                      <input type="text" class="form-control" id="Mprovedor" name="Mprovedor" placeholder="Buscar provedor por nombre" value="" onchange="buscar_por_nombre2(this.value)"  autocomplete="off" />
                      <span id="Mprovedor-error" class="error invalid-feedback" style="display: none;"></span>  
                    </div>
                  </div>
                  
                </div>
                <div class="row">
                  <div class="col-sm-6">
                      <div class="form-group">
                      <label for="Mfoto_producto">Previsualizar imagen</label>
                          <div class="row col-sm-6">
                              <img id="Mfoto_producto" class="img-fluid" src="" alt="Photo" style="max-height: 160px;">
                          </div>
                      </div>
                  </div>
                </div>
                <div class="row" >
                  <div class="col-sm-6">
                    <label for="Mimg_producto">&nbsp;&nbsp;</label> 
                    <div class="custom-file">
                      <div class="form-group" >
                        
                        <input style="cursor: pointer;" type="file" id="Mimg_producto" name="Mimg_producto" class="custom-file-input" aria-describedby="Mimg_producto-error" aria-invalid="true" accept="image/png" >
                        <span id="Mimg_producto" class="error invalid-feedback" style="display: none;"></span>
                        <label class="custom-file-label align-middle" for="Mimg_producto" data-browse="Buscar">Seleccione una foto</label>
                      </div>
                    </div>
                  </div>  
                  <div class="col-sm-3">
                    <div class="form-group">
                        <label for="Minventariable">Inventariable</label>
                        <select class="form-control" name="Minventariable" id="Minventariable" aria-describedby="Minventariable-error" aria-invalid="true">
                          <option selected disabled value="">es inventariable</option>
                          <option value="0">no</option>
                          <option value="1">si</option>
                        </select> 
                        <span id="Minventariable-error" class="error invalid-feedback" style="display: none;"></span>
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
      ajax: "{{route('producto.datos2',1)}}",
      columns: [
          {data: 'id',searchable: true,orderable: false},
          {data: 'foto',searchable: true},
          {data: 'nombre',searchable: true,orderable: false},
          {data: 'descripcion',searchable: false},
          {data: 'stock',searchable: false},
          {data: 'stock_minimo',searchable: false},
          {data: 'nombre_provedor',searchable: true},
          {data: 'precio_venta',searchable: false},
          {data: 'precio_compra',searchable: false},
          {data: 'estado',searchable: false},
          {data: 'actions',searchable: false,orderable: false}
      ],
    })
</script>
<script>
    
    $('.select2').select2();
  
    function agregar(){
      clear_frm_agregar();
      document.getElementById("frm_agregar").reset(); 
      $('#modal_agregar').modal('show');  
     
      
    }

    function mostrarerror(title,icono,error){
      Swal.fire({
        title: title,
        text: error,
        icon: icono,
        confirmButtonText: 'okey'
      })
    }

    function almacenar(){
      var link="{{route('producto.store')}}";
      $.ajax({
          url: link,
          type: "POST",
          processData: false,
          contentType: false,
          data: new FormData($('#frm_agregar')[0]),    
          success:function(response){
            if (response.error==1){
                  toastr.error(response.mensaje, 'Guardar Registro', {timeOut:3000});
               }else{
                  $("#example1").DataTable().ajax.reload();
                  toastr.success('El registro fue guardado correctamente.', 'Guardar Registro', {timeOut:3000}); 
                  $('#modal_agregar').modal('hide');
                  
                  //setTimeout(redirigir, '3000');
               }
          }
      })
    }

    function Modificar(id){
      clear_frm_editar();
      $.ajax({
        url:"{{asset('')}}"+"producto/show/"+id, dataType:'json',
        success: function(resultado){
          $("#id_registro").val(resultado.producto.id);
          $("#Mnombre").val(resultado.producto.nombre); 
          $("#Mdescripcion").val(resultado.producto.descripcion);
          $("#Mstock").val(resultado.producto.stock);
          $("#Mstock_minimo").val(resultado.producto.stock_minimo);
          $("#Mprecio_venta").val(resultado.producto.precio_venta);
          $("#Mprecio_compra").val(resultado.producto.precio_compra);
          $("#Minventariable").val(resultado.producto.inventariable);
          $("#Mid_categoria").val(resultado.producto.id_categoria);
          $('#Mid_provedor').empty();
          $('#Mid_provedor').append($('<option  />', {
                  text: resultado.provedor,
                  value: resultado.id_provedor,
                  selected: true,
          }));
          //$("#Mid_provedor").val(resultado.producto.id_provedor);
          $("#Mfoto_producto").attr("src",resultado.foto);
          $('#modal_editar').modal('show');
        }
      });
    }

    function actualizar(){
      var id = $("#id_registro").val();
      var link="{{asset('')}}"+"producto/update/"+id;
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
                  $("#example1").DataTable().ajax.reload();
                  toastr.success('El registro fue actualizado correctamente.', 'Guardar Registro', {timeOut:3000}); 
                  $('#modal_editar').modal('hide');
                  //setTimeout(redirigir, '1000');
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
          var link="{{asset('')}}"+"producto/destroy/"+id;
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
            $("#example1").DataTable().ajax.reload();
           // setTimeout(redirigir, '0000');
          })
          
        }
      })
    }

     
  
    function buscar_por_nombre(nombre){
     // e=jQuery.Event("keypress");
      if(nombre!=''){
        var url='{{asset('')}}provedor/autocompleteData'+'/'+nombre;
        $.ajax({
            url: url,
            method:"GET",dataType: 'json',
            success: function(resultado){
              $('#id_provedor').empty();
              $('#id_provedor').append($('<option  />', {
                  text: "seleccione un provedor",
                  value: null,
                  //selected: true,
              }));
              resultado.forEach(function(elemento, indice, array) {
              $('#id_provedor').append($('<option  />', {
                  text: elemento.nombre,
                  value: elemento.id,
                  //selected: true,
              }));
              })
            }
        });  
      }
    }
    function buscar_por_nombre2(nombre){
     // e=jQuery.Event("keypress");
      if(nombre!=''){
        var url='{{asset('')}}provedor/autocompleteData'+'/'+nombre;
        $.ajax({
            url: url,
            method:"GET",dataType: 'json',
            success: function(resultado){
              $('#Mid_provedor').empty();
              $('#Mid_provedor').append($('<option  />', {
                  text: "seleccione un provedor",
                  value: null,
                  //selected: true,
              }));
              resultado.forEach(function(elemento, indice, array) {
              $('#Mid_provedor').append($('<option  />', {
                  text: elemento.nombre,
                  value: elemento.id,
                  //selected: true,
              }));
              })
            }
        });  
      }
    }

   

    $('#frm_agregar').validate({
        rules: {
          nombre: {
            required: true,
          },
          descripcion: {
            required: true,
          },
          stock_minimo: {
            required: true,
          },
          precio_compra: {
            required: true,
            minlength: 0
          },
          precio_venta: {
            required: true,
            minlength: 0
          },
          img_producto: {
            required: false,
          },
          id_categoria: {
            required: true,
          },
          inventariable: {
            required: true,
          },
        },
        messages: {
          nombre: {
            required: "Por favor, introduzca su nombre",
          },
          descripcion: {
            required: "Por favor, ingrese una descripcion",
          },
          stock_minimo: {
            required: "Por favor, ingrese el stock minimo",
          },
          precio_compra: {
            required: "Por favor, ingrese el precio de compra",
          },
          precio_venta: {
            required: "Por favor, ingrese el precio de venta",
          },
          inventariable: {
            required: "Por favor, seleccione si es inventariable",
          },
          id_categoria: {
            required: "Por favor, seleccione una categoria",
          },
          img_producto: {
            required: "Por favor, es necesario una imagen",
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
          Mdescripcion: {
            required: true,
          },
          Mstock_minimo: {
            required: true,
          },
          Mprecio_compra: {
            required: true,
            minlength: 0
          },
          Mprecio_venta: {
            required: true,
            minlength: 0
          },
          Mimg_producto: {
            required: false,
          },
          Mid_categoria: {
            required: true,
          },
          Minventariable: {
            required: true,
          },
        },
        messages: {
          Mnombre: {
            required: "Por favor, introduzca su nombre",
          },
          Mdescripcion: {
            required: "Por favor, ingrese una descripcion",
          },
          Mstock_minimo: {
            required: "Por favor, ingrese el stock minimo",
          },
          Mprecio_compra: {
            required: "Por favor, ingrese el precio de compra",
          },
          Mprecio_venta: {
            required: "Por favor, ingrese el precio de venta",
          },
          Minventariable: {
            required: "Por favor, seleccione si es inventariable",
          },
          Mid_categoria: {
            required: "Por favor, seleccione una categoria",
          },
          Mimg_producto: {
            required: "Por favor, es necesario una imagen",
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

    
    $("#img_producto").change(function () {
        readImage(this,'#foto_producto');
    });
    $("#Mimg_producto").change(function () {
        readImage(this,'#Mfoto_producto');
    });

</script>


    
@stop