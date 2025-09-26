<?php

namespace agustinelumandong\LivewireCstepper\Support;

use agustinelumandong\LivewireCstepper\CStepper;

class NavigationGuard
{
    protected CStepper $stepper;

    public function __construct(CStepper $stepper)
    {
        $this->stepper = $stepper;
    }

    public static function for(CStepper $stepper): static
    {
        return new static($stepper);
    }

    public function canNavigateToStep(int $targetIndex): bool
    {
        // Check if step exists
        if (!$this->stepExists($targetIndex)) {
            return false;
        }

        // Check configuration for step jumping
        if (!config('livewire-cstepper.allow_step_jumping', false)) {
            return $this->canNavigateWithoutJumping($targetIndex);
        }

        // Validate required steps if moving forward
        if ($targetIndex > $this->stepper->currentIndex) {
            return $this->canAdvanceToStep($targetIndex);
        }

        // Allow backward navigation
        return true;
    }

    public function canAdvanceToStep(int $targetIndex): bool
    {
        // Validate all steps from current to target-1
        for ($i = $this->stepper->currentIndex; $i < $targetIndex; $i++) {
            if (!$this->validateStepAtIndex($i)) {
                return false;
            }
        }

        return true;
    }

    public function canGoBack(): bool
    {
        return $this->stepper->currentIndex > 0;
    }

    public function getNavigationBlockReason(int $targetIndex): ?string
    {
        if (!$this->stepExists($targetIndex)) {
            return 'Step does not exist.';
        }

        if (!config('livewire-cstepper.allow_step_jumping', false)) {
            if (!$this->canNavigateWithoutJumping($targetIndex)) {
                return 'Step jumping is disabled. You can only move to adjacent steps.';
            }
        }

        if ($targetIndex > $this->stepper->currentIndex) {
            for ($i = $this->stepper->currentIndex; $i < $targetIndex; $i++) {
                if (!$this->validateStepAtIndex($i)) {
                    return "Step " . ($i + 1) . " must be completed before advancing.";
                }
            }
        }

        return null;
    }

    public function getRequiredStepsForNavigation(int $targetIndex): array
    {
        $requiredSteps = [];

        if ($targetIndex > $this->stepper->currentIndex) {
            for ($i = $this->stepper->currentIndex; $i < $targetIndex; $i++) {
                if (!$this->validateStepAtIndex($i)) {
                    $requiredSteps[] = $i;
                }
            }
        }

        return $requiredSteps;
    }

    protected function canNavigateWithoutJumping(int $targetIndex): bool
    {
        $currentIndex = $this->stepper->currentIndex;
        
        // Allow adjacent steps or going backward
        $allowedIndexes = [
            $currentIndex - 1, // Previous step
            $currentIndex + 1, // Next step
        ];

        // Also allow any previous step (going backward)
        if ($targetIndex < $currentIndex) {
            return true;
        }

        return in_array($targetIndex, $allowedIndexes);
    }

    protected function stepExists(int $index): bool
    {
        return $index >= 0 && $index < $this->stepper->getTotalSteps();
    }

    protected function validateStepAtIndex(int $index): bool
    {
        $step = $this->getStepComponentAtIndex($index);
        
        if (!$step) {
            return false;
        }

        return StepValidator::for($step)->validate();
    }

    protected function getStepComponentAtIndex(int $index): ?object
    {
        $steps = $this->stepper->stepComponentInstances();
        return $steps[$index] ?? null;
    }
}