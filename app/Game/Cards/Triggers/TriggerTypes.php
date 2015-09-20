<?php namespace App\Game\Cards\Triggers;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/5/15
 * Time: 8:03 PM
 */
class TriggerTypes
{
    public static $AFTER_SUMMON_PHASE = 'after_summon_phase';
    public static $SPELL_TEXT_PHASE   = 'spell_text_phase';
    public static $AURA               = 'aura';
    public static $BATTLECRY          = 'battlecry';
    public static $CHOOSE_ONE         = 'choose_one';
    public static $OVERLOAD           = 'overload';
    public static $SPELLPOWER         = 'spellpower';
    public static $ON_DAMAGE          = 'on_damage';
    public static $END_OF_TURN        = 'end_of_turn';
}