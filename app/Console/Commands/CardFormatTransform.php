<?php namespace App\Console\Commands;

use App\Game\CardSets\CardSets;
use Illuminate\Console\Command;

class CardFormatTransform extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'card:transform';

    public $card_sets = null;

    /**
     * Create a new command instance.
     *
     * @param CardSets $card_sets
     */
    public function __construct(CardSets $card_sets) {
        parent::__construct();
        $this->card_sets = $card_sets;
    }

    public function handle() {
        $cards     = json_decode(file_get_contents(base_path() . '/resources/triggers/Classic.json'), true);
        $new_cards = [];
        foreach ($cards as $card_name => $card) {
            $top_key = array_keys($card)[0];

            if ($top_key == "spellpower" || $top_key == "overload") {
                $new_cards[$card_name] = $card;
                continue;
            }

            $properties     = $card[$top_key];
            $new_properties = [];

            $target      = array_get($properties, 'targets.type');
            if($target) {
                $new_properties['target_type'] = $target;
            }

            $target_race = array_get($properties, 'targets.race');
            if($target_race) {
                $new_properties['target_race'] = $target_race;
            }

            $buffs = ['enchantment', 'spell'];

            $property_keys = array_keys($properties);
            foreach ($property_keys as $index => $property_key) {
                if ($property_key == 'targets') {
                    unset($property_keys[$index]);
                    continue;
                }

                if(in_array($property_key, $buffs)) {

                    $new_properties['buff'] = $property_key;
                    $tmp_properties = $properties[$property_key];

                    foreach($tmp_properties as $key => $value) {

                        if($key == 'health' && $value < 0) {
                            $new_properties['damage'] = abs($value);
                            unset($new_properties['buff']);
                            continue;
                        }

                        if($key == 'name') {
                            $new_properties['buff_name'] = $value;
                            continue;
                        }

                        $new_properties[$key] = $value;
                    }
                    continue;
                }

                if($top_key == 'aura') {
                    $new_properties['buff'] = 'aura';
                    continue;
                }

                $new_properties[$property_key] = $properties[$property_key];
            }
            ksort($new_properties);
            $new_cards[$card_name] = [ $top_key => [$new_properties]];
            ksort($new_cards);
        }
        print_r(json_encode($new_cards));
//        dd($new_cards);
    }

}
