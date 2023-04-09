<?php
namespace Codelife\CodelifeModelGeneratorHelper\Providers;

use Illuminate\Support\ServiceProvider;

// add extras in composer.json file
// Include this provider first in config/app.php
// Codelife\CodelifeModelGeneratorHelper\Providers\ModelHelperServiceProvider::class,
class ModelHelperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public const STUB_PATH = __DIR__.'/../Console/Stubs/';

    public function boot(): void{
        $this->publishes([
            __DIR__.'/../Console/Commands/' => app_path('/Console/Commands')
        ], 'commands');
        $this->publishes([
            __DIR__.'/../public/' => public_path()
        ], 'assets');
        // Publish these migrations tags
        // php artisan vendor:publish --provider="Codelife\CodelifeModelGeneratorHelper\Providers\ModelHelperServiceProvider" --tag="commands"
        // Publish these assets tags
        // php artisan vendor:publish --provider="Codelife\CodelifeModelGeneratorHelper\Providers\ModelHelperServiceProvider" --tag="assets"
    }
}
