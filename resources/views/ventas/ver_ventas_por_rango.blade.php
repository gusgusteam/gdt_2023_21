@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark">Informe de ventas por rango de fecha</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        
        <div class="col-sm-4">
            <label for="fecha_inicial">fecha inicial</label>
            <input class="form-control" type="date" name="fecha_inicial" id="fecha_inicial" required>
        </div>
        <div class="col-sm-4">
            <label for="fecha_final">fecha final</label>
            <input class="form-control" type="date" name="fecha_final" id="fecha_final" required>
        </div>
        
        <div class="col-sm-4">
            <label for="btn_reporte">&nbsp;&nbsp;</label>
            <br>
            <a onclick="reporte(fecha_inicial.value,fecha_final.value)" id="btn_reporte" class="btn btn-primary" >generar reporte</a>
        </div>
    </div>
</div>

<br>
<div class="embed-responsive embed-responsive-16by9">
    <iframe id="iframe_reporte" class="embed-responsive-item" src="" allowfullscreen></iframe>
</div>

@stop

@section('js')
<script>
function reporte(fecha_inicio,fecha_final){
    var url='{{asset('')}}reporte_venta_rango/'+fecha_inicio+'/'+fecha_final ;
   // var url='{{asset('')}}informe_dia/'+fecha_inicio;
    
    $('#iframe_reporte').attr('src', url);
    
      
}
</script>
 
    
@stop