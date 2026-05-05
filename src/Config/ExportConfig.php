<?php

declare(strict_types=1);

namespace Sp4tz\AbacusXmlExporter\Config;

final readonly class ExportConfig
{
    /**
     * @param list<string> $requiredFields
     * @param array<string, scalar|null> $defaults
     */
    public function __construct(
        public ?string $companyId = null,
        public string $rootNode = 'AbaConnectContainer',
        public string $recordNode = 'TimeSheet',
        public bool $strict = true,
        public string $encoding = 'UTF-8',
        public string $dateFormat = 'Y-m-d',
        public string $decimalSeparator = '.',
        public bool $skipNullValues = true,
        public array $requiredFields = ['employee_id', 'date', 'duration'],
        public array $defaults = [],
    ) {
    }

    /**
     * @param array<string, mixed> $config
     */
    public static function fromArray(array $config): self
    {
        return new self(
            companyId: $config['company_id'] ?? null,
            rootNode: $config['root_node'] ?? 'AbaConnectContainer',
            recordNode: $config['record_node'] ?? 'TimeSheet',
            strict: (bool) ($config['strict'] ?? true),
            encoding: $config['encoding'] ?? 'UTF-8',
            dateFormat: $config['date_format'] ?? 'Y-m-d',
            decimalSeparator: $config['decimal_separator'] ?? '.',
            skipNullValues: (bool) ($config['skip_null_values'] ?? true),
            requiredFields: array_values($config['required_fields'] ?? ['employee_id', 'date', 'duration']),
            defaults: $config['defaults'] ?? [],
        );
    }
}
