<?php

declare(strict_types=1);

namespace Revolution\Bluesky\FeedGenerator;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Revolution\Bluesky\Crypto\DidKey;
use Revolution\Bluesky\Crypto\JsonWebToken;
use Revolution\Bluesky\Facades\Bluesky;
use Revolution\Bluesky\Support\DidDocument;
use Revolution\Bluesky\Support\Identity;
use stdClass;

/**
 * @link https://github.com/bluesky-social/feed-generator/blob/main/src/auth.ts
 */
class ValidateAuth
{
    /**
     * @return string|null User's did
     */
    public function __invoke(?string $jwt, Request $request): ?string
    {
        [, $payload] = JsonWebToken::explode($jwt);

        /** @var string $did */
        $did = data_get($payload, 'iss');

        if (is_null($jwt) || ! Identity::isDID($did)) {
            return null;
        }

        /** @var DidKey|array $didKey */
        $didKey = cache()->remember(
            key: 'bluesky:did:key:'.$did,
            ttl: now()->addDay(),
            callback: fn () => DidKey::parse(DidDocument::make(Bluesky::identity()->resolveDID($did)->json())->publicKey())->toArray(),
        );

        $key = new Key($didKey['key'], $didKey['alg']);

        /** @var ?stdClass $payload */
        $payload = rescue(fn () => JWT::decode($jwt, $key));

        return $payload?->iss;
    }
}
