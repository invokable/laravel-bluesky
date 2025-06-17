<?php

/**
 * GENERATED CODE.
 */

declare(strict_types=1);

namespace Revolution\Bluesky\Client\Concerns;

use Illuminate\Http\Client\Response;
use Revolution\AtProto\Lexicon\Contracts\Tools\Ozone\Verification;

trait ToolsOzoneVerification
{
    public function grantVerifications(array $verifications): Response
    {
        return $this->call(
            api: Verification::grantVerifications,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function listVerifications(?string $cursor = null, ?int $limit = 50, ?string $createdAfter = null, ?string $createdBefore = null, ?array $issuers = null, ?array $subjects = null, ?string $sortDirection = 'desc', ?bool $isRevoked = null): Response
    {
        return $this->call(
            api: Verification::listVerifications,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function revokeVerifications(array $uris, ?string $revokeReason = null): Response
    {
        return $this->call(
            api: Verification::revokeVerifications,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }
}
