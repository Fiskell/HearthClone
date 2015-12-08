<?php namespace App\Game\Cards\Triggers;

use App\Exceptions\DumbassDeveloperException;
use App\Exceptions\InvalidTargetException;
use App\Game\Cards\Card;
use App\Game\Cards\Minion;
use App\Game\Cards\TargetTypes\BoardTargetGroups;
use App\Game\Cards\TargetTypes\FriendlyHero;
use App\Game\Cards\TargetTypes\FriendlyPlayer;
use App\Game\Cards\TargetTypes\ProvidedMinion;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/5/15
 * Time: 6:59 PM
 */
class TargetTypes
{
    public static $PROVIDED_MINION                  = 'provided_minion';
    public static $PROVIDED_ENEMY_MINION            = 'provided_enemy_minion';
    public static $DAMAGED_PROVIDED_MINION          = 'damaged_provided_minion';
    public static $UNDAMAGED_PROVIDED_MINION        = 'undamaged_provided_minion';
    public static $RANDOM_OPPONENT_CHARACTER        = 'random_opponent_character';
    public static $ALL_CHARACTERS                   = 'all_characters';
    public static $ALL_MINIONS                      = 'all_minions';
    public static $ALL_FRIENDLY_CHARACTERS          = 'all_friendly_characters';
    public static $ALL_FRIENDLY_MINIONS             = 'all_friendly_minions';
    public static $ALL_OTHER_CHARACTERS             = 'all_other_characters';
    public static $All_OTHER_MINIONS_WITH_RACE      = 'all_other_minions_with_race';
    public static $ALL_OPPONENT_MINIONS             = 'all_opponent_minions';
    public static $ALL_OPPONENT_CHARACTERS          = 'all_opponent_characters';
    public static $OTHER_FRIENDLY_MINIONS_WITH_RACE = 'other_friendly_minions_with_race';
    public static $OTHER_FRIENDLY_MINIONS           = 'other_friendly_minions';
    public static $FRIENDLY_PLAYER                  = 'friendly_player';
    public static $FRIENDLY_HERO                    = 'friendly_hero';
    public static $FRIENDLY_WEAPON                  = 'friendly_weapon';
    public static $OPPONENT_HERO                    = 'opponent_hero';
    public static $OPPONENT_WEAPON                  = 'opponent_weapon';
    public static $ADJACENT_MINIONS                 = 'adjacent_minions';
    public static $SELF                             = 'self';

    /**
     * @param Card $trigger_card
     * @param $target_type
     * @param null $target_race
     * @param array $provided_targets
     * @return array
     * @throws DumbassDeveloperException
     * @throws InvalidTargetException
     */
    public static function getTargets(Card $trigger_card, $target_type, $target_race = null, $provided_targets = []) {
        $player   = $trigger_card->getOwner();
        $opponent = $player->getOtherPlayer();

        $opponent_minions                                          = $opponent->getMinionsInPlay();
        $opponent_minions_with_hero                                = $opponent_minions;
        $opponent_minions_with_hero[$opponent->getHero()->getId()] = $opponent->getHero();

        $player_minions                                        = $player->getMinionsInPlay();
        $player_minions_with_hero                              = $player_minions;
        $player_minions_with_hero[$player->getHero()->getId()] = $player->getHero();

        $all_minions_with_heroes = $opponent_minions_with_hero + $player_minions_with_hero;

        if (is_null($provided_targets)) {
            $provided_targets = [];
        }

        $player_minions   = BoardTargetGroups::removeDeadMinions($player_minions);
        $opponent_minions = BoardTargetGroups::removeDeadMinions($opponent_minions);

        $boardTargetGroups = new BoardTargetGroups();
        $boardTargetGroups->setProvidedTargets($provided_targets);
        $boardTargetGroups->setTriggerCard($trigger_card);

        $target_types = [
            TargetTypes::$PROVIDED_MINION => new ProvidedMinion(),
            TargetTypes::$FRIENDLY_HERO   => new FriendlyHero(),
            TargetTypes::$FRIENDLY_PLAYER => new FriendlyPlayer()
        ];

        $found_target = array_get($target_types, $target_type);
        if (!is_null($found_target)) {
            return $found_target->getTargets($boardTargetGroups);
        }

        switch ($target_type) {
            case TargetTypes::$ALL_CHARACTERS:
                break;
            case TargetTypes::$ALL_OTHER_CHARACTERS:
                $targets = $all_minions_with_heroes;
                unset($targets[$trigger_card->getId()]);
                break;
            case TargetTypes::$OPPONENT_HERO:
                $targets = [$opponent->getHero()];
                break;
            case TargetTypes::$ALL_FRIENDLY_CHARACTERS:
                $targets = $player_minions_with_hero;
                break;
            case TargetTypes::$OTHER_FRIENDLY_MINIONS:
                unset($player_minions[$trigger_card->getId()]);
                $targets = $player_minions;
                break;
            case TargetTypes::$RANDOM_OPPONENT_CHARACTER:
                $keys          = array_keys($opponent_minions_with_hero);
                $random_number = app('Random')->getFromRange(0, (count($keys) - 1));
                $targets       = [$opponent_minions_with_hero[$keys[$random_number]]];
                break;
            case TargetTypes::$ALL_OPPONENT_MINIONS:
                $targets = $opponent_minions;
                break;
            case TargetTypes::$OTHER_FRIENDLY_MINIONS_WITH_RACE:
                unset($player_minions[$trigger_card->getId()]);
                $targets = [];
                /** @var Minion $player_minion */
                foreach ($player_minions as $player_minion) {
                    if ($player_minion->getRace() == $target_race) {
                        $targets[] = $player_minion;
                    }
                }
                break;
            case TargetTypes::$All_OTHER_MINIONS_WITH_RACE:
                unset($player_minions[$trigger_card->getId()]);
                $targets = [];
                foreach ($player_minions as $player_minion) {
                    if ($player_minion->getRace() == $target_race) {
                        $targets[] = $player_minion;
                    }
                }
                foreach ($opponent_minions as $opponent_minion) {
                    if ($opponent_minion->getRace() == $target_race) {
                        $targets[] = $opponent_minion;
                    }
                }
                break;
            case TargetTypes::$ADJACENT_MINIONS:
                /** @var Minion $trigger_card */
                $adjacent_positions = [
                    ($trigger_card->getPosition() - 1),
                    ($trigger_card->getPosition() + 1)
                ];

                $targets = [];
                foreach ($player_minions as $minion) {
                    if (in_array($minion->getPosition(), $adjacent_positions)) {
                        $targets[] = $minion;
                    }
                }

                break;
            case TargetTypes::$SELF:
                $targets = [$trigger_card];
                break;
            case TargetTypes::$ALL_FRIENDLY_MINIONS:
                $targets = $player_minions;
                break;
            case TargetTypes::$OPPONENT_WEAPON:
                $targets = [$opponent->getHero()->getWeapon()];
                break;
            case TargetTypes::$UNDAMAGED_PROVIDED_MINION:
                /** @var Minion[] $targets */
                $targets = $provided_targets;
                foreach ($targets as $target) {
                    if ($target->getHealth() < $target->getMaxHealth()) {
                        throw new InvalidTargetException('Target must be undamaged');
                    }
                }
                break;
            case TargetTypes::$DAMAGED_PROVIDED_MINION:
                /** @var Minion[] $targets */
                $targets = $provided_targets;
                foreach ($targets as $target) {
                    if ($target->getHealth() == $target->getMaxHealth()) {
                        throw new InvalidTargetException('Target must be damaged');
                    }
                }
                break;
            case TargetTypes::$ALL_OPPONENT_CHARACTERS:
                $targets = $opponent_minions_with_hero;
                break;
            case TargetTypes::$ALL_MINIONS:
                $targets = $opponent_minions + $player_minions;
                break;
            case TargetTypes::$PROVIDED_ENEMY_MINION:
                /** @var Minion $target */
                $target = current($provided_targets);
                if (!array_get($opponent_minions, $target->getId())) {
                    throw new InvalidTargetException('Target must belong to opponent');
                }
                $targets = [$target];
                break;
            case TargetTypes::$FRIENDLY_WEAPON:
                $targets = [$player->getHero()->getWeapon()];
                break;
            default:
                throw new DumbassDeveloperException('Unknown target type ' . $target_type);
        }

        return $targets;
    }

}