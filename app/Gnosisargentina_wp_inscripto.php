<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gnosisargentina_wp_inscripto extends Model
{  
    //protected $primaryKey = 'id_sede_de_difusion';
    protected $guarded = ['ID'];  

    protected $connection = 'gnosis-ar-wp';
    protected $table = 'vw_inscriptos_gnosisargentina_wp';  
    public $timestamps = false;
}
