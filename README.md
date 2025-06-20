Bluesky(AT Protocol) for Laravel
====

[![packagist](https://badgen.net/packagist/v/revolution/laravel-bluesky)](https://packagist.org/packages/revolution/laravel-bluesky)
[![Maintainability](https://qlty.sh/badges/cd9f285b-4006-4a23-bbf0-fdf266b38cc2/maintainability.svg)](https://qlty.sh/gh/invokable/projects/laravel-bluesky)
[![Code Coverage](https://qlty.sh/badges/cd9f285b-4006-4a23-bbf0-fdf266b38cc2/test_coverage.svg)](https://qlty.sh/gh/invokable/projects/laravel-bluesky)

[![Ask DeepWiki](https://deepwiki.com/badge.svg)](https://deepwiki.com/invokable/laravel-bluesky)

## Requirements
- PHP >= 8.2
- Laravel >= 11.x

## Version
| ver | PHP  | Laravel |
|-----|------|---------|
| 1.x | ^8.2 | ^11.x   |

- The auto-generated code from lexicon may contain breaking changes.

## Installation

```shell
composer require revolution/laravel-bluesky
```

Basically, everything can be set in the `.env` file, so publishing the config file is optional.

### Uninstall
```shell
composer remove revolution/laravel-bluesky
```

## Quick start

### Search posts (no auth required, no need for your own account)

There are many public APIs that do not require authentication if you just want to retrieve data.

> **Note** Due to temporary API restrictions, searchPosts() currently requires authentication. Please use the authenticated example below instead.

```php
// routes/web.php (CURRENTLY NOT WORKING - unauthenticated usage is temporarily restricted)

use Illuminate\Support\Facades\Route;
use Revolution\Bluesky\Facades\Bluesky;

Route::get('search', function () {
    /** @var \Illuminate\Http\Client\Response $response */
    $response = Bluesky::searchPosts(q: '#bluesky', limit: 10);

    $response->collect('posts')
        ->each(function (array $post) {
            dump(data_get($post, 'author.displayName'));
            dump(data_get($post, 'author.handle'));
            dump(data_get($post, 'author.did'));
            dump(data_get($post, 'record.text'));
        });
});
```

#### Authenticated usage (currently required due to temporary API restriction)

```php
// routes/web.php

use Illuminate\Support\Facades\Route;
use Revolution\Bluesky\Facades\Bluesky;

Route::get('search-auth', function () {
    $response = Bluesky::login(identifier: config('bluesky.identifier'), password: config('bluesky.password'))
        ->searchPosts(q: '#bluesky', limit: 10);

    $response->collect('posts')
        ->each(function (array $post) {
            dump(data_get($post, 'author.displayName'));
            dump(data_get($post, 'author.handle'));
            dump(data_get($post, 'author.did'));
            dump(data_get($post, 'record.text'));
        });
});
```

### Get someone's posts (no auth required)

```php
// routes/web.php

use Illuminate\Support\Facades\Route;
use Revolution\Bluesky\Facades\Bluesky;

Route::get('feed', function () {
    // "actor" is did(did:plc:***) or handle(***.bsky.social, alice.test)
    $response = Bluesky::getAuthorFeed(actor: '***.bsky.social');

    $response->collect('feed')
        ->each(function (array $feed) {
            dump(data_get($feed, 'post.author.displayName'));
            dump(data_get($feed, 'post.record.text'));
        });
});
```

You can get your own posts by specifying your did or handle as the actor. No authentication is required to get and save your own posts.

### Create a post (requires auth)

There are two authentication methods for Bluesky: "App password" and "OAuth". Here we will use "App password". Obtain the App password from Bluesky and set it in .env.

```
// .env

BLUESKY_IDENTIFIER=***.bsky.social
BLUESKY_APP_PASSWORD=****-****-****-****
```

```php
// routes/web.php

use Illuminate\Support\Facades\Route;
use Revolution\Bluesky\Facades\Bluesky;

Route::get('post', function () {
    $response = Bluesky::login(identifier: config('bluesky.identifier'), password: config('bluesky.password'))
                       ->post('Hello Bluesky');
});
```

This is easy if you're just sending simple text, but in the real world you'll need to use `TextBuilder` to make links and tags work.

```php
// routes/web.php

use Illuminate\Support\Facades\Route;
use Revolution\Bluesky\Facades\Bluesky;
use Revolution\Bluesky\Record\Post;
use Revolution\Bluesky\RichText\TextBuilder;

Route::get('text-builder', function () {
    $post = Post::build(function (TextBuilder $builder) {
        $builder->text('Hello Bluesky')
                ->newLine(count: 2)
                ->link('https://bsky.app/')
                ->newLine()
                ->tag('#Bluesky');
    });

    $response = Bluesky::login(identifier: config('bluesky.identifier'), password: config('bluesky.password'))
                       ->post($post);
});
```

Following message will be posted:

```
Hello Bluesky

https://bsky.app/
#Bluesky
```

To authenticate with OAuth, read the Socialite documentation.

## Usage
- [Basic Client](./docs/basic-client.md)
- [Laravel Notifications](./docs/notification.md)
- [Socialite](./docs/socialite.md)
- [FeedGenerator](./docs/feed-generator.md)
- [Testing](./docs/testing.md)

## Advanced
- [Identity](./docs/identity.md)
- [Verify](./docs/verify.md)
- [Route](./docs/route.md)
- [Labeler](./docs/labeler.md)

## Sample project
- [Labeler](https://github.com/invokable/laralabeler)
- [Statusphere Laravel edition](https://github.com/invokable/statusphere)

## Contracts
https://github.com/invokable/atproto-lexicon-contracts

## LICENCE
MIT
