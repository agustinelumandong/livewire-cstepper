<?php

namespace agustinelumandong\LivewireCstepper\Traits;

use agustinelumandong\LivewireCstepper\Contracts\StepperContract;

trait InteractsWithStepper
{
    protected StepperContract $stepper;

    public function setStepper(StepperContract $stepper): static
    {
        $this->stepper = $stepper;
        return $this;
    }

    public function getStepper(): StepperContract
    {
        return $this->stepper;
    }

    public function getStepperFormData(): array
    {
        return $this->stepper->getFormData();
    }

    public function setStepperFormData(array $data): static
    {
        $this->stepper->setFormData($data);
        return $this;
    }

    public function updateStepperFormData(array $data): static
    {
        $this->stepper->updateFormData($data);
        return $this;
    }

    public function appendStepperFormData($key, $value = null, $default = null): static
    {
        $this->stepper->appendFormData($key, $value, $default);
        return $this;
    }
}