<?php

namespace App\Console\Commands;

use App\Http\Controllers\FormController;
use Illuminate\Console\Command;

class HearthstoneCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:hearthstone-Command {param}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'enter current mmr to get what percentile you fall under';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $input = $this->argument('param');
        $controller = new FormController();
        $this->info($controller->findPercentiles($input));
    }
}
