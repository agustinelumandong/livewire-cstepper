<div class="livewire-cstepper-container">
    <!-- Progress Bar -->
    <div class="mb-8">
        @include('livewire-cstepper::components.progress-bar', [
            'currentIndex' => $this->currentIndex,
            'totalSteps' => $this->getTotalSteps(),
            'percentage' => $this->getProgressPercentage()
        ])
    </div>

    <!-- Step Content -->
    <div class="step-content">
        @if($currentStep)
            <div class="current-step">
                <h2 class="text-2xl font-bold mb-4">{{ $currentStep->stepTitle() }}</h2>
                @if($currentStep->stepDescription())
                    <p class="text-gray-600 mb-6">{{ $currentStep->stepDescription() }}</p>
                @endif
                
                <div class="step-body">
                    {!! $currentStep->toHtml() !!}
                </div>
            </div>
        @endif
    </div>

    <!-- Navigation Controls -->
    <div class="mt-8 flex justify-between">
        <div class="flex space-x-4">
            @if($canGoBack && !$this->isFirstStep())
                <x-button 
                    wire:click="goBack" 
                    variant="secondary"
                    icon="arrow-left"
                >
                    Back
                </x-button>
            @endif
        </div>

        <div class="flex space-x-4">
            @if(!$this->isLastStep())
                <x-button 
                    wire:click="advance" 
                    :disabled="!$canAdvance"
                    icon="arrow-right"
                    right-icon
                >
                    Next
                </x-button>
            @else
                <x-button 
                    wire:click="submitStepper" 
                    variant="primary"
                    icon="check"
                >
                    Complete
                </x-button>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.livewire-cstepper-container {
    @apply max-w-4xl mx-auto p-6;
}

.step-content {
    @apply min-h-96;
}

@if(config('livewire-cstepper.animation_enabled', true))
.current-step {
    animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
@endif
</style>
@endpush