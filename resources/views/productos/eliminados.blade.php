@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark">Productos Eliminados</h1>
@stop

@section('content')
    <div class="row mb-2">
        <div class="col-12 text-right">
            <a class="btn btn-success" href="{{route('producto.index',1)}}">Productos</a>
        </div>
    </div>
    <div class="row">
      <div class="col-12">
          <div class="card {{ config('adminlte.classes_index', '') }}">
              <div class="card-body ">
                  <h1 class="card-title {{ config('adminlte.classes_index_header', '') }} ">LISTA DE PRODUCTOS</h1> 
              </div>
              <div class="card-header">
                  <table id="example1" class="table table-responsive-xl table-bordered table-sm table-hover table-striped"  >
                    <thead>
                        <tr>  
                          <th width="5%"> # </th>
                          <th width="5%"> </th>
                          <th width="15%">Nombre</th>
                          <th width="20%">Descripcion</th>
                          <th width="5%">Stock</th>
                          <th width="10%">Stock minimo</th>
                          <th width="12%">Provedor</th>
                          <th width="9%">Precio venta</th>
                          <th width="9%">Precio compra</th>
                          <th width="5%"><span class="badge bg-primary">Estado</span></th>
                          <th width="5%">Acción</th>
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
    $("#group_productos .nav-link").addClass("active");
    $("#btn_categoria .nav-link").removeClass("active");
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
      retrieve: true,
      serverSide: true,
      autoWidth: false,
      responsive: r,
      ajax: "{{route('producto.datos2',0)}}",
      columns: [
          {data: 'id',searchable: true,orderable: false},
          {data: 'foto',searchable: true},
          {data: 'nombre',searchable: true,orderable: false},
          {data: 'descripcion',searchable: false},
          {data: 'stock',searchable: false},
          {data: 'stock_minimo',searchable: false},
          {data: 'nombre_provedor',searchable: true},
          {data: 'precio_venta',searchable: false},
          {data: 'precio_compra',searchable: false},
          {data: 'estado',searchable: false},
          {data: 'actions',searchable: false,orderable: false}
      ],
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
          var link="{{asset('')}}"+"producto/restore/"+id;
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
            $("#example1").DataTable().ajax.reload();
          })
        }
      })
    }

     

   
</script>




        
@stop