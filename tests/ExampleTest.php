<?php

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     * s
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->visit('/')
             ->see('So it begins');

    }
}
