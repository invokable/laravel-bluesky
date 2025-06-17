<?php

/**
 * GENERATED CODE.
 */

declare(strict_types=1);

namespace Revolution\Bluesky\Client\Concerns;

use Illuminate\Http\Client\Response;
use Revolution\AtProto\Lexicon\Contracts\Tools\Ozone\Hosting;

trait ToolsOzoneHosting
{
    public function getAccountHistory(string $did, ?array $events = null, ?string $cursor = null, ?int $limit = 50): Response
    {
        return $this->call(
            api: Hosting::getAccountHistory,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }
}
