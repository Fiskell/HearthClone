<?php
use App\Models\HearthCloneTest;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/5/15
 * Time: 7:00 PM
 */
class BattlecryTest extends HearthCloneTest
{
    public function test_houndmaster_does_not_target_himself() {
        $this->initPlayers();
        $houndmaster = $this->playCard($this->houndmaster_name, 1);
        $this->assertEquals(4, $houndmaster->getAttack());
        $this->assertEquals(3, $houndmaster->getHealth());
    }
}