<?php

namespace agustinelumandong\LivewireCstepper\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class MakeStepCommand extends Command
{
    protected $signature = 'make:step {name} {--stepper=}';
    protected $description = 'Create a new Step component and attach it to an existing Stepper';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $stepper = $this->option('stepper');

        // Step paths
        $classPath = app_path("Livewire/Steps/{$name}.php");
        $viewPath = resource_path("views/livewire/steps/" . Str::kebab($name) . ".blade.php");

        // Check if step already exists
        if ($this->files->exists($classPath)) {
            return $this->error("Step already exists: {$classPath}");
        }

        // Create step files
        $this->files->ensureDirectoryExists(dirname($classPath));
        $this->files->put($classPath, $this->stepStub($name));
        $this->files->ensureDirectoryExists(dirname($viewPath));
        $this->files->put($viewPath, $this->stepViewStub($name));

        $this->info("Step created: {$classPath}");
        $this->info("View created: {$viewPath}");

        // Attach to Stepper if provided
        if ($stepper) {
            $this->attachToStepper($stepper, $name);
        }

        $this->info("✅ Step {$name} created successfully!");
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

    private function attachToStepper(string $stepperName, string $stepName): void
    {
        $stepperClass = app_path("Livewire/{$stepperName}.php");

        if (!$this->files->exists($stepperClass)) {
            $this->error("❌ Stepper {$stepperName} not found at {$stepperClass}");
            return;
        }

        $contents = $this->files->get($stepperClass);

        // Add use statement for the step
        $useStatement = "use App\\Livewire\\Steps\\{$stepName};";
        if (!str_contains($contents, $useStatement)) {
            $contents = preg_replace(
                '/(use agustinelumandong\\\\LivewireCstepper\\\\CStepper;)/',
                "$1\n$useStatement",
                $contents
            );
        }

        // Insert into defineSteps() method
        $pattern = '/return \[(.*?)\];/s';
        if (preg_match($pattern, $contents, $matches)) {
            $inside = trim($matches[1]);

            // Check if step already exists
            if (!str_contains($inside, "{$stepName}::class")) {
                if (empty($inside) || $inside === '// Steps will be registered automatically') {
                    // First step
                    $newInside = "\n            {$stepName}::class,\n        ";
                } else {
                    // Additional step
                    $newInside = $inside . "\n            {$stepName}::class,";
                }
                
                $contents = preg_replace($pattern, "return [{$newInside}];", $contents);
                $this->info("🔗 Added {$stepName} to {$stepperName}::defineSteps()");
            } else {
                $this->warn("⚠️ {$stepName} already exists in {$stepperName}");
            }
        }

        $this->files->put($stepperClass, $contents);
    }
}