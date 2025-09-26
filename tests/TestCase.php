<?php

namespace agustinelumandong\LivewireCstepper\Tests;

use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use agustinelumandong\LivewireCstepper\StepperServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up Livewire testing
        $this->artisan('view:clear');
    }

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            StepperServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Set testing view paths
        $app['config']->set('view.paths', [
            __DIR__.'/views',
            resource_path('views'),
        ]);

        // Configure Livewire CStepper for testing
        $app['config']->set('livewire-cstepper', [
            'ui_framework' => 'wireui',
            'strict_validation' => false,  // Disable for easier testing
            'allow_step_jumping' => true,  // Enable for testing
            'progress_bar_style' => 'modern',
            'animation_enabled' => true,
            'auto_save_enabled' => false,
        ]);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
    }
}