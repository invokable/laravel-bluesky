<?php

/**
 * GENERATED CODE.
 */

declare(strict_types=1);

namespace Revolution\Bluesky\Client\Concerns;

use Illuminate\Http\Client\Response;
use Revolution\AtProto\Lexicon\Contracts\Tools\Ozone\Inbox;

trait ToolsOzoneInbox
{
    public function appealActionedSubject(array $subject, ?array $action = null, ?string $reason = null, ?array $modTool = null): Response
    {
        return $this->call(
            api: Inbox::appealActionedSubject,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }
}
