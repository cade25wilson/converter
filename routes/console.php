<?php

use App\Jobs\IndexWebsite;
use Illuminate\Support\Facades\Schedule;

Schedule::command('app:clearoldfiles')->everyFifteenMinutes();
Schedule::job(new IndexWebsite)->daily();