<?php

namespace App;

use App\Rating;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
	protected $guarded = ['id'];    

    protected $fillable = ['id', 'inscripcion_id', 'rating'];
}
