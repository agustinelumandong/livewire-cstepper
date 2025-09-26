// Livewire CStepper JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize CStepper functionality
    console.log('Livewire CStepper initialized');
    
    // Animation support
    if (window.Alpine) {
        Alpine.directive('cstepper-animate', (el, { expression }, { effect, cleanup }) => {
            effect(() => {
                if (expression) {
                    el.classList.add('animate-fadeInUp');
                } else {
                    el.classList.remove('animate-fadeInUp');
                }
            });
        });
    }
});

// WireUI Integration hooks
window.addEventListener('livewire:initialized', () => {
    console.log('Livewire and WireUI ready for CStepper');
    
    // Custom CStepper events
    Livewire.on('step-changed', (event) => {
        console.log('Step changed to:', event.step);
        
        // Scroll to top of stepper on step change
        const stepperContainer = document.querySelector('.livewire-cstepper-container');
        if (stepperContainer) {
            stepperContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
    
    Livewire.on('stepper-completed', (event) => {
        console.log('Stepper completed:', event);
        
        // Show success notification if WireUI is available
        if (window.$wireui) {
            $wireui.notify({
                title: 'Success!',
                description: 'Form completed successfully.',
                icon: 'success'
            });
        }
    });
});

// Export for use in other scripts
window.LivewireCStepper = {
    version: '1.0.0',
    initialized: true
};