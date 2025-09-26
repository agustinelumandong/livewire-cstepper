<?php

namespace agustinelumandong\LivewireCstepper\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

trait ManagesFormData
{
    public array $formData = [];

    public function getFormData(): array
    {
        return $this->formData;
    }

    public function setFormData(array $data): static
    {
        $this->ensureSerializableData($data);
        $this->formData = $data;
        return $this;
    }

    public function updateFormData($data): static
    {
        // Handle both array and single key-value updates
        if (is_array($data)) {
            $this->ensureSerializableData($data);
            $this->formData = array_merge($this->formData, $data);
        } else {
            // For single key updates, expect arguments: ($key, $value)
            $args = func_get_args();
            if (count($args) >= 2) {
                $key = $args[0];
                $value = $args[1];
                $this->ensureSerializableData([$key => $value]);
                data_set($this->formData, $key, $value);
            }
        }
        return $this;
    }

    public function appendFormData($key, $value = null, $default = null): static
    {
        // Ensure key is valid before validation
        if (!is_string($key) && !is_int($key)) {
            throw new \InvalidArgumentException("Form data key must be a string or integer, " . gettype($key) . " given.");
        }
        
        if ($value !== null) {
            $this->ensureSerializableData([$key => $value]);
        }
        data_set($this->formData, $key, $value, $default);
        return $this;
    }

    /**
     * Validate that form data is serializable for Livewire 3
     * 
     * @param array $data
     * @throws \InvalidArgumentException
     */
    protected function ensureSerializableData(array $data): void
    {
        if (!config('livewire-cstepper.strict_validation', true)) {
            return;
        }

        foreach ($data as $key => $value) {
            if ($value instanceof Model) {
                throw new \InvalidArgumentException(
                    "Cannot store Eloquent Model [{$key}] in stepper form data. " .
                    "Consider storing the model's ID instead and re-hydrating when needed."
                );
            }

            if ($value instanceof Collection) {
                throw new \InvalidArgumentException(
                    "Cannot store Collection [{$key}] in stepper form data. " .
                    "Convert to array using ->toArray() or store as primitive data types."
                );
            }

            if (is_object($value) && !method_exists($value, '__toString') && !$value instanceof \JsonSerializable) {
                throw new \InvalidArgumentException(
                    "Cannot store non-serializable object [{$key}] of type [" . get_class($value) . "] in stepper form data. " .
                    "Ensure the object implements JsonSerializable or convert to primitive data types."
                );
            }

            if (is_resource($value)) {
                throw new \InvalidArgumentException(
                    "Cannot store resource [{$key}] in stepper form data. " .
                    "Convert to a serializable format first."
                );
            }

            // Recursively check nested arrays
            if (is_array($value)) {
                $this->ensureSerializableData($value);
            }
        }
    }

    /**
     * Clear all form data
     */
    public function clearFormData(): static
    {
        $this->formData = [];
        return $this;
    }

    /**
     * Get a specific value from form data
     */
    public function getFormDataValue($key, $default = null)
    {
        return data_get($this->formData, $key, $default);
    }

    /**
     * Check if form data has a specific key
     */
    public function hasFormDataKey($key): bool
    {
        return data_get($this->formData, $key) !== null;
    }

    /**
     * Remove a key from form data
     */
    public function forgetFormDataKey($key): static
    {
        data_forget($this->formData, $key);
        return $this;
    }

    /**
     * Get form data as a collection
     */
    public function getFormDataCollection(): Collection
    {
        return collect($this->formData);
    }
}