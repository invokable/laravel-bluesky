<?php

/**
 * GENERATED CODE.
 */

declare(strict_types=1);

namespace Revolution\Bluesky\Client\Concerns;

use Illuminate\Http\Client\Response;
use Revolution\AtProto\Lexicon\Contracts\Chat\Bsky\Moderation;

trait ChatBskyModeration
{
    public function getActorMetadata(string $actor): Response
    {
        return $this->call(
            api: Moderation::getActorMetadata,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getConvo(string $convoId): Response
    {
        return $this->call(
            api: Moderation::getConvo,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getConvoMembers(string $convoId, ?int $limit = 50, ?string $cursor = null): Response
    {
        return $this->call(
            api: Moderation::getConvoMembers,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getConvos(array $convoIds): Response
    {
        return $this->call(
            api: Moderation::getConvos,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getMessageContext(string $messageId, ?string $convoId = null, ?int $before = 5, ?int $after = 5, ?int $maxInterleavedSystemMessages = 10): Response
    {
        return $this->call(
            api: Moderation::getMessageContext,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function updateActorAccess(string $actor, bool $allowAccess, ?string $ref = null): Response
    {
        return $this->call(
            api: Moderation::updateActorAccess,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }
}
