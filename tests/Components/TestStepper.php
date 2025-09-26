<?php

namespace agustinelumandong\LivewireCstepper\Tests\Components;

use agustinelumandong\LivewireCstepper\CStepper;

class TestStepper extends CStepper
{
    public array $formData = [
        'step1' => [],
        'step2' => [],
        'step3' => []
    ];
    
    public ?string $message = null;

    public array $steps = [
        TestStepOne::class,
        TestStepTwo::class,
        TestStepThree::class,
    ];

    public function submit()
    {
        $this->dispatch('stepper-completed', $this->getAllFormData());
        session()->flash('message', 'Stepper completed successfully!');
        
        // Also set as a property for testing
        $this->message = 'Stepper completed successfully!';
    }

    protected function stepperView(): string
    {
        return 'test-stepper';
    }

    // Helper method for testing
    public function getStepCount(): int
    {
        return count($this->steps);
    }
}