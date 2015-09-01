<?php namespace App\Models;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/31/15
 * Time: 11:32 PM
 */

class TriggerQueue
{

    /** @var TriggerableInterface[] $queue */
    private $queue = [];

    /**
     * Queue triggers so that we can parse them later in the right order.
     *
     * @param TriggerableInterface $trigger
     */
    public function queue(TriggerableInterface $trigger) {
        $this->queue[] = $trigger;
    }

    /**
     * Resolve current triggers.
     */
    public function resolveQueue() {
        foreach($this->queue as $key => $trigger) {
            $trigger->resolve();
            unset($this->queue[$key]);
        }
    }

    /**
     * @return TriggerableInterface[]
     */
    public function getQueue() {
        return $this->queue;
    }

}