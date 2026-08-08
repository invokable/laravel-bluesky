<?php

/**
 * GENERATED CODE.
 */

declare(strict_types=1);

namespace Revolution\Bluesky\Client\Concerns;

use Illuminate\Http\Client\Response;
use Revolution\AtProto\Lexicon\Contracts\Tools\Ozone\Report;

trait ToolsOzoneReport
{
    public function assignModerator(int $reportId, ?int $queueId = null, ?string $did = null, ?bool $isPermanent = null): Response
    {
        return $this->call(
            api: Report::assignModerator,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function closeReports(string $subject, ?array $reportTypes = null, ?string $internalNote = null, ?bool $isAutomated = null): Response
    {
        return $this->call(
            api: Report::closeReports,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function createActivity(array $activity, ?int $reportId = null, ?int $eventId = null, ?string $internalNote = null, ?string $publicNote = null, ?bool $isAutomated = null): Response
    {
        return $this->call(
            api: Report::createActivity,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getAssignments(?bool $onlyActive = null, ?array $reportIds = null, ?array $dids = null, ?int $limit = 50, ?string $cursor = null): Response
    {
        return $this->call(
            api: Report::getAssignments,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getHistoricalStats(?int $queueId = null, ?string $moderatorDid = null, ?array $reportTypes = null, ?string $startDate = null, ?string $endDate = null, ?int $limit = 30, ?string $cursor = null): Response
    {
        return $this->call(
            api: Report::getHistoricalStats,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getLatestReport(): Response
    {
        return $this->call(
            api: Report::getLatestReport,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getLiveStats(?int $queueId = null, ?string $moderatorDid = null, ?array $reportTypes = null): Response
    {
        return $this->call(
            api: Report::getLiveStats,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getReport(int $id): Response
    {
        return $this->call(
            api: Report::getReport,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function listActivities(int $reportId, ?int $limit = 50, ?string $cursor = null): Response
    {
        return $this->call(
            api: Report::listActivities,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function queryActivities(?array $activityTypes = null, ?string $createdAfter = null, ?string $createdBefore = null, ?string $sortDirection = 'desc', ?int $limit = 50, ?string $cursor = null): Response
    {
        return $this->call(
            api: Report::queryActivities,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function queryReports(string $status, ?int $queueId = null, ?array $reportTypes = null, ?string $subject = null, ?string $did = null, ?string $subjectType = null, ?array $collections = null, ?string $reportedAfter = null, ?string $reportedBefore = null, ?bool $isMuted = null, ?string $assignedTo = null, ?string $sortField = 'createdAt', ?string $sortDirection = 'desc', ?int $limit = 50, ?string $cursor = null): Response
    {
        return $this->call(
            api: Report::queryReports,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function reassignQueue(int $reportId, int $queueId, ?string $comment = null): Response
    {
        return $this->call(
            api: Report::reassignQueue,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function refreshStats(string $startDate, string $endDate, ?array $queueIds = null): Response
    {
        return $this->call(
            api: Report::refreshStats,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function unassignModerator(int $reportId): Response
    {
        return $this->call(
            api: Report::unassignModerator,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }
}
