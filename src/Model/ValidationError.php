<?php

declare(strict_types=1);

namespace Sp4tz\AbacusXmlExporter\Model;

final readonly class ValidationError
{
    public function __construct(
        private int $index,
        private string $field,
        private string $message,
    ) {
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array{index:int, field:string, message:string}
     */
    public function toArray(): array
    {
        return [
            'index' => $this->index,
            'field' => $this->field,
            'message' => $this->message,
        ];
    }
}
