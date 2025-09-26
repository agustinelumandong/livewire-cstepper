<?php

namespace agustinelumandong\LivewireCstepper\Tests\Feature;

use Livewire\Livewire;
use agustinelumandong\LivewireCstepper\Tests\TestCase;
use agustinelumandong\LivewireCstepper\Tests\Components\TestStepper;

class IntegrationTest extends TestCase
{
    /** @test */
    public function it_integrates_all_components_successfully()
    {
        $component = Livewire::test(TestStepper::class);
        
        // Test initial state
        $this->assertEquals(0, $component->get('currentIndex'));
        $this->assertEquals(3, $component->instance()->getStepCount());
        
        // Test step components are properly instantiated
        $stepInstances = $component->instance()->stepComponentInstances();
        $this->assertCount(3, $stepInstances);
        
        // Test each step has correct sequence
        foreach ($stepInstances as $index => $step) {
            $this->assertEquals($index, $step->getSequence());
        }
    }

    /** @test */
    public function it_handles_full_user_workflow()
    {
        $component = Livewire::test(TestStepper::class);
        
        // Complete Step 1
        $component
            ->set('formData.step1.name', 'John Doe')
            ->set('formData.step1.email', 'john@example.com');
            
        // Validate step 1 data
        $currentStep = $component->instance()->getCurrentStepComponent();
        $this->assertTrue($currentStep->isValid());
        
        // Advance to step 2
        $component->call('advance')->assertSet('currentIndex', 1);
        
        // Complete Step 2
        $component
            ->set('formData.step2.phone', '123-456-7890')
            ->set('formData.step2.address', '123 Main St, City, State');
            
        // Advance to step 3
        $component->call('advance')->assertSet('currentIndex', 2);
        
        // Complete Step 3
        $component
            ->set('formData.step3.preferences', ['newsletter', 'updates'])
            ->set('formData.step3.terms_accepted', true);
            
        // Submit the stepper
        $component
            ->call('submit')
            ->assertDispatched('stepper-completed')
            ->assertSet('message', 'Stepper completed successfully!');
    }

    /** @test */
    public function it_handles_complex_navigation_scenarios()
    {
        $component = Livewire::test(TestStepper::class);
        
        // Jump to middle step
        $component->call('jumpTo', 1)->assertSet('currentIndex', 1);
        
        // Go back to first step
        $component->call('goBack')->assertSet('currentIndex', 0);
        
        // Fill first step and advance
        $component
            ->set('formData.step1.name', 'Jane Doe')
            ->set('formData.step1.email', 'jane@example.com')
            ->call('advance')
            ->assertSet('currentIndex', 1);
            
        // Jump to last step
        $component->call('jumpTo', 2)->assertSet('currentIndex', 2);
        
        // Go back multiple steps
        $component->call('jumpTo', 0)->assertSet('currentIndex', 0);
        
        // Data should be preserved
        $this->assertEquals('Jane Doe', $component->get('formData.step1.name'));
        $this->assertEquals('jane@example.com', $component->get('formData.step1.email'));
    }

    /** @test */
    public function it_handles_validation_across_all_steps()
    {
        $component = Livewire::test(TestStepper::class);
        
        // Test validation on each step
        
        // Step 1 - Invalid data
        $component
            ->set('formData.step1.name', '')
            ->set('formData.step1.email', 'invalid')
            ->call('advance')
            ->assertSet('currentIndex', 0);
            
        // Step 1 - Valid data
        $component
            ->set('formData.step1.name', 'John Doe')
            ->set('formData.step1.email', 'john@example.com')
            ->call('advance')
            ->assertSet('currentIndex', 1);
            
        // Step 2 - Invalid data
        $component
            ->set('formData.step2.phone', '')
            ->set('formData.step2.address', '')
            ->call('advance')
            ->assertSet('currentIndex', 1);
            
        // Step 2 - Valid data
        $component
            ->set('formData.step2.phone', '123-456-7890')
            ->set('formData.step2.address', '123 Main St')
            ->call('advance')
            ->assertSet('currentIndex', 2);
            
        // Step 3 - Invalid data (no preferences, terms not accepted)
        $component
            ->set('formData.step3.preferences', [])
            ->set('formData.step3.terms_accepted', false);
            
        $currentStep = $component->instance()->getCurrentStepComponent();
        $this->assertFalse($currentStep->isValid());
        
        // Step 3 - Valid data
        $component
            ->set('formData.step3.preferences', ['newsletter'])
            ->set('formData.step3.terms_accepted', true);
            
        $currentStep = $component->instance()->getCurrentStepComponent();
        $this->assertTrue($currentStep->isValid());
    }

    /** @test */
    public function it_handles_error_recovery()
    {
        $component = Livewire::test(TestStepper::class);
        
        // Try to advance with invalid data
        $component
            ->set('formData.step1.name', '')
            ->call('advance')
            ->assertSet('currentIndex', 0);
            
        // Fix the data and try again
        $component
            ->set('formData.step1.name', 'John Doe')
            ->set('formData.step1.email', 'john@example.com')
            ->call('advance')
            ->assertSet('currentIndex', 1);
            
        // Should work normally after error recovery
        $this->assertEquals('John Doe', $component->get('formData.step1.name'));
    }

    /** @test */
    public function it_maintains_state_consistency()
    {
        $component = Livewire::test(TestStepper::class);
        
        // Set data across multiple steps
        $component
            ->set('formData.step1.name', 'John Doe')
            ->set('formData.step1.email', 'john@example.com')
            ->set('formData.step2.phone', '123-456-7890')
            ->set('formData.step3.preferences', ['newsletter']);
            
        // Navigate around
        $component
            ->call('jumpTo', 1)
            ->call('jumpTo', 2)
            ->call('jumpTo', 0);
            
        // All data should still be present
        $allData = $component->instance()->getAllFormData();
        
        $this->assertEquals('John Doe', $allData['step1']['name']);
        $this->assertEquals('john@example.com', $allData['step1']['email']);
        $this->assertEquals('123-456-7890', $allData['step2']['phone']);
        $this->assertEquals(['newsletter'], $allData['step3']['preferences']);
    }
}