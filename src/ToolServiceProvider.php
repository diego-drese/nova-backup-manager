<?php

namespace DiegoDrese\NovaBackupManager;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use DiegoDrese\NovaBackupManager\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider {
    public function boot() {
        $this->publishes([
            __DIR__ . '/../config/nova-backup-manager.php' => config_path('nova-backup-manager.php'),
        ], 'config');


        $this->publishes([
            __DIR__.'/../resources/lang/' => resource_path('lang/vendor/nova-backup-manager'),
        ]);

        $this->registerTranslations();

        $this->app->booted(function () {
            $this->routes();
        });

        $this->provideConfigToScript();
    }

    public function register() {
        $this->mergeConfigFrom(__DIR__ . '/../config/nova-backup-manager.php', 'nova-backup-manager');
    }

    protected function routes() {
        if ($this->app->routesAreCached()) {
            return;
        }
        Nova::router(['nova', 'auth'], 'nova-backup-manager')
            ->group(__DIR__.'/../routes/api.php');

        Nova::router(config('nova.api_middleware', []), 'backup-manager')
            ->group(__DIR__.'/../routes/inertia.php');


    }

    protected function provideConfigToScript() {
        Nova::serving(function () {
            $user = auth()->user();
            $resolver = config('nova-backup-manager.resolve_permissions');
            $permissions = is_callable($resolver)
                ? $resolver($user)
                : [
                    'create' => config('nova-backup-manager.create'),
                    'delete' => config('nova-backup-manager.delete'),
                    'download' => config('nova-backup-manager.download'),
                ];
            Nova::provideToScript([
                'nova_backup_manager' => array_merge($permissions, [
                    'polling' => config('nova-backup-manager.polling'),
                    'polling_interval' => config('nova-backup-manager.polling_interval'),
                ]),
            ]);
        });

    }

    protected function registerTranslations()
    {
        Nova::serving(function (ServingNova $event) {
            $currentLocale = app()->getLocale();
            Nova::translations(__DIR__.'/../resources/lang/'.$currentLocale.'.json');
            Nova::translations(resource_path('lang/vendor/nova-backup-manager/'.$currentLocale.'.json'));
            Nova::translations(lang_path('vendor/nova-backup-manager/'.$currentLocale.'.json'));
        });

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'BackupTool');
        $this->loadJSONTranslationsFrom(__DIR__.'/../resources/lang');
        $this->loadJSONTranslationsFrom(resource_path('lang/vendor/nova-backup-manager'));
        $this->loadJSONTranslationsFrom(lang_path('vendor/nova-backup-manager'));
    }
}
