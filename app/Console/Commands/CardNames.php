<?php

namespace App\Console\Commands;

use App\Models\CardSets;
use Illuminate\Console\Command;

class CardNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'card:names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all card names from json files';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /** @var CardSets $card_sets */
        $card_sets = app('CardSets');
        $names = [];
        foreach($card_sets->getSets() as $key => $set) {



//            protected $set_names = ['Basic', 'Classic',
// 'Blackrock Mountain', 'Curse of Naxxramas', 'Goblins vs Gnomes', 'The Grand Tournament']
            if($key != 'The Grand Tournament') {
                continue;
            }
            foreach($set as $card) {
                $names[] = array_get($card, 'name');
            }
        }
        $this->info(implode(PHP_EOL, $names));
    }
}
