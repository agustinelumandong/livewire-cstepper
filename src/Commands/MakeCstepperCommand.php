<?php

namespace agustinelumandong\LivewireCstepper\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class MakeCstepperCommand extends Command
{
    protected $signature = 'make:cstepper {type} {name} {stepName?} {--steps=3}';
    protected $description = 'Generate a Livewire Stepper, Step, or preset with demo steps';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $type = strtolower($this->argument('type'));
        $name = Str::studly($this->argument('name'));

        match ($type) {
            'stepper' => $this->makeStepper($name),
            'step'    => $this->makeStep($name, Str::studly($this->argument('stepName'))),
            'preset'  => $this->makePreset($name),
            default   => $this->error("Invalid type. Use: stepper | step | preset"),
        };
    }

    private function makeStepper(string $name)
    {
        $classPath = app_path("Livewire/{$name}.php");
        $viewPath = resource_path("views/livewire/" . Str::kebab($name) . ".blade.php");

        if ($this->files->exists($classPath)) {
            return $this->error("Stepper already exists: {$classPath}");
        }

        $this->files->ensureDirectoryExists(dirname($classPath));
        $this->files->put($classPath, $this->stepperStub($name));
        $this->files->ensureDirectoryExists(dirname($viewPath));
        $this->files->put($viewPath, $this->stepperViewStub($name));

        $this->info("Stepper created: {$classPath}");
        $this->info("View created: {$viewPath}");
    }

    private function makeStep(string $stepper, string $step)
    {
        $classPath = app_path("Livewire/Steps/{$step}.php");
        $viewPath = resource_path("views/livewire/steps/" . Str::kebab($step) . ".blade.php");

        if ($this->files->exists($classPath)) {
            return $this->error("Step already exists: {$classPath}");
        }

        $this->files->ensureDirectoryExists(dirname($classPath));
        $this->files->put($classPath, $this->stepStub($step));
        $this->files->ensureDirectoryExists(dirname($viewPath));
        $this->files->put($viewPath, $this->stepViewStub($step));

        $this->info("Step created: {$classPath}");
        $this->info("View created: {$viewPath}");

        // 🔑 Auto-register into Stepper
        $this->registerStepInStepper($stepper, $step);
    }

    private function makePreset(string $name)
    {
        $this->makeStepper($name);

        $count = (int) $this->option('steps');
        $this->info("Generating {$count} steps for preset...");

        // Smart naming: Profile, Address, Confirm for first 3, then Step4, Step5, etc.
        $defaultNames = ['Profile', 'Address', 'Confirm'];
        
        for ($i = 0; $i < $count; $i++) {
            $stepName = $defaultNames[$i] ?? "Step" . ($i + 1);
            $this->makeStep($name, $stepName);
        }

        // Auto-register route for preset
        $this->appendRoute($name);

        $this->info("✅ Preset '{$name}' created with {$count} demo steps and route.");
    }

    private function stepperStub(string $name): string
    {
        $kebabName = Str::kebab($name);
        
        return <<<PHP
<?php

namespace App\Livewire;

use agustinelumandong\LivewireCstepper\CStepper;

class {$name} extends CStepper
{
    public function defineSteps(): array
    {
        return [
            // Steps will be registered automatically
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
    <!-- Generated {$name} Stepper -->
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

    private function registerStepInStepper(string $stepper, string $step): void
    {
        $stepperPath = app_path("Livewire/{$stepper}.php");

        if (!$this->files->exists($stepperPath)) {
            $this->warn("Stepper {$stepper} not found. Step not auto-registered.");
            return;
        }

        $content = $this->files->get($stepperPath);

        // Add use statement for the step
        $useStatement = "use App\\Livewire\\Steps\\{$step};";
        if (!str_contains($content, $useStatement)) {
            $content = preg_replace(
                '/(use agustinelumandong\\\\LivewireCstepper\\\\CStepper;)/',
                "$1\n$useStatement",
                $content
            );
        }

        // Inject step class into defineSteps() method
        $pattern = '/return \[(.*?)\];/s';
        if (preg_match($pattern, $content, $matches)) {
            $inside = trim($matches[1]);
            
            // Check if step already exists
            if (!str_contains($inside, "{$step}::class")) {
                if (empty($inside) || $inside === '// Steps will be registered automatically') {
                    // First step
                    $newInside = "\n            {$step}::class,\n        ";
                } else {
                    // Additional step
                    $newInside = $inside . "\n            {$step}::class,";
                }
                
                $content = preg_replace(
                    $pattern, 
                    "return [{$newInside}];", 
                    $content
                );
                
                $this->info("🔗 Added {$step} to {$stepper}::defineSteps()");
            } else {
                $this->warn("⚠️ {$step} already exists in {$stepper}");
            }
        }

        $this->files->put($stepperPath, $content);
    }

    private function appendRoute(string $name): void
    {
        $routeFile = base_path("routes/web.php");
        $routeName = Str::kebab($name);
        
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