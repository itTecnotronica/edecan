<?php

namespace App;

use App\Testimonio;
use Illuminate\Database\Eloquent\Model;

class Testimonio extends Model
{
	protected $guarded = ['id'];    

    protected $fillable = ['id', 'inscripcion_id', 'texto', 'audio_url', 'video_url', 'autorizacion'];

}
