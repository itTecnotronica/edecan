<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistorialIngreso extends Model
{
    protected $table = 'historial_ingresos';

    protected $fillable = [
        'miembro_id',
        'fecha_ingreso',
        'tipo_ingreso',
        'numero_ingreso',
        'observaciones'
    ];

    public function miembro()
    {
        return $this->belongsTo(Miembros::class, 'miembro_id', 'id');
    }
}
