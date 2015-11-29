<?php namespace tests\Export;

class ExportCardTest extends AbstractCardTest
{
    public function test_export_card_for_minion() {
        $wisp   = $this->playCard('Wisp', 1);
        $export = $wisp->export();

        $expected = '{
            "Minion": {
                "cost":0,
                "name":"Wisp",
                "play_order_id":3,
                "attack":1,
                "health":1,
                "max_health":1,
                "race":null,
                "alive":true,
                "sleeping":false,
                "frozen":false,
                "times_attacked_this_turn":0,
                "position":3
            }
        }';

        $this->assertJsonEquals($expected, $export);
    }

    public function test_export_card_for_weapon() {
        $wisp   = $this->playCard("Assassin's Blade", 1);
        $export = $wisp->export();

        $expected = '{
            "Weapon": {
                "cost":5,
                "name":"Assassin\'s Blade",
                "play_order_id":3,
                "attack":3,
                "durability":4
            }
        }';

        $this->assertJsonEquals($expected, $export);
    }
}