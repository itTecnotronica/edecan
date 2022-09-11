<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log_accion extends Model
{
	protected $guarded = ['id'];    

    protected $table = 'log_acciones';  
}
