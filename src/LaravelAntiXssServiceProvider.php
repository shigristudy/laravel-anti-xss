<?php

namespace Kabeer\LaravelAntiXss;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Kabeer\LaravelAntiXss\Http\Middleware\XssProtection;

class LaravelAntiXssServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-anti-xss')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton('anti-xss', function ($app) {
            return new AntiXss($app['config']['anti-xss']);
        });
    }

    public function packageBooted(): void
    {
        $this->app['router']->aliasMiddleware('xss-protection', XssProtection::class);
    }
}
