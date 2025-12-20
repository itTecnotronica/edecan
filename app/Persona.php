<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
	protected $guarded = ['id'];    

    protected $table = 'tb_personas';  
}
