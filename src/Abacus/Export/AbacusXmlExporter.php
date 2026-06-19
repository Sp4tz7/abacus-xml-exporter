<?php

namespace AbaConnect\Abacus\Export;

use AbaConnect\Abacus\Dto\TimesheetEntry;
use AbaConnect\Abacus\Exception\AbacusXmlExportException;
use DOMException;

final class AbacusXmlExporter
{
    /**
     * @param TimesheetEntry[] $entries
     * @throws DOMException
     */
    public function exportToString(array $entries, AbacusXmlExportConfig $config): string
    {
        if ($config->validateEntries) {
            $this->validateEntries($entries);
        }

        $dom = new \DOMDocument('1.0', $config->encoding);
        $dom->formatOutput = $config->prettyPrint;

        $root = $dom->createElement('AbaConnectContainer');
        $dom->appendChild($root);

        $taskCount = $dom->createElement('TaskCount');
        $taskCount->appendChild($dom->createTextNode("1"));
        $root->appendChild($taskCount);

        $tasks = $dom->createElement('Task');
        $root->appendChild($tasks);

        $parameter = $dom->createElement('Parameter');
        $tasks->appendChild($parameter);

        $this->appendText($dom, $parameter, 'Application', $config->application);
        $this->appendText($dom, $parameter, 'Id', $config->id);
        $this->appendText($dom, $parameter, 'MapId', $config->mapId);
        $this->appendText($dom, $parameter, 'Version', $config->version);
        $this->appendText($dom, $parameter, 'Mandant', $config->mandant);

        foreach ($entries as $entry) {

            $Transaction = $dom->createElement('Transaction');
            $tasks->appendChild($Transaction);

            $timesheet = $dom->createElement('PreEntry');
            $timesheet->setAttribute('mode', 'SAVE');
            $Transaction->appendChild($timesheet);

            $this->appendText($dom, $timesheet, 'EmployeeNumber', $entry->employeeNumber);
            $this->appendText($dom, $timesheet, 'PeriodDate', $entry->periodDate->format('Y-m-d'));
            $this->appendText($dom, $timesheet, 'PeriodNumber', $entry->periodNumber);
            $this->appendText($dom, $timesheet, 'PayrollType', $entry->payrollType);
            $this->appendText($dom, $timesheet, 'TextPayrollType', $entry->textPayrollType);
            $this->appendText($dom, $timesheet, 'Amount', $entry->amount);
            $this->appendText($dom, $timesheet, 'Factor', $entry->factor);
            $this->appendText($dom, $timesheet, 'CostCentre1', $entry->costCentre1);

        }

        $xml = $dom->saveXML();

        if ($xml === false) {
            throw new AbacusXmlExportException('Impossible de générer le XML Abacus.');
        }

        return $xml;
    }

    /**
     * @param TimesheetEntry[] $entries
     * @throws DOMException
     */
    public function exportToFile(array $entries, AbacusXmlExportConfig $config, string $filePath): void
    {
        $xml = $this->exportToString($entries, $config);

        $directory = dirname($filePath);

        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new AbacusXmlExportException(sprintf('Impossible de créer le dossier : %s', $directory));
        }

        if (file_put_contents($filePath, $xml) === false) {
            throw new AbacusXmlExportException(sprintf('Impossible d’écrire le fichier XML : %s', $filePath));
        }
    }

    /**
     * @throws DOMException
     */
    private function appendText(\DOMDocument $dom, \DOMElement $parent, string $name, string|int|float|null $value): void
    {
        if ($value === null) {
            return;
        }

        $element = $dom->createElement($name);
        $element->appendChild($dom->createTextNode((string) $value));
        $parent->appendChild($element);
    }

    /**
     * @param array $entries
     */
    private function validateEntries(array $entries): void
    {
        foreach ($entries as $index => $entry) {
            $line = $index + 1;

            if (!$entry instanceof TimesheetEntry) {
                throw new AbacusXmlExportException(
                    sprintf(
                        'Chaque entrée doit être une instance de %s.',
                        TimesheetEntry::class
                    )
                );
            }

            if ($entry->employeeNumber <= 0) {
                throw new AbacusXmlExportException(sprintf('Entrée #%d: EmployeeNumber est obligatoire et doit être > 0.', $line));
            }

            if (!$entry->periodDate instanceof \DateTimeInterface) {
                throw new AbacusXmlExportException(sprintf('Entrée #%d: PeriodDate est obligatoire.', $line));
            }

            if ($entry->periodNumber <= 0) {
                throw new AbacusXmlExportException(sprintf('Entrée #%d: PeriodNumber est obligatoire et doit être > 0.', $line));
            }

            if ($entry->payrollType <= 0) {
                throw new AbacusXmlExportException(sprintf('Entrée #%d: PayrollType est obligatoire et doit être > 0.', $line));
            }

            $this->assertDecimalField($entry->amount, 'Amount', $line);
            $this->assertDecimalField($entry->factor, 'Factor', $line);

            if ($entry->costCentre1 !== null && strlen((string) $entry->costCentre1) > 12) {
                throw new AbacusXmlExportException(sprintf('Entrée #%d: CostCentre1 ne peut pas dépasser 12 caractères.', $line));
            }

            if ($entry->textPayrollType !== null && strlen($entry->textPayrollType) > 100) {
                throw new AbacusXmlExportException(sprintf('Entrée #%d: TextPayrollType ne peut pas dépasser 100 caractères.', $line));
            }
        }
    }

    private function assertDecimalField(?float $value, string $fieldName, int $line): void
    {
        if ($value === null) {
            return;
        }

        $rawValue = rtrim(rtrim(sprintf('%.14F', $value), '0'), '.');
        $decimalPart = strstr($rawValue, '.');
        $decimalLength = $decimalPart === false ? 0 : strlen(substr($decimalPart, 1));

        if ($decimalLength > 6) {
            throw new AbacusXmlExportException(
                sprintf('Entrée #%d: %s ne peut pas avoir plus de 6 décimales.', $line, $fieldName)
            );
        }

        if ($value < 0) {
            throw new AbacusXmlExportException(sprintf('Entrée #%d: %s ne peut pas être négatif.', $line, $fieldName));
        }

        // Abacus attend un format décimal avec point et un maximum de 6 décimales.
        $normalizedValue = rtrim(rtrim(number_format($value, 6, '.', ''), '0'), '.');

        if (!str_contains($normalizedValue, '.')) {
            $normalizedValue .= '.0';
        }

        if (strlen($normalizedValue) > 10) {
            throw new AbacusXmlExportException(
                sprintf('Entrée #%d: %s dépasse 10 caractères (%s).', $line, $fieldName, $normalizedValue)
            );
        }

        if (!preg_match('/^\d+\.\d{1,6}$/', $normalizedValue)) {
            throw new AbacusXmlExportException(
                sprintf('Entrée #%d: %s doit avoir un point comme séparateur décimal et max 6 décimales.', $line, $fieldName)
            );

        }
    }

}
