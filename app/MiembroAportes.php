<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MiembroAportes extends Model
{
	protected $guarded = ['id'];    

    protected $table = 'app_miembros_aportes';  
}
