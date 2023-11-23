@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark">CAJA GENERAL INGRESOS</h1>
@stop

@section('content')
  <div  class="row"> 
      <div class="col-sm-12">
        <div class="card ">
          <div class="card-header">
            <h4 class="title text-center"><b >LISTA DE INGRESOS</b></h4>
          </div>
          <div class="card-body">
            <table id="gasto" class="table table-responsive-xl table-bordered table-sm table-hover table-striped">
              <thead>
                <tr>  
                  <th width="5%"> codigo </th>
                  <th width="40%">Descripcion</th>
                  <th width="10%">Monto</th>
                  <th width="15%">Fecha</th>
                  <th width="15%">Origen</th>
                  <th width="10%"><span class="badge bg-primary">Estado</span></th>
                  <th width="5%">Acción</th>
                </tr>
              </thead> 
            </table>
          </div>
          <div class="card-footer">
            
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
    $('#gasto').DataTable({ 
      language: {url: languages['es']},
      destroy: true,
      retrieve: true,
      serverSide: true,
      autoWidth: false,
      responsive: r,
      ajax: "{{route('ingreso.datos',7)}}",
      columns: [
          {data: 'codigo',searchable: false,orderable: false},
          {data: 'descripcion',searchable: false},
          {data: 'monto',searchable: true},
          {data: 'fecha_hora',searchable: false},
          {data: 'caja',searchable: false},
          {data: 'estado',searchable: false},
          
          {data: 'actions',searchable: false,orderable: false}
      ],
    })
</script>
<script>
  function Eliminar_ingreso_propio(id){
      Swal.fire({
        title: '¿Está seguro?',
        text: "¡El ingreso cambiara a estado cancelado!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Si, Estoy seguro!'
      }).then((result) => {
        if (result.isConfirmed) {
          var link="{{asset('')}}"+"ingreso/eliminar/"+id;
            $.ajax({
                url: link, dataType: 'json',
                type: "GET",
                success:function(resultado){
                  if(resultado.error==0){
                    $("#gasto").DataTable().ajax.reload();
                    toastr.success('cancelado exitosamente','Cancelar ingreso',{timeOut:2000});
                  }else{
                    mostrarerror('error!','error',resultado.mensaje);
                  }
                }
            })
          
        }
      })
  }
</script>
@stop
