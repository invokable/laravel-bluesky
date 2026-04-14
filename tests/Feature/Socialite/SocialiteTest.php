<?php

declare(strict_types=1);

namespace Tests\Feature\Socialite;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User;
use ReflectionClass;
use Revolution\Bluesky\Crypto\JsonWebKey;
use Revolution\Bluesky\Crypto\JsonWebToken;
use Revolution\Bluesky\Crypto\P256;
use Revolution\Bluesky\Events\DPoPNonceReceived;
use Revolution\Bluesky\Session\OAuthSession;
use Revolution\Bluesky\Socialite\BlueskyProvider;
use Revolution\Bluesky\Socialite\Key\JsonWebKeySet;
use Revolution\Bluesky\Socialite\Key\OAuthKey;
use Revolution\Bluesky\Socialite\OAuthConfig;
use Tests\TestCase;

class SocialiteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        OAuthConfig::clientMetadataUsing(null);
        OAuthConfig::jwksUsing(null);

        Http::preventStrayRequests();
    }

    public function test_instance(): void
    {
        $provider = Socialite::driver('bluesky');

        $this->assertInstanceOf(BlueskyProvider::class, $provider);
    }

    public function test_redirect(): void
    {
        Socialite::fake('bluesky');

        $response = Socialite::driver('bluesky')->redirect();

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    public function test_user(): void
    {
        $fakeUser = (new User)->map([
            'id' => 'did:plc:test',
            'nickname' => 'handle.test',
        ])->setToken('access_token')
            ->setRefreshToken('refresh_token');

        Socialite::fake('bluesky', $fakeUser);

        $user = Socialite::driver('bluesky')->user();

        $this->assertSame('did:plc:test', $user->getId());
        $this->assertSame('handle.test', $user->getNickname());
        $this->assertSame('access_token', $user->token);
        $this->assertSame('refresh_token', $user->refreshToken);
    }

    public function test_refresh(): void
    {
        $session = app('Illuminate\Contracts\Session\Session');

        $request = Request::create(uri: 'refresh');
        $request->setLaravelSession($session);

        Event::fake();

        Http::fake([
            'localhost/.well-known/oauth-authorization-server' => Http::response([
                'issuer' => 'https://iss',
                'authorization_endpoint' => 'https://authorize/oauth/authorize',
                'token_endpoint' => 'https://token/oauth/token',
            ]),
            'token/*' => Http::response([
                'did' => 'did:plc:test',
                'handle' => 'handle',
                'access_token' => 'access_token',
                'refresh_token' => 'refresh_token',
                'expires_in' => 3600,
            ]),
            'pds/*' => Http::response([
                'resource' => 'https://pds',
                'authorization_servers' => ['https://localhost'],
            ]),
        ]);

        $provider = new BlueskyProvider($request, 'client_id', 'client_secret', 'redirect');
        $provider->issuer(iss: 'localhost')
            ->setOAuthSession(OAuthSession::create());

        $token = $provider->refreshToken('refresh_token');

        $this->assertSame('access_token', $token->token);
        $this->assertSame('refresh_token', $token->refreshToken);
        $this->assertSame('refresh_token', $token->refreshToken);

        Event::assertDispatched(DPoPNonceReceived::class);
    }

    public function test_jwk_private(): void
    {
        $jwk = new JsonWebKey(OAuthKey::create()->privateKey());
        $jwk->withKid('kid');

        $this->assertArrayHasKey('d', $jwk->toArray());
        $this->assertSame('kid', $jwk->kid());
        $this->assertIsString((string) $jwk);
    }

    public function test_jwk_public(): void
    {
        $jwk = new JsonWebKey(OAuthKey::create()->publicKey());
        $jwk->withKid('kid')->asPublic();

        $this->assertArrayNotHasKey('d', $jwk->toArray());
        $this->assertSame('kid', $jwk->kid());
        $this->assertIsString((string) $jwk);
    }

    public function test_jwks(): void
    {
        $jwks = JsonWebKeySet::load();

        $this->assertArrayHasKey('keys', $jwks->toArray());
        $this->assertIsString((string) $jwks);
    }

    public function test_route_client_meta(): void
    {
        $response = $this->get(route('bluesky.oauth.client-metadata'));

        $response->assertOk();
    }

    public function test_route_client_meta_using(): void
    {
        OAuthConfig::clientMetadataUsing(function () {
            return ['client_id' => 'test'];
        });

        $response = $this->get(route('bluesky.oauth.client-metadata'));

        $response->assertOk()
            ->assertJson(['client_id' => 'test']);
    }

    public function test_route_jwks(): void
    {
        $response = $this->get(route('bluesky.oauth.jwks'));

        $response->assertOk();
    }

    public function test_route_jwks_using(): void
    {
        OAuthConfig::jwksUsing(function () {
            return ['keys' => 'test'];
        });

        $response = $this->get(route('bluesky.oauth.jwks'));

        $response->assertOk()
            ->assertJson(['keys' => 'test']);
    }

    public function test_jwt(): void
    {
        $jwtStr = JsonWebToken::encode(
            head: ['typ' => 'JWT', 'alg' => P256::ALG],
            payload: [
                'iss' => 'iss',
            ],
            key: OAuthKey::create()->privatePEM(),
        );

        [$header, $payload, $sig] = JsonWebToken::explode($jwtStr, decode: true);

        $this->assertArrayHasKey('typ', $header);
        $this->assertSame('iss', $payload['iss']);
    }

    public function test_scopes_match_config(): void
    {
        // Get the scopes from BlueskyProvider
        $provider = Socialite::driver('bluesky');
        $reflection = new ReflectionClass($provider);
        $scopesProperty = $reflection->getProperty('scopes');
        $scopesProperty->setAccessible(true);
        $providerScopes = $scopesProperty->getValue($provider);

        // Get the scope from configuration
        $configScope = config('bluesky.oauth.metadata.scope');

        // Convert config scope string to array for comparison
        $configScopeArray = explode(' ', $configScope);

        // Compare the two
        $this->assertEquals($configScopeArray, $providerScopes, 'BlueskyProvider::$scopes does not match config(bluesky.oauth.metadata.scope)');
    }
}
