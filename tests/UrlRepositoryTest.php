<?php

namespace Hexlet\Code\Tests;

use Hexlet\Code\Url\Models\Url;
use Hexlet\Code\Url\Repositories\UrlRepository;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class UrlRepositoryTest extends TestCase
{
    private PDO $pdo;
    private UrlRepository $repository;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->repository = new UrlRepository($this->pdo);
    }

    public function testFindReturnsUrlWhenExists(): void
    {
        $expectedUrl = Url::fromArray([
            'id' => 1,
            'name' => 'https://example.com',
            'created_at' => '2024-01-01 12:00:00',
            'updated_at' => '2024-01-01 12:00:00',
        ]);

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([1]);
        $stmt->expects($this->once())
            ->method('fetchObject')
            ->with(Url::class)
            ->willReturn($expectedUrl);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM urls WHERE id = ?')
            ->willReturn($stmt);

        $url = $this->repository->find(1);

        $this->assertInstanceOf(Url::class, $url);
        $this->assertEquals(1, $url->getId());
        $this->assertEquals('https://example.com', $url->getName());
    }

    public function testFindReturnsNullWhenNotExists(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([999]);
        $stmt->expects($this->once())
            ->method('fetchObject')
            ->with(Url::class)
            ->willReturn(false);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM urls WHERE id = ?')
            ->willReturn($stmt);

        $url = $this->repository->find(999);

        $this->assertNull($url);
    }

    public function testFindByNameReturnsUrlWhenExists(): void
    {
        $expectedUrl = Url::fromArray([
            'id' => 1,
            'name' => 'https://example.com',
            'created_at' => '2024-01-01 12:00:00',
            'updated_at' => '2024-01-01 12:00:00',
        ]);

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['https://example.com']);
        $stmt->expects($this->once())
            ->method('fetchObject')
            ->with(Url::class)
            ->willReturn($expectedUrl);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM urls WHERE name = ?')
            ->willReturn($stmt);

        $url = $this->repository->findByName('https://example.com');

        $this->assertInstanceOf(Url::class, $url);
        $this->assertEquals('https://example.com', $url->getName());
    }

    public function testFindByNameReturnsNullWhenNotExists(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['https://nonexistent.com']);
        $stmt->expects($this->once())
            ->method('fetchObject')
            ->with(Url::class)
            ->willReturn(false);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM urls WHERE name = ?')
            ->willReturn($stmt);

        $url = $this->repository->findByName('https://nonexistent.com');

        $this->assertNull($url);
    }

    public function testFindAllWithLatestAnalysisReturnsArray(): void
    {
        $urlsData = [
            [
                'id' => 1,
                'name' => 'https://example.com',
                'created_at' => '2024-01-01 12:00:00',
                'updated_at' => '2024-01-01 12:00:00',
            ],
            [
                'id' => 2,
                'name' => 'https://test.com',
                'created_at' => '2024-01-01 13:00:00',
                'updated_at' => '2024-01-01 13:00:00',
            ],
        ];

        $analysesData = [
            [
                'url_id' => 1,
                'created_at' => '2024-01-02 12:00:00',
                'response_code' => 200,
            ],
        ];

        $urlsStmt = $this->createMock(PDOStatement::class);
        $urlsStmt->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($urlsData);

        $analysesStmt = $this->createMock(PDOStatement::class);
        $analysesStmt->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($analysesData);

        $this->pdo->expects($this->exactly(2))
            ->method('query')
            ->willReturnOnConsecutiveCalls($urlsStmt, $analysesStmt);

        $result = $this->repository->findAllWithLatestAnalysis();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('https://example.com', $result[0]['name']);
        $this->assertEquals(200, $result[0]['response_code']);
        $this->assertEquals('2024-01-02 12:00:00', $result[0]['last_check_at']);
        $this->assertNull($result[1]['response_code']);
    }

    public function testSaveCallsInsertForNewUrl(): void
    {
        $url = Url::fromArray(['name' => 'https://example.com']);

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with($this->callback(function ($args) {
                return $args[0] === 'https://example.com' &&
                       is_string($args[1]) &&
                       is_string($args[2]);
            }));
        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn(['id' => 1]);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('INSERT INTO urls'))
            ->willReturn($stmt);

        $this->repository->save($url);

        $this->assertEquals(1, $url->getId());
        $this->assertNotNull($url->getCreatedAt());
        $this->assertNotNull($url->getUpdatedAt());
    }

    public function testSaveCallsUpdateForExistingUrl(): void
    {
        $url = Url::fromArray([
            'id' => 1,
            'name' => 'https://example.com',
            'created_at' => '2024-01-01 12:00:00',
        ]);

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with($this->callback(function ($args) {
                return $args[0] === 'https://example.com' &&
                       is_string($args[1]) &&
                       $args[2] === 1;
            }));

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('UPDATE urls'))
            ->willReturn($stmt);

        $this->repository->save($url);

        $this->assertNotNull($url->getUpdatedAt());
    }

    public function testDeleteExecutesDeleteQuery(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([1]);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM urls WHERE id = ?')
            ->willReturn($stmt);

        $this->repository->delete(1);
    }
}
