<?php

require_once 'vendor/autoload.php';

use agustinelumandong\LivewireCstepper\Tests\Components\TestStepper;
use agustinelumandong\LivewireCstepper\Tests\Components\TestStepOne;

// Create a test stepper instance
$stepper = new TestStepper();
$stepper->formData = [
    'step1' => [
        'name' => 'John Doe',
        'email' => 'john@example.com'
    ]
];

// Create a step instance
$step = new TestStepOne($stepper);

// Test if validation works
$isValid = $step->isValid();
echo "Step is valid: " . ($isValid ? 'true' : 'false') . "\n";

// Test the rules
$rules = $step->rules();
echo "Rules: " . json_encode($rules) . "\n";

// Test the form data
echo "Form data: " . json_encode($stepper->getFormData()) . "\n";

// Test validation manually
$validator = validator($stepper->getFormData(), $rules);
echo "Validation passes: " . ($validator->passes() ? 'true' : 'false') . "\n";
echo "Validation errors: " . json_encode($validator->errors()->toArray()) . "\n";