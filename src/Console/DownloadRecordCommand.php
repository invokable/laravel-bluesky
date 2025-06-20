<?php

declare(strict_types=1);

namespace Revolution\Bluesky\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Revolution\Bluesky\Core\CBOR;
use Revolution\Bluesky\Core\CID;
use Revolution\Bluesky\Facades\Bluesky;
use Revolution\Bluesky\Support\AtUri;

/**
 * Sample command to download the actor's Record. Download json directly instead of parsing CAR file.
 *
 * Specify collection with "-C" [default: "app.bsky.feed.post"]
 * ```
 * php artisan bluesky:download-record ***.bsky.social -C app.bsky.feed.post
 * ```
 *
 * @link https://docs.bsky.app/blog/repo-export
 */
class DownloadRecordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bluesky:download-record {actor : DID or handle} {--C|collection=app.bsky.feed.post}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download actor\'s Record. Does not require auth.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $actor = $this->argument('actor');
        $collection = (string) $this->option('collection');

        $this->warn('Actor: '.$actor);
        $this->warn('Collection: '.$collection);

        $cursor = '';

        do {
            $response = Bluesky::client(auth: false)
                ->baseUrl(Bluesky::entryway().'/xrpc')
                ->listRecords(repo: $actor, collection: $collection, limit: 50, cursor: $cursor)
                ->throw();

            $response->collect('records')->each(function (array $record) use ($actor, $collection) {
                $at = AtUri::parse(data_get($record, 'uri'));

                $cid = data_get($record, 'cid');
                $block = data_get($record, 'value');

                if (CID::verify(CBOR::encode($block), $cid)) {
                    $this->info('Verified');
                } else {
                    $this->error('Verify failed');
                }

                $name = Str::slug($actor, dictionary: ['.' => '-', ':' => '-']);

                $file = collect(['bluesky', 'download', $name, $collection, $at->rkey().'.json'])
                    ->implode(DIRECTORY_SEPARATOR);

                Storage::put($file, json_encode($record, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));

                $this->line('Download: '.Storage::path($file));
            });

            /** @var string $cursor */
            $cursor = $response->json('cursor');
            $this->warn('cursor: '.$cursor);
        } while (filled($cursor));

        $this->info('Download successful');

        return 0;
    }
}
