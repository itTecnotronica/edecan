<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\MyResetPassword;

class Ejecutivo extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    /*
    protected $fillable = [
        'name', 'email', 'password',
    ];
    */
    protected $guarded = ['id'];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];  

    public function sucursal()
    {
        return $this->belongsTo('App\Sucursal');
    }

    public function rol_de_usuario()
    {
        return $this->belongsTo('App\Rol_de_usuario');
    }

    public function pais()
    {
        return $this->belongsTo('App\Pais');
    }

    public function idioma()
    {
        return $this->belongsTo('App\Idioma');
    }

    public function routeNotificationForTelegram()
    {
        return '632979534';
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MyResetPassword($token));
    }

    protected $table = 'users';
}
