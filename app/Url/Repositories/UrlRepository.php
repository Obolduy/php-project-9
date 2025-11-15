<?php

namespace Hexlet\Code\Url\Repositories;

use Carbon\Carbon;
use Hexlet\Code\Url\Models\Url;
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
        $result = $stmt->fetchObject(Url::class);

        return $result !== false ? $result : null;
    }

    public function findByName(string $name): ?Url
    {
        $stmt = $this->pdo->prepare('SELECT * FROM urls WHERE name = ?');
        $stmt->execute([$name]);
        $result = $stmt->fetchObject(Url::class);

        return $result !== false ? $result : null;
    }

    public function findAllWithLatestAnalysis(): array
    {
        $urlsQuery = 'SELECT id, name, created_at, updated_at FROM urls ORDER BY id DESC';
        $urlsStmt = $this->pdo->query($urlsQuery);
        $urls = $urlsStmt->fetchAll(PDO::FETCH_ASSOC);

        $analysisQuery = '
            SELECT DISTINCT ON (url_id)
                url_id,
                created_at,
                response_code
            FROM urls_analyses
            ORDER BY url_id, created_at DESC
        ';
        $analysisStmt = $this->pdo->query($analysisQuery);
        $analyses = $analysisStmt->fetchAll(PDO::FETCH_ASSOC);

        $analysesByUrlId = [];
        foreach ($analyses as $analysis) {
            $analysesByUrlId[$analysis['url_id']] = $analysis;
        }

        foreach ($urls as &$url) {
            if (isset($analysesByUrlId[$url['id']])) {
                $url['last_check_at'] = $analysesByUrlId[$url['id']]['created_at'];
                $url['response_code'] = $analysesByUrlId[$url['id']]['response_code'];
            } else {
                $url['last_check_at'] = null;
                $url['response_code'] = null;
            }
        }

        return $urls;
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
            'INSERT INTO urls (name, created_at, updated_at) VALUES (?, ?, ?) RETURNING id'
        );
        $stmt->execute([$url->getName(), $now, $now]);
        $data = $stmt->fetch();

        $url->setId($data['id'])
            ->setCreatedAt($now)
            ->setUpdatedAt($now);
    }

    private function update(Url $url): void
    {
        $now = Carbon::now()->toDateTimeString();
        $stmt = $this->pdo->prepare('UPDATE urls SET name = ?, updated_at = ? WHERE id = ?');
        $stmt->execute([$url->getName(), $now, $url->getId()]);

        $url->setUpdatedAt($now);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM urls WHERE id = ?');
        $stmt->execute([$id]);
    }
}
