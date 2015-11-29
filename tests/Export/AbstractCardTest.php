<?php namespace tests\Export;

use App\Models\HearthCloneTest;

abstract class AbstractCardTest extends HearthCloneTest
{
    public function flattenJson($json) {
        return json_encode(json_decode($json, true));
    }

    /**
     * Flatten both json values and then compare.
     *
     * @param string $expected
     * @param string $actual
     */
    public function assertJsonEquals($expected, $actual) {
        // TODO not the best way to compare, key order currently matters.
        $this->assertEquals($this->flattenJson($expected), $this->flattenJson($actual));
    }
}