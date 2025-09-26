<?php

namespace agustinelumandong\LivewireCstepper\Tests\Unit;

use Livewire\Livewire;
use agustinelumandong\LivewireCstepper\Tests\TestCase;
use agustinelumandong\LivewireCstepper\Tests\Components\TestStepper;

class CStepperTest extends TestCase
{
    /** @test */
    public function it_initializes_with_first_step()
    {
        Livewire::test(TestStepper::class)
            ->assertSet('currentIndex', 0);
    }

    /** @test */
    public function it_has_correct_step_count()
    {
        $component = Livewire::test(TestStepper::class);
        
        $this->assertEquals(3, $component->instance()->getStepCount());
    }

    /** @test */
    public function it_can_advance_to_next_step()
    {
        Livewire::test(TestStepper::class)
            ->set('formData.step1.name', 'John Doe')
            ->set('formData.step1.email', 'john@example.com')
            ->call('advance')
            ->assertSet('currentIndex', 1);
    }

    /** @test */
    public function it_validates_before_advancing()
    {
        Livewire::test(TestStepper::class)
            ->call('advance')
            ->assertSet('currentIndex', 0); // Should stay on current step if validation fails
    }

    /** @test */
    public function it_can_go_back_to_previous_step()
    {
        Livewire::test(TestStepper::class)
            ->set('formData.step1.name', 'John Doe')
            ->set('formData.step1.email', 'john@example.com')
            ->call('advance')
            ->call('goBack')
            ->assertSet('currentIndex', 0);
    }

    /** @test */
    public function it_maintains_form_data_between_steps()
    {
        $component = Livewire::test(TestStepper::class)
            ->set('formData.step1.name', 'John Doe')
            ->set('formData.step1.email', 'john@example.com')
            ->call('advance')
            ->call('goBack');

        $this->assertEquals('John Doe', $component->get('formData.step1.name'));
        $this->assertEquals('john@example.com', $component->get('formData.step1.email'));
    }

    /** @test */
    public function it_can_jump_to_specific_step()
    {
        Livewire::test(TestStepper::class)
            ->call('jumpTo', 2)
            ->assertSet('currentIndex', 2);
    }

    /** @test */
    public function it_prevents_jumping_to_invalid_step()
    {
        Livewire::test(TestStepper::class)
            ->call('jumpTo', 10)
            ->assertSet('currentIndex', 0); // Should stay on current step
    }

    /** @test */
    public function it_handles_negative_step_index()
    {
        Livewire::test(TestStepper::class)
            ->call('jumpTo', -1)
            ->assertSet('currentIndex', 0); // Should stay on current step
    }

    /** @test */
    public function it_dispatches_completion_event_on_submit()
    {
        Livewire::test(TestStepper::class)
            ->set('formData.step1.name', 'John Doe')
            ->set('formData.step1.email', 'john@example.com')
            ->set('formData.step2.phone', '123-456-7890')
            ->set('formData.step2.address', '123 Main St')
            ->set('formData.step3.preferences', ['newsletter'])
            ->set('formData.step3.terms_accepted', true)
            ->call('submit')
            ->assertDispatched('stepper-completed');
    }
}