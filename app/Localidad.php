<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Provincia;

class Localidad extends Model
{
	protected $guarded = ['id'];    

    public function provincia()
    {
        return $this->belongsTo('App\Provincia');
    }

    public function zona()
    {
        return $this->belongsTo('App\Zona');
    }

    public function descrip_modelo()
    {
        return $this->localidad.', '.$this->Provincia->provincia.', '.$this->Provincia->Pais->pais;
    }

    protected $table = 'localidades';  
}
