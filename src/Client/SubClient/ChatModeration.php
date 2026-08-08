<?php

declare(strict_types=1);

namespace Revolution\Bluesky\Client\SubClient;

use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Revolution\AtProto\Lexicon\Contracts\Chat\Bsky\Moderation;
use Revolution\Bluesky\Client\Concerns\ChatBskyModeration;
use Revolution\Bluesky\Client\HasHttp;
use Revolution\Bluesky\Contracts\XrpcClient;

/**
 * Chat / DM Client Moderation.
 *
 * chat.bsky.moderation
 */
class ChatModeration implements Moderation, XrpcClient
{
    use ChatBskyModeration;
    use Conditionable;
    use HasHttp;
    use Macroable;
}
