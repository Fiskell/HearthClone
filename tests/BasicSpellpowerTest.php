<?php
use App\Models\HearthCloneTest;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/12/15
 * Time: 1:54 AM
 */
class BasicSpellpowerTest extends HearthCloneTest
{
    /* Archmage */
    public function test_archmage_increases_spell_power_by_one() {
        $this->playCard('Archmage', 1);
        $this->assertTrue($this->game->getPlayer1()->getSpellPowerModifier() == 1);
    }
    

    /* Ogre Magi */
    public function test_ogre_magi_increases_spell_power_by_one() {
        $this->playCard('Ogre Magi', 1);
        $this->assertTrue($this->game->getPlayer1()->getSpellPowerModifier() == 1);
    }
}