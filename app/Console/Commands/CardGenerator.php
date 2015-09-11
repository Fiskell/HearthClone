<?php

namespace App\Console\Commands;

use App\Models\Card;
use App\Models\CardSets;
use App\Models\Game;
use App\Models\Triggers\TargetTypes;
use App\Models\Triggers\TriggerTypes;
use Exception;
use Illuminate\Console\Command;

class CardGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'card:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates the trigger action json for a specified card.';

    protected $card_sets;
    protected $available_triggers;
    protected $available_target_types;
    protected $available_actions = [
        "destroy",
        "discard",
        "draw",
        "enchantment",
        "silence",
        "spell",
        "summon"
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
        $card_name = $this->ask('What is the card\'s name?');

        try {
            /** @var Game $game */
            $game = app('Game');

            /** @var Card $card */
            $card = app('Card', [$game->getPlayer1()]);
            $card->load($card_name);

            /* Triggers */
            $trigger = $this->requestTrigger();
            $this->info("Trigger: " . $trigger);

            /* Targets */
            $target_info = $this->requestTarget();
            $this->info("Target: " . $target_info);
            // todo quantity, race

            /* Action */
            $action = $this->requestAction();
            $this->info("Action: " . $action);
            // todo attack_by_count, health_by_count, full_health

            $action_value       = true;
            $no_additional_info = ["silence", "destroy"];
            if (!in_array($action, $no_additional_info)) {
                $action_value = $this->requestActionValue($action);
                $this->info("Action Value: " . $action_value);
            }

            $json = [
                $card_name => [
                    $trigger => [
                    ]
                ]
            ];

            if ($target_info != 'None') {
                // todo quantity and race
                $json[$card_name][$trigger]['targets'] = [
                    'type' => $target_info
                ];
            }

            $action_array = $this->buildActionArray($action, $action_value);

            $json[$card_name][$trigger][$action] = $action_array;

            $this->info(json_encode($json, JSON_PRETTY_PRINT));

            return true;
        } catch (Exception $ex) {
            $this->error($ex->getMessage());

            return false;
        }
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
        $target_type_input = $this->getTargetPrompt();

        $target_type_value = array_get($this->available_target_types, $target_type_input);

        if (is_null($target_type_value)) {
            $this->error('Invalid target type ' . $target_type_input);
            $this->info("Please try again...");
            $this->requestTarget();
        }

        return $target_type_value;
    }

    private function getTargetPrompt() {
        $available_target_string = $this->buildOptionString($this->available_target_types);

        return $this->ask("What target type would you like to add?\n" . $available_target_string);
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

    private function requestActionValue($action) {
        return $this->getActionValuePrompt($action);
    }

    private function getActionValuePrompt($action) {
        switch ($action) {
            case "discard":
                $prompt = "The number of cards to discard";
                break;
            case "draw":
                $prompt = "The number of cards to draw";
                break;
            case "spell":
            case "enchantment":
                $prompt = $action . " format is - Attack:Health:Taunt,Silence,Charge,...";
                break;
            case "summon":
                $prompt = "The minion to summon and how many. [name:quantity]";
                break;
            default:
                throw new Exception('Invalid action type ' . $action);
        }

        return $this->ask($prompt);
    }

    private function buildActionArray($action, $action_value) {
        $action_array = [];
        switch ($action) {
            case "destroy":
            case "silence":
                return true;
            case "discard":
            case "draw":
                return $action_value;
            case "spell":
            case "enchantment":
                $parts  = explode(':', $action_value);
                $attack = array_get($parts, 0);
                if ($attack) {
                    $action_array['attack'] = $attack;
                }

                $health = array_get($parts, 1);
                if ($health) {
                    $action_array['health'] = $health;
                }

                $mechanics = array_get($parts, 2);
                if ($mechanics) {
                    $mechanic_parts            = explode(',', $mechanics);
                    $action_array['mechanics'] = $mechanic_parts;
                }
                break;
            case "summon":
                $parts       = explode(':', $action_value);
                $minion_name = array_get($parts, 0);

                if ($minion_name) {
                    $action_array['name'] = $minion_name;
                }

                $quantity                 = array_get($parts, 1, 1);
                $action_array['quantity'] = $quantity;

                break;
            default:
                throw new Exception('Invalid action type ' . $action);
        }

        return $action_array;
    }
}
