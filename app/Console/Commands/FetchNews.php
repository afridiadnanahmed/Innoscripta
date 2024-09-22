<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\NewsController;

class FetchNews extends Command
{
    // The name and signature of the command
    protected $signature = 'news:fetch';

    // The description of the command
    protected $description = 'Fetch and update news articles from news APIs';

    // The method that executes the command
    public function handle()
    {
        // Call methods to fetch news articles from your APIs
        $newsController = new NewsController();
        $newsController->fetchNewsFromNewsApi();
        $newsController->fetchNewsFromNYT();

        $this->info('News articles have been fetched successfully.');
    }
}
