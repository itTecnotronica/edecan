<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movimientos_Contables extends Model
{
	protected $guarded = ['id'];    

    protected $table = 'app_movimientos_contables';  
}
