<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/23/15
 * Time: 3:15 PM
 */

namespace App\Exceptions;
use Exception;

class UnknownCardHandleException extends Exception
{

    /**
     * MissingCardHandle constructor.
     */
    public function __construct()
    {
    }
}