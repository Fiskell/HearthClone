<?php namespace App\Models;

use Firebase\FirebaseLib;
use Firebase\Token\TokenGenerator;

class FirebaseModel
{
    const DEFAULT_URL = 'https://inkeeper.firebaseio.com/';

    /**
     * @var FirebaseLib $connection
     */
    private $connection;

    public function init() {
        $this->connection = new FirebaseLib(self::DEFAULT_URL, $this->getToken());
    }

    private function getToken() {
        // todo handle users better
        $generator = new TokenGenerator(getenv('FIREBASE_SECRET'));
        $token = $generator
            ->setData(array('uid' => 'exampleID'))
            ->create();
        return $token;
    }
}