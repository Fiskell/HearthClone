<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/31/15
 * Time: 11:34 PM
 */

namespace App\Models;

interface TriggerableInterface
{
    public function resolve();
}