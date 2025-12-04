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

# Start firehose WebSocket server (requires cmd parameter)
php artisan bluesky:firehose start

# Start Jetstream WebSocket server (requires cmd parameter)
php artisan bluesky:ws start

# Start Jetstream with collection filters
php artisan bluesky:ws start -C app.bsky.feed.post -C app.bsky.feed.like

# Start Jetstream with DID filters
php artisan bluesky:ws start -D did:plc:example -D did:plc:another

# Setup labeler service
php artisan bluesky:labeler:setup

# Start labeler WebSocket server (requires cmd parameter)
php artisan bluesky:labeler:server start

# Start labeler with Jetstream integration
php artisan bluesky:labeler:server start --jetstream

# Start labeler with Firehose integration
php artisan bluesky:labeler:server start --firehose

# Declare labeler service labels
php artisan bluesky:labeler:declare-labels

# Start labeler polling service
php artisan bluesky:labeler:polling --limit=50
```

## Copilot Coding Guidelines

This section provides explicit coding conventions and quality standards for contributing to the Laravel Bluesky package. Follow these guidelines to ensure code consistency and maintainability.

### Design Patterns

The package uses these key design patterns:
- **Factory Pattern**: `BlueskyManager` implements `Factory` contract for authentication state management
- **Agent Pattern**: Separate agent classes handle different authentication methods and session management
- **Sub-client Pattern**: Domain-specific HTTP clients for different AT Protocol namespaces
- **Trait Composition**: `HasShortHand` provides high-level API methods, `HasHttp` manages HTTP client functionality

### Architecture Layers

**Authentication Layer**: Dual authentication system supporting both OAuth 2.0+DPoP (`OAuthAgent`/`OAuthSession`) and legacy app passwords (`LegacyAgent`/`LegacySession`). The `BlueskyManager` factory orchestrates authentication and provides unified API access.

**Client Architecture**: Layered HTTP client system with `AtpClient` as the main orchestrator that routes requests to specialized sub-clients (`BskyClient`, `VideoClient`, `ChatClient`, etc.). Each client handles specific AT Protocol namespaces and includes auto-generated API methods from lexicon contracts.

**Record System**: AT Protocol record types (`Post`, `Profile`, `Like`, etc.) implement the `Recordable` contract and provide fluent builders. The `TextBuilder` class handles rich text with automatic facet detection for mentions, links, and hashtags.

**Feed Generator Framework**: Complete system for creating custom Bluesky feeds with `FeedGenerator` algorithm registry, HTTP controllers, and JWT authentication via `ValidateAuth`.

**Labeler Service**: Content moderation system with `Labeler` core logic, WebSocket/HTTP servers, and cryptographic label signing capabilities.

### Code Structure Conventions

#### File Organization
- Use strict typing: `declare(strict_types=1);` at the top of all PHP files
- Follow PSR-4 autoloading with proper namespace declarations
- Organize classes by functionality in logical directory structures:
  - `Client/` - HTTP client system and API communication
  - `Agent/` - Authentication management
  - `Session/` - Session data handling  
  - `Notifications/` - Laravel notification channels
  - `Record/` - AT Protocol record types
  - `Support/` - Utility classes and helpers
  - `Contracts/` - Interfaces and contracts

#### Class Design
- Implement single responsibility principle - one class, one purpose
- Use interfaces (`Contracts/`) for dependency injection and testing
- Leverage traits for shared functionality (e.g., `HasHttp`, `HasShortHand`)
- Prefer composition over inheritance
- Use final classes where inheritance is not intended

#### Method Design
- Use named parameters for methods with multiple optional parameters
- Return Response objects from HTTP client methods
- Provide fluent interfaces where appropriate (e.g., `TextBuilder`)
- Include comprehensive PHPDoc with usage examples

```php
/**
 * Create a new post with rich text.
 *
 * ```
 * use Revolution\Bluesky\Record\Post;
 *
 * $post = Post::create(text: 'Hello world!')
 *             ->addImage(blob: $blob, alt: 'Description');
 * ```
 */
public function create(string $text, ?array $facets = null): self
```

### Laravel Integration Standards

#### Service Provider Registration
- Register all services, routes, and commands in `BlueskyServiceProvider`
- Use conditional registration based on configuration flags
- Register facades through the `extra.laravel.providers` composer configuration

#### Facade Implementation
- Provide static interface through `Bluesky` facade to `BlueskyManager`
- Implement factory pattern for multiple authentication contexts
- Support method chaining for fluent API usage

#### Notification Channels
- Extend Laravel's notification system with dedicated channels
- Support both OAuth and app password authentication via `BlueskyRoute`
- Provide clear routing methods for different authentication types

```php
// App password
BlueskyRoute::to(identifier: config('bluesky.identifier'), password: config('bluesky.password'))

// OAuth
BlueskyRoute::to(oauth: $session)
```

#### Console Commands
- Prefix all commands with `bluesky:`
- Provide detailed descriptions and usage examples
- Support common Laravel command patterns (signatures, validation)
- Use appropriate exit codes and output formatting

#### Testing Integration
- Support Laravel's HTTP client mocking via `Http::fake()`
- Provide facade mocking capabilities for unit tests
- Use `Http::preventStrayRequests()` to catch unintended external calls

### Configuration Management

#### Environment Variables
- Use descriptive `BLUESKY_` prefixed environment variable names
- Provide sensible defaults in config files
- Document all configuration options with inline comments

#### Config Structure
- Organize related settings in nested arrays
- Use boolean flags for feature toggles (e.g., `disabled` options)
- Support both development and production configurations

```php
'oauth' => [
    'disabled' => env('BLUESKY_OAUTH_DISABLED', false),
    'metadata' => [
        'scope' => env('BLUESKY_OAUTH_SCOPE', 'atproto transition:generic'),
        // Additional OAuth settings...
    ],
],
```

#### Route Registration
- Use conditional route registration based on config flags
- Group related routes with appropriate prefixes
- Follow RESTful conventions where applicable

### Terminology and Naming Standards

#### AT Protocol Conventions
- Use proper AT Protocol terminology (DID, PDS, XRPC, CAR, CID)
- Follow camelCase for method names, PascalCase for class names
- Use descriptive names that reflect AT Protocol concepts

#### Authentication Naming
- Use "OAuth" for OAuth 2.0 + DPoP authentication
- Use "Legacy" for app password authentication
- Use "Agent" for authentication managers, "Session" for session data

#### HTTP Client Naming
- Main client: `AtpClient` 
- Specialized clients: `BskyClient`, `VideoClient`, etc.
- Use "Client" suffix for HTTP client classes
- Use descriptive method names matching AT Protocol endpoints

#### Record and Data Types
- Use singular nouns for record types: `Post`, `Profile`, `Like`
- Use "Ref" suffix for reference types: `StrongRef`, `RepoRef`
- Use "Builder" suffix for fluent construction classes

### Error Handling Standards

#### Exception Types
- Use Laravel's built-in exceptions where appropriate
- Create custom exceptions for AT Protocol specific errors
- Provide meaningful error messages with context

#### HTTP Error Handling
- Return Response objects to allow caller to handle errors
- Use appropriate HTTP status codes
- Log errors appropriately without exposing sensitive data

### Documentation Standards

#### PHPDoc Requirements
- Include comprehensive method documentation
- Provide usage examples in code blocks
- Document all parameters with types and descriptions
- Include `@return` and `@throws` annotations

#### Code Examples
- Use realistic, working examples in documentation
- Show both basic and advanced usage patterns
- Include error handling examples where relevant
- Use consistent variable naming in examples

### PR Checklist and Quality Standards

#### Before Submitting
- [ ] All tests pass (`composer test`)
- [ ] Code passes linting (`composer lint`)
- [ ] New functionality includes appropriate tests
- [ ] Documentation includes usage examples
- [ ] Breaking changes are clearly marked and documented
- [ ] Configuration changes include environment variable documentation

#### Code Quality
- [ ] Follows PSR-12 coding standards (enforced by Laravel Pint)
- [ ] Uses strict typing throughout
- [ ] Includes comprehensive error handling
- [ ] Avoids code duplication through proper abstraction
- [ ] Uses dependency injection where appropriate

#### Testing Requirements
- [ ] Unit tests for business logic
- [ ] Integration tests for Laravel features
- [ ] Mock external API calls appropriately
- [ ] Test both success and failure scenarios
- [ ] Maintain or improve code coverage

### Common Testing Patterns

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

#### Documentation Requirements
- [ ] Update relevant documentation files
- [ ] Include inline code examples
- [ ] Update glossary for new terminology
- [ ] Verify all links and references are working

#### AT Protocol Compliance
- [ ] Follows AT Protocol specifications correctly
- [ ] Handles DID resolution properly
- [ ] Implements proper XRPC request/response patterns
- [ ] Uses correct content types and encoding
- [ ] Validates AT Protocol data structures

### AT Protocol Integration Details

**Data Formats**: Handles CAR (Content Addressable Archive) files, CBOR encoding, CID (Content Identifier) verification, and TID (Timestamp Identifier) generation.

**Cryptography**: Supports ECDSA P-256/K-256 key pairs, JWT signing, DPoP proof generation, and `did:key` format handling.

**Identity Resolution**: Automatic DID document parsing, PDS discovery, and handle-to-DID resolution via DNS and well-known endpoints.

### Troubleshooting Common Issues

- **OAuth Authentication**: Ensure private key is base64url encoded and properly configured
- **WebSocket Servers**: Verify `workerman/workerman` and `revolt/event-loop` are installed
- **CAR File Processing**: Check file permissions and storage configuration
- **DID Resolution**: Verify network connectivity for DNS/HTTPS lookups
- **Feed Generator**: Ensure proper JWT validation and route configuration

This ensures all contributions maintain the high quality standards and architectural consistency that make this package reliable and easy to use for Laravel developers.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4.15
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- laravel/socialite (SOCIALITE) - v5
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- phpunit/phpunit (PHPUNIT) - v12

## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure - don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.


=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double check the available parameters.

## URLs
- Whenever you share a project URL with the user you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain / IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation specific for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The 'search-docs' tool is perfect for all Laravel related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel-ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries - package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit"
3. Quoted Phrases (Exact Position) - query="infinite scroll" - Words must be adjacent and in that order
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit"
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms


=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over comments. Never use comments within the code itself unless there is something _very_ complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.


=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.


=== laravel/v12 rules ===

## Laravel 12

- Use the `search-docs` tool to get version specific documentation.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

### Laravel 12 Structure
- No middleware files in `app/Http/Middleware/`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- **No app\Console\Kernel.php** - use `bootstrap/app.php` or `routes/console.php` for console configuration.
- **Commands auto-register** - files in `app/Console/Commands/` are automatically available and do not require manual registration.

### Database
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 11 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models
- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.


=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.


=== phpunit/core rules ===

## PHPUnit Core

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should test all of the happy paths, failure paths, and weird paths.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files, these are core to the application.

### Running Tests
- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test`.
- To run all tests in a file: `php artisan test tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --filter=testName` (recommended after making a change to a related file).


=== revolution/laravel-boost-copilot-cli rules ===

## Laravel Boost for GitHub Copilot CLI

### MCP Configuration File Required
- If you cannot see the `laravel-boost` MCP server or tools, the user has likely forgotten to specify the MCP configuration file when starting Copilot CLI.
- Instruct the user to restart Copilot CLI with the correct command:
  ```
  copilot --additional-mcp-config @.github/mcp-config.json --continue
  ```
- The `--additional-mcp-config` option is **required** for every Copilot CLI session to access Laravel Boost MCP tools.

### Laravel Package Development Environment
- This is a **Laravel package development project** using Orchestra Testbench, not a standard Laravel application.
- The environment differs significantly from a typical Laravel project - there is no full application context, database, or application-specific models.
- **Important:** Not all Laravel Boost MCP tools will work correctly in this environment:
  - Tools that depend on database connections, specific models, application routes, or other application-specific features may not be available or may fail.
  - Tools like `database-query`, `database-schema`, `list-routes` may return limited or no results.
  - Basic tools like `application-info`, `list-artisan-commands`, `search-docs` should work normally.
- Focus on package-specific development tasks: writing tests, implementing package features, and ensuring compatibility with Laravel.
- Use `vendor/bin/testbench` commands instead of `php artisan` when needed.
</laravel-boost-guidelines>
