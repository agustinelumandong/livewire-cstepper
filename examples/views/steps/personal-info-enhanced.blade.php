{{-- Enhanced Personal Information Step with Full WireUI Integration --}}
<div class="space-y-6" x-data="{ completionPercentage: 0 }" 
     x-init="
        $watch('$wire.formData', () => {
            // Calculate completion percentage
            let filled = 0;
            let total = 8; // Total required fields
            
            if ($wire.formData.first_name) filled++;
            if ($wire.formData.last_name) filled++;
            if ($wire.formData.email) filled++;
            if ($wire.formData.phone) filled++;
            if ($wire.formData.birth_date) filled++;
            if ($wire.formData.gender) filled++;
            if ($wire.formData.country) filled++;
            if ($wire.formData.terms_accepted) filled++;
            
            completionPercentage = Math.round((filled / total) * 100);
        })
     ">
    
    {{-- Progress Indicator --}}
    <div class="bg-gradient-to-r from-primary-50 to-secondary-50 rounded-lg p-4 border border-primary-200">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-semibold text-primary-800">Personal Information</h3>
            <span class="text-sm font-medium text-primary-600" x-text="`${completionPercentage}% Complete`"></span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-gradient-to-r from-primary-500 to-secondary-500 h-2 rounded-full transition-all duration-300 ease-out"
                 :style="`width: ${completionPercentage}%`"></div>
        </div>
    </div>

    {{-- Error Summary --}}
    @if ($errors->any())
        <x-alert title="Please correct the following errors:" icon="exclamation-triangle" 
                 class="border-red-200 bg-red-50">
            <ul class="list-disc list-inside space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    {{-- Basic Information Section --}}
    <x-card title="Basic Information" icon="user" class="shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-input 
                label="First Name *" 
                placeholder="Enter your first name"
                wire:model.live="formData.first_name"
                icon="user"
                corner-hint="Required"
                :error="$errors->first('formData.first_name')"
            />
            
            <x-input 
                label="Last Name *" 
                placeholder="Enter your last name"
                wire:model.live="formData.last_name"
                icon="user"
                corner-hint="Required"
                :error="$errors->first('formData.last_name')"
            />
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <x-input 
                label="Email Address *" 
                placeholder="your.email@example.com"
                wire:model.live="formData.email"
                type="email"
                icon="mail"
                corner-hint="We'll never share your email"
                :error="$errors->first('formData.email')"
            />
            
            <x-input 
                label="Phone Number *" 
                placeholder="(555) 123-4567"
                wire:model.live="formData.phone"
                icon="phone"
                corner-hint="Required"
                :error="$errors->first('formData.phone')"
            />
        </div>
    </x-card>

    {{-- Personal Details Section --}}
    <x-card title="Personal Details" icon="identification" class="shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-input 
                label="Date of Birth *" 
                type="date"
                wire:model.live="formData.birth_date"
                icon="calendar"
                max="{{ now()->subYears(13)->format('Y-m-d') }}"
                corner-hint="Must be 13+ years old"
                :error="$errors->first('formData.birth_date')"
            />
            
            <div>
                <x-label for="gender" class="mb-2">Gender *</x-label>
                <div class="space-y-2">
                    <x-radio id="gender_male" label="Male" value="male" wire:model.live="formData.gender" />
                    <x-radio id="gender_female" label="Female" value="female" wire:model.live="formData.gender" />
                    <x-radio id="gender_non_binary" label="Non-binary" value="non_binary" wire:model.live="formData.gender" />
                    <x-radio id="gender_prefer_not_to_say" label="Prefer not to say" value="prefer_not_to_say" wire:model.live="formData.gender" />
                </div>
                @error('formData.gender')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </x-card>

    {{-- Location & Preferences Section --}}
    <x-card title="Location & Preferences" icon="globe" class="shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-select 
                label="Country *" 
                placeholder="Select your country"
                wire:model.live="formData.country"
                icon="flag"
                :options="[
                    ['label' => 'United States', 'value' => 'US'],
                    ['label' => 'Canada', 'value' => 'CA'],
                    ['label' => 'United Kingdom', 'value' => 'GB'],
                    ['label' => 'Australia', 'value' => 'AU'],
                    ['label' => 'Germany', 'value' => 'DE'],
                    ['label' => 'France', 'value' => 'FR'],
                    ['label' => 'Japan', 'value' => 'JP'],
                    ['label' => 'Other', 'value' => 'OTHER']
                ]"
                corner-hint="Required"
                :error="$errors->first('formData.country')"
            />
            
            <div class="space-y-4">
                <x-checkbox 
                    label="Subscribe to Newsletter" 
                    description="Receive updates about new features and promotions"
                    wire:model.live="formData.newsletter"
                />
                
                <x-checkbox 
                    label="Accept Terms of Service *" 
                    description="I agree to the terms and conditions"
                    wire:model.live="formData.terms_accepted"
                    :error="$errors->first('formData.terms_accepted')"
                />
            </div>
        </div>
    </x-card>

    {{-- Bio Section --}}
    <x-card title="About You (Optional)" icon="document-text" class="shadow-sm">
        <x-textarea 
            label="Brief Bio" 
            placeholder="Tell us a little about yourself..."
            wire:model.live="formData.bio"
            rows="4"
            corner-hint="Optional - Maximum 500 characters"
            :error="$errors->first('formData.bio')"
        />
        
        <div class="mt-2 text-right">
            <span class="text-sm text-gray-500" 
                  x-text="`${($wire.formData.bio || '').length}/500 characters`"></span>
        </div>
    </x-card>

    {{-- Completion Status --}}
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-4 border border-green-200"
         x-show="completionPercentage >= 80"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100">
        <div class="flex items-center space-x-3">
            <x-icon name="check-circle" class="w-6 h-6 text-green-500" />
            <div>
                <h4 class="font-semibold text-green-800">Almost There!</h4>
                <p class="text-sm text-green-600">Your profile is looking great. Ready to continue?</p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom animations for this step */
    .animate-progress {
        animation: progress-fill 0.5s ease-out;
    }
    
    @keyframes progress-fill {
        from { width: 0%; }
        to { width: var(--progress-width); }
    }
    
    /* Enhance form validation states */
    .invalid {
        animation: shake 0.5s ease-in-out;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
</style>

    <!-- Contact Information Section -->
    <x-card title="Contact Information" shadow>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-input 
                label="Phone Number" 
                wire:model.live="formData.personal.phone"
                placeholder="+1 (555) 123-4567"
                hint="Include country code"
            />
            
            <x-select 
                label="Country" 
                wire:model.live="formData.personal.country"
                placeholder="Select your country"
                searchable
                required
            >
                <x-select.option label="United States" value="us" />
                <x-select.option label="Canada" value="ca" />
                <x-select.option label="United Kingdom" value="uk" />
                <x-select.option label="Australia" value="au" />
                <x-select.option label="Germany" value="de" />
                <x-select.option label="France" value="fr" />
                <x-select.option label="Japan" value="jp" />
                <x-select.option label="Other" value="other" />
            </x-select>
        </div>
        
        <x-textarea 
            label="Address"
            wire:model.live="formData.personal.address"
            placeholder="Enter your full address"
            rows="3"
            hint="Street address, city, state/province, postal code"
        />
    </x-card>

    <!-- Newsletter Subscription -->
    <x-card>
        <div class="flex items-center space-x-3">
            <x-checkbox 
                id="newsletter"
                wire:model.live="formData.personal.newsletter_subscription"
                label="Subscribe to our newsletter"
            />
        </div>
        <p class="text-sm text-gray-500 mt-2">
            Get updates about new features and special offers.
        </p>
    </x-card>

    <!-- Information Panel -->
    <x-alert title="Privacy Notice" info>
        <p class="text-sm">
            Your personal information is secure and will only be used for account setup and communication purposes. 
            We follow strict privacy guidelines and never share your data with third parties.
        </p>
    </x-alert>
</div>