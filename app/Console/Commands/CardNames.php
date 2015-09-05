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
    protected $signature = 'card:names {set_code} {card_type?} {--list_types}';

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
    public function handle() {
        $list_types = $this->option('list_types');
        $set_code   = $this->argument('set_code');
        $set_name   = array_get(CardSets::$set_names, $set_code);
        if (is_null($set_name)) {
            $this->info('Invalid set code ' . $set_code);

            return;
        }

        $card_type = $this->argument('card_type');

        /** @var CardSets $card_sets */
        $card_sets = app('CardSets');
        $names     = [];
        foreach ($card_sets->getSets() as $key => $set) {

            if ($key != $set_name) {
                continue;
            }

            foreach ($set as $card) {
                if($list_types) {
                    $names[$card['type']] = $card['type'];
                } else {
                    if (array_get($card, 'type') == $card_type) {
                        $names[] = array_get($card, 'name');
                    }
                }
            }

        }

        $imploded = implode(PHP_EOL, array_values($names));
        $this->info($imploded);
    }
}
