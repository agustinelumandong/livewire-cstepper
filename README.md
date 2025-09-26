# Livewire CStepper

A modern, feature-rich Laravel Livewire 3 package for creating multi-step forms with enhanced UX and WireUI integration.

## Features

✨ **Modern Architecture**: Clean, maintainable code with proper separation of concerns  
🎯 **Livewire 3 Optimized**: Built specifically for Livewire 3 with proper serialization handling  
🎨 **WireUI Integration**: Beautiful, accessible components out of the box  
🔄 **Flexible Navigation**: Support for linear and non-linear step progression  
✅ **Advanced Validation**: Step-level and cross-step validation with real-time feedback  
🎪 **Lifecycle Hooks**: Comprehensive event system for customization  
📱 **Responsive Design**: Mobile-friendly progress indicators and controls  
⚡ **Performance Optimized**: Efficient state management and caching  

## Installation

Install the package via Composer:

```bash
composer require agustinelumandong/livewire-cstepper
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag="livewire-cstepper-config"
```

Optionally, publish the views for customization:

```bash
php artisan vendor:publish --tag="livewire-cstepper-views"
```

## Quick Start

### 1. Create a Stepper Component

```php
<?php

namespace App\Livewire;

use agustinelumandong\LivewireCstepper\CStepper;
use App\Livewire\Steps\PersonalInfoStep;
use App\Livewire\Steps\ContactInfoStep;
use App\Livewire\Steps\ReviewStep;

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
        
        $this->setFormData([
            'personal' => [],
            'contact' => [],
        ]);
    }

    public function handleSubmission()
    {
        $formData = $this->getFormData();
        
        // Process your completed form
        User::create($formData);
        
        session()->flash('success', 'Registration completed!');
    }
}
```

### 2. Create Step Components

```php
<?php

namespace App\Livewire\Steps;

use agustinelumandong\LivewireCstepper\Components\StepComponent;

class PersonalInfoStep extends StepComponent
{
    public function stepTitle(): string
    {
        return 'Personal Information';
    }

    public function stepDescription(): string
    {
        return 'Please provide your basic personal information.';
    }

    public function validationRules(): array
    {
        return [
            'formData.personal.first_name' => ['required', 'string', 'min:2'],
            'formData.personal.last_name' => ['required', 'string', 'min:2'],
            'formData.personal.email' => ['required', 'email'],
        ];
    }

    public function render()
    {
        return view('steps.personal-info', [
            'personalData' => $this->getStepperFormData()['personal'] ?? [],
        ]);
    }
}
```

### 3. Create Step Views

```blade
{{-- resources/views/steps/personal-info.blade.php --}}
<div class="space-y-6">
    <x-input 
        label="First Name" 
        wire:model.live="formData.personal.first_name"
        placeholder="Enter your first name"
    />
    
    <x-input 
        label="Last Name" 
        wire:model.live="formData.personal.last_name"
        placeholder="Enter your last name"
    />
    
    <x-input 
        label="Email" 
        type="email"
        wire:model.live="formData.personal.email"
        placeholder="Enter your email address"
    />
</div>
```

### 4. Use in Blade Template

```blade
{{-- resources/views/livewire/user-registration-stepper.blade.php --}}
<div>
    <h1 class="text-3xl font-bold mb-8">User Registration</h1>
    
    @livewire('user-registration-stepper')
</div>
```

## Advanced Features

### Custom Navigation Logic

```php
public function canAdvanceToStep(int $targetIndex): bool
{
    // Custom navigation logic
    if ($targetIndex === 2 && !$this->hasValidPaymentMethod()) {
        return false;
    }
    
    return parent::canAdvanceToStep($targetIndex);
}
```

### Lifecycle Hooks

```php
public function beforeStepChange($from, $to): void
{
    // Save progress to database
    $this->saveProgress();
}

public function afterStepChange($from, $to): void
{
    // Track analytics
    Analytics::track('step_changed', [
        'from' => $from,
        'to' => $to,
        'user_id' => auth()->id(),
    ]);
}
```

### Conditional Steps

```php
public function defineSteps(): array
{
    $steps = [
        PersonalInfoStep::class,
        ContactInfoStep::class,
    ];
    
    // Add payment step only for premium users
    if ($this->isPremiumUser()) {
        $steps[] = PaymentInfoStep::class;
    }
    
    $steps[] = ReviewStep::class;
    
    return $steps;
}
```

## Configuration

The package provides extensive configuration options:

```php
// config/livewire-cstepper.php
return [
    'ui_framework' => 'wireui',
    'strict_validation' => true,
    'allow_step_jumping' => false,
    'progress_bar_style' => 'modern',
    'animation_enabled' => true,
    'auto_save_enabled' => false,
    // ... more options
];
```

## API Reference

### CStepper Methods

| Method | Description |
|--------|-------------|
| `advance(?int $toIndex = null)` | Move to the next step or specific step |
| `goBack(?int $toIndex = null)` | Move to the previous step or specific step |
| `jumpTo(int $index)` | Jump directly to a specific step |
| `canAdvanceToNext()` | Check if can move to next step |
| `canGoBackToPrevious()` | Check if can go back |
| `getCurrentStepComponent()` | Get the current step component instance |
| `getProgressPercentage()` | Get completion percentage |
| `getTotalSteps()` | Get total number of steps |
| `resetStepper()` | Reset stepper to first step |
| `submitStepper()` | Submit the completed stepper |

### StepComponent Methods

| Method | Description |
|--------|-------------|
| `stepTitle()` | Define step title |
| `stepDescription()` | Define step description |
| `stepIcon()` | Define step icon |
| `validationRules()` | Define validation rules |
| `canSkip()` | Whether step can be skipped |
| `isValid()` | Check if step is valid |

## Testing

Run the tests:

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email <agustinelumandong@gmail.com> instead of using the issue tracker.

## Credits

- [Cshan](https://github.com/agustinelumandong)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---

## Comparison with Other Packages

| Feature | Livewire CStepper | livewire-wizard | Other Packages |
|---------|------------------|-----------------|----------------|
| Livewire 3 Support | ✅ Full Support | ❌ Limited | ❌ Outdated |
| WireUI Integration | ✅ Built-in | ❌ Manual | ❌ None |
| Advanced Navigation | ✅ Non-linear | ❌ Linear Only | ❌ Basic |
| Lifecycle Hooks | ✅ Comprehensive | ❌ Limited | ❌ Basic |
| Validation Strategy | ✅ Multi-level | ❌ Basic | ❌ Basic |
| Modern UI | ✅ WireUI | ❌ Bootstrap | ❌ Custom |
| Documentation | ✅ Comprehensive | ❌ Basic | ❌ Limited |

This package provides a **legally distinct** and **functionally enhanced** alternative to existing wizard packages, with modern architecture and advanced features designed specifically for Livewire 3.