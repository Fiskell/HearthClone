<?php namespace tests;

use App\Game\Helpers\Random;
use App\Models\HearthCloneTest;

class HelperTest extends HearthCloneTest
{
    /* Test Random */
    public function test_coin_flip_returns_heads_or_tails() {
        $random = app('Random');
        $coin_flip = $random->flipCoin();
        $this->assertTrue(in_array($coin_flip, [Random::$HEADS, Random::$TAILS]));
    }
}