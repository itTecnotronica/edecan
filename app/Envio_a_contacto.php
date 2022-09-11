<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Envio_a_contacto extends Model
{
	protected $guarded = ['id'];    

    public function contacto()
    {
        return $this->belongsTo('App\Contacto');
    }

    public function codigo_de_envio()
    {
        return $this->belongsTo('App\Codigo_de_envio');
    }

    public function medio_de_envio()
    {
        return $this->belongsTo('App\Medio_de_envio');
    }    

    protected $table = 'envios_a_contactos';
}
