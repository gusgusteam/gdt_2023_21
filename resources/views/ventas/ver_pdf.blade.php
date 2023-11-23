@extends('adminlte::page')

@section('title')

@section('content_header')
   
@stop

@section('content')
<div class="embed-responsive embed-responsive-16by9">
    <iframe class="embed-responsive-item" src="{{asset('venta/ticket/'.$id_venta)}}" allowfullscreen></iframe>
</div>

@stop