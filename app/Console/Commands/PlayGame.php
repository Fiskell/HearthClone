<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PlayGame extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:play';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Play a game of Hearthstone!';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
//        app('Game')->start();
    }
}
