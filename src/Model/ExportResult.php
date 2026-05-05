<?php

declare(strict_types=1);

namespace Sp4tz\AbacusXmlExporter\Model;

use Sp4tz\AbacusXmlExporter\Validator\ValidationResult;

final readonly class ExportResult
{
    private function __construct(
        private bool $success,
        private ?string $xml,
        private ValidationResult $validationResult,
        private int $exportedRows,
    ) {
    }

    public static function success(string $xml, ValidationResult $validationResult, int $exportedRows): self
    {
        return new self(true, $xml, $validationResult, $exportedRows);
    }

    public static function failed(ValidationResult $validationResult): self
    {
        return new self(false, null, $validationResult, 0);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getXml(): ?string
    {
        return $this->xml;
    }

    public function getValidationResult(): ValidationResult
    {
        return $this->validationResult;
    }

    public function getExportedRows(): int
    {
        return $this->exportedRows;
    }
}
