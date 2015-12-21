<?php

namespace App\Console\Commands;

use App\Game\Cards\Heroes\HeroClass;
use App\Game\Deck;
use Illuminate\Console\Command;

class Server extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a Hearthclone game server';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    }
}
