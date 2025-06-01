<?php

declare(strict_types=1);

namespace Revolution\Bluesky\Client\SubClient;

use Revolution\AtProto\Lexicon\Contracts\Com\Atproto\Admin;
use Revolution\Bluesky\Client\Concerns\ComAtprotoAdmin;
use Revolution\Bluesky\Client\HasHttp;
use Revolution\Bluesky\Contracts\XrpcClient;

class AdminClient implements Admin, XrpcClient
{
    use ComAtprotoAdmin;
    use HasHttp;
}
