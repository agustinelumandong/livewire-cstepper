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

    protected $queryString = [];

    public function resetStepper(): void
    {
        $this->triggerEvent('beforeResetStepper');

        $this->currentIndex = 0;
        $this->clearFormData();
        $this->cachedStepComponents = [];
        
        $this->mount();

        $this->triggerEvent('afterResetStepper');
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
            return;
        }

        if (method_exists($this, 'handleSubmission')) {
            $this->handleSubmission();
        }

        $this->triggerEvent('afterSubmitStepper');
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