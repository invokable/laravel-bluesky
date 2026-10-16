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

    public function getAccountStatus(?string $did = null): Response
    {
        return $this->call(
            api: Inbox::getAccountStatus,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getActionedSubject(string $subject, ?string $did = null, ?int $limit = 50, ?string $cursor = null): Response
    {
        return $this->call(
            api: Inbox::getActionedSubject,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getNotificationPreferences(?string $did = null): Response
    {
        return $this->call(
            api: Inbox::getNotificationPreferences,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getReport(int $id, ?string $did = null): Response
    {
        return $this->call(
            api: Inbox::getReport,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getUnreadCount(?string $section = null, ?string $did = null): Response
    {
        return $this->call(
            api: Inbox::getUnreadCount,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function listActionedSubjects(?string $did = null, ?string $filter = 'all', ?string $sortField = 'updatedAt', ?string $sortDirection = 'desc', ?int $limit = 50, ?string $cursor = null): Response
    {
        return $this->call(
            api: Inbox::listActionedSubjects,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function listNotifications(?string $section = null, ?array $reasons = null, ?bool $unreadOnly = null, ?int $limit = 50, ?string $cursor = null, ?string $did = null): Response
    {
        return $this->call(
            api: Inbox::listNotifications,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function listReports(?string $did = null, ?string $filter = 'all', ?string $sortField = 'updatedAt', ?string $sortDirection = 'desc', ?int $limit = 50, ?string $cursor = null): Response
    {
        return $this->call(
            api: Inbox::listReports,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function putNotificationPreferences(?bool $reports = null, ?bool $account = null, ?bool $labels = null): Response
    {
        return $this->call(
            api: Inbox::putNotificationPreferences,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function updateSeen(array $sections, ?string $seenAt = null): Response
    {
        return $this->call(
            api: Inbox::updateSeen,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }
}
