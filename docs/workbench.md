# Orchestra Testbench Workbench Development Guide

This guide provides comprehensive documentation for developing Laravel packages using Orchestra Testbench Workbench, based on real-world examples and development patterns from the Laravel ecosystem.

## Overview

Orchestra Testbench Workbench provides a complete Laravel application environment for package development, allowing you to:

- **Test your package in a real Laravel application context**
- **Develop and preview package functionality interactively**
- **Create demo applications and examples**
- **Build comprehensive test suites with realistic scenarios**
- **Serve your package with a full Laravel application for development**

Workbench creates a complete Laravel application structure within your package, enabling you to develop, test, and demonstrate your package functionality in an isolated environment.

## Installation and Setup

### 1. Install Orchestra Testbench

Add Orchestra Testbench to your package's development dependencies:

```bash
composer require --dev orchestra/testbench
```

### 2. Install Workbench

Use the built-in installation command to set up workbench automatically:

```bash
# Install workbench (creates directory structure and updates composer.json)
vendor/bin/testbench workbench:install
```

This command will:
- Create the complete `workbench/` directory structure
- Automatically update your `composer.json` with the necessary autoload-dev configuration
- Set up the required composer scripts for workbench development

#### Installation Options

```bash
# Force overwrite existing files
vendor/bin/testbench workbench:install --force

# Skip routes and discovers installation (basic setup)
vendor/bin/testbench workbench:install --basic

# Install with DevTool support
vendor/bin/testbench workbench:install --devtool
```

#### Manual Composer Configuration (Alternative)

If you prefer to configure manually or need custom settings, you can update your `composer.json`:

```json
{
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/",
            "Workbench\\App\\": "workbench/app/",
            "Workbench\\Database\\Factories\\": "workbench/database/factories/",
            "Workbench\\Database\\Seeders\\": "workbench/database/seeders/"
        }
    },
    "scripts": {
        "post-autoload-dump": [
            "@clear",
            "@prepare"
        ],
        "clear": "@php vendor/bin/testbench package:purge-skeleton --ansi",
        "prepare": "@php vendor/bin/testbench package:discover --ansi",
        "build": "@php vendor/bin/testbench workbench:build --ansi",
        "serve": [
            "Composer\\Config::disableProcessTimeout",
            "@build",
            "@php vendor/bin/testbench serve --ansi"
        ]
    }
}
```

### 3. Initialize Workbench

After installation, run the following commands to set up your workbench environment:

```bash
# Clear any existing skeleton
composer clear

# Discover and prepare the package
composer prepare

# Build the workbench application
composer build
```

## Configuration with testbench.yaml

The `testbench.yaml` file is the heart of workbench configuration. Create this file in your package root:

### Basic Configuration

```yaml
providers:
  - Laravel\Socialite\SocialiteServiceProvider
  - Your\Package\Providers\YourServiceProvider
  - Workbench\App\Providers\WorkbenchServiceProvider

migrations:
  - workbench/database/migrations

workbench:
  start: '/'
  install: true
  health: false
  discovers:
    web: true
    api: false
    commands: false
    components: false
    views: false
    config: true
  build:
    - asset-publish
    - create-sqlite-db
    - db-wipe
    - migrate-fresh:
        --seed: true
        --seeder: Workbench\Database\Seeders\DatabaseSeeder
  assets:
    - laravel-assets
  sync: []
```

### Configuration File Management

The `testbench.yaml` file is specified in `.gitignore` and is not included in the repository. This allows developers to have personalized configurations without committing sensitive or environment-specific settings.

To use workbench configuration:

1. Copy the example configuration file:
   ```bash
   cp testbench.yaml.example testbench.yaml
   ```

2. Customize your local `testbench.yaml` file as needed for your development environment

3. The `testbench.yaml.example` file serves as a template and should be committed to the repository to help other developers set up their workbench

### Configuration Options Explained

#### Providers
List all service providers that should be loaded in the workbench environment:
- Third-party service providers (like Socialite)
- Your package's service providers
- Custom workbench service providers

#### Environment Variables
```yaml
env:
  - APP_ENV=testing
  - APP_KEY=base64:your-app-key
  - DB_CONNECTION=sqlite
  - DB_DATABASE=:memory:
  - YOUR_PACKAGE_CONFIG=value
```

#### Migrations
Specify directories containing migrations to run:
```yaml
migrations:
  - workbench/database/migrations
  - database/migrations
```

#### Workbench Settings
- `start`: The default route when serving the workbench application
- `install`: Whether to run package installation during build
- `health`: Enable/disable health checks
- `discovers`: Control Laravel's package auto-discovery features
  - `config: true`: Load configuration files from `workbench/config/` directory

#### Build Process
Define commands to run during workbench build:
```yaml
build:
  - asset-publish          # Publish package assets
  - create-sqlite-db       # Create SQLite database
  - db-wipe               # Clean database
  - migrate-fresh:        # Run fresh migrations
      --seed: true
      --seeder: Workbench\Database\Seeders\DatabaseSeeder
```

## Workbench Directory Structure

When you build your workbench, it creates a complete Laravel application structure:

```
workbench/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   ├── Models/
│   └── Providers/
│       └── WorkbenchServiceProvider.php
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
│       └── DatabaseSeeder.php
├── public/
├── resources/
│   ├── views/
│   └── js/
├── routes/
│   ├── web.php
│   ├── api.php
│   └── console.php
└── storage/
```

## Creating Workbench Components

### 1. Workbench Service Provider

Create a custom service provider for workbench-specific functionality:

```php
<?php

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider;
use Your\Package\SomeClass;
use Workbench\App\Services\DemoService;

class WorkbenchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register workbench-specific services
        $this->app->singleton(DemoService::class);
    }

    public function boot(): void
    {
        // Register package extensions or demo functionality
        SomeClass::register('demo', DemoService::class);
        
        // Load workbench routes
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        
        // Load workbench views
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'workbench');
    }
}
```

### 2. Database Seeders

Create seeders to populate test data:

```php
<?php

namespace Workbench\Database\Seeders;

use Illuminate\Database\Seeder;
use Workbench\Database\Factories\UserFactory;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create test users
        UserFactory::new()->count(10)->create();
        
        // Seed package-specific data
        $this->call([
            PackageDataSeeder::class,
        ]);
    }
}
```

### 3. Model Factories

Create factories for testing and demo data:

```php
<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
        ];
    }
}
```

### 4. Controllers and Routes

Create controllers to demonstrate package functionality:

```php
<?php

namespace Workbench\App\Http\Controllers;

use Illuminate\Http\Request;
use Your\Package\Facades\YourPackage;

class DemoController extends Controller
{
    public function index()
    {
        $data = YourPackage::getData();
        
        return view('workbench::demo', compact('data'));
    }
    
    public function api()
    {
        return response()->json([
            'package_version' => YourPackage::version(),
            'features' => YourPackage::getFeatures(),
        ]);
    }
}
```

Routes in `workbench/routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;
use Workbench\App\Http\Controllers\DemoController;

Route::get('/', [DemoController::class, 'index']);
Route::get('/api/demo', [DemoController::class, 'api']);
```

### 5. Console Commands

Define custom Artisan commands in `workbench/routes/console.php`:

```php
<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
```

Run workbench console commands using the testbench command wrapper:

```bash
# Execute workbench console commands
vendor/bin/testbench inspire

# List all available commands
vendor/bin/testbench list
```

## Testing with Workbench

### Using WithWorkbench Trait

Integrate workbench into your test suite:

```php
<?php

namespace Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use WithWorkbench;

    protected function getPackageProviders($app)
    {
        return [
            \Your\Package\Providers\YourServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        // Define test environment
        $app['config']->set('database.default', 'testing');
        $app['config']->set('your-package.key', 'test-value');
    }
}
```

### Feature Tests with Workbench

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class WorkbenchIntegrationTest extends TestCase
{
    public function test_workbench_routes_are_accessible()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertViewIs('workbench::demo');
    }
    
    public function test_package_api_integration()
    {
        $response = $this->get('/api/demo');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'package_version',
            'features',
        ]);
    }
}
```

## Development Workflow

### 1. Daily Development

```bash
# Start development server
composer serve

# Build workbench after changes
composer build

# Run tests
composer test

# Lint code
composer lint
```

### 2. Package Development Cycle

1. **Develop**: Write your package code in `src/`
2. **Configure**: Update `testbench.yaml` with required providers and settings
3. **Build**: Run `composer build` to create workbench application
4. **Test**: Use workbench routes and controllers to test functionality
5. **Iterate**: Make changes and rebuild as needed

### 3. Serving the Workbench

The workbench serves as a complete Laravel application:

```bash
# Serve with automatic building
composer serve

# Or manually
composer build
vendor/bin/testbench serve
```

Access your workbench at `http://localhost:8000` (or configured port).

## Advanced Configuration

### Custom Environment Files

Create environment-specific configurations:

```yaml
# testbench.yaml
env:
  - APP_ENV=testing
  - DB_CONNECTION=sqlite
  - DB_DATABASE=:memory:

# For different environments
workbench:
  start: '/'
  install: true
  discovers:
    web: true
    api: true
    commands: true
```

### Asset Management

Handle package assets in workbench:

```yaml
workbench:
  assets:
    - laravel-assets
    - package-assets
  build:
    - asset-publish
    - npm-install
    - npm-run-build
```

### Database Configuration

Configure different database setups:

```yaml
env:
  - DB_CONNECTION=mysql
  - DB_HOST=127.0.0.1
  - DB_PORT=3306
  - DB_DATABASE=workbench_test
  - DB_USERNAME=root
  - DB_PASSWORD=

migrations:
  - workbench/database/migrations
  - vendor/other-package/migrations
```

## Best Practices

### 1. Workbench Organization

- Keep workbench code separate from package code
- Use workbench for demonstrations and integration testing
- Create realistic test scenarios in workbench controllers
- Document workbench setup in your package README

### 2. Configuration Management

- Use environment variables for sensitive configuration
- Provide example configurations in `testbench.yaml.example`
- Document required environment variables
- Use sensible defaults for development

### 3. Testing Strategy

- Use workbench for integration testing
- Test package functionality through workbench routes
- Create comprehensive seeders for test data
- Verify package behavior in realistic scenarios

### 4. Development Efficiency

- Set up composer scripts for common tasks
- Use workbench for rapid prototyping
- Create demo pages to showcase package features
- Automate workbench building in your development workflow

## Common Patterns

### API Package Development

For packages providing API functionality:

```yaml
workbench:
  discovers:
    api: true
  build:
    - create-sqlite-db
    - migrate-fresh
```

### UI Component Packages

For packages with frontend components:

```yaml
workbench:
  discovers:
    views: true
    components: true
  assets:
    - laravel-assets
    - npm-install
    - npm-run-build
```

### Service Integration Packages

For packages integrating external services:

```yaml
env:
  - SERVICE_API_KEY=test-key
  - SERVICE_ENDPOINT=https://api.example.com

workbench:
  health: true
  build:
    - service-health-check
```

## Troubleshooting

### Common Issues

1. **Workbench not building**: Check `testbench.yaml` syntax and provider configuration
2. **Routes not loading**: Verify route file paths and service provider registration
3. **Database issues**: Ensure migration paths are correct and database is configured
4. **Asset problems**: Check asset publishing configuration and build scripts

### Debug Commands

```bash
# Clear and rebuild
composer clear && composer prepare && composer build

# Check package discovery
vendor/bin/testbench package:discover --ansi

# Verify configuration
vendor/bin/testbench about

# Check routes
vendor/bin/testbench route:list
```

## Conclusion

Orchestra Testbench Workbench provides a powerful environment for Laravel package development. By following these patterns and best practices, you can create comprehensive development and testing environments that make package development more efficient and reliable.

The key to successful workbench usage is treating it as a complete Laravel application that showcases and tests your package functionality in realistic scenarios. This approach leads to better package design, more thorough testing, and clearer documentation for your package users.
