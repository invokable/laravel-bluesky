<?php

/**
 * GENERATED CODE.
 */

declare(strict_types=1);

namespace Revolution\Bluesky\Client\Concerns;

use Illuminate\Http\Client\Response;
use Revolution\AtProto\Lexicon\Contracts\App\Bsky\Video;

trait AppBskyVideo
{
    public function abortUpload(string $jobId): Response
    {
        return $this->call(
            api: Video::abortUpload,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function finishUpload(string $jobId): Response
    {
        return $this->call(
            api: Video::finishUpload,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getJobStatus(string $jobId): Response
    {
        return $this->call(
            api: Video::getJobStatus,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getUploadLimits(): Response
    {
        return $this->call(
            api: Video::getUploadLimits,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getUploadStatus(string $jobId): Response
    {
        return $this->call(
            api: Video::getUploadStatus,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function startUpload(int $sizeBytes, string $mimeType, ?string $name = null, ?int $durationMs = null, ?int $width = null, ?int $height = null): Response
    {
        return $this->call(
            api: Video::startUpload,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function uploadPart(string $jobId, int $partNumber): Response
    {
        return $this->call(
            api: Video::uploadPart,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function uploadVideo(): Response
    {
        return $this->call(
            api: Video::uploadVideo,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }
}
