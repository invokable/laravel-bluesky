<?php

declare(strict_types=1);

namespace Revolution\Bluesky\Socialite;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;
use Revolution\Bluesky\Crypto\DPoP;
use Revolution\Bluesky\Socialite\Concerns\WithAuthServer;
use Revolution\Bluesky\Socialite\Concerns\WithClientAssertion;
use Revolution\Bluesky\Socialite\Concerns\WithOAuthSession;
use Revolution\Bluesky\Socialite\Concerns\WithPAR;
use Revolution\Bluesky\Socialite\Concerns\WithPDS;
use Revolution\Bluesky\Socialite\Concerns\WithTokenRequest;

class BlueskyProvider extends AbstractProvider implements ProviderInterface
{
    use WithAuthServer;
    use WithClientAssertion;
    use WithOAuthSession;
    use WithPAR;
    use WithPDS;
    use WithTokenRequest;

    protected ?string $service = null;

    protected ?string $login_hint = null;

    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = [
        'atproto',
        'transition:generic',
        'transition:email',
        'transition:chat.bsky',
    ];

    /**
     * The separating character for the requested scopes.
     *
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * Indicates if PKCE should be used.
     *
     * @var bool
     */
    protected $usesPKCE = true;

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state): string
    {
        if ($this->isStateless() || empty($state)) {
            throw new InvalidStateException('Bluesky does not support stateless.');
        }

        if (! $this->usesPKCE()) {
            throw new InvalidArgumentException('Bluesky requires PKCE.');
        }

        // Generate a new private key for DPoP when starting a new authentication.
        $this->request->session()->put(DPoP::SESSION_KEY, DPoP::generate());

        $this->updateServiceWithHint();

        $par_request_uri = $this->getParRequestUrl($state);

        $authorize_url = $this->authServerMeta('authorization_endpoint');

        return $authorize_url.'?'.
            http_build_query([
                'client_id' => $this->clientId,
                'request_uri' => $par_request_uri,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        if ($this->user) {
            return $this->user;
        }

        throw_if($this->hasInvalidState(), InvalidStateException::class);

        $this->updateServiceWithHint();

        if ($this->hasInvalidIssuer()) {
            throw new InvalidArgumentException('Invalid Issuer.');
        }

        $response = $this->getAccessTokenResponse($this->getCode());

        $user = $this->getUserWithSession($response);

        $this->clearSession();

        return $this->userInstance($response, $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user): User
    {
        return (new User)->setRaw($user)->map([
            'id' => data_get($user, 'did'),
            'nickname' => data_get($user, 'handle'),
            'name' => data_get($user, 'displayName'),
            'avatar' => data_get($user, 'avatar'),
            'session' => data_get($user, 'session'),
        ]);
    }

    protected function hasInvalidIssuer(): bool
    {
        return $this->authServerMeta('issuer') !== $this->request->input('iss');
    }

    /**
     * Set service/auth server/issuer.
     *
     * e.g. "https://bsky.social" "http://localhost:2583"
     */
    public function service(string $service): self
    {
        if (! Str::startsWith($service, 'http')) {
            $service = 'https://'.$service;
        }

        $this->service = Str::rtrim($service, '/');

        return $this;
    }

    /**
     * Set service/auth server/issuer.
     *
     * e.g. "https://bsky.social" "http://localhost:2583"
     */
    public function issuer(string $iss): self
    {
        return $this->service($iss);
    }

    public function hint(?string $login = null): self
    {
        $this->login_hint = $login;

        return $this;
    }

    protected function authUrl(): string
    {
        return $this->service ?? config('bluesky.service');
    }

    protected function clearSession(): void
    {
        $this->request->session()->forget([
            'code_verifier',
        ]);
    }
}
