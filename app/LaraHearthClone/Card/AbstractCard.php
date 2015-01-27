<?php
/**
 * Created by PhpStorm.
 * User: stephenfiskell
 * Date: 1/25/15
 * Time: 5:19 PM
 */

namespace App\LaraHearthClone\Card;

abstract class AbstractCard {
	protected $name;
	protected $cost;
	protected $rarity;
	protected $foil;
	protected $owner; // Me or Them
	protected $possesion; // Me or Them
}