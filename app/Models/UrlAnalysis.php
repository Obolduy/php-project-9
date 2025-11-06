<?php

namespace Hexlet\Code\Models;

class UrlAnalysis
{
    private ?int $id = null;

    public int $urlId {
        get {
            return $this->urlId;
        }
        set {
            $this->urlId = $value;
        }
    }

    public ?int $responseCode = null {
        get {
            return $this->responseCode;
        }
        set {
            $this->responseCode = $value;
        }
    }

    public ?string $h1 = null {
        get {
            return $this->h1;
        }
        set {
            $this->h1 = $value;
        }
    }

    public ?string $title = null {
        get {
            return $this->title;
        }
        set {
            $this->title = $value;
        }
    }

    public ?string $description = null {
        get {
            return $this->description;
        }
        set {
            $this->description = $value;
        }
    }

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
