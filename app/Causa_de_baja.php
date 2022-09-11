<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Causa_de_baja extends Model
{
	protected $guarded = ['id'];    

    protected $table = 'causas_de_baja';
}
