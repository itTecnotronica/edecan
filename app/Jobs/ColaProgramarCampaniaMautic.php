<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Http\Controllers\MauticController;

class ColaProgramarCampaniaMautic implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $solicitud_id;
    public $tries = 1;
    public $timeout = 3000;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($solicitud_id)
    {
        $this->solicitud_id = $solicitud_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $MauticController = new MauticController();

        $MauticController->programarCampaniaMautic($this->solicitud_id);
    }
}
