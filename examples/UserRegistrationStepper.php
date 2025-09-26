<?php

namespace App\Livewire\Examples;

use agustinelumandong\LivewireCstepper\CStepper;
use App\Livewire\Examples\Steps\PersonalInfoStep;
use App\Livewire\Examples\Steps\ContactInfoStep;
use App\Livewire\Examples\Steps\ReviewStep;

class UserRegistrationStepper extends CStepper
{
    protected $steps = [
        PersonalInfoStep::class,
        ContactInfoStep::class,
        ReviewStep::class,
    ];

    public function mount()
    {
        parent::mount();
        
        // Initialize with empty form data
        $this->setFormData([
            'personal' => [],
            'contact' => [],
        ]);
    }

    public function handleSubmission()
    {
        // Process the completed form
        $formData = $this->getFormData();
        
        // Create user or perform other actions
        // User::create($formData);
        
        // Redirect or show success message
        session()->flash('success', 'Registration completed successfully!');
        
        // Optionally reset the stepper
        $this->resetStepper();
    }

    // Lifecycle hooks examples
    
    public function beforeStepChange($from, $to): void
    {
        // Log step changes
        logger("Step changed from {$from} to {$to}");
    }

    public function afterStepChange($from, $to): void
    {
        // Update progress tracking
        $this->dispatch('step-changed', [
            'from' => $from,
            'to' => $to,
            'progress' => $this->getProgressPercentage()
        ]);
    }

    public function stepperValidationFailed(): void
    {
        // Handle validation failures
        $this->dispatch('show-notification', [
            'type' => 'error',
            'message' => 'Please complete all required fields before proceeding.',
        ]);
    }
}