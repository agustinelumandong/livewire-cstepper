<div class="space-y-6">
    <x-alert title="Review & Confirm" success>
        <x-slot name="slot">
            Please review all information carefully before submitting. You can go back to make changes if needed.
        </x-slot>
    </x-alert>

    <!-- Personal Information Review -->
    <x-card title="Personal Information" padding="lg">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">Name:</span>
                    <span class="text-gray-900">
                        {{ trim($firstName . ' ' . $lastName) ?: 'Not provided' }}
                    </span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">Email:</span>
                    <span class="text-gray-900">{{ $email ?: 'Not provided' }}</span>
                </div>
            </div>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">Phone:</span>
                    <span class="text-gray-900">{{ $phone ?: 'Not provided' }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">Date of Birth:</span>
                    <span class="text-gray-900">{{ $dateOfBirth ?: 'Not provided' }}</span>
                </div>
            </div>
        </div>
    </x-card>

    <!-- Address Information Review -->
    <x-card title="Address Information" padding="lg">
        @if($address || $city || $state || $zipCode || $country)
            <address class="not-italic space-y-1">
                @if($address)
                    <div class="font-medium">{{ $address }}</div>
                @endif
                
                <div>
                    @if($city){{ $city }}@endif@if($city && $state), @endif@if($state){{ $state }} @endif@if($zipCode){{ $zipCode }}@endif
                </div>
                
                @if($country)
                    <div class="text-gray-600">
                        @switch($country)
                            @case('US') United States @break
                            @case('CA') Canada @break
                            @case('UK') United Kingdom @break
                            @case('AU') Australia @break
                            @case('DE') Germany @break
                            @case('FR') France @break
                            @case('JP') Japan @break
                            @default {{ $country }}
                        @endswitch
                    </div>
                @endif
            </address>
        @else
            <p class="text-gray-500 italic">No address information provided</p>
        @endif
    </x-card>

    <!-- Form Completion Status -->
    <x-card title="Form Completion Status" padding="lg">
        <div class="space-y-4">
            <!-- Overall Progress -->
            <div class="flex items-center justify-between">
                <span class="font-medium text-gray-700">Overall Completion:</span>
                <span class="text-lg font-bold text-blue-600">{{ $this->getCompletionPercentage() }}%</span>
            </div>
            
            <div class="bg-gray-200 rounded-full h-3 overflow-hidden">
                <div 
                    class="bg-blue-600 h-3 rounded-full transition-all duration-500"
                    style="width: {{ $this->getCompletionPercentage() }}%"
                ></div>
            </div>

            <!-- Field Status -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                <div class="flex items-center space-x-2">
                    <x-badge 
                        :label="$firstName ? 'Complete' : 'Missing'"
                        :positive="!!$firstName"
                        :negative="!$firstName"
                        xs
                    />
                    <span class="text-sm text-gray-600">First Name</span>
                </div>
                
                <div class="flex items-center space-x-2">
                    <x-badge 
                        :label="$lastName ? 'Complete' : 'Missing'"
                        :positive="!!$lastName"
                        :negative="!$lastName"
                        xs
                    />
                    <span class="text-sm text-gray-600">Last Name</span>
                </div>
                
                <div class="flex items-center space-x-2">
                    <x-badge 
                        :label="($email && filter_var($email, FILTER_VALIDATE_EMAIL)) ? 'Valid' : 'Invalid'"
                        :positive="$email && filter_var($email, FILTER_VALIDATE_EMAIL)"
                        :negative="!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)"
                        xs
                    />
                    <span class="text-sm text-gray-600">Email</span>
                </div>
                
                <div class="flex items-center space-x-2">
                    <x-badge 
                        :label="$address ? 'Complete' : 'Missing'"
                        :positive="!!$address"
                        :negative="!$address"
                        xs
                    />
                    <span class="text-sm text-gray-600">Address</span>
                </div>
            </div>
        </div>
    </x-card>

    <!-- Terms and Conditions -->
    <x-card padding="lg">
        <div class="space-y-4">
            <x-checkbox
                wire:model.live="termsAccepted"
                id="terms"
                label="I agree to the terms and conditions"
                description="By checking this box, you agree to our terms of service and privacy policy."
            />
            
            @if(!$termsAccepted)
                <x-alert title="Agreement Required" warning>
                    <x-slot name="slot">
                        You must accept the terms and conditions to proceed with the submission.
                    </x-slot>
                </x-alert>
            @endif
        </div>
    </x-card>

    <!-- Submission Status -->
    @if($termsAccepted)
        <x-alert title="Ready to Submit!" success>
            <x-slot name="slot">
                All required information has been provided and terms accepted. You can now submit your form.
            </x-slot>
        </x-alert>
    @else
        <x-alert title="Not Ready" info>
            <x-slot name="slot">
                Please accept the terms and conditions to enable form submission.
            </x-slot>
        </x-alert>
    @endif
</div>