<div class="space-y-6">
    <x-alert title="Personal Information" info>
        <x-slot name="slot">
            Please provide your basic personal information. All fields marked with * are required.
        </x-slot>
    </x-alert>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="space-y-4">
            <x-input
                wire:model.live="firstName"
                label="First Name"
                placeholder="Enter your first name"
                icon="user"
                required
            />

            <x-input
                wire:model.live="lastName"
                label="Last Name"
                placeholder="Enter your last name"
                icon="user"
                required
            />

            <x-input
                wire:model.live="email"
                label="Email Address"
                placeholder="Enter your email address"
                icon="mail"
                type="email"
                required
            />
        </div>

        <div class="space-y-4">
            <x-input
                wire:model.live="phone"
                label="Phone Number"
                placeholder="+1 (555) 123-4567"
                icon="phone"
                type="tel"
            />

            <x-input
                wire:model.live="dateOfBirth"
                label="Date of Birth"
                type="date"
                icon="calendar"
            />

            <!-- Progress indicator for this step -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <div class="flex items-center justify-between text-sm">
                    <span class="font-medium text-blue-900">Step Progress</span>
                    <span class="text-blue-700">
                        {{ collect([$firstName, $lastName, $email, $phone, $dateOfBirth])->filter()->count() }} / 5 fields completed
                    </span>
                </div>
                <div class="mt-2 bg-blue-200 rounded-full h-2 overflow-hidden">
                    <div 
                        class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                        style="width: {{ (collect([$firstName, $lastName, $email, $phone, $dateOfBirth])->filter()->count() / 5) * 100 }}%"
                    ></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Field Validation Status -->
    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-4 mt-6">
        <div class="flex items-center space-x-2">
            <x-icon 
                :name="$firstName ? 'check-circle' : 'x-circle'" 
                :class="$firstName ? 'text-green-500' : 'text-gray-300'"
                size="sm"
            />
            <span class="text-sm text-gray-600">First Name</span>
        </div>
        
        <div class="flex items-center space-x-2">
            <x-icon 
                :name="$lastName ? 'check-circle' : 'x-circle'" 
                :class="$lastName ? 'text-green-500' : 'text-gray-300'"
                size="sm"
            />
            <span class="text-sm text-gray-600">Last Name</span>
        </div>
        
        <div class="flex items-center space-x-2">
            <x-icon 
                :name="($email && filter_var($email, FILTER_VALIDATE_EMAIL)) ? 'check-circle' : 'x-circle'" 
                :class="($email && filter_var($email, FILTER_VALIDATE_EMAIL)) ? 'text-green-500' : 'text-gray-300'"
                size="sm"
            />
            <span class="text-sm text-gray-600">Email</span>
        </div>
        
        <div class="flex items-center space-x-2">
            <x-icon 
                :name="$phone ? 'check-circle' : 'minus-circle'" 
                :class="$phone ? 'text-green-500' : 'text-yellow-500'"
                size="sm"
            />
            <span class="text-sm text-gray-600">Phone (Optional)</span>
        </div>
        
        <div class="flex items-center space-x-2">
            <x-icon 
                :name="$dateOfBirth ? 'check-circle' : 'minus-circle'" 
                :class="$dateOfBirth ? 'text-green-500' : 'text-yellow-500'"
                size="sm"
            />
            <span class="text-sm text-gray-600">Birth Date (Optional)</span>
        </div>
    </div>
</div>