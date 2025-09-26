<?php

namespace agustinelumandong\LivewireCstepper\Tests\Components;

use agustinelumandong\LivewireCstepper\Components\StepComponent;
use Illuminate\Contracts\View\View;

class TestStepTwo extends StepComponent
{
    public function rules(): array
    {
        return [
            'formData.step2.phone' => 'required|string|max:20',
            'formData.step2.address' => 'required|string|max:500',
        ];
    }

    public function render(): View
    {
        return view('test-step-two', [
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