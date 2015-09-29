<?php namespace App\Game\Cards\Triggers;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/5/15
 * Time: 6:59 PM
 */
class TargetTypes
{
    public static $PROVIDED_MINION                  = 'provided_minion';
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
    public static $OPPONENT_HERO                    = 'opponent_hero';
    public static $OPPONENT_WEAPON                  = 'opponent_weapon';
    public static $ADJACENT_MINIONS                 = 'adjacent_minions';
    public static $SELF                             = 'self';
}