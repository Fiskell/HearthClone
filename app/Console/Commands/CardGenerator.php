<?php

namespace App\Console\Commands;

use App\Game\Cards\Card;
use App\Game\Cards\Triggers\TargetTypes;
use App\Game\Cards\Triggers\TriggerTypes;
use App\Game\CardSets\CardSets;
use App\Game\Game;
use Exception;
use Illuminate\Console\Command;

class CardGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'card:generate {num_actions?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates the trigger action json for a specified card.';

    protected $card_sets;
    protected $card_name;
    protected $available_triggers;
    protected $available_target_types;
    protected $available_actions = [
        "create_mana_crystals",
        "damage",
        "destroy",
        "destroy_mana_crystals",
        "discard",
        "draw",
        "enchantment",
        "freeze",
        "silence",
        "spell",
        "summon"
    ];


    protected $buffs = [
        "aura",
        "enchantment",
        "spell"
    ];

    protected $buff_attributes = [
        "mechanics",
        "attack",
        "health",
        "max_health",
        "set_attack",
        "set_health",
        "armor",
    ];

    /**
     * Create a new command instance.
     *
     * @param CardSets $card_sets
     */
    public function __construct(CardSets $card_sets) {
        parent::__construct();
        $this->card_sets                = $card_sets;
        $this->available_triggers       = $this->getClassMembersValuesAsArray(new TriggerTypes());
        $this->available_target_types   = $this->getClassMembersValuesAsArray(new TargetTypes());
        $this->available_target_types[] = 'None';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $card_name       = $this->ask('What is the card\'s name?');
        $this->card_name = $card_name;

        try {
            /** @var Game $game */
            $game = app('Game');

            /** @var Card $card */
            $card = app('Card', [$game->getPlayer1(), $card_name]);

            $this->checkCardDoesNotExist($card);

            $num_actions = $this->argument('num_actions');
            if(!$num_actions) {
                $num_actions = 1;
            }

            $this->info($card->getText());

            /* Triggers */
            $trigger = $this->requestTrigger();
            $this->info("Trigger: " . $trigger);

            $trigger_json = [];
            for($i = 0; $i < $num_actions; $i++) {
                $this->info('Card action ' . ($i + 1));
                $this->info($card->getText());

                $trigger_obj = $this->buildTriggerJson($card_name, $trigger);

                $trigger_json[$trigger][] = $trigger_obj;
            }

            $json = [$card_name => $trigger_json];

            $json_formatted = json_encode($json, JSON_PRETTY_PRINT);
            $this->info($json_formatted);

            if ($this->confirm('Is the above card correct?')) {
                $this->writeCard($card, $json);

                $filename = $this->getCardFilename($card);
                $this->info($card->getName() . ' has been added to file ' . $filename);

                return true;
            }
            $this->error('Abort! Abort!  Whew, that was a close one.');
        } catch (Exception $ex) {
            $this->error($ex->getMessage());
        }

        return false;
    }


    /**
     * Return a formated json for the trigger selected.
     *
     * @param $card_name
     * @param $trigger
     * @return array
     * @throws Exception
     */
    private function buildTriggerJson($card_name, $trigger) {
        $race_targets_types = [
            TargetTypes::$All_OTHER_MINIONS_WITH_RACE,
            TargetTypes::$OTHER_FRIENDLY_MINIONS_WITH_RACE
        ];

        /* Spell Power */
        if ($trigger == TriggerTypes::$SPELLPOWER) {
            $spell_power = $this->ask('How much spell power does ' . $card_name . ' have?');

            return [TriggerTypes::$SPELLPOWER => $spell_power];
        }

        /* Overload */
        if ($trigger == TriggerTypes::$OVERLOAD) {
            $overload_quantity = $this->ask('How many mana crystals does ' . $card_name . ' overload?');

            return [TriggerTypes::$OVERLOAD => $overload_quantity];
        }

        /* Choose One */
        if ($trigger == TriggerTypes::$CHOOSE_ONE) {
            $number_of_options = $this->ask('How many choose options do you have?');

            $choose_json = [];
            for ($i = 0; $i < $number_of_options; $i++) {
                $this->info('Choose card option ' . ($i + 1) . '...');

                $choose_json[] = $this->buildTriggerJson($card_name, 'temp')['temp'];
            }

            return [TriggerTypes::$CHOOSE_ONE => $choose_json];
        }

        /* Targets */
        $target_info = $this->requestTarget();
        $this->info("Target: " . $target_info);
        if (in_array($target_info, $race_targets_types)) {
            $race = $this->ask('What is ' . $card_name . '\'s target race?');
        }
        /* Action */
        $action = $this->requestAction();
        $this->info("Action: " . $action);
        // todo attack_by_count, health_by_count, full_health

        /* Action Values */
        $action_value       = true;
        $no_additional_info = ["silence", "destroy", "freeze", "enchantment", "spell", "aura"];
        if (!in_array($action, $no_additional_info)) {
            $action_value = $this->requestSimpleActionValue($action);
            $this->info("Action Value: " . $action_value);
        }

        $trigger_obj = [];

        // Build json for targets.
        if ($target_info != 'None') {

            if (in_array($target_info, $race_targets_types) && isset($race)) {
                $trigger_obj['target_race'] = $race;
            }

            $trigger_obj['target_type'] = $target_info;
        }

        $action_array = $this->buildActionArray($action, $action_value);

        /* Aura */
        if ($trigger == TriggerTypes::$AURA) {
            $action_array['name'] = $this->ask('What is the name of the aura applied by ' . $card_name);
            // todo validation on aura name
        }

        if (in_array($action, $this->buffs)) {
            $trigger_obj += $action_array;
        } else {
            $trigger_obj[$action] = $action_array;
        }

        return $trigger_obj;
    }

    private function requestTrigger() {
        $trigger_input = $this->getTriggerPrompt();

        $trigger_value = array_get($this->available_triggers, $trigger_input);

        if (is_null($trigger_value)) {
            $this->error('Invalid trigger option ' . $trigger_input);
            $this->info("Please try again...");
            $this->requestTrigger();
        }

        return $trigger_value;
    }

    /**
     * @return array
     */
    private function getTriggerPrompt() {
        $available_triggers_string = $this->buildOptionString($this->available_triggers);

        return $this->ask("What trigger would you like to add?\n" . $available_triggers_string);
    }

    private function requestTarget() {
        $target_type_input = $this->getArrayPrompt("What target type would you like to add", $this->available_target_types);

        $target_type_value = array_get($this->available_target_types, $target_type_input);

        if (is_null($target_type_value)) {
            $this->error('Invalid target type ' . $target_type_input);
            $this->info("Please try again...");
            $this->requestTarget();
        }

        return $target_type_value;
    }

    private function getArrayPrompt($question, $optionArray) {
        $option_string = $this->buildOptionString($optionArray);

        return $this->ask($question . "\n" . $option_string);
    }

    /**
     * @param array $available_options
     * @return string
     */
    private function buildOptionString($available_options = []) {
        $available_string = "";
        foreach ($available_options as $key => $value) {
            $available_string .= '[' . $key . '] ' . $value . "\n";
        }

        return $available_string;
    }

    /**
     * @return array
     */
    private function getClassMembersValuesAsArray($obj) {
        $trigger_members    = get_class_vars(get_class($obj));
        $available_triggers = [];
        foreach ($trigger_members as $member) {
            $available_triggers[] = $member;
        }

        return $available_triggers;
    }

    private function requestAction() {
        $action_input = $this->getActionPrompt();
        $action_value = array_get($this->available_actions, $action_input);

        if (is_null($action_value)) {
            $this->error('Invalid action type ' . $action_value);
            $this->info("Please try again...");
            $this->requestTarget();
        }

        return $action_value;
    }

    private function getActionPrompt() {
        $available_action_string = $this->buildOptionString($this->available_actions);

        return $this->ask("What action does your card have?\n" . $available_action_string);
    }

    /**
     * This function is pretty useless.
     *
     * @param $action
     * @return string
     * @throws Exception
     */
    private function requestSimpleActionValue($action) {
        switch ($action) {
            case "create_mana_crystals":
                $prompt = "The number of mana crystals to create";
                break;
            case "damage":
                $prompt = "The amount of damage to deal";
                break;
            case "discard":
                $prompt = "The number of cards to discard";
                break;
            case "draw":
                $prompt = "The number of cards to draw";
                break;
            case "destroy_mana_crystals":
                $prompt = "The number of mana crystals to destroy";
                break;
            case "summon":
                $prompt = "The minion to summon and how many. [name:quantity]";
                break;
            default:
                throw new Exception('Invalid action type ' . $action);
        }

        return $this->ask($prompt);
    }

    /**
     * Parse action value.
     *
     * @param $action
     * @param $action_value
     * @return array|bool|int
     * @throws Exception
     */
    private function buildActionArray($action, $action_value) {
        $action_array = [];
        switch ($action) {
            case "destroy":
            case "silence":
            case "freeze":
                return true;
            case "damage":
            case "discard":
            case "draw":
            case "destroy_mana_crystals":
            case "create_mana_crystals":
                return (int)$action_value;
            case "spell":
            case "enchantment":
            case "aura":
                $number_of_buffs = $this->ask("How many buffs does " . $this->card_name . " give?");
                $action_array    = [];
                for ($i = 0; $i < $number_of_buffs; $i++) {
                    $buff_attribute       = array_get($this->buff_attributes, $this->getArrayPrompt(($i + 1) . "a) Choose a buff attribute:", $this->buff_attributes));
                    $buff_attribute_value = $this->ask(($i + 1) . "v) What is the " . $buff_attribute . " value?");
                    if ($buff_attribute == "mechanics") {
                        $buff_attribute_value = explode(',', $buff_attribute_value);
                    }
                    $action_array[$buff_attribute] = $buff_attribute_value;
                }

                if ($this->confirm('Does your buff have a name?')) {
                    $action_array['name'] = $this->ask('What is your enchantment name?');
                }
                $action_array['buff'] = $action;

                break;
            case "summon":
                $parts       = explode(':', $action_value);
                $minion_name = array_get($parts, 0);

                if ($minion_name) {
                    $action_array['name'] = $minion_name;
                }

                $quantity                 = (int)array_get($parts, 1, 1);
                $action_array['quantity'] = $quantity;

                break;
            default:
                throw new Exception('Invalid action type ' . $action);
        }

        return $action_array;
    }

    /**
     * Verify that the specified card is actually a card.
     *
     * @param Card $card
     * @throws Exception
     */
    private function checkCardDoesNotExist(Card $card) {
        $filename = $this->getCardFilename($card);
        $json     = @file_get_contents(__DIR__ . '/../../../resources/triggers/' . $filename);
        $array    = json_decode($json, true);
        if (array_get($array, $card->getName())) {
            throw new Exception('Card ' . $card->getName() . ' already exists in class ' . $filename);
        }
    }

    /**
     * Return the filename for the specified card.
     *
     * @param Card $card
     * @return string
     */
    private function getCardFilename(Card $card) {
        $set           = $card->getSet();
        $set_file_name = str_replace(' ', '_', $set);

        return $set_file_name . '.json';
    }

    /**
     * Write json to correct set file.
     *
     * @param $card
     * @param $card_trigger_info_array
     */
    private function writeCard($card, $card_trigger_info_array) {
        $filename = $this->getCardFilename($card);
        $filepath = __DIR__ . '/../../../resources/triggers/' . $filename;
        $json     = @file_get_contents($filepath);
        $array    = json_decode($json, true);
        $array    = array_merge($array, $card_trigger_info_array);
        ksort($array);
        $new_json = json_encode($array, JSON_PRETTY_PRINT);
        @file_put_contents($filepath, $new_json);
    }

}
