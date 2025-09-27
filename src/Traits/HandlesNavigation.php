<?php

namespace agustinelumandong\LivewireCstepper\Traits;

use Livewire\Attributes\Url;

trait HandlesNavigation
{
    #[Url(keep: true)]
    public int $currentIndex = 0;
    public array $steps = [];

    public function stepIs($index): bool
    {
        return $this->currentIndex == $index;
    }

    public function stepIsGreaterOrEqualThan($index): bool
    {
        return $this->currentIndex >= $index;
    }

    public function stepIsLessOrEqualThan($index): bool
    {
        return $this->currentIndex <= $index;
    }

    public function advance($toIndex = null): void
    {
        $targetIndex = $toIndex ?? $this->getNextStepIndex();
        
        if ($this->stepExists($targetIndex)) {
            // Validate current step before advancing
            if ($this->validateCurrentStep()) {
                $this->jumpTo($targetIndex);
                
                // Mark stepper as active (user has interacted)
                $this->markStepperActive();
                
                // Dispatch step changed event for JavaScript
                $this->dispatch('step-changed', [
                    'step' => $this->currentIndex,
                    'total' => $this->getTotalSteps()
                ]);
            } else {
                // Trigger validation failed event
                $this->triggerEvent('stepValidationFailed', $this->currentIndex);
                
                // Show validation error notification
                $this->dispatch('wireui:notification', [
                    'title' => 'Validation Required',
                    'description' => 'Please complete all required fields before continuing.',
                    'icon' => 'exclamation-triangle'
                ]);
            }
        }
    }

    public function goBack($toIndex = null): void
    {
        $targetIndex = $toIndex ?? $this->getPreviousStepIndex();
        $this->jumpTo($targetIndex);
        
        // Mark stepper as active (user has interacted)
        $this->markStepperActive();
    }

    public function jumpTo($index): void
    {
        // Ensure index is valid
        if (!$this->stepExists($index)) {
            return;
        }

        $this->triggerEvent('beforeStepChange', $this->currentIndex, $index);

        // Check if navigation is allowed
        if ($this->canNavigateToStep($index)) {
            
            if ($this->hasPreviousStep($index)) {
                $this->validateStepsUpTo($this->getPreviousStepIndex($index));
            }

            $currentStep = $this->getCurrentStepComponent();
            if ($currentStep && method_exists($currentStep, 'triggerEvent')) {
                $currentStep->triggerEvent('onStepLeave', $this->currentIndex);
            }

            $previousIndex = $this->currentIndex;
            $this->currentIndex = $index;

            $newStep = $this->getCurrentStepComponent();
            if ($newStep && method_exists($newStep, 'triggerEvent')) {
                $newStep->triggerEvent('onStepEnter', $this->currentIndex);
            }

            $this->triggerEvent('afterStepChange', $previousIndex, $this->currentIndex);
        } else {
            $this->triggerEvent('navigationBlocked', $this->currentIndex, $index);
        }
    }

    public function canNavigateToStep($index): bool
    {
        // Check if step exists
        if (!$this->stepExists($index)) {
            return false;
        }

        // Allow navigation flexibility for basic tests, but respect validation settings
        if (!config('livewire-cstepper.strict_validation', true)) {
            return true;
        }

        // Check configuration for step jumping
        if (!config('livewire-cstepper.allow_step_jumping', false)) {
            // Only allow moving to adjacent steps or going backwards
            $allowedIndexes = [$this->getNextStepIndex(), $this->getPreviousStepIndex()];
            if (!in_array($index, $allowedIndexes)) {
                return false;
            }
        }

        // Validate all previous steps if moving forward
        if ($index > $this->currentIndex) {
            return $this->canAdvanceTo($index);
        }

        return true;
    }

    public function canAdvanceTo($index): bool
    {
        // Validate all steps from current to target-1  
        for ($stepIndex = $this->currentIndex; $stepIndex < $index; $stepIndex++) {
            if (!$this->validateStepAtIndex($stepIndex)) {
                return false;
            }
        }

        return true;
    }

    public function canAdvanceToNext(): bool
    {
        return $this->canAdvanceTo($this->getNextStepIndex()) && $this->hasNextStep();
    }

    public function canGoBackToPrevious(): bool
    {
        return $this->hasPreviousStep();
    }

    public function validateCurrentStep(): bool
    {
        return $this->validateStepAtIndex($this->currentIndex);
    }

    protected function validateStepAtIndex($index): bool
    {
        $step = $this->getStepComponentAtIndex($index);
        if (!$step) {
            return false;
        }
        
        try {
            $isValid = $step->isValid();
            return $isValid;
        } catch (\Exception $e) {
            // If validation fails due to an exception, consider it invalid
            // For debugging - we could log this
            return false;
        }
    }

    protected function validateStepsUpTo($maxIndex): bool
    {
        $allValid = true;
        
        for ($i = 0; $i <= $maxIndex; $i++) {
            if (!$this->validateStepAtIndex($i)) {
                $allValid = false;
            }
        }

        return $allValid;
    }

    public function hasNextStep(): bool
    {
        return $this->stepExists($this->getNextStepIndex());
    }

    public function hasPreviousStep($fromIndex = null): bool
    {
        $checkIndex = $fromIndex ?? $this->currentIndex;
        return $this->stepExists($this->getPreviousStepIndex($checkIndex));
    }

    public function getNextStepIndex($fromIndex = null): int
    {
        $checkIndex = $fromIndex ?? $this->currentIndex;
        return $checkIndex + 1;
    }

    public function getPreviousStepIndex($fromIndex = null): int
    {
        $checkIndex = $fromIndex ?? $this->currentIndex;
        return max(0, $checkIndex - 1);
    }

    public function isFirstStep(): bool
    {
        return $this->currentIndex === 0;
    }

    public function isLastStep(): bool
    {
        return $this->currentIndex === $this->getTotalSteps() - 1;
    }

    public function getTotalSteps(): int
    {
        return count($this->defineSteps());
    }

    public function getProgressPercentage(): float
    {
        if ($this->getTotalSteps() === 0) {
            return 0;
        }

        return ($this->currentIndex / ($this->getTotalSteps() - 1)) * 100;
    }

    public function getCompletedStepsCount(): int
    {
        return $this->currentIndex;
    }

    public function getRemainingStepsCount(): int
    {
        return max(0, $this->getTotalSteps() - $this->currentIndex - 1);
    }

    public function stepExists($index): bool
    {
        return $index >= 0 && $index < $this->getTotalSteps();
    }

    protected function getStepComponentAtIndex($index): ?object
    {
        $steps = $this->stepComponentInstances();
        return $steps[$index] ?? null;
    }

    protected function markStepperActive(): void
    {
        if (property_exists($this, 'sessionKey') && !empty($this->sessionKey)) {
            session()->put($this->sessionKey . '_active', true);
        }
    }
}