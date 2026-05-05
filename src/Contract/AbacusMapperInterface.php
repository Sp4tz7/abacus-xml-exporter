<?php

declare(strict_types=1);

namespace Sp4tz\AbacusXmlExporter\Contract;

interface AbacusMapperInterface
{
    /**
     * @return array<string, scalar|null>
     */
    public function map(TimesheetEntryInterface $entry): array;
}
