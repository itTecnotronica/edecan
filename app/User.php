<?php

namespace App;

use App\Rol_extra;
use Laravel\Passport\HasApiTokens;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\MyResetPassword;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

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


    public function descrip_modelo()
    {    
        $descripcion = $this->name.' '.$this->apellido;
        
        if ($this->pais_id > 0) {
            $descripcion .= ' | Pais: '.$this->pais->pais;
        }

        if ($this->diocesis <> '') {
            $descripcion .= ' | Diocesis: '.$this->diocesis;
        }
        
        return $descripcion;
    }

    public function roles() {

        $Roles_extra = Rol_extra::where('user_id', $this->id)->get();
        $Coordinador = Equipo::where('coordinador_user_id', $this->id)->get();

        $Roles = array();

        if ($this->rol_de_usuario_id <> '') {
            $Roles[] = $this->rol_de_usuario_id;
        }
        foreach ($Roles_extra as $Rol_extra) {
            $Roles[] = $Rol_extra->rol_de_usuario_id;
        }

        if ($Coordinador->count() > 0) {
            $Roles[] = 21;
        }


        return $Roles;
    }

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


    public function usuario_por_equipo()
    {
        return $this->hasMany(Usuario_por_equipo::class);
    }

}
