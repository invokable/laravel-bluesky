<?php

/**
 * GENERATED CODE.
 */

declare(strict_types=1);

namespace Revolution\Bluesky\Client\Concerns;

use Illuminate\Http\Client\Response;
use Revolution\AtProto\Lexicon\Contracts\App\Bsky\Bookmark;

trait AppBskyBookmark
{
    public function createBookmark(string $uri, string $cid): Response
    {
        return $this->call(
            api: Bookmark::createBookmark,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function deleteBookmark(string $uri): Response
    {
        return $this->call(
            api: Bookmark::deleteBookmark,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getBookmarks(?int $limit = 50, ?string $cursor = null): Response
    {
        return $this->call(
            api: Bookmark::getBookmarks,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }
}
