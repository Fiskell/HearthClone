<?php

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/23/15
 * Time: 3:01 PM
 */
class CardTest extends TestCase
{
    /** @expectedException \App\Exceptions\MissingCardHandleException
     */
    public function testCardLoadFailsWhenNoCardNameSpecified() {
        $card = $this->app->make('Card');
        $card->load();
    }
}