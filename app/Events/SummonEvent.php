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

    protected $choose_mechanic;

    public function __construct(Minion $summoned_minion, array $targets = [], $choose_mechanic=null) {
        $this->summoned_minion = $summoned_minion;
        $this->targets         = $targets;
        $this->choose_mechanic = $choose_mechanic;
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

    /**
     * @return null
     */
    public function getChooseMechanic() {
        return $this->choose_mechanic;
    }
}