<?php

namespace agustinelumandong\LivewireCstepper;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class StepperServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('livewire-cstepper')
            ->hasConfigFile()
            ->hasViews()
            ->hasAssets();
    }

    public function packageRegistered(): void
    {
        // Register any package-specific services here
    }

    public function packageBooted(): void
    {
        // Publish assets
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../dist' => public_path('vendor/livewire-cstepper'),
            ], 'livewire-cstepper-assets');
        }
    }
}