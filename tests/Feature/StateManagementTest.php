<?php

namespace agustinelumandong\LivewireCstepper\Tests\Feature;

use Livewire\Livewire;
use agustinelumandong\LivewireCstepper\Tests\TestCase;
use agustinelumandong\LivewireCstepper\Tests\Components\TestStepper;

class StateManagementTest extends TestCase
{
    /** @test */
    public function it_persists_state_across_requests()
    {
        $component = Livewire::test(TestStepper::class)
            ->set('formData.step1.name', 'John Doe')
            ->set('formData.step1.email', 'john@example.com');

        // Check that the data is persisted in the same component instance
        $this->assertEquals('John Doe', $component->get('formData.step1.name'));
        $this->assertEquals('john@example.com', $component->get('formData.step1.email'));
    }

    /** @test */
    public function it_maintains_current_step_across_requests()
    {
        $component = Livewire::test(TestStepper::class)
            ->set('formData.step1.name', 'John Doe')
            ->set('formData.step1.email', 'john@example.com')
            ->call('advance');

        // Check that the current step is maintained
        $this->assertEquals(1, $component->get('currentIndex'));
    }

    /** @test */
    public function it_can_reset_stepper_state()
    {
        $component = Livewire::test(TestStepper::class)
            ->set('formData.step1.name', 'John Doe')
            ->set('formData.step1.email', 'john@example.com')
            ->call('advance');
            
        $component->call('resetStepper')
            ->assertSet('currentIndex', 0);
            
        // Verify form data is cleared
        $formData = $component->instance()->getFormData();
        $this->assertEmpty($formData['step1']['name'] ?? null);
    }

    /** @test */
    public function it_gets_all_form_data()
    {
        $component = Livewire::test(TestStepper::class)
            ->set('formData.step1.name', 'John Doe')
            ->set('formData.step1.email', 'john@example.com')
            ->set('formData.step2.phone', '123-456-7890');

        $allData = $component->instance()->getAllFormData();
        
        $this->assertEquals([
            'step1' => [
                'name' => 'John Doe',
                'email' => 'john@example.com'
            ],
            'step2' => [
                'phone' => '123-456-7890'
            ],
            'step3' => []
        ], $allData);
    }

    /** @test */
    public function it_can_update_specific_form_data()
    {
        $component = Livewire::test(TestStepper::class)
            ->call('updateFormData', 'step1.name', 'Jane Doe')
            ->call('updateFormData', 'step1.email', 'jane@example.com');

        $this->assertEquals('Jane Doe', $component->get('formData.step1.name'));
        $this->assertEquals('jane@example.com', $component->get('formData.step1.email'));
    }

    /** @test */
    public function it_can_append_to_form_data()
    {
        $component = Livewire::test(TestStepper::class)
            ->call('appendFormData', 'step1.name', 'John Doe')
            ->call('appendFormData', 'step1.email', 'john@example.com');

        $this->assertEquals('John Doe', $component->get('formData.step1.name'));
        $this->assertEquals('john@example.com', $component->get('formData.step1.email'));
    }

    /** @test */
    public function it_validates_serializable_data()
    {
        // Temporarily enable strict validation for this test
        config(['livewire-cstepper.strict_validation' => true]);
        
        $this->expectException(\InvalidArgumentException::class);
        
        Livewire::test(TestStepper::class)
            ->call('setFormData', [
                'step1' => [
                    'invalid_object' => new \stdClass()
                ]
            ]);
    }

    /** @test */
    public function it_handles_nested_form_data_updates()
    {
        $component = Livewire::test(TestStepper::class)
            ->call('updateFormData', [
                'step1' => [
                    'nested' => [
                        'deep' => [
                            'value' => 'test'
                        ]
                    ]
                ]
            ]);

        $formData = $component->get('formData');
        $this->assertEquals('test', $formData['step1']['nested']['deep']['value']);
    }

    /** @test */
    public function it_merges_form_data_correctly()
    {
        $component = Livewire::test(TestStepper::class)
            ->set('formData.step1.name', 'John')
            ->call('appendFormData', 'step1.email', 'john@example.com')
            ->call('appendFormData', 'step2.phone', '123-456-7890');

        $formData = $component->get('formData');
        
        $this->assertEquals('John', $formData['step1']['name']);
        $this->assertEquals('john@example.com', $formData['step1']['email']);
        $this->assertEquals('123-456-7890', $formData['step2']['phone']);
    }
}