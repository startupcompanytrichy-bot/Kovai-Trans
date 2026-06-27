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

        $req = $this->app['request'];

        // Trust Render / proxy for correct URL generation
        if ($req->headers->has('X-Forwarded-Proto')) {
            $req->server->set('HTTPS', str_starts_with($req->header('X-Forwarded-Proto'), 'https') ? 'on' : 'off');
            \Illuminate\Support\Facades\URL::forceRootUrl($req->getSchemeAndHttpHost());
            \Illuminate\Support\Facades\URL::forceScheme($req->getScheme());
        }
        try {
            $settings = Setting::pluck('value', 'key');
            view()->share('globalSettings', $settings);
        } catch (\Exception $e) {
            view()->share('globalSettings', collect());
        }
    }
}
