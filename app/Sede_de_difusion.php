<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Provincia;

class Sede_de_difusion extends Model
{  
    protected $primaryKey = 'id_sede_de_difusion';
    protected $guarded = ['id'];  

    public function provincia()
    {
        return $this->belongsTo('App\Provincia');
    }

    public function descrip_modelo()
    {
        return $this->localidad.', '.$this->Provincia->provincia.', '.$this->Provincia->Pais->pais;
    }
    protected $connection = 'ageacac-ar';
    protected $table = 'tb_sede_de_difusion';  
    public $timestamps = false;
}
