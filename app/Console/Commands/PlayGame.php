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
    protected $signature = 'game:play {player_id}';

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
        $player_id = $this->argument('player_id');
        if(!is_numeric($player_id)) {
            $this->error('Player id must be a number');
        }

        $this->info($player_id);
    }
}
