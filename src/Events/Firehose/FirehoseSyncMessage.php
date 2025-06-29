<?php

declare(strict_types=1);

namespace Revolution\Bluesky\Events\Firehose;

use Illuminate\Foundation\Events\Dispatchable;

/**
 * @property-read string $did
 * @property-read int $seq
 * @property-read string $time
 * @property-read string $commit
 * @property-read string $blocks
 * @property-read string $raw
 */
final class FirehoseSyncMessage
{
    use Dispatchable;

    public function __construct(
        public string $did,
        public int $seq,
        public string $time,
        public string $commit,
        public string $blocks,
        public string $raw,
    ) {}
}
