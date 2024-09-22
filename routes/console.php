<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use App\Models\News;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Controllers\NewsController;

// Register artisan command to display an inspiring quote
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Define the `news:fetch` command to fetch news
Artisan::command('news:fetch', function () {
    // Fetch news from your defined APIs
    (new NewsController)->fetchNewsFromNewsApi(); // Assuming it's a public method
    (new NewsController)->fetchNewsFromNYT();
})->describe('Fetch news from various news sources');

// Register the scheduled task in a separate command
Artisan::command('schedule:run', function (Schedule $schedule) {
    // Schedule the `news:fetch` command to run daily
    $schedule->command('news:fetch')->daily();
});
