<?php

class Article
{
    private ?int $id = null;

    public function __construct(
        private string $title,
        private string $content,
        private string $publishDate,
        private int $level,
        private string $description
    ) {
    }

                                                // Getters and setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getPublishDate(): string
    {
        return $this->publishDate;
    }

    public function setPublishDate(string $publishDate): void
    {
        $this->publishDate = $publishDate;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
