<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    public function descrip_modelo()
    {    

    	$descripcion = $this->app;

        return $descripcion;
    }

	protected $guarded = ['id'];    

}
