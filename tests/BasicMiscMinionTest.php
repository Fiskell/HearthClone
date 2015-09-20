<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/19/15
 * Time: 11:50 AM
 */

namespace tests;


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
        $chillwind_yet = $this->playCardStrict('Chillwind Yeti', 1);
        $this->playCard('Healing Totem', 1);
        $chillwind_yet->takeDamage(2);
        dd($chillwind_yet);
        $this->assertEquals(3, $chillwind_yet->getHealth());
        $chillwind_yet->getOwner()->passTurn();
        $this->assertEquals(4, $chillwind_yet->getHealth());
    }
}