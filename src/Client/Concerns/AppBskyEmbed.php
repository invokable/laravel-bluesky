<?php

/**
 * GENERATED CODE.
 */

declare(strict_types=1);

namespace Revolution\Bluesky\Client\Concerns;

use Illuminate\Http\Client\Response;
use Revolution\AtProto\Lexicon\Contracts\App\Bsky\Embed;

trait AppBskyEmbed
{
    public function getEmbedExternalView(string $url, ?string $canonicalUri = null, ?array $uris = null): Response
    {
        return $this->call(
            api: Embed::getEmbedExternalView,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }
}
