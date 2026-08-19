<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Miembros extends Model
{
	protected $guarded = ['id'];    

    protected $table = 'app_miembros';  

    public function historialIngresos()
    {
        return $this->hasMany(HistorialIngreso::class, 'miembro_id', 'id');
    }

    public function zonaEncargada()
    {
        return $this->hasOne(AppMiembrosDiocesis::class, 'encargado_id', 'id');
    }
}
