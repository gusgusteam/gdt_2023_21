
@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark">Detalle de venta</h1>
@stop

@section('content')
<input type="hidden" value="{{session('monto_cliente')}}">
        <input type="hidden" id="id_venta" value="{{$key}}">
        <div class="row">
          <div class="col-sm-12">
            <div class="custom-control custom-switch">
              <input class="custom-control-input" type="checkbox" id="modo_cliente" checked value ="0" name= "modo_cliente" onclick="ModoCliente(this.value)"/>
              <label class="custom-control-label font-weight-normal" for="modo_cliente">publico general</label>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-sm-4  mb-3">
            <div class="ui-widget">
                <label  class="form-label">Cliente:  </label>
                <input type="hidden" id="id_cliente" name="id_cliente" value=""/>
                <input type="text" disabled class="form-control" id="cliente" name="cliente" placeholder="Escribe el nombre del cliente" value="" onblur="compruebaValidoEntero2(this.value)" onkeyup="buscar(event,this.value)" autocomplete="off" />
                <span id="cliente-error" class="error invalid-feedback" style="display: none;"></span>
            </div>
          </div>
          <div class="col-12 col-sm-4  mb-3">
              <label  class="form-label">Monto:</label>
              <input type="number" step="any" min="1" class="form-control" id="monto_cliente" name="monto_cliente" placeholder="Monto que va pagar el cliente" value="{{$monto}}" onblur="compruebaValidoEntero(this.value)" onkeyup="Guardar_monto_ssesion(this.value)"  autocomplete="off" />
              <span id="monto_cliente-error" class="error invalid-feedback" style="display: none;"></span>
          </div>
          <div class="col-12 col-sm-2  mb-3">
            <label  class="form-label">Tipo pago</label>
            <select class="form-control" name="tipo_pago" id="tipo_pago">
              <option selected value="1">Contado</option>
              <option value="0">Credito</option>
            </select>
          </div>
          <div class="col-12 col-sm-2  mb-3">
            <label class="form-label">Descuento</label>
            <input type="number" step="any" min="1" class="form-control" id="descuento" name="descuento" placeholder="" value="" onblur="" onkeyup=""  autocomplete="off" />
          </div>
        </div>
        <div class="row">
            <div class="col-12">
              <div class="card card-outline card-success">
                <div class="card-header">
                    <h5 class="text-center">lista de productos</h5>
                </div>

                    <div class="card-body">
                        
                        <div class="d-flex justify-content-end">
                            <div class="form-group">
                                
                              <button class="btn btn-danger btn-sm" type ="button" onclick="destroy_tabla()" title="Eliminar"><i class="far fa-trash-alt"></i>&nbsp;Limpiar</button>
                                
                            </div>
                        </div>
                        
                    
                    <table id="tablaProductos" class="table table-responsive-xl table-bordered table-sm table-hover table-striped">
                      <thead class="text-center">
                          <tr>
                            <th width="5%" > Nro </th>
                            <th width="25%">nombre</th>
                            <th width="20%">almacen</th>
                            <th width="15%">Precio</th>
                            <th width="15%">Cantidad</th>
                            <th width="12%">Subtotal</th>
                            <th width="8%"></th>
                          </tr>
                      </thead>
                      <tbody >
                      </tbody>
                     </table>
                      <br>
                      <div class="row mb-0">
                        <div class="col">
                          <div class="d-flex justify-content-left">
                            <div class="form-group mb-0">
                              <div class="card mb-0">
                                <ul class="list-group list-group-flush">
                                  <li class="list-group-item"><b>Cantidad Productos:</b> <span style="font-weight: bold; font-size: 20px; text-align: center;" id="cant_total" name="cant_total" class="info-box-number"></span> </li>
                                </ul>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col">
                          <div class="d-flex justify-content-end">
                            <div class="form-group  mb-0">
                              <div class="card shadow-ms mb-0">
                                <ul class="list-group list-group-flush">
                                  <li class="list-group-item"><b>Monto Total: </b> <span style="font-weight: bold; font-size: 30px; text-align: center;" id="monto_total" name="monto_total" class="info-box-number"></span></li>
                                </ul>
                              </div>
                            </div>
                          </div>
                        </div>
                        
                      </div><!-- /.row -->
                      <div class="row">
                        <div class="col">
                            <div class="text-center">
                                <button class="btn btn-success btn-flat" type="button" id="completa_pedido" onclick="guardar_venta()">Completar Venta</button>
                            </div>
                        </div>
                      </div>
                    </div>
                    <!-- /.card-body -->
              </div>
            </div>
        </div>
  @stop

  @section('js')


  <script >
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
              //cant = parseInt(document.getElementById('swal-input1').value)
              //var cant=document.getElementById('swal-input1').value;
              if(isNaN(cant)==false){
                var url='{{asset('')}}TemporalVenta/insertar/'+id_producto+'/'+id_almacen+'/'+codigo+'/'+cant;
                  $.ajax({
                      url:url,dataType: 'json', 
                      success: function(resultado){
                        if(resultado.error==1){
                          mostrarerror('error!','error',resultado.mensaje);
                        }else{
                          update_temporal(resultado.datos,resultado.total,resultado.cant);
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

    function actualizarProducto(id_producto,id_almacen,codigo,cantidad) {
      var url='';
      if(cantidad>0){
        url='{{asset('')}}TemporalVenta/aumentar/'+id_producto+'/'+id_almacen+'/'+codigo+'/'+cantidad;
      }else{
        cantidad=cantidad*-1;
        url='{{asset('')}}TemporalVenta/reducir/'+id_producto+'/'+id_almacen+'/'+codigo+'/'+cantidad;
      }
        $.ajax({
            url: url,dataType: 'json', 
            method:"GET",
            success: function(resultado){
              if(resultado.error==1){
                mostrarerror('error!','error',resultado.mensaje); 
              }else{
                update_temporal(resultado.datos,resultado.total,resultado.cant);
              }
            }
      });
    }

    function eliminarProducto(id_producto,id_almacen,codigo) {
      var url='{{asset('')}}TemporalVenta/eliminar/'+id_producto+'/'+id_almacen+'/'+codigo;
        $.ajax({
            url: url,dataType: 'json', 
            method:"GET",
            success: function(resultado){
              if(resultado.error==1){
                mostrarerror('error!','error',resultado.mensaje); 
              }else{
                update_temporal(resultado.datos,resultado.total,resultado.cant);
              }
            }
      });
    }

    function modificarCantidad(id_almacen,id_producto,id_input,codigo){
      var d='#iden'+id_input; 
      var cant= $(d).val();
      var url='{{asset('')}}TemporalVenta/updatecantidad/'+id_producto+'/'+id_almacen+'/'+codigo+'/'+cant;
        $.ajax({
            url: url,
            method:"GET",dataType: 'json',
            success: function(resultado){
              if(resultado.error==1){
                
                mostrarerror('error!','error',resultado.mensaje); 
              }else{
                toastr.success('cantidad modificada del producto','Modificacion Cantidad',{timeOut:0500});
              }
              update_temporal(resultado.datos,resultado.total,resultado.cant);
            }
        });
    }

    function update_temporal(datos_temporal,total_monto,cantidad_productos){
      $("#tablaProductos tbody").empty();
      $("#tablaProductos tbody").append(datos_temporal);
      $('#cant_total').empty();
      $('#monto_total').empty();
      $('#contador_venta').empty();
      var total= total_monto + " bs";
      var cant= cantidad_productos ;
      var text = document.createTextNode(cant);
      var text2 = document.createTextNode(total);    
      var text3 = document.createTextNode(cant); 
      document.getElementById("cant_total").appendChild(text);
      document.getElementById("monto_total").appendChild(text2);
      if(cantidad_productos==0){
        $("#btn_carrito").removeClass("badge-white");
        $("#btn_carrito").addClass("badge-light");
      }else{
        $("#btn_carrito").removeClass("badge-light");
        $("#btn_carrito").addClass("badge-success");
      }   
      document.getElementById("contador_venta").appendChild(text3);
    }

    function update_tabla(){
      var cod= $('#id_venta').val();
      var url='{{asset('')}}TemporalVenta/datos/'+ cod ;
        $.ajax({
            url: url,
            method:"GET",dataType: 'json',
            success: function(resultado){
              update_temporal(resultado.datos,resultado.total,resultado.cant);
            }
      });
    }

    function ModoCliente(value){
      // 0 publico general 1 cliente
      if (value==1) {
        $("#modo_cliente").val("0"); 
        $('#cliente').prop('disabled', true);
        

      }else{
        $("#modo_cliente").val("1");  
        $('#cliente').prop('disabled', false);
      }
    }

    function guardar_venta() { 
        let url='{{asset('')}}TemporalVenta/guardar';
        //let url = '{{url('')}}/venta/store';
        Swal.fire({
            title: '¿Desea Concluir la Venta?',
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
                if($('#descuento').val()>=0){
                $.ajax({
                url: url,dataType: 'json',
                method: "post",
                data: {
                    "tipo_pago"       : $('#tipo_pago').val(),
                    "descuento"       : $('#descuento').val(),
                    "modo"            : $('#modo_cliente').val(),
                    "id_cliente"      : $('#id_cliente').val(),
                    "codigo"          : $('#id_venta').val(),
                    "_token"          :"{{ csrf_token() }}",
                },
                success: function(resultado){
                  //var resultado= JSON.parse(resultado);
                  if(resultado.error==0){
                    $("#tablaProductos tbody").empty();
                    $('#cant_total').empty();
                    $('#monto_total').empty();
                    // PARA VACIAR EL MONTO DEL CLIENTE 
                    $('#monto_cliente').val(null);
                    $("#monto_cliente-error").css("display", "none");
                    $("#monto_cliente").removeClass('is-valid');
                    destroy_tabla();
                    toastr.success('Venta realizada correctamente','Completar venta',{timeOut:0500});
                  }else{
                    mostrarerror('error!','error',resultado.mensaje);
                  } 
                },
                });
              }else{
                mostrarerror('error!','error','el descuento no puede ser negativo');
              }

          
              } else if (result.isDenied) {
                if($('#descuento').val()>=0){
                $.ajax({
                url: url,dataType: 'json',
                method: "post",
                data: {
                    
                    "tipo_pago"       : $('#tipo_pago').val(),
                    "descuento"       : $('#descuento').val(),
                    "modo"            : $('#modo_cliente').val(),
                    "id_cliente"      : $('#id_cliente').val(),
                    "codigo"          : $('#id_venta').val(),
                    "_token"          :"{{ csrf_token() }}",
                },
                success: function(resultado){
                  //var resultado= JSON.parse(resultado);
                  if(resultado.error==0){
                    window.location.href ='{{url('')}}/venta/imprimir/'+resultado.id_venta;
                   // toastr.success('Venta realizada correctamente','Completar venta',{timeOut:0500});
                  }else{
                    mostrarerror('error!','error',resultado.mensaje);
                  } 
                },
              });
              }else{
                mostrarerror('error!','error','el descuento no puede ser negativo');
              }
              
              }
      });
    }

    function destroy_tabla(){
      var url='{{asset('')}}TemporalVenta/destroy';
        $.ajax({
            url: url,dataType: 'json', 
            method:"GET",
            success: function(resultado){
              update_tabla();
              $('#monto_cliente').val(null);
              $("#monto_cliente-error").css("display", "none");
              $("#monto_cliente").removeClass('is-valid');
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

    $(function(){
        $("#cliente").autocomplete({
           source:"{{route('cliente.autocompleteData')}}", minLength:3,
            select: function(event, ui){
                event.preventDefault();
                $("#id_cliente").val(ui.item.id);
                $("#cliente").val(ui.item.value);
                $("#cliente").removeClass('is-invalid').addClass( "is-valid" );
            }
        });
    });

    function buscar(e,ci){
     // e=jQuery.Event("keypress");
     valor = parseInt(ci)
      if(e.which==13 && ci!=null){
        if(isNaN(valor)==false){
          var url='{{asset('')}}cliente/buscarCI'+'/'+ci;
          $.ajax({
              url: url,
              method:"GET",dataType: 'json',
              success: function(resultado){
                if(resultado.error==0){
                $("#id_cliente").val(resultado.cliente.id);
                $("#cliente").val(resultado.cliente.nombre +" "+ resultado.cliente.apellidos);
                $("#cliente").removeClass('is-invalid').addClass( "is-valid" );
                }else{
                  $("#cliente").addClass('is-invalid');
                //  $("#cliente").val("cliente no encontrado");
                }
              }
          });
        }  
      }
    }

    function Guardar_monto_ssesion(monto){
          if (monto != '' && monto>0 && monto!= null) {
           // var id_producto=codigo;
            var url='{{asset('')}}venta/monto_ssesion/'+monto;
              $.ajax({
                  url: url, dataType: 'json',
                  success: function(resultado) {
                    if(resultado.error==2){
                      var text2 = document.createTextNode(resultado.mensaje);   
                     // $("#Smonto_cliente").display("block");
                      $("#monto_cliente").addClass('is-invalid');
                      $("#monto_cliente-error").css("display", "block");
                      $("#monto_cliente-error").html(resultado.mensaje);
                    }else{
                      if(resultado.error==1){
                        mostrarerror('error!','error',resultado.mensaje);
                      }else{
                        $("#monto_cliente").removeClass('is-invalid').addClass( "is-valid" );
                        $("#monto_cliente-error").css("display", "none");   
                      }
                    }
                  }
              });    
          }else{
            if(monto < 0){
              $("#monto_cliente").addClass('is-invalid'); 
            }
            if(monto==''){
              $("#monto_cliente").removeClass('is-invalid');
            }
          }
      
    }

    function compruebaValidoEntero(valor){
      valor = parseInt(valor)

      //Compruebo si es un valor numérico 
      if (isNaN(valor)) { 
         //entonces (no es numero) devuelvo el valor cadena vacia 
         $("#monto_cliente").removeClass('is-valid');
         $("#monto_cliente-error").css("display", "none");
         $("#monto_cliente").removeClass('is-invalid');
      }else{ 
         //En caso contrario (Si era un número) devuelvo el valor 
         //return valor 
      } 
    }

    function compruebaValidoEntero2(valor){
      valor = parseInt(valor)

      //Compruebo si es un valor numérico 
      if (isNaN(valor)) { 
         //entonces (no es numero) devuelvo el valor cadena vacia 
         $("#cliente").removeClass('is-valid');
        // $("#monto_cliente-error").css("display", "none");
         $("#cliente").removeClass('is-invalid');
      }else{ 
         //En caso contrario (Si era un número) devuelvo el valor 
         //return valor 
      } 
    }

    $("#tipo_pago").on("click",function(){
     // var otro=$("#otro").val();   
     // 0 publico general 1 cliente
     // 1 contado 0 credito
     // $("#id_cliente").trigger("change");
      var valor=$(this).val()//obtenemos el valor seleccionado en una variable
      if(valor==0){
        //$('#id_cliente').prop('disabled', false);  
        $("#modo_cliente").val("1");
        $("#modo_cliente").prop("checked", false); 
        $('#modo_cliente').prop('disabled', true);
        $('#monto_cliente').prop('disabled', true);
        $('#cliente').prop('disabled', false);
      }
      if(valor==1){
        $("#modo_cliente").val("0");
        $('#id_cliente').val('');
        $('#cliente').prop('disabled', true);
        $('#monto_cliente').prop('disabled', false);
        $('#modo_cliente').prop('disabled', false);
        $("#modo_cliente").prop("checked", true); 
      }
    })

    

  </script>
 
    
  @stop