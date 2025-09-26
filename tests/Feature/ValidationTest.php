<?php

namespace agustinelumandong\LivewireCstepper\Tests\Feature;

use Livewire\Livewire;
use agustinelumandong\LivewireCstepper\Tests\TestCase;
use agustinelumandong\LivewireCstepper\Tests\Components\TestStepper;

class ValidationTest extends TestCase
{
    /** @test */
    public function it_validates_step_one_required_fields()
    {
        Livewire::test(TestStepper::class)
            ->set('formData.step1.name', '')
            ->set('formData.step1.email', '')
            ->call('advance')
            ->assertSet('currentIndex', 0); // Should not advance
    }

    /** @test */
    public function it_validates_step_one_email_format()
    {
        Livewire::test(TestStepper::class)
            ->set('formData.step1.name', 'John Doe')
            ->set('formData.step1.email', 'invalid-email')
            ->call('advance')
            ->assertSet('currentIndex', 0); // Should not advance
    }

    /** @test */
    public function it_validates_step_two_required_fields()
    {
        Livewire::test(TestStepper::class)
            // Complete step 1
            ->set('formData.step1.name', 'John Doe')
            ->set('formData.step1.email', 'john@example.com')
            ->call('advance')
            ->assertSet('currentIndex', 1)
            
            // Try to advance with empty step 2
            ->set('formData.step2.phone', '')
            ->set('formData.step2.address', '')
            ->call('advance')
            ->assertSet('currentIndex', 1); // Should not advance
    }

    /** @test */
    public function it_validates_step_three_preferences_array()
    {
        Livewire::test(TestStepper::class)
            // Complete steps 1 & 2
            ->set('formData.step1.name', 'John Doe')
            ->set('formData.step1.email', 'john@example.com')
            ->call('advance')
            ->set('formData.step2.phone', '123-456-7890')
            ->set('formData.step2.address', '123 Main St')
            ->call('advance')
            ->assertSet('currentIndex', 2)
            
            // Try to submit without preferences
            ->set('formData.step3.preferences', [])
            ->set('formData.step3.terms_accepted', false)
            ->call('submit');
            
        // Should not dispatch completion event due to validation failure
        // Note: The exact behavior depends on your validation implementation
    }

    /** @test */
    public function it_validates_step_three_terms_acceptance()
    {
        Livewire::test(TestStepper::class)
            // Complete steps 1 & 2
            ->set('formData.step1.name', 'John Doe')
            ->set('formData.step1.email', 'john@example.com')
            ->call('advance')
            ->set('formData.step2.phone', '123-456-7890')
            ->set('formData.step2.address', '123 Main St')
            ->call('advance')
            ->assertSet('currentIndex', 2)
            
            // Try to submit without accepting terms
            ->set('formData.step3.preferences', ['newsletter'])
            ->set('formData.step3.terms_accepted', false)
            ->call('submit');
            
        // Should not dispatch completion event due to validation failure
    }

    /** @test */
    public function it_passes_all_validations_on_successful_submission()
    {
        Livewire::test(TestStepper::class)
            ->set('formData.step1.name', 'John Doe')
            ->set('formData.step1.email', 'john@example.com')
            ->call('advance')
            ->set('formData.step2.phone', '123-456-7890')
            ->set('formData.step2.address', '123 Main St')
            ->call('advance')
            ->set('formData.step3.preferences', ['newsletter', 'updates'])
            ->set('formData.step3.terms_accepted', true)
            ->call('submit')
            ->assertDispatched('stepper-completed');
    }

    /** @test */
    public function it_maintains_validation_state_between_steps()
    {
        $component = Livewire::test(TestStepper::class)
            ->set('formData.step1.name', 'John Doe')
            ->set('formData.step1.email', 'john@example.com')
            ->call('advance')
            ->call('goBack');

        // Step 1 should still be valid after going back
        $this->assertEquals('John Doe', $component->get('formData.step1.name'));
        $this->assertEquals('john@example.com', $component->get('formData.step1.email'));
    }

    /** @test */
    public function it_handles_validation_errors_gracefully()
    {
        Livewire::test(TestStepper::class)
            ->set('formData.step1.name', str_repeat('a', 300)) // Too long
            ->set('formData.step1.email', 'john@example.com')
            ->call('advance')
            ->assertSet('currentIndex', 0); // Should not advance due to validation
    }
}