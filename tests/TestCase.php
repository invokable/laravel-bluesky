<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Laravel\Socialite\SocialiteServiceProvider;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Revolution\Bluesky\Providers\BlueskyServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use WithWorkbench;

    /**
     * Load package service provider.
     *
     * @param  Application  $app
     */
    protected function getPackageProviders($app): array
    {
        return [
            BlueskyServiceProvider::class,
            SocialiteServiceProvider::class,
        ];
    }

    /**
     * Load package alias.
     *
     * @param  Application  $app
     */
    protected function getPackageAliases($app): array
    {
        return [
            //
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        tap($app['config'], function (Repository $config) {
            // for testing
            $config->set('bluesky.oauth.private_key', 'LS0tLS1CRUdJTiBQUklWQVRFIEtFWS0tLS0tDQpNSUdIQWdFQU1CTUdCeXFHU000OUFnRUdDQ3FHU000OUF3RUhCRzB3YXdJQkFRUWdEZkhxckZzSkRUUkVMa1ZIDQpHNG9DNTh2U2k0SnFkN3NPSTVNdTZzeVdrY21oUkFOQ0FBVHc2VU1DMlpYcFV4blhFc1BjRTA5aFdoYWdLbWxODQpRUXovSUlFYUVHdEFtSU5YeGUzTHZ0NE5KUS9YVWdGV3hkdEJBbUhQcFN4MlM3RnIvdmFhT2UzZw0KLS0tLS1FTkQgUFJJVkFURSBLRVktLS0tLQ',
            );
            $config->set('bluesky.labeler.private_key', 'LS0tLS1CRUdJTiBQUklWQVRFIEtFWS0tLS0tDQpNSUdFQWdFQU1CQUdCeXFHU000OUFnRUdCU3VCQkFBS0JHMHdhd0lCQVFRZ0ZlcFByV2RtSkdTUGpGbStiVmxuDQp
jWE4rV2lvaWVZaW1lU2ZDRiswSGRIeWhSQU5DQUFSbDlGVjREMGhPQTlKTEpXamQyaDRQaDVxUFdQakkxVjJmDQp4ZDdzV3c4WXp2SUZGZ2ExaE5pNU1ZWkg0ZmlMQUdQMXhuajU4RDZ0OEVJaEs3cUVYSUlUDQotLS0tLUVORCBQUklWQVRFIEtFWS0tLS0t',
            );
        });
    }
}
