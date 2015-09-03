<?php

namespace App\Events;

use App\Models\HearthStoneEvent;
use App\Models\Minion;

class SummonMinionEvent extends Event implements HearthStoneEvent
{

    /** @var  Minion $summoned_minion */
    protected $summoned_minion;

    public function __construct(Minion $summoned_minion)
    {
        $this->summoned_minion = $summoned_minion;
    }


    /**
     * @return Minion
     */
    public function getSummonedMinion() {
        return $this->summoned_minion;
    }

}
