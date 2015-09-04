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
        $this->info('Printing out card names');
        /** @var CardSets $card_sets */
        $card_sets = app('CardSets');
//        $this->info(count($card_sets->getSets()));
        $names = [];
        foreach($card_sets->getSets() as $set) {
            $this->info(count($set));
            foreach($set as $card) {
                $names[] = array_get($card, 'name');
            }
        }
        $this->info(implode(',' . PHP_EOL, $names));
    }
}
