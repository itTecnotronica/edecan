<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    protected $guarded = ['id'];    
    protected $table = 'app_preguntas';
    protected $fillable = ['id_encuesta', 'orden', 'texto_pregunta'];

    public function encuesta()
    {
        return $this->belongsTo(Encuesta::class, 'id_encuesta');
    }
}