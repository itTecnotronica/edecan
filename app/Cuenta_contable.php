<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cuenta_contable extends Model
{
	protected $guarded = ['id'];    

    protected $table = 'cuentas_contables';  
}
