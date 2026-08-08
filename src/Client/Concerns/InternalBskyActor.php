<?php

/**
 * GENERATED CODE.
 */

declare(strict_types=1);

namespace Revolution\Bluesky\Client\Concerns;

use Illuminate\Http\Client\Response;
use Revolution\AtProto\Lexicon\Contracts\Internal\Bsky\Actor;

trait InternalBskyActor
{
    public function getProfiles(array $dids, ?string $viewer = null, ?array $socialProof = null, ?bool $includeTakedowns = null): Response
    {
        return $this->call(
            api: Actor::getProfiles,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }
}
