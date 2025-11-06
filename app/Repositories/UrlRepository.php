<?php

namespace Hexlet\Code\Repositories;

use Carbon\Carbon;
use Hexlet\Code\Models\Url;
use PDO;

class UrlRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function find(int $id): ?Url
    {
        $stmt = $this->pdo->prepare('SELECT * FROM urls WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? new Url($data) : null;
    }

    public function findByName(string $name): ?Url
    {
        $stmt = $this->pdo->prepare('SELECT * FROM urls WHERE name = ?');
        $stmt->execute([$name]);
        $data = $stmt->fetch();

        return $data ? new Url($data) : null;
    }

    public function findAllWithLatestAnalysis(): array
    {
        $query = '
            SELECT 
                u.id,
                u.name,
                u.created_at,
                u.updated_at,
                latest_analysis.created_at as last_check_at,
                latest_analysis.response_code
            FROM urls u
            LEFT JOIN (
                SELECT DISTINCT ON (url_id) 
                    url_id,
                    created_at,
                    response_code
                FROM urls_analyses
                ORDER BY url_id, created_at DESC
            ) latest_analysis ON latest_analysis.url_id = u.id
            ORDER BY u.id DESC
        ';

        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save(Url $url): void
    {
        if ($url->getId() === null) {
            $this->insert($url);
        } else {
            $this->update($url);
        }
    }

    private function insert(Url $url): void
    {
        $now = Carbon::now()->toDateTimeString();
        $stmt = $this->pdo->prepare(
            'INSERT INTO urls (name, created_at, updated_at) 
             VALUES (?, ?, ?) 
             RETURNING id'
        );
        $stmt->execute([$url->name, $now, $now]);
        $data = $stmt->fetch();

        $url->setId($data['id'])
            ->setCreatedAt($now)
            ->setUpdatedAt($now);
    }

    private function update(Url $url): void
    {
        $now = Carbon::now()->toDateTimeString();
        $stmt = $this->pdo->prepare(
            'UPDATE urls 
             SET name = ?, updated_at = ? 
             WHERE id = ?'
        );
        $stmt->execute([$url->name, $now, $url->getId()]);

        $url->setUpdatedAt($now);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM urls WHERE id = ?');
        $stmt->execute([$id]);
    }
}
