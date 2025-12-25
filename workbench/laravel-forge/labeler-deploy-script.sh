$CREATE_RELEASE()

cd $FORGE_RELEASE_DIRECTORY

$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader
$FORGE_PHP artisan optimize
$FORGE_PHP artisan storage:link
$FORGE_PHP artisan migrate --force

npm ci || npm install && npm run build

$ACTIVATE_RELEASE()

$RESTART_QUEUES()

# If you are using zero-downtime deployments with the new Laravel Forge in 2025, please adjust as we have not confirmed that this will work correctly.

# After being stopped during deployment, the Labeler Server is automatically restarted by Supervisor.
cd $FORGE_RELEASE_DIRECTORY
$FORGE_PHP artisan bluesky:labeler:server status
$FORGE_PHP artisan bluesky:labeler:server stop

# If the above stop command does not work, restart the daemon directly.
# https://forge.laravel.com/docs/sites/deployments#restarting-background-processes
# https://forge.laravel.com/docs/resources/background-processes#manually-restarting-background-processes
sudo -S supervisorctl restart daemon-{id}:*
$FORGE_PHP artisan bluesky:labeler:server status
