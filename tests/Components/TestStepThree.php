<?php

namespace agustinelumandong\LivewireCstepper\Tests\Components;

use agustinelumandong\LivewireCstepper\Components\StepComponent;
use Illuminate\Contracts\View\View;

class TestStepThree extends StepComponent
{
    public function rules(): array
    {
        return [
            'formData.step3.preferences' => 'required|array|min:1',
            'formData.step3.terms_accepted' => 'required|boolean|accepted',
        ];
    }

    public function render(): View
    {
        return view('test-step-three', [
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