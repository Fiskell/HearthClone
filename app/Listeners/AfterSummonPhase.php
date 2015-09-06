<?php namespace App\Listeners;

use App\Events\AfterSummonPhaseEvent;
use App\Exceptions\DumbassDeveloperException;
use App\Exceptions\InvalidTargetException;
use App\Models\Minion;
use App\Models\TriggerableInterface;
use App\Models\TriggerQueue;

class AfterSummonPhase implements TriggerableInterface
{
    /** @var  AfterSummonPhaseEvent $event */
    private $event;

    /**
     * Handle the event.
     *
     * @param  AfterSummonPhaseEvent $event
     * @return void
     */
    public function handle(AfterSummonPhaseEvent $event) {
        $this->event = $event;

        /** @var TriggerQueue $trigger_queue */
        $trigger_queue = app('TriggerQueue');
        $trigger_queue->queue($this);
    }

    public function resolve() {
        $summoned_minion            = $this->event->getSummonedMinion();
        $player                     = $summoned_minion->getOwner();
        $player_minions             = $player->getMinionsInPlay();
        $opponent                   = $player->getOtherPlayer();
        $opponent_minions           = $opponent->getMinionsInPlay();
        $hero_id                    = $opponent->getHero()->getId();
        $opponent_minions[$hero_id] = $opponent->getHero();

        $trigger_json  = file_get_contents(__DIR__ . '/../../resources/triggers/Classic.json');
        $trigger_array = json_decode($trigger_json, true);

        foreach ($player_minions as $minion) {
            // todo may change depending on card
            if ($minion->getId() == $summoned_minion->getId()) {
                continue;
            }

            // todo assumes we only have one trigger.
            $trigger = array_get($trigger_array, $minion->getName() . '.triggers.0.after_summon_phase');

            if (is_null($trigger)) {
                continue;
            }

            $target_type = array_get($trigger, 'targets.type');
            if (is_null($target_type)) {
                throw new DumbassDeveloperException('Missing target type for ' . $minion->getName());
            }

            switch ($target_type) {
                case 'random_opponent_character':
                    // todo fix random numbers using ioc
                    $opponent_minion_keys = array_keys(
                        array_sort($opponent_minions, function (Minion $value) {
                            return $value->getId();
                        })
                    );

                    $random_key = $minion->getRandomNumber();
                    $targets    = [$opponent_minions[$opponent_minion_keys[$random_key]]];
                    break;
                default:
                    throw new DumbassDeveloperException('Unknown target type ' . $target_type);
            }

            // Should take damage.
            $damage = array_get($trigger, 'damage.value');
            if (!is_null($damage)) {
                if (!count($targets)) {
                    throw new InvalidTargetException('You must have at least one target');
                }

                /** @var Minion $target */
                foreach ($targets as $target) {
                    $target->takeDamage($damage);
                }
            }
        }
    }
}
