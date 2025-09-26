<?php

namespace agustinelumandong\LivewireCstepper\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class MakeCstepperPresetCommand extends Command
{
    protected $signature = 'make:cstepper-preset {name} {--steps=3}';
    protected $description = 'Scaffold a full Stepper with N demo steps and a test route';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $routeName = Str::kebab($name);
        $stepsCount = (int) $this->option('steps');

        // Step names (Profile, Address, Confirm) or Generic
        $defaultNames = ['Profile', 'Address', 'Confirm'];
        $steps = [];

        for ($i = 0; $i < $stepsCount; $i++) {
            $steps[] = $defaultNames[$i] ?? "Step" . ($i + 1);
        }

        // Create stepper
        $this->createStepper($name, $steps);

        // Create steps
        foreach ($steps as $step) {
            $this->createStep($step);
        }

        // Auto-register route
        $this->appendRoute($name, $routeName);

        $this->info("✅ Preset {$name} created successfully with {$stepsCount} steps and route /{$routeName}");
    }

    private function createStepper(string $name, array $steps): void
    {
        $stepperClassPath = app_path("Livewire/{$name}.php");
        $stepperViewPath = resource_path("views/livewire/" . Str::kebab($name) . ".blade.php");

        if ($this->files->exists($stepperClassPath)) {
            $this->error("Stepper already exists: {$stepperClassPath}");
            return;
        }

        // Build imports and step array
        $imports = [];
        $stepClasses = [];
        foreach ($steps as $step) {
            $imports[] = "use App\\Livewire\\Steps\\{$step};";
            $stepClasses[] = "{$step}::class";
        }

        $stepperClass = $this->stepperStub($name, $imports, $stepClasses);
        $stepperView = $this->stepperViewStub($name);

        $this->files->ensureDirectoryExists(dirname($stepperClassPath));
        $this->files->put($stepperClassPath, $stepperClass);
        $this->files->ensureDirectoryExists(dirname($stepperViewPath));
        $this->files->put($stepperViewPath, $stepperView);

        $this->info("Created: {$stepperClassPath}");
        $this->info("Created: {$stepperViewPath}");
    }

    private function createStep(string $name): void
    {
        $classPath = app_path("Livewire/Steps/{$name}.php");
        $viewPath = resource_path("views/livewire/steps/" . Str::kebab($name) . ".blade.php");

        $this->files->ensureDirectoryExists(dirname($classPath));
        $this->files->put($classPath, $this->stepStub($name));
        $this->files->ensureDirectoryExists(dirname($viewPath));
        $this->files->put($viewPath, $this->stepViewStub($name));

        $this->info("Created: {$classPath}");
        $this->info("Created: {$viewPath}");
    }

    private function stepperStub(string $name, array $imports, array $stepClasses): string
    {
        $kebabName = Str::kebab($name);
        $importsString = implode("\n", $imports);
        $stepsString = implode(",\n            ", $stepClasses);

        return <<<PHP
<?php

namespace App\Livewire;

use agustinelumandong\LivewireCstepper\CStepper;
{$importsString}

class {$name} extends CStepper
{
    public function defineSteps(): array
    {
        return [
            {$stepsString},
        ];
    }

    public function render()
    {
        return view('livewire.{$kebabName}');
    }
}
PHP;
    }

    private function stepperViewStub(string $name): string
    {
        return <<<BLADE
<div class="livewire-cstepper-container">
    <!-- Generated {$name} Stepper Preset -->
    <x-card class="stepper-card">
        <!-- Progress Bar -->
        <div class="mb-8">
            @include('livewire-cstepper::components.progress-bar', [
                'currentIndex' => \$this->currentIndex,
                'totalSteps' => \$this->getTotalSteps(),
                'percentage' => \$this->getProgressPercentage()
            ])
        </div>

        <!-- Step Content -->
        <div class="step-content">
            @if(\$currentStep = \$this->getCurrentStepComponent())
                <div class="current-step">
                    <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">
                        {{ \$currentStep->stepTitle() ?? 'Step ' . (\$this->currentIndex + 1) }}
                    </h2>
                    
                    @if(method_exists(\$currentStep, 'stepDescription') && \$currentStep->stepDescription())
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            {{ \$currentStep->stepDescription() }}
                        </p>
                    @endif
                    
                    <!-- Step Body -->
                    <div class="step-body" wire:loading.class="opacity-50">
                        @if(\$errors->any())
                            <x-alert title="Please correct the following errors:" negative class="mb-6">
                                <ul class="list-disc list-inside">
                                    @foreach(\$errors->all() as \$error)
                                        <li>{{ \$error }}</li>
                                    @endforeach
                                </ul>
                            </x-alert>
                        @endif
                        
                        {!! \$currentStep->toHtml() !!}
                    </div>
                </div>
            @endif
        </div>

        <!-- Navigation Controls -->
        <x-slot name="footer" class="step-navigation">
            <div class="flex justify-between items-center w-full">
                <div class="flex space-x-4">
                    @if(!\$this->isFirstStep())
                        <x-button 
                            wire:click="goBack" 
                            wire:loading.attr="disabled"
                            secondary
                            icon="arrow-left"
                        >
                            Previous
                        </x-button>
                    @endif
                </div>

                <div class="flex space-x-4">
                    @if(\$this->isLastStep())
                        <x-button 
                            wire:click="submitStepper" 
                            wire:loading.attr="disabled"
                            positive
                            icon="check"
                            spinner="submitStepper"
                        >
                            Complete
                        </x-button>
                    @else
                        <x-button 
                            wire:click="advance" 
                            wire:loading.attr="disabled"
                            primary
                            icon="arrow-right"
                            right-icon="arrow-right"
                            spinner="advance"
                        >
                            Next
                        </x-button>
                    @endif
                </div>
            </div>
        </x-slot>
    </x-card>
</div>
BLADE;
    }

    private function stepStub(string $step): string
    {
        $kebabStep = Str::kebab($step);
        
        return <<<PHP
<?php

namespace App\Livewire\Steps;

use agustinelumandong\LivewireCstepper\Components\StepComponent;

class {$step} extends StepComponent
{
    public function render()
    {
        return view('livewire.steps.{$kebabStep}');
    }

    public function isValid(): bool
    {
        // Add your validation logic here
        // Return true if this step is valid and user can proceed
        return true;
    }

    public function validate(): array
    {
        // Return validation errors as array, empty array means no errors
        return [];
    }
}
PHP;
    }

    private function stepViewStub(string $step): string
    {
        return <<<BLADE
<div class="step-container">
    <!-- {$step} Step Content -->
    <div class="space-y-6">
        <div class="text-center">
            <x-icon name="clipboard-list" class="w-12 h-12 mx-auto text-primary-500 mb-4" />
            <p class="text-gray-600 dark:text-gray-400">
                This is the {$step} step. Add your form fields and content here.
            </p>
        </div>

        <!-- Example form fields using WireUI -->
        <div class="grid gap-6">
            <x-input 
                label="Sample Field" 
                placeholder="Enter value here..." 
                hint="This is an example field for the {$step} step"
            />
            
            <x-textarea 
                label="Notes" 
                placeholder="Add any additional notes..."
                rows="3"
            />
        </div>

        <!-- Step-specific actions (optional) -->
        <div class="flex justify-center">
            <x-button 
                outline 
                secondary 
                icon="information-circle"
            >
                Need Help?
            </x-button>
        </div>
    </div>
</div>
BLADE;
    }

    private function appendRoute(string $name, string $routeName): void
    {
        $routeFile = base_path("routes/web.php");
        $routeEntry = <<<PHP

// Auto-generated by livewire-cstepper
use App\Livewire\\{$name};

Route::get('/{$routeName}', {$name}::class)->name('{$routeName}');
PHP;

        if ($this->files->exists($routeFile)) {
            $contents = $this->files->get($routeFile);

            if (!str_contains($contents, "Route::get('/{$routeName}'")) {
                $this->files->append($routeFile, $routeEntry . PHP_EOL);
                $this->info("📍 Added route: GET /{$routeName}");
            } else {
                $this->warn("⚠️ Route /{$routeName} already exists in routes/web.php");
            }
        }
    }
}