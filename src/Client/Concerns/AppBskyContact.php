<?php

/**
 * GENERATED CODE.
 */

declare(strict_types=1);

namespace Revolution\Bluesky\Client\Concerns;

use Illuminate\Http\Client\Response;
use Revolution\AtProto\Lexicon\Contracts\App\Bsky\Contact;

trait AppBskyContact
{
    public function dismissMatch(string $subject): Response
    {
        return $this->call(
            api: Contact::dismissMatch,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getMatches(?int $limit = 50, ?string $cursor = null): Response
    {
        return $this->call(
            api: Contact::getMatches,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function getSyncStatus(): Response
    {
        return $this->call(
            api: Contact::getSyncStatus,
            method: self::GET,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function importContacts(string $token, array $contacts): Response
    {
        return $this->call(
            api: Contact::importContacts,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function removeData(): Response
    {
        return $this->call(
            api: Contact::removeData,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function sendNotification(string $from, string $to): Response
    {
        return $this->call(
            api: Contact::sendNotification,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function startPhoneVerification(string $phone): Response
    {
        return $this->call(
            api: Contact::startPhoneVerification,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }

    public function verifyPhone(string $phone, string $code): Response
    {
        return $this->call(
            api: Contact::verifyPhone,
            method: self::POST,
            params: compact($this->params(__METHOD__)),
        );
    }
}
