<?php

namespace FaiscaCriativa\LaravelExtensions\Listeners;

use FaiscaCriativa\LaravelExtensions\Events\TokenDeleted as TokenDeletedEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TokenDeleted
{
    /**
     * Handle the event.
     *
     * @param \FaiscaCriativa\LaravelExtensions\Events\TokenDeleted $event
     *
     * @return mixed
     */
    public function handle(TokenDeletedEvent $event)
    {
        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $event->id)
            ->where('user_id', Auth::user()->id)
            ->delete();
    }
}
