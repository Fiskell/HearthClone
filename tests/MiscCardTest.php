<?php
use App\Game\Cards\Heroes\HeroClass;
use App\Game\Cards\Heroes\Shaman;
use App\Game\Cards\Card;
use App\Game\Cards\CardType;
use App\Game\Cards\Mechanics;
use App\Models\HearthCloneTest;


/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/23/15
 * Time: 3:01 PM
 */
class MiscCardTest extends HearthCloneTest
{
    public function test_lights_justice_can_be_equipped() {
        $this->initPlayers(HeroClass::$PALADIN);
        $this->playWeaponCard('Light\'s Justice', 1);
        $this->assertEquals(1, $this->game->getPlayer1()->getHero()->getWeapon()->getAttack());
        $this->assertEquals(4, $this->game->getPlayer1()->getHero()->getWeapon()->getDurability());
        $this->assertEquals('Light\'s Justice', $this->game->getPlayer1()->getHero()->getWeapon()->getName());
    }

//todo need to do some mocking
//    public function test_knife_juggler_kills_enemy_minion_when_friendly_minion_is_summoned() {
//        $knife_juggler = $this->playCard('Knife Juggler', 1);
//        $knife_juggler->setRandomNumber(1);
//        $wisp = $this->playCard('Wisp', 2);
//        $this->playCard('Argent Squire', 1);
//        $this->assertFalse($wisp->isAlive());
//    }

//    public function test_knife_juggler_damages_hero_when_friendly_minion_is_summoned() {
//        $knife_juggler = $this->playCard('Knife Juggler', 1);
//        $knife_juggler->setRandomNumber(0);
//
//        $random_mock = Mockery::mock('Random')->makePartial();
//        $random_mock->shouldReceive('getFromRange')->once()->andReturn($this->player2->getHero()->getId());
//        $this->instance('Random', $random_mock);
//
//        $this->playCard('Argent Squire', 1);
//        $this->assertEquals(29, $this->game->getPlayer2()->getHero()->getHealth());
//    }

}

