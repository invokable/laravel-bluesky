<?php

declare(strict_types=1);

namespace Revolution\Bluesky\Client\SubClient;

use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Revolution\AtProto\Lexicon\Contracts\Chat\Bsky\Actor;
use Revolution\AtProto\Lexicon\Contracts\Chat\Bsky\Convo;
use Revolution\AtProto\Lexicon\Contracts\Chat\Bsky\Moderation;
use Revolution\AtProto\Lexicon\Contracts\Chat\Bsky\Notification;
use Revolution\Bluesky\Client\Concerns\ChatBskyActor;
use Revolution\Bluesky\Client\Concerns\ChatBskyConvo;
use Revolution\Bluesky\Client\Concerns\ChatBskyModeration;
use Revolution\Bluesky\Client\Concerns\ChatBskyNotification;
use Revolution\Bluesky\Client\HasHttp;
use Revolution\Bluesky\Contracts\XrpcClient;

/**
 * Chat / DM Client.
 *
 * chat.bsky
 */
class ChatClient implements Actor, Convo, Notification, XrpcClient
{
    use ChatBskyActor;
    use ChatBskyConvo;
    use ChatBskyNotification;
    use Conditionable;
    use HasHttp;
    use Macroable;

    public const CHAT_SERVICE_DID = 'did:web:api.bsky.chat#bsky_chat';
}
