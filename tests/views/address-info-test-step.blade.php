<div class="space-y-6">
    <x-alert title="Address Information" info>
        <x-slot name="slot">
            Enter your address details for shipping and billing purposes. All fields are required.
        </x-slot>
    </x-alert>

    <div class="space-y-4">
        <x-input
            wire:model.live="address"
            label="Street Address"
            placeholder="123 Main Street"
            icon="home"
            required
        />

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <x-input
                wire:model.live="city"
                label="City"
                placeholder="New York"
                icon="building-office"
                required
            />

            <x-select
                wire:model.live="state"
                label="State/Province"
                placeholder="Select state"
                icon="map"
                required
            >
                <x-select.option label="Alabama" value="AL" />
                <x-select.option label="Alaska" value="AK" />
                <x-select.option label="Arizona" value="AZ" />
                <x-select.option label="California" value="CA" />
                <x-select.option label="Colorado" value="CO" />
                <x-select.option label="Florida" value="FL" />
                <x-select.option label="Georgia" value="GA" />
                <x-select.option label="Illinois" value="IL" />
                <x-select.option label="New York" value="NY" />
                <x-select.option label="Texas" value="TX" />
                <x-select.option label="Washington" value="WA" />
            </x-select>

            <x-input
                wire:model.live="zipCode"
                label="ZIP/Postal Code"
                placeholder="12345"
                icon="map-pin"
                required
            />
        </div>

        <x-select
            wire:model.live="country"
            label="Country"
            placeholder="Select country"
            icon="globe-americas"
            required
        >
            <x-select.option label="United States" value="US" />
            <x-select.option label="Canada" value="CA" />
            <x-select.option label="United Kingdom" value="UK" />
            <x-select.option label="Australia" value="AU" />
            <x-select.option label="Germany" value="DE" />
            <x-select.option label="France" value="FR" />
            <x-select.option label="Japan" value="JP" />
            <x-select.option label="Other" value="OTHER" />
        </x-select>
    </div>

    <!-- Address Preview -->
    @if($address || $city || $state || $zipCode || $country)
        <x-card title="Address Preview" padding="sm">
            <div class="space-y-1 text-sm">
                @if($address)
                    <div class="font-medium">{{ $address }}</div>
                @endif
                
                <div class="flex space-x-1">
                    @if($city)
                        <span>{{ $city }}</span>
                    @endif
                    @if($city && $state)
                        <span>,</span>
                    @endif
                    @if($state)
                        <span>{{ $state }}</span>
                    @endif
                    @if($zipCode)
                        <span>{{ $zipCode }}</span>
                    @endif
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
            </div>
        </x-card>
    @endif

    <!-- Progress indicator for this step -->
    <div class="p-4 bg-green-50 rounded-lg">
        <div class="flex items-center justify-between text-sm">
            <span class="font-medium text-green-900">Step Progress</span>
            <span class="text-green-700">
                {{ collect([$address, $city, $state, $zipCode, $country])->filter()->count() }} / 5 fields completed
            </span>
        </div>
        <div class="mt-2 bg-green-200 rounded-full h-2 overflow-hidden">
            <div 
                class="bg-green-600 h-2 rounded-full transition-all duration-300"
                style="width: {{ (collect([$address, $city, $state, $zipCode, $country])->filter()->count() / 5) * 100 }}%"
            ></div>
        </div>
    </div>

    <!-- Field Status Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="flex items-center space-x-2">
            <x-icon 
                :name="$address ? 'check-circle' : 'x-circle'" 
                :class="$address ? 'text-green-500' : 'text-gray-300'"
                size="sm"
            />
            <span class="text-sm text-gray-600">Address</span>
        </div>
        
        <div class="flex items-center space-x-2">
            <x-icon 
                :name="$city ? 'check-circle' : 'x-circle'" 
                :class="$city ? 'text-green-500' : 'text-gray-300'"
                size="sm"
            />
            <span class="text-sm text-gray-600">City</span>
        </div>
        
        <div class="flex items-center space-x-2">
            <x-icon 
                :name="$state ? 'check-circle' : 'x-circle'" 
                :class="$state ? 'text-green-500' : 'text-gray-300'"
                size="sm"
            />
            <span class="text-sm text-gray-600">State</span>
        </div>
        
        <div class="flex items-center space-x-2">
            <x-icon 
                :name="$zipCode ? 'check-circle' : 'x-circle'" 
                :class="$zipCode ? 'text-green-500' : 'text-gray-300'"
                size="sm"
            />
            <span class="text-sm text-gray-600">ZIP Code</span>
        </div>
        
        <div class="flex items-center space-x-2">
            <x-icon 
                :name="$country ? 'check-circle' : 'x-circle'" 
                :class="$country ? 'text-green-500' : 'text-gray-300'"
                size="sm"
            />
            <span class="text-sm text-gray-600">Country</span>
        </div>
    </div>
</div>