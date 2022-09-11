<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipo_de_movimiento_contable extends Model
{
	protected $guarded = ['id'];    

    protected $table = 'tipos_de_movimientos_contables';  

}
