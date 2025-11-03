<?php

/**
 * GENERATED CODE.
 */

declare(strict_types=1);

namespace Revolution\Bluesky\Client\Concerns;

use Illuminate\Http\Client\Response;
use Revolution\AtProto\Lexicon\Contracts\Com\Atproto\Lexicon;

trait ComAtprotoLexicon
{
    public function resolveLexicon(string $nsid): Response
    {
        return $this->call(
            api: Lexicon::resolveLexicon,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }
}
