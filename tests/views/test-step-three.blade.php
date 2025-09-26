<div class="test-step-three">
    <h2>Step 3: Preferences</h2>
    
    <div class="form-group">
        <label>Preferences</label>
        <div class="checkbox-group">
            <label>
                <input 
                    type="checkbox" 
                    wire:model="formData.step3.preferences" 
                    value="newsletter"
                >
                Newsletter
            </label>
            <label>
                <input 
                    type="checkbox" 
                    wire:model="formData.step3.preferences" 
                    value="updates"
                >
                Product Updates
            </label>
        </div>
        @error('formData.step3.preferences') 
            <span class="error">{{ $message }}</span> 
        @enderror
    </div>
    
    <div class="form-group">
        <label>
            <input 
                type="checkbox" 
                wire:model="formData.step3.terms_accepted" 
                value="1"
            >
            I accept the terms and conditions
        </label>
        @error('formData.step3.terms_accepted') 
            <span class="error">{{ $message }}</span> 
        @enderror
    </div>
</div>