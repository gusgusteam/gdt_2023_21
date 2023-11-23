@extends('adminlte::page')

@section('title')

@section('content_header')
   
@stop

@section('content')
<div class="embed-responsive embed-responsive-16by9">
    <iframe class="embed-responsive-item" src="{{asset('informe_dia/'.$fecha)}}" allowfullscreen></iframe>
</div>

@stop