<?php

namespace Lucid\Jaak\Playback;

use Lucid\Jaak\Utils\Key;

class Api
{
    public static function sendSignedRequest(array $payload, string $uri, Key $key)
    {

        $key->sign(json_encode($payload));
    }
}