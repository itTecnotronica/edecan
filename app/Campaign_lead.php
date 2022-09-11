<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign_lead extends Model
{
    protected $primaryKey = null;
    public $incrementing = false;
    protected $connection = 'mautic';
    public $timestamps = false;
    protected $table = 'campaign_leads';
    
}
