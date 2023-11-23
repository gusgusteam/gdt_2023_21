@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark">Cambiar contraseña</h1>
@stop

@section('content')
<div class="container-fluid px-4"> 
   
    <form method="POST" action="{{asset('update/password')}}" autocomplete="off">
        @csrf
        <div class="form-group">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <label>Usuario</label> 
                    <div class="container-fluid px-0 py-2">
                       <input class="form-control" id="usuario" name="usuario" type="text" value="{{Auth::user()->email}}" disabled />
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <label>Nombre</label>
                    <div class="container-fluid px-0 py-2">
                       <input class="form-control" id="nombre" name="nombre" type="text" value="{{Auth::user()->name}}"disabled />
                    </div>
                </div>
            </div>   
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <label>Contraseña actual</label>
                    <div class="container-fluid px-0 py-2">
                       <input class="form-control" id="contraseña" name="contraseña" type="password" required />
                    </div> 
                </div>
                <div class="col-12 col-sm-6">
                    <label>Nueva nontraseña</label>
                    <div class="container-fluid px-0 py-2">
                       <input class="form-control" id="nueva_contraseña" name="nueva_contraseña" type="password" minlength="5" maxlength="14" required />
                    </div> 
                </div>
                <div class="col-12 col-sm-6">
                    <label>Confirma nueva contraseña</label> 
                    <div class="container-fluid px-0 py-2">
                       <input class="form-control" id="confirmar_nueva_contraseña" name="confirmar_nueva_contraseña"  type="password" required />
                    </div> 
                </div>
            </div>   
        </div>

        <div class="container-fluid px-0 py-3">
           <a href="{{asset('perfil/show')}}" class= "btn btn-primary">Regresar</a>   
           <button type="submit" class= "btn btn-success">Guardar</button> 
       </div> 
    </form> 
</div> 
@stop
