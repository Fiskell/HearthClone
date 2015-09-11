<?php
use App\Models\HearthCloneTest;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/10/15
 * Time: 10:48 PM
 */
class ClassicBattlecryTest extends HearthCloneTest
{
    /* Abusive Sergeant */
    public function test_abusive_sergeant_gives_wisp_2_attack() {
        $wisp = $this->playCard('Wisp', 1);
        $this->playCard('Abusive Sergeant', 1, [$wisp]);
        $this->assertEquals(3, $wisp->getAttack());
        $this->assertEquals(1, $wisp->getHealth());
        $this->assertTrue($wisp->isAlive());
    }

    
}