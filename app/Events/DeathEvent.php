<?php namespace App\Events;

use App\Models\Card;

class DeathEvent extends Event
{
    /** @var  Card $killed_card */
    protected $killed_card;

    public function __construct(Card $killed_card) {
        $this->killed_card = $killed_card;
    }

    /**
     * @return Card
     */
    public function getKilledCard() {
        return $this->killed_card;
    }
}