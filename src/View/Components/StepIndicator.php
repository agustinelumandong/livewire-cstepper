<?php

namespace agustinelumandong\LivewireCstepper\View\Components;

use Illuminate\View\Component;

class StepIndicator extends Component
{
    public int $index;
    public string $status;
    public ?string $title;
    public ?string $description;
    public ?string $icon;

    public function __construct(
        int $index,
        string $status = 'pending',
        ?string $title = null,
        ?string $description = null,
        ?string $icon = null
    ) {
        $this->index = $index;
        $this->status = $status;
        $this->title = $title ?? "Step " . ($index + 1);
        $this->description = $description;
        $this->icon = $icon ?? $this->getDefaultIcon();
    }

    public function render()
    {
        return view('livewire-cstepper::components.step-indicator');
    }

    public function getDefaultIcon(): string
    {
        return match ($this->status) {
            'completed' => config('livewire-cstepper.icons.completed', 'check-circle'),
            'current' => config('livewire-cstepper.icons.current', 'arrow-right'),
            'error' => config('livewire-cstepper.icons.error', 'exclamation-circle'),
            'pending' => config('livewire-cstepper.icons.pending', 'clock'),
            default => 'circle',
        };
    }

    public function getStatusClasses(): string
    {
        return match ($this->status) {
            'completed' => 'bg-green-500 border-green-500 text-white',
            'current' => 'bg-blue-500 border-blue-500 text-white ring-4 ring-blue-200',
            'error' => 'bg-red-500 border-red-500 text-white',
            'pending' => 'bg-gray-100 border-gray-300 text-gray-500',
            default => 'bg-gray-100 border-gray-300 text-gray-500',
        };
    }
}