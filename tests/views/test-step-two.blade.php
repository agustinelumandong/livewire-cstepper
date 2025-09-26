<div class="test-step-two">
    <h2>Step 2: Contact Information</h2>
    
    <div class="form-group">
        <label for="phone">Phone</label>
        <input 
            type="text" 
            id="phone"
            wire:model="formData.step2.phone" 
            placeholder="Enter your phone number"
            class="form-control"
        >
        @error('formData.step2.phone') 
            <span class="error">{{ $message }}</span> 
        @enderror
    </div>
    
    <div class="form-group">
        <label for="address">Address</label>
        <textarea 
            id="address"
            wire:model="formData.step2.address" 
            placeholder="Enter your address"
            class="form-control"
            rows="3"
        ></textarea>
        @error('formData.step2.address') 
            <span class="error">{{ $message }}</span> 
        @enderror
    </div>
</div>