# Laravel Bluesky Package Onboarding Guide

## Overview

**Laravel Bluesky** (`revolution/laravel-bluesky`) is a comprehensive Laravel package that enables PHP applications to integrate with the Bluesky social network and AT Protocol ecosystem. The package serves Laravel developers who want to build applications that can:

- **Authenticate users** via Bluesky OAuth or app passwords
- **Post content** and interact with Bluesky feeds (likes, reposts, follows)
- **Send notifications** through Bluesky as public posts or private messages
- **Generate custom feeds** that appear in the Bluesky app
- **Operate labeler services** for content moderation
- **Process AT Protocol data** including repositories, records, and identity resolution

The package abstracts the complexities of AT Protocol communication, providing a Laravel-native experience with facades, notification channels, console commands, and configuration-driven setup. It supports both modern OAuth 2.0 with DPoP (Demonstrating Proof-of-Possession) security and legacy app password authentication.

## Project Organization

### Core Architecture

The package is built around a **layered architecture** with clear separation of concerns:

```
src/
├── BlueskyManager.php           # Central orchestrator
├── HasShortHand.php            # High-level API trait  
├── Client/                     # HTTP client system
├── Agent/                      # Authentication management
├── Session/                    # Session data handling
├── Notifications/              # Laravel notification channels
├── FeedGenerator/              # Custom feed system
├── Labeler/                    # Content moderation system
├── Support/                    # AT Protocol utilities
├── Console/                    # Artisan commands
└── Providers/                  # Laravel service provider
```

### Main Systems

1. **Client System** (`src/Client/`)
   - `AtpClient.php` - Main AT Protocol client
   - `SubClient/` - Specialized API clients (BskyClient, VideoClient, etc.)
   - `HasHttp.php` - HTTP communication trait
   - `Concerns/` - Generated API method traits

2. **Authentication System** (`src/Agent/`, `src/Session/`)
   - `OAuthAgent.php` - OAuth 2.0 + DPoP authentication
   - `LegacyAgent.php` - App password authentication  
   - `OAuthSession.php` / `LegacySession.php` - Session management

3. **Feed Generator** (`src/FeedGenerator/`)
   - `FeedGenerator.php` - Core feed algorithm registry
   - `Http/FeedSkeletonController.php` - HTTP endpoint
   - `ValidateAuth.php` - JWT authentication

4. **Labeler Service** (`src/Labeler/`)
   - `Labeler.php` - Core labeling logic
   - `Server/` - WebSocket and HTTP server components

5. **Notification Channels** (`src/Notifications/`)
   - `BlueskyChannel.php` - Public posts
   - `BlueskyPrivateChannel.php` - Private messages

### Configuration

- `config/bluesky.php` - Central configuration with service URLs, OAuth settings, and feature toggles
- Environment variables for credentials and private keys
- Conditional route/feature registration based on config

### Console Commands

The package provides extensive CLI tools:
- `bluesky:download-*` - Repository and blob downloading
- `bluesky:labeler:*` - Labeler service management  
- `bluesky:*-private-key` - Cryptographic key generation
- WebSocket servers for real-time data processing

### Laravel Integration

- `BlueskyServiceProvider` registers all services, routes, and commands
- `Bluesky` facade provides static interface to `BlueskyManager`
- Notification channels integrate with Laravel's notification system
- `WithBluesky` trait for user models

## Glossary of Codebase-specific Terms

**AtpClient** - Main HTTP client for AT Protocol APIs. Routes requests to specialized sub-clients.
*Location: `src/Client/AtpClient.php`*

**BlueskyManager** - Central orchestrator implementing Factory pattern. Manages authentication and API access.  
*Location: `src/BlueskyManager.php`*

**BlueskyChannel** - Laravel notification channel for sending public Bluesky posts.
*Location: `src/Notifications/BlueskyChannel.php`*

**BlueskyRoute** - Authentication routing object specifying OAuth/legacy credentials for notifications.
*Location: `src/Notifications/BlueskyRoute.php`*

**CAR** - Content Addressable Archive decoder for AT Protocol repository data.
*Location: `src/Core/CAR.php`*

**CID** - Content Identifier utilities for data integrity verification in AT Protocol.
*Location: `src/Core/CID.php`*

**DetectFacets** - Automatic rich text annotation detector for mentions, links, hashtags.
*Location: `src/RichText/DetectFacets.php`*

**DidDocument** - Parser for DID documents containing identity metadata and service endpoints.
*Location: `src/Support/DidDocument.php`*

**DidKey** - Cryptographic key handling for `did:key:` format with Base58btc encoding.
*Location: `src/Crypto/DidKey.php`*

**DPoP** - Demonstrating Proof-of-Possession. OAuth security mechanism binding tokens to client keys.
*Location: `src/Crypto/DPoP.php`*

**FeedGenerator** - System for creating custom Bluesky feeds. Manages algorithm registration and execution.
*Location: `src/FeedGenerator/FeedGenerator.php`*

**FeedSkeleton** - Feed response format containing post URIs and pagination cursor.
*Related: `FeedSkeletonController`*

**HasShortHand** - High-level API trait providing convenient methods like `post()`, `like()`, `follow()`.
*Location: `src/HasShortHand.php`*

**Identity** - Handle and DID resolution utilities with DNS and well-known endpoint support.
*Location: `src/Support/Identity.php`*

**Labeler** - Content moderation service managing label definitions and signed label generation.
*Location: `src/Labeler/Labeler.php`*

**LegacyAgent** - Authentication agent for app password-based login (older method).
*Location: `src/Agent/LegacyAgent.php`*

**LegacySession** - Session management for app password authentication with JWT tokens.
*Location: `src/Session/LegacySession.php`*

**OAuthAgent** - Authentication agent for OAuth 2.0 + DPoP with automatic token refresh.
*Location: `src/Agent/OAuthAgent.php`*

**OAuthSession** - Session management for OAuth authentication with access/refresh tokens.
*Location: `src/Session/OAuthSession.php`*

**PDS** - Personal Data Server. User's AT Protocol data host extracted from DID documents.
*Usage: `DidDocument::pdsUrl()`*

**Post** - Record type for Bluesky posts supporting rich text, embeds, replies.
*Location: `src/Record/Post.php`*

**Recordable** - Interface for AT Protocol record types that can be converted to arrays.
*Usage: Post, Profile, UserList implement this*

**StrongRef** - Reference type containing URI and CID for uniquely identifying AT Protocol records.
*Location: `src/Types/StrongRef.php`*

**TextBuilder** - Fluent interface for constructing rich text with facets (mentions, links, tags).
*Location: `src/RichText/TextBuilder.php`*

**TID** - Timestamp Identifier. Time-ordered unique ID generation for AT Protocol.
*Location: `src/Core/TID.php`*

**ValidateAuth** - JWT token validation with DID-based public key verification for feed generators.
*Location: `src/FeedGenerator/ValidateAuth.php`*

**WithBluesky** - Laravel model trait providing `bluesky()` method for authenticated API access.
*Location: `src/Traits/WithBluesky.php`*

**XRPC** - AT Protocol's remote procedure call system. Base communication protocol.
*Usage: All API endpoints use `/xrpc/` prefix*
