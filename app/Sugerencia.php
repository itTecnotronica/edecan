<?php

namespace App;

use App\Sugerencia;
use Illuminate\Database\Eloquent\Model;

class Sugerencia extends Model
{
	protected $guarded = ['id'];    

    protected $fillable = ['id', 'inscripcion_id', 'texto', 'created_at', 'updated_at'];

}
