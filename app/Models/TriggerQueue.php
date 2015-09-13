<?php namespace App\Models;
use App\Listeners\AbstractTrigger;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/31/15
 * Time: 11:32 PM
 */

class TriggerQueue
{

    /** @var AbstractTrigger[] $queue */
    private $queue = [];

    /**
     * Queue triggers so that we can parse them later in the right order.
     *
     * @param $trigger
     */
    public function queue($trigger) {
        $this->queue[] = $trigger;
    }

    /**
     * Resolve current triggers.
     */
    public function resolveQueue() {
        foreach($this->queue as $key => $trigger) {
            unset($this->queue[$key]);
            $trigger->resolve();
        }
    }

    /**
     * @return TriggerableInterface[]
     */
    public function getQueue() {
        return $this->queue;
    }

}