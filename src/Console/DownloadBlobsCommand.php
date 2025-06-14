<?php

declare(strict_types=1);

namespace Revolution\Bluesky\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Revolution\Bluesky\Core\CID;
use Revolution\Bluesky\Facades\Bluesky;
use Revolution\Bluesky\Support\DidDocument;
use Revolution\Bluesky\Support\Identity;
use Symfony\Component\Mime\MimeTypes;

/**
 * Sample command to download the actor's blob files.
 *
 * ```
 * php artisan bluesky:download-blobs ***.bsky.social
 * ```
 *
 * @link https://docs.bsky.app/blog/repo-export
 */
class DownloadBlobsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bluesky:download-blobs {actor : DID or handle}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download actor\'s blobs. Does not require auth.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $actor = $this->argument('actor');

        $this->warn('Actor: '.$actor);

        if (Identity::isHandle($actor)) {
            $did = Bluesky::resolveHandle($actor)->json('did');
        } else {
            $did = $actor;
        }

        if (! Identity::isDID($did)) {
            $this->error('Invalid actor');

            return 1;
        }

        /** @var string $did */
        $this->warn('DID: '.$did);

        $pds = DidDocument::make(Bluesky::identity()->resolveDID($did, cache: false)->json())->pdsUrl();

        $this->warn('PDS: '.$pds);

        $cursor = '';

        do {
            $response = Bluesky::client(auth: false)
                ->sync()
                ->baseUrl($pds.'/xrpc/')
                ->listBlobs(did: $did, cursor: $cursor)
                ->throw();

            $response->collect('cids')->each(function ($cid) use ($actor, $did, $pds) {
                $content = Bluesky::client(auth: false)
                    ->sync()
                    ->baseUrl($pds.'/xrpc/')
                    ->getBlob(did: $did, cid: $cid)
                    ->throw()
                    ->body();

                if (CID::verify(data: $content, cid: $cid, codec: CID::RAW)) {
                    $this->info('Verified');
                } else {
                    $this->error('Verify failed');
                }

                $name = Str::slug($actor, dictionary: ['.' => '-', ':' => '-']);

                $file = collect(['bluesky', 'download', $name, 'blob', $cid])
                    ->implode(DIRECTORY_SEPARATOR);

                Storage::put($file, $content);

                $file_ext = $file.$this->ext((string) Storage::mimeType($file));

                Storage::move($file, $file_ext);

                $this->line('Download: '.Storage::path($file_ext));
            });

            /** @var string $cursor */
            $cursor = $response->json('cursor');
            $this->warn('cursor: '.$cursor);
        } while (filled($cursor));

        $this->info('Download successful');

        return 0;
    }

    protected function ext(string $type): string
    {
        /** @var ?string $ext */
        $ext = head(MimeTypes::getDefault()->getExtensions($type));

        if (empty($ext)) {
            return '';
        }

        return '.'.$ext;
    }
}
