<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Moneda extends Model
{
    public function descrip_modelo()
    {
        return __($this->moneda);
    }

	protected $guarded = ['id'];    

}
