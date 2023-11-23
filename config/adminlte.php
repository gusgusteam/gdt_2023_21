<?php




return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'sistema 2023',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b>Agro</b> CB',
    'logo_img' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Admin Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => true,
    'usermenu_header_class' => 'bg-default',
    'usermenu_image' => true,
    'usermenu_desc' => true,
    'usermenu_profile_url' => true,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => false,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => true,
    'layout_fixed_footer' => true,
    'layout_dark_mode' => null,
/*
    'layout_topnav' => null,
    'layout_boxed' => false, // para que no se desplace todo con el topnav
    'layout_fixed_sidebar' => true, // para que no desplace el sidebar
    'layout_fixed_navbar' => true, // para que no desplace el navbar
    'layout_fixed_footer' => true, // se habilita el footer de abajo
    'layout_dark_mode' => null,
    
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */
    'classes_index' => 'card-outline card-primary',
    'classes_index_header' => ' m-0 text-dark w-100 text-center font-weight-bold ',
    'classes_index_modal_agregar' => 'bg-lightblue',
    'classes_index_modal_editar' => 'bg-lightblue',
    'classes_modal' => 'card-primary',
    'classes_modal_header' => 'w-100 text-center font-weight-bold text-white ',

    'classes_btn_editar' => 'btn-primary',
    'classes_btn_eliminar' => 'btn-danger',
    'classes_btn_restaurar' => 'btn-secondary',

    'classes_auth_card' => 'card-outline card-success',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 0,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => '/',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => true,

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For detailed instructions you can look the laravel mix section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        // Navbar items:
        [
            'type'         => 'darkmode-widget',
            'topnav_right' => true, // Or "topnav => true" to place on the left.
        ],
        [
            'type'         => 'navbar-search',
            'text'         => 'search',
            'topnav_right' => true,
        ],
        [
            'type'         => 'fullscreen-widget',
            'topnav_right' => true,
        ],

        // Sidebar items:
        [
            'type' => 'sidebar-menu-search',
            'text' => 'search',
        ],
        [
            'text' => 'blog',
            'url'  => 'admin/blog',
            'can'  => 'manage-blog',
        ],
        [
            'id' => 'group_administracion',
            'text'        => 'Administracion',
           // 'url'         => '#',
            'icon'        => 'fas fa-tv',
            //'label'       => 2,
            //'label_color' => 'success',
            'can'         => 'admin',
            'submenu' => [
                [
                    'id'          => 'btn_usuario',
                    'name'          => 'btn_usuario',
                    'text'        => 'Usuarios',
                    'url'         => 'usuario/1',
                    //'route'         => 'usuario.index',
                    'icon'        => 'fas fa-users',
                    'can'         => 'usuario',
                    
                ],
                [
                    'id'          => 'btn_empleado',
                    'name'          => 'btn_empleado',
                    'text'        => 'Empleados',
                    'url'         => 'empleado',
                    'icon'        => 'fas fa-users-cog',
                    'can'         => 'empleado',
                    
                ],
                [
                    'id'          => 'btn_rol',
                    'name'          => 'btn_rol',
                    'text'        => 'Roles',
                    'url'         => 'rol/1',
                    'icon'        => 'fas fa-user-lock',  
                    'can'         => 'rol', 
                ],
                [
                    'id'          => 'btn_configuracion',
                    'name'          => 'btn_configuracion',
                    'text'        => 'Configuracion',
                    //'url'         => 'configuracion',
                    'route'         => 'configuracion.index',
                    'icon'        => 'fas fa-tools',   
                    'can'         => 'configuracion',
                ],

            ],
        ],
        [
            'id'          => 'btn_cliente',
            'name'          => 'btn_cliente',
            'text'        => 'Clientes',
            'url'         => 'cliente',
            'icon'        => 'fas fa-user-circle',
            'can'         => 'cliente',
            
        ],
        [
            'id' => 'group_productos',
            'text'        => 'Productos',
            'icon'        => 'fas fa-table',
            'can'         => 'producto',
            'submenu' => [
                [
                    'id'          => 'btn_producto',
                    'name'          => 'btn_producto',
                    'text'        => 'Productos',
                    'url'         => 'producto/1',
                    'icon'        => 'fab fa-product-hunt',
                    'can'         => 'producto',
                    
                ],
                [
                    'id'          => 'btn_categoria',
                    'name'          => 'btn_categoria',
                    'text'        => 'Categorias',
                    'url'         => 'categoria/1',
                    'icon'        => 'fas fa-grip-horizontal',   
                    'can'         => 'categoria',
                ],
               
                [
                    'id'          => 'btn_provedor',
                    'name'          => 'btn_provedor',
                    'text'        => 'Provedores',
                    'url'         => 'provedor',
                    'icon'        => 'fas fa-grip-horizontal',   
                    //'can'         => 'categoria',
                ],
            ],
        ],
        [
            'id'          => 'btn_venta',
            'name'          => 'btn_venta',
            'text'        => 'Nota de venta',
            'url'         => 'venta/nueva',
            'icon'        => 'fas fa-cash-register',
            'can'         => 'nota.venta',
            
        ],
        [
            'id'          => 'btn_venta_realizadas',
            'name'          => 'btn_venta_realizadas',
            'text'        => 'Venta realizadas',
            'url'         => 'venta',
            'icon'        => 'fas fa-cash-register',   
            'can'         => 'venta.show',
        ],
        [
            'id'          => 'btn_plan_pago',
            'name'          => 'btn_plan_pago',
            'text'        => 'Pagos',
            'url'         => 'plan_pago/index',
            'icon'        => 'fas fa-cash-register',   
            'can'         => 'plan.plan_pago',
        ],
        [
            'id'          => 'btn_caja_general',
            'name'          => 'btn_caja_general',
            'text'        => 'Caja',
            'url'         => 'caja',
            'icon'        => 'fas fa-cash-register',
            'can'         => 'caja.ver',     
        ],
        [
            'id'          => 'btn_caja_general_servicio',
            'name'          => 'btn_caja_general_servicio',
            'text'        => 'Comprobantes servicio',
            'url'         => 'general/conprobantes',
            'icon'        => 'fas fa-cash-register',
            'can'         => 'caja.ver',     
        ],
        [
            'id' => 'group_inventario',
            'text'        => 'Inventario',
            'icon'        => 'fas fa-truck-loading',
            'can'         => 'inventario',
            'submenu' => [
                [
                    'id'          => 'btn_producto_almacen',
                    'name'          => 'btn_producto_almacen',
                    'text'        => 'Producto Almacen',
                    'url'         => 'producto_almacen',
                    'icon'        => 'fas fa-boxes',
                    'can'         => 'inventario.producto_almacen',  
                ],
                [
                    'id'          => 'btn_compra',
                    'name'          => 'btn_compra',
                    'text'        => 'Nota de compra',
                    'url'         => 'compra/nueva',
                    'icon'        => 'fas fa-shopping-bag',
                    'can'         => 'nota.compra',
                    
                ],
                [
                    'id'          => 'btn_compras_realizadas',
                    'name'          => 'btn_compras_realizadas',
                    'text'        => 'Compras realizadas',
                    'route'         => 'compra.index',
                    'icon'        => 'fas fa-tasks',
                    'can'         => 'compra.show',
                    
                ],
                
            ],
        ],
        [
            'id' => 'group_caja',
            'text'        => 'Caja General',
            'icon'        => 'fas fa-truck-loading',
            'can'         => 'cajageneral.ver',
            'submenu' => [
                [
                    'id'          => 'btn_gasto',
                    'name'          => 'btn_gasto',
                    'text'        => 'Gastos (I,E)',
                    //'url'         => 'producto_almacen',
                    'icon'        => 'fas fa-boxes',
                    'can'         => 'gastos.ver',
                    'submenu' => [
                        [
                            'id'          => 'btn_ingreso',
                            'name'        => 'btn_ingreso',
                            'text'        => 'INGRESOS',
                            'url'         => 'ingreso/index'
                        ],
                        [
                            'id'          => 'btn_egreso',
                            'name'        => 'btn_egreso',
                            'text'        => 'EGRESOS',
                            'url'         => 'egreso/index'
                        ],
                    ],
                   // 'can'         => 'inventario.producto_almacen',
                    
                ],
                [
                    'id'          => 'btn_cajas_generales',
                    'name'          => 'btn_cajas_generales',
                    'text'        => 'Cajas GDT',
                     'url'         => 'caja_general',
                    'icon'        => 'fas fa-shopping-bag',
                    'can'         => 'cajaprincipal.ver',
                    
                ],
                
                
            ],
        ],
        
       
        
     
       
        
      

        
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => true,
            'files' => [ 
            
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/datatables/css/dataTables.bootstrap4.min.css',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/datatables-plugins/buttons/css/buttons.bootstrap4.min.css',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/datatables-plugins/responsive/css/responsive.bootstrap4.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/jquery-validation/jquery.validate.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/jquery-ui/jquery-ui.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/jquery-ui/jquery-ui.min.js',
                ],
                
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables-plugins/responsive/js/dataTables.responsive.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables-plugins/responsive/js/responsive.bootstrap4.min.js',
                ],
            ],
        ],
        
        'Select2' => [
            'active' => true,
            'files' => [
              
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/select2/css/select2.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/select2/js/select2.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/select2/js/select2.full.min.js',
                ],
               
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/bootstrap4-duallistbox/bootstrap-duallistbox.min.css',
                ],
                
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => true,
            'files' => [
               
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/sweetalert2/sweetalert2.all.min.js',
                ],
                
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/daterangepicker/daterangepicker.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/daterangepicker/daterangepicker.css',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
        'Toastr' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/toastr/toastr.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/toastr/toastr.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
