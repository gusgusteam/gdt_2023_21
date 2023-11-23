
@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark">Nota de venta</h1>
@stop

@section('content')
      <input type="hidden" id="id_venta" value="{{$key}}">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-3">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <input id="check_descripcion" type="checkbox">
                </div>
              </div>
              <input id="dato_descripcion" type="text" class="form-control" placeholder="busqueda por descripcion del producto">
            </div>
          </div>
          <div class="col-sm-3">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <input id="check_nombre" type="checkbox" >
                </div>
              </div>
              <input id="dato_nombre" type="text" class="form-control" placeholder="busqueda por nombre del producto">
            </div>
          </div>
          <div class="col-sm-2">
            <label class="mr-sm-2 sr-only" for="categoria">Categoria</label>
            <select class="custom-select mr-sm-2" id="categoria">
              <option selected  value="-1">todos...</option>
              @foreach ($categorias as $row)
                <option value="{{$row->id}}">{{$row->nombre}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-sm-1">
           <a onclick="productos_lista()" class="btn btn-primary">Buscar</a>
          </div>
          <div class="col-sm-3">
            <div class="small-box bg-white">
              <div class="inner">
                <h4 id="total_venta" >Total = <b  class="text-success" id="monto_total_venta"></b></h4>
                <p id="cantidad_producto">Total de productos&nbsp;&nbsp;</p>
              </div>
              <div class="icon">
                <i class="fab fa-product-hunt"></i>
              </div>
              <a href="{{asset('TemporalVenta/show')}}" class="small-box-footer">&nbsp;&nbsp;DETALLE DE VENTA <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>
      </div>
      <br>
      <div class="container-fluid">
        <div id="cc"  class="row row-cols-2 row-cols-sm-2  row-cols-md-3 row-cols-lg-6  g-3">
                       
        </div>
      </div>
      
  @stop

  @section('js')



  <script >

    productos_lista();
    function productos_lista(){
      var descripcion='-1';
      var nombre='-1';
      var id_categoria=-1;
      if ($("#check_descripcion").is(":checked") && $("#dato_descripcion").val().trim().length > 0) {
        // el checkbox está seleccionado
        descripcion=$('#dato_descripcion').val();
      } 
      if($("#check_nombre").is(":checked") && $("#dato_nombre").val().trim().length > 0){
        nombre=$('#dato_nombre').val();
      }
      id_categoria=$('#categoria').val();
   
        var url='{{asset('')}}venta/new/'+nombre+'/'+descripcion+'/'+id_categoria;
          $.ajax({
              url: url,
              method:"GET",dataType: 'json',
              success: function(resultado){
              //document.getElementById("cc").insertAdjacentHTML("afterend",resultado);
                document.getElementById("cc").innerHTML = resultado;
               // $('#cc').appendChild(resultado);
              }
        });
      
    }

    update_tabla();
    $('.select2').select2();
   
    function agregarProducto(codigo,id_producto,id_almacen) { 
      if(codigo !=''){
        if (id_producto != null && id_producto != 0 && id_almacen != null && id_almacen !=0 ) {
          Swal.fire({
            title: 'Cantidad Producto',
            input: 'text',
            inputAttributes: {
              autocapitalize: 'off'
            },
            showCancelButton: false,
            confirmButtonText: 'ok',
            showLoaderOnConfirm: true,
            preConfirm: (valor) => {
              cant = parseInt(valor)
            
              if(isNaN(cant)==false){
                var url='{{asset('')}}TemporalVenta/insertar/'+id_producto+'/'+id_almacen+'/'+codigo+'/'+cant;
                  $.ajax({
                      url:url,dataType: 'json', 
                      success: function(resultado){
                        if(resultado.error==1){
                          mostrarerror('error!','error',resultado.mensaje);
                        }else{
                          update_tabla()
                         // update_temporal(resultado.datos,resultado.total,resultado.cant);
                        }
                       // update_tabla();
                      }
                  });
              }else{
                mostrarerror('error!','error',"el dato requerido debe ser numero");
              }
            }
          })
        }else { mostrarerror('error!','error','por favor el producto y almacen son necesarios');}
      }else{mostrarerror('error!','error','Problemas de la pagina de espera recarge de nuevo');}
    }

    function update_tabla(){
      var cod= $('#id_venta').val();
      var url='{{asset('')}}TemporalVenta/datos/'+ cod ;
        $.ajax({
            url: url,
            method:"GET",dataType: 'json',
            success: function(resultado){
             // $('#total_venta').text('total = '+resultado.total+' bs');
              $('#monto_total_venta').text(resultado.total+' bs');
              $('#cantidad_producto').text('cantidad = '+resultado.cant);
              $('#contador_venta').empty();
              var text2 = document.createTextNode(resultado.cant); 
              if(resultado.cant==0){
                $("#btn_carrito").removeClass("badge-white");
                $("#btn_carrito").addClass("badge-light");
              }else{
                $("#btn_carrito").removeClass("badge-light");
                $("#btn_carrito").addClass("badge-success");
              }   
              document.getElementById("contador_venta").appendChild(text2);
            }
      });
    }


    function redirigir(){
      window.location.href ='{{url('')}}/venta';
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