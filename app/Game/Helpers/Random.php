<?php namespace App\Game\Helpers;

class Random
{
    public static $HEADS = 1;
    public static $TAILS = 0;

    /**
     * Return a random number in a range
     *
     * @param $rangeStart
     * @param $rangeEnd
     * @return int
     */
    public function getFromRange($rangeStart, $rangeEnd) {
        return rand($rangeStart, $rangeEnd);
    }

    /**
     * Return a 1 (heads) or a 0 (tails)
     *
     * @return int
     */
    public function flipCoin() {
        return rand(0, 1);
    }

}