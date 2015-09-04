<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/29/15
 * Time: 3:16 PM
 */

namespace App\Models;

use App\Exceptions\UnknownCardNameException;

class CardSets
{
    protected $sets ;
    protected $set_names = ['Basic', 'Classic'];

    public function __construct() {
        // Load card sets into memory.
        $sets = $this->set_names;
        foreach($sets as $set) {
            $cards_in_set = [];

            $tmp_set_cards = file_get_contents(__DIR__ . '/../../resources/sets/' . $set . '.enUS.json');
            $tmp_set_cards = json_decode($tmp_set_cards, true);

            foreach($tmp_set_cards as $tmp_set_card) {
                $cards_in_set[$tmp_set_card['name']] = $tmp_set_card;
            }

            $this->sets[$set] = $cards_in_set;
        }
    }

    public function findCard($name=null) {

        $card_json = null;
        foreach($this->sets as $set) {
            $card_json = array_get($set, $name);
            if(!is_null($card_json)) {
                break;
            }
        }

        if(is_null($card_json)) {
            throw new UnknownCardNameException('Failed to find card handle ' . $name);
        }

        return $card_json;
    }

    /**
     * @return array
     */
    public function getSets() {
        return $this->sets;
    }

    /**
     * @return array
     */
    public function getSetsNames() {
        return $this->sets_names;
    }

}