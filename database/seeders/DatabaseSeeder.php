<?php

namespace Database\Seeders;

use App\Models\Almacen;
use App\Models\Caja;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Configuracion;
use App\Models\Empleado;
use App\Models\Producto;
use App\Models\ProductoAlmacen;
use App\Models\Proveedor;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Configuracion::create([
            'nombre' => 'San Pedro',
            'nombre2' => 'GDT',
            'direccion' => 'Santa Cruz/Montero/San pedro',
            'leyenda' => 'tenga un excelente dia',
            'telefono' => '71619345',
            'nic' => '00011111',
            'correo' => 'sanpedro@gmail.com'
        ]);
        $role1=Role::create(['name'=>'Administrador']);
        $role2=Role::create(['name'=>'Cliente','estado'=> 0]);
        $role3=Role::create(['name'=>'Caja']);
        $role4=Role::create(['name'=>'Socio']);

        Permission::create(['guard_name2'=> 'administracion','name'=> 'admin'        ,     'tipo' =>0])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'usuario','name'=> 'usuario'        ,     'tipo' =>1])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'usuario editar','name'=> 'usuario.editar',      'tipo' =>1])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'usuario eliminar','name'=> 'usuario.eliminar',    'tipo' =>1])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'usuario agregar','name'=> 'usuario.agregar',     'tipo' =>1])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'usuario eliminados','name'=> 'usuario.eliminados',  'tipo' =>1])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'usuario restaurar','name'=> 'usuario.restore',     'tipo' =>1])->syncRoles([$role1]);
        //roles
        Permission::create(['guard_name2'=> 'rol','name'=> 'rol' ,          'tipo' =>2])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'rol editar','name'=> 'rol.editar',    'tipo' =>2])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'rol eliminar','name'=> 'rol.eliminar',  'tipo' =>2])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'rol agregar','name'=> 'rol.agregar',   'tipo' =>2])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'rol eliminados','name'=> 'rol.eliminados','tipo' =>2])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'rol restaurar','name'=> 'rol.restore',   'tipo' =>2])->syncRoles([$role1]);
        //empleado
        Permission::create(['guard_name2'=> 'empleado','name'=> 'empleado' ,          'tipo' =>3])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'empleado editar','name'=> 'empleado.editar',    'tipo' =>3])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'empleado eliminar','name'=> 'empleado.eliminar',  'tipo' =>3])->syncRoles([$role1]);
        //configuracion
        Permission::create(['guard_name2'=> 'configuracion','name'=> 'configuracion' ,          'tipo' =>4])->syncRoles([$role1]);
        //cliente
        Permission::create(['guard_name2'=> 'cliente','name'=> 'cliente' ,          'tipo' =>5])->syncRoles([$role1,$role3,$role4]);
        Permission::create(['guard_name2'=> 'cliente editar','name'=> 'cliente.editar',    'tipo' =>5])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'cliente eliminar','name'=> 'cliente.eliminar',  'tipo' =>5])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'cliente agregar','name'=> 'cliente.agregar',   'tipo' =>5])->syncRoles([$role1]);
        //Permission::create(['guard_name2'=> 'cliente eliminados','name'=> 'cliente.eliminados','tipo' =>5])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'cliente restaurar','name'=> 'cliente.restore',   'tipo' =>5])->syncRoles([$role1]);
        //producto
        Permission::create(['guard_name2'=> 'producto','name'=> 'producto' ,          'tipo' =>6])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'producto editar','name'=> 'producto.editar',    'tipo' =>6])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'producto eliminar','name'=> 'producto.eliminar',  'tipo' =>6])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'producto agregar','name'=> 'producto.agregar',   'tipo' =>6])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'producto eliminados','name'=> 'producto.eliminados','tipo' =>6])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'producto restaurar','name'=> 'producto.restore',   'tipo' =>6])->syncRoles([$role1]);
        //categoria
        Permission::create(['guard_name2'=> 'categoria','name'=> 'categoria' ,          'tipo' =>7])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'categoria editar','name'=> 'categoria.editar',    'tipo' =>7])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'categoria eliminar','name'=> 'categoria.eliminar',  'tipo' =>7])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'categoria agregar','name'=> 'categoria.agregar',   'tipo' =>7])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'categoria eliminados','name'=> 'categoria.eliminados','tipo' =>7])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'categoria restaurar','name'=> 'categoria.restore',   'tipo' =>7])->syncRoles([$role1]);
        
        //nota producto almacen
        Permission::create(['guard_name2'=> 'inventario','name'=> 'inventario' ,                           'tipo' =>8])->syncRoles([$role1,$role3]);
        Permission::create(['guard_name2'=> 'producto almacen','name'=> 'inventario.producto_almacen' ,          'tipo' =>8])->syncRoles([$role1,$role3]);
       // nota compra
        Permission::create(['guard_name2'=> 'nota de compra','name'=> 'nota.compra',                             'tipo' =>9])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'compras realizadas','name'=> 'compra.show',                         'tipo' =>9])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'cancelar compra','name'=> 'compra.cancelar',                        'tipo' =>9])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'pdf compra','name'=> 'compra.imprimir',                             'tipo' =>9])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'ver detalle compra','name'=> 'compra.ver',                          'tipo' =>9])->syncRoles([$role1]);
        //nota venta
       // Permission::create(['guard_name2'=> 'nota','name'=> 'nota' ,                        'tipo' =>10])->syncRoles([$role1,$role3]);
        Permission::create(['guard_name2'=> 'nota de venta','name'=> 'nota.venta',          'tipo' =>10])->syncRoles([$role1,$role3]);
        Permission::create(['guard_name2'=> 'venta realizadas','name'=> 'venta.show',       'tipo' =>10])->syncRoles([$role1,$role3]);
        Permission::create(['guard_name2'=> 'cancelar venta','name'=> 'venta.cancelar',     'tipo' =>10])->syncRoles([$role1,$role3]);
        Permission::create(['guard_name2'=> 'imprimir venta','name'=> 'venta.imprimir',     'tipo' =>10])->syncRoles([$role1,$role3]);
        Permission::create(['guard_name2'=> 'ver detalle venta','name'=> 'venta.ver',       'tipo' =>10])->syncRoles([$role1,$role3]);
        Permission::create(['guard_name2'=> 'cancelar credito venta','name'=> 'venta.credito',                'tipo' =>10])->syncRoles([$role1]);

        
        
        
        Permission::create(['guard_name2'=> 'Caja','name'=> 'caja.ver',                             'tipo' =>11])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'Caja general','name'=> 'cajageneral.ver',              'tipo' =>11])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'Gastos ingresos y egresos','name'=> 'gastos.ver',       'tipo' =>11])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'Caja GDT','name'=> 'cajaprincipal.ver',                'tipo' =>11])->syncRoles([$role1]);

        Permission::create(['guard_name2'=> 'Eliminar ingresos','name'=> 'ingreso.eliminar',                'tipo' =>12])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'Eliminar egresos','name'=> 'egreso.eliminar',                'tipo' =>12])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'Eliminar servicios','name'=> 'servicio.eliminar',                'tipo' =>12])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'cancelar credito servicios','name'=> 'servicio.credito',                'tipo' =>12])->syncRoles([$role1]);


        Permission::create(['guard_name2'=> 'Plan de pago','name'=> 'plan.plan_pago','tipo' =>13])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'Plan de pago eliminar','name'=> 'plan.eliminar','tipo' =>13])->syncRoles([$role1]);
        Permission::create(['guard_name2'=> 'Plan de pago pdf','name'=> 'plan.pdf','tipo' =>13])->syncRoles([$role1]);




        
        
        $user1=User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123'),
        ])->assignRole('Administrador');

        $user2=User::create([
            'name' => 'cajera',
            'email' => 'caja1@gmail.com',
            'password' => Hash::make('123'),
        ])->assignRole('Caja');

        $user3=User::create([
            'name' => 'socio1',
            'email' => 'socio1@gmail.com',
            'password' => Hash::make('123'),
        ])->assignRole('Socio');

        $categora1=Categoria::create([
            'nombre'=>'perno',
        ]);
        $categora2=Categoria::create([
            'nombre'=>'aceite',
        ]);
        /*$categora3=Categoria::create([
            'nombre'=>'rodamiento',
        ]);
        $categora4=Categoria::create([
            'nombre'=>'correa',
        ]);*/
       
        
        
        Producto::create([
            'nombre'=> 'perno nro 8',
            'descripcion' => 'color negro y hilo delgado',
            'precio_venta' => '1' ,
            'precio_compra' => '0.5',
            'stock' => '0',
            'stock_minimo' => '10',
            'id_categoria' => $categora1->id
        ]);
        Producto::create([
            'nombre'=> 'perno nro 17',
            'descripcion' => 'color negro y hilo grueso',
            'precio_venta' => '2' ,
            'precio_compra' => '1.5',
            'stock' => '0',
            'stock_minimo' => '10',
            'id_categoria' => $categora1->id
        ]);
        Producto::create([
            'nombre'=> 'tuerca para perno 19',
            'descripcion' => 'color negro de hilo grueso',
            'precio_venta' => '2.5' ,
            'precio_compra' => '1.75',
            'stock' => '0',
            'stock_minimo' => '10',
            'id_categoria' => $categora1->id
        ]);
        Producto::create([
            'nombre'=> 'arandela para perno 19',
            'descripcion' => 'color plomo y resistente',
            'precio_venta' => '1' ,
            'precio_compra' => '0.75',
            'stock' => '0',
            'stock_minimo' => '10',
            'id_categoria' => $categora1->id
        ]);
        Producto::create([
            'nombre'=> 'aceite AMA 20W - 50',
            'descripcion' => 'aceite de origen argentino',
            'precio_venta' => '35' ,
            'precio_compra' => '31',
            'stock' => '0',
            'stock_minimo' => '10',
            'id_categoria' => $categora2->id
        ]);
        Producto::create([
            'nombre'=> 'aceite MOTUL 20W - 50',
            'descripcion' => 'aceite de origen italiano',
            'precio_venta' => '70' ,
            'precio_compra' => '63.5',
            'stock' => '0',
            'stock_minimo' => '10',
            'id_categoria' => $categora2->id
        ]); 

       /*Producto::create([
            'nombre'=> 'pollo economico',
            'descripcion' => 'papas fritas con su fideo y una pocion de pollo',
            'precio_venta' => '10' ,
            'precio_compra' => '8',
            'stock' => '0',
            'stock_minimo' => '10',
            'inventariable' => '0',
            'id_categoria' => $categora6->id
        ]);*/

        $almacen1=Almacen::create([
            'nombre' => 'Almacen general',
            'capacidad'=> '120',
            'sigla'=> 'G'
        ]);
        /*$almacen2=Almacen::create([
            'nombre' => 'Almacen A-1',
            'capacidad'=> '90',
            'sigla'=> 'A-1'
        ]);
        $almacenn=Almacen::create([
            'nombre' => 'Almacen A-2',
            'capacidad'=> '90',
            'sigla'=> 'A-2'
        ]);
        $almacen3=Almacen::create([
            'nombre' => 'Almacen B-1',
            'capacidad'=> '90',
            'sigla'=> 'B-1'
        ]);*/

        Empleado::create([
            'nombre'=>'richar',
            'apellidos'=>'canaviri',
            'edad'=>'25',
            'sexo'=> 'Masculino',
            'telefono'=>'71619345',
            'sueldo'=>1000,
            'ci'=>8879285,
            'direccion'=>'montero/ san pedro/avenida sc',
            'id_usuario'=>$user1->id
        ]);

        Empleado::create([
            'nombre'=>'ana',
            'apellidos'=>'n',
            'edad'=>'23',
            'sexo'=> 'Femenino',
            'telefono'=>'827828',
            'sueldo'=>1500,
            'ci'=>777777,
            'direccion'=>'san jose/barrios 24 de septiembre',
            'id_usuario'=>$user2->id
        ]);

        Empleado::create([
            'nombre'=>'sergio',
            'apellidos'=>'canaviri',
            'edad'=>'22',
            'sexo'=> 'Masculino',
            'telefono'=>'68837629',
            'sueldo'=>1800,
            'ci'=>82372,
            'direccion'=>'san pedro',
            'id_usuario'=>null
        ]);

      /*  Empleado::create([
            'nombre'=>'ana',
            'apellidos'=>'artega',
            'edad'=>'23',
            'sexo'=> 'Femenino',
            'telefono'=>'28237',
            'sueldo'=>1300,
            'ci'=>19022,
            'direccion'=>'san jose/barrioss',
            'id_usuario'=>null
        ]);*/

        Cliente::create([
            'nombre'=>'aisa',
            'apellidos'=>'carvajal cuellar',
            'edad'=>'1',
            'sexo'=> 'Femenino',
            'ci'=>728712
        ]);

        Cliente::create([
            'nombre'=>'fernando',
            'apellidos'=>'carvajal barrios',
            'edad'=>'20',
            'sexo'=> 'Masculino',
            'ci'=>82379
        ]);
        Cliente::create([
            'nombre'=>'diego',
            'apellidos'=>'barrios zenteno',
            'edad'=>'20',
            'sexo'=> 'Masculino',
            'ci'=>726353
        ]);
        Cliente::create([
            'nombre'=>'diego',
            'apellidos'=>'ortuño veisaga',
            'edad'=>'21',
            'sexo'=> 'Masculino',
            'ci'=>873892
        ]);
        

        Proveedor::create([
            'nombre'=>'Motul SRL SC',
            'descripcion'=>'empresa de aceites de calidad',
            'direccion'=>'avenida santa cruz 3er anillo',
            'telefono'=> 88888,
            'tipo'=>1,
            'correo'=>'motul@gmail.com',
            'nic'=>'192763'
        ]);
        

        Caja::create([
            'nro_caja' => 1,
            'nombre' => 'Caja Inventario',
            'descripcion' => 'se registrara Ventas y Compras de productos'
        ]);
        Caja::create([
            'nro_caja' => 2,
            'nombre' => 'Caja Servicio Taller',
            'descripcion' => 'se registrara los soldadura y otros'
        ]);
        Caja::create([
            'nro_caja' => 3,
            'nombre' => 'Caja Servicio Grua',
            'descripcion' => 'se registrara los servicios de trasportey mecanizacion'
        ]);
        Caja::create([
            'nro_caja' => 4,
            'nombre' => 'Caja Servicio Maquinaria',
            'descripcion' => 'se registrara los servicios de mecanizacion'
        ]);
        Caja::create([
            'nro_caja' => 5,
            'nombre' => 'Caja Servicio labadero , agua, balanza',
            'descripcion' => 'se registrara los servicios basicos'
        ]);
        Caja::create([
            'nro_caja' => 6,
            'nombre' => 'Caja Servicio Ganaderia',
            'descripcion' => 'se registrara los servicios de venta y compra de bacas'
        ]);
        Caja::create([
            'nro_caja' => 7,
            'nombre' => 'CAJA GENERAL',
            'descripcion' => 'se controla todo tipo de gasto'
        ]);
        
    
       // factory(Producto::class())->create();
       //Producto::factory(100)->create();
       //ProductoAlmacen::factory(10000)->create();
       //Producto::factory()->count(50)->create();

       
      // User::factory(2000)->create();
    }
}
