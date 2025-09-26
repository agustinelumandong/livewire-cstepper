<div class="test-stepper">
    <div class="stepper-progress">
        Step {{ $currentStep ? $currentStep->getSequence() + 1 : 1 }} of {{ count($steps) }}
    </div>
    
    <div class="stepper-content">
        @if($currentStep)
            {!! $currentStep->render() !!}
        @endif
    </div>
    
    <div class="stepper-navigation">
        @if($canGoBack)
            <button wire:click="goBack" class="btn-previous">Previous</button>
        @endif
        
        @if($canAdvance)
            <button wire:click="advance" class="btn-next">Next</button>
        @else
            <button wire:click="submit" class="btn-submit">Submit</button>
        @endif
    </div>
</div>