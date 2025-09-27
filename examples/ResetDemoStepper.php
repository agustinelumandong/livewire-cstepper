<?php

namespace agustinelumandong\LivewireCstepper\Examples;

use agustinelumandong\LivewireCstepper\CStepper;
use agustinelumandong\LivewireCstepper\Examples\Steps\PersonalInfoStep;

/**
 * Example demonstrating stepper reset functionality
 * 
 * This example shows how the stepper handles:
 * - Automatic reset on page refresh
 * - Manual reset with confirmation
 * - Session-based state management
 */
class ResetDemoStepper extends CStepper
{
    public array $formData = [
        'name' => '',
        'email' => '',
        'phone' => '',
        'address' => '',
        'preferences' => []
    ];

    public function defineSteps(): array
    {
        return [
            BasicInfoStep::class,
            PreferencesStep::class,
            ReviewStep::class,
        ];
    }

    public function handleSubmission(): void
    {
        // Validate all form data
        $this->validate([
            'formData.name' => 'required|string|min:2',
            'formData.email' => 'required|email',
            'formData.phone' => 'required|string|min:10',
            'formData.address' => 'required|string|min:10',
        ]);

        // Process the form submission
        logger('Demo form submitted with data:', $this->formData);

        // Show success notification
        $this->dispatch('wireui:notification', [
            'title' => 'Form Submitted Successfully!',
            'description' => 'Your information has been processed.',
            'icon' => 'success'
        ]);

        // Reset after successful submission
        $this->resetStepper();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return parent::render()->with([
            'sessionInfo' => $this->getSessionInfo(),
        ]);
    }

    /**
     * Get session information for debugging/demo purposes
     */
    public function getSessionInfo(): array
    {
        return [
            'session_key' => $this->sessionKey ?? 'not_set',
            'is_initialized' => session()->has($this->sessionKey . '_initialized'),
            'is_active' => session()->has($this->sessionKey . '_active'),
            'refresh_detected' => session()->get('cstepper_refresh_detected', false),
        ];
    }

    /**
     * Demo method to simulate form data corruption (for testing reset)
     */
    public function corruptFormData(): void
    {
        $this->formData['corrupted'] = 'This data should not be here';
        $this->currentIndex = 999; // Invalid step
        
        $this->dispatch('wireui:notification', [
            'title' => 'Form Data Corrupted',
            'description' => 'Demo corruption applied. Try refreshing the page or using reset.',
            'icon' => 'exclamation'
        ]);
    }
}

/**
 * Example step classes for the demo
 */
class BasicInfoStep extends \agustinelumandong\LivewireCstepper\Components\StepComponent
{
    public function stepTitle(): string
    {
        return 'Basic Information';
    }

    public function stepDescription(): string
    {
        return 'Please provide your basic contact information.';
    }

    public function toHtml(): string
    {
        return '
            <div class="space-y-4">
                <x-input 
                    wire:model="formData.name" 
                    label="Full Name" 
                    placeholder="Enter your full name"
                    required
                />
                <x-input 
                    wire:model="formData.email" 
                    label="Email Address" 
                    placeholder="Enter your email"
                    type="email"
                    required
                />
            </div>
        ';
    }

    public function isValid(): bool
    {
        $formData = $this->stepper->getFormData();
        return !empty($formData['name']) && 
               !empty($formData['email']) &&
               filter_var($formData['email'], FILTER_VALIDATE_EMAIL);
    }
}

class PreferencesStep extends \agustinelumandong\LivewireCstepper\Components\StepComponent
{
    public function stepTitle(): string
    {
        return 'Preferences';
    }

    public function stepDescription(): string
    {
        return 'Set your preferences and contact details.';
    }

    public function toHtml(): string
    {
        return '
            <div class="space-y-4">
                <x-input 
                    wire:model="formData.phone" 
                    label="Phone Number" 
                    placeholder="Enter your phone number"
                    required
                />
                <x-textarea 
                    wire:model="formData.address" 
                    label="Address" 
                    placeholder="Enter your address"
                    rows="3"
                    required
                />
                <x-checkbox 
                    wire:model="formData.preferences.newsletter" 
                    label="Subscribe to newsletter"
                />
                <x-checkbox 
                    wire:model="formData.preferences.sms" 
                    label="Receive SMS notifications"
                />
            </div>
        ';
    }

    public function isValid(): bool
    {
        $formData = $this->stepper->getFormData();
        return !empty($formData['phone']) && 
               !empty($formData['address']);
    }
}

class ReviewStep extends \agustinelumandong\LivewireCstepper\Components\StepComponent
{
    public function stepTitle(): string
    {
        return 'Review & Submit';
    }

    public function stepDescription(): string
    {
        return 'Please review your information before submitting.';
    }

    public function toHtml(): string
    {
        $data = $this->stepper->getFormData();
        
        return '
            <div class="space-y-6">
                <x-card title="Review Your Information" class="bg-gray-50">
                    <div class="space-y-3">
                        <div><strong>Name:</strong> ' . ($data['name'] ?? 'Not provided') . '</div>
                        <div><strong>Email:</strong> ' . ($data['email'] ?? 'Not provided') . '</div>
                        <div><strong>Phone:</strong> ' . ($data['phone'] ?? 'Not provided') . '</div>
                        <div><strong>Address:</strong> ' . ($data['address'] ?? 'Not provided') . '</div>
                        <div><strong>Newsletter:</strong> ' . (($data['preferences']['newsletter'] ?? false) ? 'Yes' : 'No') . '</div>
                        <div><strong>SMS Notifications:</strong> ' . (($data['preferences']['sms'] ?? false) ? 'Yes' : 'No') . '</div>
                    </div>
                </x-card>
                
                <x-alert title="Reset Demo Features" info class="mt-4">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Try refreshing the page - the stepper should reset automatically</li>
                        <li>Use the Reset button to manually reset with confirmation</li>
                        <li>The stepper maintains session state during normal navigation</li>
                        <li>External referrers trigger automatic reset</li>
                    </ul>
                </x-alert>
                
                <!-- Demo Controls -->
                <div class="flex space-x-2 pt-4">
                    <x-button 
                        wire:click="corruptFormData" 
                        outline 
                        warning
                        icon="exclamation"
                    >
                        Corrupt Data (Demo)
                    </x-button>
                </div>
            </div>
        ';
    }

    public function isValid(): bool
    {
        return true; // Review step is always valid
    }
}