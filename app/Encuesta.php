<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

 
class Encuesta extends Model
{ 

    protected $guarded = ['id'];    
    protected $table = 'app_encuestas';
    protected $fillable = ['titulo', 'descripcion'];

    public function preguntas()
    {
        return $this->hasMany(Pregunta::class, 'id_encuesta');
    }
}

 
