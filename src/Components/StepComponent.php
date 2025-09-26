<?php

namespace agustinelumandong\LivewireCstepper\Components;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\View\Component as ViewComponent;
use agustinelumandong\LivewireCstepper\Traits\InteractsWithStepper;
use agustinelumandong\LivewireCstepper\Traits\SupportsLifecycle;
use agustinelumandong\LivewireCstepper\Contracts\StepperContract;

abstract class StepComponent extends ViewComponent implements Htmlable
{
    use InteractsWithStepper;
    use SupportsLifecycle;

    public ?int $sequence = null;
    public null|array|Model $model = null;
    protected string $view;
    public bool $validationFailed = false;

    public function __construct(StepperContract $stepper)
    {
        $this
            ->setStepper($stepper)
            ->setModel($stepper->getModel());
    }

    public static function make(StepperContract $stepper): static
    {
        return new static($stepper);
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setModel(?Model $model): StepComponent
    {
        $this->model = $model;
        return $this;
    }

    public function getSequence(): ?int
    {
        return $this->sequence;
    }

    public function setSequence(?int $sequence): StepComponent
    {
        $this->sequence = $sequence;
        return $this;
    }

    public function stepIcon(): string
    {
        return 'check-circle';
    }

    public function stepTitle(): string
    {
        return 'Step ' . ($this->sequence + 1);
    }

    public function stepDescription(): ?string
    {
        return null;
    }

    public function canSkip(): bool
    {
        return false;
    }

    public function isValid(): bool
    {
        if (method_exists($this, 'validationRules')) {
            try {
                $rules = $this->validationRules();
                if (empty($rules)) {
                    return true;
                }

                return !validator(['formData' => $this->stepper->getFormData()], ...$rules)->fails();
            } catch (\Exception $e) {
                // Log validation errors for debugging
                if (app()->hasDebugModeEnabled()) {
                    logger('Step validation error: ' . $e->getMessage(), [
                        'step' => static::class,
                        'formData' => $this->stepper->getFormData()
                    ]);
                }
                return false;
            }
        }

        return true;
    }

    public function setFormData(array $data = [])
    {
        $this->getStepper()->setFormData($data);
    }

    public function updateFormData(array $data = [])
    {
        $this->getStepper()->updateFormData($data);
    }

    public function appendFormData($key, $value = null, $default = null)
    {
        $this->getStepper()->appendFormData($key, $value, $default);
    }

    public function initialize(): void
    {
        $this->triggerEvent('onStepInitialize');
    }

    public function persist(): void
    {
        $this->triggerEvent('onStepPersist');
    }

    public function validate(): bool
    {
        $isValid = $this->isValid();
        $this->validationFailed = !$isValid;
        
        if ($isValid) {
            $this->triggerEvent('onStepValidationPassed');
        } else {
            $this->triggerEvent('onStepValidationFailed');
        }

        return $isValid;
    }

    public function cleanup(): void
    {
        $this->triggerEvent('onStepCleanup');
    }

    public function toHtml()
    {
        return $this->render()->render();
    }

    public function render(): View
    {
        $viewName = $this->view ?? $this->getDefaultView();
        
        return view($viewName, [
            'step' => $this,
            'formData' => $this->stepper->getFormData(),
            'model' => $this->model,
        ]);
    }

    protected function getDefaultView(): string
    {
        $className = class_basename(static::class);
        $viewName = Str::kebab($className);
        
        return "livewire-cstepper::steps.{$viewName}";
    }
}