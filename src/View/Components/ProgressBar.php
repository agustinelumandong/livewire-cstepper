<?php

namespace agustinelumandong\LivewireCstepper\View\Components;

use Illuminate\View\Component;

class ProgressBar extends Component
{
    public int $currentIndex;
    public int $totalSteps;
    public float $percentage;
    public string $style;

    public function __construct(
        int $currentIndex = 0,
        int $totalSteps = 1,
        ?float $percentage = null,
        string $style = null
    ) {
        $this->currentIndex = $currentIndex;
        $this->totalSteps = $totalSteps;
        $this->percentage = $percentage ?? ($totalSteps > 1 ? ($currentIndex / ($totalSteps - 1)) * 100 : 0);
        $this->style = $style ?? config('livewire-cstepper.progress_bar_style', 'modern');
    }

    public function render()
    {
        return view('livewire-cstepper::components.progress-bar');
    }

    public function getStepStatus(int $index): string
    {
        if ($index < $this->currentIndex) {
            return 'completed';
        } elseif ($index === $this->currentIndex) {
            return 'current';
        } else {
            return 'pending';
        }
    }

    public function getStepIcon(int $index): string
    {
        $status = $this->getStepStatus($index);
        
        return match ($status) {
            'completed' => config('livewire-cstepper.icons.completed', 'check-circle'),
            'current' => config('livewire-cstepper.icons.current', 'arrow-right'),
            'pending' => config('livewire-cstepper.icons.pending', 'clock'),
            default => 'circle',
        };
    }
}