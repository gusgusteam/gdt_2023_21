@extends('adminlte::page')

@section('title')

@section('content_header')
   
@stop

@section('content')
<div class="content-wrapper" style="min-height: 1592.03px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Permiso denegado</h1>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="error-page">
        <h2 class="headline text-warning"></h2>

        <div class="error-content">
          <h3><i class="fas fa-exclamation-triangle text-warning"></i> Dirigirse con el administrador para mas informacion.</h3>

          <p>
           Intentar violar las normas de los permisos no es recomendable para el sistema 
            Por favor seguir con su camino 
          </p>

          
        </div>
        <!-- /.error-content -->
      </div>
      <!-- /.error-page -->
    </section>
    <!-- /.content -->
  </div>
@stop