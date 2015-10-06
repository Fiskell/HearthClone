<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 10/5/15
 * Time: 8:47 PM
 */

use App\Game\Cards\Mechanics;
use App\Models\HearthCloneTest;

class ClassicGeneralMinionTest extends HearthCloneTest
{
    /* Ironbeak Owl */
    public function test_ironbeak_owl_silences_minion() {
        $frostwolf_grunt = $this->playCard("Frostwolf Grunt", 1);
        $this->playCard("Ironbeak Owl", 2, [$frostwolf_grunt]);
        $this->assertFalse($frostwolf_grunt->hasMechanic(Mechanics::$TAUNT));
    }

}