<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Debito extends Model
{
	protected $guarded = ['id'];    

    protected $table = 'app_debitos';  
}
