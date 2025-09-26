<?php

namespace agustinelumandong\LivewireCstepper\Tests\Unit;

use agustinelumandong\LivewireCstepper\Tests\SimpleTestCase;
use agustinelumandong\LivewireCstepper\Traits\ManagesFormData;

class ManagesFormDataTest extends SimpleTestCase
{
    use ManagesFormData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->formData = [];
    }

    public function test_can_set_form_data()
    {
        $data = ['name' => 'John', 'email' => 'john@example.com'];
        
        $this->setFormData($data);
        
        $this->assertEquals($data, $this->getFormData());
    }

    public function test_can_update_form_data()
    {
        $this->setFormData(['name' => 'John']);
        $this->updateFormData(['email' => 'john@example.com']);
        
        $expected = ['name' => 'John', 'email' => 'john@example.com'];
        
        $this->assertEquals($expected, $this->getFormData());
    }

    public function test_can_append_form_data()
    {
        $this->appendFormData('user.name', 'John');
        $this->appendFormData('user.email', 'john@example.com');
        
        $expected = [
            'user' => [
                'name' => 'John',
                'email' => 'john@example.com'
            ]
        ];
        
        $this->assertEquals($expected, $this->getFormData());
    }

    public function test_can_get_form_data_value()
    {
        $this->setFormData(['user' => ['name' => 'John']]);
        
        $this->assertEquals('John', $this->getFormDataValue('user.name'));
        $this->assertEquals('default', $this->getFormDataValue('user.nonexistent', 'default'));
    }

    public function test_can_check_form_data_key_exists()
    {
        $this->setFormData(['name' => 'John']);
        
        $this->assertTrue($this->hasFormDataKey('name'));
        $this->assertFalse($this->hasFormDataKey('email'));
    }

    public function test_can_forget_form_data_key()
    {
        $this->setFormData(['name' => 'John', 'email' => 'john@example.com']);
        
        $this->forgetFormDataKey('email');
        
        $this->assertEquals(['name' => 'John'], $this->getFormData());
    }

    public function test_can_clear_form_data()
    {
        $this->setFormData(['name' => 'John', 'email' => 'john@example.com']);
        
        $this->clearFormData();
        
        $this->assertEquals([], $this->getFormData());
    }

    public function test_can_get_form_data_as_collection()
    {
        $data = ['name' => 'John', 'email' => 'john@example.com'];
        $this->setFormData($data);
        
        $collection = $this->getFormDataCollection();
        
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $collection);
        $this->assertEquals($data, $collection->toArray());
    }

    // Mock config for testing
    protected function ensureSerializableData(array $data): void
    {
        // Skip validation for tests
        return;
    }
}