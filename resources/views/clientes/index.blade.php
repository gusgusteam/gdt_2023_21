@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark">Clientes</h1>
@stop

@section('content')
    <div class="row mb-2">
        <div class="col-4 text-left">
            <a class="btn btn-primary" onclick="agregar()">agregar</a>
        </div>
        <div class="col-4 text-center">
            <a class="btn btn-success" onclick="mostrar_deudores_inventario()">Ver deudores inventario</a>
        </div>
        <div class="col-4 text-right">
          <a class="btn btn-dark" onclick="Mostrar_deuda_servicios()">Ver deudores caja</a>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card {{ config('adminlte.classes_index', '') }}">
                <div class="card-body ">
                    <h1 class="card-title {{ config('adminlte.classes_index_header', '') }} ">LISTA DE CLIENTES</h1> 
                </div>
                <div class="card-header">
                    <table id="example1" class="table table-responsive-xl table-bordered table-sm table-hover table-striped"  >
                      <thead>
                          <tr>  
                            <th width="5%"> # </th>
                            <th width="5%"> Foto </th>
                            <th width="15%">Nombre</th>
                            <th width="25%">Apellidos</th>
                            <th width="5%">Edad</th>
                            <th width="10%">Sexo</th>
                            <th width="10%">Telefono</th>
                            <th width="10%">CI</th>
                            <th width="7%"><span class="badge bg-primary">Estado</span></th>
                            <th width="8%">Acción</th>
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
              <h5 class="modal-title">Registrar Cliente</h5>
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
            <h5 class="modal-title">Editar Cliente</h5>
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
    
    <div class="modal fade" id="modal_deudores" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <h4 class="modal-title text-uppercase">Clientes deudores de ventas</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <table id="example2" class="table table-responsive-xl table-bordered table-sm table-hover table-striped"  >
              <thead>
                  <tr>  
                    <th width="8%"> CI</th>
                    <th width="32%">Nombre</th>
                    <th width="5%">Edad</th>
                    <th width="10%">Sexo</th>
                    <th width="15%">Telefono</th>
                    <th width="15%">Deuda</th>
                    
                    <th width="10%"><span class="badge bg-primary">Estado</span></th>
                    <th width="5%">Acción</th>
                  </tr>
              </thead>  
              <tbody>
              </tbody>
            </table>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade " id="detalle_deudor" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content ">
          <div class="modal-header bg-info">
            <h4 id="titulo_cliente" class="modal-title text-uppercase"></h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="id_cliente_aux" value="-1">
            <table id="example3" class="table table-responsive-xl table-bordered table-sm table-hover table-striped"  >
              <thead>
                  <tr>  
                    <th width="10%">Anio</th>
                    <th width="10%">mes</th>
                    <th width="20%">Total deuda</th>
                    <th width="20%">Saldo faltante</th>
                    <th width="15%">Saldo pagado</th>
                    <th width="20%">Estado</th>
                    <th width="5%">accion</th>
                  </tr>
              </thead>  
              <tbody>
              </tbody>
            </table>
            <h3 id="monto_cliente" class="text-dark"></h3>
          </div>
          <div class="modal-footer bg-info justify-content-between">
            <button type="button" onclick="mostrar_deudores_inventario()" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modal_notas_ventas" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;" data-gtm-vis-first-on-screen-2340190_1302="5621" data-gtm-vis-total-visible-time-2340190_1302="100" data-gtm-vis-has-fired-2340190_1302="1" aria-hidden="true">
      <div class="modal-dialog modal-xl" role="document">
        <!--Content-->
        <div class="modal-content">
          <!--Header-->
          <div class="modal-header bg-success">
            <h5 id="titulo_notas" class="modal-title text-uppercase">Lista Deudas</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" class="white-text">×</span>
            </button>
          </div>
          <input type="hidden" id="id_cliente_venta">
          <input type="hidden" id="anio_nota">
          <input type="hidden" id="mes_nota">
          <div id="frm" class="modal-body">
            <table  id="example5" class="table table-responsive table-bordered table-sm table-hover table-striped ">
              <thead>
                  <tr>
                    <th width="5%">Nro</th>
                    <th width="10%">Codigo</th>
                    <th width="15%">Fecha</th>  
                    <th width="15%">Fecha Deuda</th>    
                    <th width="30%">Empleado</th>
                    <th width="8%">Monto</th>  
                    <th width="12%">Estado</th>     
                    <th width="5%"></th>    
                  </tr>
              </thead>
              <tbody>
               
              </tbody>
          </table>
          </div>
          <div class="modal-footer bg-success justify-content-between">
            <button type="button" onclick="mostrar_deuda_cliente(id_cliente_venta.value)" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="pagos" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content ">
          <div class="modal-header bg-dark">
            <h4 id="titulo_cliente_pago" class="modal-title text-uppercase"></h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
           
            <table id="example4" class="table table-responsive-xl table-bordered table-sm table-hover table-striped"  >
              <thead>
                  <tr>  
                    <th width="9%">codigo</th>
                    <th width="25%">Descripcion</th>
                    <th width="15%">Fecha</th>
                    <th width="10%">Monto total</th>
                    <th width="8%">Interes</th>
                    <th width="20%">Empleado</th>
                    <th width="8%">Estado</th>
                    <th width="5%">Accion</th>
                  </tr>
              </thead>  
              <tbody>
              </tbody>
            </table>
            
          </div>
          <div class="modal-footer bg-dark justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="completar_credito" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;" data-gtm-vis-first-on-screen-2340190_1302="5621" data-gtm-vis-total-visible-time-2340190_1302="100" data-gtm-vis-has-fired-2340190_1302="1" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <!--Content-->
        <div class="modal-content">
          <!--Header-->
          <div class="modal-header bg-success">
            <h5 class="modal-title">Completar credito</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" class="white-text">×</span>
            </button>
          </div>
          <div id="frm" class="modal-body">
            <input type="hidden" value="-1" name="id_cliente_credito" id="id_cliente_credito">
            <input type="hidden" value="-1" name="tipo_credito" id="tipo_credito">
            <input type="hidden" value="-1" name="mes_credito" id="mes_credito">
            <input type="hidden" value="-1" name="anio_credito" id="anio_credito">
            <input type="hidden" value="-1" name="id_venta_credito" id="id_venta_credito">

            <div class="row">
              <div class="col-sm-6">
                <label for="monto_completar">Monto total</label>
                <input class="form-control" disabled type="text" id="monto_completar" value="0">
              </div>
              <div class="col-sm-6">
                <label for="fecha_completar">Fecha de hoy : <p id="fecha_completar"></p></label>
             
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <label for="interes">Interes</label>
                <input class="form-control" min="0" type="number" step="any" name="interes_credito" id="interes_credito" value="0">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <a class="btn btn-sm btn-success" onclick="guardar_credito()" >Completar credito</a>
            <button type="button" class="btn btn-info btn-sm" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>


    <div class="modal fade" id="pdf_modal_venta" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;" data-gtm-vis-first-on-screen-2340190_1302="5621" data-gtm-vis-total-visible-time-2340190_1302="100" data-gtm-vis-has-fired-2340190_1302="1" aria-hidden="true">
      <div class="modal-dialog modal-xl" role="document">
        <!--Content-->
        <div class="modal-content">
          <!--Header-->
          <div class="modal-header {{ config('adminlte.classes_index_modal_agregar','') }}">
            <h5 class="modal-title">PDF DETALLE</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" class="white-text">×</span>
            </button>
          </div>
          <div id="frm" class="modal-body">
            <div class="embed-responsive embed-responsive-16by9">
              <iframe id="detalle_pdf_venta" class="embed-responsive-item" src="" allowfullscreen></iframe>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-info btn-sm" data-dismiss="modal">Cerrar PDF</button>
          </div>
        </div>
      </div>
    </div>
    
    <div class="modal fade" id="pdf_modal_plan" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;" data-gtm-vis-first-on-screen-2340190_1302="5621" data-gtm-vis-total-visible-time-2340190_1302="100" data-gtm-vis-has-fired-2340190_1302="1" aria-hidden="true">
      <div class="modal-dialog modal-xl" role="document">
        <!--Content-->
        <div class="modal-content">
          <!--Header-->
          <div class="modal-header {{ config('adminlte.classes_index_modal_agregar','') }}">
            <h5 class="modal-title">PDF CREDITO</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" class="white-text">×</span>
            </button>
          </div>
          <div id="frm" class="modal-body">
            <div class="embed-responsive embed-responsive-16by9">
              <iframe id="detalle_pdf_plan" class="embed-responsive-item" src="" allowfullscreen></iframe>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-info btn-sm" data-dismiss="modal">Cerrar PDF</button>
          </div>
        </div>
      </div>
    </div>



    <div class="modal fade" id="modal_deudores_servicios" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <h4 class="modal-title text-uppercase">Clientes deudores de servicios</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <table id="example_deuda_servicio" class="table table-responsive-xl table-bordered table-sm table-hover table-striped"  >
              <thead>
                  <tr>  
                    <th width="8%"> CI</th>
                    <th width="32%">Nombre</th>
                    <th width="5%">Edad</th>
                    <th width="10%">Sexo</th>
                    <th width="15%">Telefono</th>
                    <th width="15%">Deuda</th>
                    <th width="10%"><span class="badge bg-primary">Estado</span></th>
                    <th width="5%">Acción</th>
                  </tr>
              </thead>  
              <tbody>
              </tbody>
            </table>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modal_deudores_servicios_cliente" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <h4 id="cliente_servicio" class="modal-title text-uppercase"></h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <table id="example_deuda_servicio_cliente" class="table table-responsive-xl table-bordered table-sm table-hover table-striped"  >
              <thead>
                  <tr>  
                    <th width="5%">Nro</th>
                    <th width="10%">Codigo</th>
                    <th width="14%">Fecha</th>  
                    <th width="15%">Fecha Deuda</th>    
                    <th width="30%">Empleado</th>
                    <th width="6%">Monto</th>  
                    <th width="5%">Interes</th> 
                    <th width="10%">Estado</th>     
                    <th width="5%"></th>    
                  </tr>
              </thead>  
              <tbody>
              </tbody>
            </table>
          </div>
          <div class="modal-footer justify-content-between">
            <h4 class="text-uppercase" id="total_servicio"></h4>
            <button onclick="deudores_servicio()" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="pdf_modal_servicio" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;" data-gtm-vis-first-on-screen-2340190_1302="5621" data-gtm-vis-total-visible-time-2340190_1302="100" data-gtm-vis-has-fired-2340190_1302="1" aria-hidden="true">
      <div class="modal-dialog modal-xl" role="document">
        <!--Content-->
        <div class="modal-content">
          <!--Header-->
          <div class="modal-header {{ config('adminlte.classes_index_modal_agregar','') }}">
            <h5 class="modal-title">PDF SERVICIO</h5>
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
            <button onclick="deudores_cliente()" type="button" class="btn btn-info btn-sm" data-dismiss="modal">Cerrar PDF</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="completar_credito_servicio" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;" data-gtm-vis-first-on-screen-2340190_1302="5621" data-gtm-vis-total-visible-time-2340190_1302="100" data-gtm-vis-has-fired-2340190_1302="1" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <!--Content-->
        <div class="modal-content">
          <!--Header-->
          <div class="modal-header {{ config('adminlte.classes_index_modal_agregar','') }}">
            <h5 class="modal-title text-uppercase">Completar credito servicio</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" class="white-text">×</span>
            </button>
          </div>
          <div id="frm_servicio" class="modal-body">
            <input type="hidden" value="" id="id_registro_servicio">
            <div class="row">
              <div class="col-sm-6">
                <label for="monto_completar_servicio">Monto total</label>
                <input class="form-control" disabled type="text" id="monto_completar_servicio" value="0">
              </div>
              <div class="col-sm-6">
                <label for="fecha_completar_servicio">Fecha de credito : <p id="fecha_completar_servicio"></p></label>
             
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <label for="interes_servicio">Interes</label>
                <input class="form-control" type="number" step="any" name="interes_servicio" id="interes_servicio" value="0">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <a class="btn btn-sm btn-success" onclick="actualizar_nota_servicio()" >Completar credito</a>
            <button type="button" class="btn btn-info btn-sm" data-dismiss="modal">Cerrar</button>
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
      ajax: "{{route('cliente.datos')}}",
      columns: [
          {data: 'id',searchable: false,orderable: false},
          {data: 'foto',searchable: false},
          {data: 'nombre',searchable: true},
          {data: 'apellidos',searchable: true},
          {data: 'edad',searchable: false},
          {data: 'sexo',searchable: false},
          {data: 'telefono',searchable: false},
          {data: 'ci',searchable: true},
          {data: 'estado',searchable: false},
          {data: 'actions',searchable: false,orderable: false}
      ],
    })

   
    $('#example2').DataTable({ 
      language: {url: languages['es']},
      destroy: true,
      retrieve: true,
      serverSide: true,
      autoWidth: false,
      responsive: r,
      ajax: "{{route('cliente.deudores')}}",
      columns: [
          {data: 'ci',searchable: true,orderable: false},
          {data: 'nombre_completo',searchable: true},
          {data: 'edad',searchable: false},
          {data: 'sexo',searchable: false},
          {data: 'telefono',searchable: false},
          {data: 'total_deuda2',searchable: false},
         // {data: 'total_pagado2',searchable: false},
          {data: 'estado',searchable: false},
          {data: 'actions',searchable: false,orderable: false}
      ],
    })

    $('#example_deuda_servicio').DataTable({ 
      language: {url: languages['es']},
      destroy: true,
      retrieve: true,
      serverSide: true,
      autoWidth: false,
      responsive: r,
      ajax: "{{route('general.clientes_deudores_servicios')}}",
      columns: [
          {data: 'ci',searchable: true,orderable: false},
          {data: 'nombre_completo',searchable: true},
          {data: 'edad',searchable: false},
          {data: 'sexo',searchable: false},
          {data: 'telefono',searchable: false},
          {data: 'total_deuda2',searchable: false},
         // {data: 'total_pagado2',searchable: false},
          {data: 'estado',searchable: false},
          {data: 'actions',searchable: false,orderable: false}
      ],
    })

    

    
</script>
<script>
  function completar_credito_servicio(id_servicio,fecha,monto){
    $('#id_registro_servicio').val(id_servicio);
    $('#monto_completar_servicio').val(monto+' bs');
    $('#fecha_completar_servicio').text(fecha);
    $('#completar_credito_servicio').modal('show');
  }
  function actualizar_nota_servicio(){
    if($('#interes_servicio').val()!=null && $('#interes_servicio').val()!='' && $('#interes_servicio').val()>=0){
    var url='{{asset('')}}general/cancelar_credito';
      $.ajax({
        url:url,dataType: 'json', 
        method: "post",
        data: {
            "interes"         : $('#interes_servicio').val(),
            "id_servicio"        : $('#id_registro_servicio').val(),
            "_token"          :"{{ csrf_token() }}",
        },
        success: function(resultado){
          if(resultado.error==0){
            toastr.success('El credito fue concluido.', 'Credito', {timeOut:1000}); 
            $('#completar_credito_servicio').modal('hide');
            $("#example_deuda_servicio").DataTable().ajax.reload();
            datos_de_deuda_cliente_servicio(resultado.id_cliente);
          }else{
            mostrarerror('error!','error',resultado.mensaje);
          }
        }
      });
    }else{
      mostrarerror('error!','error','por favor verifique los datos  interes');
    }
  }

  function mostrar_detalle_servicio(id_servicio){
    var url='{{asset('')}}general/pdf_tiket/'+id_servicio ;
    $('#modal_deudores_servicios_cliente').modal('hide');
    $('#pdf_modal_servicio').modal('show');
    $('#detalle_pdf_servicio').attr('src', url);
  }
  function deudores_cliente(){
    $('#modal_deudores_servicios_cliente').modal('show');
  }
  
  function deudores_servicio(){
    $('#modal_deudores_servicios_cliente').modal('hide');
    $("#example_deuda_servicio").DataTable().ajax.reload();
    $('#modal_deudores_servicios').modal('show');
  }
  function Mostrar_notas_servicios(id_cliente){
    $('#modal_deudores_servicios').modal('hide');
    $('#modal_deudores_servicios_cliente').modal('show');
    
    datos_de_deuda_cliente_servicio(id_cliente);
  }
  function datos_de_deuda_cliente_servicio(id_cliente) { 
    if(id_cliente !=''){
     
            var url='{{asset('')}}general/deuda_dias/'+id_cliente;
              $.ajax({
                  url:url,dataType: 'json', 
                  success: function(resultado){
                      $("#cliente_servicio").text(resultado.nombre_cliente);
                      $("#total_servicio").text(resultado.total+' bs');
                      $("#example_deuda_servicio_cliente tbody").empty();
                      $("#example_deuda_servicio_cliente tbody").append(resultado.fila);
                      
                  }
            });
     
    }else{mostrarerror('error!','error','Problemas de la pagina de espera recarge de nuevo');}
  }
    
    function Mostrar_deuda_servicios(){
      $('#modal_deudores_servicios').modal('show');
    }
  
    function Mostrar_pdf_plan(id_plan){
      var url='{{asset('')}}plan_pago/pdf/'+id_plan ;
      
      $('#pdf_modal_plan').modal('show');
      $('#detalle_pdf_plan').attr('src', url);
    }

    function mostrar_deudores_inventario(){
      $("#example2").DataTable().ajax.reload();
      $('#modal_deudores').modal('show');
    }

    function mostrar_deuda_cliente(id){
      deuda_cliente(id);
      $('#modal_deudores').modal('hide');
      $('#detalle_deudor').modal('show');
    }

    function pagar_todo_venta(id,monto,anio,mes,id_venta,tipo){
      var today = new Date();
      var now = today.toLocaleDateString('en-US');
      now += '  '+ today.toLocaleTimeString('en-US');
      $('#id_cliente_credito').val(id),
      $('#tipo_credito').val(tipo),
      $('#mes_credito').val(mes),
      $('#anio_credito').val(anio),
      $('#id_venta_credito').val(id_venta),
      $('#monto_completar').val(monto),
      $('#fecha_completar').text(now);
      $('#completar_credito').modal('show');
    }

    function mostrar_plan_pagos(id){
      plan_cliente(id);
      $('#pagos').modal('show');
    }

    function plan_cliente(id){
      $.ajax({
        url:"{{asset('')}}"+"cliente/pagos_venta/"+id, dataType:'json',
        success: function(resultado){
          $('#titulo_cliente_pago').text(resultado.nombre_cliente);
          $("#example4 tbody").empty();
          $("#example4 tbody").append(resultado.fila);
        }
      });
    }


    function Mostrar_notas_ventas(id_cliente,anio,mes) { 

      if(id_cliente !=''){
        if (anio != null && anio != 0 && mes != null && mes !=0 && mes<=12) {
          $('#detalle_deudor').modal('hide');
          cargar_nota_venta(id_cliente,anio,mes);
          $('#modal_notas_ventas').modal('show');
        }else { mostrarerror('error!','error','por favor verifique los datos');}
      }else{mostrarerror('error!','error','Problemas de la pagina de espera recarge de nuevo');}
    }

  function cargar_nota_venta(id_cliente,anio,mes){
    var url='{{asset('')}}cliente/deuda_dias/'+id_cliente+'/'+anio+'/'+mes;
      $.ajax({
          url:url,dataType: 'json', 
          success: function(resultado){
              
              $('#id_cliente_venta').val(id_cliente);
              $('#anio_nota').val(anio);
              $('#mes_nota').val(mes);
              $("#example5 tbody").empty();
              $("#example5 tbody").append(resultado.fila);
              $('#titulo_notas').text(resultado.nombre_cliente);
          }
    });
  }

    function guardar_credito() { 
      var link="{{asset('')}}"+"cliente/cancelar_deuda_venta";
        Swal.fire({
            title: '¿Desea Concluir el credito?',
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
                if($('#interes_credito').val()>=0 && $("#interes_credito").val().trim().length > 0){
                  $.ajax({
                      url: link,
                      type: "POST",dataType:'json',
                 
                      data: {
                        "opcion_pago"     : $('#tipo_credito').val(),
                        "anio"            : $('#anio_credito').val(),
                        "mes"             : $('#mes_credito').val(),
                        "id_venta"        : $('#id_venta_credito').val(),
                        "interes"         : $('#interes_credito').val(),
                        "id_cliente"      : $('#id_cliente_credito').val(),
                        "_token"          :"{{ csrf_token() }}",
                      },  
                      success:function(response){
                        if (response.error==0){
                           toastr.success('El registro fue actualizado correctamente.', 'Guardar Registro', {timeOut:3000}); 
                           $("#example2").DataTable().ajax.reload();
                           if($('#tipo_credito').val()==3){
                            cargar_nota_venta($('#id_cliente_venta').val(),$('#anio_nota').val(),$('#mes_nota').val());
                           }
                           deuda_cliente($('#id_cliente_credito').val());
                           $('#completar_credito').modal('hide');
                        }else{
                         toastr.error(response.mensaje, 'Guardar Registro', {timeOut:5000});
                        }
                      }
                  })
                }else{
                  mostrarerror('error!','error','el interes no puede ser negativo');
                }
              } else if (result.isDenied) {
                if($('#interes_credito').val()>=0){
                
                }else{
                  mostrarerror('error!','error','el interes no puede ser negativo');
                }
              
              }
      });
    }


    function deuda_cliente(id){

      $.ajax({
        url:"{{asset('')}}"+"cliente/deuda/"+id, dataType:'json',
        success: function(resultado){
          $('#titulo_cliente').text(resultado.nombre_cliente);
          $('#id_cliente_aux').val(resultado.id_cliente);
          $('#monto_cliente').text(resultado.total);
          $("#example3 tbody").empty();
          $("#example3 tbody").append(resultado.fila);
        //  $('#modal_deudas').modal('show');
        }
      });
    }

    function agregar(){
      clear_frm_agregar();
      document.getElementById("frm_agregar").reset(); 
      $('#modal_agregar').modal('show');
    }

    function almacenar(){
      var link="{{route('cliente.store')}}";
      $.ajax({
          url: link,dataType:'json',
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

                  //setTimeout(redirigir, '3000');
               }
          }
      })
    }

    function Modificar(id){
      clear_frm_editar();
      $.ajax({
        url:"{{asset('')}}"+"cliente/show/"+id, dataType:'json',
        success: function(resultado){
          $("#id_registro").val(resultado.cliente.id);
          $("#Mnombre").val(resultado.cliente.nombre); 
          $("#Mapellidos").val(resultado.cliente.apellidos); 
          $("#Msexo").val(resultado.cliente.sexo); 
          $("#Medad").val(resultado.cliente.edad); 
          $("#Mtelefono").val(resultado.cliente.telefono); 
          $("#Mnro_carnet").val(resultado.cliente.ci); 
          $('#modal_editar').modal('show');
        }
      });
    }

    function actualizar(){
      var id = $("#id_registro").val();
      var link="{{asset('')}}"+"cliente/update/"+id;
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
          var link="{{asset('')}}"+"cliente/destroy/"+id;
            $.ajax({
                url: link,
                type: "GET",
                cache: false,
                async: false,
                success:function(response){
                  $("#example1").DataTable().ajax.reload();
                  Swal.fire(
                    'Inactivo!',
                    'Su registro ha sido inhabilitado no se realizara venta a este cliente.',
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
          var link="{{asset('')}}"+"cliente/restore/"+id;
            $.ajax({
                url: link,
                type: "GET",
                cache: false,
                async: false,
                success:function(response){
                  $("#example1").DataTable().ajax.reload();
                  Swal.fire({
                    title: '¡Restaurado!',
                    text: 'El cliente ha sido habilitado podra realizar ventas.',
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


    function mostrar_detalle_venta(id_venta){
    var url='{{asset('')}}venta/ticket/'+id_venta ;
   // var url='{{asset('')}}informe_dia/'+fecha_inicio;
    $('#pdf_modal_venta').modal('show');
    $('#detalle_pdf_venta').attr('src', url);
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
   

    function Cancelar(id) { 
      var link="{{asset('')}}"+"plan_pago/cancelar/"+id;
        Swal.fire({
            title: '¿Desea Cancelar el plan de pago?',
            text: "¡No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#58D68D',
            cancelButtonColor: '#d33',
            confirmButtonText: '!cancelar!',
            denyButtonColor: '#3498DB',
            showDenyButton: false,
          //  showCancelButton: true,
           // confirmButtonText: 'Save',
            denyButtonText: 'completar y imprimir',

        }).then((result) => {
              if (result.isConfirmed) {
                $.ajax({
                      url:link, dataType: 'json', 
                      success: function(resultado){
                        if(resultado.error==0){
                          plan_cliente(resultado.id_cliente);
                          //$("#example1").DataTable().ajax.reload();
                        }else{
                          mostrarerror('error!','error',resultado.mensaje);
                        }
                         
                      }
                });
              }
      });
    }
   
</script>
    
@stop