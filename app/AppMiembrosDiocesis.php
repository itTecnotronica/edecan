<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppMiembrosDiocesis extends Model
{
    protected $table = 'app_miembros_diocesis';
    
    protected $primaryKey = 'UUID';
    public $incrementing = false;
    protected $keyType = 'string';
    
    public $timestamps = false;

    protected $fillable = [
        'Name',
        'UUID',
        'Lumisial',
        'State'
    ];
}
