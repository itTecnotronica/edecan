<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rol_de_usuario extends Model
{
	protected $guarded = ['id'];    

    protected $table = 'roles_de_usuario';
}
