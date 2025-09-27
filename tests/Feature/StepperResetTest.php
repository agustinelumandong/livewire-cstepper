<?php

namespace Tests\Feature;

use agustinelumandong\LivewireCstepper\Tests\TestCase;
use Livewire\Livewire;
use agustinelumandong\LivewireCstepper\Examples\ResetDemoStepper;

/**
 * Test the stepper reset functionality
 */
class StepperResetTest extends TestCase
{
    /** @test */
    public function stepper_resets_on_fresh_session()
    {
        // Start a fresh session
        session()->flush();
        
        $component = Livewire::test(ResetDemoStepper::class);
        
        // Should start at step 0
        $component->assertSet('currentIndex', 0);
        
        // Should have empty form data
        $component->assertSet('formData.name', '');
        $component->assertSet('formData.email', '');
    }

    /** @test */
    public function stepper_maintains_state_during_session()
    {
        $component = Livewire::test(ResetDemoStepper::class);
        
        // Fill form data and advance
        $component
            ->set('formData.name', 'John Doe')
            ->set('formData.email', 'john@example.com')
            ->call('advance');
        
        $component->assertSet('currentIndex', 1);
        
        // Simulate same session continuation (mount again)
        $component->call('mount');
        
        // Should maintain state if session is marked as initialized
        $component->assertSet('formData.name', 'John Doe');
    }

    /** @test */
    public function manual_reset_clears_all_data()
    {
        $component = Livewire::test(ResetDemoStepper::class);
        
        // Fill some data and advance
        $component
            ->set('formData.name', 'John Doe')
            ->set('formData.email', 'john@example.com')
            ->call('advance');
        
        $component->assertSet('currentIndex', 1);
        
        // Reset manually
        $component->call('confirmReset');
        
        // Should be back to beginning
        $component->assertSet('currentIndex', 0);
        $component->assertSet('formData.name', '');
        $component->assertSet('formData.email', '');
    }

    /** @test */
    public function reset_confirmation_shows_wireui_dialog()
    {
        $component = Livewire::test(ResetDemoStepper::class);
        
        // Call reset confirmation
        $component->call('resetWithConfirmation');
        
        // Should dispatch WireUI confirmation dialog
        $component->assertDispatched('wireui:confirm');
    }

    /** @test */
    public function reset_shows_notification()
    {
        $component = Livewire::test(ResetDemoStepper::class);
        
        // Reset stepper
        $component->call('confirmReset');
        
        // Should show reset notification
        $component->assertDispatched('wireui:notification');
    }

    /** @test */
    public function session_info_provides_debugging_data()
    {
        $component = Livewire::test(ResetDemoStepper::class);
        
        $sessionInfo = $component->get('sessionInfo');
        
        $this->assertArrayHasKey('session_key', $sessionInfo);
        $this->assertArrayHasKey('is_initialized', $sessionInfo);
        $this->assertArrayHasKey('is_active', $sessionInfo);
        $this->assertArrayHasKey('refresh_detected', $sessionInfo);
    }

    /** @test */
    public function auto_reset_triggers_on_external_referrer()
    {
        // Simulate external referrer
        request()->headers->set('referer', 'https://external-site.com');
        
        $component = Livewire::test(ResetDemoStepper::class);
        
        // Set some data first
        $component->set('formData.name', 'John Doe');
        
        // Call mount (simulating fresh page load from external site)
        $component->call('mount');
        
        // Should have been reset due to external referrer
        $component->assertSet('currentIndex', 0);
    }

    /** @test */
    public function stepper_marks_active_on_navigation()
    {
        $component = Livewire::test(ResetDemoStepper::class);
        
        // Fill data and advance
        $component
            ->set('formData.name', 'John Doe')
            ->set('formData.email', 'john@example.com')
            ->call('advance');
        
        // Check if session shows as active
        $sessionInfo = $component->get('sessionInfo');
        
        // After navigation, stepper should be marked as active
        $this->assertTrue($sessionInfo['is_active'] ?? false);
    }

    /** @test */
    public function form_corruption_demo_works()
    {
        $component = Livewire::test(ResetDemoStepper::class);
        
        // Call corruption demo
        $component->call('corruptFormData');
        
        // Should have corrupted data
        $component->assertSet('formData.corrupted', 'This data should not be here');
        
        // Should show notification
        $component->assertDispatched('wireui:notification');
    }
}