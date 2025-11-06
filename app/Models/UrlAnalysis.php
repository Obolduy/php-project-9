<?php

namespace Hexlet\Code\Models;

class UrlAnalysis
{
    private ?int $id = null;

    private int $urlId;

    private ?int $responseCode = null;

    private ?string $h1 = null;

    private ?string $title = null;

    private ?string $description = null;

    private ?string $createdAt = null;

    private ?string $updatedAt = null;


    public function __construct(array $data = [])
    {
        if (isset($data['id'])) {
            $this->id = (int) $data['id'];
        }

        if (isset($data['url_id'])) {
            $this->urlId = (int) $data['url_id'];
        }

        if (isset($data['response_code'])) {
            $this->responseCode = (int) $data['response_code'];
        }

        if (isset($data['h1'])) {
            $this->h1 = $data['h1'];
        }

        if (isset($data['title'])) {
            $this->title = $data['title'];
        }

        if (isset($data['description'])) {
            $this->description = $data['description'];
        }

        if (isset($data['created_at'])) {
            $this->createdAt = $data['created_at'];
        }

        if (isset($data['updated_at'])) {
            $this->updatedAt = $data['updated_at'];
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUrlId(): int
    {
        return $this->urlId;
    }

    public function setUrlId(int $urlId): void
    {
        $this->urlId = $urlId;
    }

    public function getResponseCode(): ?int
    {
        return $this->responseCode;
    }

    public function setResponseCode(?int $responseCode): void
    {
        $this->responseCode = $responseCode;
    }

    public function getH1(): ?string
    {
        return $this->h1;
    }

    public function setH1(?string $h1): void
    {
        $this->h1 = $h1;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'url_id' => $this->urlId,
            'response_code' => $this->responseCode,
            'h1' => $this->h1,
            'title' => $this->title,
            'description' => $this->description,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
