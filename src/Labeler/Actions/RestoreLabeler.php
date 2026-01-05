<?php

declare(strict_types=1);

namespace Revolution\Bluesky\Labeler\Actions;

use Revolution\Bluesky\Facades\Bluesky;

/**
 * @internal
 *
 * @link https://github.com/skyware-js/labeler/blob/main/src/scripts/plc.ts#L45
 */
class RestoreLabeler
{
    public function __invoke(string $did, string $password, string $service, string $plcToken): array
    {
        Bluesky::login($did, $password, $service);

        $credentials = Bluesky::client()
            ->atproto()
            ->getRecommendedDidCredentials();

        $operation = [];

        if (filled($credentials->json('verificationMethods.atproto_label'))) {
            $operation['verificationMethods'] = $credentials->collect('verificationMethods')
                ->forget(['atproto_label'])
                ->toArray();
        }

        if (filled($credentials->json('services.atproto_labeler'))) {
            $operation['services'] = $credentials->collect('services')
                ->forget(['atproto_labeler'])
                ->toArray();
        }

        if (empty($operation)) {
            return [];
        }

        $plcOp = Bluesky::client()
            ->atproto()
            ->signPlcOperation(
                token: $plcToken,
                verificationMethods: data_get($operation, 'verificationMethods'),
                services: data_get($operation, 'services'),
            );

        $submit = Bluesky::client()
            ->atproto()
            ->submitPlcOperation(operation: $plcOp->json('operation'));

        return $operation;
    }
}
