<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Carnet extends Model
{
	protected $guarded = ['id'];    

    protected $table = 'app_carnets';  
}
