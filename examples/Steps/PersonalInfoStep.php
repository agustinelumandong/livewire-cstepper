<?php

namespace App\Livewire\Examples\Steps;

use agustinelumandong\LivewireCstepper\Components\StepComponent;
use Illuminate\Contracts\View\View;

class PersonalInfoStep extends StepComponent
{
    protected $view = 'examples.steps.personal-info';

    public function stepTitle(): string
    {
        return 'Personal Information';
    }

    public function stepDescription(): string
    {
        return 'Please provide your basic personal information.';
    }

    public function stepIcon(): string
    {
        return 'user';
    }

    public function validationRules(): array
    {
        return [
            'formData.personal.first_name' => ['required', 'string', 'min:2', 'max:50'],
            'formData.personal.last_name' => ['required', 'string', 'min:2', 'max:50'],
            'formData.personal.date_of_birth' => ['required', 'date', 'before:today'],
            'formData.personal.gender' => ['required', 'in:male,female,other'],
        ];
    }

    public function mount()
    {
        // Initialize step-specific form data
        $this->updateStepperFormData([
            'personal' => array_merge([
                'first_name' => '',
                'last_name' => '',
                'date_of_birth' => '',
                'gender' => '',
            ], $this->getStepperFormData()['personal'] ?? [])
        ]);
    }

    public function onStepEnter($index): void
    {
        // This step is being entered
        // Custom logic for when entering this step
        logger('Entered personal info step');
    }

    public function onStepLeave($index): void
    {
        // This step is being left
        // Custom logic for when leaving this step
        logger('Left personal info step');
    }

    public function onStepValidationFailed(): void
    {
        // Handle step-specific validation failures
        logger('Personal info step validation failed');
    }

    public function render(): View
    {
        return view($this->view, [
            'personalData' => $this->getStepperFormData()['personal'] ?? [],
        ]);
    }
}