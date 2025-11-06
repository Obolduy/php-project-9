<?php

namespace Hexlet\Code\Repositories;

use Hexlet\Code\Models\UrlAnalysis;
use PDO;

class UrlAnalysisRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function find(int $id): ?UrlAnalysis
    {
        $stmt = $this->pdo->prepare('SELECT * FROM urls_analyses WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? new UrlAnalysis($data) : null;
    }

    public function findByUrlId(int $urlId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM urls_analyses WHERE url_id = ? ORDER BY created_at DESC'
        );
        $stmt->execute([$urlId]);
        $rows = $stmt->fetchAll();

        return array_map(fn($row) => new UrlAnalysis($row), $rows);
    }

    public function save(UrlAnalysis $analysis): void
    {
        if ($analysis->getId() === null) {
            $this->insert($analysis);
        } else {
            $this->update($analysis);
        }
    }

    private function insert(UrlAnalysis $analysis): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO urls_analyses (url_id, response_code, h1, title, description, created_at, updated_at) 
             VALUES (?, ?, ?, ?, ?, NOW(), NOW()) 
             RETURNING id, created_at, updated_at'
        );
        $stmt->execute([
            $analysis->urlId,
            $analysis->responseCode,
            $analysis->h1,
            $analysis->title,
            $analysis->description,
        ]);
        $data = $stmt->fetch();

        $analysis->setId($data['id']);
        $analysis->setCreatedAt($data['created_at']);
        $analysis->setUpdatedAt($data['updated_at']);
    }

    private function update(UrlAnalysis $analysis): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE urls_analyses 
             SET response_code = ?, h1 = ?, title = ?, description = ?, updated_at = NOW() 
             WHERE id = ?
             RETURNING updated_at'
        );
        $stmt->execute([
            $analysis->responseCode,
            $analysis->h1,
            $analysis->title,
            $analysis->description,
            $analysis->getId(),
        ]);
        $data = $stmt->fetch();

        $analysis->setUpdatedAt($data['updated_at']);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM urls_analyses WHERE id = ?');
        $stmt->execute([$id]);
    }
}
