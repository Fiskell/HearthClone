<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/12/15
 * Time: 6:29 PM
 */

namespace App\Game\CardSets;

class CardSetTriggers
{
    private $set_triggers = [];

    public function __construct() {
        foreach (CardSets::$set_names as $set_name) {
            $trigger_json = @file_get_contents(base_path() . '/resources/triggers/' . $set_name . '.json');

            if (!$trigger_json) {
                continue;
            }

            $trigger_array      = json_decode($trigger_json, true);
            $this->set_triggers = array_merge($this->set_triggers, $trigger_array);
        }
    }

    /**
     * @return array
     */
    public function getSetTriggers() {
        return $this->set_triggers;
    }
}