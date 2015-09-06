<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/5/15
 * Time: 7:28 PM
 */

namespace App\Listeners;

use App\Models\CardSets;

class SummonListener
{
    public $set_triggers = [];

    public function __construct() {
        foreach (CardSets::$set_names as $set_name) {
            $trigger_json = @file_get_contents(__DIR__ . '/../../resources/triggers/' . $set_name . '.json');

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