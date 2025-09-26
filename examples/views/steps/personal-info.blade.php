<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-input 
            label="First Name" 
            wire:model.live="formData.personal.first_name"
            placeholder="Enter your first name"
            required
        />
        
        <x-input 
            label="Last Name" 
            wire:model.live="formData.personal.last_name"
            placeholder="Enter your last name"
            required
        />
    </div>
    
    <x-input 
        label="Date of Birth" 
        type="date"
        wire:model.live="formData.personal.date_of_birth"
        required
    />
    
    <x-select 
        label="Gender" 
        wire:model.live="formData.personal.gender"
        placeholder="Select your gender"
        required
    >
        <x-select.option label="Male" value="male" />
        <x-select.option label="Female" value="female" />
        <x-select.option label="Other" value="other" />
        <x-select.option label="Prefer not to say" value="not_specified" />
    </x-select>
    
    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <h4 class="text-sm font-medium text-blue-900 mb-2">Why do we need this information?</h4>
        <p class="text-sm text-blue-700">
            This information helps us personalize your experience and ensure we can properly verify your identity.
        </p>
    </div>
</div>