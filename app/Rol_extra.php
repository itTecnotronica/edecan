<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rol_extra extends Model
{
	protected $guarded = ['id'];    

    public function rol_de_usuario()
    {
        return $this->belongsTo('App\Rol_de_usuario');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    protected $table = 'roles_extra';

}
