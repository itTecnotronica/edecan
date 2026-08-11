<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppMiembrosLumisialDocumento extends Model
{
    protected $table = 'app_miembros_lumisial_documentos';
    
    protected $fillable = [
        'lumisial_uuid',
        'file_data',
        'mime_type',
        'original_name',
    ];
}
