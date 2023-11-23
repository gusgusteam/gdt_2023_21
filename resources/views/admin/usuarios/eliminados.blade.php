@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark">Usuarios Eliminados</h1>
@stop

@section('content')
    <div class="row mb-2">
        <div class="col-12 text-right">
            <a class="btn btn-success" href="{{route('usuario.index',1)}}">Usuarios</a>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card {{ config('adminlte.classes_index', '') }}">
                <div class="card-body ">
                    <h1 class="card-title {{ config('adminlte.classes_index_header', '') }} ">LISTA DE USUARIOS</h1> 
                </div>
                <div class="card-header">
                    <table id="example1" class="table table-responsive-xl table-bordered table-sm table-hover table-striped"  >
                      <thead>
                          <tr>  
                            <th width="5%"> # </th>
                            <th width="20%">Usuario</th>
                            <th width="40%">Correo</th>
                            <th width="15%">Rol</th>
                            <th width="10%"><span class="badge bg-primary">Estado</span></th>
                            <th width="5%">Acción</th>
                          </tr>
                      </thead>  
                      <tfoot>
                      </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')


<script>
    $("#group_administracion .nav-link").addClass("active");
    $("#btn_configuracion .nav-link").removeClass("active");
    $("#btn_empleado .nav-link").removeClass("active");
    $("#btn_rol .nav-link").removeClass("active");
    $("#group_administracion").addClass("menu-is-opening menu-open");
    //$("#group_administracion").trigger("click");
   // $("#group_administracion").click();
    //$("#group_administracion .nav nav-treeview").css("display","block");
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
      ajax: "{{route('usuario.datos',0)}}",
      columns: [
        {data: 'id',searchable: false,orderable: false},
        {data: 'name',searchable: true},
        {data: 'email',searchable: true},
        {data: 'rol_uso',searchable: false},
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
          var link="{{asset('')}}"+"usuario/restore/"+id;
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
          })
          $("#example1").DataTable().ajax.reload();
        }
      })
    }

     

   
</script>




        
@stop