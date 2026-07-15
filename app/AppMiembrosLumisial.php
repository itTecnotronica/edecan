<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppMiembrosLumisial extends Model
{
    protected $table = 'app_miembros_lumisial';
    
    // Asumiendo que la PK es 'uuid' o id autonumerico, pero como hay uuid lo definiremos:
    // protected $primaryKey = 'id';
    
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
        'lumisialesHabilitados'
    ];
}
