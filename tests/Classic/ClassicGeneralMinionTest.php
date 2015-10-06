<?php

use App\Game\Cards\Mechanics;
use App\Models\HearthCloneTest;

class ClassicGeneralMinionTest extends HearthCloneTest
{

    /* Abomination */
    // todo

    /* Abusive Sergeant */
    // todo

    /* Acolyte of Pain */
    // todo

    /* Al'Akir the Windlord */
    // todo

    /* Alarm-o-Bot */
    // todo

    /* Aldor Peacekeeper */
    // todo

    /* Alexstrasza */
    // todo

    /* Amani Berserker */
    // todo

    /* Ancient Brewmaster */
    // todo

    /* Ancient Mage */
    // todo

    /* Ancient Watcher */
    // todo

    /* Ancient of Lore */
    // todo

    /* Ancient of War */
    // todo

    /* Angry Chicken */
    // todo

    /* Arathi Weaponsmith */
    // todo

    /* Arcane Golem */
    // todo

    /* Archmage Antonidas */
    // todo

    /* Argent Commander */
    // todo

    /* Argent Protector */
    // todo

    /* Argent Squire */
    // todo

    /* Armorsmith */
    // todo

    /* Auchenai Soulpriest */
    // todo

    /* Azure Drake */
    // todo

    /* Baine Bloodhoof */
    // todo

    /* Baron Geddon */
    // todo

    /* Big Game Hunter */
    // todo

    /* Blood Imp */
    // todo

    /* Blood Knight */
    // todo

    /* Bloodmage Thalnos */
    // todo

    /* Bloodsail Corsair */
    // todo

    /* Bloodsail Raider */
    // todo

    /* Cabal Shadow Priest */
    // todo

    /* Cairne Bloodhoof */
    // todo

    /* Captain Greenskin */
    // todo

    /* Cenarius */
    // todo

    /* Coldlight Oracle */
    // todo

    /* Coldlight Seer */
    // todo

    /* Crazed Alchemist */
    // todo

    /* Cruel Taskmaster */
    // todo

    /* Cult Master */
    // todo

    /* Damaged Golem */
    // todo

    /* Dark Iron Dwarf */
    // todo

    /* Deathwing */
    // todo

    /* Defender */
    // todo

    /* Defender of Argus */
    // todo

    /* Defias Bandit */
    // todo

    /* Defias Ringleader */
    // todo

    /* Demolisher */
    // todo

    /* Devilsaur */
    // todo

    /* Dire Wolf Alpha */
    // todo

    /* Doomguard */
    // todo

    /* Doomsayer */
    // todo

    /* Dread Corsair */
    // todo

    /* Druid of the Claw */
    // todo

    /* Druid of the Claw */
    // todo

    /* Druid of the Claw */
    // todo

    /* Dust Devil */
    // todo

    /* Earth Elemental */
    // todo

    /* Earthen Ring Farseer */
    // todo

    /* Edwin VanCleef */
    // todo

    /* Emerald Drake */
    // todo

    /* Emperor Cobra */
    // todo

    /* Ethereal Arcanist */
    // todo

    /* Faceless Manipulator */
    // todo

    /* Faerie Dragon */
    // todo

    /* Felguard */
    // todo

    /* Fen Creeper */
    // todo

    /* Finkle Einhorn */
    // todo

    /* Flame Imp */
    // todo

    /* Flame of Azzinoth */
    // todo

    /* Flesheating Ghoul */
    // todo

    /* Frost Elemental */
    // todo

    /* Frothing Berserker */
    // todo

    /* Gadgetzan Auctioneer */
    // todo

    /* Gnoll */
    // todo

    /* Grommash Hellscream */
    // todo

    /* Gruul */
    // todo

    /* Harrison Jones */
    // todo

    /* Harvest Golem */
    // todo

    /* Hogger */
    // todo

    /* Hound */
    // todo

    /* Hungry Crab */
    // todo

    /* Hyena */
    // todo

    /* Illidan Stormrage */
    // todo

    /* Imp */
    // todo

    /* Imp Master */
    // todo

    /* Infernal */
    // todo

    /* Injured Blademaster */
    // todo

    /* Ironbeak Owl */
    public function test_ironbeak_owl_silences_minion() {
        $frostwolf_grunt = $this->playCard("Frostwolf Grunt", 1);
        $this->playCard("Ironbeak Owl", 2, [$frostwolf_grunt]);
        $this->assertFalse($frostwolf_grunt->hasMechanic(Mechanics::$TAUNT));
    }

    /* Jungle Panther */
    // todo

    /* Keeper of the Grove */
    // todo

    /* Kidnapper */
    // todo

    /* King Krush */
    // todo

    /* King Mukla */
    // todo

    /* Kirin Tor Mage */
    // todo

    /* Knife Juggler */
    // todo

    /* Laughing Sister */
    // todo

    /* Leeroy Jenkins */
    // todo

    /* Leper Gnome */
    // todo

    /* Lightspawn */
    // todo

    /* Lightwarden */
    // todo

    /* Lightwell */
    // todo

    /* Loot Hoarder */
    // todo

    /* Lord Jaraxxus */
    // todo

    /* Lorewalker Cho */
    // todo

    /* Mad Bomber */
    // todo

    /* Malygos */
    // todo

    /* Mana Addict */
    // todo

    /* Mana Tide Totem */
    // todo

    /* Mana Wraith */
    // todo

    /* Mana Wyrm */
    // todo

    /* Master Swordsmith */
    // todo

    /* Master of Disguise */
    // todo

    /* Millhouse Manastorm */
    // todo

    /* Mind Control Tech */
    // todo

    /* Mogu'shan Warden */
    // todo

    /* Molten Giant */
    // todo

    /* Mountain Giant */
    // todo

    /* Murloc Tidecaller */
    // todo

    /* Murloc Warleader */
    // todo

    /* Nat Pagle */
    // todo

    /* Nozdormu */
    // todo

    /* Onyxia */
    // todo

    /* Panther */
    // todo

    /* Patient Assassin */
    // todo

    /* Pint-Sized Summoner */
    // todo

    /* Pit Lord */
    // todo

    /* Priestess of Elune */
    // todo

    /* Prophet Velen */
    // todo

    /* Questing Adventurer */
    // todo

    /* Raging Worgen */
    // todo

    /* Ragnaros the Firelord */
    // todo

    /* Ravenholdt Assassin */
    // todo

    /* SI:7 Agent */
    // todo

    /* Savannah Highmane */
    // todo

    /* Scarlet Crusader */
    // todo

    /* Scavenging Hyena */
    // todo

    /* Sea Giant */
    // todo

    /* Secretkeeper */
    // todo

    /* Shadow of Nothing */
    // todo

    /* Shieldbearer */
    // todo

    /* Silver Hand Knight */
    // todo

    /* Silvermoon Guardian */
    // todo

    /* Snake */
    // todo

    /* Sorcerer's Apprentice */
    // todo

    /* Southsea Captain */
    // todo

    /* Southsea Deckhand */
    // todo

    /* Spellbender */
    // todo

    /* Spellbreaker */
    // todo

    /* Spirit Wolf */
    // todo

    /* Spiteful Smith */
    // todo

    /* Squire */
    // todo

    /* Squirrel */
    // todo

    /* Stampeding Kodo */
    // todo

    /* Stranglethorn Tiger */
    // todo

    /* Summoning Portal */
    // todo

    /* Sunfury Protector */
    // todo

    /* Sunwalker */
    // todo

    /* Sylvanas Windrunner */
    // todo

    /* Tauren Warrior */
    // todo

    /* Temple Enforcer */
    // todo

    /* The Beast */
    // todo

    /* The Black Knight */
    // todo

    /* Thrallmar Farseer */
    // todo

    /* Tinkmaster Overspark */
    // todo

    /* Tirion Fordring */
    // todo

    /* Treant */
    // todo

    /* Treant */
    // todo

    /* Treant */
    // todo

    /* Twilight Drake */
    // todo

    /* Unbound Elemental */
    // todo

    /* Venture Co. Mercenary */
    // todo

    /* Violet Apprentice */
    // todo

    /* Violet Teacher */
    // todo

    /* Void Terror */
    // todo

    /* Whelp */
    // todo

    /* Whelp */
    // todo

    /* Wild Pyromancer */
    // todo

    /* Windfury Harpy */
    // todo

    /* Wisp */
    // todo

    /* Worgen Infiltrator */
    // todo

    /* Worthless Imp */
    // todo

    /* Young Dragonhawk */
    // todo

    /* Young Priestess */
    // todo

    /* Youthful Brewmaster */
    // todo

    /* Ysera */
    // todo


}
