<?php

declare(strict_types=1);

namespace Revolution\Bluesky\Client\SubClient;

use Revolution\AtProto\Lexicon\Contracts\App\Bsky\Notification;
use Revolution\Bluesky\Client\Concerns\AppBskyNotification;
use Revolution\Bluesky\Client\HasHttp;
use Revolution\Bluesky\Contracts\XrpcClient;

class BskyNotification implements Notification, XrpcClient
{
    use AppBskyNotification;
    use HasHttp;
}
