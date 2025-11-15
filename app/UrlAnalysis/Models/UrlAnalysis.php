<?php

namespace Hexlet\Code\UrlAnalysis\Models;

class UrlAnalysis
{
    private array $attributes = [];

    private array $propertyMap = [
        'id' => 'id',
        'urlId' => 'url_id',
        'url_id' => 'url_id',
        'responseCode' => 'response_code',
        'response_code' => 'response_code',
        'h1' => 'h1',
        'title' => 'title',
        'description' => 'description',
        'createdAt' => 'created_at',
        'created_at' => 'created_at',
        'updatedAt' => 'updated_at',
        'updated_at' => 'updated_at',
    ];

    public function __construct()
    {
    }

    public static function fromArray(array $data): self
    {
        $analysis = new self();

        if (isset($data['id'])) {
            $analysis->attributes['id'] = is_int($data['id']) ? $data['id'] : (int) $data['id'];
        }

        if (isset($data['url_id'])) {
            $analysis->attributes['url_id'] = is_int($data['url_id']) ? $data['url_id'] : (int) $data['url_id'];
        }

        if (isset($data['response_code'])) {
            $analysis->attributes['response_code'] = is_int($data['response_code'])
                ? $data['response_code']
                : (int) $data['response_code'];
        }

        if (isset($data['h1'])) {
            $analysis->attributes['h1'] = $data['h1'];
        }

        if (isset($data['title'])) {
            $analysis->attributes['title'] = $data['title'];
        }

        if (isset($data['description'])) {
            $analysis->attributes['description'] = $data['description'];
        }

        if (isset($data['created_at'])) {
            $analysis->attributes['created_at'] = $data['created_at'];
        }

        if (isset($data['updated_at'])) {
            $analysis->attributes['updated_at'] = $data['updated_at'];
        }

        return $analysis;
    }

    public function __get(string $name): mixed
    {
        $key = $this->propertyMap[$name] ?? $name;
        return $this->attributes[$key] ?? null;
    }

    public function __set(string $name, mixed $value): void
    {
        $key = $this->propertyMap[$name] ?? $name;
        $this->attributes[$key] = $value;
    }

    public function __isset(string $name): bool
    {
        $key = $this->propertyMap[$name] ?? $name;
        return isset($this->attributes[$key]);
    }

    public function getId(): ?int
    {
        return $this->attributes['id'] ?? null;
    }

    public function setId(int $id): void
    {
        $this->attributes['id'] = $id;
    }

    public function getUrlId(): ?int
    {
        return $this->attributes['url_id'] ?? null;
    }

    public function setUrlId(int $urlId): void
    {
        $this->attributes['url_id'] = $urlId;
    }

    public function getResponseCode(): ?int
    {
        return $this->attributes['response_code'] ?? null;
    }

    public function setResponseCode(?int $responseCode): void
    {
        $this->attributes['response_code'] = $responseCode;
    }

    public function getH1(): ?string
    {
        return $this->attributes['h1'] ?? null;
    }

    public function setH1(?string $h1): void
    {
        $this->attributes['h1'] = $h1;
    }

    public function getTitle(): ?string
    {
        return $this->attributes['title'] ?? null;
    }

    public function setTitle(?string $title): void
    {
        $this->attributes['title'] = $title;
    }

    public function getDescription(): ?string
    {
        return $this->attributes['description'] ?? null;
    }

    public function setDescription(?string $description): void
    {
        $this->attributes['description'] = $description;
    }

    public function getCreatedAt(): ?string
    {
        return $this->attributes['created_at'] ?? null;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->attributes['created_at'] = $createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->attributes['updated_at'] ?? null;
    }

    public function setUpdatedAt(string $updatedAt): void
    {
        $this->attributes['updated_at'] = $updatedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'url_id' => $this->getUrlId(),
            'response_code' => $this->getResponseCode(),
            'h1' => $this->getH1(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];
    }
}
