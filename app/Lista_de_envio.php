<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lista_de_envio extends Model
{
	protected $guarded = ['id'];    

    public function encabezado_de_envio()
    {
        return $this->belongsTo('App\Encabezado_de_envio');
    } 
    
    public function tipo_de_lista_de_envio()
    {
        return $this->belongsTo('App\Tipo_de_lista_de_envio');
    } 

    protected $table = 'listas_de_envios';

}
