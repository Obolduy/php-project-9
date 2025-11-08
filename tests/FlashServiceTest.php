<?php

namespace Hexlet\Code\Tests;

use Hexlet\Code\Common\Services\FlashService;
use PHPUnit\Framework\TestCase;

class FlashServiceTest extends TestCase
{
    private FlashService $flash;

    protected function setUp(): void
    {
        $_SESSION = [];
        $this->flash = new FlashService();
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testSetAddsValueToSession(): void
    {
        $this->flash->set('success', 'Test message');
        
        $this->assertArrayHasKey('flash', $_SESSION);
        $this->assertArrayHasKey('success', $_SESSION['flash']);
        $this->assertEquals('Test message', $_SESSION['flash']['success']);
    }

    public function testGetReturnsAndRemovesValue(): void
    {
        $this->flash->set('success', 'Test message');
        
        $value = $this->flash->get('success');
        
        $this->assertEquals('Test message', $value);
        $this->assertArrayNotHasKey('success', $_SESSION['flash'] ?? []);
    }

    public function testGetReturnsNullForNonExistentKey(): void
    {
        $value = $this->flash->get('nonexistent');
        
        $this->assertNull($value);
    }

    public function testHasReturnsTrueWhenFlashExists(): void
    {
        $this->flash->set('success', 'Test message');
        
        $this->assertTrue($this->flash->has());
    }

    public function testHasReturnsFalseWhenFlashIsEmpty(): void
    {
        $this->assertFalse($this->flash->has());
    }

    public function testGetAllReturnsAllFlashMessages(): void
    {
        $this->flash->set('success', 'Success message');
        $this->flash->set('error', 'Error message');
        
        $all = $this->flash->getAll();
        
        $this->assertIsArray($all);
        $this->assertArrayHasKey('success', $all);
        $this->assertArrayHasKey('error', $all);
        $this->assertEquals('Success message', $all['success']);
        $this->assertEquals('Error message', $all['error']);
    }

    public function testGetAllRemovesFlashFromSession(): void
    {
        $this->flash->set('success', 'Test message');
        
        $this->flash->getAll();
        
        $this->assertArrayNotHasKey('flash', $_SESSION);
    }

    public function testGetAllReturnsEmptyArrayWhenNoFlash(): void
    {
        $all = $this->flash->getAll();
        
        $this->assertIsArray($all);
        $this->assertEmpty($all);
    }

    public function testClearRemovesAllFlashMessages(): void
    {
        $this->flash->set('success', 'Success message');
        $this->flash->set('error', 'Error message');
        
        $this->flash->clear();
        
        $this->assertArrayNotHasKey('flash', $_SESSION);
    }

    public function testClearWhenNoFlashMessages(): void
    {
        $this->flash->clear();
        
        $this->assertArrayNotHasKey('flash', $_SESSION);
    }

    public function testMultipleSetCallsOverwriteValue(): void
    {
        $this->flash->set('success', 'First message');
        $this->flash->set('success', 'Second message');
        
        $value = $this->flash->get('success');
        
        $this->assertEquals('Second message', $value);
    }

    public function testSetWithDifferentTypes(): void
    {
        $this->flash->set('string', 'text');
        $this->flash->set('number', 123);
        $this->flash->set('array', ['key' => 'value']);
        
        $this->assertEquals('text', $this->flash->get('string'));
        $this->assertEquals(123, $this->flash->get('number'));
        $this->assertEquals(['key' => 'value'], $this->flash->get('array'));
    }
}
