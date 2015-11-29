<?php namespace tests\Export;

class ExportCardTest extends AbstractCardTest
{
    public function test_export_card_for_simple_card() {
        $wisp = $this->playCard('Wisp', 1);
        $export = $wisp->export();

        $expected =
        '{
            "Card": {
                "cost": 0,
                "name": "Wisp",
                "play_order_id": 3
            }
        }';

        $this->assertJsonEquals($expected, $export);
    }

}