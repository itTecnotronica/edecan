<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Respuesta extends Model
{
    protected $guarded = ['id'];    
    protected $table = 'app_respuestas';
    protected $fillable = ['id_encuesta', 'id_pregunta', 'respuesta', 'id_usuario'];
}
