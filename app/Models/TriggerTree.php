<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/30/15
 * Time: 8:26 PM
 */

namespace App\Models;


class TriggerTree
{

    protected $result_from_last_trigger;

    /**
     * @param Player $player
     * @param $trigger
     */
    public function addResult(Player $player, $trigger) {
        $this->result_from_last_trigger[] = $player->getPlayerId() . '.' . $trigger;
    }

    public function getResult() {
        return [];
    }

}