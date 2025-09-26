<div class="livewire-cstepper-container">
    <!-- WireUI Card Wrapper -->
    <x-card class="stepper-card">
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
                    <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">
                        {{ $currentStep->stepTitle() }}
                    </h2>
                    @if($currentStep->stepDescription())
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            {{ $currentStep->stepDescription() }}
                        </p>
                    @endif
                    
                    <!-- Step Body with Error Boundary -->
                    <div class="step-body" wire:loading.class="opacity-50">
                        @if($errors->any())
                            <x-alert title="Please correct the following errors:" negative class="mb-6">
                                <ul class="list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </x-alert>
                        @endif
                        
                        {!! $currentStep->toHtml() !!}
                    </div>
                </div>
            @endif
        </div>

        <!-- Navigation Controls -->
        <x-slot name="footer" class="step-navigation">
            <div class="flex justify-between items-center w-full">
                <div class="flex space-x-4">
                    @if($canGoBack && !$this->isFirstStep())
                        <x-button 
                            wire:click="goBack" 
                            secondary
                            icon="arrow-left"
                            wire:loading.attr="disabled"
                        >
                            Back
                        </x-button>
                    @endif
                </div>

                <!-- Step Counter -->
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Step {{ $this->currentIndex + 1 }} of {{ $this->getTotalSteps() }}
                </div>

                <div class="flex space-x-4">
                    @if(!$this->isLastStep())
                        <x-button 
                            wire:click="advance" 
                            :disabled="!$canAdvance"
                            primary
                            icon="arrow-right"
                            right-icon
                            wire:loading.attr="disabled"
                            wire:target="advance"
                        >
                            <span wire:loading.remove wire:target="advance">Next</span>
                            <span wire:loading wire:target="advance">Processing...</span>
                        </x-button>
                    @else
                        <x-button 
                            wire:click="submitStepper" 
                            positive
                            icon="check"
                            wire:loading.attr="disabled"
                            wire:target="submitStepper"
                        >
                            <span wire:loading.remove wire:target="submitStepper">Complete</span>
                            <span wire:loading wire:target="submitStepper">Submitting...</span>
                        </x-button>
                    @endif
                </div>
            </div>
        </x-slot>
    </x-card>
</div> 