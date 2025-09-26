<?php

namespace Tests\Components;

use AgustinElumandong\LivewireCstepper\CStepper;
use AgustinElumandong\LivewireCstepper\Components\StepComponent;
use Livewire\Attributes\Validate;

class WireUITestStepper extends CStepper
{
    // Form data properties with validation
    #[Validate('required|string|min:2')]
    public $firstName = '';
    
    #[Validate('required|string|min:2')]
    public $lastName = '';
    
    #[Validate('required|email')]
    public $email = '';
    
    #[Validate('nullable|string')]
    public $phone = '';
    
    #[Validate('nullable|date')]
    public $dateOfBirth = '';
    
    #[Validate('required|string')]
    public $address = '';
    
    #[Validate('required|string')]
    public $city = '';
    
    #[Validate('required|string')]
    public $state = '';
    
    #[Validate('required|string')]
    public $zipCode = '';
    
    #[Validate('required|string')]
    public $country = '';
    
    #[Validate('accepted')]
    public $termsAccepted = false;

    /**
     * Define the steps for this stepper
     */
    public function steps(): array
    {
        return [
            PersonalInfoTestStep::class,
            AddressInfoTestStep::class,
            ReviewConfirmTestStep::class,
        ];
    }

    /**
     * Handle successful completion
     */
    public function onCompleted(): void
    {
        // Show success notification using WireUI
        $this->dispatch('wireui:notification', [
            'title' => 'Success!',
            'description' => 'Your information has been submitted successfully.',
            'icon' => 'check-circle',
            'iconColor' => 'text-green-500'
        ]);

        // Log the form data (in real app, save to database)
        logger('WireUI Test Form Completed', [
            'personal' => [
                'firstName' => $this->firstName,
                'lastName' => $this->lastName,
                'email' => $this->email,
                'phone' => $this->phone,
                'dateOfBirth' => $this->dateOfBirth,
            ],
            'address' => [
                'address' => $this->address,
                'city' => $this->city,
                'state' => $this->state,
                'zipCode' => $this->zipCode,
                'country' => $this->country,
            ],
            'termsAccepted' => $this->termsAccepted,
        ]);

        // Reset form for demo purposes
        $this->resetFormData();
    }

    /**
     * Reset all form data
     */
    public function resetFormData(): void
    {
        $this->firstName = '';
        $this->lastName = '';
        $this->email = '';
        $this->phone = '';
        $this->dateOfBirth = '';
        $this->address = '';
        $this->city = '';
        $this->state = '';
        $this->zipCode = '';
        $this->country = '';
        $this->termsAccepted = false;
        
        $this->currentIndex = 0;
        $this->resetErrorBag();
    }

    /**
     * Get completion percentage for current step
     */
    public function getCompletionPercentage(): int
    {
        $totalFields = 11; // Total required fields
        $completedFields = 0;

        // Count completed fields
        if (!empty($this->firstName)) $completedFields++;
        if (!empty($this->lastName)) $completedFields++;
        if (!empty($this->email)) $completedFields++;
        if (!empty($this->phone)) $completedFields++;
        if (!empty($this->dateOfBirth)) $completedFields++;
        if (!empty($this->address)) $completedFields++;
        if (!empty($this->city)) $completedFields++;
        if (!empty($this->state)) $completedFields++;
        if (!empty($this->zipCode)) $completedFields++;
        if (!empty($this->country)) $completedFields++;
        if ($this->termsAccepted) $completedFields++;

        return round(($completedFields / $totalFields) * 100);
    }

    /**
     * Test method to simulate form filling
     */
    public function fillTestData(): void
    {
        $this->firstName = 'John';
        $this->lastName = 'Doe';
        $this->email = 'john.doe@example.com';
        $this->phone = '+1 (555) 123-4567';
        $this->dateOfBirth = '1990-01-15';
        $this->address = '123 Main Street';
        $this->city = 'New York';
        $this->state = 'NY';
        $this->zipCode = '10001';
        $this->country = 'US';
        $this->termsAccepted = true;

        $this->dispatch('wireui:notification', [
            'title' => 'Test Data Filled',
            'description' => 'All form fields have been populated with test data.',
            'icon' => 'information-circle'
        ]);
    }

    /**
     * Get form data summary for review
     */
    public function getFormSummary(): array
    {
        return [
            'personal' => [
                'name' => trim($this->firstName . ' ' . $this->lastName),
                'email' => $this->email,
                'phone' => $this->phone ?: 'Not provided',
                'dateOfBirth' => $this->dateOfBirth ?: 'Not provided',
            ],
            'address' => [
                'street' => $this->address,
                'city' => $this->city,
                'state' => $this->state,
                'zipCode' => $this->zipCode,
                'country' => $this->country,
            ],
            'completionPercentage' => $this->getCompletionPercentage(),
        ];
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('tests.views.wireui-test-stepper', [
            'formSummary' => $this->getFormSummary(),
        ]);
    }
}

// Step 1: Personal Information
class PersonalInfoTestStep extends StepComponent
{
    public function title(): string
    {
        return 'Personal Information';
    }

    public function description(): string
    {
        return 'Please provide your basic personal information to get started.';
    }

    public function validate(): bool
    {
        // Access stepper via parent component (CStepper)
        $stepper = $this->parent;
        
        // Simple validation for required fields
        return !empty($stepper->firstName) && 
               !empty($stepper->lastName) && 
               !empty($stepper->email) &&
               filter_var($stepper->email, FILTER_VALIDATE_EMAIL);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('tests.views.personal-info-test-step');
    }
}

// Step 2: Address Information  
class AddressInfoTestStep extends StepComponent
{
    public function title(): string
    {
        return 'Address Information';
    }

    public function description(): string
    {
        return 'Enter your address details for shipping and billing purposes.';
    }

    public function validate(): bool
    {
        // Access stepper via parent component (CStepper)
        $stepper = $this->parent;
        
        return !empty($stepper->address) &&
               !empty($stepper->city) &&
               !empty($stepper->state) &&
               !empty($stepper->zipCode) &&
               !empty($stepper->country);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('tests.views.address-info-test-step');
    }
}

// Step 3: Review & Confirm
class ReviewConfirmTestStep extends StepComponent
{
    public function title(): string
    {
        return 'Review & Confirm';
    }

    public function description(): string
    {
        return 'Please review all information carefully before submitting.';
    }

    public function validate(): bool
    {
        // Access stepper via parent component (CStepper)
        $stepper = $this->parent;
        
        return $stepper->termsAccepted === true;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('tests.views.review-confirm-test-step');
    }
}