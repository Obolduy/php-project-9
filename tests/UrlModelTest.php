<?php

namespace Hexlet\Code\Tests;

use Hexlet\Code\Url\Models\Url;
use PHPUnit\Framework\TestCase;

class UrlModelTest extends TestCase
{
    public function testConstructWithoutData(): void
    {
        $url = new Url();
        
        $this->assertNull($url->getId());
        $this->assertNull($url->getCreatedAt());
        $this->assertNull($url->getUpdatedAt());
    }

    public function testConstructWithData(): void
    {
        $data = [
            'id' => 1,
            'name' => 'https://example.com',
            'created_at' => '2024-01-01 12:00:00',
            'updated_at' => '2024-01-02 12:00:00',
        ];
        
        $url = new Url($data);
        
        $this->assertEquals(1, $url->getId());
        $this->assertEquals('https://example.com', $url->getName());
        $this->assertEquals('2024-01-01 12:00:00', $url->getCreatedAt());
        $this->assertEquals('2024-01-02 12:00:00', $url->getUpdatedAt());
    }

    public function testSetId(): void
    {
        $url = new Url();
        $result = $url->setId(5);
        
        $this->assertSame($url, $result);
        $this->assertEquals(5, $url->getId());
    }

    public function testSetName(): void
    {
        $url = new Url();
        $result = $url->setName('https://test.com');
        
        $this->assertSame($url, $result);
        $this->assertEquals('https://test.com', $url->getName());
    }

    public function testSetCreatedAt(): void
    {
        $url = new Url();
        $timestamp = '2024-01-01 12:00:00';
        $result = $url->setCreatedAt($timestamp);
        
        $this->assertSame($url, $result);
        $this->assertEquals($timestamp, $url->getCreatedAt());
    }

    public function testSetUpdatedAt(): void
    {
        $url = new Url();
        $timestamp = '2024-01-02 12:00:00';
        $result = $url->setUpdatedAt($timestamp);
        
        $this->assertSame($url, $result);
        $this->assertEquals($timestamp, $url->getUpdatedAt());
    }

    public function testToArray(): void
    {
        $data = [
            'id' => 1,
            'name' => 'https://example.com',
            'created_at' => '2024-01-01 12:00:00',
            'updated_at' => '2024-01-02 12:00:00',
        ];
        
        $url = new Url($data);
        $array = $url->toArray();
        
        $this->assertEquals($data, $array);
    }

    public function testToArrayWithPartialData(): void
    {
        $url = new Url(['name' => 'https://example.com']);
        $array = $url->toArray();
        
        $this->assertNull($array['id']);
        $this->assertEquals('https://example.com', $array['name']);
        $this->assertNull($array['created_at']);
        $this->assertNull($array['updated_at']);
    }

    public function testChainedSetters(): void
    {
        $url = new Url();
        
        $result = $url
            ->setId(1)
            ->setName('https://example.com')
            ->setCreatedAt('2024-01-01 12:00:00')
            ->setUpdatedAt('2024-01-02 12:00:00');
        
        $this->assertSame($url, $result);
        $this->assertEquals(1, $url->getId());
        $this->assertEquals('https://example.com', $url->getName());
    }

    public function testConstructWithStringId(): void
    {
        $url = new Url(['id' => '123']);
        
        $this->assertSame(123, $url->getId());
    }
}
