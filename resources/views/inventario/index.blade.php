@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark">Inventario</h1>
@stop

@section('content')
    
    <div class="row mb-2">
        <div class="col-12">
            <div class="card {{ config('adminlte.classes_index', '') }}">
                <div class="card-body ">
                    <h1 class="card-title {{ config('adminlte.classes_index_header', '') }} ">LISTA DE PRODUCTOS EN ALMACEN</h1> 
                </div>
                <div class="card-header">
                    <table id="example1" class="table table-responsive-xl table-bordered table-sm table-hover table-striped"  >
                      <thead>
                          <tr>  
                            <th width="5%"> # </th>
                            <th width="35%">Nombre producto</th>
                            <th width="25%">Nombre almacen</th>
                            <th width="25%">stock</th>
                            <th width="10%"><span class="badge bg-primary">Estado</span></th>
                          </tr>
                      </thead>  
                      <tbody>
                       
                      </tbody>
                    </table>
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
      ajax: "{{route('producto_almacen.datos')}}",
      columns: [
          {data: 'id',searchable: true,orderable: false},
          {data: 'nombre_producto',searchable: false},
          {data: 'nombre_almacen',searchable: false},
          {data: 'stock',searchable: true},
          {data: 'estado',searchable: false},
          
      ],
    })
</script>
<script>
    
  

   
</script>
    
@stop