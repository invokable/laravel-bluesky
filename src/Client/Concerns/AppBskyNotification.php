<?php

/**
 * GENERATED CODE.
 */

declare(strict_types=1);

namespace Revolution\Bluesky\Client\Concerns;

use Illuminate\Http\Client\Response;
use Revolution\AtProto\Lexicon\Contracts\App\Bsky\Notification;

trait AppBskyNotification
{
    public function getPreferences(): Response
    {
        return $this->call(
            api: Notification::getPreferences,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getUnreadCount(?bool $priority = null, ?string $seenAt = null): Response
    {
        return $this->call(
            api: Notification::getUnreadCount,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function listNotifications(?array $reasons = null, ?int $limit = 50, ?bool $priority = null, ?string $cursor = null, ?string $seenAt = null): Response
    {
        return $this->call(
            api: Notification::listNotifications,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function putPreferences(bool $priority): Response
    {
        return $this->call(
            api: Notification::putPreferences,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function putPreferencesV2(?array $chat = null, ?array $follow = null, ?array $like = null, ?array $likeViaRepost = null, ?array $mention = null, ?array $quote = null, ?array $reply = null, ?array $repost = null, ?array $repostViaRepost = null, ?array $starterpackJoined = null, ?array $subscribedPost = null, ?array $unverified = null, ?array $verified = null): Response
    {
        return $this->call(
            api: Notification::putPreferencesV2,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function registerPush(string $serviceDid, string $token, string $platform, string $appId): Response
    {
        return $this->call(
            api: Notification::registerPush,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function updateSeen(string $seenAt): Response
    {
        return $this->call(
            api: Notification::updateSeen,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }
}
