<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movimiento_contable extends Model
{
	protected $guarded = ['id'];    

    protected $table = 'movimientos_contables';  

    public function tipo_de_movimiento_contable()
    {
        return $this->belongsTo('App\Tipo_de_movimiento_contable');
    }

    public function caja_contable()
    {
        return $this->belongsTo('App\Caja_contable');
    }

    public function cuenta_contable()
    {
        return $this->belongsTo('App\Cuenta_contable');
    }

    public function subcuenta_contable()
    {
        return $this->belongsTo('App\Subcuenta_contable');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
