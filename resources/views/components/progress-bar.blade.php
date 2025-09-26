<div class="progress-bar-container">
    <!-- Step Indicators -->
    <div class="flex items-center justify-between mb-4">
        @for($i = 0; $i < $totalSteps; $i++)
            <div class="flex items-center {{ $i < $totalSteps - 1 ? 'flex-1' : '' }}">
                <!-- Step Circle with WireUI Badge -->
                @if($i < $currentIndex)
                    <x-badge green lg rounded class="step-indicator completed">
                        <x-icon name="check" class="w-4 h-4" />
                    </x-badge>
                @elseif($i === $currentIndex)
                    <x-badge primary lg rounded class="step-indicator current ring-4 ring-primary-200">
                        {{ $i + 1 }}
                    </x-badge>
                @else
                    <x-badge gray lg rounded class="step-indicator pending">
                        {{ $i + 1 }}
                    </x-badge>
                @endif

                <!-- Connecting Line -->
                @if($i < $totalSteps - 1)
                    <div class="flex-1 h-0.5 mx-4 transition-colors duration-300
                        {{ $i < $currentIndex ? 'bg-primary-500' : 'bg-gray-200 dark:bg-gray-700' }}
                    "></div>
                @endif
            </div>
        @endfor
    </div>

    <!-- Progress Percentage -->
    @if(config('livewire-cstepper.progress_bar_style') === 'modern')
        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
            <div class="bg-primary-500 h-2 rounded-full transition-all duration-300" 
                 style="width: {{ $percentage }}%"></div>
        </div>
        <div class="text-sm text-gray-600 text-center">
            Step {{ $currentIndex + 1 }} of {{ $totalSteps }} 
            ({{ number_format($percentage, 0) }}% complete)
        </div>
    @endif
</div>

@push('styles')
<style>
.step-indicator {
    @apply w-10 h-10 rounded-full flex items-center justify-center border-2 transition-all duration-200;
}

.step-indicator.completed {
    @apply bg-primary-500 border-primary-500 text-white;
}

.step-indicator.current {
    @apply bg-primary-500 border-primary-500 text-white ring-4 ring-primary-200;
}

.step-indicator.pending {
    @apply bg-white border-gray-300 text-gray-500;
}

@if(config('livewire-cstepper.animation_enabled', true))
.step-indicator {
    transition: all 0.2s ease-in-out;
}

.step-indicator.completed,
.step-indicator.current {
    animation: pulse 0.5s ease-out;
}
@endif
</style>
@endpush