<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppMiembrosProvincia extends Model
{
    protected $table = 'app_miembros_provincia';
    
    public $timestamps = false;

    protected $fillable = [
        'description',
        'name',
        'uuid',
        'valid'
    ];
}
