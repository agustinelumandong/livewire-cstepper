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
            ->hasViews();
    }

    public function packageRegistered(): void
    {
        // Register any package-specific services here
    }

    public function packageBooted(): void
    {
        // Boot any package-specific services here
    }
}