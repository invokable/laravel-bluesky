<?php

declare(strict_types=1);

namespace Workbench\App\FeedGenerator;

use Illuminate\Http\Request;
use Revolution\Bluesky\Contracts\FeedGeneratorAlgorithm;
use Revolution\Bluesky\Facades\Bluesky;

class ArtisanFeed implements FeedGeneratorAlgorithm
{
    public function __invoke(int $limit, ?string $cursor, ?string $user, Request $request): array
    {
        // Use searchPosts() in the public api. It cannot be used when restricted, so use the authentication method below.
        $response = Bluesky::public()->searchPosts(q: '#laravel', until: $cursor, limit: $limit);

        // Using searchPosts() with authentication
        $response = Bluesky::login(identifier: config('bluesky.identifier'), password: config('bluesky.password'))
                        ->searchPosts(q: $q, limit: 10);

        $cursor = data_get($response->collect('posts')->last(), 'indexedAt', '');

        $feed = $response->collect('posts')->map(function (array $post) {
            return ['post' => data_get($post, 'uri')];
        })->toArray();

        return compact('cursor', 'feed');
    }
}
