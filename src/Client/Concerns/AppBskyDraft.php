<?php

/**
 * GENERATED CODE.
 */

declare(strict_types=1);

namespace Revolution\Bluesky\Client\Concerns;

use Illuminate\Http\Client\Response;
use Revolution\AtProto\Lexicon\Contracts\App\Bsky\Draft;

trait AppBskyDraft
{
    public function createDraft(array $draft): Response
    {
        return $this->call(
            api: Draft::createDraft,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function deleteDraft(string $id): Response
    {
        return $this->call(
            api: Draft::deleteDraft,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getDrafts(?int $limit = 50, ?string $cursor = null): Response
    {
        return $this->call(
            api: Draft::getDrafts,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function updateDraft(array $draft): Response
    {
        return $this->call(
            api: Draft::updateDraft,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }
}
