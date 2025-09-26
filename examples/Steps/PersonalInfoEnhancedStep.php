<?php

namespace App\Livewire\Examples\Steps;

use agustinelumandong\LivewireCstepper\Components\StepComponent;
use Illuminate\Contracts\View\View;

class PersonalInfoEnhancedStep extends StepComponent
{
    protected $view = 'examples.steps.personal-info-enhanced';

    public function stepTitle(): string
    {
        return 'Personal Information';
    }

    public function stepDescription(): string
    {
        return 'Please provide your personal details to create your account.';
    }

    public function stepIcon(): string
    {
        return 'user-circle';
    }

    public function validationRules(): array
    {
        return [
            'formData.personal.first_name' => ['required', 'string', 'min:2', 'max:50'],
            'formData.personal.last_name' => ['required', 'string', 'min:2', 'max:50'],
            'formData.personal.email' => ['required', 'email', 'max:255'],
            'formData.personal.date_of_birth' => ['required', 'date', 'before:13 years ago'],
            'formData.personal.gender' => ['required', 'in:male,female,other,not_specified'],
            'formData.personal.phone' => ['nullable', 'string', 'max:20'],
            'formData.personal.country' => ['required', 'string', 'max:2'],
            'formData.personal.address' => ['nullable', 'string', 'max:500'],
            'formData.personal.newsletter_subscription' => ['boolean'],
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'formData.personal.first_name' => 'first name',
            'formData.personal.last_name' => 'last name',
            'formData.personal.email' => 'email address',
            'formData.personal.date_of_birth' => 'date of birth',
            'formData.personal.gender' => 'gender',
            'formData.personal.phone' => 'phone number',
            'formData.personal.country' => 'country',
            'formData.personal.address' => 'address',
        ];
    }

    public function mount()
    {
        // Initialize step-specific form data with enhanced fields
        $this->updateStepperFormData([
            'personal' => array_merge([
                'first_name' => '',
                'last_name' => '',
                'email' => '',
                'date_of_birth' => '',
                'gender' => '',
                'phone' => '',
                'country' => '',
                'address' => '',
                'newsletter_subscription' => false,
            ], $this->getStepperFormData()['personal'] ?? [])
        ]);
    }

    public function onStepEnter($index): void
    {
        // Custom logic when entering this step
        // You can add notifications here if needed
    }

    public function onStepLeave($index): void
    {
        // Custom logic when leaving this step
        // Validation is handled automatically
    }

    public function render(): View
    {
        return view($this->view, [
            'formData' => $this->getStepperFormData(),
        ]);
    }

    /**
     * Custom validation messages for better UX
     */
    public function validationMessages(): array
    {
        return [
            'formData.personal.first_name.required' => 'Please enter your first name.',
            'formData.personal.first_name.min' => 'Your first name must be at least 2 characters long.',
            'formData.personal.last_name.required' => 'Please enter your last name.',
            'formData.personal.last_name.min' => 'Your last name must be at least 2 characters long.',
            'formData.personal.email.required' => 'Please provide your email address.',
            'formData.personal.email.email' => 'Please enter a valid email address.',
            'formData.personal.date_of_birth.required' => 'Please select your date of birth.',
            'formData.personal.date_of_birth.before' => 'You must be at least 13 years old to register.',
            'formData.personal.gender.required' => 'Please select your gender.',
            'formData.personal.country.required' => 'Please select your country.',
        ];
    }

    /**
     * Check if the step can be completed
     */
    public function canComplete(): bool
    {
        $formData = $this->getStepperFormData();
        $personal = $formData['personal'] ?? [];

        return !empty($personal['first_name']) &&
               !empty($personal['last_name']) &&
               !empty($personal['email']) &&
               !empty($personal['date_of_birth']) &&
               !empty($personal['gender']) &&
               !empty($personal['country']);
    }

    /**
     * Get completion percentage for this step
     */
    public function getCompletionPercentage(): int
    {
        $formData = $this->getStepperFormData();
        $personal = $formData['personal'] ?? [];
        
        $requiredFields = ['first_name', 'last_name', 'email', 'date_of_birth', 'gender', 'country'];
        $completedFields = 0;
        
        foreach ($requiredFields as $field) {
            if (!empty($personal[$field])) {
                $completedFields++;
            }
        }
        
        return (int) (($completedFields / count($requiredFields)) * 100);
    }
}