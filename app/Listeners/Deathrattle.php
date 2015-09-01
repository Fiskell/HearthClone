<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/31/15
 * Time: 12:03 AM
 */

namespace App\Listeners;

use App\Events\DeathEvent;
use App\Models\Mechanics;

class Deathrattle
{

    public function handle(DeathEvent $event)
    {
        $killed_card = $event->getKilledCard();
        if(!$killed_card->hasMechanic(Mechanics::$DEATHRATTLE)) {
            return;
        }


        switch ($killed_card->getName()) {
            case 'Loot Hoarder':
                $killed_card->getOwner()->drawCard();
        }
    }
}