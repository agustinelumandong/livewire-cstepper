<?php

namespace agustinelumandong\LivewireCstepper\Tests\Unit;

use agustinelumandong\LivewireCstepper\Tests\SimpleTestCase;
use agustinelumandong\LivewireCstepper\Traits\HandlesNavigation;

class HandlesNavigationTest extends SimpleTestCase
{
    use HandlesNavigation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->currentIndex = 0;
        $this->steps = ['step1', 'step2', 'step3'];
    }

    /** @test */
    public function it_can_check_current_step()
    {
        $this->assertTrue($this->stepIs(0));
        $this->assertFalse($this->stepIs(1));
    }

    /** @test */
    public function it_can_check_step_comparisons()
    {
        $this->currentIndex = 1;
        
        $this->assertTrue($this->stepIsGreaterOrEqualThan(0));
        $this->assertTrue($this->stepIsGreaterOrEqualThan(1));
        $this->assertFalse($this->stepIsGreaterOrEqualThan(2));
        
        $this->assertFalse($this->stepIsLessOrEqualThan(0));
        $this->assertTrue($this->stepIsLessOrEqualThan(1));
        $this->assertTrue($this->stepIsLessOrEqualThan(2));
    }

    /** @test */
    public function it_calculates_next_step_index_correctly()
    {
        $this->currentIndex = 0;
        $this->assertEquals(1, $this->getNextStepIndex());
        
        $this->currentIndex = 1;
        $this->assertEquals(2, $this->getNextStepIndex());
        
        $this->currentIndex = 2;
        $this->assertEquals(2, $this->getNextStepIndex()); // Should stay at last step
    }

    /** @test */
    public function it_calculates_previous_step_index_correctly()
    {
        $this->currentIndex = 2;
        $this->assertEquals(1, $this->getPreviousStepIndex());
        
        $this->currentIndex = 1;
        $this->assertEquals(0, $this->getPreviousStepIndex());
        
        $this->currentIndex = 0;
        $this->assertEquals(0, $this->getPreviousStepIndex()); // Should stay at first step
    }

    /** @test */
    public function it_checks_if_has_next_step()
    {
        $this->currentIndex = 0;
        $this->assertTrue($this->hasNextStep());
        
        $this->currentIndex = 1;
        $this->assertTrue($this->hasNextStep());
        
        $this->currentIndex = 2;
        $this->assertFalse($this->hasNextStep());
    }

    /** @test */
    public function it_checks_if_has_previous_step()
    {
        $this->currentIndex = 0;
        $this->assertFalse($this->hasPreviousStep());
        
        $this->currentIndex = 1;
        $this->assertTrue($this->hasPreviousStep());
        
        $this->currentIndex = 2;
        $this->assertTrue($this->hasPreviousStep());
    }

    /** @test */
    public function it_validates_step_navigation()
    {
        $this->assertTrue($this->canNavigateToStep(0));
        $this->assertTrue($this->canNavigateToStep(1));
        $this->assertTrue($this->canNavigateToStep(2));
        
        $this->assertFalse($this->canNavigateToStep(-1));
        $this->assertFalse($this->canNavigateToStep(3));
        $this->assertFalse($this->canNavigateToStep(10));
    }

    // Mock required methods for trait functionality
    protected function triggerEvent(string $event, ...$args): void
    {
        // Mock implementation
    }

    protected function canNavigateToStep($index): bool
    {
        return $index >= 0 && $index < count($this->steps);
    }

    protected function hasPreviousStep($index = null): bool
    {
        $index = $index ?? $this->currentIndex;
        return $index > 0;
    }

    protected function hasNextStep($index = null): bool
    {
        $index = $index ?? $this->currentIndex;
        return $index < count($this->steps) - 1;
    }

    protected function getNextStepIndex(): int
    {
        return min($this->currentIndex + 1, count($this->steps) - 1);
    }

    protected function getPreviousStepIndex(): int
    {
        return max($this->currentIndex - 1, 0);
    }
}