<?php

namespace agustinelumandong\LivewireCstepper\Contracts;

use Illuminate\Database\Eloquent\Model;

interface StepperContract
{
    /**
     * Get the stepper form data
     */
    public function getFormData(): array;

    /**
     * Set the stepper form data
     */
    public function setFormData(array $data): static;

    /**
     * Update the stepper form data
     */
    public function updateFormData(array $data): static;

    /**
     * Append a value to the stepper form data
     */
    public function appendFormData($key, $value = null, $default = null): static;

    /**
     * Get the associated model
     */
    public function getModel(): ?Model;
}