<?php

namespace agustinelumandong\LivewireCstepper\Tests\Feature;

use Livewire\Livewire;
use agustinelumandong\LivewireCstepper\Tests\TestCase;
use agustinelumandong\LivewireCstepper\Tests\Components\TestStepper;

class PerformanceTest extends TestCase
{
    /** @test */
    public function it_handles_multiple_step_instances_efficiently()
    {
        $startTime = microtime(true);
        
        $component = Livewire::test(TestStepper::class);
        
        // Create multiple step instances
        for ($i = 0; $i < 10; $i++) {
            $component->instance()->stepComponentInstances();
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Should complete within reasonable time (1 second)
        $this->assertLessThan(1.0, $executionTime);
    }

    /** @test */
    public function it_handles_large_form_data_efficiently()
    {
        $component = Livewire::test(TestStepper::class);
        
        $startTime = microtime(true);
        
        // Set large amount of form data
        $largeData = [];
        for ($i = 0; $i < 1000; $i++) {
            $largeData["field_$i"] = "value_$i";
        }
        
        $component->set('formData.step1', $largeData);
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Should complete within reasonable time (2 seconds)
        $this->assertLessThan(2.0, $executionTime);
    }

    /** @test */
    public function it_handles_rapid_navigation_efficiently()
    {
        $component = Livewire::test(TestStepper::class);
        
        $startTime = microtime(true);
        
        // Rapid navigation between steps
        for ($i = 0; $i < 100; $i++) {
            $component->call('jumpTo', $i % 3);
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Should complete within reasonable time (3 seconds)
        $this->assertLessThan(3.0, $executionTime);
    }

    /** @test */
    public function it_caches_step_instances_properly()
    {
        $component = Livewire::test(TestStepper::class);
        
        // First call should create instances
        $firstCall = $component->instance()->stepComponentInstances();
        
        // Second call should use cached instances
        $secondCall = $component->instance()->stepComponentInstances();
        
        // Should be the same instances (cached)
        $this->assertSame($firstCall, $secondCall);
    }

    /** @test */
    public function it_handles_memory_usage_efficiently()
    {
        $initialMemory = memory_get_usage();
        
        $components = [];
        
        // Create multiple stepper instances
        for ($i = 0; $i < 10; $i++) {
            $components[] = Livewire::test(TestStepper::class);
        }
        
        $peakMemory = memory_get_peak_usage();
        $memoryUsed = $peakMemory - $initialMemory;
        
        // Should use less than 50MB for 10 instances
        $this->assertLessThan(50 * 1024 * 1024, $memoryUsed);
        
        // Clean up
        unset($components);
    }
}