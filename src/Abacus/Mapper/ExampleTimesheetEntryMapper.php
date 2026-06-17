<?php

namespace AbaConnect\Abacus\Mapper;

use AbaConnect\Abacus\Dto\TimesheetEntry;

/**
 * Exemple volontairement générique.
 *
 * Remplacez les appels getXxx() par ceux de votre entité réelle.
 */
final class ExampleTimesheetEntryMapper implements TimesheetEntryMapperInterface
{
    public function map(object $source): TimesheetEntry
    {
        if (!method_exists($source, 'getUser') || !method_exists($source, 'getDate') || !method_exists($source, 'getHours')) {
            throw new \InvalidArgumentException('L’objet fourni ne ressemble pas à une feuille de temps exploitable.');
        }

        $user = $source->getUser();

        return new TimesheetEntry(
            employeeNumber: (string) $user->getEmployeeNumber(),
            periodDate: $source->getDate(),
            periodNumber: $source->getHours(),
            payrollType: $source->getPayrollType(),
            amount: $source->getAmount(),
            factor: $source->getFactor(),
            costCentre1: $source->getCostCentre1(),
            textPayrollType: $source->getTextPayrollType(),
        );
    }
}
