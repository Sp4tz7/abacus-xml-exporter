<?php

namespace AbaConnect\Abacus\Mapper;

use AbaConnect\Abacus\Dto\TimesheetEntry;

interface TimesheetEntryMapperInterface
{
    public function map(object $source): TimesheetEntry;
}
