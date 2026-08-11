<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppMiembrosLumisial extends Model
{
    protected $table = 'app_miembros_lumisial';
    
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    
    public $timestamps = false; // si la tabla no tiene created_at/updated_at

    protected $fillable = [
        'address',
        'city',
        'diocesisUuid',
        'email',
        'name',
        'stateUuid',
        'status',
        'totalMembers',
        'uuid',
        'valid',
        'lumisialesHabilitados',
        'es_propio',
        'latitud',
        'longitud'
    ];
}
