<?php

declare(strict_types=1);

namespace Sp4tz\AbacusXmlExporter\Config;

final readonly class MappingConfig
{
    /**
     * @param array<string, string> $mapping
     */
    public function __construct(private array $mapping)
    {
    }

    /**
     * @param array<string, string> $mapping
     */
    public static function fromArray(array $mapping): self
    {
        return new self($mapping);
    }

    public function getNode(string $field): ?string
    {
        return $this->mapping[$field] ?? null;
    }

    public function hasField(string $field): bool
    {
        return isset($this->mapping[$field]);
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->mapping;
    }
}
