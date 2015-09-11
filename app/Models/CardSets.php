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
    public static $BASIC                = 'Basic';
    public static $CLASSIC              = 'Classic';
    public static $BLACKROCK_MOUNTAIN   = 'Blackrock Mountain';
    public static $CURSE_OF_NAXXRAMAS   = 'Curse of Naxxramas';
    public static $GOBLINS_VS_GNOMES    = 'Goblins vs Gnomes';
    public static $THE_GRAND_TOURNAMENT = 'The Grand Tournament';

    protected     $sets;
    public static $set_names = ['b'   => 'Basic',
                                'c'   => 'Classic',
                                'bm'  => 'Blackrock Mountain',
                                'con' => 'Curse of Naxxramas',
                                'gvg' => 'Goblins vs Gnomes',
                                'tgt' => 'The Grand Tournament'];

    public function __construct() {
        // Load card sets into memory.
        $sets = self::$set_names;
        foreach ($sets as $set_abbr => $set) {
            $cards_in_set = [];

            $tmp_set_cards = file_get_contents(__DIR__ . '/../../resources/sets/' . $set . '.enUS.json');
            $tmp_set_cards = json_decode($tmp_set_cards, true);

            foreach ($tmp_set_cards as $tmp_set_card) {
                $tmp_set_card['set']      = $set;
                $tmp_set_card['set_abbr'] = $set_abbr;

                $cards_in_set[$tmp_set_card['name']] = $tmp_set_card;
            }

            $this->sets[$set] = $cards_in_set;
        }
    }

    public function findCard($name = null) {

        $card_json = null;
        foreach ($this->sets as $set) {
            $card_json = array_get($set, $name);
            if (!is_null($card_json)) {
                break;
            }
        }

        if (is_null($card_json)) {
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