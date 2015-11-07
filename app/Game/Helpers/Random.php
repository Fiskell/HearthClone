<?php namespace App\Game\Helpers;

class Random
{
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

}