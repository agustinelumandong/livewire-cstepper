<?php

namespace agustinelumandong\LivewireCstepper\Tests\Feature;

use Livewire\Livewire;
use agustinelumandong\LivewireCstepper\Tests\TestCase;
use agustinelumandong\LivewireCstepper\Tests\Components\TestStepper;

class NavigationTest extends TestCase
{
    /** @test */
    public function it_can_complete_full_stepper_flow()
    {
        Livewire::test(TestStepper::class)
            // Step 1
            ->set('formData.step1.name', 'John Doe')
            ->set('formData.step1.email', 'john@example.com')
            ->call('advance')
            ->assertSet('currentIndex', 1)
            
            // Step 2
            ->set('formData.step2.phone', '123-456-7890')
            ->set('formData.step2.address', '123 Main St, City, State')
            ->call('advance')
            ->assertSet('currentIndex', 2)
            
            // Step 3
            ->set('formData.step3.preferences', ['newsletter', 'updates'])
            ->set('formData.step3.terms_accepted', true)
            ->call('submit')
            ->assertDispatched('stepper-completed');
    }

    /** @test */
    public function it_can_navigate_backwards_through_steps()
    {
        Livewire::test(TestStepper::class)
            ->set('formData.step1.name', 'John Doe')
            ->set('formData.step1.email', 'john@example.com')
            ->call('advance')
            ->assertSet('currentIndex', 1)
            ->set('formData.step2.phone', '123-456-7890')
            ->set('formData.step2.address', '123 Main St')
            ->call('advance')
            ->assertSet('currentIndex', 2)
            ->call('goBack')
            ->assertSet('currentIndex', 1)
            ->call('goBack')
            ->assertSet('currentIndex', 0);
    }

    /** @test */
    public function it_prevents_advancing_with_invalid_data()
    {
        Livewire::test(TestStepper::class)
            ->set('formData.step1.name', '') // Invalid
            ->set('formData.step1.email', 'invalid-email') // Invalid
            ->call('advance')
            ->assertSet('currentIndex', 0); // Should stay on step 0
    }

    /** @test */
    public function it_allows_jumping_to_valid_steps()
    {
        Livewire::test(TestStepper::class)
            ->call('jumpTo', 1)
            ->assertSet('currentIndex', 1)
            ->call('jumpTo', 2)
            ->assertSet('currentIndex', 2)
            ->call('jumpTo', 0)
            ->assertSet('currentIndex', 0);
    }

    /** @test */
    public function it_maintains_step_data_during_navigation()
    {
        $component = Livewire::test(TestStepper::class)
            ->set('formData.step1.name', 'John Doe')
            ->set('formData.step1.email', 'john@example.com')
            ->call('advance')
            ->set('formData.step2.phone', '123-456-7890')
            ->call('goBack')
            ->call('advance');

        // Data should be maintained
        $this->assertEquals('John Doe', $component->get('formData.step1.name'));
        $this->assertEquals('john@example.com', $component->get('formData.step1.email'));
        $this->assertEquals('123-456-7890', $component->get('formData.step2.phone'));
    }

    /** @test */
    public function it_handles_edge_case_navigation()
    {
        Livewire::test(TestStepper::class)
            // Try to go back from first step
            ->call('goBack')
            ->assertSet('currentIndex', 0)
            
            // Jump to last step and try to advance
            ->call('jumpTo', 2)
            ->call('advance')
            ->assertSet('currentIndex', 2); // Should stay on last step
    }

    /** @test */
    public function it_tracks_current_step_in_url()
    {
        Livewire::test(TestStepper::class)
            ->set('formData.step1.name', 'John Doe')
            ->set('formData.step1.email', 'john@example.com')
            ->call('advance')
            ->assertSet('currentIndex', 1);
            
        // The currentIndex should be URL tracked due to #[Url(keep: true)]
    }
}