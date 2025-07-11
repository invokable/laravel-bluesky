<?php

declare(strict_types=1);

namespace Revolution\Bluesky\Socialite\Concerns;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Revolution\Bluesky\Crypto\DPoP;
use Revolution\Bluesky\Events\DPoPNonceReceived;
use Revolution\Bluesky\Events\RefreshTokenReplayed;

trait WithTokenRequest
{
    /**
     * @throws RequestException
     * @throws ConnectionException
     * @throws AuthenticationException
     */
    protected function sendTokenRequest(string $token_url, array $payload): array
    {
        return Http::retry(times: 2, throw: false)
            ->withRequestMiddleware($this->tokenRequestMiddleware(...))
            ->withResponseMiddleware($this->tokenResponseMiddleware(...))
            ->post($token_url, $payload)
            ->throwIf(fn (Response $response) => $response->serverError())
            ->json();
    }

    protected function tokenRequestMiddleware(RequestInterface $request): RequestInterface
    {
        $dpop_nonce = $this->getOAuthSession()->get(DPoP::AUTH_NONCE, '');

        $uri = $request->getUri();

        $dpop_proof = DPoP::authProof(
            jwk: DPoP::load(),
            url: $uri->getScheme().'://'.$uri->getHost().$uri->getPath(),
            nonce: $dpop_nonce,
        );

        return $request->withHeader('DPoP', $dpop_proof);
    }

    /**
     * @throws AuthenticationException
     */
    protected function tokenResponseMiddleware(ResponseInterface $response): ResponseInterface
    {
        $res = new Response($response);

        // "refresh token replayed" error
        if ($res->status() === 400 && $res->json('error') === 'invalid_grant') {
            RefreshTokenReplayed::dispatch(
                $this->getOAuthSession(),
                $res,
            );

            throw new AuthenticationException;
        }

        $dpop_nonce = $res->header('DPoP-Nonce');

        $this->getOAuthSession()->put(DPoP::AUTH_NONCE, $dpop_nonce);

        $sub = $res->json('sub');
        if (filled($sub)) {
            $this->getOAuthSession()->put('sub', $sub);
        }

        DPoPNonceReceived::dispatch($dpop_nonce, $this->getOAuthSession());

        return $response;
    }

    /**
     * Get the access token response for the given code.
     *
     * @param  string  $code
     *
     * @throws RequestException
     * @throws ConnectionException
     * @throws AuthenticationException
     */
    public function getAccessTokenResponse($code): array
    {
        return $this->sendTokenRequest($this->getTokenUrl(), $this->getTokenFields($code));
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl(): string
    {
        return (string) $this->authServerMeta('token_endpoint');
    }

    /**
     * Get the POST fields for the token request.
     *
     * @param  string  $code
     */
    protected function getTokenFields($code): array
    {
        $fields = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_assertion_type' => self::CLIENT_ASSERTION_TYPE,
            'client_assertion' => $this->getClientAssertion(),
            'code_verifier' => $this->request->session()->get('code_verifier'),
        ];

        return array_merge($fields, $this->parameters);
    }

    /**
     * Get the refresh token response for the given refresh token.
     *
     * @param  string  $refreshToken
     *
     * @throws RequestException
     * @throws ConnectionException
     * @throws AuthenticationException
     */
    protected function getRefreshTokenResponse($refreshToken): array
    {
        $this->getOAuthSession()->put('old_refresh_token', $refreshToken);

        $payload = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $this->clientId,
            'client_assertion_type' => self::CLIENT_ASSERTION_TYPE,
            'client_assertion' => $this->getClientAssertion(),
        ];

        return $this->sendTokenRequest($this->getTokenUrl(), $payload);
    }
}
