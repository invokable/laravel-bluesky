# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

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

# Start firehose WebSocket server
php artisan bluesky:firehose

# Setup labeler service
php artisan bluesky:labeler:setup
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
