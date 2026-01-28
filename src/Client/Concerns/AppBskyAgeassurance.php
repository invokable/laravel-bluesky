<?php

/**
 * GENERATED CODE.
 */

declare(strict_types=1);

namespace Revolution\Bluesky\Client\Concerns;

use Illuminate\Http\Client\Response;
use Revolution\AtProto\Lexicon\Contracts\App\Bsky\Ageassurance;

trait AppBskyAgeassurance
{
    public function begin(string $email, string $language, string $countryCode, ?string $regionCode = null): Response
    {
        return $this->call(
            api: Ageassurance::begin,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getConfig(): Response
    {
        return $this->call(
            api: Ageassurance::getConfig,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getState(string $countryCode, ?string $regionCode = null): Response
    {
        return $this->call(
            api: Ageassurance::getState,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }
}
