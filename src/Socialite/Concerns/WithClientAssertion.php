<?php

declare(strict_types=1);

namespace Revolution\Bluesky\Socialite\Concerns;

use Illuminate\Support\Str;
use Revolution\Bluesky\Crypto\JsonWebToken;
use Revolution\Bluesky\Socialite\Key\OAuthKey;

trait WithClientAssertion
{
    protected const CLIENT_ASSERTION_TYPE = 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer';

    protected function getClientAssertion(): string
    {
        $key = OAuthKey::load();

        $head = [
            'alg' => $key::ALG,
            'kid' => $key->toJWK()->kid(),
        ];

        $payload = [
            'iss' => $this->clientId,
            'sub' => $this->clientId,
            'aud' => $this->authUrl(),
            'jti' => Str::random(40),
            'iat' => now()->timestamp,
            'exp' => now()->addSeconds(600)->timestamp,
        ];

        return JsonWebToken::encode($head, $payload, $key->privatePEM());
    }
}
