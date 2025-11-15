<?php

namespace Hexlet\Code\Url\Models;

class Url
{
    private array $attributes = [];

    private array $propertyMap = [
        'id' => 'id',
        'name' => 'name',
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
        $url = new self();
        
        if (isset($data['id'])) {
            $url->attributes['id'] = is_int($data['id']) ? $data['id'] : (int) $data['id'];
        }
        
        if (isset($data['name'])) {
            $url->attributes['name'] = $data['name'];
        }
        
        if (isset($data['created_at'])) {
            $url->attributes['created_at'] = $data['created_at'];
        }
        
        if (isset($data['updated_at'])) {
            $url->attributes['updated_at'] = $data['updated_at'];
        }
        
        return $url;
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

    public function setId(int $id): self
    {
        $this->attributes['id'] = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->attributes['name'] ?? '';
    }

    public function setName(string $name): self
    {
        $this->attributes['name'] = $name;
        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->attributes['created_at'] ?? null;
    }

    public function setCreatedAt(string $createdAt): self
    {
        $this->attributes['created_at'] = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->attributes['updated_at'] ?? null;
    }

    public function setUpdatedAt(string $updatedAt): self
    {
        $this->attributes['updated_at'] = $updatedAt;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];
    }
}
