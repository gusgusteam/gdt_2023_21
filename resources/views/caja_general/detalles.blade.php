@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark text-uppercase">{{$nombre}}</h1>
@stop

@section('content')
    
    <div class="row">
        <div class="col-12">
            <div class="card {{ config('adminlte.classes_index', '') }}">
                <div class="card-body ">
                    <h1 class="card-title {{ config('adminlte.classes_index_header', '') }} ">DETALLES DE SERVICIOS</h1> 
                </div>
                <div class="card-header">
                    <table id="example1" class="table table-responsive-xl table-bordered table-sm table-hover table-striped"  >
                      <thead>
                          <tr>  
                            <th width="8%">codigo </th>
                            <th width="20%">descripcion </th>
                            <th width="5%">monto </th>
                            <th width="5%">interes </th>
                            <th width="15%">fecha </th>
                            <th width="15%">cliente</th>
                            <th width="17%">empleado</th>
                            <th width="5%"><span class="badge bg-primary">Estado</span></th>
                            <th width="5%">tipo pago</th>
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
      ajax: "{{route('general.servicios',$id_caja)}}",
      columns: [
          {data: 'codigo',searchable: true,orderable: false},
          {data: 'descripcion',searchable: false},
          {data: 'monto',searchable: false},
          {data: 'interes',searchable: false},
          {data: 'fecha_hora',searchable: false},
          {data: 'cliente',searchable: false},
          {data: 'empleado',searchable: false},
          {data: 'estado',searchable: false},
          {data: 'tipo',searchable: false},
          {data: 'actions',searchable: false,orderable: false}
      ],
    })
</script>
<script>
    
  

  
    function Eliminar(id){
      Swal.fire({
        title: '¿Está seguro?',
        text: "¡El servicio se canselara!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Si, Estoy seguro!'
      }).then((result) => {
        if (result.isConfirmed) {
          var link="{{asset('')}}"+"general/destroy/"+id;
            $.ajax(     {
                url: link,dataType: 'json',
                type: "GET",
                cache: false,
                async: false,
                success:function(resultado){
                  if(resultado.error==0){
                    Swal.fire(
                      '¡Cancelando!',
                      'Su servicio ha sido cancelado.',
                      'De acuerdo'
                    ).then((result)=>{
                      $("#example1").DataTable().ajax.reload();
                    })
                  }else{
                    mostrarerror('Error de cancelacion','error',resultado.mensaje);
                  }
                }
            })  
        }
      })
    }

    function mostrarerror(title,icono,error){
      Swal.fire({
        title: title,
        text: error,
        icon: icono,
        confirmButtonText: 'okey'
      })
    }

   


</script>
    
@stop