<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/5/15
 * Time: 7:17 PM
 */

namespace App\Events;

use App\Game\Cards\Minion;

class SummonEvent extends Event
{
    /** @var  Minion $summoned_minion */
    protected $summoned_minion;

    protected $targets;

    public function __construct(Minion $summoned_minion, array $targets = []) {
        $this->summoned_minion = $summoned_minion;
        $this->targets         = $targets;
    }

    /**
     * @return Minion
     */
    public function getSummonedMinion() {
        return $this->summoned_minion;
    }

    /**
     * @return array
     */
    public function getTargets() {
        return $this->targets;
    }
}