<?php namespace App\Models;
use App\Exceptions\MissingCardHandleException;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/23/15
 * Time: 3:07 PM
 */
class Card
{
    public function load($handle=null) {
       if(is_null($handle)) {
           throw new MissingCardHandleException();
       }
    }
}