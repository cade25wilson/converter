<?php

use App\Jobs\IndexWebsite;
use Illuminate\Support\Facades\Schedule;

Schedule::command('app:clearoldfiles')->everyThirtyMinutes();
Schedule::command('app:database-backup')->daily();
Schedule::command('app:clear-old-backups')->daily();
Schedule::job(new IndexWebsite)->daily();