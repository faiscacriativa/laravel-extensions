<?php

namespace FaiscaCriativa\LaravelExtensions;

use FaiscaCriativa\LaravelExtensions\Events\TokenCreating;

class Token extends \Laravel\Passport\Token
{
    protected $dispatchesEvents = [
        'creating' => TokenCreating::class,
        'deleted'  => TokenDeleted::class
    ];
}
