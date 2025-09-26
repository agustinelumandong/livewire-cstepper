# Livewire CStepper

A modern, feature-rich Laravel Livewire 3 package for creating multi-step forms with enhanced UX and **full WireUI integration**.

## Features

✨ **Modern Architecture**: Clean, maintainable code with proper separation of concerns  
🎯 **Livewire 3 Optimized**: Built specifically for Livewire 3 with proper serialization handling  
🎨 **WireUI Integration**: Beautiful, accessible components with cards, inputs, and notifications  
🔄 **Flexible Navigation**: Support for linear and non-linear step progression  
✅ **Advanced Validation**: Step-level and cross-step validation with real-time feedback  
🎪 **Lifecycle Hooks**: Comprehensive event system for customization  
📱 **Responsive Design**: Mobile-friendly progress indicators and controls  
⚡ **Performance Optimized**: Efficient state management and caching  
🎭 **Enhanced UI**: Progress bars, animations, and interactive feedback  

## Requirements

- PHP 8.1+
- Laravel 12.0+
- Livewire 3.0+
- WireUI 2.0+ (automatically installed)

## Installation

### 1. Install the Package

```bash
composer require agustinelumandong/livewire-cstepper
```

### 2. Install WireUI (if not already installed)

```bash
# Install WireUI
composer require wireui/wireui

# Install frontend dependencies
npm install alpinejs @alpinejs/focus @tailwindcss/forms @tailwindcss/typography

# Update your tailwind.config.js
# Add WireUI preset and CStepper content paths
```

### 3. Configure Tailwind CSS

Update your `tailwind.config.js`:

```javascript
const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
    presets: [
        require('./vendor/wireui/wireui/tailwind.config.js')
    ],
    content: [
        './app/**/*.php',
        './resources/**/*.html',
        './resources/**/*.js',
        './resources/**/*.jsx',
        './resources/**/*.ts',
        './resources/**/*.tsx',
        './resources/**/*.php',
        './resources/**/*.vue',
        './resources/**/*.twig',
        './vendor/wireui/wireui/src/*.php',
        './vendor/wireui/wireui/ts/**/*.ts',
        './vendor/wireui/wireui/src/WireUi/**/*.php',
        './vendor/wireui/wireui/src/Components/**/*.php',
        './vendor/agustinelumandong/livewire-cstepper/resources/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter var', ...defaultTheme.fontFamily.sans],
            },
        },
    },
}
```

### 4. Update Your CSS

Add to your `resources/css/app.css`:

```css
@import 'tailwindcss/base';
@import 'tailwindcss/components';
@import 'tailwindcss/utilities';
@import '../../vendor/wireui/wireui/dist/wireui.css';

/* CStepper enhancements */
.stepper-card {
    @apply bg-white rounded-lg shadow-sm border border-gray-200;
}

.stepper-progress {
    @apply bg-gradient-to-r from-primary-500 to-secondary-500;
}
```

### 5. Configure Laravel

Add to your `resources/js/app.js`:

```javascript
import './bootstrap';
import { wireui } from '../../vendor/wireui/wireui/dist/wireui.esm';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

Alpine.plugin(focus);
wireui(Alpine);
Alpine.start();

window.Alpine = Alpine;
```

### 6. Publish Configuration

```bash
# Publish configuration
php artisan vendor:publish --tag="livewire-cstepper-config"

# Publish views (optional)
php artisan vendor:publish --tag="livewire-cstepper-views"

# Publish WireUI assets
php artisan vendor:publish --tag="wireui.config"
```

### 7. Compile Assets

```bash
npm run dev
# or for production
npm run build
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
        icon="user"
        corner-hint="Required"
    />
    
    <x-input 
        label="Last Name" 
        wire:model.live="formData.personal.last_name"
        placeholder="Enter your last name"
        icon="user"
        corner-hint="Required"
    />
    
    <x-input 
        label="Email" 
        type="email"
        wire:model.live="formData.personal.email"
        placeholder="Enter your email address"
        icon="mail"
        corner-hint="We'll never share your email"
    />
</div>
```

### 4. Enhanced WireUI Step Example

For more advanced forms with full WireUI integration:

```blade
{{-- Enhanced step with progress tracking --}}
<div class="space-y-6" x-data="{ completionPercentage: 0 }">
    {{-- Progress Indicator --}}
    <div class="bg-gradient-to-r from-primary-50 to-secondary-50 rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-semibold text-primary-800">Personal Information</h3>
            <span class="text-sm font-medium" x-text="`${completionPercentage}% Complete`"></span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-gradient-to-r from-primary-500 to-secondary-500 h-2 rounded-full transition-all duration-300"
                 :style="`width: ${completionPercentage}%`"></div>
        </div>
    </div>

    {{-- Error Summary --}}
    @if ($errors->any())
        <x-alert title="Please correct the following errors:" icon="exclamation-triangle">
            <ul class="list-disc list-inside space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    {{-- Enhanced form with WireUI components --}}
    <x-card title="Basic Information" icon="user" class="shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-input 
                label="First Name *" 
                placeholder="Enter your first name"
                wire:model.live="formData.first_name"
                icon="user"
                corner-hint="Required"
                :error="$errors->first('formData.first_name')"
            />
            
            <x-select 
                label="Country *" 
                placeholder="Select your country"
                wire:model.live="formData.country"
                icon="flag"
                :options="[
                    ['label' => 'United States', 'value' => 'US'],
                    ['label' => 'Canada', 'value' => 'CA'],
                    ['label' => 'United Kingdom', 'value' => 'GB']
                ]"
                :error="$errors->first('formData.country')"
            />
        </div>
    </x-card>
</div>
```

### 5. Use in Blade Template

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