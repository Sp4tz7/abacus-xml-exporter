<?php

declare(strict_types=1);

namespace Sp4tz\AbacusXmlExporter\Exporter;

use Sp4tz\AbacusXmlExporter\Config\ExportConfig;
use Sp4tz\AbacusXmlExporter\Contract\AbacusMapperInterface;
use Sp4tz\AbacusXmlExporter\Contract\TimesheetEntryInterface;
use Sp4tz\AbacusXmlExporter\Contract\XmlExporterInterface;
use Sp4tz\AbacusXmlExporter\Mapper\DefaultAbacusMapper;
use Sp4tz\AbacusXmlExporter\Model\ExportResult;
use Sp4tz\AbacusXmlExporter\Validator\TimesheetValidator;
use Sp4tz\AbacusXmlExporter\Xml\AbacusXmlBuilder;

final readonly class AbacusTimesheetExporter implements XmlExporterInterface
{
    public function __construct(
        private TimesheetValidator $validator = new TimesheetValidator(),
        private AbacusMapperInterface $mapper = new DefaultAbacusMapper(),
        private AbacusXmlBuilder $xmlBuilder = new AbacusXmlBuilder(),
        private ExportConfig $config = new ExportConfig(),
    ) {
    }

    /**
     * @param iterable<TimesheetEntryInterface> $entries
     */
    public function export(iterable $entries): ExportResult
    {
        $entries = is_array($entries) ? array_values($entries) : iterator_to_array($entries, false);
        $validation = $this->validator->validate($entries);

        if ($this->config->strict && !$validation->isValid()) {
            return ExportResult::failed($validation);
        }

        $rows = [];

        foreach ($entries as $entry) {
            if (!$entry instanceof TimesheetEntryInterface) {
                continue;
            }

            $rows[] = $this->mapper->map($entry);
        }

        $xml = $this->xmlBuilder->build($rows, $this->config);

        return ExportResult::success($xml, $validation, count($rows));
    }
}
