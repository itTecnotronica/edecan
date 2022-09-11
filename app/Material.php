<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{

	protected $guarded = ['id'];   
    protected $table = 'materiales'; 


    public function idioma()
    {
        return $this->belongsTo('App\Idioma');
    }

    public function tipo_de_material()
    {
        return $this->belongsTo('App\Tipo_de_material');
    }

    public function autor()
    {
        return $this->belongsTo('App\Autor');
    }

}
