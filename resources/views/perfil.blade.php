@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark">Perfil de usuario</h1>
@stop

@section('content')
<div class="container-fluid px-4">
    
    <input type="hidden" id="id" name="id" value=""/>
    <div class="card mb-4" style="max-width: 540px;">
        <div class="row no-gutters">
            @php
                $imagen='img/usuarios/'.Auth::user()->id.'.png';
                if (!file_exists($imagen)) {
                 $imagen = "img/usuarios/user.png";
                }
                $url=asset($imagen.'?'.time());
            @endphp
            <div class="col-md-4">
                <img src="{{asset($url)}}" width="142"class="img-thumbnail">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h5 class="card-title">Informacion</h5>
                    <table width="100%">
                        <tr>
                            <td width="30%"><b>Usuario:</b></td>
                            <td width="70%">{{Auth::user()->name}}</td>
                        </tr>
                        <tr>
                            <td width="30%"><b>nombre:</b></td>
                            <td width="70%">{{$nombre_completo}}</td>
                        </tr>
                        <tr>
                            <td width="30%"><b>sexo:</b></td>
                            <td width="70%">{{$sexo}}</td>
                        </tr>
                        <tr>
                            <td width="30%"><b>edad:</b></td>
                            <td width="70%">{{$edad}}</td>
                        </tr>
                        <tr>
                            <td width="30%"><b>telefono:</b></td>
                            <td width="70%">{{$telefono}}</td>
                        </tr>
                        <tr>
                            <td width="30%"><b>direccion:</b></td>
                            <td width="70%">{{$direccion}}</td>
                        </tr>
                        <tr>
                            @php
                                $r='';
                            @endphp
                            <td><b>Rol:</b></td>
                            <td> @foreach(Auth::user()->roles as $rol) @php $r.= '['.$rol->name.']' @endphp  @endforeach {{$r}} </td>
                        </tr>
                        <tr>
                            <td><b>Caja:</b></td>
                            <td> --- </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <a class="btn btn-primary" href="{{asset('perfil/password')}}">Cambiar contraseña</a>
        </div>
    </div>
</div>
    
@stop