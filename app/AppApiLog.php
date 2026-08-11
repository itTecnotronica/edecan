<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppApiLog extends Model
{
    protected $table = 'api_logs';
    
    protected $fillable = [
        'app_source',
        'url',
        'method',
        'nivel_error',
        'requestHeaders',
        'request_payload',
        'response_payload'
    ];
}
