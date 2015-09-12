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

    /* Dalaran Mage */
    public function test_dalaran_mage_increases_spell_power_by_one() {
        $this->playCard('Dalaran Mage', 1);
        $this->assertTrue($this->game->getPlayer1()->getSpellPowerModifier() == 1);
    }

    /* Kobold Geomancer */
    public function test_kobold_geomancer_increases_spell_power_by_one() {
        $this->playCard('Kobold Geomancer', 1);
        $this->assertTrue($this->game->getPlayer1()->getSpellPowerModifier() == 1);
    }

    /* Ogre Magi */
    public function test_ogre_magi_increases_spell_power_by_one() {
        $this->playCard('Ogre Magi', 1);
        $this->assertTrue($this->game->getPlayer1()->getSpellPowerModifier() == 1);
    }

    /* Wrath of Air Totem */
    public function test_wrath_of_air_totem_increases_spell_power_by_one() {
        $this->playCard('Wrath of Air Totem', 1);
        $this->assertTrue($this->game->getPlayer1()->getSpellPowerModifier() == 1);
    }
}