<?php

namespace Hexlet\Code\Tests;

use Hexlet\Code\UrlAnalysis\Models\UrlAnalysis;
use Hexlet\Code\UrlAnalysis\Repositories\UrlAnalysisRepository;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class UrlAnalysisRepositoryTest extends TestCase
{
    private PDO $pdo;
    private UrlAnalysisRepository $repository;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->repository = new UrlAnalysisRepository($this->pdo);
    }

    public function testFindReturnsAnalysisWhenExists(): void
    {
        $expectedData = [
            'id' => 1,
            'url_id' => 5,
            'response_code' => 200,
            'h1' => 'Main Heading',
            'title' => 'Page Title',
            'description' => 'Page description',
            'created_at' => '2024-01-01 12:00:00',
            'updated_at' => '2024-01-01 12:00:00',
        ];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([1]);
        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn($expectedData);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM urls_analyses WHERE id = ?')
            ->willReturn($stmt);

        $analysis = $this->repository->find(1);

        $this->assertInstanceOf(UrlAnalysis::class, $analysis);
        $this->assertEquals(1, $analysis->getId());
        $this->assertEquals(5, $analysis->getUrlId());
        $this->assertEquals(200, $analysis->getResponseCode());
    }

    public function testFindReturnsNullWhenNotExists(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([999]);
        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM urls_analyses WHERE id = ?')
            ->willReturn($stmt);

        $analysis = $this->repository->find(999);

        $this->assertNull($analysis);
    }

    public function testFindByUrlIdReturnsArrayOfAnalyses(): void
    {
        $expectedData = [
            [
                'id' => 1,
                'url_id' => 5,
                'response_code' => 200,
                'h1' => 'Heading 1',
                'title' => 'Title 1',
                'description' => 'Description 1',
                'created_at' => '2024-01-02 12:00:00',
                'updated_at' => '2024-01-02 12:00:00',
            ],
            [
                'id' => 2,
                'url_id' => 5,
                'response_code' => 200,
                'h1' => 'Heading 2',
                'title' => 'Title 2',
                'description' => 'Description 2',
                'created_at' => '2024-01-01 12:00:00',
                'updated_at' => '2024-01-01 12:00:00',
            ],
        ];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([5]);
        $stmt->expects($this->once())
            ->method('fetchAll')
            ->willReturn($expectedData);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('SELECT * FROM urls_analyses WHERE url_id = ?'))
            ->willReturn($stmt);

        $analyses = $this->repository->findByUrlId(5);

        $this->assertIsArray($analyses);
        $this->assertCount(2, $analyses);
        $this->assertInstanceOf(UrlAnalysis::class, $analyses[0]);
        $this->assertEquals(1, $analyses[0]->getId());
        $this->assertEquals(2, $analyses[1]->getId());
    }

    public function testFindByUrlIdReturnsEmptyArrayWhenNoAnalyses(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([999]);
        $stmt->expects($this->once())
            ->method('fetchAll')
            ->willReturn([]);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        $analyses = $this->repository->findByUrlId(999);

        $this->assertIsArray($analyses);
        $this->assertEmpty($analyses);
    }

    public function testSaveCallsInsertForNewAnalysis(): void
    {
        $analysis = new UrlAnalysis([
            'url_id' => 5,
            'response_code' => 200,
            'h1' => 'Test H1',
            'title' => 'Test Title',
            'description' => 'Test Description',
        ]);

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([5, 200, 'Test H1', 'Test Title', 'Test Description']);
        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'id' => 1,
                'created_at' => '2024-01-01 12:00:00',
                'updated_at' => '2024-01-01 12:00:00',
            ]);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('INSERT INTO urls_analyses'))
            ->willReturn($stmt);

        $this->repository->save($analysis);

        $this->assertEquals(1, $analysis->getId());
        $this->assertNotNull($analysis->getCreatedAt());
        $this->assertNotNull($analysis->getUpdatedAt());
    }

    public function testSaveCallsUpdateForExistingAnalysis(): void
    {
        $analysis = new UrlAnalysis([
            'id' => 1,
            'url_id' => 5,
            'response_code' => 404,
            'h1' => 'Updated H1',
            'title' => 'Updated Title',
            'description' => 'Updated Description',
        ]);

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([404, 'Updated H1', 'Updated Title', 'Updated Description', 1]);
        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn(['updated_at' => '2024-01-02 12:00:00']);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('UPDATE urls_analyses'))
            ->willReturn($stmt);

        $this->repository->save($analysis);

        $this->assertNotNull($analysis->getUpdatedAt());
    }

    public function testDeleteExecutesDeleteQuery(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([1]);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM urls_analyses WHERE id = ?')
            ->willReturn($stmt);

        $this->repository->delete(1);
    }

    public function testSaveHandlesNullableFields(): void
    {
        $analysis = new UrlAnalysis([
            'url_id' => 5,
            'response_code' => 200,
            'h1' => null,
            'title' => null,
            'description' => null,
        ]);

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([5, 200, null, null, null]);
        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'id' => 1,
                'created_at' => '2024-01-01 12:00:00',
                'updated_at' => '2024-01-01 12:00:00',
            ]);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        $this->repository->save($analysis);

        $this->assertEquals(1, $analysis->getId());
    }
}
