<?php

declare(strict_types=1);

namespace Sp4tz\AbacusXmlExporter\Contract;

interface TimesheetProviderInterface
{
    /**
     * @return iterable<TimesheetEntryInterface>
     */
    public function getEntries(): iterable;
}
