<?php

use App\Http\Controllers\CajaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductoAlmacenController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\TemporalCompraController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\EgresosController;
use App\Http\Controllers\IngresoController;

use App\Http\Controllers\PlanpagoController;
use App\Http\Controllers\ProveedorController;

use App\Http\Controllers\ServicioGeneralController;
use App\Http\Controllers\TemporalVentaController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('/home', function () {
  //  return view('home');
  return redirect(route('inicio'));
})->name('home')->middleware('auth');

Route::get('/', [HomeController::class,'inicio'])->name('inicio')->middleware('auth');
Route::get('ver_informe/{fecha}', [HomeController::class,'ver_informe'])->name('ver_informe')->middleware('auth');
Route::get('informe_dia/{fecha}', [HomeController::class,'informe_dia_ventas'])->name('informe_dia')->middleware('auth');
Route::get('informe_productos', [HomeController::class,'productos_en_stock_minimo'])->name('productos_en_stock_minimo')->middleware('auth');


Route::get('generar_reporte', [HomeController::class,'generar_reporte'])->name('generar_reporte')->middleware('auth');
Route::get('reporte_venta_rango/{fecha_inicial}/{fecha_final}', [HomeController::class,'reporte_rango'])->name('reporte_venta_rango')->middleware('auth');


Route::get('perfil/show', [HomeController::class,'perfil_datos'])->name('perfil')->middleware('auth');
Route::get('perfil/password', [HomeController::class,'password'])->name('password')->middleware('auth');
Route::get('reporte/home', [HomeController::class,'export'])->name('reporte.home')->middleware('auth');
Route::post('update/password', [HomeController::class,'update_password'])->name('update.password')->middleware('auth');

Auth::routes();

Route::controller(HomeController::class)->group(function (){
    Route::get('configuracion','configuracion')->name('configuracion.index')->middleware('auth');
    Route::get('configuracion/show','show')->name('configuracion.show')->middleware('auth');
    Route::post('configuracion/logo_update','logo')->name('configuracion.logo_update')->middleware('auth');
    Route::post('configuracion/fondo_update','fondo')->name('configuracion.fondo_update')->middleware('auth');
    Route::post('configuracion/datos_update','datos')->name('configuracion.datos_update')->middleware('auth');
    Route::post('configuracion/config_update','config')->name('configuracion.config_update')->middleware('auth');
});


Route::controller(UsuarioController::class)->group(function (){
    Route::get('usuario/{option}','index')->name('usuario.index')->middleware('can:usuario');
    Route::get('usuario/show/{user}','show')->name('usuario.show')->middleware('can:usuario.editar');
    Route::get('usuario/DatosServerSideActivo/{sw}','datos')->name('usuario.datos')->middleware('can:usuario');
    Route::get('usuario/destroy/{user}','destroy')->name('usuario.destroy')->middleware('can:usuario.eliminar');
    Route::get('usuario/restore/{user}','restore')->name('usuario.restore')->middleware('can:usuario.restore');
   Route::post('usuario/store','store')->name('usuario.store')->middleware('can:usuario.agregar');
   Route::post('usuario/update/{user}','update')->name('usuario.update')->middleware('can:usuario.editar'); 
});
Route::controller(EmpleadoController::class)->group(function (){
    Route::get('empleado','index')->name('empleado.index')->middleware('can:empleado');
    Route::get('empleado/show/{empleado}','show')->name('empleado.show')->middleware('can:empleado.editar');
    Route::get('empleado/DatosServerSideActivo','datos')->name('empleado.datos')->middleware('can:empleado');
    Route::get('empleado/destroy/{empleado}','destroy')->name('empleado.destroy')->middleware('can:empleado.eliminar');
    Route::get('empleado/restore/{empleado}','restore')->name('empleado.restore');
   Route::post('empleado/store','store')->name('empleado.store');
   Route::post('empleado/update/{empleado}','update')->name('empleado.update'); 
});
Route::controller(ClienteController::class)->group(function (){
    Route::post('cliente/cancelar_deuda_venta','cancelar_deuda_venta')->name('cliente.cancelar_deuda_venta');

    Route::get('cliente/autocompleteData','autocompleteData')->name('cliente.autocompleteData');
    Route::get('cliente','index')->name('cliente.index')->middleware('can:cliente');
    Route::get('cliente/show/{cliente}','show')->name('cliente.show')->middleware('can:cliente.editar');
    Route::get('cliente/buscarCI/{ci}','buscar_por_ci')->name('cliente.buscarCI');
    Route::get('cliente/DatosServerSideActivo','datos')->name('cliente.datos')->middleware('can:cliente');
    Route::get('cliente/deudores','clientes_deudores')->name('cliente.deudores');
    Route::get('cliente/deuda_dias/{id_cliente}/{anio}/{mes}','deuda_cliente_dias')->name('cliente.deuda_dias');

    Route::get('cliente/deuda/{id_cliente}','cliente_deuda')->name('cliente.deuda');
    Route::get('cliente/pagos_venta/{id_cliente}','cliente_pagos_venta')->name('cliente.pagos_venta');


    Route::get('cliente/destroy/{cliente}','destroy')->name('cliente.destroy')->middleware('can:cliente.eliminar');
    Route::get('cliente/restore/{cliente}','restore')->name('cliente.restore')->middleware('can:cliente.restore');
   Route::post('cliente/store','store')->name('cliente.store')->middleware('can:cliente.agregar');
   Route::post('cliente/update/{cliente}','update')->name('cliente.update')->middleware('can:cliente.editar'); 
});
Route::controller(ProveedorController::class)->group(function (){
    Route::get('provedor/autocompleteData/{nombre}','autocompleteData')->name('provedor.autocompleteData');
    Route::get('provedor','index')->name('provedor.index')->middleware('can:cliente');
    Route::get('provedor/show/{proveedor}','show')->name('provedor.show')->middleware('can:cliente.editar');
    //Route::get('provedor/buscar/{nombre}','buscar_por_nombre')->name('provedor.autocompleteData');
    Route::get('provedor/DatosServerSideActivo','datos')->name('provedor.datos')->middleware('can:cliente');
    Route::get('provedor/destroy/{proveedor}','destroy')->name('provedor.destroy')->middleware('can:cliente.eliminar');
    Route::get('provedor/restore/{proveedor}','restore')->name('provedor.restore')->middleware('can:cliente.restore');
   Route::post('provedor/store','store')->name('provedor.store');
   Route::post('provedor/update/{proveedor}','update')->name('provedor.update')->middleware('can:cliente.editar'); 
});
Route::controller(RolController::class)->group(function (){
    Route::get('rol/datos','datos')->name('rol.datos');
    Route::get('rol/{sw}','index')->name('rol.index')->middleware('can:rol');
    Route::get('rol/show/{rol}','show')->name('rol.show')->middleware('can:rol.editar');
    Route::get('rol/permisos/{rol}','show_permisos')->name('rol.permisos')->middleware('can:rol.editar');
    Route::get('rol/destroy/{rol}','destroy')->name('rol.destroy')->middleware('can:rol.eliminar');
    Route::get('rol/restore/{rol}','restore')->name('rol.restore')->middleware('can:rol.restore');
   Route::post('rol/store','store')->name('rol.store')->middleware('can:rol.agregar');
   Route::post('rol/update/{rol}','update')->name('rol.update')->middleware('can:rol.editar');
   Route::post('rol/update_permisos/{id}','actualizar_permisos')->name('rol.update_permisos')->middleware('can:rol.editar');
});
Route::controller(ProductoController::class)->group(function (){
    Route::get('producto/{sw}','index')->name('producto.index')->middleware('can:producto');
    Route::get('producto/show/{producto}','show')->name('producto.show')->middleware('can:producto.editar');
    Route::get('producto/destroy/{producto}','destroy')->name('producto.destroy')->middleware('can:producto.eliminar');
    Route::get('producto/restore/{producto}','restore')->name('producto.restore')->middleware('can:producto.restore');
    Route::get('producto/datos/{producto}','datos')->name('producto.datos');
    Route::get('producto/DatosServerSideActivo/{sw}','datos2')->name('producto.datos2')->middleware('can:producto'); 
   Route::post('producto/store','store')->name('producto.store')->middleware('can:producto.agregar');
   Route::post('producto/update/{producto}','update')->name('producto.update')->middleware('can:producto.editar');

});
Route::controller(CategoriaController::class)->group(function (){
    Route::get('categoria/{sw}','index')->name('categoria.index')->middleware('can:categoria');
    Route::get('categoria/show/{categoria}','show')->name('categoria.show')->middleware('can:categoria.editar');
    Route::get('categoria/destroy/{categoria}','destroy')->name('categoria.destroy')->middleware('can:categoria.eliminar');
    Route::get('categoria/restore/{categoria}','restore')->name('categoria.restore')->middleware('can:categoria.restore');
   Route::post('categoria/store','store')->name('categoria.store')->middleware('can:categoria.agregar');
   Route::post('categoria/update/{categoria}','update')->name('categoria.update')->middleware('can:categoria.editar'); 
});
Route::controller(ProductoAlmacenController::class)->group(function (){
    Route::get('producto_almacen','index')->name('producto_almacen.index')->middleware('can:inventario.producto_almacen'); 
    Route::get('producto_almacen/DatosServerSideActivo','datos')->name('producto_almacen.datos');

});
Route::controller(CompraController::class)->group(function (){
    Route::get('compra/nueva','nueva_compra')->name('compra.nueva_compra')->middleware('can:nota.compra');
    Route::get('compra/calcular_subtotal_producto/{producto}/{cantidad}','calcular_subtotal_producto')->name('compra.calcular_subtotal_producto');
    Route::get('compra','index')->name('compra.index')->middleware('can:compra.show');
    Route::get('compra/destroy/{compra}','destroy')->name('compra.destroy')->middleware('can:compra.cancelar');
    Route::get('compra/detalle/{compra}','detalle_compra')->name('compra.detalle')->middleware('can:compra.ver');
    Route::get('compra/DatosServerSideActivo','datos')->name('compra.datos');
    Route::get('compra/pdf/{compra}','pdf_detalle')->name('compra.pdf')->middleware('can:compra.imprimir');
});
Route::controller(TemporalCompraController::class)->group(function (){
    Route::get('TemporalCompra/insertar/{id_producto}/{id_almacen}/{cantidad}/{codigo}','insertar')->name('TemporalCompra.insertar');
    Route::get('TemporalCompra/actualizar/{id_producto}/{id_almacen}/{codigo}/{cantidad}','actualizar_producto')->name('TemporalCompra.actualizar');
    Route::get('TemporalCompra/guardar/{codigo}','guardar_compra')->name('TemporalCompra.guardar');
    Route::get('TemporalCompra/datos/{codigo}','get_datos')->name('TemporalCompra.datos');

});

Route::controller(TemporalVentaController::class)->group(function (){
    Route::get('TemporalVenta/insertar/{id_producto}/{id_almacen}/{codigo}/{cantidad}','insertar')->name('TemporalVenta.insertar');
    Route::get('TemporalVenta/aumentar/{id_producto}/{id_almacen}/{codigo}/{cantidad}','aumentar_cantidad')->name('TemporalVenta.aumentar');
    Route::get('TemporalVenta/reducir/{id_producto}/{id_almacen}/{codigo}/{cantidad}','reducir_cantidad')->name('TemporalVenta.reducir');
    Route::get('TemporalVenta/updatecantidad/{id_producto}/{id_almacen}/{codigo}/{cantidad}','modificar_cantidad')->name('TemporalVenta.updatecantidad');
    Route::get('TemporalVenta/eliminar/{id_producto}/{id_almacen}/{codigo}','eliminar_producto')->name('TemporalVenta.eliminar');
    Route::post('TemporalVenta/guardar','guardar_venta')->name('TemporalVenta.guardar');
    Route::get('TemporalVenta/datos/{codigo}','get_datos')->name('TemporalVenta.datos');
    Route::get('TemporalVenta/show','show')->name('TemporalVenta.show')->middleware('can:nota.venta');
    Route::get('TemporalVenta/destroy','destroy')->name('TemporalVenta.destroy');
});

Route::controller(VentaController::class)->group(function (){
    Route::get('venta/deuda/{id_cliente}','deuda_cliente_mes')->name('venta.deuda');
    Route::get('venta/deuda_dias/{id_cliente}/{anio}/{mes}','deuda_cliente_dias')->name('venta.deuda_dias');
    Route::post('venta/cancelar_credito','cancelar_credito')->name('venta.cancelar_credito');

    Route::get('venta/nueva','nueva_venta')->name('venta.nueva_venta')->middleware('can:nota.venta');
    Route::get('venta/new/{nombre}/{descripcion}/{id_categoria}','DatosServerSide')->name('venta.new')->middleware('can:nota.venta');

    Route::get('venta','index')->name('venta.index')->middleware('can:venta.show');
    Route::post('venta/store','store')->name('venta.store');

    Route::get('venta/destroy/{venta}','destroy')->name('venta.destroy')->middleware('can:venta.cancelar');
    Route::get('venta/detalle/{venta}','detalle_venta')->name('venta.detalle')->middleware('can:venta.ver');
    Route::get('venta/DatosServerSideActivo','datos')->name('venta.datos');
    Route::get('venta/monto_ssesion/{monto}','ssesion_monto')->name('venta.SsesionMonto');
    Route::get('venta/pdf/{venta}','pdf_detalle')->name('venta.pdf')->middleware('can:venta.imprimir');
    Route::get('venta/ticket/{venta}','ticket')->name('venta.ticket')->middleware('can:venta.imprimir');
    Route::get('venta/imprimir/{venta}','imprimir')->name('venta.imprimir')->middleware('can:venta.imprimir');

});

Route::controller(CajaController::class)->group(function (){
    Route::get('caja','index')->name('caja.index');
    Route::get('caja_general','caja_general')->name('caja.caja_general'); 
    Route::get('caja_general/{caja}','caja_panel')->name('caja.caja_panel'); 
    Route::get('caja/datos','datos')->name('caja.datos');
    Route::get('caja/cerrar/{caja}','cerrar_caja')->name('caja.cerrar');
    Route::get('caja/iniciar/{caja}','iniciar_caja')->name('caja.iniciar');
    Route::get('caja/cajas_generales','cajas_generales')->name('caja.cajas_generales'); 
    Route::get('caja/informe/{id_caja}/{fecha_inicial}/{fecha_final}','reporte_informe')->name('caja.informe');

    Route::get('caja/informe_general/{id_caja}/{tipo}','reporte_informe2')->name('caja.informe2');

    Route::get('caja/informe_servicios/{caja}','informe_cajas_servicios')->name('caja.informe_servicios');

});

Route::controller(ServicioGeneralController::class)->group(function (){
    Route::get('general/datos_deudores','clientes_deudores_servicio')->name('general.clientes_deudores_servicios');

    Route::get('general/datos','datos')->name('general.datos');
    Route::get('general/conprobantes','comprobantes')->name('general.comprobantes');
    Route::get('general/datos_servicios','servicios_datos')->name('general.servicios_datos');

    Route::post('general/guardar','guardar')->name('general.guardar');
    Route::get('general/detalles/{id_caja}','detalles')->name('general.detalles');
    Route::get('general/servicios/{id_caja}','servicios')->name('general.servicios');
    Route::get('general/destroy/{servicioGeneral}','destroy')->name('general.destroy');
    //credito

    Route::get('general/deuda/{id_caja}/{id_cliente}','deuda')->name('general.deuda');
    Route::get('general/server/{id_caja}/{id_cliente}','servicios_deuda')->name('general.servicios_deuda');
    Route::get('general/deuda_dias/{id_cliente}','deuda_cliente_dias')->name('general.deuda_dias');
    Route::post('general/cancelar_credito','cancelar_credito')->name('general.cancelar_credito');
    Route::get('general/pdf_tiket/{servicioGeneral}','pdf_servicio')->name('general.pdf_tiket');
});

Route::controller(IngresoController::class)->group(function (){
    Route::get('ingreso/index','index')->name('ingreso.index');
    Route::get('ingreso/datos/{id_caja}','datos')->name('ingreso.datos');
    Route::get('ingreso/datos_caja/{id_caja}','datos_caja')->name('ingreso.datos_caja'); 

    Route::post('ingreso/guardar/{tipo}','guardar')->name('ingreso.guardar');
    Route::get('ingreso/eliminar/{ingreso}','eliminar')->name('ingreso.eliminar');
   
});
Route::controller(EgresosController::class)->group(function (){
    Route::get('egreso/index','index')->name('egreso.index');
    Route::get('egreso/datos/{id_caja}','datos')->name('egreso.datos');
  // Route::post('egreso/guardar/{tipo}','guardar')->name('egreso.guardar');
   Route::post('egreso/guardar','guardar')->name('egreso.guardar');
    Route::get('egreso/eliminar/{egresos}','eliminar')->name('egreso.eliminar');
   
});

Route::controller(PlanpagoController::class)->group(function (){
    Route::get('plan_pago/index','index')->name('plan_pago.index')->middleware('can:plan.plan_pago');
    Route::get('plan_pago/datos','datos')->name('plan_pago.datos');
    Route::get('plan_pago/cancelar/{planpago}','cancelar')->name('plan_pago.cancelar')->middleware('can:plan.eliminar');
    Route::get('plan_pago/pdf/{planpago}','pdf')->name('plan_pago.pdf')->middleware('can:plan.pdf');
   
});





