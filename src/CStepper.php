<?php

namespace agustinelumandong\LivewireCstepper;

use Closure;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Illuminate\Support\Str;
use agustinelumandong\LivewireCstepper\Components\StepComponent;
use agustinelumandong\LivewireCstepper\Traits\ManagesFormData;
use agustinelumandong\LivewireCstepper\Traits\HandlesNavigation;
use agustinelumandong\LivewireCstepper\Traits\SupportsLifecycle;
use agustinelumandong\LivewireCstepper\Contracts\StepperContract;

abstract class CStepper extends Component implements StepperContract
{
    use HandlesNavigation;
    use SupportsLifecycle;
    use ManagesFormData;

    public bool $persistStepData = true;
    public null|array|Model $model = null;
    protected array $cachedStepComponents = [];
    protected string $sessionKey;

    protected $queryString = [];

    public function resetStepper(): void
    {
        $this->triggerEvent('beforeResetStepper');

        $this->currentIndex = 0;
        $this->clearFormData();
        $this->cachedStepComponents = [];
        
        // Clear session markers
        session()->forget($this->sessionKey . '_initialized');
        session()->forget($this->sessionKey . '_data');
        
        // Clear any validation errors
        $this->resetValidation();
        
        $this->mount();

        $this->triggerEvent('afterResetStepper');
        
        // Show reset confirmation
        $this->dispatch('wireui:notification', [
            'title' => 'Stepper Reset',
            'description' => 'The stepper has been reset to the beginning.',
            'icon' => 'refresh'
        ]);
    }

    public function resetWithConfirmation(): void
    {
        $this->dispatch('wireui:confirm', [
            'title' => 'Reset Stepper',
            'description' => 'Are you sure you want to reset the stepper? All progress will be lost.',
            'icon' => 'question',
            'accept' => [
                'label' => 'Yes, Reset',
                'method' => 'confirmReset',
            ],
            'reject' => [
                'label' => 'Cancel',
            ]
        ]);
    }

    public function confirmReset(): void
    {
        $this->resetStepper();
    }

    public function defineSteps(): array
    {
        if (property_exists($this, 'steps')) {
            return $this->steps;
        }

        return [];
    }

    public function mount()
    {
        $this->triggerEvent('beforeMount', ...func_get_args());

        // Initialize session key for this stepper instance
        $this->sessionKey = 'cstepper_' . $this->getId();
        
        // Check if this is a fresh page load (not a Livewire request)
        if ($this->shouldAutoReset()) {
            $this->performAutoReset();
        } else {
            // Mark stepper as initialized for this session
            session()->put($this->sessionKey . '_initialized', true);
        }

        if (method_exists($this, 'model')) {
            $this->model = $this->model();
        }

        $this->stepComponentInstances(function (StepComponent $step) {
            
            if (method_exists($this, 'model')) {
                $step->setModel($this->model);
            }
            
            if (method_exists($step, 'mount')) {
                $step->mount();
            }

            if ($step->getSequence() < $this->currentIndex && !$step->isValid()) {
                $this->jumpTo($step->getSequence());
            }
        });

        $this->triggerEvent('afterMount', ...func_get_args());
    }

    protected function shouldAutoReset(): bool
    {
        // Reset if this is a fresh page load (no session marker)
        if (!session()->has($this->sessionKey . '_initialized')) {
            return true;
        }

        // Also reset if user came from external source (no referrer from same domain)
        $referrer = request()->header('referer');
        if (!$referrer || !str_contains($referrer, request()->getHost())) {
            return true;
        }

        // Check for explicit reset parameter in URL
        if (request()->has('reset') && request()->get('reset') === 'true') {
            return true;
        }

        return false;
    }

    protected function performAutoReset(): void
    {
        $this->currentIndex = 0;
        $this->clearFormData();
        $this->cachedStepComponents = [];
        
        // Clear any existing session data
        session()->forget($this->sessionKey . '_data');
        
        // Mark as initialized
        session()->put($this->sessionKey . '_initialized', true);
    }

    public function stepComponentInstances(null|Closure $callback = null): array
    {
        if (filled($this->cachedStepComponents)) {
            return collect($this->cachedStepComponents)
                ->each(fn(StepComponent $step, $index) => value($callback, $step, $index))
                ->toArray();
        }

        if (filled($this->defineSteps())) {
            $this->cachedStepComponents = collect($this->defineSteps())
                ->map(function ($step, $index) use ($callback) {
                    if (class_exists($step) && is_subclass_of($step, StepComponent::class)) {
                        $stepInstance = $step::make($this);

                        if (is_null($stepInstance->getSequence())) {
                            $stepInstance->setSequence($index);
                        }

                        return $stepInstance;
                    }
                    return null;
                })
                ->filter()
                ->sortBy('sequence')
                ->values()
                ->toArray();

            collect($this->cachedStepComponents)
                ->each(fn(StepComponent $step, $index) => value($callback, $step, $index));
        }

        return $this->cachedStepComponents;
    }

    public function render(): View
    {
        return view($this->stepperView(), [
            'steps' => $this->stepComponentInstances(),
            'currentStep' => $this->getCurrentStepComponent(),
            'formData' => $this->getFormData(),
            'canAdvance' => $this->canAdvanceToNext(),
            'canGoBack' => $this->canGoBackToPrevious(),
        ]);
    }

    protected function stepperView(): string
    {
        return 'livewire-cstepper::stepper';
    }

    public function getCurrentStepComponent(): ?StepComponent
    {
        return collect($this->stepComponentInstances())
            ->firstWhere('sequence', $this->currentIndex);
    }

    public function submitStepper(): void
    {
        $this->triggerEvent('beforeSubmitStepper');

        if (!$this->validateAllSteps()) {
            $this->triggerEvent('stepperValidationFailed');
            $this->showValidationErrorNotification();
            return;
        }

        if (method_exists($this, 'handleSubmission')) {
            $this->handleSubmission();
        }

        $this->showSuccessNotification();
        $this->triggerEvent('afterSubmitStepper');
    }

    /**
     * Show success notification using WireUI
     */
    protected function showSuccessNotification(): void
    {
        $this->dispatch('wireui:notification', [
            'title' => 'Success!',
            'description' => 'Form completed successfully.',
            'icon' => 'success'
        ]);
    }

    /**
     * Show validation error notification using WireUI
     */
    protected function showValidationErrorNotification(): void
    {
        $this->dispatch('wireui:notification', [
            'title' => 'Validation Error',
            'description' => 'Please correct the errors before proceeding.',
            'icon' => 'error'
        ]);
    }

    /**
     * Get WireUI configuration
     */
    protected function getWireUIConfig(): array
    {
        return config('livewire-cstepper.wireui', [
            'card_variant' => 'default',
            'button_variant' => 'primary',
            'progress_color' => 'primary',
        ]);
    }

    protected function validateAllSteps(): bool
    {
        return collect($this->stepComponentInstances())
            ->every(fn(StepComponent $step) => $step->isValid());
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    /**
     * Get all form data - alias for getFormData() for backward compatibility
     */
    public function getAllFormData(): array
    {
        return $this->getFormData();
    }
}