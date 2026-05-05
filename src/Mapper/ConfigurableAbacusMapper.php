<?php

declare(strict_types=1);

namespace Sp4tz\AbacusXmlExporter\Mapper;

use Sp4tz\AbacusXmlExporter\Config\ExportConfig;
use Sp4tz\AbacusXmlExporter\Config\MappingConfig;
use Sp4tz\AbacusXmlExporter\Contract\AbacusMapperInterface;
use Sp4tz\AbacusXmlExporter\Contract\TimesheetEntryInterface;

final readonly class ConfigurableAbacusMapper implements AbacusMapperInterface
{
    public function __construct(
        private MappingConfig $mappingConfig,
        private ExportConfig $exportConfig = new ExportConfig(),
    ) {
    }

    public function map(TimesheetEntryInterface $entry): array
    {
        $values = [
            'employee_id' => $entry->getEmployeeIdentifier(),
            'date' => $entry->getDate()->format($this->exportConfig->dateFormat),
            'duration' => $this->formatDuration($entry->getDuration()),
            'activity_code' => $entry->getActivityCode(),
            'cost_center' => $entry->getCostCenter(),
            'project_code' => $entry->getProjectCode(),
            'comment' => $entry->getComment(),
        ];

        foreach ($entry->getExtraFields() as $key => $value) {
            $values[$key] = $value;
        }

        $row = $this->exportConfig->defaults;

        foreach ($values as $field => $value) {
            $nodeName = $this->mappingConfig->getNode($field) ?? $field;
            $row[$nodeName] = $value;
        }

        return $row;
    }

    private function formatDuration(float $duration): string
    {
        $value = number_format($duration, 2, $this->exportConfig->decimalSeparator, '');

        return rtrim(rtrim($value, '0'), $this->exportConfig->decimalSeparator);
    }
}
