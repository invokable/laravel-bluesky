<?php

/**
 * GENERATED CODE.
 */

declare(strict_types=1);

namespace Revolution\Bluesky\Client\Concerns;

use Illuminate\Http\Client\Response;
use Revolution\AtProto\Lexicon\Contracts\Chat\Bsky\Notification;

trait ChatBskyNotification
{
    public function getPreferences(): Response
    {
        return $this->call(
            api: Notification::getPreferences,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function putPreferences(?array $chat = null, ?array $chatRequest = null): Response
    {
        return $this->call(
            api: Notification::putPreferences,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }
}
