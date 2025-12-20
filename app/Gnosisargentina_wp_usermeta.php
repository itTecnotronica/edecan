<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gnosisargentina_wp_usermeta extends Model
{  
    //protected $primaryKey = 'id_sede_de_difusion';
    protected $guarded = ['umeta_id'];  

    protected $connection = 'gnosis-ar-wp';
    protected $table = 'wp_usermeta';  
    public $timestamps = false;
}
