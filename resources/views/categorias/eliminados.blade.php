@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark">Categorias Eliminados</h1>
@stop

@section('content')
    <div class="row mb-2">
        <div class="col-12 text-right">
            <a class="btn btn-success" href="{{route('categoria.index',1)}}">Categorias</a>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
          <div class="card {{ config('adminlte.classes_index', '') }}">
            <div class="card-body ">
                <h1 class="card-title {{ config('adminlte.classes_index_header', '') }} ">LISTA DE CATEGORIAS</h1> 
            </div>
            <div class="card-header">
                <table id="example1" class="table table-responsive-xl table-bordered table-sm table-hover table-striped"  >
                  <thead>
                      <tr>  
                        <th width="5%"> # </th>
                        <th width="75%">Nombre</th>
                        <th width="10%"><span class="badge bg-primary">Estado</span></th>
                        <th width="10%">Acción</th>
                      </tr>
                  </thead>  
                  <tbody>
                    @php
                        $c=1;
                        $css_btn_restaurar= config('adminlte.classes_btn_restaurar') ;
                    @endphp
                    @foreach ($categorias as $categoria)
                    <tr>
                      <td>{{$c}}</td>
                      <td>{{$categoria->nombre}}</td>
                      <td><span class="badge bg-danger">inactivo</span></td>
                      <td class="text-right">
                        <div class="btn-group btn-group-sm">  
                          <a class="btn {{$css_btn_restaurar}}" rel="tooltip" data-placement="top" title="Eliminar" onclick="Restaurar({{$categoria->id}})"><i class="far fa-trash-alt"></i></a>
                        </div>
                      </td>
                    </tr>
                    @php $c = $c + 1; @endphp
                    @endforeach
                  </tbody>
                </table>
            </div>
        </div>
        </div>
    </div>
@stop

@section('js')

<script>
    $("#group_productos .nav-link").addClass("active");
    $("#btn_producto .nav-link").removeClass("active");
    $("#btn_provedor .nav-link").removeClass("active");
    $("#group_productos").addClass("menu-is-opening menu-open");
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
      autoWidth: false,
      responsive: r,
    });
</script>
<script>
  
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
          var link="{{asset('')}}"+"categoria/restore/"+id;
            $.ajax({
                url: link,
                type: "GET",
                cache: false,
                async: false,
                
            })

          Swal.fire({
            title: '¡Restaurado!',
            text: 'Su registro ha sido habilitado.',
            confirmButtonText:'De acuerdo',
            icon: 'success'
          }).then((result)=>{
            setTimeout(redirigir, '0000');
          })
        }
      })
    }

     

   
</script>




        
@stop