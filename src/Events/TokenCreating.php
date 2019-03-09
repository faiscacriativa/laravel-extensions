<?php

namespace FaiscaCriativa\LaravelExtensions\Events;

use FaiscaCriativa\LaravelExtensions\Token;
use Illuminate\Queue\SerializesModels;

class TokenCreating
{
    use SerializesModels;

    public $token;

    /**
     * Create a new event instance.
     *
     * @param \FaiscaCriativa\LaravelExtensions\Token $token
     */
    public function __construct(Token $token)
    {
        $this->token = $token;
    }
}
