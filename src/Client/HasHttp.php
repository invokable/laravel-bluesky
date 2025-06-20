<?php

declare(strict_types=1);

namespace Revolution\Bluesky\Client;

use BackedEnum;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Psr\Http\Message\StreamInterface;
use ReflectionMethod;
use Revolution\AtProto\Lexicon\Attributes\Format;
use Revolution\AtProto\Lexicon\Attributes\KnownValues;

use function Illuminate\Support\enum_value;

trait HasHttp
{
    protected const POST = 'post';

    protected const GET = 'get';

    protected PendingRequest $http;

    protected function call(#[Format('nsid')] BackedEnum|string $api, #[KnownValues([self::GET, self::POST])] string $method = self::GET, ?array $params = null): Response
    {
        $params = collect($params)
            ->reject(fn ($param) => is_null($param))
            ->toArray();

        if (blank($params)) {
            $params = null;
        }

        return $this->http()->$method(enum_value($api), $params);
    }

    /**
     * Get the method parameter names. Used in conjunction with compact() on the original method.
     *
     * example: {@link AppBskyActor::getProfile}
     *
     * ```
     * params('...AppBskyActor::getProfile')
     *
     * ['actor']
     *
     * compact(['actor'])
     *
     * // An array of named arguments and values
     * ['actor' => 'did:plc:***']
     * ```
     */
    protected function params(string $method): array
    {
        if (! str_contains($method, '::')) {
            return [];
        }

        [$class, $method] = explode('::', $method, 2);

        if (! method_exists($class, $method)) {
            return [];
        }

        $ref = new ReflectionMethod($class, $method);

        return collect($ref->getParameters())
            ->map
            ->getName()
            ->toArray();
    }

    protected function http(): PendingRequest
    {
        return $this->http;
    }

    public function withHttp(PendingRequest $http): static
    {
        $this->http = $http;

        return $this;
    }

    /**
     * Normally you don't need to use this method. You only need to use it if you want to specify a special BaseUrl.
     *
     * ```
     * Bluesky::client(auth: false)
     *        ->baseUrl(Bluesky::entryway().'/xrpc');
     * ```
     */
    public function baseUrl(string $baseUrl): static
    {
        $this->http->baseUrl($baseUrl);

        return $this;
    }

    /**
     * Used when uploadBlob().
     */
    public function withBody(StreamInterface|string $content, string $contentType = 'image/png'): static
    {
        $this->http->withBody($content, $contentType);

        return $this;
    }

    /**
     * Add any header.
     *
     * ```
     * ->withHeader(name: 'atproto-accept-labelers', value: '')
     * ```
     */
    public function withHeader(string $name, mixed $value): static
    {
        $this->http->withHeader($name, $value);

        return $this;
    }

    /**
     * Add "atproto-proxy" header.
     *
     * ```
     * ->withServiceProxy('did:web:api.bsky.chat#bsky_chat')
     * ```
     */
    public function withServiceProxy(#[Format('did')] string $did): static
    {
        return $this->withHeader('atproto-proxy', $did);
    }
}
