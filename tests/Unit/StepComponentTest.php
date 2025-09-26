<?php

namespace agustinelumandong\LivewireCstepper\Tests\Unit;

use agustinelumandong\LivewireCstepper\Tests\TestCase;
use agustinelumandong\LivewireCstepper\Tests\Components\TestStepOne;
use agustinelumandong\LivewireCstepper\Tests\Components\TestStepper;

class StepComponentTest extends TestCase
{
    /** @test */
    public function it_can_create_step_component()
    {
        $stepper = new TestStepper();
        $step = TestStepOne::make($stepper);
        
        $this->assertInstanceOf(TestStepOne::class, $step);
        $this->assertSame($stepper, $step->getStepper());
    }

    /** @test */
    public function it_has_validation_rules()
    {
        $stepper = new TestStepper();
        $step = TestStepOne::make($stepper);
        
        $rules = $step->rules();
        
        $this->assertArrayHasKey('formData.step1.name', $rules);
        $this->assertArrayHasKey('formData.step1.email', $rules);
    }

    /** @test */
    public function it_can_validate_step_data()
    {
        $stepper = new TestStepper();
        $stepper->formData = [
            'step1' => [
                'name' => 'John Doe',
                'email' => 'john@example.com'
            ]
        ];
        
        $step = TestStepOne::make($stepper);
        
        $this->assertTrue($step->isValid());
    }

    /** @test */
    public function it_fails_validation_with_invalid_data()
    {
        $stepper = new TestStepper();
        $stepper->formData = [
            'step1' => [
                'name' => '',
                'email' => 'invalid-email'
            ]
        ];
        
        $step = TestStepOne::make($stepper);
        
        $this->assertFalse($step->isValid());
    }

    /** @test */
    public function it_can_set_and_get_sequence()
    {
        $stepper = new TestStepper();
        $step = TestStepOne::make($stepper);
        
        $step->setSequence(5);
        
        $this->assertEquals(5, $step->getSequence());
    }

    /** @test */
    public function it_can_render_view()
    {
        $stepper = new TestStepper();
        $step = TestStepOne::make($stepper);
        
        $view = $step->render();
        
        $this->assertInstanceOf(\Illuminate\Contracts\View\View::class, $view);
    }
}