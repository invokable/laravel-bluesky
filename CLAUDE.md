# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a comprehensive Laravel package for Bluesky (AT Protocol) integration that enables Laravel applications to interact with the decentralized Bluesky social network. The package provides:

- **Dual Authentication**: OAuth 2.0+DPoP and legacy app password support
- **Complete API Coverage**: Auto-generated clients from AT Protocol lexicon
- **Rich Content Creation**: Posts with mentions, links, hashtags, and media
- **Real-time Services**: Feed generators and labeler services
- **Laravel Integration**: Notifications, Socialite, Facades, and Artisan commands

## Common Development Commands

### Testing and Quality
```bash
# Run tests
composer test

# Run linting (Laravel Pint)
composer lint

# Run single test file
./vendor/bin/phpunit tests/Feature/Client/ClientTest.php

# Generate coverage report
./vendor/bin/phpunit --coverage-html build/coverage
```

### Package Development
```bash
# Install dependencies
composer install

# Purge and rediscover package
composer clear && composer prepare

# Build workbench application
composer build

# Serve workbench for testing
composer serve

# Update lexicon client (after protocol changes)
composer run post-update-cmd
```

### Console Commands
```bash
# Generate OAuth private key
php artisan bluesky:new-private-key

# Generate labeler private key  
php artisan bluesky:labeler:new-private-key

# Download AT Protocol repositories
php artisan bluesky:download-repo did:plc:example

# Download all blob files for an actor
php artisan bluesky:download-blobs alice.bsky.social

# Download specific record collections
php artisan bluesky:download-record alice.bsky.social -C app.bsky.feed.post

# Unpack CAR files into individual records
php artisan bluesky:unpack-repo alice.bsky.social

# Start firehose WebSocket server
php artisan bluesky:firehose-serve

# Start Jetstream WebSocket server
php artisan bluesky:jetstream-serve

# Setup labeler service
php artisan bluesky:labeler:setup

# Start labeler WebSocket server
php artisan bluesky:labeler-serve

# Declare labeler service labels
php artisan bluesky:labeler-declare-labels

# Start labeler polling service
php artisan bluesky:labeler-polling
```

## Code Architecture

### Core Systems

**Authentication Layer**: Dual authentication system supporting both OAuth 2.0+DPoP (`OAuthAgent`/`OAuthSession`) and legacy app passwords (`LegacyAgent`/`LegacySession`). The `BlueskyManager` factory orchestrates authentication and provides unified API access.

**Client Architecture**: Layered HTTP client system with `AtpClient` as the main orchestrator that routes requests to specialized sub-clients (`BskyClient`, `VideoClient`, `ChatClient`, etc.). Each client handles specific AT Protocol namespaces and includes auto-generated API methods from lexicon contracts.

**Record System**: AT Protocol record types (`Post`, `Profile`, `Like`, etc.) implement the `Recordable` contract and provide fluent builders. The `TextBuilder` class handles rich text with automatic facet detection for mentions, links, and hashtags.

**Feed Generator Framework**: Complete system for creating custom Bluesky feeds with `FeedGenerator` algorithm registry, HTTP controllers, and JWT authentication via `ValidateAuth`.

**Labeler Service**: Content moderation system with `Labeler` core logic, WebSocket/HTTP servers, and cryptographic label signing capabilities.

### Key Design Patterns

- **Factory Pattern**: `BlueskyManager` implements `Factory` contract for authentication state management
- **Agent Pattern**: Separate agent classes handle different authentication methods and session management
- **Sub-client Pattern**: Domain-specific HTTP clients for different AT Protocol namespaces
- **Trait Composition**: `HasShortHand` provides high-level API methods, `HasHttp` manages HTTP client functionality

### Configuration Structure

Configuration in `config/bluesky.php` supports:
- Service endpoints (PDS, public API, PLC directory)
- OAuth 2.0 client metadata with DPoP support
- Notification channel routing
- Feature toggles for conditional functionality

### AT Protocol Integration

**Data Formats**: Handles CAR (Content Addressable Archive) files, CBOR encoding, CID (Content Identifier) verification, and TID (Timestamp Identifier) generation.

**Cryptography**: Supports ECDSA P-256/K-256 key pairs, JWT signing, DPoP proof generation, and `did:key` format handling.

**Identity Resolution**: Automatic DID document parsing, PDS discovery, and handle-to-DID resolution via DNS and well-known endpoints.

### Laravel Integration Points

- Service provider registers routes, commands, and services based on configuration
- Notification channels (`BlueskyChannel`, `BlueskyPrivateChannel`) integrate with Laravel's notification system
- `WithBluesky` trait for user models provides authenticated API access
- Facade provides static interface to `BlueskyManager`
- Conditional route registration based on feature flags

### Important Implementation Notes

- All classes use strict typing (`declare(strict_types=1)`)
- API client methods return `Response` objects for caller error handling
- Auto-generated client methods in `Concerns/` directory from AT Protocol lexicon
- WebSocket servers require `workerman/workerman` and `revolt/event-loop` extensions
- OAuth implementation includes PKCE, DPoP, and private key JWT client authentication
- Testbench workbench application provides development environment with sample implementations

## API Usage Patterns

### Basic Authentication and Posting
```php
// Legacy app password authentication
Bluesky::login('identifier', 'password')->post('Hello Bluesky!');

// OAuth authentication
Bluesky::withToken($oauthSession)->getTimeline();

// Public API access (no auth required)
Bluesky::public()->getProfile('did:plc:example');
```

### Rich Text Content Creation
```php
// Using TextBuilder for rich content
$post = Post::build(function (TextBuilder $builder) {
    $builder->text('Hello ')
            ->mention('@alice.bsky.social')
            ->text(' check out ')
            ->link('https://example.com')
            ->newLine()
            ->tag('#Laravel');
});

// Auto-detect facets from plain text
$builder = TextBuilder::make('@alice.bsky.social https://example.com #Laravel')
    ->detectFacets();
```

### Feed Generator Development
```php
// Register custom feed algorithm
FeedGenerator::register('laravel-posts', function($limit, $cursor, $user, $request) {
    return Bluesky::searchPosts(q: '#Laravel', limit: $limit);
});

// Publishing feed generator to Bluesky
$generator = Generator::create(did: 'did:web:example.com', displayName: 'Laravel Feed')
                     ->description('Posts about Laravel development');
Bluesky::publishFeedGenerator($generator);
```

### Labeler Service Implementation
```php
// Custom labeler implementation
class CustomLabeler extends AbstractLabeler
{
    public function labels(): array
    {
        return [
            ['id' => 'spam', 'title' => 'Spam', 'description' => 'Content is spam'],
            ['id' => 'misleading', 'title' => 'Misleading', 'description' => 'Misleading content'],
        ];
    }
    
    public function emitEvent(Request $request, string $did, ?string $token): iterable
    {
        if ($this->isSpam($request->get('subject'))) {
            yield UnsignedLabel::fromArray([
                'src' => $did,
                'uri' => $request->get('subject')['uri'],
                'val' => 'spam',
                'neg' => false,
            ]);
        }
    }
    
    public function saveLabel(SignedLabel $label, string $sign): ?SavedLabel
    {
        // Store signed label in database
        return new SavedLabel();
    }
}

// Register labeler
Labeler::register(CustomLabeler::class);
```

### Laravel Notifications Integration
```php
// Public post notification
class BlueskyPostNotification extends Notification
{
    public function toBluesky($notifiable): Post
    {
        return Post::create('Hello from Laravel notifications!');
    }
}

// Private message notification
class BlueskyPrivateNotification extends Notification
{
    public function toBlueskyPrivate($notifiable): BlueskyPrivateMessage
    {
        return BlueskyPrivateMessage::build(function (TextBuilder $builder) {
            $builder->text('Private message with ')
                    ->mention('@alice.bsky.social');
        });
    }
}

// User model integration
class User extends Authenticatable
{
    use WithBluesky;
    
    protected function tokenForBluesky(): OAuthSession
    {
        return OAuthSession::create([
            'did' => $this->bluesky_did,
            'refresh_token' => $this->bluesky_refresh_token,
        ]);
    }
}
```

## Data Processing and Verification

### CAR File Processing
```php
// Decode CAR file
[$roots, $blocks] = CAR::decode($carData);

// Process AT Protocol repository
foreach (CAR::blockMap($carData) as $key => $record) {
    [$collection, $rkey] = explode('/', $key);
    $value = data_get($record, 'value');
    $uri = data_get($record, 'uri');
}

// Verify signed commit
$signed = CAR::signedCommit($carData);
$publicKey = DidKey::parse($didKeyFromDocument);
if (CAR::verifySignedCommit($signed, $publicKey)) {
    // Commit signature is valid
}
```

### Identity Resolution
```php
// Handle to DID resolution
$did = Bluesky::resolveHandle('alice.bsky.social');

// DID document retrieval
$didDoc = Bluesky::identity()->resolveDID('did:plc:example');
$pdsUrl = $didDoc->pdsUrl();
$publicKey = $didDoc->publicKey('#atproto');
```

### Cryptographic Operations
```php
// Parse DID key
$didKey = DidKey::parse('did:key:z...');
$publicKeyPEM = $didKey['publicKeyPEM'];
$algorithm = $didKey['algorithm'];

// JWT validation for feed generators
$validator = new ValidateAuth();
$userDid = $validator($jwtToken, $request);
```

## Configuration Examples

### Basic Bluesky Configuration
```php
// config/bluesky.php
return [
    'identifier' => env('BLUESKY_IDENTIFIER'),
    'password' => env('BLUESKY_APP_PASSWORD'),
    
    'oauth' => [
        'private_key' => env('BLUESKY_OAUTH_PRIVATE_KEY'),
        'client_id' => env('BLUESKY_CLIENT_ID'),
        'redirect' => env('BLUESKY_REDIRECT'),
    ],
    
    'generator' => [
        'service' => env('BLUESKY_GENERATOR_SERVICE'),
        'publisher' => env('BLUESKY_GENERATOR_PUBLISHER'),
    ],
    
    'labeler' => [
        'did' => env('BLUESKY_LABELER_DID'),
        'private_key' => env('BLUESKY_LABELER_PRIVATE_KEY'),
        'host' => env('BLUESKY_LABELER_HOST', '127.0.0.1'),
        'port' => env('BLUESKY_LABELER_PORT', 7000),
    ],
];
```

### Environment Variables
```env
# Basic Authentication
BLUESKY_IDENTIFIER=your-handle.bsky.social
BLUESKY_APP_PASSWORD=your-app-password

# OAuth Configuration
BLUESKY_OAUTH_PRIVATE_KEY=base64url-encoded-private-key
BLUESKY_CLIENT_ID=http://localhost
BLUESKY_REDIRECT=http://127.0.0.1:8000/bluesky/callback

# Feed Generator
BLUESKY_GENERATOR_SERVICE=did:web:your-domain.com
BLUESKY_GENERATOR_PUBLISHER=did:plc:your-publisher-did

# Labeler Service
BLUESKY_LABELER_DID=did:plc:your-labeler-did
BLUESKY_LABELER_PRIVATE_KEY=base64url-encoded-k256-key
BLUESKY_LABELER_HOST=127.0.0.1
BLUESKY_LABELER_PORT=7000
```

## Testing Guidelines

### Package Testing Commands
```bash
# Run all tests
composer test

# Run specific test class
./vendor/bin/phpunit tests/Feature/Client/ClientTest.php

# Run tests with coverage
./vendor/bin/phpunit --coverage-html build/coverage

# Clear test cache and prepare
composer clear && composer prepare
```

### Mocking External Services
```php
// Mock Bluesky API responses
Bluesky::shouldReceive('login->post')
    ->once()
    ->andReturn(new Response(200, [], '{"success": true}'));

// Test feed algorithms in isolation
FeedGenerator::register('test', function($limit, $cursor) {
    return ['cursor' => null, 'feed' => [['post' => 'at://test']]];
});
```

## Troubleshooting

### Common Issues
- **OAuth Authentication**: Ensure private key is base64url encoded and properly configured
- **WebSocket Servers**: Verify `workerman/workerman` and `revolt/event-loop` are installed
- **CAR File Processing**: Check file permissions and storage configuration
- **DID Resolution**: Verify network connectivity for DNS/HTTPS lookups
- **Feed Generator**: Ensure proper JWT validation and route configuration
