<?php

/**
 * GENERATED CODE.
 */

declare(strict_types=1);

namespace Revolution\Bluesky\Client\Concerns;

use Illuminate\Http\Client\Response;
use Revolution\AtProto\Lexicon\Contracts\Tools\Ozone\Safelink;

trait ToolsOzoneSafelink
{
    public function addRule(string $url, string $pattern, string $action, string $reason, ?string $comment = null, ?string $createdBy = null): Response
    {
        return $this->call(
            api: Safelink::addRule,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function queryEvents(?string $cursor = null, ?int $limit = 50, ?array $urls = null, ?string $patternType = null, ?string $sortDirection = 'desc'): Response
    {
        return $this->call(
            api: Safelink::queryEvents,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function queryRules(?string $cursor = null, ?int $limit = 50, ?array $urls = null, ?string $patternType = null, ?array $actions = null, ?string $reason = null, ?string $createdBy = null, ?string $sortDirection = 'desc'): Response
    {
        return $this->call(
            api: Safelink::queryRules,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function removeRule(string $url, string $pattern, ?string $comment = null, ?string $createdBy = null): Response
    {
        return $this->call(
            api: Safelink::removeRule,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function updateRule(string $url, string $pattern, string $action, string $reason, ?string $comment = null, ?string $createdBy = null): Response
    {
        return $this->call(
            api: Safelink::updateRule,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }
}
