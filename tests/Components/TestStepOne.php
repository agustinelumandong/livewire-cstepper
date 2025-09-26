<?php

namespace agustinelumandong\LivewireCstepper\Tests\Components;

use agustinelumandong\LivewireCstepper\Components\StepComponent;
use Illuminate\Contracts\View\View;

class TestStepOne extends StepComponent
{
    public function rules(): array
    {
        return [
            'formData.step1.name' => 'required|string|max:255',
            'formData.step1.email' => 'required|email',
        ];
    }

    public function render(): View
    {
        return view('test-step-one', [
            'formData' => $this->getStepper()->getFormData()
        ]);
    }

    public function isValid(): bool
    {
        $stepper = $this->getStepper();
        $validator = validator(
            ['formData' => $stepper->getFormData()],
            $this->rules()
        );

        return !$validator->fails();
    }
}