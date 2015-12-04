<?php

namespace App\Console\Commands;

use DateTime;
use Firebase\FirebaseLib;
use Illuminate\Console\Command;
use Firebase\Token\TokenException;
use Firebase\Token\TokenGenerator;

class TestFirebase extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'start:fire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Talk to firebase';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $DEFAULT_URL = 'https://inkeeper.firebaseio.com/';
            $DEFAULT_PATH = '/hello/firebase';

            $generator = new TokenGenerator(getenv('FIREBASE_SECRET'));
            $token = $generator
                ->setData(array('uid' => 'exampleID'))
                ->create();

            $firebase = new FirebaseLib($DEFAULT_URL, $token);

            // --- storing an array ---
            $test = array(
                "foo" => "bar",
                "i_love" => "lamp",
                "id" => 42
            );

            $dateTime = new DateTime();
            $firebase->set($DEFAULT_PATH . '/' . $dateTime->format('c'), $test);

            // --- storing a string ---
            $firebase->set($DEFAULT_PATH . '/name/contact001', "John Doe");

            // --- reading the stored string ---
            $name = $firebase->get($DEFAULT_PATH . '/name/contact001');
            $stuff = $firebase->get($DEFAULT_PATH);
            $this->info($stuff);
            $this->info($name);
        } catch (TokenException $e) {
            echo "Error: ".$e->getMessage();
        }
    }
}
