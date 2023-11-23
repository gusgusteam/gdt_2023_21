<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $datos=Configuracion::all()->where('id','=',1)->first();
        if(!session()->has('nombre')){
            session(['nombre'=>$datos->nombre]);
            session(['nombre2'=>$datos->nombre2]);
        }else{
        session(['nombre'=>'--']);
        session(['nombre2'=>'--']);
        }
        session(['responsivo'=>$datos->tabla]);
        //session(['control_tabla'=>]);
        $this->middleware('guest')->except('logout');
    }
}
