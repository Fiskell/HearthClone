<?php namespace App\Game\Cards\Triggers;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/5/15
 * Time: 6:59 PM
 */
class TargetTypes
{
    public static $RANDOM_OPPONENT_CHARACTER        = 'random_opponent_character';
    public static $ALL_FRIENDLY_CHARACTERS          = 'all_friendly_characters';
    public static $ALL_OTHER_CHARACTERS             = 'all_other_characters';
    public static $ALL_OPPONENT_MINIONS             = 'all_opponent_minions';
    public static $OTHER_FRIENDLY_MINIONS_WITH_RACE = 'other_friendly_minions_with_race';
    public static $OTHER_FRIENDLY_MINIONS           = 'other_friendly_minions';
    public static $PROVIDED_MINION                  = 'provided_minion';
    public static $FRIENDLY_PLAYER                  = 'friendly_player';
    public static $FRIENDLY_HERO                    = 'friendly_hero';
    public static $OPPONENT_HERO                    = 'opponent_hero';
    public static $OPPONENT_WEAPON                  = 'opponent_weapon';
}