<?php

use App\Game\Cards\Mechanics;
use App\Models\HearthCloneTest;

class BasicMiscMinionTest extends HearthCloneTest
{
    /* Gurubashi Berserker */
    public function test_gurubashi_berserker_gains_3_attack_when_damaged() {
        $gurubashi_berserker = $this->playCard('Gurubashi Berserker', 1);
        $gurubashi_berserker->takeDamage(1);
        $this->assertEquals(5, $gurubashi_berserker->getAttack());
        $gurubashi_berserker->takeDamage(1);
        $this->assertEquals(8, $gurubashi_berserker->getAttack());
    }

    /* Healing Totem */
    public function test_healing_totem_heals_damaged_minion_when_pass_turn() {
        $chillwind_yet = $this->playCard('Chillwind Yeti', 1);
        $this->playCard('Healing Totem', 1, [], true);
        $chillwind_yet->takeDamage(2);
        $this->assertEquals(3, $chillwind_yet->getHealth());
        $chillwind_yet->getOwner()->passTurn();
        $this->assertEquals(4, $chillwind_yet->getHealth());
    }

    /* Northshire Cleric */
    public function test_northshire_cleric_will_draw_card_when_damaged_minion_is_healed() {
        $this->playCard('Northshire Cleric', 1);
        $chillwind_yeti = $this->playCard('Chillwind Yeti', 1);
        $chillwind_yeti->takeDamage(3);
        $this->assertEquals(0, $this->game->getPlayer1()->getHandSize());
        $chillwind_yeti->heal(2);
        $this->assertEquals(1, $this->game->getPlayer1()->getHandSize());
    }

    public function test_northshire_cleric_will_not_draw_card_when_minion_healed_is_at_full_health() {
        $this->playCard('Northshire Cleric', 1);
        $chillwind_yeti = $this->playCard('Chillwind Yeti', 1);
        $this->assertEquals(0, $this->game->getPlayer1()->getHandSize());
        $chillwind_yeti->heal(2);
        $this->assertEquals(0, $this->game->getPlayer1()->getHandSize());
    }

    /* Starving Buzzard */
    public function test_starving_buzzard_draws_card_when_beast_is_played() {
        $this->playCard('Starving Buzzard', 1);
        $this->assertEquals(0, $this->game->getPlayer1()->getHandSize());
        $this->playCard('Timber Wolf', 1);
        $this->assertEquals(1, $this->game->getPlayer1()->getHandSize());
    }

    public function test_starving_buzzard_does_not_draw_card_when_non_beast_is_played() {
        $this->playCard('Starving Buzzard', 1);
        $this->assertEquals(0, $this->game->getPlayer1()->getHandSize());
        $this->playCard('Wisp', 1);
        $this->assertEquals(0, $this->game->getPlayer1()->getHandSize());
    }

    /* Warsong Commander */
    public function test_warsong_commander_gives_wisp_charge_when_summoned() {
        $this->playCard('Warsong Commander', 1);
        $wisp = $this->playCard('Wisp', 1);
        $this->assertTrue($wisp->hasMechanic(Mechanics::$CHARGE));
    }

    public function test_warsong_commander_does_not_give_chillwind_yeti_charge_when_summoned() {
        $this->playCard('Warsong Commander', 1);
        $chillwind_yeti = $this->playCard('Chillwind Yeti', 1);
        $this->assertFalse($chillwind_yeti->hasMechanic(Mechanics::$CHARGE));
    }

    /* Water Elemental */
    public function test_chillwind_yeti_is_frozen_when_attacked_by_water_elemental() {
        $water_elemental = $this->playCard('Water Elemental', 1);
        $chillwind_yeti  = $this->playCard('Chillwind Yeti', 2);

        $water_elemental->attack($chillwind_yeti);

        $is_frozen = $chillwind_yeti->isFrozen();
        $this->assertTrue($is_frozen);
    }

    public function test_chillwind_yeti_is_frozen_when_attacking_water_elemental() {
        $water_elemental = $this->playCard('Water Elemental', 1);
        $chillwind_yeti  = $this->playCard('Chillwind Yeti', 2);

        $chillwind_yeti->attack($water_elemental);

        $is_frozen = $chillwind_yeti->isFrozen();
        $this->assertTrue($is_frozen);
    }

    /** @expectedException \App\Exceptions\InvalidTargetException */
    public function test_chillwind_yeti_can_not_attack_when_frozen() {
        $water_elemental = $this->playCard('Water Elemental', $this->getActivePlayerId());
        $chillwind_yeti  = $this->playCard('Chillwind Yeti', $this->getDefendingPlayerId());

        $water_elemental->attack($chillwind_yeti);
        $this->game->getActivePlayer()->passTurn();
        $chillwind_yeti->attack($water_elemental);
    }

    public function test_chillwind_yeti_is_thawed_after_passing_turn() {
        $water_elemental = $this->playCard('Water Elemental', $this->getActivePlayerId());
        $chillwind_yeti  = $this->playCard('Chillwind Yeti', $this->getDefendingPlayerId());

        $water_elemental->attack($chillwind_yeti);
        $this->game->getActivePlayer()->passTurn();
        $this->assertTrue($chillwind_yeti->isFrozen());
        $this->game->getActivePlayer()->passTurn();
        $this->assertTrue(!$chillwind_yeti->isFrozen());
    }
}