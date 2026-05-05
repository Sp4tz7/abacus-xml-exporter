<?php

declare(strict_types=1);

namespace Sp4tz\AbacusXmlExporter\Contract;

use Sp4tz\AbacusXmlExporter\Model\ExportResult;

interface XmlExporterInterface
{
    /**
     * @param iterable<TimesheetEntryInterface> $entries
     */
    public function export(iterable $entries): ExportResult;
}
