<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('helpers.php');

        if (! env('DB_URL') && env('DATABASE_URL')) {
            $databaseUrl = env('DATABASE_URL');

            putenv("DB_URL={$databaseUrl}");
            $_ENV['DB_URL'] = $databaseUrl;
            $_SERVER['DB_URL'] = $databaseUrl;
        }

        if (! env('DB_CONNECTION') && env('DATABASE_URL')) {
            $connection = str_starts_with((string) env('DATABASE_URL'), 'mysql')
                ? 'mysql'
                : 'pgsql';

            putenv("DB_CONNECTION={$connection}");
            $_ENV['DB_CONNECTION'] = $connection;
            $_SERVER['DB_CONNECTION'] = $connection;
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
