<?php

/**
 * GENERATED CODE.
 */

declare(strict_types=1);

namespace Revolution\Bluesky\Client\Concerns;

use Illuminate\Http\Client\Response;
use Revolution\AtProto\Lexicon\Contracts\Chat\Bsky\Group;

trait ChatBskyGroup
{
    public function addMembers(string $convoId, array $members): Response
    {
        return $this->call(
            api: Group::addMembers,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function approveJoinRequest(string $convoId, string $member): Response
    {
        return $this->call(
            api: Group::approveJoinRequest,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function createGroup(array $members, string $name): Response
    {
        return $this->call(
            api: Group::createGroup,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function createJoinLink(string $convoId, string $joinRule, ?bool $requireApproval = null): Response
    {
        return $this->call(
            api: Group::createJoinLink,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function disableJoinLink(string $convoId): Response
    {
        return $this->call(
            api: Group::disableJoinLink,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function editGroup(string $convoId, string $name): Response
    {
        return $this->call(
            api: Group::editGroup,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function editJoinLink(string $convoId, ?bool $requireApproval = null, ?string $joinRule = null): Response
    {
        return $this->call(
            api: Group::editJoinLink,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function enableJoinLink(string $convoId): Response
    {
        return $this->call(
            api: Group::enableJoinLink,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getGroupPublicInfo(string $code): Response
    {
        return $this->call(
            api: Group::getGroupPublicInfo,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function listJoinRequests(string $convoId, ?int $limit = 50, ?string $cursor = null): Response
    {
        return $this->call(
            api: Group::listJoinRequests,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function rejectJoinRequest(string $convoId, string $member): Response
    {
        return $this->call(
            api: Group::rejectJoinRequest,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function removeMembers(string $convoId, array $members): Response
    {
        return $this->call(
            api: Group::removeMembers,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function requestJoin(string $code): Response
    {
        return $this->call(
            api: Group::requestJoin,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }
}
