<x-card title="WireUI CStepper Test" padding="xl">
    <div class="space-y-8">
        <!-- Test Controls -->
        <div class="flex flex-wrap gap-4">
            <x-button 
                wire:click="fillTestData"
                outline
                icon="beaker"
                label="Fill Test Data"
            />
            <x-button 
                wire:click="resetFormData"
                outline
                negative
                icon="refresh"
                label="Reset Form"
            />
            <x-button 
                wire:click="$refresh"
                outline
                icon="arrow-path"
                label="Refresh"
            />
        </div>

        <!-- Progress Information -->
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="font-medium text-gray-700">Current Step:</span>
                    <span class="ml-2 text-gray-900">{{ $currentIndex + 1 }} of {{ count($this->steps()) }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Completion:</span>
                    <span class="ml-2 text-gray-900">{{ $this->getCompletionPercentage() }}%</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Valid:</span>
                    <span class="ml-2 text-gray-900">{{ $this->isCurrentStepValid() ? 'Yes' : 'No' }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Total Fields:</span>
                    <span class="ml-2 text-gray-900">11</span>
                </div>
            </div>
        </div>

        <!-- Form Summary (for debugging) -->
        <div x-data="{ showData: false }" class="space-y-2">
            <x-button 
                x-on:click="showData = !showData"
                outline
                xs
                :label="showData ? 'Hide Form Data' : 'Show Form Data'"
            />
            
            <div x-show="showData" x-collapse class="bg-gray-100 rounded p-4 text-sm">
                <pre>{{ json_encode($formSummary, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    </div>

    <!-- Include the main stepper component -->
    @include('resources.views.stepper')
</x-card>

@push('scripts')
<script>
    // Listen for WireUI notifications and log them
    document.addEventListener('DOMContentLoaded', function() {
        Livewire.on('wireui:notification', function(data) {
            console.log('WireUI Notification:', data);
        });

        // Log step changes
        Livewire.on('step-changed', function(data) {
            console.log('Step Changed:', data);
        });

        // Log completion
        Livewire.on('stepper-completed', function(data) {
            console.log('Stepper Completed:', data);
        });
    });
</script>
@endpush