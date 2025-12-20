<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Miembros extends Model
{
	protected $guarded = ['id'];    

    protected $table = 'app_miembros';  
}
