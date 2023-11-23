<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function adminlte_image()
    {
        $id=Auth::user()->id;
        $imagen='img/usuarios/'.$id.'png';

            if (!file_exists($imagen)) {
              $imagen = "img/usuarios/user.png";
            }

        $url=asset($imagen.'?'.time());
        return $url;
      //  return 'https://picsum.photos/300/300';
    }

    public function adminlte_desc()
    {
        $nombres='';
        foreach (Auth::user()->roles as $rol){
          $nombres.='['. $rol->name .']';  
        }
        return $nombres;
       // return Auth::user()->roles[1]->name;
    }

    public function adminlte_profile_url()
    {
        return 'perfil/show';
       // return 'profile/username';
    }
}
