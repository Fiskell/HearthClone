<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/11/15
 * Time: 11:31 PM
 */

namespace App\Console\Commands;

use App\Game\Cards\Card;
use App\Game\CardSets\CardSets;
use App\Game\Game;
use Exception;
use Illuminate\Console\Command;

class CardRemove extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'card:remove';

    /**
     * Create a new command instance.
     *
     * @param CardSets $card_sets
     */
    public function __construct(CardSets $card_sets) {
        parent::__construct();
        $this->card_sets = $card_sets;
    }

    public function handle() {
        $card_name = $this->ask('What card would you like to remove?');
        try {

            /** @var Game $game */
            $game = app('Game');

            /** @var Card $card */
            $card = app('Card', [$game->getPlayer1()]);
            $card->load($card_name);

            $this->checkCardExists($card);

            $this->removeCard($card);

            $filename = $this->getCardFilename($card);
            $this->info('Successfully removed ' . $card_name . ' from set ' . $filename);
            return true;
        } catch (Exception $ex) {
            $this->error($ex->getMessage());
        }
        return false;
    }

    private function checkCardExists(Card $card) {
        $filename = $this->getCardFilename($card);
        $json     = @file_get_contents(__DIR__ . '/../../../resources/triggers/' . $filename);
        $array    = json_decode($json, true);
        if (!array_get($array, $card->getName())) {
            throw new Exception('Card ' . $card->getName() . ' does not exist in class ' . $filename);
        }
    }

    private function getCardFilename(Card $card) {
        $set           = $card->getSet();
        $set_file_name = str_replace(' ', '_', $set);

        return $set_file_name . '.json';
    }

    /**
     * Write json to correct set file.
     *
     * @param Card $card
     */
    private function removeCard(Card $card) {
        $filename = $this->getCardFilename($card);
        $filepath = __DIR__ . '/../../../resources/triggers/' . $filename;
        $json     = @file_get_contents($filepath);
        $array    = json_decode($json, true);
        unset($array[$card->getName()]);
        ksort($array);
        $new_json = json_encode($array, JSON_PRETTY_PRINT);
        @file_put_contents($filepath, $new_json);
    }
}