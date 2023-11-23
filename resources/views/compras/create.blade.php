@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark"></h1>
@stop

@section('content')
    
    <div class="row">
        <div class="col-12">
            <div class="card {{ config('adminlte.classes_index', '') }}">
                <div class="card-body ">
                    <h1 class="card-title {{ config('adminlte.classes_index_header', '') }} ">NUEVA COMPRA</h1> 
                </div>
                <div class="card-header">
                    <div class="row">
                      <div id="frm" class="modal-body">
                       
                            <input type="hidden" id="id_producto">
                            <input type="hidden" id="id_compra" value="{{$key}}">
                            <div class="row">
                                <div class="col-sm-4">
                                  <label >Producto</label> 
                                  <div class="input-group input-group-sm mb-0 eliminarbtn">
                                      <input type="text" class="form-control form-control-sm" id="nombre" name="nombre" data-toggle="tooltip" data-placement="bottom" title="Producto" disabled>
                                      <span class="input-group-append">
                                          <button type="button" class="btn btn-info btn-flat" title="Lista de producto" onclick="mostrar_productos()"><i class="fas fa-list-ol"></i></button>
                                      </span>
                                  </div>
                                  <div class="row mb-0 px-2">
                                      <small for="codigo" id ="resultado_error" class="text-danger"></small>
                                  </div>
                                </div>
                                <div class="col-sm-4">
                                  <div class="form-group">
                                    <label for="descripcion">descripcion</label> 
                                    <textarea disabled class="form-control form-control-sm" id="descripcion" name="descripcion" type="text"></textarea>
                                  </div>
                                </div>
                                <div class="col-sm-4">
                                  <div class="form-group">
                                    <label for="id_almacen">almacen</label> 
                                    <select class="form-control form-control-sm" id="id_almacen" name="id_almacen" >
                                      <option disabled selected value="">seleccione un almacen</option>
                                      @foreach ($almacenes as $almacen)
                                          @if ($almacen->id==1)
                                            <option selected value="{{$almacen->id}}">{{$almacen->nombre}}</option>
                                          @else
                                            <option value="{{$almacen->id}}">{{$almacen->nombre}}</option>  
                                          @endif
                                          
                                      @endforeach
                                    </select>
                                  </div>
                                </div>
                            </div>
                            <div class="row">
                              <div class="col-sm-2">
                                <div class="form-group">
                                  <label for="precio_compra">Precio de compra</label> 
                                  <input disabled class="form-control form-control-sm" id="precio_compra" name="precio_compra" type="text">
                                </div>
                              </div>
                              <div class="col-sm-2">
                                <div class="form-group">
                                  <label for="cantidad">Cantidad</label> 
                                  <input  class="form-control form-control-sm" id="cantidad" name="cantidad" min="1" onkeyup="Calcula_cantidad_subtotal(id_producto.value,this.value)" type="number">
                                </div>
                              </div>
                              <div class="col-sm-4">
                                <div class="form-group">
                                  <label for="subtotal">Sub Total</label> 
                                  <input class="form-control form-control-sm" id="subtotal" name="subtotal" step="any" type="number">
                                </div>
                              </div>
                              <div class="col-sm-4">
                                <div class="form-group">
                                  <label for="">&nbsp;</label>
                                  <div class="text-right">
                                    <a  onclick="agregarProducto(id_compra.value,id_producto.value,id_almacen.value,cantidad.value)" class="btn btn-primary btn-sm">Agregar Producto</a>
                                  </div>
                                </div>
                              </div>
                            </div>
                      
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-12">
                        <table id="tablaProductos" class="table table-responsive-sm table-bordered table-sm table-hover table-striped"  >
                          <thead>
                              <tr>  
                                <th width="5%"> # </th>
                                <th width="20%">Producto</th>
                                <th width="20%">Almacen</th>
                                <th width="10%">Precio compra</th>
                                <th width="10%">Cantidad</th>
                                <th width="19%">Sub total</th>
                                <th width="1%">Acción</th>
                              </tr>
                          </thead>  
                          <tbody>
                          
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <div class="row justify-content-between">
                      <div class="col-sm-6 text-left">
                      </div>   
                      <div class="col-sm-6 text-right">
                        <label style="font-weight: bold; font-size: 30px; text-align: center;">Total Bs.</label>
                        <input type="text" id="total" name="total" size="6" readonly="true" value="0.00" style="font-weight: bold; font-size: 30px; text-align: center;"/>
                      </div>   
                    </div>
                    <div class="row">
                      <div class="col-sm-12 text-center">
                        <a class="btn btn-success" onclick="guardar_compra(id_compra.value)" id="completa_compra">Completar compra</a>
                      </div>
                    </div>
                    
                    
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modal_productos" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;" data-gtm-vis-first-on-screen-2340190_1302="5621" data-gtm-vis-total-visible-time-2340190_1302="100" data-gtm-vis-has-fired-2340190_1302="1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
          <!--Content-->
          <div class="modal-content">
            <!--Header-->
            <div class="modal-header {{ config('adminlte.classes_index_modal_agregar','') }}">
              <h5 class="modal-title">Lista Productos</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true" class="white-text">×</span>
              </button>
            </div>
            <div id="frm" class="modal-body">
              <table  id="example3" class="table table-responsive table-bordered table-sm table-hover table-striped ">
                <thead>
                    <tr>
                      <th width="10%">Imagen</th>
                      <th width="30%">Nombre</th>
                      <th width="50%">Descripción</th>      
                      <th width="5%">stock</th>
                      <th width= "5%"></th>    
                    </tr>
                </thead>
                <tbody>
                  
                </tbody>
            </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-info btn-sm" data-dismiss="modal">Cerrar lista</button>
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
    $('#tablaProductos').DataTable({ 
      language: {url: languages['es']},
      destroy: true,
      autoWidth: false,
      retrieve: true,
      cache: false,
      filter: false,
      info: false,
      lengthChange: false,
      bSort: false,
      paging: false,
      ordering: false,
      responsive: r,
    })


    var languages = {'es': '{{asset('vendor/adminlte/Spanish.json')}}'};
    $('#example3').DataTable({ 
      language: {url: languages['es']},
      destroy: true,
      retrieve: true,
      serverSide: true,
      autoWidth: false,
      responsive: r,
      ajax: "{{route('producto.datos2',1)}}",
      columns: [
         
          {data: 'foto',searchable: true},
          {data: 'nombre',searchable: true,orderable: false},
          {data: 'descripcion',searchable: true},
          {data: 'stock',searchable: false},
         
          {data: 'btn_compra',searchable: false}
      ],
    })

  
</script>
<script>
    function mostrar_productos(){
      $("#example3").DataTable().ajax.reload();
      $('#modal_productos').modal('show');
    }

    function datos_producto(id_producto){
      var url='{{asset('')}}producto/datos/'+id_producto;
        $.ajax({
             url: url,
            success: function(resultado){
                var resultado= JSON.parse(resultado);
                // alert(resultado.datos.nombre);
                $("#id_producto").val(resultado.datos.id);
                $("#nombre").val(resultado.datos.nombre);
                $("#descripcion").val(resultado.datos.descripcion);
                $("#precio_compra").val(resultado.datos.precio_compra);
                $('#modal_productos').modal('hide');
              //  document.getElementById('cantidad').disabled=false;
            },
        });
    }

    function Calcula_cantidad_subtotal(codigo,cantidad){
      if (codigo != '' && cantidad>0 ) {
        var id_producto=codigo;
        var url='{{asset('')}}compra/calcular_subtotal_producto/'+id_producto+'/'+cantidad;
          $.ajax({
              url: url, dataType: 'json',
              success: function(resultado) {
                $("#subtotal").val((resultado).toFixed(2));  
              }
          });    
      }else{
        $("#subtotal").val("");  
      }
    }
actualizar_tabla();
    function actualizar_tabla(){
      var codigo= $('#id_compra').val();
      var url='{{asset('')}}TemporalCompra/datos/'+codigo;
              $.ajax({
                  url:url,dataType: 'json', 
                  success: function(resultado){
                 
                      $("#tablaProductos tbody").empty();
                      $("#tablaProductos tbody").append(resultado.datos);
                      $("#total").val(resultado.total);
                      $("#id_producto").val('');
                      //$("#id_almacen").val('');
                      $("#cantidad").val('');
                      $("#nombre").val('');
                      $("#descripcion").val('');
                      $("#subtotal").val('');
                      $("#precio_compra").val('');
                    
                   
                  }
              });
    }

    function agregarProducto(codigo,id_producto,id_almacen,cantidad) { 
    if(codigo !=''){
      if (id_producto != null && id_producto != 0 && id_almacen != null && id_almacen !=0 ) {
          if(cantidad > 0 && cantidad!='')
          {
            var url='{{asset('')}}TemporalCompra/insertar/'+id_producto+'/'+id_almacen+'/'+cantidad+'/'+codigo;
              $.ajax({
                  url:url,dataType: 'json', 
                  success: function(resultado){
                    if(resultado.error==0){
                      $("#tablaProductos tbody").empty();
                      $("#tablaProductos tbody").append(resultado.datos);
                      $("#total").val(resultado.total);
                      $("#id_producto").val('');
                      //$("#id_almacen").val('');
                      $("#cantidad").val('');
                      $("#nombre").val('');
                      $("#descripcion").val('');
                      $("#subtotal").val('');
                      $("#precio_compra").val('');
                    //  document.getElementById('cantidad').disabled=;
                    }else{

                    }
                  }
              });
          }
          else { mostrarerror('advertencia!','warning','Agrege una cantidad');} 
      }else { mostrarerror('error!','error','por favor el producto y almacen son necesarios');}
    }else{mostrarerror('error!','error','Problemas de la pagina de espera recarge de nuevo');}
    }

  
    function actualizarProducto(id_producto,id_almacen,codigo,cantidad) {
      var url='{{asset('')}}TemporalCompra/actualizar/'+id_producto+'/'+id_almacen+'/'+codigo+'/'+cantidad;
        $.ajax({
            url: url,dataType: 'json', 
            method:"GET",
            success: function(resultado){
              $("#tablaProductos tbody").empty();
              $("#tablaProductos tbody").append(resultado.datos);
              $("#total").val(resultado.total);
            }
        });
    }

    function guardar_compra(codigo){
      var url='{{asset('')}}TemporalCompra/guardar/'+codigo;
        $.ajax({
            url: url,dataType: 'json', 
            method:"GET",
            success: function(resultado){
              if(resultado.error==1){
                mostrarerror('error!','error',resultado.mensaje);
              }else{
              $("#tablaProductos tbody").empty();
              $("#total").val('');
              toastr.success('Su compra fue guardado correctamente.', 'Completar compra', {timeOut:2000}) ;  
              }
            }
        });
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