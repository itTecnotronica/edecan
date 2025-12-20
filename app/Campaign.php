<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;

class Campaign extends Model
{   
    protected $guarded = ['id'];    
    protected $connection = 'mautic';
    public $timestamps = false;

    public static function isDatabaseAvailable()
    {
        try {
            DB::connection('mautic')->getPdo();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
