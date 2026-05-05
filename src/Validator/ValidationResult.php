<?php

declare(strict_types=1);

namespace Sp4tz\AbacusXmlExporter\Validator;

use Sp4tz\AbacusXmlExporter\Model\ValidationError;

final readonly class ValidationResult
{
    /**
     * @param list<ValidationError> $errors
     */
    public function __construct(private array $errors = [])
    {
    }

    public function isValid(): bool
    {
        return $this->errors === [];
    }

    /**
     * @return list<ValidationError>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return list<array{index:int, field:string, message:string}>
     */
    public function toArray(): array
    {
        return array_map(static fn (ValidationError $error): array => $error->toArray(), $this->errors);
    }
}
