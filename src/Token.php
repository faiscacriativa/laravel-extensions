<?php

namespace FaiscaCriativa\LaravelExtensions;

use FaiscaCriativa\LaravelExtensions\Events\TokenCreating as TokenCreatingEvent;
use FaiscaCriativa\LaravelExtensions\Events\TokenDeleted as TokenDeletedEvent;

class Token extends \Laravel\Passport\Token
{
    protected $dispatchesEvents = [
        'creating' => TokenCreatingEvent::class,
        'deleted'  => TokenDeletedEvent::class
    ];
}
