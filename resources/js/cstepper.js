// Livewire CStepper with WireUI Integration
document.addEventListener('DOMContentLoaded', function() {
    console.log('Livewire CStepper with WireUI initialized');
    
    // Detect browser refresh and handle stepper state
    detectBrowserRefresh();
    
    // Enhanced animation support
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

        // Auto-scroll to validation errors
        Alpine.directive('cstepper-scroll', (el) => {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        if (el.classList.contains('invalid')) {
                            el.scrollIntoView({ 
                                behavior: 'smooth', 
                                block: 'center' 
                            });
                        }
                    }
                });
            });
            observer.observe(el, { attributes: true });
        });
    }

    // Browser refresh detection
    function detectBrowserRefresh() {
        // Check if page was refreshed
        if (performance.navigation.type === performance.navigation.TYPE_RELOAD) {
            console.log('Browser refresh detected - stepper should auto-reset');
            
            // Mark in session storage that this was a refresh
            sessionStorage.setItem('cstepper_refresh_detected', 'true');
            
            // Find stepper components and trigger reset if needed
            const steppers = document.querySelectorAll('[wire\\:id]');
            steppers.forEach(stepper => {
                if (stepper.getAttribute('wire:id')) {
                    // The backend will handle the reset via session detection
                    console.log('Stepper component found, backend should handle reset');
                }
            });
        } else {
            // Clear refresh marker on normal navigation
            sessionStorage.removeItem('cstepper_refresh_detected');
        }
    }
});

// Enhanced WireUI Integration
window.addEventListener('livewire:initialized', () => {
    console.log('Livewire CStepper with WireUI ready');
    
    // Step change events with animations
    Livewire.on('step-changed', (event) => {
        console.log('Step changed to:', event[0].step);
        
        // Smooth scroll to stepper
        const stepperContainer = document.querySelector('.livewire-cstepper-container');
        if (stepperContainer) {
            stepperContainer.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start',
                inline: 'nearest'
            });
        }

        // Update progress animations
        const indicators = document.querySelectorAll('.step-indicator');
        indicators.forEach((indicator, index) => {
            const badge = indicator.querySelector('.badge, x-badge');
            if (badge) {
                if (index < event[0].step) {
                    badge.classList.add('animate-bounce');
                    setTimeout(() => badge.classList.remove('animate-bounce'), 600);
                } else if (index === event[0].step) {
                    badge.classList.add('animate-pulse');
                } else {
                    badge.classList.remove('animate-pulse');
                }
            }
        });
    });
    
    // Stepper reset event handling
    Livewire.on('stepper-reset', (event) => {
        console.log('Stepper reset detected');
        
        // Clear any cached form data
        sessionStorage.removeItem('cstepper_temp_data');
        
        // Reset progress animations
        const indicators = document.querySelectorAll('.step-indicator');
        indicators.forEach((indicator) => {
            const badge = indicator.querySelector('.badge, x-badge');
            if (badge) {
                badge.classList.remove('animate-pulse', 'animate-bounce');
            }
        });
        
        // Smooth scroll to top of stepper
        const stepperContainer = document.querySelector('.livewire-cstepper-container');
        if (stepperContainer) {
            stepperContainer.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start',
                inline: 'nearest'
            });
        }
        
        // Show reset animation
        const stepContent = document.querySelector('.step-content');
        if (stepContent) {
            stepContent.classList.add('animate-fadeIn');
            setTimeout(() => stepContent.classList.remove('animate-fadeIn'), 500);
        }
    });
    
    // Stepper completion with enhanced feedback
    Livewire.on('stepper-completed', (event) => {
        console.log('Stepper completed:', event);
        
        // Show confetti animation if available
        if (window.confetti) {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 }
            });
        }

        // Trigger success notification via WireUI events
        window.dispatchEvent(new CustomEvent('wireui:notification', {
            detail: {
                title: 'Congratulations!',
                description: 'Your form has been completed successfully.',
                icon: 'check-circle',
                iconColor: 'text-green-500'
            }
        }));
    });

    // Enhanced form validation feedback
    Livewire.on('validation-errors', (event) => {
        // Find first invalid field and focus it
        const firstInvalid = document.querySelector('.invalid input, .invalid select, .invalid textarea');
        if (firstInvalid) {
            firstInvalid.focus();
            firstInvalid.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
        }
    });

    // Auto-save functionality (if enabled)
    if (window.LivewireCStepperConfig?.autoSave) {
        let autoSaveTimer;
        document.addEventListener('input', (e) => {
            if (e.target.hasAttribute('wire:model') || e.target.hasAttribute('wire:model.live')) {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(() => {
                    // Trigger auto-save event
                    Livewire.dispatch('auto-save-progress');
                }, window.LivewireCStepperConfig.autoSaveInterval || 5000);
            }
        });
    }
});

// Enhanced accessibility support
document.addEventListener('keydown', (e) => {
    // Allow navigation with arrow keys when focused on stepper
    const stepperContainer = document.querySelector('.livewire-cstepper-container');
    if (document.activeElement && stepperContainer?.contains(document.activeElement)) {
        if (e.key === 'ArrowLeft' && !e.target.matches('input, textarea, select')) {
            e.preventDefault();
            const backButton = document.querySelector('[wire\\:click="goBack"]');
            if (backButton && !backButton.disabled) {
                backButton.click();
            }
        } else if (e.key === 'ArrowRight' && !e.target.matches('input, textarea, select')) {
            e.preventDefault();
            const nextButton = document.querySelector('[wire\\:click="advance"]');
            if (nextButton && !nextButton.disabled) {
                nextButton.click();
            }
        }
    }
});

// Export enhanced API
window.LivewireCStepper = {
    version: '1.0.0',
    initialized: true,
    wireUIIntegrated: true,
    
    // Utility methods
    utils: {
        scrollToStep: () => {
            const container = document.querySelector('.livewire-cstepper-container');
            if (container) {
                container.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        },
        
        focusFirstInvalidField: () => {
            const firstInvalid = document.querySelector('.invalid input, .invalid select, .invalid textarea');
            if (firstInvalid) {
                firstInvalid.focus();
                return true;
            }
            return false;
        },
        
        getStepProgress: () => {
            const indicators = document.querySelectorAll('.step-indicator');
            const current = document.querySelector('.step-indicator.current');
            const currentIndex = Array.from(indicators).indexOf(current);
            return {
                current: currentIndex + 1,
                total: indicators.length,
                percentage: Math.round(((currentIndex + 1) / indicators.length) * 100)
            };
        }
    }
};