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
        // Use authentication due to temporary API restriction
        $response = Bluesky::login(identifier: config('bluesky.identifier'), password: config('bluesky.password'))
            ->searchPosts(q: '#laravel', until: $cursor, limit: $limit);

        $cursor = data_get($response->collect('posts')->last(), 'indexedAt', '');

        $feed = $response->collect('posts')->map(function (array $post) {
            return ['post' => data_get($post, 'uri')];
        })->toArray();

        return compact('cursor', 'feed');
    }
}
