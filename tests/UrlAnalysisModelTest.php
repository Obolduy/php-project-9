<?php

namespace Hexlet\Code\Tests;

use Hexlet\Code\UrlAnalysis\Models\UrlAnalysis;
use PHPUnit\Framework\TestCase;

class UrlAnalysisModelTest extends TestCase
{
    public function testConstructWithoutData(): void
    {
        $analysis = new UrlAnalysis();

        $this->assertNull($analysis->getId());
        $this->assertNull($analysis->getResponseCode());
        $this->assertNull($analysis->getH1());
        $this->assertNull($analysis->getTitle());
        $this->assertNull($analysis->getDescription());
        $this->assertNull($analysis->getCreatedAt());
        $this->assertNull($analysis->getUpdatedAt());
    }

    public function testConstructWithFullData(): void
    {
        $data = [
            'id' => 1,
            'url_id' => 5,
            'response_code' => 200,
            'h1' => 'Main Heading',
            'title' => 'Page Title',
            'description' => 'Page description',
            'created_at' => '2024-01-01 12:00:00',
            'updated_at' => '2024-01-02 12:00:00',
        ];

        $analysis = new UrlAnalysis($data);

        $this->assertEquals(1, $analysis->getId());
        $this->assertEquals(5, $analysis->getUrlId());
        $this->assertEquals(200, $analysis->getResponseCode());
        $this->assertEquals('Main Heading', $analysis->getH1());
        $this->assertEquals('Page Title', $analysis->getTitle());
        $this->assertEquals('Page description', $analysis->getDescription());
        $this->assertEquals('2024-01-01 12:00:00', $analysis->getCreatedAt());
        $this->assertEquals('2024-01-02 12:00:00', $analysis->getUpdatedAt());
    }

    public function testSetId(): void
    {
        $analysis = new UrlAnalysis();
        $analysis->setId(10);

        $this->assertEquals(10, $analysis->getId());
    }

    public function testSetUrlId(): void
    {
        $analysis = new UrlAnalysis();
        $analysis->setUrlId(5);

        $this->assertEquals(5, $analysis->getUrlId());
    }

    public function testSetResponseCode(): void
    {
        $analysis = new UrlAnalysis();
        $analysis->setResponseCode(404);

        $this->assertEquals(404, $analysis->getResponseCode());
    }

    public function testSetResponseCodeToNull(): void
    {
        $analysis = new UrlAnalysis(['response_code' => 200]);
        $analysis->setResponseCode(null);

        $this->assertNull($analysis->getResponseCode());
    }

    public function testSetH1(): void
    {
        $analysis = new UrlAnalysis();
        $analysis->setH1('Test H1');

        $this->assertEquals('Test H1', $analysis->getH1());
    }

    public function testSetH1ToNull(): void
    {
        $analysis = new UrlAnalysis(['h1' => 'Old H1']);
        $analysis->setH1(null);

        $this->assertNull($analysis->getH1());
    }

    public function testSetTitle(): void
    {
        $analysis = new UrlAnalysis();
        $analysis->setTitle('Test Title');

        $this->assertEquals('Test Title', $analysis->getTitle());
    }

    public function testSetTitleToNull(): void
    {
        $analysis = new UrlAnalysis(['title' => 'Old Title']);
        $analysis->setTitle(null);

        $this->assertNull($analysis->getTitle());
    }

    public function testSetDescription(): void
    {
        $analysis = new UrlAnalysis();
        $analysis->setDescription('Test Description');

        $this->assertEquals('Test Description', $analysis->getDescription());
    }

    public function testSetDescriptionToNull(): void
    {
        $analysis = new UrlAnalysis(['description' => 'Old Description']);
        $analysis->setDescription(null);

        $this->assertNull($analysis->getDescription());
    }

    public function testSetCreatedAt(): void
    {
        $analysis = new UrlAnalysis();
        $timestamp = '2024-01-01 12:00:00';
        $analysis->setCreatedAt($timestamp);

        $this->assertEquals($timestamp, $analysis->getCreatedAt());
    }

    public function testSetUpdatedAt(): void
    {
        $analysis = new UrlAnalysis();
        $timestamp = '2024-01-02 12:00:00';
        $analysis->setUpdatedAt($timestamp);

        $this->assertEquals($timestamp, $analysis->getUpdatedAt());
    }

    public function testToArray(): void
    {
        $data = [
            'id' => 1,
            'url_id' => 5,
            'response_code' => 200,
            'h1' => 'Main Heading',
            'title' => 'Page Title',
            'description' => 'Page description',
            'created_at' => '2024-01-01 12:00:00',
            'updated_at' => '2024-01-02 12:00:00',
        ];

        $analysis = new UrlAnalysis($data);
        $array = $analysis->toArray();

        $this->assertEquals($data, $array);
    }

    public function testToArrayWithPartialData(): void
    {
        $analysis = new UrlAnalysis(['url_id' => 5, 'response_code' => 200]);
        $array = $analysis->toArray();

        $this->assertNull($array['id']);
        $this->assertEquals(5, $array['url_id']);
        $this->assertEquals(200, $array['response_code']);
        $this->assertNull($array['h1']);
        $this->assertNull($array['title']);
        $this->assertNull($array['description']);
        $this->assertNull($array['created_at']);
        $this->assertNull($array['updated_at']);
    }

    public function testConstructWithStringIds(): void
    {
        $analysis = new UrlAnalysis([
            'id' => '123',
            'url_id' => '456',
            'response_code' => '200'
        ]);

        $this->assertSame(123, $analysis->getId());
        $this->assertSame(456, $analysis->getUrlId());
        $this->assertSame(200, $analysis->getResponseCode());
    }

    public function testConstructWithNullableFields(): void
    {
        $analysis = new UrlAnalysis([
            'url_id' => 1,
            'h1' => null,
            'title' => null,
            'description' => null,
        ]);

        $this->assertEquals(1, $analysis->getUrlId());
        $this->assertNull($analysis->getH1());
        $this->assertNull($analysis->getTitle());
        $this->assertNull($analysis->getDescription());
    }
}
