<?php

use Illuminate\Support\Facades\Facade;

class Triggers extends Facade {

    protected static function getFacadeAccessor() { return 'TriggerQueue'; }

}