<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        require_once app_path('Helpers/FinancialYearHelper.php');
        require_once app_path('Helpers/SettingsHelper.php');
    }

    public function boot(): void
    {
        if ($this->app->environment('local')) {
            $this->app->make('view')->getEngineResolver()->register('blade', function () {
                $cachePath = $this->app['config']->get('view.compiled', storage_path('framework/views'));
                $compiler = new class($this->app['files'], $cachePath) extends \Illuminate\View\Compilers\BladeCompiler {
                    public function isExpired($path) { return true; }
                };
                return new \Illuminate\View\Engines\CompilerEngine($compiler);
            });
        }

        if (($this->app['request']->header('X-Forwarded-Proto') ?? '') === 'https') {
            $this->app['request']->server->set('HTTPS', 'on');
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        try {
            $settings = Setting::pluck('value', 'key');
            view()->share('globalSettings', $settings);
        } catch (\Exception $e) {
            view()->share('globalSettings', collect());
        }
    }
}
