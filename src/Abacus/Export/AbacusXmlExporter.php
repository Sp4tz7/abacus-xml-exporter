<?php

namespace AbaConnect\Abacus\Export;

use AbaConnect\Abacus\Dto\TimesheetEntry;
use AbaConnect\Abacus\Exception\AbacusXmlExportException;

final class AbacusXmlExporter
{
    /**
     * @param TimesheetEntry[] $entries
     * @throws \DOMException
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
     * @param mixed[] $entries
     */
    private function validateEntries(array $entries): void
    {
        foreach ($entries as $entry) {
            if (!$entry instanceof TimesheetEntry) {
                throw new AbacusXmlExportException(
                    sprintf(
                        'Chaque entrée doit être une instance de %s.',
                        TimesheetEntry::class
                    )
                );
            }

            if (trim($entry->employeeNumber) === '') {
                throw new AbacusXmlExportException('Le numéro d’employé est obligatoire.');
            }

        }
    }

}
