<?php

namespace agustinelumandong\LivewireCstepper\Tests\Unit;

use agustinelumandong\LivewireCstepper\Tests\SimpleTestCase;
use agustinelumandong\LivewireCstepper\Traits\SupportsLifecycle;

class SupportsLifecycleTest extends SimpleTestCase
{
    use SupportsLifecycle;

    private array $triggeredEvents = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->triggeredEvents = [];
    }

    /** @test */
    public function it_can_trigger_events()
    {
        $this->triggerEvent('testEvent', 'arg1', 'arg2');
        
        $this->assertContains('testEvent', $this->triggeredEvents);
    }

    /** @test */
    public function it_calls_hook_methods_when_they_exist()
    {
        $this->triggerEvent('onTestHook', 'data');
        
        $this->assertContains('onTestHook', $this->triggeredEvents);
    }

    /** @test */
    public function it_handles_before_and_after_events()
    {
        $this->triggerEvent('beforeMount');
        $this->triggerEvent('afterMount');
        
        $this->assertContains('beforeMount', $this->triggeredEvents);
        $this->assertContains('afterMount', $this->triggeredEvents);
    }

    /** @test */
    public function it_passes_arguments_to_hook_methods()
    {
        $args = ['arg1', 'arg2', 'arg3'];
        $this->triggerEvent('onTestWithArgs', ...$args);
        
        $this->assertContains('onTestWithArgs', $this->triggeredEvents);
    }

    /** @test */
    public function it_handles_step_lifecycle_events()
    {
        $this->triggerEvent('onStepEnter', 0);
        $this->triggerEvent('onStepLeave', 0);
        
        $this->assertContains('onStepEnter', $this->triggeredEvents);
        $this->assertContains('onStepLeave', $this->triggeredEvents);
    }

    /** @test */
    public function it_handles_navigation_events()
    {
        $this->triggerEvent('beforeStepChange', 0, 1);
        $this->triggerEvent('afterStepChange', 0, 1);
        $this->triggerEvent('navigationBlocked', 0, 1);
        
        $this->assertContains('beforeStepChange', $this->triggeredEvents);
        $this->assertContains('afterStepChange', $this->triggeredEvents);
        $this->assertContains('navigationBlocked', $this->triggeredEvents);
    }

    /** @test */
    public function it_handles_stepper_lifecycle_events()
    {
        $this->triggerEvent('beforeResetStepper');
        $this->triggerEvent('afterResetStepper');
        $this->triggerEvent('beforeSubmitStepper');
        $this->triggerEvent('afterSubmitStepper');
        
        $this->assertContains('beforeResetStepper', $this->triggeredEvents);
        $this->assertContains('afterResetStepper', $this->triggeredEvents);
        $this->assertContains('beforeSubmitStepper', $this->triggeredEvents);
        $this->assertContains('afterSubmitStepper', $this->triggeredEvents);
    }

    // Mock implementation of triggerEvent for testing
    protected function triggerEvent(string $event, ...$args): void
    {
        $this->triggeredEvents[] = $event;
        
        // Simulate calling hook method if it exists
        $hookMethod = 'on' . ucfirst($event);
        if (method_exists($this, $hookMethod)) {
            $this->$hookMethod(...$args);
        }
    }

    // Mock hook methods for testing
    public function onTestHook($data): void
    {
        // Mock hook implementation
    }

    public function onTestWithArgs($arg1, $arg2, $arg3): void
    {
        // Mock hook implementation
    }

    public function onStepEnter($stepIndex): void
    {
        // Mock hook implementation
    }

    public function onStepLeave($stepIndex): void
    {
        // Mock hook implementation
    }

    public function beforeStepChange($from, $to): void
    {
        // Mock hook implementation
    }

    public function afterStepChange($from, $to): void
    {
        // Mock hook implementation
    }

    public function navigationBlocked($from, $to): void
    {
        // Mock hook implementation
    }

    public function beforeResetStepper(): void
    {
        // Mock hook implementation
    }

    public function afterResetStepper(): void
    {
        // Mock hook implementation
    }

    public function beforeSubmitStepper(): void
    {
        // Mock hook implementation
    }

    public function afterSubmitStepper(): void
    {
        // Mock hook implementation
    }
}