<?php namespace App\Models;
use App\Exceptions\MissingCardHandleException;
use App\Exceptions\UnknownCardHandleException;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/23/15
 * Time: 3:07 PM
 */
class Card
{
    protected $handle;
    protected $attack;
    protected $defense;

    public function load($handle=null) {
        if(is_null($handle)) {
           throw new MissingCardHandleException();
        }
        $this->handle = $handle;

        switch($this->handle) {
            case 'argent-squire':
                $this->attack = 1;
                $this->defense = 1;
                break;
            case 'knife-juggler':
                $this->attack = 3;
                $this->defense = 2;
                break;
            default:
                throw new UnknownCardHandleException();
        }
    }

    /**
     * @return null
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @return mixed
     */
    public function getAttack()
    {
        return $this->attack;
    }

    /**
     * @return mixed
     */
    public function getDefense()
    {
        return $this->defense;
    }

}