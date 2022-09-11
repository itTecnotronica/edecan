<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instancia_de_envio extends Model
{
	protected $guarded = ['id'];    

    public function contacto_historico()
    {
        return $this->belongsTo('App\Contacto_historico');
    } 

    public function lista_de_envio()
    {
        return $this->belongsTo('App\Lista_de_envio');
    } 

    protected $table = 'instancias_de_envios';

}
