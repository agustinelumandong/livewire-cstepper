<div class="test-step-one">
    <h2>Step 1: Personal Information</h2>
    
    <div class="form-group">
        <label for="name">Name</label>
        <input 
            type="text" 
            id="name"
            wire:model="formData.step1.name" 
            placeholder="Enter your name"
            class="form-control"
        >
        @error('formData.step1.name') 
            <span class="error">{{ $message }}</span> 
        @enderror
    </div>
    
    <div class="form-group">
        <label for="email">Email</label>
        <input 
            type="email" 
            id="email"
            wire:model="formData.step1.email" 
            placeholder="Enter your email"
            class="form-control"
        >
        @error('formData.step1.email') 
            <span class="error">{{ $message }}</span> 
        @enderror
    </div>
</div>