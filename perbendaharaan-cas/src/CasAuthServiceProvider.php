<?php

declare(strict_types=1);

namespace Perbendaharaan\CasAuth;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Perbendaharaan\CasAuth\Console\Commands\CasInstall;
use Perbendaharaan\CasAuth\Http\Middleware\AuthCas;
use Perbendaharaan\CasAuth\Http\Middleware\AuthLocal;

final class CasAuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            path: __DIR__ . '/Config/cas.php',
            key:  'cas',
        );
    }

    public function boot(): void
    {
        $this->registerPublishables();
        $this->registerMiddleware();
        $this->registerViews();
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->commands([CasInstall::class]);
        }
    }

    // -------------------------------------------------------------------------

    private function registerPublishables(): void
    {
        $this->publishes([
            __DIR__ . '/Config/cas.php'      => config_path('cas.php'),
            __DIR__ . '/../.env.cas.example' => base_path('.env.cas.example'),
        ], tags: 'cas-config');

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], tags: 'cas-migrations');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/cas-auth'),
        ], tags: 'cas-views');
    }

    private function registerMiddleware(): void
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        // cas.auth  → production/staging (SATUID)
        $router->aliasMiddleware('cas.auth', AuthCas::class);

        // cas.local → development tempatan (CAS_ENABLED=false)
        $router->aliasMiddleware('cas.local', AuthLocal::class);
    }

    private function registerViews(): void
    {
        // Daftarkan views pakej dengan namespace 'cas-auth'
        // Contoh: view('cas-auth::local.login')
        $this->loadViewsFrom(
            path: __DIR__ . '/../resources/views',
            namespace: 'cas-auth',
        );
    }
}
