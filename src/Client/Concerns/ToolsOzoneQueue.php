<?php

/**
 * GENERATED CODE.
 */

declare(strict_types=1);

namespace Revolution\Bluesky\Client\Concerns;

use Illuminate\Http\Client\Response;
use Revolution\AtProto\Lexicon\Contracts\Tools\Ozone\Queue;

trait ToolsOzoneQueue
{
    public function assignModerator(int $queueId, string $did): Response
    {
        return $this->call(
            api: Queue::assignModerator,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function createQueue(string $name, ?array $subjectTypes = null, ?string $collection = null, ?array $reportTypes = null, ?string $description = null, ?array $recommendedPolicies = null): Response
    {
        return $this->call(
            api: Queue::createQueue,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function deleteQueue(int $queueId, ?int $migrateToQueueId = null): Response
    {
        return $this->call(
            api: Queue::deleteQueue,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getAssignments(?bool $onlyActive = null, ?array $queueIds = null, ?array $dids = null, ?int $limit = 50, ?string $cursor = null): Response
    {
        return $this->call(
            api: Queue::getAssignments,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function listQueues(?bool $enabled = null, ?string $subjectType = null, ?string $collection = null, ?array $reportTypes = null, ?int $limit = 50, ?string $cursor = null): Response
    {
        return $this->call(
            api: Queue::listQueues,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function routeReports(int $startReportId, int $endReportId): Response
    {
        return $this->call(
            api: Queue::routeReports,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function unassignModerator(int $queueId, string $did): Response
    {
        return $this->call(
            api: Queue::unassignModerator,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function updateQueue(int $queueId, ?string $name = null, ?bool $enabled = null, ?string $description = null, ?array $recommendedPolicies = null): Response
    {
        return $this->call(
            api: Queue::updateQueue,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }
}
