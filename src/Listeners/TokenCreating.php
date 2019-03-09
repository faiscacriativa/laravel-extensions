<?php

namespace FaiscaCriativa\LaravelExtensions\Listeners;

use DeviceDetector\DeviceDetector;
use FaiscaCriativa\LaravelExtensions\Events\TokenCreating as TokenCreatingEvent;

class TokenCreating
{
    /**
     * Handle the event.
     *
     * @param \FaiscaCriativa\LaravelExtensions\Events\TokenCreating $event
     *
     * @return mixed
     */
    public function handle(TokenCreatingEvent $event)
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        $dd = new DeviceDetector($userAgent);
        $dd->parse();

        if (!$dd->isBot()) {
            $clientInfo = $dd->getClient();
            $osInfo = $dd->getOs();
            $device = $dd->getDeviceName();
            $brand = $dd->getBrandName();
            $model = $dd->getModel();

            $event->token->client_details = json_encode(
                [
                    'device' => [
                        'type' => $device,
                        'brand' => $brand,
                        'model' => $model
                    ],
                    'os' => [
                        'name' => $osInfo['name'],
                        'version' => $osInfo['version'],
                        'platform' => $osInfo['platform']
                    ],
                    'client' => [
                        'name' => $clientInfo['name'],
                        'version' => $clientInfo['version']
                    ]
                ]
            );
        }
    }
}
