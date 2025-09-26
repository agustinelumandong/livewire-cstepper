<?php

namespace agustinelumandong\LivewireCstepper\Traits;

trait SupportsLifecycle
{
    public function triggerEvent(string $event, ...$args): void
    {
        if (!method_exists($this, $event)) {
            return;
        }

        $this->{$event}(...$args);
    }

    // Default lifecycle hooks (can be overridden)
    
    public function beforeMount(...$args): void
    {
        // Override in implementing class
    }

    public function afterMount(...$args): void
    {
        // Override in implementing class
    }

    public function beforeStepChange($from, $to): void
    {
        // Override in implementing class
    }

    public function afterStepChange($from, $to): void
    {
        // Override in implementing class
    }

    public function beforeResetStepper(): void
    {
        // Override in implementing class
    }

    public function afterResetStepper(): void
    {
        // Override in implementing class
    }

    public function beforeSubmitStepper(): void
    {
        // Override in implementing class
    }

    public function afterSubmitStepper(): void
    {
        // Override in implementing class
    }

    public function stepperValidationFailed(): void
    {
        // Override in implementing class
    }

    public function navigationBlocked($from, $to): void
    {
        // Override in implementing class
    }

    // Step-level lifecycle hooks
    
    public function onStepEnter($index): void
    {
        // Override in implementing class
    }

    public function onStepLeave($index): void
    {
        // Override in implementing class
    }

    public function onStepInitialize(): void
    {
        // Override in implementing class
    }

    public function onStepPersist(): void
    {
        // Override in implementing class
    }

    public function onStepValidationPassed(): void
    {
        // Override in implementing class
    }

    public function onStepValidationFailed(): void
    {
        // Override in implementing class
    }

    public function onStepCleanup(): void
    {
        // Override in implementing class
    }
}