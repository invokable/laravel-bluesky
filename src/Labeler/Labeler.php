<?php

declare(strict_types=1);

namespace Revolution\Bluesky\Labeler;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Revolution\Bluesky\Core\CBOR;
use Revolution\Bluesky\Core\CBOR\AtBytes;
use Revolution\Bluesky\Core\CBOR\MapKeySort;
use Revolution\Bluesky\Crypto\K256;
use Revolution\Bluesky\Crypto\Signature;
use Revolution\Bluesky\FeedGenerator\ValidateAuth;
use Revolution\Bluesky\Labeler\Response\SubscribeLabelResponse;
use RuntimeException;

final class Labeler
{
    public const VERSION = 1;

    protected static AbstractLabeler $labeler;

    /**
     * Only one Labeler can be registered.
     *
     * @param  class-string|AbstractLabeler  $labeler
     */
    public static function register(string|AbstractLabeler $labeler): void
    {
        if (is_string($labeler) && class_exists($labeler)) {
            $labeler = app($labeler);
        }

        if (! $labeler instanceof AbstractLabeler) {
            throw new InvalidArgumentException('Labeler must be an instance of AbstractLabeler');
        }

        self::$labeler = $labeler;
    }

    public static function getLabelDefinitions(): array
    {
        return collect(self::$labeler->labels())->toArray();
    }

    /**
     * @return iterable<SubscribeLabelResponse>
     */
    public static function subscribeLabels(?int $cursor): iterable
    {
        yield from self::$labeler->subscribeLabels($cursor);
    }

    /**
     * @return iterable<UnsignedLabel>
     *
     * @throws LabelerException
     */
    public static function emitEvent(Request $request, ?string $token): iterable
    {
        $did = self::verifyJWT($request, $token);

        if (empty($did) || $did !== Config::string('bluesky.labeler.did')) {
            throw new LabelerException('Invalid JWT');
        }

        yield from self::$labeler->emitEvent($request, $did, $token);
    }

    private static function verifyJWT(Request $request, ?string $token): ?string
    {
        return app()->call(ValidateAuth::class, ['jwt' => $token, 'request' => $request]);
    }

    public static function saveLabel(SignedLabel $label, string $sign): ?SavedLabel
    {
        return self::$labeler->saveLabel($label, $sign);
    }

    public static function queryLabels(Request $request): array
    {
        return self::$labeler->queryLabels($request);
    }

    public static function createReport(Request $request): array
    {
        return self::$labeler->createReport($request);
    }

    /**
     * ```
     * [$signed, $sign] = Labeler::signLabel($unsigned);
     *
     * $signed
     * SignedLabel
     *
     * $sign
     * raw bytes
     * ```
     *
     * @return array{0: SignedLabel, 1: string}
     */
    public static function signLabel(UnsignedLabel $unsigned): array
    {
        if (isset(self::$labeler) && method_exists(self::$labeler, 'signLabel')) {
            return self::$labeler->signLabel($unsigned);
        }

        $label = $unsigned->toArray();
        $label = self::formatLabel($label);

        $bytes = CBOR::encode($label);

        $key = Config::string('bluesky.labeler.private_key');

        if (empty($key)) {
            throw new RuntimeException('Private key for Labeler is required.');
        }

        $sign = K256::load($key)->privateKey()->sign($bytes);
        $sign = Signature::toCompact($sign);

        $label = Arr::add($label, 'sig', new AtBytes($sign));
        $signed = SignedLabel::fromArray($label);

        return [$signed, $sign];
    }

    public static function formatLabel(array $label): array
    {
        return collect($label)
            ->put('ver', self::VERSION)
            ->except(['id', 'created_at', 'updated_at'])
            ->reject(fn ($value) => is_null($value))
            ->reject(fn ($value, $key) => $key === 'neg' && $value === false)
            ->sortKeysUsing(new MapKeySort)
            ->toArray();
    }

    public static function health(?array $header = null): array
    {
        if (isset(self::$labeler) && method_exists(self::$labeler, 'health')) {
            return self::$labeler->health($header);
        }

        return ['version' => app()->version()];
    }

    public static function log(string $message, null|array|string|int $context = []): void
    {
        Log::build(config('bluesky.labeler.logging'))->info($message, Arr::wrap($context));
    }
}
