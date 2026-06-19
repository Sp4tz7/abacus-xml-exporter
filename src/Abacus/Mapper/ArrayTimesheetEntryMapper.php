<?php

namespace AbaConnect\Abacus\Mapper;

use AbaConnect\Abacus\Dto\TimesheetEntry;

/**
 * Mapper pratique pour des objets simples (ex: (object) $array).
 */
final class ArrayTimesheetEntryMapper implements TimesheetEntryMapperInterface
{
    public function map(object $source): TimesheetEntry
    {
        $employeeNumber = $this->readValue($source, 'ligne1');
        $periodDate = $this->readValue($source, 'ligne2');
        $periodNumber = $this->readValue($source, 'ligne3');
        $payrollType = $this->readValue($source, 'ligne4');
        $amount = $this->readValue($source, 'ligne5');
        $factor = $this->readValue($source, 'ligne6');
        $costCentre1 = $this->readValue($source, 'ligne7');
        $textPayrollType = $this->readValue($source, 'ligne8');

        return new TimesheetEntry(
            employeeNumber: $employeeNumber,
            periodDate: $periodDate,
            periodNumber: $periodNumber,
            payrollType: $payrollType,
            amount: $amount,
            factor: $factor,
            costCentre1: $costCentre1,
            textPayrollType: $textPayrollType,
        );
    }

    private function readValue(object $source, string $field): mixed
    {
        if (property_exists($source, $field)) {
            return $source->{$field};
        }

        $getter = 'get' . ucfirst($field);
        if (method_exists($source, $getter)) {
            return $source->{$getter}();
        }

        throw new \InvalidArgumentException(sprintf('Champ manquant: %s', $field));
    }

    private function readRequiredString(object $source, string $field): string
    {
        $value = $this->readValue($source, $field);

        if ($value === null || trim((string) $value) === '') {
            throw new \InvalidArgumentException(sprintf('Champ obligatoire vide: %s', $field));
        }

        return (string) $value;
    }

    private function readOptionalString(object $source, string $field): ?string
    {
        $value = $this->readValue($source, $field);

        if ($value === null) {
            return null;
        }

        return (string) $value;
    }

    private function readRequiredInt(object $source, string $field): int
    {
        $value = $this->readValue($source, $field);

        if (!is_numeric($value)) {
            throw new \InvalidArgumentException(sprintf('Champ numerique invalide: %s', $field));
        }

        return (int) $value;
    }

    private function readRequiredFloat(object $source, string $field): float
    {
        $value = $this->readValue($source, $field);

        if (!is_numeric($value)) {
            throw new \InvalidArgumentException(sprintf('Champ decimal invalide: %s', $field));
        }

        return (float) $value;
    }

    private function readRequiredDate(object $source, string $field): \DateTime
    {
        $value = $this->readValue($source, $field);

        try {
            return $value;
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(sprintf('Date invalide pour %s: %s', $field, $value), 0, $e);
        }
    }
}

