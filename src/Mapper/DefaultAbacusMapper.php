<?php

declare(strict_types=1);

namespace Sp4tz\AbacusXmlExporter\Mapper;

use Sp4tz\AbacusXmlExporter\Config\ExportConfig;
use Sp4tz\AbacusXmlExporter\Contract\AbacusMapperInterface;
use Sp4tz\AbacusXmlExporter\Contract\TimesheetEntryInterface;

final readonly class DefaultAbacusMapper implements AbacusMapperInterface
{
    public function __construct(private ExportConfig $config = new ExportConfig())
    {
    }

    public function map(TimesheetEntryInterface $entry): array
    {
        $row = [
            'PersonNumber' => $entry->getEmployeeIdentifier(),
            'Date' => $entry->getDate()->format($this->config->dateFormat),
            'Hours' => $this->formatDuration($entry->getDuration()),
            'Activity' => $entry->getActivityCode(),
            'CostCenter' => $entry->getCostCenter(),
            'Project' => $entry->getProjectCode(),
            'Text' => $entry->getComment(),
        ];

        foreach ($entry->getExtraFields() as $key => $value) {
            $row[$key] = $value;
        }

        return array_merge($this->config->defaults, $row);
    }

    private function formatDuration(float $duration): string
    {
        $value = number_format($duration, 2, $this->config->decimalSeparator, '');

        return rtrim(rtrim($value, '0'), $this->config->decimalSeparator);
    }
}
