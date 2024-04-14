<?php

namespace App\Listeners;

use App\Events\ImageConverted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BroadcastImageConverted implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(ImageConverted $event): void
    {
        broadcast($event)->toOthers();
    }
}
