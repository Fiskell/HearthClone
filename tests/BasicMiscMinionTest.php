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
}