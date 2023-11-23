@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1 class="m-0 text-dark">Permisos</h1>
@stop

@section('content')
<form action="{{route('rol.update_permisos',$id_rol)}}" method="post">
    @csrf
    <div class="row mb-2">
        <div class="col-6 text-left">
            <button class="btn btn-primary" type="submit">Actualizar permisos</button>
        </div>
        <div class="col-6 text-right">
            <h3>{{$nombre_rol}}</h3>
        </div>
    </div>
    <div class="row row-cols-2 row-cols-sm-2  row-cols-md-3 row-cols-lg-6  g-3">
        <div class="col">
          <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <h3 class="card-title  m-0 text-dark w-100 text-center font-weight-bold">
                    ADMINISTRACION
                </h3>
            </div>
            
            <div class="card-body">
              @foreach ($permisos_sistema as $permisos)
              @if ($permisos->tipo==0)
              @php($per=$permisos['id'])
              @php($sw=0)
              @foreach ($roles as $rol_permiso)
                  @if ($per==$rol_permiso['id'])
                    @php($sw=1)
                  @endif
              @endforeach
                  <div class="custom-control custom-switch">
                      <input class="custom-control-input" type="checkbox" id="{{$permisos['id']}}" @if ($sw==1){{'checked'}} @endif value ="{{$permisos['id']}}" name= "permisos[]"/>
                      <label class="custom-control-label font-weight-normal" for="{{$permisos['id']}}">{{$permisos['guard_name2']}}</label>
                  </div>
              @endif
             @endforeach
              <label class="fw-bold">Usuario</label>
              @foreach ($permisos_sistema as $permisos)
                @if ($permisos->tipo==1)
                  @php($per=$permisos['id'])
                  @php($sw=0)
                  @foreach ($roles as $rol_permiso)
                      @if ($per==$rol_permiso['id'])
                        @php($sw=1)
                      @endif
                  @endforeach
                      <div class="custom-control custom-switch">
                          <input class="custom-control-input" type="checkbox" id="{{$permisos['id']}}" @if ($sw==1){{'checked'}} @endif value ="{{$permisos['id']}}" name= "permisos[]"/>
                          <label class="custom-control-label font-weight-normal" for="{{$permisos['id']}}">{{$permisos['guard_name2']}}</label>
                      </div>
                @endif
              @endforeach
              <label class="fw-bold">Rol</label>
              @foreach ($permisos_sistema as $permisos)
                @if ($permisos->tipo==2)
                  @php($per=$permisos['id'])
                  @php($sw=0)
                  @foreach ($roles as $rol_permiso)
                      @if ($per==$rol_permiso['id'])
                        @php($sw=1)
                      @endif
                  @endforeach
                      <div class="custom-control custom-switch">
                          <input class="custom-control-input" type="checkbox" id="{{$permisos['id']}}" @if ($sw==1){{'checked'}} @endif value ="{{$permisos['id']}}" name= "permisos[]"/>
                          <label class="custom-control-label font-weight-normal" for="{{$permisos['id']}}">{{$permisos['guard_name2']}}</label>
                      </div>
                @endif
              @endforeach
              <label class="fw-bold">Empleado</label>
              @foreach ($permisos_sistema as $permisos)
                @if ($permisos->tipo==3)
                  @php($per=$permisos['id'])
                  @php($sw=0)
                  @foreach ($roles as $rol_permiso)
                      @if ($per==$rol_permiso['id'])
                        @php($sw=1)
                      @endif
                  @endforeach
                      <div class="custom-control custom-switch">
                          <input class="custom-control-input" type="checkbox" id="{{$permisos['id']}}" @if ($sw==1){{'checked'}} @endif value ="{{$permisos['id']}}" name= "permisos[]"/>
                          <label class="custom-control-label font-weight-normal" for="{{$permisos['id']}}">{{$permisos['guard_name2']}}</label>
                      </div>
                @endif
              @endforeach
              <label class="fw-bold">Configuracion</label>
              @foreach ($permisos_sistema as $permisos)
                @if ($permisos->tipo==4)
                  @php($per=$permisos['id'])
                  @php($sw=0)
                  @foreach ($roles as $rol_permiso)
                      @if ($per==$rol_permiso['id'])
                        @php($sw=1)
                      @endif
                  @endforeach
                      <div class="custom-control custom-switch">
                          <input class="custom-control-input" type="checkbox" id="{{$permisos['id']}}" @if ($sw==1){{'checked'}} @endif value ="{{$permisos['id']}}" name= "permisos[]"/>
                          <label class="custom-control-label font-weight-normal" for="{{$permisos['id']}}">{{$permisos['guard_name2']}}</label>
                      </div>
                @endif
              @endforeach
            </div>
          </div>
        </div>
        <div class="col">
            <div class="card card-outline card-primary">
              <div class="card-header text-center">
                  <h3 class="card-title  m-0 text-dark w-100 text-center font-weight-bold">
                      CLIENTE
                  </h3>
              </div>
              <div class="card-body">
                <label class="fw-bold">Cliente</label>
                @foreach ($permisos_sistema as $permisos)
                  @if ($permisos->tipo==5)
                    @php($per=$permisos['id'])
                    @php($sw=0)
                    @foreach ($roles as $rol_permiso)
                        @if ($per==$rol_permiso['id'])
                          @php($sw=1)
                        @endif
                    @endforeach
                        <div class="custom-control custom-switch">
                            <input class="custom-control-input" type="checkbox" id="{{$permisos['id']}}" @if ($sw==1){{'checked'}} @endif value ="{{$permisos['id']}}" name= "permisos[]"/>
                            <label class="custom-control-label font-weight-normal" for="{{$permisos['id']}}">{{$permisos['guard_name2']}}</label>
                        </div>
                  @endif
                @endforeach
                
              </div>
            </div>
            <div class="card card-outline card-primary">
                <div class="card-header text-center">
                    <h3 class="card-title  m-0 text-dark w-100 text-center font-weight-bold">
                        PRODUCTO
                    </h3>
                </div>
                <div class="card-body">
                  <label class="fw-bold">Producto</label>
                  @foreach ($permisos_sistema as $permisos)
                    @if ($permisos->tipo==6)
                      @php($per=$permisos['id'])
                      @php($sw=0)
                      @foreach ($roles as $rol_permiso)
                          @if ($per==$rol_permiso['id'])
                            @php($sw=1)
                          @endif
                      @endforeach
                          <div class="custom-control custom-switch">
                              <input class="custom-control-input" type="checkbox" id="{{$permisos['id']}}" @if ($sw==1){{'checked'}} @endif value ="{{$permisos['id']}}" name= "permisos[]"/>
                              <label class="custom-control-label font-weight-normal" for="{{$permisos['id']}}">{{$permisos['guard_name2']}}</label>
                          </div>
                    @endif
                  @endforeach
                  <label class="fw-bold">Categoria</label>
                  @foreach ($permisos_sistema as $permisos)
                    @if ($permisos->tipo==7)
                      @php($per=$permisos['id'])
                      @php($sw=0)
                      @foreach ($roles as $rol_permiso)
                          @if ($per==$rol_permiso['id'])
                            @php($sw=1)
                          @endif
                      @endforeach
                          <div class="custom-control custom-switch">
                              <input class="custom-control-input" type="checkbox" id="{{$permisos['id']}}" @if ($sw==1){{'checked'}} @endif value ="{{$permisos['id']}}" name= "permisos[]"/>
                              <label class="custom-control-label font-weight-normal" for="{{$permisos['id']}}">{{$permisos['guard_name2']}}</label>
                          </div>
                    @endif
                  @endforeach
                  
                </div>
              </div>
        </div>
        <div class="col">
            <div class="card card-outline card-primary">
              <div class="card-header text-center">
                  <h3 class="card-title  m-0 text-dark w-100 text-center font-weight-bold">
                      INVENTARIO
                  </h3>
              </div>
              <div class="card-body">
                <label class="fw-bold">Producto almacen</label>
                @foreach ($permisos_sistema as $permisos)
                  @if ($permisos->tipo==8)
                    @php($per=$permisos['id'])
                    @php($sw=0)
                    @foreach ($roles as $rol_permiso)
                        @if ($per==$rol_permiso['id'])
                          @php($sw=1)
                        @endif
                    @endforeach
                        <div class="custom-control custom-switch">
                            <input class="custom-control-input" type="checkbox" id="{{$permisos['id']}}" @if ($sw==1){{'checked'}} @endif value ="{{$permisos['id']}}" name= "permisos[]"/>
                            <label class="custom-control-label font-weight-normal" for="{{$permisos['id']}}">{{$permisos['guard_name2']}}</label>
                        </div>
                  @endif
                @endforeach
              </div>
            </div>
            <div class="card card-outline card-primary">
                <div class="card-header text-center">
                    <h3 class="card-title  m-0 text-dark w-100 text-center font-weight-bold">
                        COMPRA
                    </h3>
                </div>
                <div class="card-body">
                  <label class="fw-bold">Compra</label>
                  @foreach ($permisos_sistema as $permisos)
                    @if ($permisos->tipo==9)
                      @php($per=$permisos['id'])
                      @php($sw=0)
                      @foreach ($roles as $rol_permiso)
                          @if ($per==$rol_permiso['id'])
                            @php($sw=1)
                          @endif
                      @endforeach
                          <div class="custom-control custom-switch">
                              <input class="custom-control-input" type="checkbox" id="{{$permisos['id']}}" @if ($sw==1){{'checked'}} @endif value ="{{$permisos['id']}}" name= "permisos[]"/>
                              <label class="custom-control-label font-weight-normal" for="{{$permisos['id']}}">{{$permisos['guard_name2']}}</label>
                          </div>
                    @endif
                  @endforeach
                </div>
            </div>
            <div class="card card-outline card-primary">
                <div class="card-header text-center">
                    <h3 class="card-title  m-0 text-dark w-100 text-center font-weight-bold">
                        VENTA
                    </h3>
                </div>
                <div class="card-body">
                  <label class="fw-bold">Venta</label>
                  @foreach ($permisos_sistema as $permisos)
                    @if ($permisos->tipo==10)
                      @php($per=$permisos['id'])
                      @php($sw=0)
                      @foreach ($roles as $rol_permiso)
                          @if ($per==$rol_permiso['id'])
                            @php($sw=1)
                          @endif
                      @endforeach
                          <div class="custom-control custom-switch">
                              <input class="custom-control-input" type="checkbox" id="{{$permisos['id']}}" @if ($sw==1){{'checked'}} @endif value ="{{$permisos['id']}}" name= "permisos[]"/>
                              <label class="custom-control-label font-weight-normal" for="{{$permisos['id']}}">{{$permisos['guard_name2']}}</label>
                          </div>
                    @endif
                  @endforeach
                </div>
            </div>
        </div>
        <div class="col">
          <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <h3 class="card-title  m-0 text-dark w-100 text-center font-weight-bold">
                    CONTROL DE TRABAJO
                </h3>
            </div>
            <div class="card-body">
              <label class="fw-bold">caja y movimiento</label>
              @foreach ($permisos_sistema as $permisos)
                @if ($permisos->tipo==11)
                  @php($per=$permisos['id'])
                  @php($sw=0)
                  @foreach ($roles as $rol_permiso)
                      @if ($per==$rol_permiso['id'])
                        @php($sw=1)
                      @endif
                  @endforeach
                      <div class="custom-control custom-switch">
                          <input class="custom-control-input" type="checkbox" id="{{$permisos['id']}}" @if ($sw==1){{'checked'}} @endif value ="{{$permisos['id']}}" name= "permisos[]"/>
                          <label class="custom-control-label font-weight-normal" for="{{$permisos['id']}}">{{$permisos['guard_name2']}}</label>
                      </div>
                @endif
              @endforeach

              <label class="fw-bold">Permisos de trabajo</label>
              @foreach ($permisos_sistema as $permisos)
                @if ($permisos->tipo==12)
                  @php($per=$permisos['id'])
                  @php($sw=0)
                  @foreach ($roles as $rol_permiso)
                      @if ($per==$rol_permiso['id'])
                        @php($sw=1)
                      @endif
                  @endforeach
                      <div class="custom-control custom-switch">
                          <input class="custom-control-input" type="checkbox" id="{{$permisos['id']}}" @if ($sw==1){{'checked'}} @endif value ="{{$permisos['id']}}" name= "permisos[]"/>
                          <label class="custom-control-label font-weight-normal" for="{{$permisos['id']}}">{{$permisos['guard_name2']}}</label>
                      </div>
                @endif
              @endforeach
              <label class="fw-bold">Plan de pago</label>
              @foreach ($permisos_sistema as $permisos)
                @if ($permisos->tipo==13)
                  @php($per=$permisos['id'])
                  @php($sw=0)
                  @foreach ($roles as $rol_permiso)
                      @if ($per==$rol_permiso['id'])
                        @php($sw=1)
                      @endif
                  @endforeach
                      <div class="custom-control custom-switch">
                          <input class="custom-control-input" type="checkbox" id="{{$permisos['id']}}" @if ($sw==1){{'checked'}} @endif value ="{{$permisos['id']}}" name= "permisos[]"/>
                          <label class="custom-control-label font-weight-normal" for="{{$permisos['id']}}">{{$permisos['guard_name2']}}</label>
                      </div>
                @endif
              @endforeach
            </div>
          </div>
         
      </div>
    </div>
</form>
   


@stop

@section('js')


<script>
    
    function actualizar(){
      var id = $("#id_registro").val();
      var link="{{asset('')}}"+"rol/update/"+id;
      $.ajax({
          url: link,
          type: "POST",
          processData: false,
          contentType: false,
          data: new FormData($('#frm_editar')[0]),    
          success:function(response){
            if (response.error==1){
                  toastr.error(response.mensaje, 'Guardar Registro', {timeOut:5000});
               }else{
                  toastr.success('El registro fue actualizado correctamente.', 'Guardar Registro', {timeOut:3000}); 
                  $('#modal_editar').modal('hide');
                  setTimeout(redirigir, '0000');
               }
          }
      })
    }
  
    f


    



   
   
</script>
    
@stop