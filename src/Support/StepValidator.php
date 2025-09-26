<?php

namespace agustinelumandong\LivewireCstepper\Support;

use agustinelumandong\LivewireCstepper\Components\StepComponent;

class StepValidator
{
    protected StepComponent $step;

    public function __construct(StepComponent $step)
    {
        $this->step = $step;
    }

    public static function for(StepComponent $step): static
    {
        return new static($step);
    }

    public function validate(): bool
    {
        try {
            if (!method_exists($this->step, 'validationRules')) {
                return true;
            }

            $rules = $this->step->validationRules();
            if (empty($rules)) {
                return true;
            }

            $validator = validator(
                ['formData' => $this->step->getStepper()->getFormData()],
                ...$rules
            );

            return !$validator->fails();
        } catch (\Exception $e) {
            $this->logValidationError($e);
            return false;
        }
    }

    public function getValidationMessages(): array
    {
        try {
            if (!method_exists($this->step, 'validationRules')) {
                return [];
            }

            $rules = $this->step->validationRules();
            if (empty($rules)) {
                return [];
            }

            $validator = validator(
                ['formData' => $this->step->getStepper()->getFormData()],
                ...$rules
            );

            return $validator->errors()->all();
        } catch (\Exception $e) {
            $this->logValidationError($e);
            return ['Validation error occurred'];
        }
    }

    public function hasValidationErrors(): bool
    {
        return !$this->validate();
    }

    protected function logValidationError(\Exception $e): void
    {
        if (app()->hasDebugModeEnabled()) {
            logger('Step validation error: ' . $e->getMessage(), [
                'step' => get_class($this->step),
                'formData' => $this->step->getStepper()->getFormData(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}