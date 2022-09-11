<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{   
    protected $guarded = ['id'];    
    protected $connection = 'mautic';
    public $timestamps = false;

}
