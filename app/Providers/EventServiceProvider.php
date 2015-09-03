<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
        ],
        'App\Events\DeathEvent' => [
            'App\Listeners\Deathrattle'
        ],
        'App\Events\SummonMinionEvent' => [
            'App\Listeners\KnifeJuggler'
        ]
    ];
}
